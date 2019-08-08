function _valChange(selector, next, past='') {
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

$.fn.isInViewport = function(element) {
    if(!$(this).is(':visible')) return false;
    var elementTop = $(this).offset().top;
    var elementBottom = elementTop + $(this).outerHeight();
    var viewportTop = $(element).scrollTop() + 76;
    var viewportBottom = viewportTop + $(element).height();
    return elementBottom > viewportTop && elementTop < viewportBottom;
};

function updatePreferredServer(sel){
    var preferred = sel.options[sel.selectedIndex].value;
    document.preferred_server_form.preferred_server.value = preferred;
    document.preferred_server_form.submit();
}
function updatePreferredTheme(theme){
    $.post(window.location.href, {preferred_theme: theme}, function(res) {
        location.reload();
    })
}
function reload(){
    location.reload();
}

statusRefresh = 0;
woeOn = false;
countdownTemplate_off = "Happening in %d days, %h hours, %i minutes, %s seconds";
countdownTemplate_on = "Ending in %d days, %h hours, %i minutes, %s seconds";
woeTimes = {};
servers = {};
countOn  = 0;
function fetchWoETimes() {
    $.get(woeURL, function(res) {
        modal.find('[item-type=t_loader]').hide();
        countOn = 0;
        res = JSON.parse(res);
        woeTimes = res.woeTimes;
        let container = '.naviOriginal .emperium_content';
        $(container).html('');
        let $table=`<table class="table table-bordered woeModal" name="woeModal">`;
        $.each(woeTimes, function(serverName) {
            countOn+=res.serverTimes[serverName].activeCount;
        $table += `
                <thead>
                    <tr>
                        <th colspan="3">WoE Times for ${serverName} (Current Time: ${res.serverTimes[serverName].time})</th>
                    </tr>
                    <tr>
                        <th>Schedule</th>
                        <th>Castle</th>
                        <th>Timer</th>
                    </tr>
                </thead>
                <tbody>`
            $.each(woeTimes[serverName], function() {
                $table += `
                    <tr>
                        <td>${this.startingDay} ${this.startingHour} ~ ${this.endingDay} ${this.endingHour}</td>
                        <td>${this.castle}</td>
                        <td countdownName="cd_${this.start_timestamp}${this.end_timestamp}" data-trigger="countdownItem" serverName="${serverName}">
                <span countdownName="cd_${this.start_timestamp}${this.end_timestamp}" start_timestamp="${this.start_timestamp}" end_timestamp="${this.end_timestamp}" castle="${this.castle}" woeOn="${(this.woeOn)}" }"></span></td>
                    </tr>
                `;
            });
        $table += `
                </tbody>
        `;
        });

        $table +=`</table>`;
        $(container).html($table);
        if($(modal).is(':visible')) {
            $(modal).find('.modal-body').html('');
            $('.naviOriginal table[name=woeModal]').clone().appendTo($(modal).find('.modal-body'));
        }
        createCountdown();
        $('.emperium_status').attr('title',`${countOn>0 ? countOn : 'No'} active WoE schedule.<br>Click to view more information`);
        
        toggleRotator('.emperium_status', (countOn>0) ? 2 : 0, (countOn==0) ? {color: 'grey'} : {color: 'empColor'});
        if(countOn==0) {
            $('.icon_woe').addClass('icon_grayscale');
        }
        else {
            $('.icon_woe').removeClass('icon_grayscale');
        }

    });

}

function createCountdown() {
    $('[data-trigger=countdownItem]').each(function() {
        $parent = $(this);
        $parent.find('span[countdownName]').each(function() {
            $this = $(this);
            $woeOn = $this.attr('woeOn');
            startTime = new Date(Number($this.attr('start_timestamp')));
            endTime = new Date(Number($this.attr('end_timestamp')));
            dateToUse = ($woeOn=='1') ? endTime : startTime;
            $this.countdown({
                date: dateToUse,
                htmlTemplate: ($woeOn=='1') ? countdownTemplate_on : countdownTemplate_off,
                onComplete: function( event ) {
                    fetchWoETimes(woeURL);
                },
            });
        });
    });
}

function toggleRotator(item, action=0, ...args) {
    let vals = (args.length>0 && typeof args[0] == 'object') ? args[0] : {};
    let $rotator = $(`${item} .rotator`);
    let $botongui_rotator = $rotator.find("#botongui_rotator");
    $rotator.fadeIn();
    if(action==2) {
        $rotator.addClass("rotate linear");
    }
    else if(action==1) {
        $rotator.addClass("rotate");
    }
    else {
        $rotator.on('animationiteration webkitAnimationIteration', function () {
            $rotator.removeClass('rotate');
        });
    }
    if(vals.fadeOut) $rotator.fadeOut();
    if(vals.color) {
        $botongui_rotator.removeClass();
        $botongui_rotator.addClass(vals.color)
    }
}

function woeStatusModal(button) {
    doOnce = false;
    fetchWoETimes(woeURL);
    modal.find('.modal-title').html("WoE Times");
    $(modal).find('.modal-body').html('');
    $('table[name=woeModal]').clone().appendTo($(modal).find('.modal-body'));
    content.show();   
}

function fetchServerStatus() {
    let serverStatusDiv = '.server_status';
    let onlineCount = 9999999;
    toggleRotator(serverStatusDiv, 1);
    toggleRotator('.online_players', 1);
    $('.tooltip').remove();
    $.get(servURL, function(res) {
        $('.tooltip').remove();
        modal.find('[item-type=t_loader]').hide();
        res = JSON.parse(res);
        serverStatus = res.serverStatus;

        $(serverStatusDiv).find('#botongui_rotator').removeClass();
        switch (res.minimum) {
            case 0:
                toggleRotator(serverStatusDiv, 0, {color: 'grey'});
                $('.icon_serverstatus').addClass('icon_grayscale');
                break;
            case 1: 
            case 2: 
                $('.icon_serverstatus').addClass('icon_orangeFromGreen');
                toggleRotator(serverStatusDiv, 2, {color: 'orange'});
            default:
                $('.icon_serverstatus').removeClass('icon_grayscale icon_orangeFromGreen');
                toggleRotator(serverStatusDiv, 2, {color: 'green'});
        }

        let $table = `<table class="table table-bordered"  name="serverStatusModal">`;
        let onlinePlayersTitle = ``;
        let serverStatusTitle = ``;
        $.each(serverStatus, function(serverName) {
            serverStatusMessage = '';
            let child = this;
            onlineCount = Math.min(onlineCount,child.playersOnline);
            let loginServerUp = child.loginServerUp ? "Online" : "Offline";
            let charServerUp = child.charServerUp ? "Online" : "Offline";
            let mapServerUp = child.mapServerUp ? "Online" : "Offline";

            switch (child.loginServerUp + child.charServerUp + child.mapServerUp) {
                case 0:
                    serverStatusMessage = (!serverStatusMessage) ? "All servers down.": serverStatusMessage;
                    break;
                case 1:
                    serverStatusMessage = (!serverStatusMessage) ? "2 servers down": serverStatusMessage;
                case 2:
                    serverStatusMessage = (!serverStatusMessage) ? "1 server down": serverStatusMessage;
                default:
                    serverStatusMessage = (!serverStatusMessage) ? "All servers up.": serverStatusMessage;
            }
            $table += `
                <thead>
                    <tr>
                        <th colspan="6">Server Status for ${serverName}</th>
                    </tr>
                    <tr>
                        <th>Login Server</th>
                        <th>Character Server</th>
                        <th>Map Server</th>
                        <th>Online Players</th>
                        <th>Autotrade Merchants</th>
                        <th>Population</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${loginServerUp}</td>
                        <td>${charServerUp}</td>
                        <td>${mapServerUp}</td>
                        <td>${child.playersOnline}</td>
                        <td>${child.autotradeMerchants}</td>
                        <td>${child.population}</td>
                    </tr>
                </tbody>
            `;
            onlinePlayersTitle += `${child.playersOnline} online in ${serverName} <br>`;
            serverStatusTitle += `Server: ${serverName} <br>Current Time: ${child.serverTime}<br>${serverStatusMessage}<br>`;
        });
        $table += `</table>`;
        $(modal).find('.modal-body').html($table);
        modal.find('.modal-title').html("Server Status");
        onlinePlayersTitle += `Click here to view more info`;
        serverStatusTitle += `Click here to view more info`;
        $('.online_players').attr('title',onlinePlayersTitle);
        $('.online_players').attr('data-original-title',onlinePlayersTitle);
        $('.server_status').attr('title',serverStatusTitle);
        $('.server_status').attr('data-original-title',serverStatusTitle);
        if(onlineCount==0) {
            toggleRotator('.online_players', 0);
            $('.icon_onlineplayers').addClass('icon_grayscale');
        }
        else {
            toggleRotator('.online_players', 0, {color: 'green'});
            $('.icon_onlineplayers').removeClass('icon_grayscale');
        }
        content.show(); 
    });
}

$(document).ready(function() {

    modal = $('#modal_botongui');
    content = modal.find('.modal-main-content');
    let text = `
    ______ _     _    ___   _______ _____  
    |  ____| |   | |  | \\ \\ / / ____|  __ \\ 
    | |__  | |   | |  | |\\ V / |    | |__) |
    |  __| | |   | |  | | > <| |    |  ___/ 
    | |    | |___| |__| |/ . \\ |____| |     
    |_|    |______\\____//_/ \\_\\_____|_|     
                                            
    -----------------------------------------------
    Botong-ui Theme by Marq Roldan (Hyvraine)
    -----------------------------------------------
    `;
    console.log(text+$('.fluxDetails').html());

    modal_original = $('.modal-main-content').html();

    $('#modal_botongui').on('hidden.bs.modal', function (event) {
        $(this).find('.modal-main-content').html(modal_original);
    });

    $('#modal_botongui').on('show.bs.modal', function (event) {
        $('.tooltip').remove();
        $(this).find('[item-type=t_loader]').show();
        button = $(event.relatedTarget);
        content.hide();
        func = button.attr('data-function');
        if(typeof window[func] === "function") {
                window[func](button);
        }
        else { return; }
    });

    if($('.botonguiPage').is(':visible')) {
        $('.botonguiPage').overlayScrollbars({
            className       : "os-theme-dark",
            sizeAutoCapable : true,
            paddingAbsolute : false,
            scrollbars : {
                    clickScrolling : true,
                    autoHide: 'leave', 
                    autoHideDelay: 400, 
            },
        }); 
    }

    $('body').tooltip({
        selector: '[data-toggle=tooltip]',
        delay: {"show":200, "hide":0},
    });

    $('[data-toggle=tooltip]').on('shown.bs.tooltip', function (e) {
        $(this).removeAttr('title');
        $(this).attr('tooltip-replacealso',`#${$('.tooltip').attr('id')}`);
    })
    $('[data-toggle=tooltip]').on('show.bs.tooltip', function (e) {
        $(this).removeAttr('title');
    })
    $('[data-toggle=tooltip]').on('hidden.bs.tooltip', function (e) {
        $(this).removeAttr('tooltip-replacealso');
        $(this).attr('title',$(this).attr('data-original-title'));
    })
    $('[data-toggle="popover"]').popover()

    fetchWoETimes();
    fetchServerStatus();

    $('.user_picture_area').on('tap mouseenter click',function() {
        $('.fcp_user').toggle();
    });

    $(document).on('scroll click mouseover mousemove ', function(e) 
    {   
        let container = $('.fcp_user, .user_picture_area');
        if (!container.is(e.target) && container.has(e.target).length === 0) 
        {
            $('.fcp_user').hide();
        }
    });

    $('.smallMenuToggle').on('click', function() {
        $('#smallMenu').toggle();
    });

    $('.searchShow').on('click', function() {
        $('.search').toggle();
    });
	
	$('._showMenuBarToggler').on('click', function() {
		$('.naviToggle').fadeToggle();
	});
});

