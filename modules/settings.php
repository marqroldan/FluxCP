<?php
if (!defined('FLUX_ROOT')) exit;

$hConfig = array(
    'resultsBatchLimit' => 45, 
    'defaultColValue' => 0,
    'columnDefault' => array(
        'item' => array(
            'tables' => array(
                'item_db','item_db2',
            ),
            'columns' => array(
                'id' => array(
                    'replace_with' => 'item_id',
                    ),
                'name_english' => array(
                    'default' => '',
                ),
                'name_japanese' => array(
                    'default' => '',
                ),
                "trade_group" => array(
                    'default' => '',
                ),
                "nouse_group" => array(
                    'default' => '',
                ),
                "stack_flag" => array(
                    'default' => '',
                ),
                "script" => array(
                    'default' => '',
                ),
                "equip_script" => array(
                    'default' => '',
                ),
                "unequip_script" => array(
                    'default' => '',
                ),
            ),
        ),
        'mob' => array(

        ),
    ),

);

Flux::config('hConfig',$hConfig);