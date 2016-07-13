<?php
/*
Plugin Name: Heartbeat Monitor
Plugin URI: http://develop.calebstauffer.com
Description: Visual notification of the WordPress Hearbeat
Version: 0.0.3
Author: Caleb Stauffer
Author URI: http://develop.calebstauffer.com
*/

if (!defined('ABSPATH') || !function_exists('add_filter')) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if (!defined('DOING_AJAX') || !DOING_AJAX)
	add_action('init',array('css_heartbeat_monitor','action_init'));

class css_heartbeat_monitor {

	private static $alive = false;

	public static function action_init() {
		if (!current_user_can('administrator')) return;

		add_action((is_admin() ? 'admin' : 'wp') . '_enqueue_scripts',array(__CLASS__,'action_enqueue_scripts'),0);
		add_action('shutdown',array(__CLASS__,'action_shutdown'),0);

		global $wp_current_filter,$wp_scripts;
		$current_filter = $wp_current_filter[0];
		if (false === array_search('heartbeat-monitor-lub',$wp_scripts->registered['heartbeat']->deps))
			$wp_scripts->registered['heartbeat']->deps[] = 'heartbeat-monitor-lub';
	}

	public static function action_enqueue_scripts() {
		wp_add_inline_script('heartbeat',
			'console.group("-√`- HEARTBEAT MONITOR: IT\'S ALIVE!");
			console.log("PULSE: " + (60 / window.wp.heartbeat.interval()) + "bpm");
			console.groupEnd();'
		);

		wp_register_style('heartbeat-monitor',plugin_dir_url(__FILE__) . 'styles.css',array(),'0.0.3');
		wp_register_script('heartbeat-monitor-lub',plugin_dir_url(__FILE__) . 'lub.js',array('jquery'),'0.0.3');
		wp_register_script('heartbeat-monitor-dub',plugin_dir_url(__FILE__) . 'dub.js',array('jquery','heartbeat','heartbeat-monitor-lub'),'0.0.3');
		wp_register_script('heartbeat-monitor-nobeat',plugin_dir_url(__FILE__) . 'nobeat.js',array('jquery'),'0.0.3');

		if (false === wp_script_is('heartbeat')) return;

		wp_enqueue_style('heartbeat-monitor');
		self::$alive = true;
	}

	public static function action_shutdown() {
		if (false === wp_script_is('heartbeat','done')) {
			self::no_heartbeat_detected();
			return;
		} else if (false === self::$alive) {
			global $wp_scripts;
			$wp_scripts->print_scripts('heartbeat-monitor');
			self::$alive = true;
		}

		if (true === self::$alive)
			add_action('shutdown',array(__CLASS__,'dub'),999999999999999999);
	}

	public static function no_heartbeat_detected() {
		global $wp_scripts;
		$wp_scripts->print_scripts('heartbeat-monitor-nobeat');

		$scripts = '';
		foreach (array(
			'heartbeat-monitor-lub',
			'heartbeat-monitor-dub',
		) as $script)
			$scripts .= '<script id="' . $script . '-script" type="text/javascript" data-src="' . esc_attr($wp_scripts->registered[$script]->src) . ('' !== $wp_scripts->registered[$script]->ver ? '?ver=' . esc_attr($wp_scripts->registered[$script]->ver) : '') . '"></script>';

		echo '<script>console.log("-√`- HEARTBEAT MONITOR: NO HEARTBEAT DETECTED");</script>';
		echo $scripts;
	}

	public static function dub() {
		global $wp_scripts;
		$wp_scripts->print_scripts('heartbeat-monitor-dub');
	}

}

?>
