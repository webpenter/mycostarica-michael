<?php 
/**
 * Settings callbacks.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Api\Callbacks
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Api\Callbacks;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

class ManagerCallbacks
{
	use HelperFunctions, Hooker;

	public function enable_plugin( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_enable_plugin]" value="1" <?php checked( $this->get_data( 'wpar_enable_plugin' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to auto republish old posts of your blog.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function minimun_republish_interval( $args )
	{
		$items = [
			'300'     => __( '5 Minutes', 'wp-auto-republish' ),
			'600'     => __( '10 Minutes', 'wp-auto-republish' ),
			'900'     => __( '15 Minutes', 'wp-auto-republish' ),
			'1200'    => __( '20 Minutes', 'wp-auto-republish' ),
			'1800'    => __( '30 Minutes', 'wp-auto-republish' ),
			'2700'    => __( '45 Minutes', 'wp-auto-republish' ),
			'3600'    => __( '1 hour', 'wp-auto-republish' ),
			'7200'    => __( '2 hours', 'wp-auto-republish' ),
			'14400'   => __( '4 hours', 'wp-auto-republish' ),
			'21600'   => __( '6 hours', 'wp-auto-republish' ),
			'28800'   => __( '8 hours', 'wp-auto-republish' ),
			'43200'   => __( '12 hours', 'wp-auto-republish' ),
			'86400'   => __( '24 hours (1 day)', 'wp-auto-republish' ),
			'172800'  => __( '48 hours (2 days)', 'wp-auto-republish' ),
			'259200'  => __( '72 hours (3 days)', 'wp-auto-republish' ),
			'432000'  => __( '120 hours (5 days)', 'wp-auto-republish' ),
			'604800'  => __( '168 hours (7 days)', 'wp-auto-republish' ),
			'custom'  => __( 'Custom Interval (Premium)', 'wp-auto-republish' ),
		];
		$items = $this->do_filter( 'minimum_republish_interval', $items );
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_minimun_republish_interval]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_minimun_republish_interval', 43200 ) == $item ) ? ' selected="selected"' : '';
			$disabled = '';
			if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    $disabled = ( ! is_numeric( $item ) ) ? ' disabled="disabled"' : '';
			}
			echo '<option value="' . $item . '"' . $selected . $disabled . '>' . $label . '</option>';
		}
		echo '</select><input type="hidden" id="wpar_global_cron_change" value="none">';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post republish interval between two post republish event.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_custom_interval__premium_only( $args )
    {
		?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[republish_custom_interval]" type="number" size="40" style="width:40%;" placeholder="25" min="5" value="<?php echo $this->get_data( 'republish_custom_interval' ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'You can set custom interval in minutes between two republish event from here. Minimum is 5 minutes.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}

	public function random_republish_interval( $args )
	{
		$items = [
			'premium_1'  => __( 'No Randomness (Premium)', 'wp-auto-republish' ),
			'premium_2'  => __( 'Upto 5 Minutes (Premium)', 'wp-auto-republish' ),
			'premium_3'  => __( 'Upto 10 Minutes (Premium)', 'wp-auto-republish' ),
			'premium_4'  => __( 'Upto 15 Minutes (Premium)', 'wp-auto-republish' ),
			'premium_5'  => __( 'Upto 20 Minutes (Premium)', 'wp-auto-republish' ),
			'premium_6'  => __( 'Upto 30 Minutes (Premium)', 'wp-auto-republish' ),
			'premium_7'  => __( 'Upto 45 Minutes (Premium)', 'wp-auto-republish' ),
			'3600'       => __( 'Upto 1 hour', 'wp-auto-republish' ),
			'7200'       => __( 'Upto 2 hours', 'wp-auto-republish' ),
			'14400'      => __( 'Upto 4 hours', 'wp-auto-republish' ),
			'21600'      => __( 'Upto 6 hours', 'wp-auto-republish' ),
			'premium_8'  => __( 'Upto 8 hours (Premium)', 'wp-auto-republish' ),
			'premium_9'  => __( 'Upto 12 hours (Premium)', 'wp-auto-republish' ),
			'premium_10' => __( 'Upto 24 hours (Premium)', 'wp-auto-republish' )
		];
		$items = $this->do_filter( 'random_republish_interval', $items );
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_random_republish_interval]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_random_republish_interval', 14400 ) == $item ) ? ' selected="selected"' : '';
			$disabled = '';
			if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    $disabled = ( ! is_numeric( $item ) ) ? ' disabled="disabled"' : '';
			}
			echo '<option value="' . $item . '"' . $selected . $disabled . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select randomness interval from here which will be added to minimum republish interval.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_post_position( $args )
	{
		$items = [
			'one'   => __( 'First Position', 'wp-auto-republish' ),
			'two'   => __( 'Second Position', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_republish_post_position]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_republish_post_position', 'one' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the position of republished post (choosing the 2nd position will leave the most recent post in 1st place).', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_action__premium_only( $args )
	{
		$items = [
			'repost'  => __( 'Republish Post', 'wp-auto-republish' ),
			'clone'   => __( 'Duplicate Post', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_republish_action]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_republish_action', 'repost' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the post republish action from here. Default is Republish Post.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function number_of_posts__premium_only( $args )
    {
		?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[number_of_posts]" type="number" size="40" style="width:40%;" placeholder="5" min="1" max="500" value="<?php echo $this->get_data( 'number_of_posts', 1 ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'You can set here the number of Posts to be Republished at a time.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}

	public function republish_time_start( $args )
    {
        ?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_start_time]" type="text" class="wpar-timepicker" size="40" style="width:40%;" placeholder="05:00:00" required readonly="readonly" value="<?php echo $this->get_data( 'wpar_start_time', '05:00:00' ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the starting time period for republish old posts from here.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
    }
    
    public function republish_time_end( $args )
    {
        ?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_end_time]" type="text" class="wpar-timepicker" size="40" style="width:40%;" placeholder="23:59:59" required readonly="readonly" value="<?php echo $this->get_data( 'wpar_end_time', '23:59:59' ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the ending time period for republish old posts from here.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}

	public function republish_days( $args )
    {
		$items = [
            'sun'  => __( 'Sunday', 'wp-auto-republish' ),
            'mon'  => __( 'Monday', 'wp-auto-republish' ),
            'tue'  => __( 'Tuesday', 'wp-auto-republish' ),
            'wed'  => __( 'Wednesday', 'wp-auto-republish' ),
            'thu'  => __( 'Thursday', 'wp-auto-republish' ),
            'fri'  => __( 'Friday', 'wp-auto-republish' ),
            'sat'  => __( 'Saturday', 'wp-auto-republish' )
		];
        echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_days][]" multiple="multiple" required style="width:90%;">';
        foreach( $items as $item => $label ) {
            $selected = in_array( $item, $this->get_data( 'wpar_days', [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ] ) ) ? ' selected="selected"' : '';
            echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
        }
        echo '</select>';
        ?>
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the weekdays when you want to republish old posts.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_info( $args )
	{
		$items = [
			'disable'         => __( 'Disable', 'wp-auto-republish' ),
			'before_content'  => __( 'Before Content', 'wp-auto-republish' ),
			'after_content'   => __( 'After Content', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_republish_position]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_republish_position', 'disable' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select how you want to show original published date of the post on frontend.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}
	
	public function republish_info_text( $args )
	{
		?> <input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_republish_position_text]" type="text" size="35" style="width:40%;" required value="<?php echo htmlspecialchars( wp_kses_post( $this->get_data( 'wpar_republish_position_text', 'Originally posted on ' ) ) ); ?>" />
			&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Message before original published date of the post on frontend.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function post_types_list_display__premium_only( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[wpar_post_types_display][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'wpar_post_types_display', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types on which you want to display original published date.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_post_age( $args )
	{
		$items = [
			'premium_1'   => __( 'No Age Limit (Premium)', 'wp-auto-republish' ),
			'premium_2'   => __( '1 Day (Premium)', 'wp-auto-republish' ),
			'premium_3'   => __( '2 Days (Premium)', 'wp-auto-republish' ),
			'premium_4'   => __( '3 Days (Premium)', 'wp-auto-republish' ),
			'premium_5'   => __( '5 Days (Premium)', 'wp-auto-republish' ),
			'premium_6'   => __( '7 Days (Premium)', 'wp-auto-republish' ),
			'premium_7'   => __( '10 Days (Premium)', 'wp-auto-republish' ),
			'premium_8'   => __( '14 Days (Premium)', 'wp-auto-republish' ),
			'premium_9'   => __( '21 Days (Premium)', 'wp-auto-republish' ),
			'premium_10'  => __( '28 Days (Premium)', 'wp-auto-republish' ),
			'30'          => __( '30 Days (1 month)', 'wp-auto-republish' ),
			'45'          => __( '45 Days (1.5 months)', 'wp-auto-republish' ),
			'60'          => __( '60 Days (2 months)', 'wp-auto-republish' ),
			'90'          => __( '90 Days (3 months)', 'wp-auto-republish' ),
			'120'         => __( '120 Days (4 months)', 'wp-auto-republish' ),
			'180'         => __( '180 Days (6 months)', 'wp-auto-republish' ),
			'240'         => __( '240 Days (8 months)', 'wp-auto-republish' ),
			'365'         => __( '365 Days (1 year)', 'wp-auto-republish' ),
			'730'         => __( '730 Days (2 years)', 'wp-auto-republish' ),
			'1095'        => __( '1095 Days (3 years)', 'wp-auto-republish' ),
			'premium_11'  => __( '1460 Days (4 Years) (Premium)', 'wp-auto-republish' ),
			'premium_12'  => __( '1825 Days (5 Years) (Premium)', 'wp-auto-republish' ),
			'premium_13'  => __( '2190 Days (6 Years) (Premium)', 'wp-auto-republish' ),
			'premium_14'  => __( '2555 Days (7 Years) (Premium)', 'wp-auto-republish' ),
			'premium_15'  => __( '2920 Days (8 Years) (Premium)', 'wp-auto-republish' ),
			'premium_16'  => __( '3285 Days (9 Years) (Premium)', 'wp-auto-republish' ),
			'premium_17'  => __( '3650 Days (10 Years) (Premium)', 'wp-auto-republish' ),
			'premium_18'  => __( 'Custom Age Limit (Premium)', 'wp-auto-republish' )
		];
		$items = $this->do_filter( 'republish_eligibility_age', $items );
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_republish_post_age]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_republish_post_age', 120 ) == $item ) ? ' selected="selected"' : '';
			$disabled = '';
			if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    $disabled = ( ! is_numeric( $item ) ) ? ' disabled="disabled"' : '';
			}
			echo '<option value="' . $item . '"' . $selected . $disabled . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the post age for republishing. Post actually published before this, will be available for republish.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<p id="wpar-post-age-help"><small style="line-height: 2;font-style: italic;"><span class="wpar-has-limit"><?php printf(
			__( 'Posts published before %s are eligible for republish.', 'wp-auto-republish' ), '<span class="wpar-age-date"></span>'
		); ?></span><span class="wpar-no-limit"><?php _e( 'All published posts will be eligible for republish.', 'wp-auto-republish' ); ?></span></small></p><?php
	}

	public function republish_custom_age__premium_only( $args )
    {
		?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[republish_post_custom_age]" type="number" size="40" style="width:40%;" placeholder="180" min="1" max="7300" value="<?php echo $this->get_data( 'republish_post_custom_age' ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set the custom post republishing age (in days) from here.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}

	public function republish_order( $args )
	{
		$items = [
			'old_first'   => __( 'Select Old Post First (ASC)', 'wp-auto-republish' ),
			'new_first'   => __( 'Select New Post First (DESC)', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_republish_method]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_republish_method', 'old_first' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the method of getting old posts from database.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_orderby( $args )
	{
		$items = [
			'date'        => __( 'Post Date', 'wp-auto-republish' ),
			'premium_1'   => __( 'Post ID (Premium)', 'wp-auto-republish' ),
			'premium_2'   => __( 'Post Author (Premium)', 'wp-auto-republish' ),
			'premium_3'   => __( 'Post Title (Premium)', 'wp-auto-republish' ),
			'premium_4'   => __( 'Random Selection (Premium)', 'wp-auto-republish' ),
			'premium_5'   => __( 'Comment Count (Premium)', 'wp-auto-republish' ),
			'premium_6'   => __( 'Relevance (Premium)', 'wp-auto-republish' ),
			'premium_7'   => __( 'Menu Order (Premium)', 'wp-auto-republish' )
		];
		$items = $this->do_filter( 'republish_orderby_items', $items );
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_republish_orderby]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_republish_orderby', 'date' ) == $item ) ? ' selected="selected"' : '';
			$disabled = '';
			if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			    $disabled = ( strpos( $item, 'premium' ) !== false ) ? ' disabled="disabled"' : '';
			}
			echo '<option value="' . $item . '"' . $selected . $disabled . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the method of getting old posts order by parameter. Default: Post Date', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_post_age_start_method__premium_only( $args )
	{
		$items = [
			'specific_date'  => __( 'A Specific Date', 'wp-auto-republish' ),
			'last_hours'     => __( 'Last Minutes', 'wp-auto-republish' ),
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[republish_post_age_start_method]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'republish_post_age_start_method', 'specific_date' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the method by which you want to exclude posts before a particular time interval or date.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function republish_post_age_start__premium_only( $args )
    {
		$current_time = current_time( 'timestamp', 0 );
		$time = date( 'd/m/Y', strtotime( '-15 years', $current_time ) ); ?>
        <input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[republish_post_age_start]" type="text" class="wpar-datepicker" size="40" style="width:40%;" placeholder="05:00" required readonly="readonly" value="<?php echo $this->get_data( 'republish_post_age_start', $time ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the Republish range date from here. Republish process will ignore all posts originally published before this date.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <p><small style="line-height: 2;font-style: italic;"><?php _e( 'Posts published after this date are eligible for republish.', 'wp-auto-republish' ); ?></small></p><?php
	}

	public function republish_custom_age_start__premium_only( $args )
    {
		?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[republish_custom_age_start]" type="number" size="40" style="width:40%;" placeholder="60" min="1" value="<?php echo $this->get_data( 'republish_custom_age_start' ); ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the Republish range interval from here. Republish process will ignore all posts originally published before this interval.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <p><small style="line-height: 2;font-style: italic;"><?php _e( 'All posts published before the specified minutes from now are eligible for republish.', 'wp-auto-republish' ); ?></small></p><?php
	}

	public function post_types_list( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[wpar_post_types][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'wpar_post_types', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types of which you want to republish.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}
	
	public function exclude_by_type( $args )
	{
        $items = [
            'none'     => __( 'Ignoring all Taxonomies', 'wp-auto-republish' ),
            'include'  => __( 'Including Taxonomies', 'wp-auto-republish' ),
			'exclude'  => __( 'Excluding Taxonomies', 'wp-auto-republish' )
		];
        echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_exclude_by_type]" style="width:40%;">';
        foreach( $items as $item => $label ) {
            $selected = ( $this->get_data( 'wpar_exclude_by_type', 'none' ) == $item ) ? ' selected="selected"' : '';
            echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
        }
        echo '</select>';
        ?>
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select how you want to include or exclude a post category from republishing. If you choose excluding, selected taxonomies will be ignored and vice-versa.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
    }
    
	public function post_taxonomy( $args )
	{
		echo '<select id="' . $args['label_for'] . '" class="wpar-taxonomies" name="wpar_plugin_settings[wpar_post_taxonomy][]" multiple="multiple" style="width:90%;">';
		$taxonomies = $this->get_all_taxonomies( [ 'public' => true, '_builtin' => true ] );
		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
			$taxonomies = $this->get_all_taxonomies( [ 'public' => true ], false, false );
		}
		if ( ! empty( $taxonomies ) ) {
			foreach( $taxonomies as $post_type => $post_data ) {
				echo '<optgroup label="'.$post_data['label'].'">';
				if ( isset( $post_data['categories'] ) && ! empty( $post_data['categories'] ) && is_array( $post_data['categories'] ) ) {
					foreach( $post_data['categories'] as $cat_slug => $cat_name ) {
						$selected = in_array( $cat_slug, $this->get_data( 'wpar_post_taxonomy', [] ) ) ? ' selected="selected"' : '';
				        echo '<option value="' . $cat_slug . '" ' . $selected . '>' . $cat_name . '</option>';
					}
				}
				echo '</optgroup>';
			}
		}
        echo '</select>';
        ?>
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select taxonimies which you want to include to republishing or exclude from republishing.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}

	public function override_category_tag( $args )
	{
    	$wpar_omit_override = preg_replace( [ '/[^\d,]/', '/(?<=,),+/', '/^,+/', '/,+$/' ], '', $this->get_data( 'wpar_override_category_tag' ) );
    	?> <input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_override_category_tag]" type="text" size="90" style="width:90%;" value="<?php echo $wpar_omit_override; ?>" />
        &nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Write the post IDs which you want to select forcefully (when you select excluding) or want to not select forcefully (when you select including).', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}
	
	public function enable_single_republishing__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_enable_single_republishing]" value="1" <?php checked( $this->get_data( 'wpar_enable_single_republishing' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to enable automatic republish of single posts of your blog. It adds a metabox on post edit screen from where you can set custom republish events.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
	   <?php
	}

	public function enable_instant_republishing__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_enable_instant_republishing]" value="1" <?php checked( $this->get_data( 'wpar_enable_instant_republishing' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to enable republish links on post edit rows and post edit screen.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
	   <?php
	}

	public function single_republish_action__premium_only( $args )
	{
		$items = [
			'repost'  => __( 'Republish Post', 'wp-auto-republish' ),
			'clone'   => __( 'Duplicate Post', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_single_republish_action]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'wpar_single_republish_action', 'repost' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select the single post republish action from here. Default is Republish Post.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}
	
	public function post_types_list_single__premium_only( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[post_types_list_single][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'post_types_list_single', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types on which you want to enable single republish. It will add a metabox to the all posts from which you can configure the single republishing for a particular post.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function single_roles__premium_only( $args )
	{
		$roles = get_editable_roles();
        echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[wpar_single_roles][]" multiple="multiple" required style="width:90%;">';
		foreach ( $roles as $role => $details ) {
            $selected = in_array( esc_attr( $role ), $this->get_data( 'wpar_single_roles', [ 'administrator' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . esc_attr( $role ) . '"' . $selected . '>' . translate_user_role( $details['name'] ) . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Set user roles who can access the metabox and post row links.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}

	public function fb_social_enable__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[facebook_enable]" value="1" <?php checked( $this->get_data( 'facebook_enable' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to enable auto post publish to Facebook upon post republishing.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function fb_social_og_tag__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[facebook_og_tag]" value="1" <?php checked( $this->get_data( 'facebook_og_tag' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to add Open Graph metadata to your site head section and other social networks use this data when your pages are shared. If you are using any SEO plugin, you can leave this option as disable.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}
	
	public function fb_social_post_as__premium_only( $args )
	{
		$items = [
			'link' => __( 'Post as Link', 'wp-auto-republish' ),
			'status'  => __( 'Post as Status', 'wp-auto-republish' ),
			'link_status'  => __( 'Post as Status & Link', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[facebook_post_as]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'facebook_post_as', 'link_status' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select Facebook post template type from here.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function fb_social_content_source__premium_only( $args )
	{
		$items = [
			'post_content'  => __( 'Post Content', 'wp-auto-republish' ),
			'post_excerpt'  => __( 'Post Excerpt', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[facebook_content_source]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'facebook_content_source', 'post_content' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select Facebook post template content source from here. %post_content% will be replaced by this in the below field.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function fb_social_template__premium_only( $args )
	{
		?> <textarea id="<?php echo $args['label_for']; ?>" placeholder="%post_title% %post_content% %post_url% %hashtags%" name="wpar_plugin_settings[facebook_template]" rows="3" cols="100" style="width:90%;"><?php echo $this->get_data( 'facebook_template', '%post_title% %post_content% %post_url% %hashtags%' ); ?></textarea>
		<br><?php printf(
			'<small style="line-height: 2;"><i>%1$s </i><code>&#37;post_title&#37;</code> <code>&#37;post_content&#37;</code> <code>&#37;post_url&#37;</code> <code>&#37;hashtags&#37;</code>. <i>%2$s</i> <code>63206</code></small>',
			__( 'Use these tags:', 'wp-auto-republish' ), __( 'Charecter limit:', 'wp-auto-republish' )
		);
	}

	public function fb_post_types_list_display__premium_only( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[facebook_post_types_display][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'facebook_post_types_display', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types of which you want to share on Facebook as post.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function fb_social_taxonomy__premium_only( $args )
	{
		echo '<select id="' . $args['label_for'] . '" class="wpar-taxonomies" name="wpar_plugin_settings[facebook_social_taxonomy][]" multiple="multiple" style="width:90%;">';
		$post_type_categories = $this->get_all_taxonomies( [ 'public' => true ], false, false );
        if ( ! empty( $post_type_categories ) ) {
			foreach( $post_type_categories as $post_type => $post_data ) {
				echo '<optgroup label="'.$post_data['label'].'">';
				if ( isset( $post_data['categories'] ) && ! empty( $post_data['categories'] ) && is_array( $post_data['categories'] ) ) {
					foreach( $post_data['categories'] as $cat_slug => $cat_name ) {
						$selected = in_array( $cat_slug, $this->get_data( 'facebook_social_taxonomy', [] ) ) ? ' selected="selected"' : '';
				        echo '<option value="' . $cat_slug . '" ' . $selected . '>' . $cat_name . '</option>';
					}
				}
				echo '</optgroup>';
			}
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select taxonomies of which you want to post them on Facebook as hashtags.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php 
	}

	public function tw_social_enable__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[twitter_enable]" value="1" <?php checked( $this->get_data( 'twitter_enable' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to enable auto tweet publish to Twitter upon post republishing.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function tw_social_thumbnail__premium_only( $args )
	{
		$items = [
			'yes' => __( 'Show Thumbnail', 'wp-auto-republish' ),
			'no'  => __( 'Don\'t Show Thumbnail', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[twitter_thumbnail]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'twitter_thumbnail', 'yes' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable or Disable Tweet Post Thumbnail form here.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function tw_social_content_source__premium_only( $args )
	{
		$items = [
			'post_content'  => __( 'Post Content', 'wp-auto-republish' ),
			'post_excerpt'  => __( 'Post Excerpt', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[twitter_content_source]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'twitter_content_source', 'post_content' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select Twitter tweet template content source from here. %post_content% tag will be replaced by this in the below field.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function tw_social_template__premium_only( $args )
	{
		?> <textarea id="<?php echo $args['label_for']; ?>" placeholder="%post_title% %post_content% %post_url% %hashtags%" name="wpar_plugin_settings[twitter_template]" rows="3" cols="100" style="width:90%;"><?php echo $this->get_data( 'twitter_template', '%post_title% %post_content% %post_url% %hashtags%' ); ?></textarea>
		<br><?php printf(
			'<small style="line-height: 2;"><i>%1$s </i><code>&#37;post_title&#37;</code> <code>&#37;post_content&#37;</code> <code>&#37;post_url&#37;</code> <code>&#37;hashtags&#37;</code>. <i>%2$s</i> <code>280</code></small>',
			__( 'Use these tags:', 'wp-auto-republish' ), __( 'Charecter limit:', 'wp-auto-republish' )
		);
	}
	
	public function tw_post_types_list_display__premium_only( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[twitter_post_types_display][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'twitter_post_types_display', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types of which you want to share on Twitter as tweet.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function tw_social_taxonomy__premium_only( $args )
	{
		echo '<select id="' . $args['label_for'] . '" class="wpar-taxonomies" name="wpar_plugin_settings[twitter_social_taxonomy][]" multiple="multiple" style="width:90%;">';
		$post_type_categories = $this->get_all_taxonomies( [ 'public' => true ], false, false );
        if ( ! empty( $post_type_categories ) ) {
			foreach( $post_type_categories as $post_type => $post_data ) {
				echo '<optgroup label="'.$post_data['label'].'">';
				if ( isset( $post_data['categories'] ) && ! empty( $post_data['categories'] ) && is_array( $post_data['categories'] ) ) {
					foreach( $post_data['categories'] as $cat_slug => $cat_name ) {
						$selected = in_array( $cat_slug, $this->get_data( 'twitter_social_taxonomy', [] ) ) ? ' selected="selected"' : '';
				        echo '<option value="' . $cat_slug . '" ' . $selected . '>' . $cat_name . '</option>';
					}
				}
				echo '</optgroup>';
			}
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select taxonomies of which you want to post them on Twitter as hashtags.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php 
	}

	public function ld_social_enable__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[linkedin_enable]" value="1" <?php checked( $this->get_data( 'linkedin_enable' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to enable auto post publish to Linkedin upon post republishing.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function ld_social_post_as__premium_only( $args )
	{
		$items = [
			'status'  => __( 'Post as Status', 'wp-auto-republish' ),
			'link_status'  => __( 'Post as Status & Link', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[linkedin_post_as]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'linkedin_post_as', 'link_status' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select Linkedin post template type from here.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function ld_social_content_source__premium_only( $args )
	{
		$items = [
			'post_content'  => __( 'Post Content', 'wp-auto-republish' ),
			'post_excerpt'  => __( 'Post Excerpt', 'wp-auto-republish' )
		];
		echo '<select id="' . $args['label_for'] . '" name="wpar_plugin_settings[linkedin_content_source]" style="width:40%;">';
		foreach( $items as $item => $label ) {
			$selected = ( $this->get_data( 'linkedin_content_source', 'post_content' ) == $item ) ? ' selected="selected"' : '';
			echo '<option value="' . $item . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select Linkedin post template content source from here. %post_content% will be replaced by this in the below field.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function ld_social_template__premium_only( $args )
	{
		?> <textarea id="<?php echo $args['label_for']; ?>" placeholder="%post_title% %post_content% %post_url% %hashtags%" name="wpar_plugin_settings[linkedin_template]" rows="3" cols="100" style="width:90%;"><?php echo $this->get_data( 'linkedin_template', '%post_title% %post_content% %post_url% %hashtags%' ); ?></textarea>
		<br><?php printf(
			'<small style="line-height: 2;"><i>%1$s </i><code>&#37;post_title&#37;</code> <code>&#37;post_content&#37;</code> <code>&#37;post_url&#37;</code> <code>&#37;hashtags&#37;</code>. <i>%2$s</i> <code>1300</code></small>',
			__( 'Use these tags:', 'wp-auto-republish' ), __( 'Charecter limit:', 'wp-auto-republish' )
		);
	}

	public function ld_post_types_list_display__premium_only( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[linkedin_post_types_display][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'linkedin_post_types_display', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types of which you want to share on Linkedin as post.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function ld_social_taxonomy__premium_only( $args )
	{
		echo '<select id="' . $args['label_for'] . '" class="wpar-taxonomies" name="wpar_plugin_settings[linkedin_social_taxonomy][]" multiple="multiple" style="width:90%;">';
		$post_type_categories = $this->get_all_taxonomies( [ 'public' => true ], false, false );
        if ( ! empty( $post_type_categories ) ) {
			foreach( $post_type_categories as $post_type => $post_data ) {
				echo '<optgroup label="'.$post_data['label'].'">';
				if ( isset( $post_data['categories'] ) && ! empty( $post_data['categories'] ) && is_array( $post_data['categories'] ) ) {
					foreach( $post_data['categories'] as $cat_slug => $cat_name ) {
						$selected = in_array( $cat_slug, $this->get_data( 'linkedin_social_taxonomy', [] ) ) ? ' selected="selected"' : '';
				        echo '<option value="' . $cat_slug . '" ' . $selected . '>' . $cat_name . '</option>';
					}
				}
				echo '</optgroup>';
			}
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select taxonomies of which you want to post them on Linkedin as hashtags.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php 
	}

	public function enable_email_notify__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[enable_email_notify]" value="1" <?php checked( $this->get_data( 'enable_email_notify' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to get republish info as an email notification.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function enable_post_author_email__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[enable_post_author_email]" value="1" <?php checked( $this->get_data( 'enable_post_author_email' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to auto send email to the original post author.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}

	public function email_recipients__premium_only( $args )
	{
		?><input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[email_recipients]" type="text" size="100" style="width:100%;" required placeholder="admin@yoursite.com" value="<?php echo $this->get_data( 'email_recipients', get_bloginfo( 'admin_email' ) ); ?>" />
		<?php
	}

	public function email_post_types__premium_only( $args )
	{
		$post_types = $this->get_post_types();
		echo '<select id="' . $args['label_for'] . '" class="wpar-post-types" name="wpar_plugin_settings[email_post_types][]" multiple="multiple" required style="width:90%;">';
		foreach( $post_types as $post_type => $label ) {
            $selected = in_array( $post_type, $this->get_data( 'email_post_types', [ 'post' ] ) ) ? ' selected="selected"' : '';
			echo '<option value="' . $post_type . '"' . $selected . '>' . $label . '</option>';
		}
		echo '</select>';
		?>
		&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Select post types for which you want to enable email notification.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
		<?php
	}
	
	public function email_subject__premium_only( $args )
	{
		$subject = $this->get_data( 'email_subject', sprintf( __( '%1$s A %2$s - %3$s has been republished on your blog.', 'wp-auto-republish' ), '[%site_name%]', '%post_type%', '%post_title%' ) ); ?>
		<input id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[email_subject]" type="text" size="100" style="width:100%;" required value="<?php echo htmlspecialchars( $subject ); ?>" />
		<?php printf(
			'<small style="line-height: 2;"><i>%s</i><code>&#37;post_title&#37;</code> <code>&#37;author_name&#37;</code> <code>&#37;post_type&#37;</code> <code>&#37;site_name&#37;</code> <code>&#37;site_url&#37;</code> <code>&#37;republish_time&#37;</code></small>',
			__( 'Use these tags into email subject - ', 'wp-auto-republish' )
		);
	}
	
	public function email_message__premium_only( $args )
	{
		$body = $this->get_data( 'email_message', 'A %post_type% is republished of your blog by %author_name%' . "\n\n" . '<p><strong>Post: %post_title%</strong></p><p><strong>Post: %post_title%</strong></p><p><strong>Republished Time: %republish_time%</strong></p><p><strong>Original Time: %post_time%</strong></p>' );
		$emailBody = html_entity_decode( $body, ENT_COMPAT, "UTF-8" );
	    $args = array(
			'textarea_name'   => 'wpar_plugin_settings[email_message]',
			'textarea_rows'   => '8',
			'teeny'           => true,
			'tinymce'         => false,
			'media_buttons'   => false,
		);
		wp_editor( $emailBody, 'wpar_email_message', $args );
		printf(
			'<small style="line-height: 2;"><i>%1$s</i><code>&#37;post_title&#37;</code> <code>&#37;author_name&#37;</code> <code>&#37;post_type&#37;</code> <code>&#37;post_time&#37;</code> <code>&#37;republish_time&#37;</code> <code>&#37;admin_email&#37;</code> <code>&#37;site_name&#37;</code> <code>&#37;site_url&#37;</code><i>. %2$s</i></small>',
			__( 'Use these tags into email body - ', 'wp-auto-republish' ), __( 'Email body supports HTML.', 'wp-auto-republish' )
		);
	}

	public function enable_silent_republishing__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_enable_silent_republishing]" value="1" <?php checked( $this->get_data( 'wpar_enable_silent_republishing' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you do not want to trigger actual WordPress post publish event. It may stop any social media share or other actions which occur every time when a new post is actually published.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
	   <?php
	}

	public function disable_log__premium_only( $args )
	{
		?>  <label class="switch">
			<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_disable_log]" value="1" <?php checked( $this->get_data( 'wpar_disable_log' ), 1 ); ?> /> 
			<span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you do not want to use republish log history.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
	   <?php
	}

	public function remove_plugin_data( $args )
	{
        ?>  <label class="switch">
            <input type="checkbox" id="<?php echo $args['label_for']; ?>" name="wpar_plugin_settings[wpar_remove_plugin_data]" value="1" <?php checked( $this->get_data( 'wpar_remove_plugin_data' ), 1 ); ?> /> 
            <span class="slider round"></span></label>&nbsp;&nbsp;<span class="tooltip" title="<?php _e( 'Enable this if you want to remove all the plugin data from your website.', 'wp-auto-republish' ); ?>"><span title="" class="dashicons dashicons-editor-help"></span></span>
        <?php
	}
}