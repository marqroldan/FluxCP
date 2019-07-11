<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php if(!isset($json_arr) && empty($json_arr)): ?>
</div></div></div></section>
<?php endif ?>
</main>
<span class="fluxDetails">
Powered by FluxCP (https://github.com/HerculesWS/FluxCP) and Hercules (https://github.com/HerculesWS/Hercules)
Version <?php echo htmlspecialchars(Flux::VERSION) ?> &#64;<?php echo Flux::REPOSVERSION ? Flux::REPOSVERSION : '' ?>

Page generated in <?php echo round(microtime(true) - __START__, 5) ?> second(s).
Number of queries executed: <?php echo (int)Flux::$numberOfQueries ?>.
<?php if (Flux::config('GzipCompressOutput')): ?>Gzip Compression: Enabled.<?php endif ?>
</span>
        <?php loadFiles($params, $scriptFiles, 'script', $pageFiles, $scriptDefaultElem, "<script %s ></script>") ?>
	</body>
</html>
