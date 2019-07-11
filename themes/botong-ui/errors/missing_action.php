<?php if (!defined('FLUX_ROOT')) exit; ?>
<section class="botongui">
        <div class="container-fluid max_height overflow-hidden">
                <div class="row h-100 overflow-hidden">
                        <div class="col d-flex flex-wrap w-100 h-100 justify-content-center align-items-center">
                            <div class="unauthorized"><?php echo htmlspecialchars(Flux::message('MissingActionHeading')) ?>
                                <div class="unauthorized_p">
                                    <p><?php echo htmlspecialchars(Flux::message('MissingActionModLabel')) ?> <span class="module-name"><?php echo $this->params->get('module') ?></span>, <?php echo htmlspecialchars(Flux::message('MissingActionActLabel')) ?> <span class="module-name"><?php echo $this->params->get('action') ?></span></p>
                                    <p><?php echo htmlspecialchars(Flux::message('MissingActionReqLabel')) ?> <span class="request"><?php echo $_SERVER['REQUEST_URI'] ?></span></p>
                                    <p><?php echo htmlspecialchars(Flux::message('MissingActionLocLabel')) ?> <span class="fs-path"><?php echo $realActionPath ?></span></p>
                                    </div>
                            </div>
                        </div>
                </div>
        </div>
</section>