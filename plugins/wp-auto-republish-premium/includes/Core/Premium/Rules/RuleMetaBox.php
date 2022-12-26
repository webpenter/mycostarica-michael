<?php
/**
 * Meta Box.
 *
 * @since      1.2.5
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium\Rules
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium\Rules;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\Premium\GetMetaData;

defined( 'ABSPATH' ) || exit;

/**
 * Post Metabox class.
 */
class RuleMetaBox
{
	use Hooker, GetMetaData;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'add_meta_boxes', 'generate_metabox', 10, 2 );
		$this->action( 'save_post_republish_rule', 'save_metadata' );
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

		if ( $this->do_filter( 'show_republish_meta_boxes', true, $post ) ) {
		    add_meta_box( 'wpar_meta_box', __( 'Republish Settings', 'wp-auto-republish' ), [ $this, 'single_callback' ], 'republish_rule', 'normal', 'high' );
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

		$post_types_meta = $this->build_meta( $post->ID, '_wpar_repost_post_types', [], true );
		$taxonomies_meta = $this->build_meta( $post->ID, '_wpar_repost_taxonomies', [], true ); 
		$post_order = $this->build_meta( $post->ID, '_wpar_repost_post_order', 'old_first' );
		$post_orderby = $this->build_meta( $post->ID, '_wpar_repost_post_orderby', 'date' );
		$randomness = $this->build_meta( $post->ID, '_wpar_repost_post_randomness', 3600 );
		$eligibility_age = $this->build_meta( $post->ID, '_wpar_repost_eligibility_age', '' );
		$number_posts = $this->build_meta( $post->ID, '_wpar_repost_number_posts', 1 );
		$republish_action = $this->build_meta( $post->ID, '_wpar_repost_republish_action', 'repost' );
		
		// make sure the form request comes from WordPress
		$this->nonce( 'single' );
		
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
		?> <span class="wpar-time-div"<?php if ( empty( $repost_option ) || $repost_option == 'disable' ) { echo ' style="display: none;"'; } ?>><?php _e( 'at', 'wp-auto-republish' ); ?> <input type="text" name="wpar_repost_at_specific_time" id="wpar_repost_at_specific_time" class="wpar-republish-timepicker" value="<?php echo $repost_at_time; ?>" readonly="readonly" style="width: 85px;vertical-align: baseline;" /></span>
		</h2>
		<fieldset class="options wpar-options">
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
		    							<?php echo '<option value="default"' . selected( 'default', $repost_status ) .  '>' . __( 'Published', 'wp-auto-republish' ) . '</option>';
		    							echo '<option value="draft"' . selected( 'draft', $repost_status ) .  '>' . __( 'Un-Published', 'wp-auto-republish' ) . '</option>'; ?>
		    						</select>&nbsp;<?php _e( 'of this Rule at the time of last rule run', 'wp-auto-republish' ); ?>
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
					<input type="radio" name="wpar_repost_option" id="wpar_repost_option_4" value="disable" <?php checked( 'disable', $repost_option ); ?><?php if ( empty( $repost_option ) ) { echo ' checked="checked"'; } ?> style="vertical-align: sub;" />
					<?php _e( 'Disable this Republish Rule', 'wp-auto-republish' ); ?>
				</label>
			</div>
			<input type="hidden" name="wpar_repost_schedule" id="wpar_repost_schedule" value="<?php echo date( 'm/d/Y', strtotime( $repost_schedule ) ); ?>" />
			<input type="hidden" name="wpar_repost_done" id="wpar_repost_done" value="<?php echo ( ! empty( $repost_enable ) ) ? $repost_enable : 'yes'; ?>" data-last="<?php echo $repost_enable; ?>" data-republish="<?php echo $repost_datetime; ?>" />
			<input type="hidden" name="wpar_regenerate_cron" id="wpar_regenerate_cron_event" value="no" />
		</fieldset>
		<fieldset class="options wpar-republish-rule-fields">
			<div class="option" style="padding: 10px 10px 5px;">
			    <label for="wpar_repost_post_types">
				    <div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'Select Post Types that you want to be Republished', 'wp-auto-republish' ); ?>:</strong></div>
					<span class="fields">
						<?php $post_types = $this->get_post_types();
		                echo '<select id="wpar_repost_post_types" name="wpar_repost_post_types[]" multiple="multiple" required style="width:100%;">';
		                foreach( $post_types as $post_type => $label ) {
                            $selected = in_array( $post_type, $post_types_meta ) ? ' selected="selected"' : '';
		                	echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		                }
		                echo '</select>'; ?>
					</span>
				</label>
			</div>
			<div class="option" style="padding: 10px 10px 5px;">
			    <label for="wpar_repost_taxonomies">
				    <div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'Select Post / Page / Custom Post Types Taxonomies', 'wp-auto-republish' ); ?>:</strong></div>
					<span class="fields">
						<?php echo '<select id="wpar_repost_taxonomies" name="wpar_repost_taxonomies[]" multiple="multiple" style="width:100%;">';
		                $taxonomies = $this->get_all_taxonomies( [ 'public' => true ], false, false );
						if ( ! empty( $taxonomies ) ) {
							foreach( $taxonomies as $post_type => $post_data ) {
								echo '<optgroup label="'.$post_data['label'].'">';
								if ( isset( $post_data['categories'] ) && ! empty( $post_data['categories'] ) && is_array( $post_data['categories'] ) ) {
									foreach( $post_data['categories'] as $cat_slug => $cat_name ) {
										$selected = in_array( $cat_slug, $taxonomies_meta ) ? ' selected="selected"' : '';
										echo '<option value="' . $cat_slug . '" ' . $selected . '>' . $cat_name . '</option>';
									}
								}
								echo '</optgroup>';
							}
						}
		                echo '</select>'; ?>
					</span>
				</label>
			</div>
			<div class="option" style="padding: 8px 10px;">
			    <div class="wpar-metabox-row">
                    <div class="wpar-metabox-column">
			    	    <label for="wpar_repost_post_order">
			    		    <div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'Select Published Posts Order', 'wp-auto-republish' ); ?>:</strong></div>
			    		    <?php $items = [
		                    	'old_first'   => __( 'Select Old Post First (ASC)', 'wp-auto-republish' ),
		                    	'new_first'   => __( 'Select New Post First (DESC)', 'wp-auto-republish' )
		                    ];
		                    echo '<select id="wpar_repost_post_order" name="wpar_repost_post_order" style="width:95%;">';
		                    foreach( $items as $item => $label ) {
		                    	$selected = ( $post_order == $item ) ? ' selected="selected"' : '';
		                    	echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		                    }
		                    echo '</select>'; ?>
		                </label>
			        </div>
                    <div class="wpar-metabox-column">
			        	<label for="wpar_repost_post_orderby">
			        		<div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'Select Published Posts Order by', 'wp-auto-republish' ); ?>:</strong></div>
			        		<?php $items = [
		                    	'date'           => __( 'Post Date', 'wp-auto-republish' ),
		                    	'ID'             => __( 'Post ID', 'wp-auto-republish' ),
		                    	'author'         => __( 'Post Author', 'wp-auto-republish' ),
		                    	'title'          => __( 'Post Title', 'wp-auto-republish' ),
		                    	'rand'           => __( 'Random Selection', 'wp-auto-republish' ),
		                    	'comment_count'  => __( 'Comment Count', 'wp-auto-republish' ),
		                    	'relevance'      => __( 'Relevance', 'wp-auto-republish' ),
		                    	'menu_order'     => __( 'Menu Order', 'wp-auto-republish' ),
		                    ];
		                    echo '<select id="wpar_repost_post_orderby" name="wpar_repost_post_orderby" style="width:95%;">';
		                    foreach( $items as $item => $label ) {
		                    	$selected = ( $post_orderby == $item ) ? ' selected="selected"' : '';
		                    	echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		                    }
		                    echo '</select>'; ?>
						</label>
			        </div>
					<div class="wpar-metabox-column">
			        	<label for="wpar_repost_post_randomness">
			        		<div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'New Date Time Randomness', 'wp-auto-republish' ); ?>:</strong></div>
			        		<?php $items = [
		                    	'60'     => __( 'No Randomness', 'wp-auto-republish' ),
								'300'    => __( 'Upto 5 Minutes', 'wp-auto-republish' ),
								'600'    => __( 'Upto 10 Minutes', 'wp-auto-republish' ),
								'900'    => __( 'Upto 15 Minutes', 'wp-auto-republish' ),
								'1200'   => __( 'Upto 20 Minutes', 'wp-auto-republish' ),
								'1800'   => __( 'Upto 30 Minutes', 'wp-auto-republish' ),
								'2700'   => __( 'Upto 45 Minutes', 'wp-auto-republish' ),
								'3600'   => __( 'Upto 1 hour', 'wp-auto-republish' ),
								'7200'   => __( 'Upto 2 hours', 'wp-auto-republish' ),
								'14400'  => __( 'Upto 4 hours', 'wp-auto-republish' ),
								'21600'  => __( 'Upto 6 hours', 'wp-auto-republish' ),
								'28800'  => __( 'Upto 8 hours', 'wp-auto-republish' ),
			                    '43200'  => __( 'Upto 12 hours', 'wp-auto-republish' ),
			                    '86400'  => __( 'Upto 24 hours', 'wp-auto-republish' )
		                    ];
		                    echo '<select id="wpar_repost_post_randomness" name="wpar_repost_post_randomness" style="width:95%;">';
		                    foreach( $items as $item => $label ) {
		                    	$selected = ( $randomness == $item ) ? ' selected="selected"' : '';
		                    	echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		                    }
		                    echo '</select>'; ?>
						</label>
			        </div>
					<div class="wpar-metabox-column">
			        	<label for="wpar_repost_republish_action">
			        		<div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'Post Auto Republish Action', 'wp-auto-republish' ); ?>:</strong></div>
			        		<?php $items = [
		                    	'repost'  => __( 'Republish Post', 'wp-auto-republish' ),
								'clone'   => __( 'Duplicate Post', 'wp-auto-republish' )
		                    ];
		                    echo '<select id="wpar_repost_republish_action" name="wpar_repost_republish_action" style="width:95%;">';
		                    foreach( $items as $item => $label ) {
		                    	$selected = ( $republish_action == $item ) ? ' selected="selected"' : '';
		                    	echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		                    }
		                    echo '</select>'; ?>
						</label>
			        </div>
			        <div class="wpar-metabox-column">
			        	<label for="wpar_repost_eligibility_age">
			        		<div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'Post Republish Eligibility Age (in days)', 'wp-auto-republish' ); ?>:</strong></div>
			        		<input type="number" name="wpar_repost_eligibility_age" id="wpar_repost_eligibility_age" min="1" max="500" placeholder="<?php _e( '180 (leave blank for no limit)', 'wp-auto-republish' ); ?>" value="<?php echo $eligibility_age; ?>" style="width: 95%;" />
			        	</label>
			        </div>
			        <div class="wpar-metabox-column">
			        	<label for="wpar_repost_number_posts">
			        		<div style="margin-bottom: 6px;font-size: 11px;"><strong><?php _e( 'No. of Posts to be Republished at a Time', 'wp-auto-republish' ); ?>:</strong></div>
			        		<input type="number" name="wpar_repost_number_posts" id="wpar_repost_number_posts" min="1" max="500" value="<?php echo $number_posts ?>" style="width: 100%;" />
			        	</label>
			        </div>
			    </div>
			</div>
		</fieldset>
		<?php
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

		$can_schedule = false;
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
				'wpar_repost_schedule',
				'wpar_repost_done',
				'wpar_repost_at_specific_time',
				'wpar_repost_last_republish_status',
				'wpar_repost_start_time',
				'wpar_repost_end_time',
				'wpar_regenerate_cron',
				'wpar_repost_post_types',
				'wpar_repost_taxonomies',
				'wpar_repost_post_order',
				'wpar_repost_post_orderby',
				'wpar_repost_post_randomness',
				'wpar_repost_republish_action',
				'wpar_repost_eligibility_age',
				'wpar_repost_number_posts'
			];

			// check if actually metabox active
			$can_schedule = true;
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
			    $this->do_action( 'republish_rule_updated', $post_id, $datetime, true );
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