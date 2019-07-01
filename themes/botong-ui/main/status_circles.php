<?php if (!defined('FLUX_ROOT')) exit; ?>
<script>
    woeOn = false;
    countdownTemplate_off = "Happening in %d days, %h hours, %i minutes, %s seconds";
    countdownTemplate_on = "Ending in %d days, %h hours, %i minutes, %s seconds";
    woeTimes = {};
     countOn  = 0;
    function fetchWoETimes() {
        $.get("<?php echo $this->url('woe','index',array('output'=>'json')) ?>", function(res) {
            countOn = 0;
            res = JSON.parse(res);
            woeTimes = res.woeTimes;
            let container = '.emperium_content';
            $(container).html('');
            $woeModal =`<table class="table table-bordered" name="woeModal">`;
            $.each(woeTimes, function(serverName) {
                countOn+=res.serverTimes[serverName].activeCount;
                toggleRotator('.emperium_status', countOn>0);
            $woeModal += `
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
                    $woeModal += `
                        <tr>
                            <td>${this.startingDay} ${this.startingHour} ~ ${this.endingDay} ${this.endingHour}</td>
                            <td>${this.castle}</td>
                            <td countdownName="cd_${this.start_timestamp}${this.end_timestamp}" data-trigger="countdownItem" serverName="${serverName}">
                    <span countdownName="cd_${this.start_timestamp}${this.end_timestamp}" start_timestamp="${this.start_timestamp}" end_timestamp="${this.end_timestamp}" castle="${this.castle}" woeOn="${(this.woeOn)}" }"></span></td>
                        </tr>
                    `;
                });
            $woeModal += `
                    </tbody>
            `;
            });
            $woeModal +=`</table>`;
            $(container).html($woeModal);

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

    function toggleRotator(item, action=false) {
        $rotator = $(`${item} .rotator`);
        if(action) {
            $rotator.addClass("rotate");
        }
        else {
            $rotator.on('animationiteration webkitAnimationIteration', function () {
                $rotator.removeClass('rotate');
                console.log('deleted');
            });
        }
    }0

    function woeStatusModal(buttom) {
        doOnce = false;
        fetchWoETimes();
        modal.find('.modal-title').html("WoE Times");
        modal.find('[item-type=t_loader]').hide();
        $('table[name=woeModal]').clone().appendTo($(modal).find('.modal-body'));
        content.show();   
    }

    $(document).ready(function() {
        fetchWoETimes();
    });

</script>
<ul class="status_circles">
    <li class="emperium_status" data-html="true" data-toggle="tooltip"><div class="rotator"><?php returnAbsoluteContents($this->themePath("css/icons/rotator.svg")); ?></div>
        <div class="status_content"data-toggle="modal" data-target="#modal_botongui" data-function="woeStatusModal">
        <div class="emperium_content" style="display: none"></div>
        </div>
    </li>
    <li>2</li>
    <li>3</li>
</ul>