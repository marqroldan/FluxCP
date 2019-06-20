<?php
if (!defined('FLUX_ROOT')) exit;
include_once('settings.php');

function prepareColumnsString($columns=array()) {
    $new_columns = array();
    foreach($columns as $name => $pseudo) {
        if(!is_array($pseudo)) {
            $pseudo = array('pseudo'=>$pseudo);
            if(is_numeric($name)) $name = $pseudo['pseudo'];
        }
        if(!array_key_exists('pseudo',$pseudo)) {
            $pseudo['pseudo'] = $name;
        }
        if(array_key_exists('default',$pseudo)) {
            $val = $pseudo['default'];
            $new_columns['defaults'][$pseudo['pseudo']] = $val;
        }
        $spcsr = $pseudo['pseudo'];
        if(array_key_exists('useEscape', $pseudo)) {
            $spcsr = "`".$spcsr."`";
        }
        if(array_key_exists('search',$pseudo)) {
            $sValue = $pseudo['search'];
            if($sValue=='range') {
                $new_columns['s_params']['columns'][] = "MAX(".$name.") AS max_".$pseudo['pseudo'];
                $new_columns['s_params']['columns'][] = "MIN(".$name.") AS min_".$pseudo['pseudo'];
            }
            $new_columns['s_params']['search'][$sValue][$spcsr] = $pseudo;
        }
        if(array_key_exists('label',$pseudo)) {
            $new_columns['labels'][$pseudo['pseudo']] = $pseudo['label'];
        }
        $new_columns['res_column'][] = $pseudo;
        $new_columns['items_query'][] = $name." AS ".$spcsr;
    }
    return $new_columns;
}


function forJSON($flux,$data,$col_data) {
    $tmp_arr = array();
	foreach($data as $item) {
		$temp_orig = array();
		$tmp = array();
		foreach($col_data['res_column'] as $cols) {
			$val = $item->{$cols['pseudo']};
            if(is_numeric($val)) $val += 0;
            if($val===0 || $val=='' || $val==null) {
                if($cols['leaveDefault']) {
                    $val = $cols['default'];
                }
                else {
                    continue;
                }
            }
			if(array_key_exists('putTempt',$cols) && $cols['putTempt']) {
                $temp_orig[$cols['pseudo']] = array('function'=> $cols['function'], 'val_'.$cols['pseudo'] => $val);
			}
			else {
				if(array_key_exists('baseOn',$cols)) {
					$r = $temp_orig[$cols['baseOn']];
					$val = $flux->{$r['function']}($r['val_'.$cols['baseOn']],$item->{$cols['pseudo']});
					$ex = explode(" - ",$val);
					$tmp[$cols['baseOn']] = $ex[0];
					$val = $ex[1];
				}
				if(array_key_exists('function',$cols)) {
					$val = $flux->{$cols['function']}($val);
				}
            }
            if(strpos($item->origin_table,"item_db")!==FALSE) {
                $tmp['iconImage'] = ($g=$flux->iconImage($item->item_id)) ? $g : '';
                $tmp['itemImage'] = ($g=$flux->itemImage($item->item_id)) ? $g : '';
            }
			$tmp[$cols['pseudo']] = $val;
        }
		$tmp_arr[] = $tmp;
    }
    return $tmp_arr;
}
