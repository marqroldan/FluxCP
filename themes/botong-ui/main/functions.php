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