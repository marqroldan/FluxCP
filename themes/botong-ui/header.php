<?php if (!defined('FLUX_ROOT')) exit; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
		<?php if (Flux::config('EnableReCaptcha')): ?>
		<link href="<?php echo $this->themePath('css/flux/recaptcha.css') ?>" rel="stylesheet" type="text/css" media="screen" title="" charset="utf-8" />
		<?php endif ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
		<script src="<?php echo $this->themePath('js/flux.console.js') ?>"></script>
		
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
			// Preload spinner image.
			var spinner = new Image();
			spinner.src = '<?php echo $this->themePath('img/spinner.gif') ?>';
			
			function refreshSecurityCode(imgSelector){
				$(imgSelector).attr('src', spinner.src);
				
				// Load image, spinner will be active until loading is complete.
				var clean = <?php echo Flux::config('UseCleanUrls') ? 'true' : 'false' ?>;
				var image = new Image();
				image.src = "<?php echo $this->url('captcha') ?>"+(clean ? '?nocache=' : '&nocache=')+Math.random();
				
				$(imgSelector).attr('src', image.src);
			}
			function toggleSearchForm()
			{
				//$('.search-form').toggle();
				$('.search-form').slideToggle('fast');
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
        <nav class="naviBottom">
            <nav class="pageNavi">
                <ul>
                    <li>Home</li>
                    <li>Forums</li>
                    <li>Ranking</li>
                    <li><a href="database.html">Database</a></li>
                    <li>FAQ</li>
                </ul>
            </nav>
            <nav class="mainNaviBottom">
                <section class="logo">
                    <img src="<?php echo $this->themePath('images/logo.png') ?>" />
                </section>
                <ul>
                    <li>Emperium</li>
                    <li>2</li>
                    <li>3</li>
                </ul>
                <section class="user">
                    <div class="user_details">
                        <span class="user_name">
                            <?php echo $session->account->userid ?>
                        </span>
                        <span class="user_notifications">
                            0 Notifications
                        </span>
                    </div>
                    <div class="user_picture_area">
                        <div class="user_picture" style="background: url('https://www.gravatar.com/avatar/<?php echo md5($session->account->email) ?>?s=47') ">
                        </div>
                    </div>
                    <div class="fcp">
                            <div class="fcp_modal">
                                <div class="row h-100 nomargin user_info_market">
                                        <div class="d-flex flex-column justify-content-center align-items-center">
                                            <span data-toggle="tooltip" data-placement="left" title="Go to Marketplace">
                                                <i class="fas fa-store"></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-grow-1 currency-flex-box justify-content-center align-items-center">
                                                <div class="d-flex flex-row justify-content-center align-items-center flex-wrap">
                                                    <div class="user_info_market_currency">
                                                        <span class="fluxicon-Zeny"></span>
                                                        <span class="value">9,999,999,999</span>
                                                    </div>
                                                    <div class="user_info_market_currency">
                                                        <span class="fluxicon-Credits"></span>
                                                        <span class="value">9,999,999,999</span>
                                                    </div>
                                                    <div class="user_info_market_currency">
                                                        <span class="fluxicon-Vote"></span>
                                                        <span class="value">9,999,999,999</span>
                                                    </div>
                                                    <div class="user_info_market_currency">
                                                        <span class="fluxicon-Events"></span>
                                                        <span class="value">9,999,999,999</span>
                                                    </div>
                                                </div>
                                        </div>
                                </div>
                            </div>
                        <div class="fcp_modal">
                            <div class="row h-100 nomargin user_info">
                                    <div class="d-flex flex-column justify-content-center align-items-center user_info_menu">
                                        <span data-toggle="tooltip" data-placement="left" title="Account Settings">
                                            <i class="fas fa-cog"></i>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="left" title="The account gender is male.">
                                            <i class="fas fa-mars"></i>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="left" title="Account Characters">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <span data-toggle="tooltip" data-placement="left" title="Storage">
                                            <i class="fas fa-briefcase"></i>
                                        </span>
                                    </div>
                                    <div class="v_divider"></div>
                                    <div class="d-flex flex-column justify-content-center align-items-center">
                                            <table class="m_user_info">
                                                    <tr>
                                                        <th>
                                                            Birthday
                                                        </th>
                                                        <td>
                                                            SEPTEMBER 29, 2019
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            GAME LAST LOGIN
                                                        </th>
                                                        <td>
                                                            SEPTEMBER 29, 2019 08:56PM
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            GAME LAST IP
                                                        </th>
                                                        <td>
                                                                192.168.8.254, USA
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            GAME CURRENT IP
                                                        </th>
                                                        <td>
                                                                192.168.8.254, USA
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            CP LAST LOGIN
                                                        </th>
                                                        <td>
                                                            SEPTEMBER 29, 2019 08:56PM
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            CP LAST IP
                                                        </th>
                                                        <td>
                                                                192.168.8.254, USA
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>
                                                            CP CURRENT IP
                                                        </th>
                                                        <td>
                                                            192.168.8.254, USA
                                                        </td>
                                                    </tr>
                                            </table>
                                    </div>
                            </div>
                        </div>
                    </div>
                </section>
            </nav>
		</nav>
		
		<main>
								<?php if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1'): ?>
									<p class="notice">Please change your <strong>ServerAddress</strong> directive in your application config to your server's real address (e.g., myserver.com).</p>
								<?php endif ?>
								
								<!-- Messages -->
								<?php if ($message=$session->getMessage()): ?>
									<p class="message"><?php echo htmlspecialchars($message) ?></p>
								<?php endif ?>
								
								<!-- Sub menu -->
								<?php include $this->themePath('main/submenu.php', true) ?>
								
								<!-- Page menu -->
								<?php include $this->themePath('main/pagemenu.php', true) ?>
								
								<!-- Credit balance -->
								<?php if (in_array($params->get('module'), array('donate', 'purchase'))) include $this->themePath('main/balance.php', true) ?>
