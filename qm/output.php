<?php

if (!defined('ABSPATH') || !function_exists('add_filter')) {
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

class CSSLLC_HeartbeatMonitor_Output_Html extends QM_Output_Html {

	public function __construct( QM_Collector $collector ) {
		parent::__construct( $collector );
        add_filter( 'qm/output/menus', array( $this, 'admin_menu' ), 80 );
	}

	public function output() {
        global $wp_filter;

		$data = $this->collector->get_data();

        echo '<div id="' . esc_attr( $this->collector->id() ) . '" class="qm qm-clear">' .

            '<table cellspacing="0">' .
                '<thead>' .
                    '<tr><th colspan="3">Heartbeat Monitor</th></tr>' .
                    '<tr>' .
                        '<th colspan="3">Action' .
                            $this->build_filter( 'component', $data['components'], 'subject' ) .
                        '</th>' .
                    '</tr>' .
                '</thead>' .
                '<tbody>';

                    foreach ( $data['hooks'] as $hook ) {

                        $hook_name = esc_html( $hook['name'] );

                        $row_attr = array();
                        $row_attr['data-qm-name']      = implode( ' ', $hook['parts'] );
                        $row_attr['data-qm-component'] = implode( ' ', $hook['components'] );

                        $attr = '';

                        if ( !empty( $hook['actions'] ) ) {
                            $rowspan = count( $hook['actions'] );
                        } else {
                            $rowspan = 1;
                        }

                        foreach ( $row_attr as $a => $v ) {
                            $attr .= ' ' . $a . '="' . esc_attr( $v ) . '"';
                        }

                        if ( !empty( $hook['actions'] ) ) {

                            $first = true;

                            foreach ( $hook['actions'] as $action ) {

                                if ( isset( $action['callback']['component'] ) ) {
                                    $component = $action['callback']['component']->name;
                                } else {
                                    $component = '';
                                }

                                printf( // WPCS: XSS ok.
                                    '<tr data-qm-subject="%s" %s>',
                                    esc_attr( $component ),
                                    $attr
                                );

                                echo '<td class="qm-num">' . intval( $action['priority'] ) . '</td>';
                                echo '<td class="qm-ltr qm-wrap">';

                                if ( isset( $action['callback']['file'] ) ) {
                                    echo self::output_filename( $action['callback']['name'], $action['callback']['file'], $action['callback']['line'] ); // WPCS: XSS ok.
                                } else {
                                    echo esc_html( $action['callback']['name'] );
                                }

                                if ( isset( $action['callback']['error'] ) ) {
                                    echo '<br><span class="qm-warn">';
                                    echo esc_html( sprintf(
                                        /* translators: %s: Error message text */
                                        __( 'Error: %s', 'query-monitor' ),
                                        $action['callback']['error']->get_error_message()
                                    ) );
                                    echo '<span>';
                                }

                                echo '</td>';
                                echo '<td class="qm-nowrap">';
                                echo esc_html( $component );
                                echo '</td>';
                                echo '</tr>';
                                $first = false;
                            }

                        } else {
                            echo "<tr{$attr}>"; // WPCS: XSS ok.
                            echo '<th>';
                            echo $hook_name; // WPCS: XSS ok.
                            echo '</th>';
                            echo '<td colspan="3">&nbsp;</td>';
                            echo '</tr>';
                        }

                    }

                echo '</tbody>' .
                '<tfoot>' .
                    '<tr>' .
                        '<td colspan="2">Beats: <span class="beat-count">0</span> | <ul class="beat-timestamps"></ul></td>' .
                        '<td class="qm-items-highlighted">Total actions: <span class="qm-items-number">' . $rowspan . '</span></td>' .
                    '</tr>' .
                '</tfoot>' .
            '</table>' .
        '</div>';

	}

}

function register_cssllc_heartbeatmonitor_output_html( array $output, QM_Collectors $collectors ) {
	if ( $collector = QM_Collectors::get( 'heartbeatmonitor' ) )
		$output['heartbeatmonitor'] = new CSSLLC_HeartbeatMonitor_Output_Html( $collector );
	return $output;
}

?>
