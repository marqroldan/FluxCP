<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
    servURL = '<?php echo $this->url('server','status',array('output'=>'json')) ?>'
    woeURL = "<?php echo $this->url('woe','index',array('output'=>'json')) ?>"
</script>
<ul class="status_circles">
    <li class="emperium_status" data-html="true" data-toggle="tooltip"><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content"data-toggle="modal" data-target="#modal_botongui" data-function="woeStatusModal">
            <div class="icon icon_woe"></div>
            <div class="emperium_content" style="display: none"></div>
        </div>
    </li>
    <li class="online_players" data-html="true" data-toggle="tooltip" title="Loading"><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content" data-toggle="modal" data-target="#modal_botongui" data-function="fetchServerStatus">
            <div class="icon icon_onlineplayers"></div>
        </div>
    </li>
    <li class="server_status" data-html="true" data-toggle="tooltip" title="Loading"><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content" data-toggle="modal" data-target="#modal_botongui" data-function="fetchServerStatus">
            <div class="icon icon_serverstatus"></div>
        </div>
    </li>
</ul>