    _delay = 0;
    dontLoad = false;
    listview = true;

    function itemModal(button) {
            item_id = button.attr('item-id');
           if(item_id in item_desc) {
                   ItemViewRender(modal, content, item_desc[item_id]);
           }
           else {
                   $.get(`${itemViewURL}&id=${item_id}&output=json`,function(m) {
                           m = JSON.parse(m);
                           ItemViewRender(modal, content, m);
                   });
           }
    } 
    
    function ItemViewRender(modal, content, m) {
            modal.find('[item-type=t_loader]').hide();
            if('item' in m) {
                    r = m.item;
                    g = ('itemDrops' in m) ? m.itemDrops : null;
                    
                    item_desc[r.item_id] = m;
                    $icon = ('icon' in r) ? `<div class="item_icon icon" style="background-image: url('${r.icon}')"></div>` : ``;
                    if('image' in r) {
                            $image = `<div class="item_image_both"><div class="item_image" style="background-image: url('${r.image}')"></div>${$icon}</div>`;
                            modal.find('.title_container').before($image);
                    }
                    else {
                            modal.find('.modal-title').before($icon);
                    }
                    modal.find('.modal-title').html(`${r.name} <a href="${itemViewURL}&id=${r.item_id}"><span class="permalink">Permalink<span></a>`);
                    if('shop_item_id' in r) {
                            modal.find('.modal-title').after(`<div class="buy_now"><button type="button" class="btn buy_button">Buy</button>Cost: ${r.cost}</div>`);
                    }

                    $table = `<div id="info_${r.item_id}" class="tab-pane fade show active"><table class="table table-bordered">`;
                    t = ['item'];
                    if('scripts' in m) t.push('scripts');
                    for(key of t) {
                            label = m.labels[key];
                            for(keys in label) {
                                    if(!m[key][keys]) continue;
                                    $table += `<tr><th>${label[keys]}</th><td>${m[key][keys]}</td></tr>`;
                            }
                    }
                    $table += '</table></div>';
                    content.find('.modal-body').html((g) ? `<div class="tab-content">${$table}</div>`: $table);
                    if(g) {
                            content.find('.modal-body .tab-content').before(`
                            <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#info_${r.item_id}">Info</a></li>
                            <li class="nav-item"><a  class="nav-link" data-toggle="tab" href="#dropped_${r.item_id}">Dropped By</a></li>
                            </ul><br/>
                            `);
                            $table = `<div id="dropped_${r.item_id}" class="tab-pane fade"><table class="table table-bordered">`;
                            titles = m.labels.itemDrops;
                            $table += `<thead><tr>`;
                            for(key in titles) $table += `<th>${titles[key]}</th>`;
                            $table += `</tr></thead><tbody>`;
                            for(i in g) {
                                    $m_id = `${g[i].monster_id}`;
                                    if('monster_link' in g[i]) {
                                            $m_id = `<a href="${monsterIndexURL}>&monster_id=${g[i].monster_id}">${g[i].monster_id}</a>`;
                                    }
                                    $table += `<tr>
                                    <td>${$m_id}</td>
                                    <td>${g[i].monster_name}</td>
                                    <td>${g[i].drop_chance}</td>
                                    <td>${g[i].monster_level}</td>
                                    <td>${g[i].monster_race}</td>
                                    <td>${g[i].monster_element}</td>
                                    </tr>`;
                            }
                            $table += `</tbody></table></div>`;
                            content.find('.modal-body .tab-content').append($table);
                    }
                    $('.tooltip').remove();
                    content.show();
            }
            else {
                    modal.find('.no_result').show();
            }
    }

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

    function whilescrolling() {
            if (_delay) clearTimeout(_delay);
            $('.tooltip').remove();
    }

    function scrollChecky() {
            if(dontLoad) return;
            if(loader.isInViewport('.item_data_container'))  {
                    if(_delay) clearTimeout(_delay);
                    _delay = setTimeout(dataUpdate(true), 700);
            }
    }

    function init() {
            data._params['p'] = 1;
            data._params_default['p'] = 1;
            drawSlider();
            checkboxCountSelected();
            showResults(data.items);
            $.when(_valChange($('#partial_res'),Object.keys(data.items).length),_valChange($('#total_res'),data.total)).then(function() {
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
                                    onUpdated: scrollChecky,
                            }
                    }); 
            });
    }
    
    function dataUpdate(stat=false) { 
            $('.tooltip').remove();
            if(dontLoad) return;
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
                    dontLoad = true;
                    $tmp = $('.search_count').html();
                    $('.search_count').html('Searching...');
                    $.get(`${itemIndexURL}&output=json&data_output=items,total`, data._params, function(new_data) {    
                            $('.search_count').html($tmp);
                            new_data = JSON.parse(new_data);
                            data.total = new_data.total
                            d = new_data.items;
                            if(stat) {
                                    for (key in d) data.items[Object.keys(data.items).length+key] = d[key];
                                    showResults(d);
                            }
                            else {
                                    data.items = d;
                                    $('.item_master').remove()
                                    showResults(data.items);
                            }
                            _valChange($('#partial_res'),Object.keys(data.items).length);
                            _valChange($('#total_res'),data.total);
                            dontLoad = false;
                    });
            }
    }

    function showResults(n_items) {
            if(data.total==0 || Object.keys(data.items).length < data.perPage ) { loader.hide();}
            else { loader.show(); }
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
                    $refineable = (item.refineable == 'yes') ? `<img src="${themePath}css/icons/item_refineable.png" data-toggle="tooltip" title="Refineable"/>` : '';
                    $for_sale = (item.for_sale > 0) ? `<img src="${themePath}css/icons/item_forsale.png" data-toggle="tooltip" title="For Sale"/>` : '';
                    $custom = (item.custom =='yes') ? `<img src="${themePath}css/icons/item_custom.png" data-toggle="tooltip" title="Custom Item"/>` : '';
                    $img = (item.itemImage !=  0) ? item.itemImage : "${themePath}img/noImage.png";
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
                            <div item-id="${item.item_id}" data-toggle="modal" data-target="#modal_botongui" data-function="itemModal" class="item_master item_container">
                                    <div class="item list align-items-center" item-view="list" data-html="true" data-toggle="tooltip" data-placement="bottom" title="${$list_title}">
                                            <div class="list_name"><div class="list_icon" style="background-image: url('${$icon}')"></div>${$slots}${item.name}</div>
                                            <div class="list_cats">
                                            ${$refineable}
                                            ${$for_sale}
                                            ${$custom}
                                            <img src="${themePath}css/icons/item_${item.type}.png" data-toggle="tooltip" title="Type: ${data.item_types[item.type]}"/>
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

    $(document).ready(function() {
            loader = $(".item_container[item-type=t_loader]");
            loader.hide();

            $('.pagemenu div').first().before(`<div class="menu_container view_toggler"><div  data-toggle="tooltip" title="Toggle View"><i class="fas ${(listview) ? 'fa-bars' : 'fa-th'}"></i></div></div>`);
            $('.pagemenu div').first().before(`<div class="dropdown_container"><div class="menu_container" id="menu_sort" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="toggle"><div data-toggle="tooltip" title="Sort By"><i class="fas fa-sort"></i></div></div>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="menu_sort">
            ${pageMenu}
            </div></div>`);
            $('.pagemenu div').first().before(`<div class="search_count"><span id="partial_res">0</span>/<span id="total_res">0</span></div>`);

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

            $('.sort_order').on('click', function() {
                    $this = $(this);
                    $val = ($this.attr('sort-val')+1) % 3;
                    $this.attr('sort-val',$val);
                    switch ($val) {
                            case 1:
                                    $this.find('i').removeClass('fa-arrow-up');
                                    $this.find('i').addClass('fa-arrow-down');
                                    break;
                            case 2:
                                    $this.find('i').removeClass('fa-arrow-down fa-arrow-up');
                                    break;
                            default:
                                    $this.find('i').removeClass('fa-arrow-down');
                                    $this.find('i').addClass('fa-arrow-up');
                    }
                    data._params[$this.attr('sort-param')+"_order"] = data.sortable[$this.attr('sort-val')];
                    if (_delay) clearTimeout(_delay);
                    _delay = setTimeout(dataUpdate, 700);
            });

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
                    $('.search_tb').val('').change();
                    window.history.pushState("", push.title, push.page);
                    if (_delay) clearTimeout(_delay);
                    _delay = setTimeout(dataUpdate, 700);
            });

            $('[cb_toggler]').on('click', function() {
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

            $('.search_param_child input[type=checkbox]').on("change",function() {
                    checkboxCountSelected($(this).attr('sp_name'));
                    if (_delay) clearTimeout(_delay);
                    _delay = setTimeout(dataUpdate, 700);
            });

            $('body').addClass('overflow-hidden');

            $(".search_tb").on('input change', function(e){ 
                data._params[$(this).attr('name')] = $(this).val();
                if (_delay) clearTimeout(_delay);  
                _delay = setTimeout(dataUpdate, 500); 
                urlParams = new URLSearchParams(window.location.search);
                if($(this).val()=='' && urlParams.get('item_id')!='') {
                window.history.pushState("", push.title, push.page);
                }
            });
        
            init();
    });