<?php
/*
Plugin Name: Heartbeat Monitor
Plugin URI: http://www.calebstauffer.com
Description: Visual notification of the WordPress Hearbeat
Version: 0.0.1
Author: Caleb Stauffer
Author URI: http://www.calebstauffer.com
*/

if (!is_admin()) return;
new css_heartbeat_monitor;

class css_heartbeat_monitor {

	function __construct() {
		add_action('admin_footer-post.php',	array(__CLASS__,'heartbeat_send'),1);
		add_action('admin_footer-post.php',	array(__CLASS__,'heartbeat_tick'),99999999999999999999);
	}

	public static function heartbeat_send() {
		//global $wp_filter; echo '<pre>' . print_r($wp_filter['heartbeat_received'],true) . '</pre>';
		$console = '\n\n*** HEARTBEAT MONITOR: ';
		if (false === wp_script_is('heartbeat')) { echo '<script>console.log("' . $console . 'NO HEARTBEAT DETECTED ***\n\n");</script>'; return; }
		echo '<script>console.log("' . $console . 'IT\'S ALIVE! ***\n\n");</script>';
		?>

		<script>
			var heartbeat_count = 0,
				heartbeat_shocking = false;

			function HBMonitor_time(pre,suf) {
				pre = typeof pre !== 'undefined' ? pre : '';
				suf = typeof suf !== 'undefined' ? suf : '';

				var date 	= new Date();
				var hours	= date.getHours();
				var mins 	= date.getMinutes();
				var secs	= date.getSeconds();
				var mils 	= date.getMilliseconds();

				if (10 > hours) hours	= '0' + hours;
				if (10 > mins)	mins	= '0' + mins;
				if (10 > secs)	secs	= '0' + secs;
				if (10 > mils) 	mils 	= '00' + mils;
				else if (100 > mils)
								mils	= '0' + mils;

				console.log(pre + hours + ':' + mins + ':' + secs + '.' + mils + suf);
			}

			(function($) {

				$(document).on('heartbeat-send',function(e,data) {
					heartbeat_count++;

					console.log("HB: " + heartbeat_count);
					console.log("PULSE: " + wp.heartbeat.interval() + "s");
					HBMonitor_time("LUB\t","\n---√---");
					$("#wpadminbar").animate({backgroundColor: "#990000"},200);
				});

				$(document).on('heartbeat-connection-lost',function(e,data) {
					console.log('*** V-FIB! ***');
					heartbeat_shocking = setInterval(function() {
						console.log('*** CLEAR! ***');
					},12000);
				});

				$(document).on('heartbeat-connection-restored',function(e,data) {
					console.log('*** NORMAL SINUS RHYTHYM ***');
					clearInterval(heartbeat_shocking);
				});

			}(jQuery));
		</script>

		<?php
	}

	public static function heartbeat_tick() {
		?>

		<script>
			(function($) {
				$(document).on('heartbeat-tick',function(e,data) {
					HBMonitor_time("---√---\nDUB\t","\n");
					$("#wpadminbar").animate({backgroundColor: "#222"},200);
				});
			}(jQuery));
		</script>

		<?php
	}

}

?>