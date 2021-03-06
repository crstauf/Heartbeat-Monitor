var heartbeat_count = 0,
    hbmonitor_count = 0;

if ('function' === typeof hbmonitor_lazy_lub)
    heartbeat_count = 1;

function HBMonitor(pre,suf,extra,exxtra,exxxtra,console_time_label) {
    hbmonitor_count++;
    pre = typeof pre !== 'undefined' ? pre : '';
    suf = typeof suf !== 'undefined' ? suf : '';
    extra = typeof extra !== 'undefined' ? extra : '';
    exxtra = typeof exxtra !== 'undefined' ? exxtra : '';
    exxxtra = typeof exxxtra !== 'undefined' ? exxxtra : '';
    console_time_label = typeof console_time_label !== 'undefined' ? console_time_label : '';

    if ('object' === typeof suf) {
        console.groupCollapsed('-√`- ' + pre + ':');
        console.log(suf);
    } else {
        if ('' !== extra)
            console.groupCollapsed('-√`- ' + pre + ' ' + suf);
        else
            console.log('-√`- ' + pre + ' ' + suf);
    }

    if ('' !== extra) console.log(extra);
    if ('' !== exxtra) console.log(exxtra);
    if ('' !== exxxtra) console.log(exxxtra);

    if ('' !== console_time_label) console.timeEnd(console_time_label);

    console.groupEnd();

}

(function($) {

    $(document).on('heartbeat-send',function(e,data) {
        $("#wpadminbar").removeClass('hearbeat-irregular');
        heartbeat_count++;
        if ($("#qm-heartbeatmonitor").length) {
            $("#qm-heartbeatmonitor .beat-count").text(heartbeat_count);
            var d = new Date();
            $("#qm-heartbeatmonitor .beat-timestamps").prepend('<li>' + d.getHours() + ':' + (10 > d.getMinutes() ? '0' : '') + d.getMinutes() + ':' + (10 > d.getSeconds() ? '0' : '') + d.getSeconds() + ' </li>');
            if ($("#qm-heartbeatmonitor .beat-timestamps > li").length > 5)
                for (var i = 5; i < $("#qm-heartbeatmonitor .beat-timestamps > li").length; i++)
                    $("#qm-heartbeatmonitor .beat-timestamps > li").eq(i).remove();
        }
        hbmonitor_count = 0;
        data['heartbeat_monitor'] = heartbeat_count;
        console.time('LUB -> DUB');
        console.groupCollapsed("-√`- LUB");
        console.log("HEARTBEAT: " + heartbeat_count);
        console.log("PULSE: " + (60 / wp.heartbeat.interval()) + "bpm");
        $("#wpadminbar").addClass('heartbeat-lub');
    });

    $(document).on('heartbeat-connection-lost',function(e,data) {
        console.warn("-√`- NO HEARTBEAT!");
        $("#wpadminbar").addClass('heartbeat-shock');
        heartbeat_shocking = setInterval(function() {
            console.warn("-√`- CLEAR!");
        },12000);
    });

    $(document).on('heartbeat-connection-restored',function(e,data) {
        $("#wpadminbar").removeClass('heartbeat-shock');
        console.log("-√`- NORMAL HEARTBEAT");
    });

    $(document).on('heartbeat-error',function(jqXHR, textStatus, error) {
        $("#wpadminbar").addClass('heartbeat-irregular');
        console.group("-√`- IRREGULAR HEARTBEAT!");
        console.log(jqXHR);
        console.log(textStatus);
        console.log(error);
        console.groupEnd();
    });

}(jQuery));
