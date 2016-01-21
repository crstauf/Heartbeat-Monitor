<?php
/*
Plugin Name: Heartbeat Monitor
Plugin URI: http://www.calebstauffer.com
Description: Visual notification of the WordPress Hearbeat
Version: 0.0.2
Author: Caleb Stauffer
Author URI: http://www.calebstauffer.com
*/

if (is_admin())
	new css_heartbeat_monitor;

class css_heartbeat_monitor {

	function __construct() {
		add_action('admin_footer-post.php',	array(__CLASS__,'heartbeat_send'),1);
		add_action('admin_footer-post.php',	array(__CLASS__,'heartbeat_tick'),99999999999999999999);
	}

	public static function heartbeat_send() {
		if (false === wp_script_is('heartbeat')) { echo '<script>console.log("\n\n-√`- HEARTBEAT MONITOR: NO HEARTBEAT DETECTED\n\n");</script>'; return; }
		echo '<script>jQuery("#post-preview").hide();console.log("\n\n-√`- HEARTBEAT MONITOR: IT\'S ALIVE!\n-√`- PULSE: " + (60 / wp.heartbeat.interval()) + "bpm\n\n");</script>';
		?>

		<style>
			#wpadminbar { transition: 0.2s background ease; }

				#wpadminbar.heartbeat-lub { background-color: #990000 }

				@-webkit-keyframes heartbeatirregular {
					0%   { background-color: #990000; }
					20%  { background-color: #222; }
					80%  { background-color: #222; }
					100% { background-color: #990000; }
				}
				@-moz-keyframes heartbeatirregular {
					0%   { background-color: #990000; }
					20%  { background-color: #222; }
					80%  { background-color: #222; }
					100% { background-color: #990000; }
				}
				@-ms-keyframes heartbeatirregular {
					0%   { background-color: #990000; }
					20%  { background-color: #222; }
					80%  { background-color: #222; }
					100% { background-color: #990000; }
				}
				#wpadminbar.heartbeat-irregular {
					-webkit-animation: heartbeatirregular 2s infinite;
					-moz-animation:    heartbeatirregular 2s infinite;
					-ms-animation:     heartbeatirregular 2s infinite;
				}

				@-webkit-keyframes heartbeatshock {
					0%   { background-color: #FFF; }
					2%   { background-color: #FFF; }
					3%   { background-color: #222; }
					100% { background-color: #222; }
				}
				@-moz-keyframes heartbeatshock {
					0%   { background-color: #FFF; }
					2%   { background-color: #FFF; }
					3%   { background-color: #222; }
					100% { background-color: #222; }
				}
				@-ms-keyframes heartbeatshock {
					0%   { background-color: #FFF; }
					2%   { background-color: #FFF; }
					3%   { background-color: #222; }
					100% { background-color: #222; }
				}
				#wpadminbar.heartbeat-shock {
					-webkit-animation: heartbeatshock 12s infinite;
					-moz-animation:    heartbeatshock 12s infinite;
					-ms-animation:     heartbeatshock 12s infinite;
				}

			#wp-toolbar > ul > li { background-color: rgba(34,34,34,0.8); }
		</style>

		<script>
			var heartbeat_count = 0,
				hbmonitor_count = 0;

			function HBMonitor(pre,suf) {
				hbmonitor_count++;
				pre = typeof pre !== 'undefined' ? pre : '';
				suf = typeof suf !== 'undefined' ? suf : '';

				if (1 === hbmonitor_count)
					console.log('');

				if ('object' === typeof suf) {
					console.log('-√`- ' + pre + ':');
					console.log(suf);
				} else
					console.log('-√`- ' + pre + ' ' + suf);

				console.log('');

			}

			(function($) {

				$(document).on('heartbeat-send',function(e,data) {
					$("#wpadminbar").removeClass('hearbeat-irregular');
					heartbeat_count++;
					hbmonitor_count = 0;
					data['heartbeat_monitor'] = heartbeat_count;
					console.log('');
					console.log("-√`- HB: " + heartbeat_count + ", PULSE: " + (60 / wp.heartbeat.interval()) + "bpm");
					console.log("-√`- LUB");
					$("#wpadminbar").addClass('heartbeat-lub');
				});

				$(document).on('heartbeat-connection-lost',function(e,data) {
					console.log("-√`- NO HEART BEAT!");
					$("#wpadminbar").addClass('heartbeat-shock');
					heartbeat_shocking = setInterval(function() {
						console.log("-√`- CLEAR! *SHOCK*");
					},12000);
				});

				$(document).on('heartbeat-connection-restored',function(e,data) {
					$("#wpadminbar").removeClass('heartbeat-shock');
					console.log("-√`- NORMAL HEARTBEAT");
				});

				$(document).on('heartbeat-error',function(jqXHR, textStatus, error) {
					$("#wpadminbar").addClass('heartbeat-irregular');
					console.log("-√`- IRREGULAR HEARTBEAT!");
					console.log(jqXHR);
					console.log(textStatus);
					console.log(error);
					console.log('');
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
					hbmonitor_count = 0;
					console.log("-√`- DUB\n");
					console.log('');
					$("#wpadminbar").removeClass('heartbeat-lub');
				});
			}(jQuery));
		</script>

		<?php
	}

}

?>
