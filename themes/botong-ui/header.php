<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php include_once('main/settings.php') ?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php if (isset($metaRefresh)): ?>
		<meta http-equiv="refresh" content="<?php echo $metaRefresh['seconds'] ?>; URL=<?php echo $metaRefresh['location'] ?>" />
		<?php endif ?>
		<title><?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?></title>
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500&display=swap" rel="stylesheet">
		<link rel="stylesheet" href="<?php echo $this->themePath('css/fluxcpfonts.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
		<link rel="stylesheet" href="<?php echo $this->themePath('css/main.css') ?>" type="text/css" media="screen" title="" charset="utf-8" />
        <link type='text/css' rel="stylesheet" href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' />
        <link type='text/css' rel="stylesheet" href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<link href="<?php echo $this->themePath('css/flux/recaptcha.css') ?>" rel="stylesheet" type="text/css" media="screen" title="" charset="utf-8" />
		<?php endif ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="<?php echo $this->themePath('js/countdown.js') ?>"></script>
        <script src="<?php echo $this->themePath('js/flux.console.js') ?>"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="<?php echo $this->themePath('css/OverlayScrollbars.min.css') ?>">
        <script src="<?php echo $this->themePath('js/jquery.overlayScrollbars.min.js') ?>"></script>
        <?php if(isset($json_arr)): ?>
        <link type="text/css" rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
        <script src="<?php echo $this->themePath('js/jquery-ui.js') ?>"></script>
        <?php endif ?>
        <script>
            $(document).ready(function() {
                $('body').tooltip({
                    selector: '[data-toggle=tooltip]',
                    delay: {"show":200, "hide":0},
                });


                $('[data-toggle=tooltip]').on('shown.bs.tooltip', function (e) {
                    $(this).removeAttr('title');
                    $(this).attr('tooltip-replacealso',`#${$('.tooltip').attr('id')}`);
                })
                $('[data-toggle=tooltip]').on('show.bs.tooltip', function (e) {
                    $(this).removeAttr('title');
                })
                $('[data-toggle=tooltip]').on('hidden.bs.tooltip', function (e) {
                    $(this).removeAttr('tooltip-replacealso');
                    $(this).attr('title',$(this).attr('data-original-title'));
                })
                $('[data-toggle="popover"]').popover()
            });
        </script>
		<script type="text/javascript">
			function reload(){
				window.location.href = '<?php echo $this->url ?>';
			}
		</script>
		
		<script type="text/javascript">
			function updatePreferredServer(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_server_form.preferred_server.value = preferred;
				document.preferred_server_form.submit();
			}
			function updatePreferredTheme(sel){
				var preferred = sel.options[sel.selectedIndex].value;
				document.preferred_theme_form.preferred_theme.value = preferred;
				document.preferred_theme_form.submit();
			}
		</script>
		
		<?php if (Flux::config('EnableReCaptcha') && Flux::config('ReCaptchaTheme')): ?>
		<script type="text/javascript">
			 var RecaptchaOptions = {
			    theme : '<?php echo Flux::config('ReCaptchaTheme') ?>'
			 };
		</script>
		<?php endif ?>
		
	</head>
	<body>
        <?php include_once('main/modal.php') ?>
        <nav class="naviBottom">
            <nav class="pageNavi">
                <?php echo mainNavigation($mainNavigation); ?>
            </nav>
            <nav class="mainNaviBottom">
                <section class="logo">
                    <img src="<?php echo $this->themePath('images/logo.png') ?>" />
                </section>
                <?php include_once('main/status_circles.php') ?>
                <?php include_once('main/user.php') ?>
            </nav>
		</nav>
		<main>
			<?php if (count(Flux::$appConfig->get('ThemeName', false)) > 1): ?>
					<span>Theme:
					<select name="preferred_theme" onchange="updatePreferredTheme(this)">
						<?php foreach (Flux::$appConfig->get('ThemeName', false) as $themeName): ?>
						<option value="<?php echo htmlspecialchars($themeName) ?>"<?php if ($session->theme == $themeName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($themeName) ?></option>
						<?php endforeach ?>
					</select>
					</span>
					
					<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_theme_form" style="display: none">
					<input type="hidden" name="preferred_theme" value="" />
					</form>
			<?php endif ?>
            <?php if(!in_array($params->get('module'), array('main'))): ?>
            <section class="botongui">
                            <header class="topbar d-flex w-100 justify-content-space-between align-items-center">
                                <h1 class="page_title"><?php echo $title ?></h1>
                                <div class="pagemenu">
                                    <div class="dropdown_container" >
                                    <div class="menu_container " id="pagemenu_bar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="toggle"><div  data-toggle="tooltip" title="Menu" ><i class="fas fa-caret-down"></i></div></div>
                                    <div class="dropdown-menu" aria-labelledby="pagemenu_bar">
                                                                    <?php include $this->themePath('main/submenu.php', true) ?><div class="dropdown-divider"></div>
                                                                    <?php include $this->themePath('main/pagemenu.php', true) ?>
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
                                <div class="col">
            <?php endif ?>
								<?php if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1'): ?>
									<p class="notice">Please change your <strong>ServerAddress</strong> directive in your application config to your server's real address (e.g., myserver.com).</p>
								<?php endif ?>
								
								<!-- Messages -->
								<?php if ($message=$session->getMessage()): ?>
									<p class="message"><?php echo htmlspecialchars($message) ?></p>
								<?php endif ?>
								
								
								<!-- Credit balance -->
								<?php if (in_array($params->get('module'), array('donate', 'purchase'))) include $this->themePath('main/balance.php', true) ?>
