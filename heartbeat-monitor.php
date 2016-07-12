<?php
/*
Plugin Name: Heartbeat Monitor
Plugin URI: http://develop.calebstauffer.com
Description: Visual notification of the WordPress Hearbeat
Version: 0.0.3
Author: Caleb Stauffer
Author URI: http://develop.calebstauffer.com
*/

add_action('wp_enqueue_scripts',array('css_heartbeat_monitor','hooks'),1);
add_action('admin_enqueue_scripts',array('css_heartbeat_monitor','hooks'),1);
add_action('wp_footer',array('css_heartbeat_monitor','action_footer'),1);
add_action('admin_footer',array('css_heartbeat_monitor','action_footer'),1);

class css_heartbeat_monitor {

	private static $alive = false;

	public static function hooks() {
		if (!current_user_can('administrator')) return;
		global $wp_current_filter;
		$current_filter = $wp_current_filter[0];

		switch ($current_filter) {
			case 'wp_head':
			case 'wp_enqueue_scripts':
			case 'admin_enqueue_scripts':
				wp_add_inline_script('heartbeat',
					'console.group("-√`- HEARTBEAT MONITOR: IT\'S ALIVE!");
					console.log("PULSE: " + (60 / window.wp.heartbeat.interval()) + "bpm");
					console.groupEnd();'
				);
				self::action_enqueue_scripts();
				break;
			case 'wp_footer':
			case 'admin_footer':
				self::action_footer();
				break;
		}
	}

	public static function action_enqueue_scripts() {
		if (false === wp_script_is('heartbeat')) return;
		self::$alive = true;
		self::heartbeat_send();
	}

	public static function action_footer() {
		if (false === wp_script_is('heartbeat')) {
			self::heartbeat_send();
			return;
		} else if (false === self::$alive) {
			self::$alive = true;
			self::heartbeat_send();
		}

		add_action(
			(is_admin() ? 'admin' : 'wp') . '_footer',
			array(__CLASS__,'heartbeat_tick'),
			99999999999999999999
		);
	}

	public static function no_heartbeat_detected() {
		echo '<script>console.log("\n-√`- HEARTBEAT MONITOR: NO HEARTBEAT DETECTED\n\n");</script>';
	}

	public static function heartbeat_send() {
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
		</script>

		<?php
	}

	public static function heartbeat_tick() {
		?>

		<script>
			(function($) {
				$(document).on('heartbeat-tick',function(e,data) {
					hbmonitor_count = 0;
					console.groupCollapsed("-√`- DUB\n");
					console.timeEnd('LUB -> DUB');
					console.groupEnd();
					console.groupEnd();
					$("#wpadminbar").removeClass('heartbeat-lub');
				});
			}(jQuery));
		</script>

		<?php
	}

}

?>
