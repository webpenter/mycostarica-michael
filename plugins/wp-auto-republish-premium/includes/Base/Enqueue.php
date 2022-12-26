<?php 
/**
 * Enqueue all css & js.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Base
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Base;

use Wpar\Helpers\Hooker;
use Wpar\Base\BaseController;

defined( 'ABSPATH' ) || exit;

/**
 * Script class.
 */
class Enqueue extends BaseController
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'admin_enqueue_scripts', 'assets' );
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$this->action( 'admin_enqueue_scripts', 'post_assets__premium_only' );
		}
	}

	/**
	 * Load admin assets.
	 */
	public function assets()
	{
		$version = ( $this->debug ) ? time() : $this->version;

		$this->load( 'css', 'jquery-ui-datepicker', 'jquery-ui.min.css', '1.12.1' );
		$this->load( 'css', 'jquery-ui-datetimepicker', 'jquery-ui-timepicker-addon.min.css', '1.6.3' );
		$this->load( 'css', 'selectize', 'selectize.min.css', '0.12.6' );
		$this->load( 'css', 'confirm', 'jquery-confirm.min.css', '3.3.4' );
		$this->load( 'css', 'jquery-ui-datepicker', 'jquery-ui.min.css', '1.12.1' );
		$this->load( 'css', 'styles', 'admin.min.css', $version, [ 'wpar-jquery-ui-datepicker', 'wpar-jquery-ui-datetimepicker', 'wpar-selectize', 'wpar-confirm' ] );

		$this->load( 'js', 'jquery-cookie', 'jquery.cookie.js', '1.4.1', [ 'jquery' ] );
		$this->load( 'js', 'datetimepicker', 'jquery-ui-timepicker-addon.min.js', '1.6.3', [ 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' ] );
		$this->load( 'js', 'selectize', 'selectize.min.js', '0.12.6', [ 'jquery' ] );
		$this->load( 'js', 'confirm', 'jquery-confirm.min.js', '3.3.4', [ 'jquery' ] );
		$this->load( 'js', 'admin', 'admin.min.js', $version, [ 'jquery', 'jquery-form', 'wpar-datetimepicker', 'wpar-selectize', 'wpar-confirm', 'wpar-jquery-cookie' ] );

        if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$this->load( 'css', 'post', 'post.min.css', $version, [ 'wpar-selectize', 'wpar-jquery-ui-datepicker', 'wpar-jquery-ui-datetimepicker' ] );
			$this->load( 'js', 'post', 'post.min.js', $version, [ 'jquery', 'wpar-datetimepicker', 'wpar-selectize' ] );
			$this->load( 'js', 'extras', 'extras.min.js', $version, [ 'jquery', 'wpar-admin' ] );
            $this->load( 'js', 'social', 'social.min.js', $version, [ 'jquery', 'wpar-admin' ] );
		}
			
		// get current screen
		$current_screen = get_current_screen();
		if ( strpos( $current_screen->base, 'wp-auto-republish' ) !== false ) {
			wp_enqueue_style( 'wpar-selectize' );
			wp_enqueue_style( 'wpar-jquery-ui-datepicker' );
			wp_enqueue_style( 'wpar-jquery-ui-datetimepicker' );
			wp_enqueue_style( 'wpar-styles' );

			wp_enqueue_script( 'jquery-form' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wpar-jquery-cookie' );
            wp_enqueue_script( 'wpar-datetimepicker' );
			wp_enqueue_script( 'wpar-selectize' );
			wp_enqueue_script( 'wpar-confirm' );
			wp_enqueue_script( 'wpar-admin' );

			if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    wp_enqueue_script( 'wpar-extras' );
				wp_enqueue_script( 'wpar-social' );
			}
			
			wp_localize_script( 'wpar-admin', 'wpar_admin_L10n', [
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'select_weekdays'     => __( 'Select weekdays (required)', 'wp-auto-republish' ),
				'select_post_types'   => __( 'Select post types (required)', 'wp-auto-republish' ),
				'select_user_roles'   => __( 'Select user roles (required)', 'wp-auto-republish' ),
				'select_taxonomies'   => __( 'Select taxonomies', 'wp-auto-republish' ),
				'post_ids'            => __( 'Enter post or page or custom post ids (comma separated)', 'wp-auto-republish' ),
			    'saving'              => __( 'Saving...', 'wp-auto-republish' ),
				'saving_text'         => __( 'Please wait while we are saving your settings...', 'wp-auto-republish' ),
				'done'                => __( 'Done!', 'wp-auto-republish' ),
				'error'               => __( 'Error!', 'wp-auto-republish' ),
				'deleting'            => __( 'Deleting...', 'wp-auto-republish' ),
				'warning'             => __( 'Warning!', 'wp-auto-republish' ),
				'processing'          => __( 'Please wait while we are processing your request...', 'wp-auto-republish' ),
				'save_button'         => __( 'Save Settings', 'wp-auto-republish' ),
				'save_success'        => __( 'Settings Saved Successfully!', 'wp-auto-republish' ),
				'are_you_sure'        => __( 'Are you sure that you want to delete this item?', 'wp-auto-republish' ),
				'process_failed'      => __( 'Invalid Nonce! We could not process your request.', 'wp-auto-republish' ),
				'ok_button'           => __( 'OK', 'wp-auto-republish' ),
				'confirm_button'      => __( 'Confirm', 'wp-auto-republish' ),
				'cancel_button'       => __( 'Cancel', 'wp-auto-republish' ),
				'close_btn'           => __( 'Close', 'wp-auto-republish' ),
				'paste_data'          => __( 'Paste Here', 'wp-auto-republish' ),
				'import_btn'          => __( 'Import', 'wp-auto-republish' ),
				'importing'           => __( 'Importing...', 'wp-auto-republish' ),
				'please_wait'         => __( 'Please wait...', 'wp-auto-republish' ),
				'no_logs_found'       => __( 'No logs found.', 'wp-auto-republish' ),
				'filter_btn'          => __( 'Filter', 'wp-auto-republish' ),
				'activating'          => __( 'Activating...', 'wp-auto-republish' ),
				'deactivating'        => __( 'Deactivating...', 'wp-auto-republish' ),
				'activate'            => __( 'Activate', 'wp-auto-republish' ),
				'deactivate'          => __( 'Deactivate', 'wp-auto-republish' ),
				'enabled'             => __( 'Enabled', 'wp-auto-republish' ),
				'disabled'            => __( 'Disabled', 'wp-auto-republish' ),
				'verify'              => __( 'Verify', 'wp-auto-republish' ),
				'new_account'         => __( 'New Account', 'wp-auto-republish' ),
				'is_empty'            => __( 'Please enter the required data first!', 'wp-auto-republish' ),
				'edit_template'       => __( 'Edit Template', 'wp-auto-republish' ),
				'save_template'       => __( 'Save Template', 'wp-auto-republish' ),
				'charecter_limit'     => __( 'Charecter limit', 'wp-auto-republish' ),
				'use_this_tags'       => __( 'Use this tags', 'wp-auto-republish' ),
				'security'            => wp_create_nonce( 'wpar_admin_nonce' ),
			] );
		}
	}

	/**
	 * Load metabox assets.
	 */
	public function post_assets__premium_only( $screen )
	{
		// check if post edit screen
		if( $screen === 'post-new.php' || $screen === 'post.php' ) {
			wp_enqueue_style( 'wpar-selectize' );
			wp_enqueue_style( 'wpar-jquery-ui-datepicker' );
			wp_enqueue_style( 'wpar-jquery-ui-datetimepicker' );
			wp_enqueue_style( 'wpar-post' );
			
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'wpar-datetimepicker' );
			wp_enqueue_script( 'wpar-selectize' );
			wp_enqueue_script( 'wpar-post' );
			
			wp_localize_script( 'wpar-post', 'wpar_post_L10n', [
				'date_format'       => $this->do_filter( 'post_editor_date_format', 'dd/mm/yy' ),
				'select_post_types' => __( 'Select post types (required)', 'wp-auto-republish' ),
				'select_taxonomies' => __( 'Select taxonomies (leave blank to republish all posts)', 'wp-auto-republish' ),
				'set_on'            => __( 'Set on', 'wp-auto-republish' ),
				'not_set'           => __( 'Not set yet', 'wp-auto-republish' ),
				'disabled'          => __( 'Republish disabled', 'wp-auto-republish' ),
				'text_on'           => __( 'on', 'wp-auto-republish' ),
				'text_on_all_days'  => __( 'on all days', 'wp-auto-republish' ),
				'week_repeat'       => __( 'weeks', 'wp-auto-republish' ),
				'weekly_repeat'     => __( 'Weekly', 'wp-auto-republish' ),
				'month_repeat'      => __( 'months', 'wp-auto-republish' ),
				'monthly_repeat'    => __( 'Monthly', 'wp-auto-republish' ),
				'year_repeat'       => __( 'years', 'wp-auto-republish' ),
				'yearly_repeat'     => __( 'Yearly', 'wp-auto-republish' ),
				'day_repeat'        => __( 'days', 'wp-auto-republish' ),
				'daily_repeat'      => __( 'Daily', 'wp-auto-republish' ),
				'hour_repeat'       => __( 'hours', 'wp-auto-republish' ),
				'hourly_repeat'     => __( 'Hourly', 'wp-auto-republish' ),
				'min_repeat'        => __( 'minutes', 'wp-auto-republish' ),
				'minutes_repeat'    => __( 'In Minutes', 'wp-auto-republish' ),
				'next_schedule'     => __( 'Next republish schedule is on', 'wp-auto-republish' ),
				'text_on_day'       => __( 'on day', 'wp-auto-republish' ),
				'text_on_the'       => __( 'on the', 'wp-auto-republish' ),
				'text_of'           => __( 'of', 'wp-auto-republish' ),
				'text_all_months'   => __( 'of all months', 'wp-auto-republish' ),
				'text_times'        => __( 'times', 'wp-auto-republish' ),
				'text_until'        => __( 'until', 'wp-auto-republish' ),
				'text_post_title'   => __( 'Enter Post Titles (use backspace or drag & drop to edit)', 'wp-auto-republish' ),
				'text_set_every'    => __( 'Set every', 'wp-auto-republish' ),
				'text_set'          => __( 'Set', 'wp-auto-republish' ),
				'clear_text'        => __( 'Clear', 'wp-auto-republish' ),
				'weekdays'          => json_encode( [
				    __( 'Sunday', 'wp-auto-republish' ),
				    __( 'Monday', 'wp-auto-republish' ),
				    __( 'Tuesday', 'wp-auto-republish' ),
				    __( 'Wednesday', 'wp-auto-republish' ),
				    __( 'Thursday', 'wp-auto-republish' ),
				    __( 'Friday', 'wp-auto-republish' ),
				    __( 'Saturday', 'wp-auto-republish' )
				] ),
				'weekdays_prefix'   => json_encode( [
					__( 'first', 'wp-auto-republish' ),
				    __( 'second', 'wp-auto-republish' ),
				    __( 'third', 'wp-auto-republish' ),
				    __( 'fourth', 'wp-auto-republish' ),
				    __( 'fifth', 'wp-auto-republish' )
				] )
			] );
		}
	}

	/**
	 * Register CSS & JS wrapper function.
	 */
	private function load( $type, $handle, $name, $version, $dep = [], $end = true )
	{
		if ( $type == 'css' ) {
		    wp_register_style( 'wpar-' . $handle, $this->plugin_url . 'assets/css/' . $name, $dep, $version );
		} else if ( $type == 'js' ) {
		    wp_register_script( 'wpar-' . $handle, $this->plugin_url . 'assets/js/' . $name, $dep, $version, $end );
		}
	}
}