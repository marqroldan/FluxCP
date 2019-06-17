<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
        $(document).ready(function() {
                _delay = 0;
                data = <?php echo json_encode($json_arr) ?>;
                dontLoad = false;
                listview = true;
                items_url = '<?php echo FLUX_DATA_DIR."/items/" ?>';
                loader = $(".item_container[item-type=t_loader]");
                
                //create an element in the pagemenu class
                $('.pagemenu_bar').before(`<div class="search_count"><span id="partial_res">0</span>/<span id="total_res">0</span></div>`);
                $('.pagemenu_bar').before(`<div class="view_toggler"><i class="fas fa-bars"></i></div>`);

                $('.view_toggler').on('click',function() {
                        if(!listview) {
                                $(this).find('i').removeClass('fa-th');
                                $(this).find('i').addClass('fa-bars');
                        }
                        else {
                                $(this).find('i').removeClass('fa-bars');
                                $(this).find('i').addClass('fa-th');
                        }
                        listview = !listview;
                        viewToggle();
                });

                function viewToggle() {
                        if(listview) {
                                $('.item[item-view=thumbnail]').removeClass('d-flex');
                                $('.item[item-view=thumbnail]').addClass('d-none');
                                $('.item[item-view=list]').removeClass('d-none');
                                $('.item[item-view=list]').addClass('d-flex');
                                loader.addClass('list');
                                loader.removeClass('thumbnail');
                        }
                        else {
                                $('.item[item-view=list]').removeClass('d-flex');
                                $('.item[item-view=list]').addClass('d-none');
                                $('.item[item-view=thumbnail]').removeClass('d-none');
                                $('.item[item-view=thumbnail]').addClass('d-flex');
                                loader.addClass('thumbnail');
                                loader.removeClass('list');
                        }
                }

                s = $('.__it, .search').overlayScrollbars({
                                className       : "os-theme-dark",
                                sizeAutoCapable : true,
                                paddingAbsolute : true,
                                scrollbars : {
                                        clickScrolling : true,
                                        autoHide: 'leave', 
                                        autoHideDelay: 400, 
                                },
	                        callbacks : {
                                        onScroll: whilescrolling,
                                        onScrollStop : scrollChecky,
                                        onContentSizeChanged: scrollChecky,
                                }
                        }); 
                function whilescrolling() {
                        if (_delay) clearTimeout(_delay);
                        $('.tooltip').remove();
                }
                function scrollChecky() {
                        if(_delay) clearTimeout(_delay);
                        if(loader.isInViewport('.item_data_container')) _delay = setTimeout(dataUpdate(true), 700);
                }

                $('.search_reset').on('click',function(){ 
                        drawSlider(true);
                        $('input[type=checkbox][sp_name]').each(function() {
                                $(this).prop('checked',true).change();
                        });
                        $('[cb_toggler]').each(function() {
                                $(this).attr('current_status','checked');
                                $(this).html($(this).attr('data-text-checked'));
                        });
                        data._params['p'] = 1;
                        data._params_default['p'] = 1;
                        if (_delay) clearTimeout(_delay);
                        _delay = setTimeout(dataUpdate, 700);
                });

                function init() {
                        data._params['p'] = 1;
                        data._params_default['p'] = 1;
                        drawSlider();
                        checkboxCountSelected();
                        showResults(data.items);
                        valChange($('#partial_res'),Object.keys(data.items).length);
                        valChange($('#total_res'),data.total);
                }

                function valChange(selector, next, past='') {
                        $({ Counter: (past=='') ? selector.html() : past }).animate(
                        {   Counter: next },
                        {
                                duration: 1000,
                                easing: 'swing',
                                queue: false,
                                step: function() {selector.text(Math.ceil(this.Counter));},
                                complete: function() {selector.text(next);},
                        });
                }

                function dataUpdate(stat=false) { 
                        $('.tooltip').remove();
                        if(dontLoad) return;
                        dontLoad = true;
                        if(stat) {
                                if(data.total >= data.perPage) data._params['p'] += 1;
                        }
                        dUp = false;
                        for (key in data._params) {
                                if(data._params[key]!=data._params_default[key]) {
                                        dUp = true;
                                        data._params_default[key] = data._params[key];
                                        if(key!='p' && !stat) {
                                                data._params['p'] = 1;
                                                data._params_default['p'] = 1;
                                        }
                                }
                        }
                        if(dUp) {
                                $tmp = $('.search_count').html();
                                $('.search_count').html('Searching...');
                                $.get("<?php echo $this->url('item', 'index', array('output' => 'json', 'data_output' => 'items,search_params,total')) ?>", data._params, function(new_data) {    
                                        $('.search_count').html($tmp);
                                        new_data = JSON.parse(new_data);
                                        data.total = new_data.total
                                        d = new_data.items;
                                        if(stat) {
                                                if(data.total > 0) {
                                                        for (key in d) data.items[key] = d[key];
                                                        loader.show();
                                                        showResults(d);
                                                }
                                                else {
                                                        loader.hide();
                                                }
                                        }
                                        else {
                                                data.items = d;
                                                $('.item_container:not([item-type])').remove()
                                                if(data.total==0 || data.total < data.perPage ) {
                                                        loader.hide();
                                                }
                                                else {
                                                        loader.show(); 
                                                }
                                                        showResults(data.items);
                                        }
                                        valChange($('#partial_res'),Object.keys(data.items).length);
                                        valChange($('#total_res'),data.total);
                                        dontLoad = false;
                                });
                        }
                }

                function showResults(n_items) {
                        $.each(n_items, function(index, item) {
                                $equip_location = (item.equip_locations > 0) ? `Equip Locations: ${data.equip_locations[item.equip_locations]} <br>`: '' ;
                                $slots = (item.slots>0) ? `[${item.slots}] ` : '';
                                $price_buy = (item.price_buy > 0) ? item.price_buy : data.defaults.price_buy;
                                $price_sell = (item.price_sell > 0) ? item.price_sell : data.defaults.price_sell;
                                $weight = (item.weight > 0) ? item.weight : data.defaults.weight;
                                $atk = (item.atk > 0) ? item.atk : data.defaults.atk;
                                $matk = (item.matk > 0) ? item.matk : data.defaults.matk;
                                $defense = (item.defence > 0) ? item.defence : data.defaults.defence;
                                $range = (item.range > 0) ? item.range : data.defaults.range;
                                $refineable = (item.refineable == 'yes') ? '<img src="<?php echo $this->themePath('css/icons/refineable.png') ?>" data-toggle="tooltip" title="Refineable"/>' : '';
                                $for_sale = (item.for_sale > 0) ? '<img src="<?php echo $this->themePath('css/icons/forsale.png') ?>" data-toggle="tooltip" title="For Sale"/>' : '';
                                $custom = (item.custom =='yes') ? '<img src="<?php echo $this->themePath('css/icons/custom.png') ?>" data-toggle="tooltip" title="Custom Item"/>' : '';
                                $img = (item.itemImage !=  0) ? item.itemImage : "<?php echo $this->themePath('img/noImage.png') ?>";
                                $icon = (item.iconImage != 0) ? item.iconImage : '';

                                $title = `
                                                Item ID: ${item.item_id} <br>
                                                Type: ${data.item_types[item.type]} <br>
                                                ${$equip_location}
                                                NPC Buy/Sell: ${$price_buy}/${$price_sell}<br>
                                                Weight: ${$weight}<br>
                                                ATK/MATK: ${$atk}/${$matk}<br>
                                                Defense: ${$defense}<br>
                                                Range: ${$range}<br>
                                        `;

                                $list_title = `${$title}`;
                                $thumbnail_title = `
                                Item Name: ${item.name} <br>
                                ${$title}
                                                Refineable:  ${(item.refineable =='yes') ? "Yes" : "No"} <br>
                                                For Sale: ${(item.for_sale > 0) ? "Yes" : "No"} <br>
                                                Custom:  ${(item.custom =='yes') ? "Yes" : "No"} <br>
                                `;
                                string_lit = `
                                        <div class="item_container">
                                                <div class="item list align-items-center" item-view="list" data-html="true" data-toggle="tooltip" data-placement="bottom" title="${$list_title}">
                                                        <div class="list_name"><div class="list_icon" style="background-image: url('${$icon}')"></div>${$slots}${item.name}</div>
                                                        <div class="list_cats">
                                                        <img src="<?php echo $this->themePath('css/icons') ?>/${item.type}.png" data-toggle="tooltip" title="Type: ${data.item_types[item.type]}"/>
                                                        ${$refineable}
                                                        ${$for_sale}
                                                        ${$custom}
                                                        </div>
                                                </div>
                                                <div class="item thumbnail align-items-center" item-view="thumbnail" data-html="true" data-toggle="tooltip" data-placement="bottom" title="${$thumbnail_title}">
                                                       <div class="thumbnail_image" style="background-image:url('${$img}')" ></div>
                                                </div>
                                        </div>
                                `;
                                loader.before(string_lit);
                        });
                        viewToggle();
                }

                function checkboxCountSelected(group_ = '') {
                        group = (group_ != '') ? `[sp_name=${group_}]` : '';
                        $('.search_param[sp_type=checkbox]' + group).each(function() {
                                let checkedBoxes = $(this).find('input[type=checkbox]:checked').length;
                                let totalChoices = $(this).attr('sp_total_choices');
                                $(this).find('.value').html(checkedBoxes + ' selected');
                                gz =  (group_ == '') ? $(this).attr('sp_name') : group_;
                                if (totalChoices == checkedBoxes || checkedBoxes==0) {
                                        data._params[gz] = "-1";
                                } else {
                                        let str = '';
                                        $(this).find('input[type=checkbox]:checked').each(function() {
                                                str += "," + $(this).attr('value');
                                        });
                                        data._params[gz] = str.substr(1, str.length);
                                }
                        });
                }

                function drawSlider(reset=false) {
                        $(".slider-range").each(function() {
                                $(".value[sp_type=" + $(this).attr("sp_type") + "]").html($(this).attr('low') + " - " + $(this).attr('high'));
                                $(this).slider({
                                        range: true,
                                        min: Number($(this).attr('min')),
                                        max: Number($(this).attr('max')),
                                        values: (reset) ? [Number($(this).attr('min')),Number($(this).attr('max'))]: [Number($(this).attr('low')), Number($(this).attr('high'))],
                                        slide: function(event, ui) {
                                                $(".value[sp_type=" + $(this).attr("sp_type") + "]").html(ui.values[0] + " - " + ui.values[1]);
                                                $(this).attr("low", ui.values[0]);
                                                $(this).attr("high", ui.values[1]);
                                                if(Number($(this).attr('min'))==ui.values[0] && Number($(this).attr('max'))==ui.values[1]) {
                                                        data._params[$(this).attr("sp_type")] = -1;
                                                }
                                                else {
                                                        data._params[$(this).attr("sp_type")] = ui.values[0]+","+ui.values[1];
                                                }
                                                if (_delay) clearTimeout(_delay);
                                                _delay = setTimeout(dataUpdate, 700);
                                        }
                                });
                                $(".sp_type_" + $(this).attr("sp_type") + "_min_label").html($(this).attr('min'));
                                $(".sp_type_" + $(this).attr("sp_type") + "_max_label").html($(this).attr('max'));
                        });
                }

                $('[cb_toggler]').each(function() {
                        $(this).on('click', function() {
                                parent = $(this);
                                curr_state = parent.attr('current_status');
                                $(`input[type=checkbox][sp_name=${parent.attr('sp_name')}]`).each(function() {
                                        if(curr_state=='checked') $(this).prop('checked', false);
                                        else $(this).prop('checked',true);
                                });
                                if(curr_state=='checked') {
                                        parent.attr('current_status','unchecked');
                                        parent.html(parent.attr('data-text-unchecked'));
                                }
                                else {
                                        parent.attr('current_status','checked');
                                        parent.html(parent.attr('data-text-checked'));
                                }
                                checkboxCountSelected(parent.attr('sp_name'));
                                if (_delay) clearTimeout(_delay);
                                _delay = setTimeout(dataUpdate, 700);
                        });
                });

                $('.search_param_child input[type=checkbox]').on("change",function() {
                        checkboxCountSelected($(this).attr('sp_name'));
                        if (_delay) clearTimeout(_delay);
                        _delay = setTimeout(dataUpdate, 700);
                });

                $(".search_tb").on('keypress', function(e){ 
                        data._params[$(this).attr('name')] = $(this).val();
                        if (_delay)  clearTimeout(_delay);
                        _delay = setTimeout(dataUpdate, 400); 
                });

                init();

        });
</script>
<section class="botongui ">
        <div class="container-fluid nopadding max_height">
                <div class="row h-100 overflow-hidden">
                        <div class="col search">
                                <div class="text-center mx-auto">
                                        <input type="text" name="itemText" class="search_tb" placeholder="Search for Item ID or Name" />
                                        <span class="search_reset">Reset Filter</span>
                                </div>
                                <?php foreach ($json_arr['search_params'] as $key => $data) : ?>
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
                                        <div class="item_container" item-type="t_loader" data-toggle="tooltip" data-placement="top" title="Loading..."><div class="loader"></div></div>
                                </div>
                                </div>
                        </div>
                </div>
        </div>
</section>