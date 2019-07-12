<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
        data = <?php echo json_encode($json_arr) ?>;
        items_url = '<?php echo FLUX_DATA_DIR."/items/" ?>';
        item_desc = {};
	push = {
               title: "<?php echo Flux::config('SiteTitle'); if (isset($title)) echo ": $title" ?>", 
                page: "<?php echo $this->url('item') ?>"
        };
        itemIndexURL = `<?php echo $this->url('item','index') ?>`;
        itemViewURL = `<?php echo $this->url('item','view') ?>`;
        monsterViewURL = `<?php echo $this->url('monster','view') ?>`;
        monsterIndexURL = `<?php echo $this->url('monster','index') ?>`;
        pageMenu = `
                    <?php foreach($sortable as $key2 => $sort): $key = is_numeric($key2) ? $sort : $key2; ?>
                    <div class='sort_order dropdown-item' sort-param="<?php echo $key ?>" sort-val="<?php echo !is_numeric($key2) ? array_search(strtoupper($sort),$json_arr['sortable']): 2 ?>"><i class="fas <?php echo !is_numeric($key2) ? (array_search(strtoupper($sort),$json_arr['sortable'])==0 ? 'fa-arrow-up' : 'fa-arrow-down') : ''?>"></i><?php echo $json_arr['labels'][$key]; ?></div>
                    <?php  endforeach; ?>`;
        themePath = `<?php echo $this->themePath('/') ?>`
</script>

<section class="botongui">
        <div class="container-fluid max_height overflow-hidden">
                <div class="row h-100 overflow-hidden">
                        <div class="col search">
                                <div class="text-center mx-auto">
                                        <input type="text" name="itemText" class="search_tb" placeholder="Search for Item ID or Name" value="<?php echo $params->get('item_id') ?>"/>
                                        <br/>
                                        <span class="search_reset">Reset Filter</span>
                                </div>
                                <?php foreach ($search_params as $key => $data) : ?>
                                        <?php foreach ($data as $name => $param_data) : $name = str_replace("`", "", $name); ?>

                                                <div class="search_param" sp_type="<?php echo $key ?>" sp_name="<?php echo $name ?>" <?php echo array_key_exists('choices', $param_data) ? 'sp_total_choices="' . count($param_data['choices']) . '"' : '' ?>>
                                                        <div class="search_param_top d-flex align-items-center flex-grow-0" data-toggle="collapse" data-target="#sp_type_<?php echo $name ?>" aria-expanded="false" aria-controls="sp_type_<?php echo $name ?>">
                                                                <div class="search_param_top_label"><?php echo $param_data['label'] ?></div>
                                                                <div class="search_param_top_value"><span class="value" sp_type="<?php echo $name ?>"></span></div>
                                                        </div>
                                                        <div class="collapse search_param_child" id="sp_type_<?php echo $name ?>">
                                                                <div style="height: 10px; width: 100%;"></div>
                                                                <?php if ($key == 'range') : ?>
                                                                        <div class="d-flex w-100 justify-content-space-between">
                                                                                <div class="search_param_top_label sp_type_<?php echo $name ?>_min_label"></div>
                                                                                <div class="search_param_top_label sp_type_<?php echo $name ?>_max_label"></div>
                                                                        </div>
                                                                        <div class="slider-range" min="<?php echo $param_data['min'] ?>" max="<?php echo $param_data['max'] ?>" low="<?php echo $param_data['min'] ?>" high="<?php echo $param_data['max'] ?>" sp_type="<?php echo $name ?>"></div>

                                                                <?php elseif ($key == 'checkbox') : ?>
                                                                        <div class="search_togglecheckbox" cb_toggler sp_name="<?php echo $name ?>" current_status="checked" data-text-checked="Uncheck all" data-text-unchecked="Check all">Uncheck all</div>
                                                                        <?php foreach ($param_data['choices'] as $value => $val_label) : ?>
                                                                                <input type="checkbox" sp_name="<?php echo $name ?>" name="sp_type_<?php echo $name ?>[]" value="<?php echo $value ?>" id="sp_cb_<?php echo md5($name . $val_label) ?>" checked><label for="sp_cb_<?php echo md5($name . $val_label) ?>"><?php echo $val_label ?></label>
                                                                        <?php endforeach ?>

                                                                <?php endif ?>
                                                        </div>
                                                </div>

                                        <?php endforeach ?>
                                <?php endforeach ?>
                        </div>
                        <div class="v_divider"></div>
                        <div class="col _content ">
                                <div class="h-100 w-100 __it">
                                <div class="col d-flex w-100 flex-wrap justify-content-start align-items-start item_data_container">
                                        <div class="item_container" item-type="t_loader" data-toggle="tooltip" data-placement="top" title="Loading"><div class="loader"></div></div>
                                </div>
                                </div>
                        </div>
                </div>
        </div>
</section>