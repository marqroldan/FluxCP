<?php if (!defined('FLUX_ROOT')) exit; ?>
<section class="botongui">
        <div class="container-fluid max_height overflow-hidden">
                <div class="row h-100 overflow-hidden">
                        <div class="col d-flex flex-wrap w-100 h-100 justify-content-center align-items-center">
                            <div class="unauthorized"><?php echo htmlspecialchars(Flux::message('UnauthorizedHeading')) ?>
                                <div class="unauthorized_p"><?php printf(Flux::message('UnauthorizedInfo'), $metaRefresh['location']) ?></div>
                            </div>
                        </div>
                </div>
        </div>
</section>