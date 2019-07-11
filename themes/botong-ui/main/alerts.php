<?php if (!defined('FLUX_ROOT')) exit; ?>
<?php print_r($session->botonguiMessage);
    if($session->botonguiMessage != '' && !is_array($session->botonguiMessage)) {
        $session->botonguiMessage = array($session->botonguiMessage);
    }
    $curMes = $session->botonguiMessage;
    if (Flux::config('DebugMode') && @gethostbyname(Flux::config('ServerAddress')) == '127.0.0.1') {
        $curMes[] = array(
            'mes' => "<strong>Warning!</strong> Please change your ServerAddress directive in your application config to your server's real address (e.g., myserver.com).",
            'type' => 'alert-warning',
        );
    }
    if ($message=$session->getMessage()) {
        $curMes[] = htmlspecialchars($message);
    }
    $session->botonguiMessage = $curMes;
?>
<div class="fcp alerts">
    <?php foreach ($curMes as $mes):
        $mesType = "alert-primary";
        if(is_array($mes))  {
            $mesType = $mes['type'];
            $mes = $mes['mes'];
        }
        ?>
    <div class="alert <?php echo $mesType ?> alert-dismissible fade show" role="alert">
    <?php echo $mes ?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
    <?php endforeach ?>
</div>
<?php 
    $session->botonguiMessage = null;
?>