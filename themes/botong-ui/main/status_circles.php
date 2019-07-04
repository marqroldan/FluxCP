<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
    statusRefresh = 0;
    woeOn = false;
    countdownTemplate_off = "Happening in %d days, %h hours, %i minutes, %s seconds";
    countdownTemplate_on = "Ending in %d days, %h hours, %i minutes, %s seconds";
    woeTimes = {};
    servers = {};
    countOn  = 0;
    function fetchWoETimes() {
        $.get("<?php echo $this->url('woe','index',array('output'=>'json')) ?>", function(res) {
            modal.find('[item-type=t_loader]').hide();
            countOn = 0;
            res = JSON.parse(res);
            woeTimes = res.woeTimes;
            let container = '.emperium_content';
            $(container).html('');
            let $table=`<table class="table table-bordered" name="woeModal">`;
            $.each(woeTimes, function(serverName) {
                countOn+=res.serverTimes[serverName].activeCount;
                toggleRotator('.emperium_status', (countOn>0) ? 2 : 0, (countOn==0) ? {color: 'grey'} : {color: 'empColor'});
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
                $('table[name=woeModal]').clone().appendTo($(modal).find('.modal-body'));
            }

            createCountdown();
            $('.emperium_status').attr('title',`${countOn>0 ? countOn : 'No'} active WoE schedule.<br>Click to view more information`);
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
                        fetchWoETimes();
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
        fetchWoETimes();
        modal.find('.modal-title').html("WoE Times");
        $(modal).find('.modal-body').html('');
        $('table[name=woeModal]').clone().appendTo($(modal).find('.modal-body'));
        content.show();   
    }

    function fetchServerStatus() {
        let serverStatusDiv = '.server_status';
        toggleRotator(serverStatusDiv, 1);
        console.log('hi');
        toggleRotator('.online_players', 1);
        $('.tooltip').remove();
        $.get('<?php echo $this->url('server','status',array('output'=>'json')) ?>', function(res) {
            $('.tooltip').remove();
            modal.find('[item-type=t_loader]').hide();
            res = JSON.parse(res);
            serverStatus = res.serverStatus;

            $(serverStatusDiv).find('#botongui_rotator').removeClass();
            switch (res.minimum) {
                case 0:
                    toggleRotator(serverStatusDiv, 0, {color: 'grey'});
                    break;
                case 1: 
                case 2: 
                    toggleRotator(serverStatusDiv, 2, {color: 'orange'});
                default:
                    toggleRotator(serverStatusDiv, 2, {color: 'green'});
            }

            let $table = `<table class="table table-bordered"  name="serverStatusModal">`;
            let onlinePlayersTitle = ``;
            let serverStatusTitle = ``;
            $.each(serverStatus, function(serverName) {
                serverStatusMessage = '';
                let child = this[serverName];

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
            toggleRotator('.online_players', 0, {fadeOut: true});
            content.show(); 
        });
    }

    $(document).ready(function() {
        fetchWoETimes();
        fetchServerStatus();
    });

</script>
<ul class="status_circles">
    <li class="emperium_status" data-html="true" data-toggle="tooltip"><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content"data-toggle="modal" data-target="#modal_botongui" data-function="woeStatusModal">
            <div class="emperium_content" style="display: none"></div>
        </div>
    </li>
    <li class="online_players" data-html="true" data-toggle="tooltip" title="Loading..."><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content" data-toggle="modal" data-target="#modal_botongui" data-function="fetchServerStatus"></div>
    </li>
    <li class="server_status" data-html="true" data-toggle="tooltip" title="Loading..."><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content" data-toggle="modal" data-target="#modal_botongui" data-function="fetchServerStatus"></div>
    </li>
</ul>