<?php
/**
 * Dashboard widget.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;
use Wpar\Helpers\HelperFunctions;

defined( 'ABSPATH' ) || exit;

/**
 * Dashboard widget class.
 */
class DashboardWidget
{
	use HelperFunctions, Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wp_dashboard_setup', 'dashboard_widget' );
		$this->action( 'admin_head-index.php', 'widget_css' );
	}

	/**
	 * Register dashboard widgets.
	 */
	public function dashboard_widget()
	{
		global $wp_meta_boxes;
		
		wp_add_dashboard_widget(
			'dashboard_republish_posts', // Widget slug.
			__( 'Republish Activity', 'wp-auto-republish' ), // Title.
			[ $this, 'widget_callback' ], // callback.
			[ $this, 'widget_control_callback' ] // control callback.
	    );
	}

	/**
	 * Dashboard widget callback.
	 * 
	 * @param string $widget_id Widget ID
	 */
	public function widget_callback( $widget_id )
	{
        // get widget options
        $widget_options = get_option( 'wpar_dashboard_widget_options' );
        $post_count = ! empty( $widget_options['number'] ) ? esc_attr( $widget_options['number'] ) : 5;
		$global_posts = $single_posts = false;
		
		echo '<div id="activity-widget">';

		if ( $this->is_enabled( 'enable_plugin', true ) ) {
		    $global_posts = $this->activity_block( [
		    	'type' => 'global',
		    	'block_title' => __( 'Global Republish Scheduled', 'wp-auto-republish' ),
		    	'query_args' => [
		    		'post_type'      => 'any',
		    		'post_status'    => 'publish',
		    		'posts_per_page' => $post_count,
		    		'order'          => 'ASC',
		    		'orderby'        => 'meta_value',
		    		'meta_key'       => '_wpar_global_republish_datetime',
		    		'meta_query' => [
		    			'relation' => 'AND',
		    			[
		    				'key'     => '_wpar_global_republish_datetime',
		    				'compare' => 'EXISTS',
		    			],
		    			[
		    				'key'     => 'wpar_global_republish_status',
		    				'compare' => 'EXISTS',
		    			]
		    		],
		    	]
		    ] );
		}
    
		if ( $this->is_enabled( 'enable_single_republishing', true ) ) {
		    $single_posts = $this->activity_block( [
		    	'type' => 'single',
		    	'block_title' => __( 'Single Republish Scheduled', 'wp-auto-republish' ),
		    	'query_args' => [
		    		'post_type'      => 'any',
		    		'post_status'    => 'publish',
		    		'posts_per_page' => $post_count,
		    		'order'          => 'ASC',
		    		'orderby'        => 'meta_value',
		    		'meta_key'       => '_wpar_repost_schedule_datetime',
		    		'meta_query' => [
		    			'relation' => 'AND',
		    			[
		    				'key'     => '_wpar_repost_schedule_datetime',
		    				'compare' => 'EXISTS',
		    			],
		    			[
		    				'key'     => 'wpar_single_republish_status',
		    				'compare' => 'EXISTS',
		    			]
		    		],
		    	]
		    ] );
		}

		if ( ! $global_posts && ! $single_posts ) {
			echo '<div class="no-activity">';
			echo '<p>' . __( 'No activity yet!' ) . '</p>';
			echo '</div>';
		}

		echo '</div>';
	}
	
	/**
	 * Dashboard widget control callback.
	 */
	private function activity_block( $args )
	{
		$posts = new \WP_Query( $this->do_filter( 'dashboard_widget_' . $args['type'] . '_args', $args['query_args'] ) );

	    if ( $posts->have_posts() ) { 

			echo '<div id="wpar-pending-' . $args['type'] . '-posts" class="activity-block wpar-pending-posts">';
			echo '<h3>' . $args['block_title'] . '</h3>';
			echo '<ul>';

			$today    = date( 'Y-m-d', current_time( 'timestamp' ) );
		    $tomorrow = date( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );

            while ( $posts->have_posts() ) {
				$posts->the_post();
	
				$datetime = $this->get_meta( get_the_ID(), $args['query_args']['meta_key'] );
				$time = strtotime( $datetime );
			    if ( date( 'Y-m-d', $time ) == $today ) {
			    	$relative = __( 'Today' );
			    } elseif ( date( 'Y-m-d', $time ) == $tomorrow ) {
			    	$relative = __( 'Tomorrow' );
			    } elseif ( date( 'Y', $time ) !== date( 'Y', current_time( 'timestamp' ) ) ) {
			    	/* translators: Date and time format for recent posts on the dashboard, from a different calendar year, see https://www.php.net/date */
			    	$relative = date_i18n( __( 'M jS Y' ), strtotime( $datetime ) );
			    } else {
			    	/* translators: Date and time format for recent posts on the dashboard, see https://www.php.net/date */
			    	$relative = date_i18n( __( 'M jS' ), strtotime( $datetime ) );
			    }
				
				// Use the post edit link for those who can edit, the permalink otherwise.
			    $recent_post_link = current_user_can( 'edit_post', get_the_ID() ) ? get_edit_post_link() : get_permalink();
    
			    $draft_or_post_title = _draft_or_post_title();
			    printf(
			    	'<li><span>%1$s</span> <a href="%2$s" aria-label="%3$s">%4$s</a></li>',
			    	/* translators: 1: Relative date, 2: Time. */
			    	sprintf( _x( '%1$s, %2$s', 'dashboard' ), $relative, date_i18n( get_option( 'time_format' ), strtotime( $datetime ) ) ),
			    	$recent_post_link,
			    	/* translators: %s: Post title. */
			    	esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $draft_or_post_title ) ),
					$draft_or_post_title
				);
			}
						
		    echo '</ul>';
			echo '</div>';
				
		} else {
			return false;
		}

		wp_reset_postdata();

	    return true;
	}

	/**
	 * Dashboard widget control callback.
	 */
	public function widget_control_callback()
	{
		// Get widget options
		$widget_options = get_option( 'wpar_dashboard_widget_options' );
		$value = isset( $widget_options['number'] ) ? esc_attr( $widget_options['number'] ) : '';
		// Update widget options
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['wpar_widget_post'] ) ) {
			update_option( 'wpar_dashboard_widget_options', $_POST['wpar_widget_value'] );
		} ?>
		<p>
			<label for="post-count"><strong><?php _e( 'No. of Posts to Display on this Widget', 'wp-auto-republish' ); ?>:</strong></label>
			&nbsp;&nbsp;&nbsp;<input class="widefat" id="post-count" name="wpar_widget_value[number]" type="number" size="15" style="width:15%;vertical-align: middle;" placeholder="5" min="3" value="<?php echo $value; ?>" /><input name="wpar_widget_post" type="hidden" value="1" />
		</p>
		<?php
	}

	/**
	 * Locad custom css for dashboard widget.
	 */
	public function widget_css()
	{ ?>
		<style type="text/css">
			#dashboard_republish_posts .no-activity p {
				color: #72777c;
				font-size: 16px;
			}
			#dashboard_republish_posts .no-activity {
				overflow: hidden;
				padding: 12px 0;
				text-align: center;
			}
			#dashboard_republish_posts .inside {
				margin: 0;
				padding-bottom: 0;
			}
			#dashboard_republish_posts .dashboard-widget-control-form {
				padding-top: 10px;
				padding-bottom: 15px;
			}
			.wpar-pending-posts ul span {
                display: inline-block;
                margin-right: 5px;
                min-width: 150px;
                color: #72777c;
            }
			.wpar-open-link {
				font-size: 15px;
				margin-left: 0px;
				min-width: 0px !important;
				display: inline !important;
			}
			#wpar-pending-global-posts li, #wpar-single-global-posts li {
                margin-bottom: 8px;
            }
			#wpar-pending-single-posts ul {
                clear: both;
                margin-bottom: 0;
            }
		</style>
	<?php }
}