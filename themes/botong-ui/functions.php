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
    $action = $params->get('action');
    $module = $params->get('module');
    $ignoreDefault = false;
    $arr = array();

    if(array_key_exists($module, $pageFiles) && array_key_exists($action, $pageFiles[$module])) {
        if(array_key_exists('ignoreDefault',$pageFiles[$module][$action])) $ignoreDefault = true;
        if(array_key_exists($fileType,$pageFiles[$module][$action])) {
            foreach($pageFiles[$module][$action][$fileType] as $index) {
                $arr[] = $index;
            }
        }
        if(array_key_exists('_'.$fileType, $pageFiles['*'])) {
            foreach($pageFiles['*']['_'.$fileType] as $index) {
                $nt[] = $index;
            }
        }
    }
    if(array_key_exists('*',$pageFiles) && count($pageFiles['*']) > 0 && !$ignoreDefault) {
        $nt = array();
        if(array_key_exists($fileType,$pageFiles['*'])) {
            foreach($pageFiles['*'][$fileType] as $index) {
                $nt[] = $index;
            }
        }
        $arr = array_merge($nt, $arr);
        if(array_key_exists('_'.$fileType, $pageFiles['*'])) {
            foreach($pageFiles['*']['_'.$fileType] as $index) {
                $arr[] = $index;
            }
        }
    }

    $arr = array_unique($arr);

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