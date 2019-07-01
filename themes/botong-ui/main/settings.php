<?php if (!defined('FLUX_ROOT')) exit;
include_once('functions.php');
$mainNavigation = array(
    //do not remove ulSettings
    'ulSettings' => array(
        'class' => '',
    ),
    //list of li elements
    'children' => array(
        array(
            //'class' 
            'content' => "<a href='".$this->url('main','index')."'>Home</a>",
        ),
        array(
            //'class' 
            'content' => "<a href='#'>Forums</a>",
        ),
        array(
            //'class' 
            'content' => "<a href='#'>Ranking</a>",
            'data-toggle' => "popover",
            'data-trigger' => "focus",
            'data-placement' => "top",
            'data-html' => "true",
            'data-content' => "
                <a href=\"{$this->url('ranking','character')}\">Character</a><br/>
                <a href=\"{$this->url('ranking','guild')}\">Guild</a><br/>
                <a href=\"{$this->url('ranking','zeny')}\">Zeny</a><br/>
                <a href=\"{$this->url('ranking','death')}\">Death</a><br/>
                <a href=\"{$this->url('ranking','alchemist')}\">Alchemist</a><br/>
                <a href=\"{$this->url('ranking','blacksmith')}\">Blacksmith</a>
            ",
        ),
        array(
            //'class' 
            'content' => "<a href='#'>Database</a>", // it has to be a link for the popover to work
            'data-toggle' => "popover",
            'data-trigger' => "focus",
            'data-placement' => "top",
            'data-html' => "true",
            'data-content' => "
                <a href=\"{$this->url('item','index')}\">Item Database</a><br/>
                <a href=\"{$this->url('monster')}\">Monster Database</a>
            ",
        ),
        array(
            //'class' 
            'content' => "<a href='#'>FAQ</a>",
        ),
    ),
);















