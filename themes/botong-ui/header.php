<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php include_once('settings.php');
$title = isset($title) ? $title : '';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
		<?php if (isset($metaRefresh)): ?>
        <meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo $metaRefresh['location'] ?>" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
		<?php endif ?>
        <title><?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?></title>
        <?php loadFiles($params, $cssFiles, 'css', $pageFiles, $cssDefaultElem, "<link %s />") ?>
		<?php if (Flux::config('EnableReCaptcha') && Flux::config('ReCaptchaTheme')): ?>
		<script type="text/javascript">
			 var RecaptchaOptions = {
			    theme : '<?php echo Flux::config('ReCaptchaTheme') ?>'
			 };
		</script>
        <?php endif ?>
	</head>
	<body>
        <div id="smallMenu">
            <nav class="naviBottom">
                <nav class="pageNavi">
                    <?php echo mainNavigation($mainNavigation); ?>
                </nav>
                <nav class="mainNaviBottom">
                    <?php include('main/status_circles.php') ?>
                    <?php include('main/user.php') ?>
                </nav>
            </nav>
        </div>
        <?php include_once('main/modal.php') ?>
        <nav class="naviBottom naviOriginal">
        <?php include_once('main/alerts.php') ?>
            <nav class="pageNavi">
                <?php echo mainNavigation($mainNavigation); ?>
            </nav>
            <nav class="mainNaviBottom">
                <section class="logo">
                    <img src="<?php echo $this->themePath('images/logo.png') ?>" />
                </section>
                <?php include('main/status_circles.php') ?>
                <?php include('main/user.php') ?>
            </nav>
		</nav>
		<main>
            <?php if(!isset($hideEverything)): ?>
            <?php if(!in_array($params->get('module'), array('main'))): ?>
            <section class="botongui">
                <header class="topbar d-flex w-100 justify-content-space-between align-items-center">
                    <h1 class="page_title"><?php echo $title ?></h1>
                    <?php 
                                $pageMenus = array();
                                $subMenuItems = $this->getSubMenuItems();
                                if (!empty($subMenuItems)) {
                                    foreach ($subMenuItems as $menuItem) {
                                        $pageMenus[] = sprintf('<a href="%s" class="dropdown-item %s">%s</a>',
                                        $this->url($menuItem['module'], $menuItem['action']),
                                        $params->get('module') == $menuItem['module'] && $params->get('action') == $menuItem['action'] ? ' active' : '',
                                        htmlspecialchars($menuItem['name']));
                                    }
                                }
                                if (!empty($pageMenuItems)) {
                                    $pageMenus[] = '<div class="dropdown-divider"></div>';
                                    foreach ($pageMenuItems as $menuItemName => $menuItemLink) {
                                        $pageMenus[] = sprintf('<a href="%s" class="dropdown-item page-menu-item">%s</a>', $menuItemLink, htmlspecialchars($menuItemName));
                                    }
                                }
                    ?>
                    <div class="pagemenu">
                        <div class="dropdown_container" >
                            <div class="menu_container  <?php echo count($pageMenus)>0 ? "" : "d-none" ?>" id="pagemenu_bar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="toggle"><div  data-toggle="tooltip" title="Menu" ><i class="fas fa-caret-down"></i></div></div>
                                <div class="dropdown-menu" aria-labelledby="pagemenu_bar">
                                    <?php  echo implode('',$pageMenus); ?>
                                </div>
                            </div>
                    </div>
                </header> 
            </section>
            <?php endif ?>
            <?php if(!isset($json_arr) && empty($json_arr)): ?>
            <section class="botongui">
                    <div class="container-fluid">
                            <div class="row">
                                <div class="col defaultCol">
            <?php endif ?>
            <?php endif ?>
								
								
								<!-- Credit balance -->
								<?php //if (in_array($params->get('module'), array('donate', 'purchase'))) include $this->themePath('main/balance.php', true) ?>