_delay = 0;
dontLoad = false;
listview = true;
loader = $(".item_container[item-type=t_loader]");


    function monsterModal(button) {
            item_id = button.attr('item-id');
           if(item_id in monster_desc) {
                   ItemViewRender(modal, content, monster_desc[item_id]);
           }
           else {
                   $.get(`${monsterViewURL}&id=${item_id}&output=json`,function(m) {
                           m = JSON.parse(m);
                           ItemViewRender(modal, content, m);
                   });
           }
    }

    function ItemViewRender(modal, content, m) {
            modal.find('[item-type=t_loader]').hide();
            if('monster' in m) {
                    r = m.monster;
                    g = m.itemDrops;
                    b = m.mobSkills;
                    id = r.monster_id;
                    monster_desc[id] = m;
                    modal.find('.modal-title').html(`${r[default_name+'_name']} <span class="permalink"><a href="${monsterViewURL}&id=${id}">Permalink</a> | Card ID: <a href="${monsterIndexURL}&item_id=${g.dropcard.id}">${g.dropcard.id}</a></span>`);
                    $table = `<div id="info_${id}" class="tab-pane fade show active"><table class="table table-bordered">`;
                    label = m.labels['monster'];
                    for(keys in label) {
                            if(!label[keys]) continue;
                            if(keys=='monster_mode') {
                                    $table += `<tr><th>${label[keys]}</th><td>`;
                                    for (value of r[keys]) {
                                            $table += `<li>${value}</li>`;
                                    }
                                    $table+= `</td></tr>`;
                            }
                            else if(keys=='monster_stats'){
                                    $table += `<tr><th>${label[keys]}</th>
                                    <td>
                                            <table class="table table-borderless my-0">
                                                    <tr>
                                                            <th>STR</th>
                                                            <td>${r[keys]['str']}</td>
                                                            <th>AGI</th>
                                                            <td>${r[keys]['agi']}</td>
                                                            <th>VIT</th>
                                                            <td>${r[keys]['vit']}</td>
                                                    </tr>
                                                    <tr>
                                                            <th>INT</th>
                                                            <td>${r[keys]['int']}</td>
                                                            <th>DEX</th>
                                                            <td>${r[keys]['dex']}</td>
                                                            <th>LUK</th>
                                                            <td>${r[keys]['luk']}</td>
                                                    </tr>
                                            </table>                                                        
                                    </td></tr>`;
                            }
                            else {
                                    $table += `<tr><th>${label[keys]}</th><td>${r[keys]}</td></tr>`;
                            }
                    }
                    $table += '</table></div>';
                    content.find('.modal-body').html((g) ? `<div class="tab-content">${$table}</div>`: $table);
                    if('itemDrops' in m || 'mobSkills' in m) {
                            content.find('.modal-body .tab-content').before(`
                                    <ul class="nav nav-tabs">
                                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#info_${id}">Info</a></li>
                                    </ul><br/>
                                    `);
                    }
                    if(g) {
                            $('.nav-tabs').append(`<li class="nav-item"><a  class="nav-link" data-toggle="tab" href="#dropped_${id}">Item Drops</a></li>`);
                            $table = `<div id="dropped_${id}" class="tab-pane fade"><table class="table table-bordered">`;
                            titles = m.labels.itemDrops;
                            $table += `<thead><tr>`;
                            for(key in titles) $table += `<th>${titles[key]}</th>`;
                            $table += `</tr></thead><tbody>`;
                            for(i in g) {
                                    $m_id = `${g[i].monster_id}`;
                                    if('monster_link' in g[i]) {
                                            $m_id = `<a href="${monsterViewURL}&id=${g[i].monster_id}">${g[i].monster_id}</a>`;
                                    }
                                    $table += `<tr>
                                    <td><a href="${monsterIndexURL}&item_id=${g[i].id}">${g[i].id}</a></td>
                                    <td>${g[i].name}</td>
                                    <td>${g[i].chance}</td>
                                    </tr>`;
                            }
                            $table += `</tbody></table></div>`;
                            content.find('.modal-body .tab-content').append($table);
                    }
                    if(b) {
                            $('.nav-tabs').append(`<li class="nav-item"><a  class="nav-link" data-toggle="tab" href="#skills_${id}">Monster Skills</a></li>`);
                            $table = `<div id="skills_${id}" class="tab-pane fade">`;
                            titles = m.labels.mobSkills;
                            $table += `<table class="table table-bordered my-0">`;
                            for(i in b) {
                                    $m_id = `${b[i].monster_id}`;
                                    if('monster_link' in b[i]) {
                                            $m_id = `<a href="${monsterViewURL}&id=${b[i].monster_id}">${b[i].monster_id}</a>`;
                                    }
                                    $table += `
                                                    <tr>
                                                            <th>${titles.info}</th>
                                                            <td>${b[i].info}</td>
                                                            <th>${titles.skill_lvl}</th>
                                                            <td>${b[i].skill_lvl}</td>
                                                            <th>${titles.state}</th>
                                                            <td>${b[i].state}</td>
                                                    </tr>
                                                    <tr>
                                                            <th>${titles.casttime}</th>
                                                            <td>${b[i].casttime}</td>
                                                            <th>${titles.rate}</th>
                                                            <td>${b[i].rate}</td>
                                                            <th>${titles.delay}</th>
                                                            <td>${b[i].delay}</td>
                                                    </tr>
                                                    <tr>
                                                            <th>${titles.cancellable}</th>
                                                            <td>${b[i].cancellable}</td>
                                                            <th>${titles.target}</th>
                                                            <td>${b[i].target}</td>
                                                            <th>${titles.condition}</th>
                                                            <td>${b[i].condition}</td>
                                                    </tr>
                                                    <tr><td colspan="8"><div style="height:10px;"></div></td></tr>
                                            `;
                            }
                            $table += `</table></div>`;
                            content.find('.modal-body .tab-content').append($table);
                    }
                    $('.tooltip').remove();
                    content.show();
            }
            else {
                    modal.find('.no_result').show();
            }
    }

    $(document).ready(function() {
            loader.hide();
            default_name = 'iro';

            $('.pagemenu div').first().before(`<div class="dropdown_container"><div class="menu_container" id="menu_sort" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-reference="toggle"><div data-toggle="tooltip" title="Sort By"><i class="fas fa-sort"></i></div></div>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="menu_sort">${pageMenu}
            </div></div>`);
            $('.pagemenu div').first().before(`<div class="search_count"><span id="partial_res">0</span>/<span id="total_res">0</span></div>`);
            $('.pagemenu').removeClass('d-none');

            function whilescrolling() {
                    if(data.total==Object.keys(data.monsters).length) {
                        loader.hide();
                    }
                    if (_delay) clearTimeout(_delay);
                    $('.tooltip').remove();
            }

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
            
            function scrollChecky() {
                    if(dontLoad) return;
                    if(loader.isInViewport('.item_data_container'))  {
                            if(_delay) clearTimeout(_delay);
                            _delay = setTimeout(dataUpdate(true), 700);
                    }
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
                    $('.search_tb').val('').change();
                    window.history.pushState("", push.title, push.page);
                    if (_delay) clearTimeout(_delay);
                    _delay = setTimeout(dataUpdate, 700);
            });

            function init() {
                    data._params['p'] = 1;
                    data._params_default['p'] = 1;
                    drawSlider();
                    checkboxCountSelected();
                    showResults(data.monsters,data.labels);
                    $.when(_valChange($('#partial_res'),Object.keys(data.monsters).length),_valChange($('#total_res'),data.total)).then(function() {
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
                            $.get(`${monsterIndexURL}&output=json&data_output=monsters,total,labels`, data._params, function(new_data) {    
                                    $('.search_count').html($tmp);
                                    new_data = JSON.parse(new_data);
                                    data.total = new_data.total
                                    d = new_data.monsters;
                                    if(stat) {
                                            for (key in d) data.monsters[Object.keys(data.monsters).length+key] = d[key];
                                            showResults(d,new_data.labels);
                                    }
                                    else {
                                            data.monsters = d;
                                            $('.item_master').remove()
                                            showResults(data.monsters,new_data.labels);
                                    }
                                    _valChange($('#partial_res'),Object.keys(data.monsters).length);
                                    _valChange($('#total_res'),data.total);
                                    dontLoad = false;
                            });
                    }
            }

            function showResults(n_items,labels=null) {
                    if(data.total==0 || Object.keys(data.monsters).length < data.perPage ) { loader.hide();}
                    else { loader.show(); }
                    $in_title_name = (default_name=='kro') ? 'iro' : 'kro';
                    $.each(n_items, function(index, item) {
                            $size = (item.size) ? item.size : 0;
                            $element_type = item.element_type ? item.element_type : 0;
                            $dropcard_id = item.dropcard_id ? `Card ID: ${item.dropcard_id} <br>` : ``;
                            $title = `
                                ${(labels) ? `${labels[$in_title_name+'_name']} : ${item[$in_title_name+'_name']} <br>` : ``}
                                Monster ID: ${item.monster_id} <br>
                                Level: ${item.level}<br>
                                HP: ${item.hp}<br>
                                Race: ${data.races[item.race]}<br>
                                ${ item.exp >= 0 ? `Base EXP: ${item.exp}<br>` : ''}
                                ${ item.jexp >= 0 ? `Job EXP: ${item.jexp}<br>` : ''}
                                ${(item.mvp_exp >= 0) ? "MVP EXP: "+item.mvp_exp+"<br>" : ''}
                                ${$dropcard_id}
                                Custom:  ${(item.custom =='yes') ? "Yes" : "No"} <br>
                                    `;
                            string_lit = `
                                    <div item-id="${item.monster_id}" data-toggle="modal" data-target="#modal_botongui" data-function="monsterModal" class="item_container item_master">
                                            <div class="item list align-items-center d-flex" item-view="list" data-html="true" data-toggle="tooltip" data-placement="bottom" title="${$title}">
                                                    <div class="list_name">${item[default_name+'_name']}</div>
                                                    <div class="list_cats">
                                                    <img src="${themePath}css/icons/monster_size_${$size}.png" data-toggle="tooltip" title="Size: ${data.sizes[$size]}"/>
                                                    <img src="${themePath}css/icons/monster_${$element_type}.png" data-toggle="tooltip" title="Element: Level ${item.element_level} ${data.elements[$element_type]}"/>
                                                    </div>
                                            </div>
                                    </div>
                            `;
                            loader.before(string_lit);
                    });
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