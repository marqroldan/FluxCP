<?php if (!defined('FLUX_ROOT')) exit; ?>
			
			<?php if (count(Flux::$appConfig->get('ThemeName', false)) > 1): ?>
			<tr>
				<td colspan="3"></td>
				<td align="right">
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
				</td>
				<td></td>
			</tr>
			<?php endif ?>
		</table>
</main>
<span class="fluxDetails">
Powered by FluxCP (https://github.com/HerculesWS/FluxCP) and Hercules (https://github.com/HerculesWS/Hercules)
Version <?php echo htmlspecialchars(Flux::VERSION) ?> &#64;<?php echo Flux::REPOSVERSION ? Flux::REPOSVERSION : '' ?>

Page generated in <?php echo round(microtime(true) - __START__, 5) ?> second(s).
Number of queries executed: <?php echo (int)Flux::$numberOfQueries ?>.
<?php if (Flux::config('GzipCompressOutput')): ?>Gzip Compression: Enabled.<?php endif ?>
</span>
	</body>
</html>
