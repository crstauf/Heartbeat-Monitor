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
		add_action('admin_footer-post.php',	array(__CLASS__,'heartbeat_footer_js'),20);
	}

	public static function heartbeat_footer_js() {
		if (false === wp_script_is('heartbeat')) { echo '<script>console.log("\n\n *** NO HEARTBEAT *** \n\n");</script>'; return; }
		echo '<script>console.log("\n\n*** HEARTBEAT MONITOR CONNECTED ***\n\n");</script>';
		?>

		<script>
			var heartbeat_count = 0;
			(function($) {
				var time	= new Date(),
					hours	= 0,
					mins	= 0,
					secs	= 0,
					mils	= 0;

				$(document).on('heartbeat-send',function(e,data) {
					heartbeat_count++;

					time 	= new Date();
					hours	= time.getHours();
					mins 	= time.getMinutes();
					secs	= time.getSeconds();
					mils 	= time.getMilliseconds();

					if (10 > hours) hours	= '0' + hours;
					if (10 > mins)	mins	= '0' + mins;
					if (10 > secs)	secs	= '0' + secs;
					if (10 > mils) 	mils 	= '00' + mils;
					else if (100 > mils)
									mils	= '0' + mils;

					console.log("\nPULSE:\t" + wp.heartbeat.interval() + "s");
					console.log("HB " + heartbeat_count + ':' + "\t" + 'LUB ' + hours + ':' + mins + ':' + secs + '.' + mils);
					$("#wpadminbar").animate({backgroundColor: "#990000"},200);
				});
				$(document).on('heartbeat-tick',function(e,data) {
					time 	= new Date();
					hours	= time.getHours();
					mins 	= time.getMinutes();
					secs	= time.getSeconds();
					mils 	= time.getMilliseconds();

					if (10 > hours) hours	= '0' + hours;
					if (10 > mins)	mins	= '0' + mins;
					if (10 > secs)	secs	= '0' + secs;
					if (10 > mils) 	mils 	= '00' + mils;
					else if (100 > mils)
									mils	= '0' + mils;

					console.log("\t\t" + 'DUB ' + hours + ':' + mins + ':' + secs + '.' + mils);
					$("#wpadminbar").animate({backgroundColor: "#222"},200);
				});
			}(jQuery));
		</script>

		<?php
	}

}

?>