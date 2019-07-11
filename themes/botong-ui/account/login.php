<?php if (!defined('FLUX_ROOT')) exit; ?>
<div class="container-fluid p-0 h-100 d-flex flex-wrap justify-content-center align-items-center">
	<div class="botonguiPage d-flex flex-wrap justify-content-center align-items-center">
	<form action="<?php echo $this->url('account', 'login', array('return_url' => $params->get('return_url'))) ?>" method="post" class="generic-form">
	<?php if (isset($errorMessage)): ?>
	<div class="botonguiPageCaption botonguiPageCaptionError"><?php echo htmlspecialchars($errorMessage) ?></div>
	<?php endif ?>
		<?php if (count($serverNames) === 1): ?>
		<input type="hidden" name="server" value="<?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?>">
		<?php endif ?>
		<div class="form-group">
			<label for="validationServerUsername"><?php echo htmlspecialchars(Flux::message('AccountUsernameLabel')) ?></label>
			<div class="input-group">
				<div class="input-group-prepend">
				<span class="input-group-text" id="loginUsername"><i class="fas fa-user"></i></span>
				</div>
				<input type="text" name="username" class="form-control<?php // is-invalid ?>" id="validationServerUsername" aria-describedby="loginUsername" required>
				<?php /*
				<div class="invalid-feedback">
				Please choose a username.
				</div> */
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="validationServerPassword"><?php echo htmlspecialchars(Flux::message('AccountPasswordLabel')) ?></label>
			<div class="input-group">
				<div class="input-group-prepend">
				<span class="input-group-text" id="loginPassword"><i class="fas fa-lock"></i></span>
				</div>
				<input type="password" class="form-control" name="password" id="validationServerPassword" aria-describedby="loginPassword" required>
			</div>
		</div>
		<?php if (count($serverNames) > 1): ?>
		<div class="form-group">
			<label for="login_server"><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?></label>
			<div class="input-group">
				<select name="server" id="login_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?> class="form-control">
					<?php foreach ($serverNames as $serverName): ?>
					<option value="<?php echo htmlspecialchars($serverName) ?>"><?php echo htmlspecialchars($serverName) ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
		<?php endif ?>
		<?php if (Flux::config('UseLoginCaptcha')): ?>
		<div class="form-group">
			<label for="register_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label>
			<div class="input-group">
				<?php if (Flux::config('EnableReCaptcha')): ?>
					<?php echo $recaptcha ?>
				<?php else: ?>
					<div class="security-code"><img src="<?php echo $this->url('captcha') ?>" /></div>
					<input type="text" name="security_code" id="register_security_code" />
					<div style="font-size: smaller;" class="action">
						<strong><a href="javascript:refreshSecurityCode('.security-code img')"><?php echo htmlspecialchars(Flux::message('RefreshSecurityCode')) ?></a></strong>
					</div>
				<?php endif ?>
				<div class="valid-feedback">
				Looks good!
				</div>
			</div>
		</div>
		<?php endif ?>
		<hr/>
		<div class="text-center">
			<button class="btn btn-secondary" type="submit"><?php echo htmlspecialchars(Flux::message('LoginButton')) ?></button>
			<a href="<?php echo $this->url('account','create') ?>"><button class="btn btn-warning" type="button">Register</button></a>
		</div>
	</form>
	</div>
</div>