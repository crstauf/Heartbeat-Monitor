<?php
/*
Plugin Name: Heartbeat Monitor
Plugin URI: http://develop.calebstauffer.com
Description: Visual notification of the WordPress Hearbeat
Version: 0.0.3
Author: Caleb Stauffer
Author URI: http://develop.calebstauffer.com
*/

if (is_admin()) {
	add_action('admin_enqueue_scripts',array('css_heartbeat_monitor','hooks'),0);
	add_action('admin_footer',array('css_heartbeat_monitor','hooks'),0);
} else {
	add_action('wp_enqueue_scripts',array('css_heartbeat_monitor','hooks'),0);
	add_action('wp_footer',array('css_heartbeat_monitor','hooks'),0);
}

class css_heartbeat_monitor {

	private static $alive = false;

	public static function hooks() {
		if (!current_user_can('administrator')) return;

		global $wp_current_filter,$wp_scripts;
		$current_filter = $wp_current_filter[0];
		if (false === array_search('heartbeat-monitor-lub',$wp_scripts->registered['heartbeat']->deps))
			$wp_scripts->registered['heartbeat']->deps[] = 'heartbeat-monitor-lub';

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
		wp_register_style('heartbeat-monitor',plugin_dir_url(__FILE__) . 'styles.css',array(),'0.0.3');
		wp_register_script('heartbeat-monitor-lub',plugin_dir_url(__FILE__) . 'lub.js',array('jquery'),'0.0.3');
		wp_register_script('heartbeat-monitor-dub',plugin_dir_url(__FILE__) . 'dub.js',array('jquery','heartbeat','heartbeat-monitor-lub'),'0.0.3');

		if (false === wp_script_is('heartbeat')) return;

		wp_enqueue_style('heartbeat-monitor');
		self::$alive = true;
	}

	public static function action_footer() {
		if (false === wp_script_is('heartbeat')) {
			self::no_heartbeat_detected();
			return;
		} else if (false === self::$alive) {
			wp_enqueue_style('heartbeat-monitor');
			self::$alive = true;
		}

		add_action(
			'shutdown',
			array(__CLASS__,'dub'),
			999999999999999999
		);
	}

	public static function no_heartbeat_detected() {
		echo '<script>console.log("-√`- HEARTBEAT MONITOR: NO HEARTBEAT DETECTED");</script>';
	}

	public static function dub() {
		global $wp_scripts;
		$wp_scripts->print_scripts('heartbeat-monitor-dub');
	}

}

?>
