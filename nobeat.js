var hbmonitor_lazy_lub = function(e,data) {
    jQuery("script#heartbeat-monitor-lub-script").attr('src',jQuery("script#heartbeat-monitor-lub-script").attr('data-src')).removeAttr('data-src');
    jQuery("script#heartbeat-monitor-dub-script").attr('src',jQuery("script#heartbeat-monitor-dub-script").attr('data-src')).removeAttr('data-src');
    console.group("-√`- GOT A HEARTBEAT!");
    console.log("PULSE: " + (60 / window.wp.heartbeat.interval()) + "bpm");
    console.groupEnd();
    console.time('LUB -> DUB');
    console.groupCollapsed("-√`- LUB");
    console.log("HEARTBEAT: 1");
    console.log("PULSE: " + (60 / wp.heartbeat.interval()) + "bpm");
    if ($("#qm-heartbeatmonitor").length) {
        $("#qm-heartbeatmonitor .beat-count").text(1);
        var d = new Date();
        $("#qm-heartbeatmonitor .beat-timestamps").prepend('<li>' + d.getHours() + ':' + (10 > d.getMinutes() ? '0' : '') + d.getMinutes() + ':' + (10 > d.getSeconds() ? '0' : '') + d.getSeconds() + ' </li>');
    }
    jQuery("#wpadminbar").addClass('heartbeat-lub');
    jQuery(document).unbind('heartbeat-send',hbmonitor_lazy_lub);
};

jQuery(document).bind('heartbeat-send',hbmonitor_lazy_lub);
