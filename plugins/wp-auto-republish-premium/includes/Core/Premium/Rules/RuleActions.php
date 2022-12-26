<?php
/**
 * Actions.
 *
 * @since      1.2.5
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium\Rules
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Rules;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Post Metabox class.
 */
class RuleActions
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/republish_rule_updated', 'set_rule_event', 10, 3 );
		$this->action( 'trash_republish_rule', 'remove_schedule' );
		$this->action( 'draft_republish_rule', 'remove_schedule' );
		$this->action( 'pending_republish_rule', 'remove_schedule' );
		$this->action( 'post_submitbox_misc_actions', 'submitbox_edit', 5 );
		$this->action( 'quick_edit_custom_box', 'quick_edit', 10, 2 );
	}

	/**
	 * Generate Single Cron if required
	 * 
	 * @param int  $post_id   Post ID
	 * @param int  $datetime  Cron schedule
	 * @param bool $generate  Generate if necessary
	 */
	public function set_rule_event( $post_id, $datetime, $generate )
	{
		// clear cron event if any
		$this->unschedule_all_actions( 'wpar/run_republish_rule_event', [ $post_id ] );

		// delete the meta
		$this->delete_meta( $post_id, 'wpar_republish_rule_next_timestamp' );

		$interval = $this->do_filter( 'random_single_republish_interval', 5 ) * MINUTE_IN_SECONDS;
		$repeats = $this->get_meta( $post_id, '_wpar_repost_repeats' );
		if ( ! in_array( $repeats, [ 'minutes', 'hourly' ] ) ) {
			$datetime = $datetime + mt_rand( 0, $interval );
		}

		$formatted_datetime = date( 'Y-m-d H:i:s', $datetime );
		$timestamp = current_time( 'timestamp', 1 );
		$new_datetime = get_gmt_from_date( $formatted_datetime, 'U' );
		$option = $this->get_meta( $post_id, '_wpar_repost_option' );
		if ( $option != 'disable' && ( $new_datetime > $timestamp ) && $generate ) {
            // schedule single post republish event
		    $this->set_single_action( $new_datetime, 'wpar/run_republish_rule_event', [ $post_id ] );

			// update required post metas
			$this->update_meta( $post_id, 'wpar_republish_rule_next_timestamp', $formatted_datetime );
		}
	}

	/**
	 * Remove Tasks
	 * 
	 * @param int  $post_id   Post ID
	 */
	public function remove_schedule( $post_id )
	{
		// clear cron event if any
        $this->unschedule_all_actions( 'wpar/run_republish_rule_event', [ $post_id ] );
	}

	/**
	 * Post Submit Box HTML output.
	 * 
	 * @param string $post WP Post
	 */
	public function submitbox_edit( $post )
	{
		if ( $post->post_type === 'republish_rule' ) {
		    // hides everything except the 'publish' button in the 'publish'-metabox
		    echo '<style type="text/css"> #duplicate-action, #minor-publishing-actions, #misc-publishing-actions, #preview-action { display: none !important; } </style>';
		}
	}

	/**
	 * Quick ecit HTML output.
	 * 
	 * @param string  $column_name  Current column name
	 * @param string  $post_type    Post type
	 */
	public function quick_edit( $column_name, $post_type )
	{
		if ( $column_name === 'republish_rule' && $post_type === 'republish_rule' ) {
		    // hides everything except the 'publish' button in the 'publish'-metabox
		    echo '<style type="text/css"> .inline-edit-group, #inline-edit-col-modified-date, .wplmi-bulkedit { display: none !important; } </style>';
		}
	}
}