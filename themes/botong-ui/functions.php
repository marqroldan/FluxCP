<?php if (!defined('FLUX_ROOT')) exit;

function mainNavigation($navi) {
    $output = "";
    if(array_key_exists("ulSettings",$navi)) {
        $ulSettings = '';
        foreach($navi['ulSettings'] as $key => $val) {
            $ulSettings .= $key."='".$val."' ";
        }
        $output .= "<ul ".$ulSettings.">";
    }
    if(array_key_exists('content',$navi)) {
        $liSettings = '';
        foreach($navi as $key => $val) {
            if($key=='content') continue;
            $liSettings .= $key."='".$val."' ";
        }
        $output .= "<li ".$liSettings.">".$navi['content']."</li>";
    }
    if(array_key_exists('children',$navi)) {
        foreach($navi['children'] as $child) $output .= mainNavigation($child);
    }
    if(array_key_exists("ulSettings",$navi)) {
    $output .= "</ul>";
    }

    return $output;
}

function returnAbsoluteContents($file) {
    $addr=Flux::config('ServerAddress');

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") {
        $_https = true;
    }
    else {
        $_https = false;
    }

    $serverProtocol = $_https ? 'https://' : 'http://';
   
    echo file_get_contents($serverProtocol.$addr.$file);
}

function loadFiles($params, $filesList, $fileType, $pageFiles, $defaultElem, $format) {
    $actions = array('*', $params->get('action'));
    $module =  $params->get('module');
    $fileTypes = array('_'.$fileType, $fileType);
    $ignoreDefault = false;
    $arr = array('*'=>array(), $params->get('action')=>array());
    $ntt = array('*'=>array(), $params->get('action')=>array());

    if(array_key_exists('*',$pageFiles)) {
        foreach($fileTypes as $fileType) {
            if(array_key_exists($fileType,$pageFiles['*'])) {
                foreach($pageFiles['*'][$fileType] as $index) {
                    if($fileType[0]=="_") {
                        $ntt['*'][] = $index;
                    }
                    else {
                        $arr['*'][] = $index;
                    }
                }
            }
        }
    }

    if(array_key_exists($module,$pageFiles)) {
        foreach($actions as $action) {
            if(array_key_exists($action,$pageFiles[$module])) {
                if(array_key_exists('ignoreDefault',$pageFiles[$module][$action])) $ignoreDefault = true;
                foreach($fileTypes as $fileType) {
                    if(array_key_exists($fileType,$pageFiles[$module][$action])) {
                        foreach($pageFiles[$module][$action][$fileType] as $index) {
                            if($fileType[0]=="_") {
                                $ntt[$action][] = $index;
                            }
                            else {
                                $arr[$action][] = $index;
                            }
                        }
                    }
                }
            }
        }
    }
    if($ignoreDefault) {
        $arr = $arr[$actions[1]];
        $ntt = $arr[$actions[1]];
    }
    else {
        $arr = array_merge($arr[$actions[0]],$arr[$actions[1]]);
        $ntt = array_merge($ntt[$actions[0]],$ntt[$actions[1]]);
    }
    $arr = array_unique(array_merge($arr,$ntt));

    $def = ($fileType=='css') ? 'href' : 'src';
    foreach($arr as $index) {
        if(is_array($filesList[$index])) {
            $item = array_merge($defaultElem,$filesList[$index]);
        }
        else {
            $item = array_merge($defaultElem, array($def=>$filesList[$index]));
        }
        $tmp = '';
        foreach($item as $attr => $value) $tmp .= $attr.'="'.$value.'" ';

        echo sprintf($format, $tmp).PHP_EOL;
    }
}