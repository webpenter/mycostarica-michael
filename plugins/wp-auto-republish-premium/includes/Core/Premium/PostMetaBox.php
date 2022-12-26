<?php
/**
 * Meta Box.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\Premium\GetMetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Post Metabox class.
 */
class PostMetaBox
{
	use Hooker, GetMetaData;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'add_meta_boxes', 'generate_metabox', 10, 2 );
		$this->action( 'save_post', 'save_metadata' );
	}

	/**
	 * Register metaboxes.
	 * 
	 * @param post $post The post object
	 */
	public function generate_metabox( $post_type, $post )
	{
		// If user can't publish posts, then get out
		if ( ! current_user_can( 'publish_posts' ) ) {
			return;
		}

		$orig_id = $this->get_meta( $post->ID, 'wpar_original_post_id' );
		
		// break early if given post is not an actual scheduled post created by this plugin.
		if ( $post->post_status === 'future' && $orig_id ) {
			return;
		}

		$post_types = array_unique( array_merge( $this->get_data( 'wpar_post_types', [ 'post' ] ), $this->get_data( 'post_types_list_single', [ 'post' ] ) ) );
        if ( ! in_array( $post_type, $post_types ) ) {
			return;
		}
		
		if ( $this->user_has_cap__premium_only() && $this->do_filter( 'show_republish_meta_boxes', true, $post ) ) {
		    if ( $this->is_enabled( 'enable_plugin', true ) || $this->is_enabled( 'enable_single_republishing', true ) || $this->is_enabled( 'enable_instant_republishing', true ) ) {
		        add_meta_box( 'wpar_meta_box', __( 'Republish Settings', 'wp-auto-republish' ), [ $this, 'single_callback' ], $post_type, 'normal' );
				add_meta_box( 'wpar_meta_box_republish', __( 'Republish Info', 'wp-auto-republish' ), [ $this, 'action_callback' ], $post_type, 'side' );
		    }
	    }
	}
	
	/**
	 * Build republish date meta box.
	 *
	 * @param object $post The post object
	 */
	public function single_callback( $post )
	{
		$timestamp = current_time( 'timestamp', 0 );
		$start_time = $this->get_data( 'wpar_start_time', '05:00:00' );
		$post_types = $this->get_data( 'post_types_list_single', [ 'post' ] );

		$repost_option = $this->get_meta( $post->ID, '_wpar_repost_option' );
		$repost_after_number = $this->get_meta( $post->ID, '_wpar_repost_after_number' );
		$repost_after_time = $this->get_meta( $post->ID, '_wpar_repost_after_time' );
		$repost_on_date = $this->build_meta( $post->ID, '_wpar_repost_on_date', date( 'm/d/Y', $timestamp ) );
		
		$repost_repeats = $this->get_meta( $post->ID, '_wpar_repost_repeats' );
		$repost_every = $this->get_meta( $post->ID, '_wpar_repost_every' );
		$repost_monthly_on = $this->build_meta( $post->ID, '_wpar_repost_monthly_on', [], true );
		$repost_by = $this->get_meta( $post->ID, '_wpar_repost_by' );
		$repost_start = $this->build_meta( $post->ID, '_wpar_repost_start', date( 'm/d/Y', $timestamp ) );
		
		$repost_end = $this->get_meta( $post->ID, '_wpar_repost_end' );
		$repost_end_after = $this->get_meta( $post->ID, '_wpar_repost_end_after' );
		$repost_end_on = $this->build_meta( $post->ID, '_wpar_repost_end_on', date( 'm/d/Y', strtotime( '+2 days', $timestamp ) ) );
		$repost_status = $this->get_meta( $post->ID, '_wpar_repost_last_republish_status' );
	
		$repost_schedule = $this->get_meta( $post->ID, '_wpar_repost_schedule' );
		$repost_at_time = $this->build_meta( $post->ID, '_wpar_repost_at_specific_time', $start_time );
		$repost_enable = $this->get_meta( $post->ID, '_wpar_repost_done' );
		$repost_datetime = $this->get_meta( $post->ID, '_wpar_repost_schedule_datetime' );

		$repost_start_time = $this->get_meta( $post->ID, '_wpar_repost_start_time' );
		$repost_end_time = $this->get_meta( $post->ID, '_wpar_repost_end_time' );
		$global_pending = $this->get_meta( $post->ID, 'wpar_global_republish_status' );
		$single_pending = $this->get_meta( $post->ID , 'wpar_single_republish_status' );
		$interval = $this->do_filter( 'random_single_republish_interval', 5 );

		$repost_option = $this->get_meta( $post->ID, '_wpar_repost_option' );
		$is_active = $this->build_meta( $post->ID, '_wpar_repost_post_titles_active', 'no' );
		$is_update = $this->build_meta( $post->ID, '_wpar_repost_post_update_url', 'no' );
		$action = $this->build_meta( $post->ID, '_wpar_repost_post_update_action', 'default' );
		$get_titles = $this->get_meta( $post->ID, '_wpar_repost_post_titles' );
	
		$titles = [];
		$titles_array = explode( ';;;', $get_titles );
		foreach( $titles_array as $title ) {
			$titles[] = wp_strip_all_tags( $title );
		}
		$get_titles = implode( ';;;', $titles );

		// make sure the form request comes from WordPress
		$this->nonce( 'single' );
		
		if ( $this->is_enabled( 'enable_single_republishing', true ) && in_array( $post->post_type, $post_types ) ) {
		    ?><h2><span style="font-weight: 700;"><?php _e( 'Schedule', 'wp-auto-republish' ); ?>:</span> <?php
		    
		    if ( $repost_schedule ) {
		    	if ( $repost_option != 'disable' ) {
		    		echo sprintf(
		    			'<span id="wpar_repost_date">%s</span>',
		    			$this->get_schedule( $post->ID, 'edit' )
		    		);
		    	} else {
		    		echo sprintf(
		    			'<span id="wpar_repost_date">%s</span>',
		    			__( 'Republish disabled', 'wp-auto-republish' )
		    		);
		    	}
		    } else {
		    	echo sprintf(
		    		'<span id="wpar_repost_date">%s</span>',
		    		__( 'Not set yet', 'wp-auto-republish' )
		    	);
		    }
		    
		    ?> <span class="wpar-time-div"<?php if ( empty( $repost_option ) || $repost_option == 'disable' ) { echo ' style="display: none;"'; } ?>><?php _e( 'at', 'wp-auto-republish' ); ?> <input type="text" name="wpar_repost_at_specific_time" id="wpar_repost_at_specific_time" class="wpar-republish-timepicker" value="<?php echo $repost_at_time; ?>" readonly="readonly" style="width: 85px;vertical-align: baseline;" /><span title="<?php printf( __( 'Republish will occur after this time within %s minutes.', 'wp-auto-republish' ), $interval ); ?>" class="dashicons dashicons-editor-help" style="font-size: 18px;vertical-align: middle;"></span></span>
		    </h2><fieldset class="options wpar-options">
		    	<div class="option" style="padding: 8px 10px;">
		    		<label>
		    			<input type="radio" name="wpar_repost_option" id="wpar_repost_option_1" value="days" <?php checked( 'days', $repost_option ); ?> style="vertical-align: middle;" />
		    			<?php _e( 'Republish after', 'wp-auto-republish' ); ?>
		    			<input type="number" name="wpar_repost_after_number" id="wpar_repost_after_number" value="<?php echo $repost_after_number; ?>" class="small" min="1" max="365" style="vertical-align: middle;" />
		    			<select name="wpar_repost_after_time" id="wpar_repost_after_time">
		    				<option value="days" <?php selected( 'days', $repost_after_time ) ?> ><?php _e( 'Days', 'wp-auto-republish' ); ?></option>
		    				<option value="weeks" <?php selected( 'weeks', $repost_after_time ) ?> ><?php _e( 'Weeks', 'wp-auto-republish' ); ?></option>
		    				<option value="months" <?php selected( 'months', $repost_after_time ) ?> ><?php _e( 'Months', 'wp-auto-republish' ); ?></option>
							<option value="years" <?php selected( 'years', $repost_after_time ) ?> ><?php _e( 'Years', 'wp-auto-republish' ); ?></option>
		    			</select>
		    		</label>
		    	</div>
		    	<div class="option">
		    		<label>
		    			<input type="radio" name="wpar_repost_option" id="wpar_repost_option_2" value="date" <?php checked( 'date', $repost_option ); ?> style="vertical-align: middle;" />
		    			<?php _e( 'Republish on', 'wp-auto-republish' ); ?>
		    			<input type="text" id="wpar-static-date" readonly="readonly" value="<?php echo date( 'd/m/Y', strtotime( $repost_on_date ) ); ?>" style="vertical-align: middle;" />
		    			<input type="hidden" name="wpar_repost_on_date" id="wpar_repost_on_date" value="<?php echo $repost_on_date; ?>" style="vertical-align: middle;" />
		    		</label>
		    	</div>
		    	<div class="option">
		    		<label>
		    			<input type="radio" name="wpar_repost_option" id="wpar_repost_option_3" value="repeat" <?php checked( 'repeat', $repost_option ); ?> style="vertical-align: sub;" />
		    			<?php _e('Enable Repeat Republishing', 'wp-auto-republish'); ?>
		    		</label>
		    		<div class="clear"></div>
		    		<div class="sub-option" id="repeat_repost_option"<?php echo ( $repost_option != 'repeat' ) ? ' style="display:none;"' : ''; ?>>
		    			<div style="margin-top: 15px;">
		    				<label>
		    					<span class="label"><?php _e( 'Repeats', 'wp-auto-republish' ); ?>:</span>
		    					<span class="fields">
		    						<select name="wpar_repost_repeats" id="wpar_repost_repeats">
		    						    <option value="minutes" <?php selected( 'minutes', $repost_repeats ) ?> ><?php _e( 'Minutes', 'wp-auto-republish' ); ?></option>
		    							<option value="hourly" <?php selected( 'hourly', $repost_repeats ) ?> ><?php _e( 'Hourly', 'wp-auto-republish' ); ?></option>
		    							<option value="daily" <?php selected( 'daily', $repost_repeats ) ?> ><?php _e( 'Daily', 'wp-auto-republish' ); ?></option>
		    							<option value="weekly" <?php selected( 'weekly', $repost_repeats ) ?> ><?php _e( 'Weekly', 'wp-auto-republish' ); ?></option>
		    							<option value="monthly" <?php selected( 'monthly', $repost_repeats ) ?> ><?php _e( 'Monthly', 'wp-auto-republish' ); ?></option>
		    							<option value="yearly" <?php selected( 'yearly', $repost_repeats ) ?> ><?php _e( 'Yearly', 'wp-auto-republish' ); ?></option>
		    						</select>
		    					</span>
		    				</label>
		    				<div class="clear"></div>
		    			</div>
		    			<div>
		    				<label>
		    					<span class="label"><?php _e( 'Repeat Every', 'wp-auto-republish' ); ?>:</span>
		    					<span class="fields">
		    						<select name="wpar_repost_every" id="wpar_repost_every"><?php
		    						for( $i = 1; $i <= 59; $i++ ) {
		    							echo sprintf( '<option value="%1$s" %2$s>%1$s</option>', $i, selected( $i, $repost_every, false ) );
		    						}
		    						?></select>
		    						<input type="hidden" id="wpar-minute-limit" value="<?php echo $this->do_filter( 'minute_starts_from', 5 ); ?>"/>
		    					</span>
		    				</label>
		    				<div class="clear"></div>
		    			</div>
		    			<div id="wpar_repeat_monthly_on_wrap"<?php echo ( $repost_repeats != 'monthly' ) ? ' style="display:none;"' : ''; ?>>
		    				<div class="wpar_elements">
		    					<span class="label" style="margin: 0 10px 0 0;"><?php _e('Repeat On', 'wp-auto-republish'); ?>:</span>
		    					<span class="fields offset">
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_1" value="january" <?php checked( 1, in_array( 'january', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="0" data-month="<?php _e( 'January', 'wp-auto-republish'); ?>" /> <?php _e( 'January', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_2" value="february" <?php checked( 1, in_array( 'february', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on"  data-value="1" data-month="<?php _e( 'February', 'wp-auto-republish'); ?>" /> <?php _e( 'February', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_3" value="march" <?php checked( 1, in_array( 'march', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on"  data-value="2" data-month="<?php _e( 'March', 'wp-auto-republish'); ?>" /> <?php _e( 'March', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_4" value="april" <?php checked( 1, in_array( 'april', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="3" data-month="<?php _e( 'April', 'wp-auto-republish'); ?>" /> <?php _e( 'April', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_5" value="may" <?php checked( 1, in_array( 'may', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="4" data-month="<?php _e( 'May', 'wp-auto-republish'); ?>" /> <?php _e( 'May', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_6" value="june" <?php checked( 1, in_array( 'june', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="5" data-month="<?php _e( 'June', 'wp-auto-republish'); ?>" /> <?php _e( 'June', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_7" value="july" <?php checked( 1, in_array( 'july', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="6" data-month="<?php _e( 'July', 'wp-auto-republish'); ?>" /> <?php _e( 'July', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_8" value="august" <?php checked( 1, in_array( 'august', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="7" data-month="<?php _e( 'August', 'wp-auto-republish'); ?>" /> <?php _e( 'August', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_9" value="september" <?php checked( 1, in_array( 'september', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="8" data-month="<?php _e( 'September', 'wp-auto-republish'); ?>" /> <?php _e( 'September', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_10" value="october" <?php checked( 1, in_array( 'october', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="9" data-month="<?php _e( 'October', 'wp-auto-republish'); ?>" /> <?php _e( 'October', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_11" value="november" <?php checked( 1, in_array( 'november', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="10" data-month="<?php _e( 'November', 'wp-auto-republish'); ?>" /> <?php _e( 'November', 'wp-auto-republish'); ?> </label>
		    						<label class="wpar_checklabel" ><input type="checkbox" name="wpar_repost_monthly_on[]" id="wpar_repost_monthly_on_12" value="december" <?php checked( 1, in_array( 'december', $repost_monthly_on ) ); ?> class="wpar_repost_monthly_on" data-value="11" data-month="<?php _e( 'December', 'wp-auto-republish'); ?>" /> <?php _e( 'December', 'wp-auto-republish'); ?> </label>
		    					</span>
		    				</div>
		    				<div class="clear"></div>
		    			</div>
		    			<div id="wpar_repeat_by_wrap"<?php echo ( $repost_repeats != 'monthly' ) ? ' style="display:none;"' : ''; ?>>
		    				<div class="wpar_elements">
		    					<span class="label" style="margin: 0 10px 0 0;"><?php _e( 'Repeat By', 'wp-auto-republish' ); ?>:</span>
		    					<span class="fields">
		    						<label style="padding-right: 10px;"><input type="radio" name="wpar_repost_by" id="wpar_repost_by_month" value="month" <?php checked( 'month', $repost_by ); ?> /><?php _e( 'Day of the month', 'wp-auto-republish' ); ?></label>
		    						<label style="padding-right: 10px;"><input type="radio" name="wpar_repost_by" id="wpar_repost_by_week" value="week" <?php checked( 'week', $repost_by ); ?> <?php if( empty( $repost_by ) ) { echo ' checked="checked"'; } ?> /><?php _e( 'Day of the week', 'wp-auto-republish' ); ?></label>
		    					</span>
		    				</div>
		    				<div class="clear"></div>
		    			</div>
		    			<div>
		    				<label>
		    					<span class="label"><?php _e( 'Starts On', 'wp-auto-republish' ); ?>:</span>
		    					<span class="fields"><input type="text" id="wpar-static-repost-start" readonly="readonly" value="<?php echo date( 'd/m/Y', strtotime( $repost_start ) ); ?>" />
		    					<input type="hidden" name="wpar_repost_start" id="wpar_repost_start" value="<?php echo $repost_start; ?>" /></span>
		    				</label>
		    				<div class="clear"></div>
		    			</div>
		    			<div>
		    				<div class="wpar_elements">
		    					<span class="label"><?php _e( 'Ends On', 'wp-auto-republish' ); ?>:</span>
		    					<div class="fields" style="margin: 0 auto 0;">
		    						<label style="padding-right: 10px;">
		    							<input type="radio" name="wpar_repost_end" id="wpar_repost_end" value="never" <?php checked( 'never', $repost_end ); ?><?php if( empty( $repost_end ) ) { echo ' checked="checked"'; } ?> style="vertical-align: middle;" /> <?php _e( 'Never', 'wp-auto-republish' ); ?>
		    						</label>
		    						<label style="padding-right: 10px;">
		    							<input type="radio" name="wpar_repost_end" id="wpar_repost_end_1" value="after" <?php checked( 'after', $repost_end ); ?> style="vertical-align: middle;" /> <?php _e( 'After', 'wp-auto-republish' ); ?>
		    							<input type="number" name="wpar_repost_end_after" id="wpar_repost_end_after" value="<?php echo $repost_end_after; ?>" class="small" min="1" style="vertical-align: unset;" /> <?php _e( 'occurences', 'wp-auto-republish' ); ?>
		    						</label>
		    						<label style="padding-right: 10px;">
		    							<input type="radio" name="wpar_repost_end" id="wpar_repost_end_2" value="on" <?php checked( 'on', $repost_end ); ?> style="vertical-align: middle;" /> <?php _e( 'Specific Date', 'wp-auto-republish' ); ?>
		    							<input type="text" id="wpar-repost-end-on" readonly="readonly" value="<?php echo date( 'd/m/Y', strtotime( $repost_end_on ) ); ?>" style="vertical-align: baseline;"/>
		    							<input type="hidden" name="wpar_repost_end_on" id="wpar_repost_end_on" value="<?php echo $repost_end_on; ?>" />
		    						</label>
		    					</div>
		    				</div>
		    				<div class="clear"></div>
		    			</div>
		    			<div id="wpar_last_republish_status" style="margin-top: 5px;<?php echo ( $repost_end == 'never' ) ? 'display: none;' : ''; ?>">
		    				<label>
		    					<span class="label"><?php _e( 'Set Status', 'wp-auto-republish' ); ?>:</span>
		    					<span class="fields">
		    						<select name="wpar_repost_last_republish_status" id="wpar_repost_last_republish_status">
		    							<?php $post_statuses = array_merge( get_post_stati( [ 'public' => true, 'publicly_queryable' => true ], 'objects' ), get_post_stati( [ 'private' => true ], 'objects' ), get_post_stati( [ 'show_in_admin_status_list' => true, 'internal' => true ], 'objects' ), get_post_stati( [ 'date_floating' => true, 'protected' => true ], 'objects' ) );
		    							echo '<option value="default"' . selected( 'default', $repost_status ) .  '>' . __( 'Default', 'wp-auto-republish' ) . '</option>';
		    							foreach ( $post_statuses as $post_status => $object ) {
		    								echo '<option value="'. $post_status . '"' . selected( $post_status, $repost_status ) .  '>' . $object->label . '</option>';
		    							} ?>
		    						</select>&nbsp;<?php _e( 'at the time of last repeated republish', 'wp-auto-republish' ); ?>
		    					</span>
		    				</label>
		    				<div class="clear"></div>
		    			</div>
		    			<div id="wpar_last_republish_time_range" style="margin-top: 5px;<?php echo ( ! in_array( $repost_repeats, [ 'minutes', 'hourly' ] ) ) ? 'display: none;' : ''; ?>">
		    				<label>
		    					<span class="label"><?php _e( 'Time Range', 'wp-auto-republish' ); ?>:</span>
		    					<div class="fields" style="margin: 0 auto 0;">
		    						<label style="padding-right: 10px;"><?php _e( 'Starts at', 'wp-auto-republish' ); ?>
		    							<input type="text" name="wpar_repost_start_time" id="wpar-repost-start-time" class="wpar-timepicker" value="<?php echo $repost_start_time; ?>" placeholder="<?php _e( 'No Restriction', 'wp-auto-republish' ); ?>" readonly="readonly" style="vertical-align: baseline;"/>
		    						</label>
		    						<label style="padding-right: 10px;"><?php _e( 'Ends at', 'wp-auto-republish' ); ?>
		    							<input type="text" name="wpar_repost_end_time" id="wpar-repost-end-time" class="wpar-timepicker" value="<?php echo $repost_end_time; ?>" placeholder="<?php _e( 'No Restriction', 'wp-auto-republish' ); ?>" readonly="readonly" style="vertical-align: baseline;"/>
		    						</label>
		    					</div>
		    				</label>
		    				<div class="clear"></div>
		    			</div>
		    		</div>
		    	</div>
		    	<div class="option">
		    		<label>
		    			<input type="radio" name="wpar_repost_option" id="wpar_repost_option_4" value="disable" <?php checked( 'disable', $repost_option ); ?><?php if( empty( $repost_option ) ) { echo ' checked="checked"'; } ?> style="vertical-align: sub;" />
		    			<?php _e( 'Disable Single Republishing', 'wp-auto-republish' ); ?>
		    		</label>
		    	</div>
		    	<input type="hidden" name="wpar_repost_schedule" id="wpar_repost_schedule" value="<?php echo date( 'm/d/Y', strtotime( $repost_schedule ) ); ?>" />
		    	<input type="hidden" name="wpar_repost_done" id="wpar_repost_done" value="<?php echo ( ! empty( $repost_enable ) ) ? $repost_enable : 'yes'; ?>" data-last="<?php echo $repost_enable; ?>" data-republish="<?php echo $repost_datetime; ?>" />
		    	<input type="hidden" name="wpar_regenerate_cron" id="wpar_regenerate_cron_event" value="no" />
		    </fieldset><?php
        } ?>
        <fieldset class="options">
			<div class="option" style="padding: 10px 10px 5px;">
				<label for="wpar_repost_post_titles_active">
					<span style="vertical-align: middle;"><strong><?php _e( 'Re-Publish Titles', 'wp-auto-republish' ); ?>:</strong></span>
					<span class="fields">
						<select name="wpar_repost_post_titles_active" id="wpar_repost_post_titles_active">
							<option value="yes" <?php selected( 'yes', $is_active ) ?> ><?php _e( 'Yes', 'wp-auto-republish' ); ?></option>
							<option value="no" <?php selected( 'no', $is_active ) ?> ><?php _e( 'No', 'wp-auto-republish' ); ?></option>
						</select>
					</span>
				</label>
				<label for="wpar_repost_post_update_url" class="wpar_post_update_url" <?php if ( empty( $is_active ) || $is_active == 'no' ) { echo 'style="display: none;"'; } ?>>
				    &nbsp;<span style="vertical-align: middle;"><strong><?php _e( 'Update Post URL', 'wp-auto-republish' ); ?>:</strong></span>
					<span class="fields">
						<select name="wpar_repost_post_update_url" id="wpar_repost_post_update_url">
							<option value="yes" <?php selected( 'yes', $is_update ) ?> ><?php _e( 'Yes', 'wp-auto-republish' ); ?></option>
							<option value="no" <?php selected( 'no', $is_update ) ?> ><?php _e( 'No', 'wp-auto-republish' ); ?></option>
						</select>
					</span>
				</label>
				<?php if ( $this->is_enabled( 'enable_plugin', true ) || $this->is_enabled( 'enable_single_republishing', true ) ) { ?>
				    <label for="wpar_repost_post_update_action" class="wpar_post_update_action">
				        &nbsp;<span style="vertical-align: middle;"><strong><?php _e( 'Republish Action', 'wp-auto-republish' ); ?>:</strong></span>
				    	<span class="fields">
				    		<select name="wpar_repost_post_update_action" id="wpar_repost_post_update_action">
				    		    <option value="default" <?php selected( 'default', $action ) ?> ><?php _e( 'Default', 'wp-auto-republish' ); ?></option>
				    			<option value="repost" <?php selected( 'repost', $action ) ?> ><?php _e( 'Republish Post', 'wp-auto-republish' ); ?></option>
				    			<option value="clone" <?php selected( 'clone', $action ) ?> ><?php _e( 'Duplicate Post', 'wp-auto-republish' ); ?></option>
				    		</select>
				    	</span>
				    </label>
				<?php } ?>
			</div>
			<div class="option title-list" style="padding: 8px 10px;<?php if ( empty( $is_active ) || $is_active == 'no' ) { echo 'display: none;'; } ?>">
				<label for="wpar_repost_post_titles">
					<div style="margin-bottom: 6px;"><strong><?php _e( 'Enter the New Post Titles', 'wp-auto-republish' ); ?>:</strong></div>
					<input type="text" name="wpar_repost_post_titles" id="wpar_repost_post_titles" value="<?php if ( ! empty( $get_titles ) ) { echo $get_titles; } else { echo _draft_or_post_title( $post ); } ?>" style="vertical-align: middle;width: 100%;" />
				</label>
			</div>
		</fieldset>
		<?php 
	}
	
	/**
	 * Build republish action meta box.
	 *
	 * @param object $post The post object
	 */
	public function action_callback( $post )
	{
		$reposted = maybe_unserialize( $this->get_meta( $post->ID, '_wpar_repost_date' ) );
		$excluded = $this->get_meta( $post->ID, '_wpar_exclude_auto_republish' );
		$global_pending = $this->get_meta( $post->ID , 'wpar_global_republish_status' );
		$original_info = $this->get_meta( $post->ID, '_wpar_hide_original_info' );
		$disable_email = $this->get_meta( $post->ID, '_wpar_disable_email' );
		$disable_facebook_share = $this->get_meta( $post->ID, '_wpar_disable_facebook_share' );
		$disable_twitter_share = $this->get_meta( $post->ID, '_wpar_disable_twitter_share' );
		$disable_linkedin_share = $this->get_meta( $post->ID, '_wpar_disable_linkedin_share' );
		$disable_tumblr_share = $this->get_meta( $post->ID, '_wpar_disable_tumblr_share' );
		$current_post_types = get_post_type_object( get_post_type( $post ) );
        $post_types = $this->do_filter( 'republish_action_post_types', $this->get_data( 'post_types_list_single', [ 'post' ] ), $post );
		
		$this->nonce( 'action' );

		// generate nonce url
		$build_url = add_query_arg( [ 'post_type' => $post->post_type, 'wpar_action' => 'republish', 'wpar_post_id' => $post->ID ] );
		$build_share_url = add_query_arg( [ 'post_type' => $post->post_type, 'wpar_action' => 'share', 'wpar_post_id' => $post->ID ] );

		if ( $post->post_status !== 'auto-draft' ) {
	    	if ( ! empty( $reposted ) ) {
	    		$last_reposted = $reposted[ count( $reposted ) - 1 ];
	    		echo sprintf(
	    			'<div class="count misc-pub-republished" style="display: inline-block;padding: 6px 10px 0px 0px;"><span id="republish-count">%s</span></div>',
	    			sprintf( __( 'Republished %s time(s)', 'wp-auto-republish' ), '<strong>' . count( $reposted ) . '</strong>' )
	    		);
	    	} else {
	    		echo sprintf(
	    			'<div class="count misc-pub-republished" style="display: inline-block;padding: 6px 10px 0px 0px;"><span id="republish-count">%s</span></div>',
	    			sprintf( __( 'Not republished yet', 'wp-auto-republish' ) )
	    		);
	    	}
	    }
		
		if ( $this->is_enabled( 'enable_instant_republishing', true ) && ! in_array( $post->post_status, [ 'auto-draft', 'trash', 'future' ] ) && in_array( $post->post_type, $post_types ) ) {
	    	echo sprintf( '<div style="margin: 10px 0 0;"><div style="display: inline;"><a href="%1$s" class="button button-secondary">%2$s</a></div>&nbsp;',
			    wp_nonce_url( add_query_arg( 'wpar_type', 'instant', $build_url ), 'wpar_republish_' . $post->ID ),
	    		__( 'Republish', 'wp-auto-republish' )
			);
			echo sprintf( '<div style="display: inline;"><a href="%1$s" class="button button-secondary" style="margin-left: -1px;">%2$s</a></div>&nbsp;',
			    wp_nonce_url( add_query_arg( 'wpar_type', 'duplicate', $build_url ), 'wpar_republish_' . $post->ID ),
	    		__( 'Clone', 'wp-auto-republish' )
			);
			echo sprintf( '<div style="display: inline;"><a href="%1$s" class="button button-secondary">%2$s</a></div></div>',
			    wp_nonce_url( add_query_arg( 'wpar_type', 'scheduled', $build_url ), 'wpar_republish_' . $post->ID ),
	    		__( 'Rewrite', 'wp-auto-republish' )
	    	);

			if ( $this->is_social_enabled__premium_only() ) {
		    	echo sprintf( '<div style="margin: 5px 0 0;"><div style="display: inline;"><a href="%1$s" class="button button-secondary">%2$s</a></div></div>',
		    	    wp_nonce_url( $build_share_url, 'wpar_republish_' . $post->ID ),
	        		__( 'Instant Social Media Share', 'wp-auto-republish' )
	        	);
		    }
	    }

		if ( ! empty( $reposted ) ) {
			$timestamps = '';
			$reposted = array_slice( array_reverse( $reposted ), 1, $this->do_filter( 'show_republish_count', 15 ), true );
			foreach( $reposted as $times ) {
				$timestamps .= sprintf(
					'<span>%s</span><br />',
					date_i18n( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), strtotime( $times ) )
				);
			}
	
			if ( count( $reposted ) > 1 ) {
			    echo sprintf(
			    	'<div class="clear" style="padding: 9px 0px 0px;"><span id="republish-timestamp">%1$s <b>%2$s</b></span><div id="republish-timestampdiv" class="hide-if-js" style="margin-top: 10px;">%3$s</div></div>',
			    	sprintf(
			    		'<a href="#republish-timestampdiv" class="edit-republish-timestamp hide-if-no-js"><span aria-hidden="true">%s</span></a>',
			    		__( 'Last Republished:', 'wp-auto-republish' )
			    	),
			    	date_i18n( 'M j, Y @ H:i', strtotime( $last_reposted ) ),
			    	$timestamps
			    );
		    } else {
				echo sprintf(
					'<div class="clear" style="padding: 9px 0px 0px;"><span id="republish-timestamp">%1$s <b>%2$s</b></span><div id="republish-timestampdiv" class="hide-if-js" style="margin-top: 10px;">%3$s</div></div>',
					__( 'Last Republished:', 'wp-auto-republish' ),
					date_i18n( 'M j, Y @ H:i', strtotime( $last_reposted ) ),
					$timestamps
				);
			}
		}

		if ( $this->is_enabled( 'enable_plugin', true ) && ! $global_pending ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_exclude_auto_republish" ><input type="checkbox" name="wpar_exclude_auto_republish" id="wpar_exclude_auto_republish" value="yes" <?php checked( $excluded, 'yes' ); ?> /> <?php _e( 'Exclude from Auto Republishing', 'wp-auto-republish' ); ?> </label>
				</span>
			</div> <?php
		}

		if ( $this->get_data( 'wpar_republish_position' ) != 'disable' ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_hide_original_info" ><input type="checkbox" name="wpar_hide_original_info" id="wpar_hide_original_info" value="yes" <?php checked( $original_info, 'yes' ); ?> /> <?php _e( 'Hide Original Publish Info', 'wp-auto-republish' ); ?> </label>
				</span>
			</div> <?php
		}

		if ( $this->is_enabled( 'enable_email_notify' ) && in_array( $post->post_type, $this->get_data( 'email_post_types', [ 'post' ] ) ) ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_disable_email" ><input type="checkbox" name="wpar_disable_email" id="wpar_disable_email" value="yes" <?php checked( $disable_email, 'yes' ); ?> /> <?php _e( 'Disable Notification for this ', 'wp-auto-republish' ); ?><?php echo $current_post_types->labels->singular_name ?> </label>
				</span>
			</div> <?php
		}

		if ( $this->is_enabled( 'facebook_enable' ) && in_array( $post->post_type, $this->get_data( 'facebook_post_types_display', [ 'post' ] ) ) ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_disable_facebook_share" ><input type="checkbox" name="wpar_disable_facebook_share" id="wpar_disable_facebook_share" value="yes" <?php checked( $disable_facebook_share, 'yes' ); ?> /> <?php _e( 'Disable Facebook Share for this ', 'wp-auto-republish' ); ?><?php echo $current_post_types->labels->singular_name ?> </label>
				</span>
			</div> <?php
		}

		if ( $this->is_enabled( 'twitter_enable' ) && in_array( $post->post_type, $this->get_data( 'twitter_post_types_display', [ 'post' ] ) ) ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_disable_twitter_share" ><input type="checkbox" name="wpar_disable_twitter_share" id="wpar_disable_twitter_share" value="yes" <?php checked( $disable_twitter_share, 'yes' ); ?> /> <?php _e( 'Disable Twitter Share for this ', 'wp-auto-republish' ); ?><?php echo $current_post_types->labels->singular_name ?> </label>
				</span>
			</div> <?php
		}

		if ( $this->is_enabled( 'linkedin_enable' ) && in_array( $post->post_type, $this->get_data( 'linkedin_post_types_display', [ 'post' ] ) ) ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_disable_linkedin_share" ><input type="checkbox" name="wpar_disable_linkedin_share" id="wpar_disable_linkedin_share" value="yes" <?php checked( $disable_linkedin_share, 'yes' ); ?> /> <?php _e( 'Disable Linkedin Share for this ', 'wp-auto-republish' ); ?><?php echo $current_post_types->labels->singular_name ?> </label>
				</span>
			</div> <?php
		}

		/*if ( $this->is_enabled( 'tumblr_enable' ) && in_array( $post->post_type, $this->get_data( 'tumblr_post_types_display', [ 'post' ] ) ) ) { ?>
			<div style="margin: 10px 0px 0px;">
				<span class="fields offset" style="vertical-align: middle;display: inline-block;">
					<label for="wpar_disable_tumblr_share" ><input type="checkbox" name="wpar_disable_tumblr_share" id="wpar_disable_tumblr_share" value="yes" <?php checked( $disable_tumblr_share, 'yes' ); ?> /> <?php _e( 'Disable Tumblr Share for this ', 'wp-auto-republish' ); ?><?php echo $current_post_types->labels->singular_name ?> </label>
				</span>
			</div> <?php
		}*/
	}
	
	/**
	 * Store custom field meta box data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_metadata( $post_id )
	{
		// return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$can_schedule = $has_meta = false;
		$metas = [];

		// verify taxonomies meta box nonce
		if ( $this->verify( 'single' ) ) {
			// store custom fields values
			$metas[] = [
				'wpar_repost_option',
				'wpar_repost_after_number',
				'wpar_repost_after_time',
				'wpar_repost_on_date',
				'wpar_repost_repeats',
				'wpar_repost_every',
				'wpar_repost_monthly_on',
				'wpar_repost_by',
				'wpar_repost_start',
				'wpar_repost_end',
				'wpar_repost_end_after',
				'wpar_repost_end_on',
				'wpar_repost_last_republish_status',
				'wpar_repost_schedule',
				'wpar_repost_done',
				'wpar_repost_at_specific_time',
				'wpar_repost_start_time',
				'wpar_repost_end_time',
				'wpar_regenerate_cron',
				'wpar_repost_post_titles_active',
				'wpar_repost_post_update_url',
				'wpar_repost_post_titles',
				'wpar_repost_post_update_action'
			];

			// check if actually metabox active
			$can_schedule = true;
		}
		
		if ( $this->verify( 'republish' ) ) {
			// store custom fields values
			$metas[] = [
				'wpar_exclude_auto_republish',
				'wpar_hide_original_info',
				'wpar_disable_email',
				'wpar_disable_facebook_share',
				'wpar_disable_twitter_share',
				'wpar_disable_linkedin_share',
				//'wpar_disable_tumblr_share'
			];
		}
	
		if ( ! empty( $metas ) ) {
			$metas = array_merge( ...$metas );
			foreach ( $metas as $meta_key ) {
				// check if post meta exists
				if ( isset( $_POST[$meta_key] ) ) {
					$value = $_POST[$meta_key];
					if ( is_array( $value ) ) {
						$value = array_map( 'sanitize_text_field', $value );
					} else {
						$value = sanitize_text_field( $value );
					}
					// add or update post meta if not exists
					$this->update_meta( $post_id, '_' . $meta_key, $value );
				} else {
					// delete post meta if not exists
					$this->delete_meta( $post_id, '_' . $meta_key );
				}
			}
		}

		// break early if given post is not published.
		if ( 'publish' !== get_post_status( $post_id ) ) {
			// check if actually metabox active
			$can_schedule = false;
		}

		$schedule = $this->get_meta( $post_id, '_wpar_repost_schedule' );
		$repost_at_time = $this->get_meta( $post_id, '_wpar_repost_at_specific_time' );
		$regenerate = $this->get_meta( $post_id, '_wpar_regenerate_cron' );
		
		if ( $can_schedule && $schedule && $repost_at_time && $regenerate ) {
			$datetime = strtotime( date( 'Y-m-d', strtotime( $schedule ) ) . ' ' . $repost_at_time );
			if ( $regenerate == 'yes' ) {
			    // fire cron generate action
			    $this->do_action( 'single_post_updated', $post_id, $datetime, true );
			}
		}
	}

	/**
	 * Store custom field meta box data.
	 *
	 * @param int $post_id The post ID.
	 */
	private function nonce( $name, $referer = true, $echo = true )
	{
		\wp_nonce_field( 'wpar_nonce_'.$name, 'wpar_metabox_'.$name.'_nonce', $referer, $echo );
	}

	/**
	 * Store custom field meta box data.
	 *
	 * @param int $post_id The post ID.
	 */
	private function verify( $name )
	{
		if ( ! isset( $_REQUEST['wpar_metabox_'.$name.'_nonce'] ) || ! \wp_verify_nonce( $_REQUEST['wpar_metabox_'.$name.'_nonce'], 'wpar_nonce_'.$name ) ) {
			return false;
		}

		return true;
	}
}