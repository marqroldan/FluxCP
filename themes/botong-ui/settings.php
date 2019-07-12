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
            'content' => "<a href='".$this->url('purchase','index')."'>Purchase</a>",
        ),
        array(
            //'class' 
            'content' => "<a href='".$this->url('donate','index')."'>Donate</a>",
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

$hideTemplateOn = array(
    /*
    'module' => array(
        'pages'
    )*/
    'account' => array(
        'create',
        'login'
    ),
    'unauthorized',
    'error',
    'main',
);

if((array_key_exists($params->get('module'),$hideTemplateOn) && in_array($params->get('action'),$hideTemplateOn[$params->get('module')])) || (in_array($params->get('module'), $hideTemplateOn))) {
    $hideEverything = true;
}

$cssDefaultElem = array(
    "rel" => "stylesheet",
    "type" => "text/css",
    "media" => "screen",
    "title" => "",
    "charset" => "utf-8",
);

$scriptDefaultElem = array(
    "type" => "text/javascript",
);

$defaultScriptFiles = array(
    'jquery' => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js",
    'bootstrap' => array(
        "src" => "https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js",
        "integrity" => "sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o",
        "crossorigin" => "anonymous",
    ),
    'countdown' => $this->themePath('js/countdown.js'),
);

$otherScriptFiles = array(
    'jqueryui' => $this->themePath('js/jquery-ui.js'),
    'overlayscrollbar' => $this->themePath('js/jquery.overlayScrollbars.min.js'),
    'fluxconsole' => $this->themePath('js/flux.console.js'),
    'fluxitemdb' => $this->themePath('js/flux.item.js'),
    'fluxmonsterdb' => $this->themePath('js/flux.monster.js'),
);

$templateScriptFiles = array(
   "jquerymigrate" => $this->themePath("lib/jquery/jquery-migrate.min.js"),
   "easing" => $this->themePath("lib/easing/easing.min.js"),
   "mobilenav" => $this->themePath("lib/mobile-nav/mobile-nav.js"),
   "wow" => $this->themePath("lib/wow/wow.min.js"),
   "waypoints" => $this->themePath("lib/waypoints/waypoints.min.js"),
   "counterup" => $this->themePath("lib/counterup/counterup.min.js"),
   "owlcarousel" => $this->themePath("lib/owlcarousel/owl.carousel.min.js"),
   "isotope" => $this->themePath("lib/isotope/isotope.pkgd.min.js"),
   "lightbox" => $this->themePath("lib/lightbox/js/lightbox.min.js"),
   "rapid_script" => $this->themePath("js/main.js"),
);

$defaultCssFiles = array(
    'google_fonts' => "https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,500,600,700,700i|Montserrat:300,400,500,600,700",
    'fluxcp_fonts' => $this->themePath('css/fluxcpfonts.css'),
    'fluxcp_main' => $this->themePath('css/main.css'),
    'bootstrap' => 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
    'datatables' => '//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css',
    'fontawesome' => array(
        "href" => "https://use.fontawesome.com/releases/v5.8.2/css/all.css",
        "integrity" => "sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay",
        "crossorigin" => "anonymous"
    ),
);

$otherCssFiles = array(
    'recaptcha' => $this->themePath('css/flux/recaptcha.css'),
    'jqueryui' => "https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css",
    'overlayscrollbar' => $this->themePath('css/OverlayScrollbars.min.css'),
);

$templateCssFiles = array(
    ////////////// These are the CSS files for the Rapid Template 
    'animate' => $this->themePath("lib/animate/animate.min.css"),
    'ionicons' => $this->themePath("lib/ionicons/css/ionicons.min.css"),
    'owlcarousel' => $this->themePath("lib/owlcarousel/assets/owl.carousel.min.css"),
    'lightbox' => $this->themePath("lib/lightbox/css/lightbox.min.css"),
    'rapid_style' => $this->themePath('css/style.css'),
);

$cssFiles = array_merge($defaultCssFiles, $otherCssFiles, $templateCssFiles);
$scriptFiles = array_merge($defaultScriptFiles, $otherScriptFiles, $templateScriptFiles);

$pageFiles = array(
    '*' => array(
        //global, happens to all first before other specific module
        'css' => array_keys($defaultCssFiles),
        'script' => array_keys($defaultScriptFiles),
        '_script' => array('fluxconsole'), //if the key has _ as the first character it is appended last
    ),
    /*
    'module' => array(
            '*' => array(
                //affects all pages under the module
            ),
        'action' => array(
            'ignoreDefault' => true,    //as long as this is present in the array it will always be true, regardless of its value. it will ignore all *
            'css' => array(),
            'script' => array()
        )
    ),
    */
    'account' => array(
        'create' => array(
            'script' => array('overlayscrollbar'),
            'css' => array('overlayscrollbar'),
        )
    ), 
    'item' => array(
        'index' => array(
            'script' => array('fluxitemdb'),
        )
    ),
    'monster' => array(
        'index' => array(
            'script' => array('fluxmonsterdb'),
        )
        ),
    'main' => array(
        '*' => array(
            'css' => array_keys($templateCssFiles),
            'script' => array_keys($templateScriptFiles),
        )
    )
);


//If you don't want to indicate all the pages and repeat, you can follow this logic
if(isset($json_arr) && !empty($json_arr)) {
    $scriptArr = array(
        'jqueryui', 
        'overlayscrollbar', 
    );
    $cssArr = array(
        'overlayscrollbar','jqueryui'
    );
    $_scriptArr = array('fluxdb');
    $_cssArr = array();
    $scriptFilesArr = array();
    $cssFilesArr = array();
    $_scriptFilesArr = array();
    $_cssFilesArr = array();
    if(array_key_exists($params->get('module'),$pageFiles)) {
        $jMod = $pageFiles[$params->get('module')];
        if(array_key_exists($params->get('action'),$jMod)) {
            $jMod = $jMod[$params->get('action')];
            if(array_key_exists('css',$jMod)) {
                $cssFilesArr = $jMod['css'];
            }
            if(array_key_exists('script',$jMod)) {
                $scriptFilesArr = $jMod['script'];
            }
            if(array_key_exists('_css',$jMod)) {
                $cssFilesArr = $jMod['css'];
            }
            if(array_key_exists('_script',$jMod)) {
                $scriptFilesArr = $jMod['script'];
            }
        }
    }
    $pageFiles[$params->get('module')][$params->get('action')]['script'] = array_merge($scriptArr, $scriptFilesArr);
    $pageFiles[$params->get('module')][$params->get('action')]['css'] = array_merge($cssArr, $cssFilesArr);
    $pageFiles[$params->get('module')][$params->get('action')]['_script'] = array_merge($_scriptArr, $_scriptFilesArr);
    $pageFiles[$params->get('module')][$params->get('action')]['_css'] = array_merge($_cssArr, $_cssFilesArr);
}
