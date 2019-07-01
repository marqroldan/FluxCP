<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
    woeOn = false;
    countdownTemplate_off = "Happening in %d days, %h hours, %i minutes, %s seconds";
    countdownTemplate_on = "Ending in %d days, %h hours, %i minutes, %s seconds";
    days = {};
    woeTimes = {};
    function fetchWoETimes() {
        $.get("<?php echo $this->url('woe','index',array('output'=>'json')) ?>", function(res) {
            res = JSON.parse(res);
            days = res.days;
            woeTimes = res.woeTimes;
            let container = '.emperium_content';
            $(container).html('');
            $woeModal =`<table class="table table-bordered" name="woeModal">`;
            $.each(woeTimes, function(serverName) {
            $woeModal += `
                    <thead>
                        <tr>
                            <th colspan="3">WoE Times for ${serverName} (Current Time: ${res.serverTimes[serverName]})</th>
                        </tr>
                        <tr>
                            <th>Schedule</th>
                            <th>Castle</th>
                            <th>Timer</th>
                        </tr>
                    </thead>
                    <tbody>`
                $.each(woeTimes[serverName], function() {
                    startTime = (new Date(this.start_timestamp));
                    endTime = (new Date(this.end_timestamp));
                    $woeModal += `
                        <tr>
                            <td>${days[startTime.getDay()]} ${startTime.getHours()}:${startTime.getMinutes()} ~ ${days[endTime.getDay()]} ${endTime.getHours()}:${endTime.getMinutes()}</td>
                            <td>${this.castle}</td>
                            <td countdownName="cd_${this.start_timestamp}${this.end_timestamp}" data-trigger="countdownItem" serverName="${serverName}">
                    <span countdownName="cd_${this.start_timestamp}${this.end_timestamp}" start_timestamp="${this.start_timestamp}" end_timestamp="${this.end_timestamp}" castle="${this.castle}" woeOn="${(this.woeOn)}"></span></td>
                        </tr>
                    `;
                });
            $woeModal += `
                    </tbody>
            `;
            });
            $woeModal +=`</table>`;
            $(container).html($woeModal);
            createCountdown();
        });
    }

    function createCountdown() {
        let countOn = 0;
        $('[data-trigger=countdownItem]').each(function() {
            $parent = $(this);
            $parent.find('span[countdownName]').each(function() {
                $this = $(this);
                $woeOn = $this.attr('woeOn');
                startTime = new Date(Number($this.attr('start_timestamp')));
                endTime = new Date(Number($this.attr('end_timestamp')));

                $this.countdown({
                    date: ($woeOn=='1') ? endTime : startTime,
		            //offSet: <?php $time = explode(":", str_replace("+", "", $server->getServerTime('P'))); echo intval($time[0].".".($time[1]/60));?>,
                    htmlTemplate: ($woeOn=='1') ? countdownTemplate_on : countdownTemplate_off,
                    onComplete: function( event ) {
                        if($woeOn=='1') {
                            $this.attr('woeOn','0');
                            fetchWoETimes();
                        }
                        else {
                            $this.countdown({date:endTime, htmlTemplate: countdownTemplate_on,});
                            $this.attr('woeOn','1');
                            countOn++;
                        }
                    },
                });
            });
        });

        if(countOn>0) {
            $('.emperium_status .rotator').toggleClass("woe_on");
            console.log("activated");
        }
        else {
            console.log('inactive');
        }
    }

    function woeStatusModal(buttom) {
        doOnce = false;
        fetchWoETimes();
        modal.find('.modal-title').html("WoE Times");
        modal.find('[item-type=t_loader]').hide();
        $('table[name=woeModal]').clone().appendTo('.modal-body');
        content.show();   
    }

    $(document).ready(function() {
        fetchWoETimes();
    });

</script>
<ul class="status_circles">
    <li class="emperium_status" data-html="true" data-toggle="tooltip" title="WoE is currently not active.<br>Click to view more information."><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content"data-toggle="modal" data-target="#modal_botongui" data-function="woeStatusModal">
        <div class="emperium_content" style="display: none"></div>
        </div>
    </li>
    <li>2</li>
    <li>3</li>
</ul>