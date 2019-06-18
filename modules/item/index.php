<?php
if (!defined('FLUX_ROOT')) exit;
include_once(FLUX_ROOT.'/'.FLUX_MODULE_DIR.'/functions.php');
//$this->loginRequired();


	if($params->get('output')=='json' && $params->get('data_output')=='') {
		echo "[]1";
		exit();
	}

	$title = 'Items Database';

	require_once 'Flux/TemporaryTable.php';
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
	$tableName = "{$server->charMapDatabase}.items";
	$shopTable = Flux::config('FluxTables.ItemShopTable');

	$json_arr = array();
	$col_data = array();

	$item_types = Flux::config("ItemTypes")->toArray();
	$item_types2 = Flux::config("ItemTypes2")->toArray();
	$types_fix = array();
	foreach($item_types as $type_id => $name) {
		$types_fix[$type_id] = $name;
		if(array_key_exists($type_id, $item_types2)) {
			foreach($item_types2[$type_id] as $subtype_id => $subtype) {
				$types_fix[$type_id."-".$subtype_id] = $subtype;
			}
		}
	}

	$columns_ = array(
		'origin_table',
		'IF(origin_table like "%item_db2", "yes", "no")' => 
			array(
				'pseudo' => 'custom',
				'default' => "no",
				'search' => 'checkbox',
				'choices' => array(
					'yes' => "Yes",
					'no' => "No",
				),
				'label' => "Custom",
			),
		'items.id' => 
			array(
				'pseudo' => 'item_id',
				'label' => "Item ID",
			),
		'name_japanese' => 
			array(
				'pseudo' => 'name',
				'label' => "Name",
			),
		'type' =>
			array(
				'default' => '0',
				'search' => 'checkbox',
				'label' => "Type",
				'leaveDefault' => true,
				//'function' => 'itemTypeText',
				'putTempt' => true,
				'choices' => $types_fix,
			),
		'subtype' => 
			array(
				'default' => '',
				'label' => "Subtype",
				//'baseOn' => 'type',
			),
		'view_sprite' => 
			array(
				'pseudo' =>  'view',
				'leaveDefault' => true,
				'default' => 0,
			),
		'IFNULL(equip_locations,0)' => 
			array(
				'pseudo' => 'equip_locations',
				'search' => 'checkbox',
				'label' => "Equip Locations",
				'choices' => Flux::config('EquipLocationCombinations')->toArray(),
				'default' => 0,
				'leaveDefault' => true,
				//'function' => 'equipLocationCombinationText',
			),
		$shopTable.'.id' => 
			array(
				'pseudo' => 'for_sale',
				'search' => 'checkbox',
				'label' => 'For Sale',
				'choices' => array(
					'yes' => "Yes",
					'no' => "No",
				),
			),
		'IFNULL(cost,0)' =>
			array(
				'pseudo' => 'cost',
				'default' => 0,
				'search' => 'range',
				'label' => "Cost",
			),
		'IFNULL(price_buy,0)' =>
			array(
				'pseudo' => 'price_buy',
				'default' => 0,
				'search' => 'range',
				'label' => "NPC Buy",
			),
		'IFNULL(weight/10, 0)' => 
			array(
				'pseudo' => 'weight',
				'default' => 0,
				'search' => 'range',
				'label' => "Weight",
			),
		'IFNULL(defence,0)' => 
			array(
				'pseudo' => 'defence',
				'default' => 0,
				'search' => 'range',
				'label' => "Defense",
			),
		'IFNULL(items.range,0)' =>
			array(
				'pseudo' => 'range',
				'default' => 0,
				'search' => 'range',
				'label' => "Range",
				'useEscape' => true, //Because it's actually a keyword in mysql so it generates errors
			),
		'IFNULL(slots,0)' =>
			array(
				'pseudo' => 'slots',
				'default' => 0,
				'search' => 'range',
				'label' => "Slots",
			),
		'IF(refineable=1, "yes", "no")' =>
			array(
				'pseudo' => 'refineable',
				'label' => 'Refineable',
				'search' => 'checkbox',
				'choices' => array(
					'yes' => "Yes",
					'no' => "No",
				),
				'default' => "no",
			),
		'IFNULL(price_sell, FLOOR(price_buy/2))' => 
			array(
				'pseudo' => 'price_sell',
				'default' => 0,
				'search' => 'range',
				'label' => "NCP Sell",
			),
		'IFNULL(atk,0)' =>
			array(
				'pseudo' => 'atk',
				'default' => 0,
				'search' => 'range',
				'label' => "Attack",
			),
		'IFNULL(matk,0)' =>
			array(
				'pseudo' => 'matk',
				'default' => 0,
				'search' => 'range',
				'label' => "Magic Attack",
			),
	);

	$col_data = array_merge($col_data,prepareColumnsString($columns_));
	try {
		$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);
		
		// Statement parameters, joins and conditions.
		$bind        = array();
		$sqlpartial  = "LEFT OUTER JOIN {$server->charMapDatabase}.$shopTable ON $shopTable.nameid = items.id ";
		$sqlpartial .= "WHERE 1=1 ";

		$itemText = $params->get('itemText');
		$itemID = $params->get('item_id');

		if($itemText!='' && $itemID=='') {
			$itemText = "%".str_replace(" ","%",$itemText)."%";
			$sqlpartial .= "AND (items.id LIKE ? OR name_japanese LIKE ?) ";
			$bind[]      = $itemText;
			$bind[]      = $itemText;
		}
		if($itemID!='') {
			$sqlpartial .= "AND (items.id LIKE ?) ";
			$bind[]      = $itemID;
		}

		$itemTypeP     = $params->get('type');
		$equipLocsP    = $params->get('equip_loc');
		$refineable   = $params->get('refineable');
		$forSale      = $params->get('for_sale');
		$custom       = $params->get('custom');
		
		if ($itemTypeP && $itemTypeP !== '-1') {
			$sqlpartial .= "AND (1=0 ";
			foreach(explode(",",$itemTypeP) as $itemType) {
				if (count($itemTypeSplit = explode('-', $itemType)) == 2) {
					$itemType = $itemTypeSplit[0];
					$itemType2 = $itemTypeSplit[1];
				}
				if (is_numeric($itemType) && (floatval($itemType) == intval($itemType))) {
					$itemTypes = Flux::config('ItemTypes')->toArray();
					if (array_key_exists($itemType, $itemTypes) && $itemTypes[$itemType]) {
						$sqlpartial .= "OR (type = ? ";
						$bind[]      = $itemType;
					} else {
						$sqlpartial .= 'OR (type IS NULL';
					}
					
					if (count($itemTypeSplit) == 2 && is_numeric($itemType2) && (floatval($itemType2) == intval($itemType2))) {
						$itemTypes2 = Flux::config('ItemTypes2')->toArray();
						if (array_key_exists($itemType, $itemTypes2) && array_key_exists($itemType2, $itemTypes2[$itemType]) && $itemTypes2[$itemType][$itemType2]) {
							$sqlpartial .= "AND subtype = ? ";
							$bind[]      = $itemType2;
						} else {
							$sqlpartial .= 'AND subtype IS NULL ';
						}
					}

					$sqlpartial .= ") ";

				}
			}
			$sqlpartial .= ") ";
		}

		if ($equipLocsP !== false && $equipLocsP !== '-1') {
			$sqlpartial.= "AND (1=0 ";
			foreach(explode(",",$equipLocsP) as $equipLocs) {
				if(is_numeric($equipLocs) && (floatval($equipLocs) == intval($equipLocs))) {
					$equipLocationCombinations = Flux::config('EquipLocationCombinations')->toArray();
					if (array_key_exists($equipLocs, $equipLocationCombinations) && $equipLocationCombinations[$equipLocs]) {
						if ($equipLocs === '0') {
							$sqlpartial .= "OR (equip_locations = 0 OR equip_locations IS NULL) ";
						} else {
							$sqlpartial .= "OR equip_locations = ? ";
							$bind[]      = $equipLocs;
						}
					}
				} else {
					$combinationName = preg_quote($equipLocs, '/');
					$equipLocationCombinations = preg_grep("/.*?$combinationName.*?/i", Flux::config('EquipLocationCombinations')->toArray());
					
					if (count($equipLocationCombinations)) {
						$equipLocationCombinations = array_keys($equipLocationCombinations);
						$sqlpartial .= "OR (";
						$partial     = '';
						
						foreach ($equipLocationCombinations as $id) {
							if ($id === 0) {
								$partial .= "(equip_locations = 0 OR equip_locations IS NULL) OR ";
							} else {
								$partial .= "equip_locations = ? OR ";
								$bind[]   = $id;
							}
						}
						
						$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
						$sqlpartial .= "$partial) ";
					}
				}
			}
			$sqlpartial.= ") ";
		}


		foreach($col_data['s_params']['search']['range'] as $rItem => $label) {
			$val = $params->get(str_replace("`","",$rItem));

			if($val!='') {
				$g = explode(",",$val);
				if(count($g)==2) {
					//$tmp = "AND ($rItem >= ".$g[0]." AND $rItem <= $g[1] )";
					if(array_key_exists('useOr',$label)) {
						$tmp = "OR ($rItem >= ? AND $rItem <= ?)";
					}
					else {
						//$tmp = "AND ($rItem >= ? AND $rItem <= ?  OR $rItem is NULL)";
						$tmp = "AND ($rItem >= ? AND $rItem <= ? )";
					}
					$sqlpartial .= $tmp;
					$bind[] = $g[0];
					$bind[] = $g[1];
				}
			}
		}

		if ($refineable) {
			if(count(explode(",",$custom))!=2 || $custom !== -1) {
				if ($refineable == 'yes') {
					$sqlpartial .= "AND refineable > 0 ";
				}
				elseif ($refineable == 'no') {
					$sqlpartial .= "AND IFNULL(refineable, 0) < 1 ";
				}
			}
		}
		
		if ($forSale) {
			if(count(explode(",",$forSale))!=2 || $forSale !== -1) {
				if ($forSale == 'yes') {
					$sqlpartial .= "AND $shopTable.cost > 0 ";
				}
				elseif ($forSale == 'no') {
					$sqlpartial .= "AND IFNULL($shopTable.cost, 0) < 1 ";
				}
			}
		}
		
		if ($custom) {
			if(count(explode(",",$custom))!=2 || $custom !== -1) {
				if ($custom == 'yes') {
					$sqlpartial .= "AND origin_table LIKE '%item_db2' ";
				}
				elseif ($custom == 'no') {
					$sqlpartial .= "AND origin_table LIKE '%item_db' ";
				}
			}
		}
		
		$sortable = array(
			'item_id' => 'asc', 'name', 'type', 'equip_locations', 'price_buy', 'price_sell', 'weight',
			'atk', 'matk', 'defence', 'range', 'slots', 'refineable', 'cost', 'custom'
		);

		//Get total count and feed back to the paginator.
		$sql = "SELECT COUNT(DISTINCT items.id) AS total, ". implode(", ",$col_data['s_params']['columns'])." FROM $tableName $sqlpartial";
		$sth = $server->connection->getStatement($sql);
		$sth->execute($bind);
		$res = $sth->fetch();
		$search_params['checkbox'] = $col_data['s_params']['search']['checkbox'];
		$json_arr['total'] = $res->total;

		$json_arr['defaults'] = $col_data['defaults'];


		foreach($col_data['s_params']['search']['range'] as $key => $label) {
			$key2 = str_replace("`","",$key);
			$search_params['range'][$key2] = array(
				'label' => $label['label'],
				'min' => (is_numeric($g=$res->{"min_".$key2})) ? $g+=0 : $g,
				'max' => (is_numeric($g=$res->{"max_".$key2})) ? $g+=0 : $g,
			);
		}
		
		$paginator = $this->getPaginator($res->total);
		$paginator->setSortableColumns($sortable);
		$sql  = "SELECT ".implode(", ",$col_data['items_query'])." FROM $tableName $sqlpartial GROUP BY items.id";
		$sql  = $paginator->getSQL($sql);
		$sth  = $server->connection->getStatement($sql);
		if(is_array($paginator->currentSortOrder))	$sortable = array_merge($sortable,$paginator->currentSortOrder);

		$sth->execute($bind);
		$items = $sth->fetchAll();
		/*
		$authorized = $auth->actionAllowed('item', 'view');
		
		if ($items && count($items) === 1 && $authorized && Flux::config('SingleMatchRedirectItem')) {
			$this->redirect($this->url('item', 'view', array('id' => $items[0]->item_id)));
		}
		*/

		$json_arr['items'] = forJSON($this,$items,$col_data);
		foreach($search_params['checkbox'] as $r => $d) { $t[$r] = '-1'; }
		if($params->get('item_id')!='') {
			$t['itemText'] = $params->get('item_id');
		}
		$json_arr['_params'] = $t;
		$json_arr['_params_default'] = $t;
		foreach($sortable as $k => $v) {if(!is_numeric($k)) $json_arr['_params'][$k."_order"] = $v; }
		$json_arr['equip_locations'] = Flux::config('EquipLocationCombinations')->toArray();
		$json_arr['item_types'] = Flux::config('ItemTypes')->toArray();
		$json_arr['perPage'] = Flux::config('ResultsPerPage');
		$json_arr['labels'] = $col_data['labels'];
		$json_arr['sortable'] = array('ASC','DESC','NONE',); 

		if($params->get('output')=='json') {
			$tmp = array();
			$data_output = $params->get('data_output');
			if($data_output!='') {
				$output = explode(",",$data_output);
				foreach($output as $opt) {
					if(array_key_exists($opt, $json_arr)) {
						$tmp[$opt] = $json_arr[$opt];
					}
				}
			}
			echo json_encode($tmp);
			exit();
		}
	}
	catch (Exception $e) {
		if (isset($tempTable) && $tempTable) {
			// Ensure table gets dropped.
			$tempTable->drop();
		}
		// Raise the original exception.
		$class = get_class($e);
		throw new $class($e->getMessage());
	}