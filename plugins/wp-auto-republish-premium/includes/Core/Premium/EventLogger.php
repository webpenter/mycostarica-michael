<?php
/**
 * Helper functions.
 *
 * @since      1.2.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Helpers
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Ajax;
use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Logger class.
 */
class EventLogger
{
	use Ajax, Hooker, HelperFunctions;
	
	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/insert_log', 'insert_log', 10, 6 );
		$this->filter( 'wpar/display_republish_logs', 'generate_log' );
	    $this->ajax( 'process_filter_log', 'filter_log' );
		$this->ajax( 'process_remove_log_item', 'remove_log_item' );
		$this->ajax( 'process_delete_log_data', 'delete_log' );
	}

	/**
	 * Generate log infos.
	 *
	 * @param int    $post_id   Post ID to add in log.
	 * @param string $action    Log Action.
	 * @param bool   $status    Status success or failed.
	 * @param string $reason    Action reason.
	 * @param int    $timestamp Action timestmap.
	 * @param string $icon      Log Icon.
	 */
	public function insert_log( $post_id, $action, $status = true, $reason = '', $timestamp = false, $icon = 'dashicons-yes' )
	{
		$log_data = unserialize( get_option( 'wpar_republish_log_history' ) );
		if ( empty( $log_data ) ) $log_data = [];

		$datetime = $this->get_meta( $post_id, 'wpar_republish_meta_query' );
		$entry_id = substr( hash( 'sha256', mt_rand() . microtime() ), 0, 10 );
		$log_data[$entry_id] = [
			'log_id' => $entry_id,
			'post_id' => $post_id,
			'post_type' => get_post_type_object( get_post_type( $post_id ) )->labels->singular_name,
			'timestamp' => ( $timestamp ) ? $timestamp : strtotime( $datetime ),
			'action' => 'default|' . $action,
			'status' => $status,
			'reason' => $reason,
			'icon' => $icon,
		];

		if ( ! $this->is_enabled( 'disable_log', true ) ) {
		    update_option( 'wpar_republish_log_history', maybe_serialize( $log_data ) );
		}
	}

	/**
	 * Generate logs.
	 */
	public function generate_log()
	{
		$log_data = get_option( 'wpar_republish_log_history' );
        if ( $log_data === false ) {
            return false;
		}

		$log_data = unserialize( $log_data );
		if ( empty( $log_data ) ) {
            return false;
		}
		
		$log_data = array_slice( array_reverse( $log_data ), 0, apply_filters( 'wpar/log_data_count', 25 ), true );
		
		$logs = '<table class="form-table">
			<thead>
				<tr valign="top">
				    <td><strong><span class="dashicons dashicons-flag"></span></strong></td>
				    <td scope="row"><strong>' . __( 'Timestamp', 'wp-auto-republish' ) . '</strong></td>
				    <td scope="row"><strong>' . __( 'Type', 'wp-auto-republish' ) . '</strong></td>
				    <td scope="row"><strong>' . __( 'ID', 'wp-auto-republish' ) . '</strong></td>
				    <td scope="row"><strong>' . __( 'Post Title', 'wp-auto-republish' ) . '</strong></td>
				    <td scope="row"><strong>' . __( 'Action Log', 'wp-auto-republish' ) . '</strong></td>
					<td scope="row"><strong>' . __( 'Status', 'wp-auto-republish' ) . '</strong></td>
					<td scope="row"><strong><span class="dashicons dashicons-warning"></span></strong></td>
				</tr>
			</thead>
			<tbody>';

		foreach( $log_data as $items => $item ) {
			$actions = explode( '|', $item['action'] );
			$logs .= $this->generate_log_table( $actions[1], $item );
	    }

		$logs .= '</tbody>
		</table>';
		
		return $logs;
	}

	/**
	 * Filter logs using inputs.
	 */
	public function filter_log()
	{
		// security check
		$this->verify_nonce();

		$action = 'default';
		$count = 25;
		if ( isset( $_POST['count'] ) ) {
			$count = absint( sanitize_text_field( $_POST['count'] ) );
		}

		if ( isset( $_POST['filter'] ) ) {
			$action = sanitize_text_field( $_POST['filter'] );
		}

		$log_data = get_option( 'wpar_republish_log_history' );
		$log_data = array_reverse( unserialize( $log_data ) );
		
	    $logs = '<table class="form-table">
	    	<thead>
				<tr valign="top">
				    <td><strong><span class="dashicons dashicons-flag"></span></strong></td>
	    		    <td scope="row"><strong>' . __( 'Timestamp', 'wp-auto-republish' ) . '</strong></td>
	    			<td scope="row"><strong>' . __( 'Type', 'wp-auto-republish' ) . '</strong></td>
	    			<td scope="row"><strong>' . __( 'ID', 'wp-auto-republish' ) . '</strong></td>
	    			<td scope="row"><strong>' . __( 'Post Title', 'wp-auto-republish' ) . '</strong></td>
	    			<td scope="row"><strong>' . __( 'Action Log', 'wp-auto-republish' ) . '</strong></td>
					<td scope="row"><strong>' . __( 'Status', 'wp-auto-republish' ) . '</strong></td>
					<td><strong><span class="dashicons dashicons-warning"></span></strong></td>
	    		</tr>
	    	</thead>
			<tbody>'; 
			
		$i = 0;
		$table = '';
	    foreach( $log_data as $items => $item ) {
	    	$actions = explode( '|', $item['action'] );
            if ( in_array( $action, $actions ) ) {
				$table .= $this->generate_log_table( $actions[1], $item );
				
				$i++;
	    	    if ( $i == $count ) {
	    		    break;
	    	    }
			}
	    }        
	
		if ( ! empty( $table ) ) {
			$logs .= $table . '</tbody></table>';
		} else {
			$logs = '<div style="padding: 10px 14px;background-color: #eee;border: 1px solid #cccccc;">' . __( 'No logs found against this query.', 'wp-auto-republish' ) . '</div>';
		}
		
		$this->success( [
			'output' => $logs
		] );
	}

	/**
	 * Filter logs using inputs.
	 * 
	 * @param string $action Log Action.
	 * @param array  $item   Log Details.
	 */
	private function generate_log_table( $action, $item )
	{
		switch ( $action ) {
			case 'republish':
				$text = __( 'Post Republished', 'wp-auto-republish' );
				break;
			case 'clone':
				$text = __( 'Post Duplicated', 'wp-auto-republish' );
				break;
			case 'trigger':
				$text = __( 'Republish Event Triggered', 'wp-auto-republish' );
				break;
			case 'cache':
				$text = __( 'Single Cache Cleared', 'wp-auto-republish' );
				break;
			case 'email':
				$text = __( 'Email Notification Sent', 'wp-auto-republish' );
				break;
			case 'facebook_page':
				$text = __( 'Facebook Page Share Initiated', 'wp-auto-republish' );
				break;
			case 'facebook_group':
				$text = __( 'Facebook Group Share Initiated', 'wp-auto-republish' );
				break;
			case 'twitter':
				$text = __( 'Twitter Profile Share Initiated', 'wp-auto-republish' );
				break;
			case 'linkedin':
				$text = __( 'Linkedin Profile Share Initiated', 'wp-auto-republish' );
				break;
			case 'cron':
				$text = __( 'Global Republish Scheduled', 'wp-auto-republish' );
				break;
			case 'single_cron':
				$text = __( 'Single Republish Scheduled', 'wp-auto-republish' );
				break;
			case 'scheduled':
				$text = __( 'Rewrite Republish Processed', 'wp-auto-republish' );
				break;
			default:
				$text = __( 'Event Logged', 'wp-auto-republish' );
				break;
		}
		
        $title = _draft_or_post_title( $item['post_id'] );

		$status = ( $item['status'] ) ? '<span style="color: #068611;">' . __( 'Success', 'wp-auto-republish' ) . '</span>' : '<a href="#" class="wpar-log-error-viewer" data-reason="' . $item['reason'] . '">' . __( 'Error', 'wp-auto-republish' ) . '</a>';
		if ( in_array( $item['icon'], [ 'dashicons-clock', 'dashicons-backup' ] ) ) {
			$status = '<span style="color: #cc7d07;">' . __( 'Pending', 'wp-auto-republish' ) . '</span>';
		}

		$log = '<tr valign="top" class="wpar-log-entries wpar-entry-' . $item['log_id'] . '" data-type="' . $action  . '">
		    <td scope="row" data-label="' . __( 'Flag', 'wp-auto-republish' ) . '"><span class="dashicons ' . $item['icon'] . '" style="font-size: 18px;"></span></td>
		    <td scope="row" data-label="' . __( 'Timestamp', 'wp-auto-republish' ) . '">' . date_i18n( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), $item['timestamp'] ) . '</td>
			<td scope="row" data-label="' . __( 'Type', 'wp-auto-republish' ) . '">' . $item['post_type'] . '</td>
			<td scope="row" data-label="' . __( 'ID', 'wp-auto-republish' ) . '"><a href="' . esc_url( get_edit_post_link( $item['post_id'] ) ) . '" target="_blank">' . $item['post_id'] . '</a></td>
			<td scope="row" data-label="' . __( 'Post Title', 'wp-auto-republish' ) . '"><a href="' . esc_url( get_permalink( $item['post_id'] ) ) . '" target="_blank">' . wp_trim_words( $title, 25 ) . '</a></td>
			<td scope="row" data-label="' . __( 'Action Log', 'wp-auto-republish' ) . '">' . $text . '</td>
			<td scope="row" data-label="' . __( 'Status', 'wp-auto-republish' ) . '">' . $status . '</td>
			<td scope="row" data-label="' . __( 'Action', 'wp-auto-republish' ) . '"><span class="wpar-remove-log-item" title="' . __( 'Delete this item', 'wp-auto-republish' ) . '" data-selector="wpar-logs" data-entry-id="' . $item['log_id'] . '"><span class="dashicons dashicons-no" style="font-size: 18px;"></span></span></td>
		</tr>';

		return $log;
	}

	/**
	 * Single Twitter account delete on request.
	 */
	public function remove_log_item()
	{
		// security check
		$this->verify_nonce();

		if ( isset( $_POST['log_entry_id'] ) ) {
			$entry_id = sanitize_text_field( $_POST['log_entry_id'] );

            $data = unserialize( get_option( 'wpar_republish_log_history' ) );
			
			if ( isset( $data[$entry_id] ) ) {
			    unset( $data[$entry_id] );
			}

			update_option( 'wpar_republish_log_history', maybe_serialize( $data ) );

			$this->success();
		} else {
			$this->error();
		}
	}

	/**
	 * Post meta cleanup on request.
	 */
	public function delete_log()
	{
		// security check
		$this->verify_nonce();

		delete_option( 'wpar_republish_log_history' );
		
		$this->success( [
			'replace_html' => true
		] );
	}
}