<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
	$(document).ready(function() {
		$('.botonguiPage').overlayScrollbars({
                className       : "os-theme-dark",
                sizeAutoCapable : true,
                paddingAbsolute : false,
                scrollbars : {
                        clickScrolling : true,
                        autoHide: 'leave', 
                        autoHideDelay: 400, 
                },
        }); 
	});
</script>
<?php $passwordNotes = array(sprintf("Your password must be between %d and %d characters.", Flux::config('MinPasswordLength'), Flux::config('MaxPasswordLength')));
	if (Flux::config('PasswordMinUpper') > 0) $passwordNotes[] = sprintf(Flux::message('PasswordNeedUpper'), Flux::config('PasswordMinUpper'));
	if (Flux::config('PasswordMinLower') > 0) $passwordNotes[] = sprintf(Flux::message('PasswordNeedLower'), Flux::config('PasswordMinLower'));
	if (Flux::config('PasswordMinNumber') > 0) $passwordNotes[] = sprintf(Flux::message('PasswordNeedNumber'), Flux::config('PasswordMinNumber'));
	if (Flux::config('PasswordMinSymbol') > 0) $passwordNotes[] = sprintf(Flux::message('PasswordNeedSymbol'), Flux::config('PasswordMinSymbol'));
	if (!Flux::config('AllowUserInPassword')) $passwordNotes[] = Flux::message('PasswordContainsUser');
?>
<div class="container-fluid p-0 h-100 d-flex flex-wrap justify-content-center align-items-center">
	<div class="botonguiPage d-flex flex-wrap justify-content-center align-items-center">
		<form action="<?php echo $this->url ?>" method="post" class="generic-form">
			<div class="botonguiPageCaption text-left"><?php printf(htmlspecialchars(Flux::message('AccountCreateInfo')), '<a href="'.$this->url('service', 'tos').'">'.Flux::message('AccountCreateTerms').'</a>') ?></div>
			<?php if (isset($errorMessage)): ?>
			<div class="botonguiPageCaption botonguiPageCaptionError"><?php echo htmlspecialchars($errorMessage) ?></div>
			<?php endif ?>
			<hr/>
			<?php if (count($serverNames) === 1): ?>
			<input type="hidden" name="server" value="<?php echo htmlspecialchars($session->loginAthenaGroup->serverName) ?>">
			<?php endif ?>
			<?php if (count($serverNames) > 1): ?>
			<div class="form-group">
				<label for="register_server"><?php echo htmlspecialchars(Flux::message('AccountServerLabel')) ?></label>
				<div class="input-group">
					<select name="server" id="register_server"<?php if (count($serverNames) === 1) echo ' disabled="disabled"' ?> class="form-control">
					<?php foreach ($serverNames as $serverName): ?>
						<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($params->get('server') == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
					<?php endforeach ?>
					</select>
				</div>
			</div>
			<?php endif ?>
			<div class="form-group">
				<label for="register_username"><?php echo htmlspecialchars(Flux::message('AccountUsernameLabel')) ?></label>
				<div class="input-group">
					<div class="input-group-prepend">
					<span class="input-group-text" id="registerUsername"><i class="fas fa-user"></i></span>
					</div>
					<input type="text" name="username" class="form-control" id="register_username" aria-describedby="registerUsername" required>
				</div>
			</div>
			<div>
			<div class="form-group">
				<label for="register_password"><?php echo htmlspecialchars(Flux::message('AccountPasswordLabel')) ?></label>
				<div class="input-group">
					<div class="input-group-prepend" data-html="true" data-toggle="tooltip" title="<div class='tooltipAlignLeft'><li><?php echo implode("<li>",$passwordNotes) ?></div>">
					<span class="input-group-text" id="registerPassword"><i class="fas fa-lock"></i></span>
					</div>
					<input type="password" class="form-control"  name="password"  id="register_password" aria-describedby="registerPassword" required>
				</div>
				<label for="register_confirm_password"><?php echo htmlspecialchars(Flux::message('AccountPassConfirmLabel')) ?></label>
				<div class="input-group">
					<div class="input-group-prepend" data-html="true" data-toggle="tooltip" title="<div class='tooltipAlignLeft'><li><?php echo implode("<li>",$passwordNotes) ?></div>">
					<span class="input-group-text" id="registerPasswordConfirm"><i class="fas fa-lock"></i></span>
					</div>
					<input type="password" class="form-control"  name="confirm_password"  id="register_confirm_password" aria-describedby="registerPasswordConfirm" required>
				</div>
			</div>
			</div>
			
			<div class="form-group">
				<label for="register_email_address"><?php echo htmlspecialchars(Flux::message('AccountEmailLabel')) ?></label>
				<div class="input-group">
					<input type="text" class="form-control"  name="email_address"  id="register_email_address"  value="<?php echo htmlspecialchars($params->get('email_address')) ?>" required>
					<?php if (Flux::config('RequireEmailConfirm')): ?>
						<div class="valid-feedback"><strong>Note:</strong> You will need to provide a working e-mail address to confirm your account before you can log-in.</div>
					<?php endif ?>
				</div>
			</div>
			
			<div class="form-group container">
				<div class="row">
					<div class="col p-0">
						<label data-toggle="tooltip" title="<?php echo htmlspecialchars(Flux::message('AccountCreateGenderInfo')) ?>"><?php echo htmlspecialchars(Flux::message('AccountGenderLabel')) ?></label>
					</div>
				</div>
				<div class="row">
					<div class="col p-0">
						<label class="w-100"><input type="radio" name="gender" class="form-check-input ml-0 mr-1 position-static" id="register_gender_m" value="M"<?php if ($params->get('gender') === 'M') echo ' checked="checked"' ?> /> <?php echo $this->genderText('M') ?></label>
					</div>
					<div class="col p-0">
						<label class="w-100"><input type="radio" name="gender"  class="form-check-input ml-0 mr-1  position-static" id="register_gender_f" value="F"<?php if ($params->get('gender') === 'F') echo ' checked="checked"' ?> /> <?php echo $this->genderText('F') ?></label>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="validationServerUsername"><?php echo htmlspecialchars(Flux::message('AccountBirthdateLabel')) ?></label>
				<?php echo $this->dateField('birthdate',null,0, null, "form-control",'<span class="date-field">%s<br>%s<br>%s</span>') ?>
			</div>
			<?php if (Flux::config('UseCaptcha')): ?>
			<div class="form-group">
				<label for="register_security_code"><?php echo htmlspecialchars(Flux::message('AccountSecurityLabel')) ?></label>
				<?php if (Flux::config('EnableReCaptcha')): ?>
				<?php echo $recaptcha ?>
				<?php else: ?>
				<div class="input-group">
					<div class="input-group-prepend"><label for="register_security_code"><div class="security-code"><img src="<?php echo $this->url('captcha') ?>" /></div></label>
					</div>
					<input type="text" class="form-control" style="height: 50px;" name="security_code" id="register_security_code" />
				</div>
					<div style="font-size: 80%;"><a href="javascript:refreshSecurityCode('.security-code img')"><?php echo htmlspecialchars(Flux::message('RefreshSecurityCode')) ?></a></div>
				<?php endif ?>
			</div>
			<?php endif ?>
			<hr/>
			<div class="text-center">
				<div class="botonguiPageCaption"><?php printf(htmlspecialchars(Flux::message('AccountCreateInfo2')), '<a href="'.$this->url('service', 'tos').'">'.Flux::message('AccountCreateTerms').'</a>') ?></div>
				<br/>
				<button class="btn btn-warning" type="submit"><?php echo htmlspecialchars(Flux::message('AccountCreateButton')) ?></button>
			</div>
		</form>
	</div>
</div>