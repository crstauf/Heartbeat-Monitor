<?php

if (!defined('ABSPATH') || !function_exists('add_filter')) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

class CSSLLC_HeartbeatMonitor_Collector extends QM_Collector {

    public $id = 'heartbeatmonitor';

    public function name() {
        return __( 'Heartbeat Monitor', 'query-monitor' );
    }

    public function __construct() {

        global $wpdb;

        parent::__construct();

    }

    public function process() {

		global $wp_actions, $wp_filter;

		$hooks = $all_parts = $components = array();

        $name = 'heartbeat_received';

		$hooks[$name] = $this->process_action( $name, $wp_filter );

		$all_parts    = array_merge( $all_parts, $hooks[$name]['parts'] );
		$components   = array_merge( $components, $hooks[$name]['components'] );

		$this->data['hooks'] = $hooks;
		$this->data['parts'] = array_unique( array_filter( $all_parts ) );
		$this->data['components'] = array_unique( array_filter( $components ) );

        print_r($this->data);

	}

	protected function process_action( $name, array $wp_filter ) {

		$actions = $components = array();

		if ( isset( $wp_filter[$name] ) ) {

			# http://core.trac.wordpress.org/ticket/17817
			$action = $wp_filter[$name];

			foreach ( $action as $priority => $callbacks ) {

				foreach ( $callbacks as $callback ) {

					$callback = QM_Util::populate_callback( $callback );

					if ( isset( $callback['component'] ) ) {
						if ( $this->hide_qm and ( 'query-monitor' === $callback['component']->context ) ) {
							continue;
						}

						$components[$callback['component']->name] = $callback['component']->name;
					}

					$actions[] = array(
						'priority'  => $priority,
						'callback'  => $callback,
					);

				}

			}

		}

		$parts = array_filter( preg_split( '#[_/-]#', $name ) );

		return array(
			'name'       => $name,
			'actions'    => $actions,
			'parts'      => $parts,
			'components' => $components,
		);

	}

    // public function process() {
    //
    //     $this->data['heartbeatmonitor'] = true;
    //
    // }

}

function register_cssllc_heartbeatmonitor_collector( array $collectors, QueryMonitor $qm ) {
	$collectors['heartbeatmonitor'] = new CSSLLC_HeartbeatMonitor_Collector;
	return $collectors;
}

add_filter( 'qm/collectors', 'register_cssllc_heartbeatmonitor_collector', 10, 2 );

?>
