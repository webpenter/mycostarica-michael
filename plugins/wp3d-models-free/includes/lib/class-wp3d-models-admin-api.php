<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WP3D_Models_Admin_API {

	/**
	 * Constructor function
	 */
	public function __construct () {
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 1 );
		
		add_action('admin_init', array( $this, 'wp3d_activate_license'), 10, 1 );
		
		add_action('admin_init', array( $this, 'wp3d_deactivate_license'), 10, 1 );

        //show expire notice
        add_action( 'admin_notices', array( $this, 'expire_notice' ));
	}
	
	
	
/************************************
* activating the license key
*************************************/

/**
 * EDD Activation settings
 * @since   1.0.7
 */
 
public function wp3d_activate_license() {
    if(!session_id()) {
        session_start();
    }
	// listen for our activate button to be clicked
	if( isset( $_POST['wp3d_license_activate'] ) ) {
        $license_key = trim( $_POST['wp3d_license_key'] );
	    $license_data = $this->getLicenseData($license_key, 'activate_license');

		if (empty($license_data)) {
		    return false;
        }
		
		// $license_data->license will be either "valid" or "invalid"
		update_option( 'wp3d_license_status', $license_data->license );
	}
}
	
/************************************
* de-activating the license key
*************************************/

/**
 * EDD Deactivation settings
 * @since   1.0.7
 */

function wp3d_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['wp3d_license_deactivate'] ) ) {

		// run a quick security check 
	 	if( ! check_admin_referer( 'wp3d_sample_nonce', 'wp3d_sample_nonce' ) ) 	
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'wp3d_license_key' ) );
			

		// data to send in our API request
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( WP3D_MODELS_PLUGIN_NAME ), // the name of our product in EDD
			'url'       => home_url()
		);
		
		// Call the custom API.
		$response = wp_remote_post( WP3D_MODELS_PLUGIN_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) )
			return false;

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' )
			delete_option( 'wp3d_license_status' );

	}
}


	/**
	 * Generate HTML for displaying fields
	 * @param  array   $field Field data
	 * @param  boolean $echo  Whether to echo the field HTML or return it
	 * @return void
	 */
	public function display_field ( $data = array(), $post = false, $echo = true ) {
		
		// get license info
		$license 	= trim( get_option( 'wp3d_license_key' ) );
		$status 	= get_option( 'wp3d_license_status' );		

		// Get field info
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}

		// Check for prefix on option name
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data
		$data = '';
		if ( $post ) {

			// Get saved field data
			$option_name .= $field['id'];
			$option = get_post_meta( $post->ID, $field['id'], true );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		} else {

			// Get saved option
			$option_name .= $field['id'];
			$option = get_option( $option_name );

			// Get data to display in field
			if ( isset( $option ) ) {
				$data = $option;
			}

		}

		// Show default data if no option saved and default is supplied
		if ( $data === false && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( $data === false ) {
			$data = '';
		}

		$html = '';

		switch( $field['type'] ) {

			case 'text':
			case 'url':
			case 'email':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '" />' . "\n";
			break;

			case 'password':
			case 'number':
			case 'hidden':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . esc_attr( $data ) . '"' . $min . '' . $max . '/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="" />' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if ( $data && 'on' == $data ) {
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '" class="checkbox_multi"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if ( $data ) {
					//$image_thumb = wp_get_attachment_thumb_url( $data );
					$image_thumb_arr = wp_get_attachment_image_src( $data, 'medium');
					$image_thumb = $image_thumb_arr[0];
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'wp3d-models' ) . '" data-uploader_button_text="' . __( 'Use image' , 'wp3d-models' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'wp3d-models' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'wp3d-models' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="wp3d-color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="wp3d-color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="wp3d-colorpicker"></div>
			    </div>
			    <?php
			break;
			
			case 'license-text':
				?>
				<input id="<?php esc_attr_e( $field['id'] ); ?>" type="text" name="<?php esc_attr_e( $option_name ); ?>" placeholder="<?php esc_attr_e( $field['placeholder'] ); ?>" value="<?php esc_attr_e( $license ); ?>" />

				<?php 
				if( $status !== false && $status == 'valid' ) { ?>
				  <?php wp_nonce_field( 'wp3d_sample_nonce', 'wp3d_sample_nonce' ); ?>
				  <input type="submit" class="button-secondary" name="wp3d_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'wp3d-models' ) ?>"/>
				  <span class="license-status license-active"> <?php _e('Active', 'wp3d-models'); ?></span>
				  <?php } else { ?>
				  <?php wp_nonce_field( 'wp3d_sample_nonce', 'wp3d_sample_nonce' ); ?>
				  <input type="submit" class="button-secondary" name="wp3d_license_activate" value="<?php esc_attr_e( 'Activate License', 'wp3d-models' ) ?>"/>
				  <span class="license-status license-inactive"> <?php _e('Not Active', 'wp3d-models'); ?></span>
				  <?php } ?>

			    <?php
			break;
			
			case 'message':
				// nothing to see here, just uses the description field
			break;
		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . "\n";
				}

				$html .= '<span class="description">' . $field['description'] . '</span>' . "\n";

				if ( ! $post ) {
					$html .= '</label>' . "\n";
				}
			break;
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html;

	}

	/**
	 * Validate form field
	 * @param  string $data Submitted value
	 * @param  string $type Type of field to validate
	 * @return string       Validated value
	 */
	public function validate_field ( $data = '', $type = 'text' ) {

		switch( $type ) {
			case 'text': $data = esc_attr( $data ); break;
			case 'url': $data = esc_url( $data ); break;
			case 'email': $data = is_email( $data ); break;
		}

		return $data;
	}

	/**
	 * Add meta box to the dashboard
	 * @param string $id            Unique ID for metabox
	 * @param string $title         Display title of metabox
	 * @param array  $post_types    Post types to which this metabox applies
	 * @param string $context       Context in which to display this metabox ('advanced' or 'side')
	 * @param string $priority      Priority of this metabox ('default', 'low' or 'high')
	 * @param array  $callback_args Any axtra arguments that will be passed to the display function for this metabox
	 * @return void
	 */
	public function add_meta_box ( $id = '', $title = '', $post_types = array(), $context = 'advanced', $priority = 'default', $callback_args = null ) {

		// Get post type(s)
		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		// Generate each metabox
		foreach ( $post_types as $post_type ) {
			add_meta_box( $id, $title, array( $this, 'meta_box_content' ), $post_type, $context, $priority, $callback_args );
		}
	}

	/**
	 * Display metabox content
	 * @param  object $post Post object
	 * @param  array  $args Arguments unique to this metabox
	 * @return void
	 */
	public function meta_box_content ( $post, $args ) {

		$fields = apply_filters( get_post_type() . '_custom_fields', array(), get_post_type() );

		if ( ! is_array( $fields ) || 0 == count( $fields ) ) return;

		echo '<div class="custom-field-panel">' . "\n";

		foreach ( $fields as $field ) {

			if ( ! isset( $field['metabox'] ) ) continue;

			if ( ! is_array( $field['metabox'] ) ) {
				$field['metabox'] = array( $field['metabox'] );
			}

			if ( in_array( $args['id'], $field['metabox'] ) ) {
				$this->display_meta_box_field( $field, $post );
			}

		}

		echo '</div>' . "\n";

	}

	/**
	 * Dispay field in metabox
	 * @param  array  $field Field data
	 * @param  object $post  Post object
	 * @return void
	 */
	public function display_meta_box_field ( $field = array(), $post ) {

		if ( ! is_array( $field ) || 0 == count( $field ) ) return;

		$field = '<p class="form-field"><label for="' . $field['id'] . '">' . $field['label'] . '</label>' . $this->display_field( $field, $post, false ) . '</p>' . "\n";

		echo $field;
	}

	/**
	 * Save metabox fields
	 * @param  integer $post_id Post ID
	 * @return void
	 */
	public function save_meta_boxes ( $post_id = 0 ) {

		if ( ! $post_id ) return;

		$post_type = get_post_type( $post_id );

		$fields = apply_filters( $post_type . '_custom_fields', array(), $post_type );

		if ( ! is_array( $fields ) || 0 == count( $fields ) ) return;

		foreach ( $fields as $field ) {
			if ( isset( $_REQUEST[ $field['id'] ] ) ) {
				update_post_meta( $post_id, $field['id'], $this->validate_field( $_REQUEST[ $field['id'] ], $field['type'] ) );
			} else {
				update_post_meta( $post_id, $field['id'], '' );
			}
		}
	}

    /**
     * Make EDD request
     *
     * @param $license_key
     * @param $action
     * @return false|mixed
     */
	function getLicenseData($license_key, $action) {
        $item_name = WP3D_MODELS_PLUGIN_NAME;
        
        $api_params = array(
            'edd_action'=> $action,
            'license' 	=> $license_key,
            'item_name' => urlencode( $item_name ), // the name of our product in EDD
            'url'       => home_url()
        );

        // Call the custom API.
        $response = wp_remote_post( WP3D_MODELS_PLUGIN_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        // make sure the response came back okay
        if (is_wp_error($response)) {
            return false;
        }

        return json_decode(wp_remote_retrieve_body($response));
    }

    /**
     * Get renew link based on license type
     *
     * @return string
     */
    public function getRenewLink() {
        $item_id = WP3D_MODELS_FREE_LICENSE_ID;
        $license_key = trim(get_option('wp3d_license_key'));

        return WP3D_MODELS_PLUGIN_URL . '/checkout/?edd_license_key=' . $license_key . '&download_id=' . $item_id;
    }

    /**
     * Show notice for expired or invalid licenses
     */
    public function expire_notice() {
        global $wpdb, $pagenow;
        $post_type = !empty($_GET['post_type']) ? $_GET['post_type'] : '';

        if (empty($post_type)) {
            $post_type = get_post_type();
        }

        //show notice only on the plugin pages
        if (in_array($post_type, ['model', 'wp3d_agent'])) {
            if ($post_type == 'wp3d_agent') {
                echo '<h1 class="wp-heading-inline">Agents / Scheduling</h1>
                    <div class="error notice">
                        <p>This functionality is limited to the Full Version (Click <a target="_blank" href="https://wp3dmodels.com/buy-now/?discount=' . WP3D_MODELS_DISCOUNT_CODE . '">here</a> to upgrade).  This enables unlimited models, gallery pages, as well as agents & scheduling (Agents can be assigned to one, several, or all models as needed).</p>
                        <a target="_blank" href="https://wp3dmodels.com/buy-now/?discount=' . WP3D_MODELS_DISCOUNT_CODE . '" class="wp3d_upgrade_button">Upgrade to pro</a>
                       </div>';
                die;
            } else {
                $models_count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->posts} WHERE post_type = 'model' AND post_status = 'publish'");
                if ($models_count) {
                    echo '<div class="wp3d_pro_modal">
                        <div class="body">
                            <div class="close">×</div>
                            <div class="lock"><i class="dashicons dashicons-lock"></i></div>
                            <div class="title">This is a PRO Feature</div>
                             <div class="text">You can create unlimited models with the <a target="_blank" href="https://wp3dmodels.com/buy-now/?discount=' . WP3D_MODELS_DISCOUNT_CODE . '">pro version</a> of WP3D Models. Also included in the pro version: Gallery Pages, Mapping (Google API), Agents, Scheduling, and organization by Type & Client.</div>
                             <a target="_blank" href="https://wp3dmodels.com/buy-now/?discount=' . WP3D_MODELS_DISCOUNT_CODE . '" class="wp3d_upgrade_button">Upgrade to pro 10% off</a>
                             <div class="bonus"><strong>Bonus:</strong> You will get <span>10% off</span> regular price, automatically applied at checkout.</div>
                        </div>
                    </div>';

                    if ($pagenow == 'post-new.php') {
                        echo '<script>
                            jQuery(".wp3d_pro_modal .close").addClass("back");
                            jQuery(".wp3d_pro_modal").show();
                        </script>';
                        die;
                    }
                }
            }

            if ($pagenow == 'edit.php' && !isset($_GET['page'])) {
                echo '<div class="error notice">
                <p>This is where you can manage all models when you have the full version of WP3D Models, this plugin is limited to a single model. Upgrade now for unlimited models, with the ability to organize gallery pages, as well as include agent information and scheduling functionality.</p>
                <a target="_blank" href="https://wp3dmodels.com/buy-now/?discount=' . WP3D_MODELS_DISCOUNT_CODE . '" class="wp3d_upgrade_button">Upgrade to pro</a>
               </div>';    
            } else {
                if (empty($_SESSION['wp3d_disable_notice']) || $_SESSION['wp3d_disable_notice'] !== true) {
                    echo '<div class="error notice is-dismissible" id="wp3d_free_notice">
                        <p>Get Unlimited Models, Gallery Pages, Categorization, Agents and Scheduling with the pro version of WP3D Models.</p>
                        <a target="_blank" href="https://wp3dmodels.com/buy-now/?discount=' . WP3D_MODELS_DISCOUNT_CODE . '" class="wp3d_upgrade_button">Buy Now 10% OFF</a>
                       </div>';
                }
            }
        }
    }

    /**
     * Get and renew license expiry date
     *
     * @return int
     */
    public function getLicenseExpireDate() {
        $check_date = get_option('wp3d_license_expire_check');
        $expire_date = get_option('wp3d_license_expire_date');

        //renew license expiry date
        if (empty($expire_date) || time() - $check_date > 60 * 60 * 24) {
            update_option('wp3d_license_expire_check', time());
            $license_key = trim(get_option('wp3d_license_key'));
            if (!empty($license_key)) {
                $license_data = $this->getLicenseData($license_key, 'check_license');
                if (!empty($license_data->expires)) {
                    $expire_date = strtotime($license_data->expires);
                    update_option('wp3d_license_expire_date', $expire_date);
                }
            }
        }

        return (int)$expire_date;
    }

}
