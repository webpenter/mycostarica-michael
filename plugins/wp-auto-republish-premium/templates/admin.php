<?php
/**
 * The Main dashboard file.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Templates
 * @author     Sayan Datta <hello@sayandatta.in>
 */
?>

<div class="wrap">
    <div class="head-wrap">
        <h1 class="title"><?php echo $this->name; ?><span class="title-count"><?php echo $this->version; ?></span></h1>
        <div><?php _e( 'This plugin helps to revive old posts by resetting the publish date to the current date.', 'wp-auto-republish' ); ?></div>
        <div class="top-sharebar">
            <a class="share-btn rate-btn" href="https://wordpress.org/support/plugin/wp-auto-republish/reviews/#new-post" target="_blank" title="Please rate 5 stars if you like <?php echo $this->name; ?>"><span class="dashicons dashicons-star-filled"></span> Rate 5 stars</a>
            <a class="share-btn twitter" href="https://twitter.com/intent/tweet?text=Check%20out%20WP%20Auto%20Republish,%20a%20%23WordPress%20%23plugin%20that%20revive%20your%20old%20posts%20by%20resetting%20the%20published%20date%20to%20the%20current%20date%20https%3A//wordpress.org/plugins/wp-auto-republish/%20via%20%40im_sayaan" target="_blank"><span class="dashicons dashicons-twitter"></span> <?php _e( 'Share on Twitter', 'wp-auto-republish' ); ?></a>
            <a class="share-btn facebook" href="https://www.facebook.com/sharer/sharer.php?u=https://wordpress.org/plugins/wp-auto-republish/" target="_blank"><span class="dashicons dashicons-facebook"></span> <?php _e( 'Share on Facebook', 'wp-auto-republish' ); ?></a>
        </div>
    </div>
    <div id="nav-container" class="nav-tab-wrapper" style="border-bottggom: none;">
        <a href="#general" class="nav-tab nav-tab-active" id="general"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'General', 'wp-auto-republish' ); ?></a>
        <a href="#post" class="nav-tab" id="post"><span class="dashicons dashicons-admin-post"></span> <?php _e( 'Post Options', 'wp-auto-republish' ); ?></a>
        <?php if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
            <a href="#single" class="nav-tab" id="single"><span class="dashicons dashicons-tag"></span> <?php _e( 'Single Post', 'wp-auto-republish' ); ?></a>
            <a href="#social" class="nav-tab" id="social"><span class="dashicons dashicons-share"></span> <?php _e( 'Social Share', 'wp-auto-republish' ); ?></a>
            <a href="#email" class="nav-tab" id="email"><span class="dashicons dashicons-email-alt"></span> <?php _e( 'Notification', 'wp-auto-republish' ); ?></a>
            <a href="#logs" class="nav-tab" id="logs"<?php echo ( isset( $options['wpar_disable_log'] ) && $options['wpar_disable_log'] == 1 ) ? ' style="display: none;"' : ''; ?>><span class="dashicons dashicons-edit-large"></span> <?php _e( 'Log', 'wp-auto-republish' ); ?></a>
        <?php } ?>
        <a href="#tools" class="nav-tab" id="tools"><span class="dashicons dashicons-admin-tools"></span> <?php _e( 'Tools', 'wp-auto-republish' ); ?></a>
        <a href="#help" class="nav-tab" id="help"><span class="dashicons dashicons-editor-help"></span> <?php _e( 'Help', 'wp-auto-republish' ); ?></a>
        <?php if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { 
            if ( wpar_load_fs_sdk()->is_not_paying() && ! wpar_load_fs_sdk()->is_trial() ) {
				if ( ! wpar_load_fs_sdk()->is_trial_utilized() ) { ?>
                    <a href="<?php echo wpar_load_fs_sdk()->get_trial_url(); ?>" target="_blank" class="nav-tab type-link" id="trial"><span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Free Premium Trial', 'wp-auto-republish' ); ?></a>
                <?php }
            } ?>
            <a href="<?php echo wpar_load_fs_sdk()->get_upgrade_url(); ?>" target="_blank" class="nav-tab type-link" id="upgrade"><span class="dashicons dashicons-arrow-up-alt"></span> <?php _e( 'Upgrade', 'wp-auto-republish' ); ?></a>
        <?php } ?>
        <a href="https://wpautorepublish.com/docs/" target="_blank" class="nav-tab type-link" id="docs"><span class="dashicons dashicons-book-alt"></span> <?php _e( 'Documentation', 'wp-auto-republish' ); ?></a>
    </div>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content" class="wpar-metabox">
                <form id="wpar-settings-form" method="post" action="options.php" stylce="padding-left: 8px;">
                <div class="sub-links wpar-social" style="display: none;">
                    <a href="#" class="sub-link sub-link-facebook" data-type="facebook"><?php _e( 'Facebook Settings', 'wp-auto-republish' ); ?></a> | 
                    <a href="#" class="sub-link sub-link-twitter dark-text" data-type="twitter"><?php _e( 'Twitter Settings', 'wp-auto-republish' ); ?></a> | 
                    <a href="#" class="sub-link sub-link-linkedin dark-text" data-type="linkedin"><?php _e( 'Linkedin Settings', 'wp-auto-republish' ); ?></a>
                </div>
                <?php settings_fields( 'wpar_plugin_settings_fields' ); ?>
                    <div id="wpar-configure" class="postbox wpar-general">
                        <h3 class="hndle" style="cursor:default;">
                            <span class="wpar-heading">
                                <?php _e( 'Configure Settings', 'wp-auto-republish' ); ?>
                            </span>
                            <?php if ( $last !== false ) { ?>
                                <span class="wpar-last-run">
                                    <?php printf( __( 'Last Run: %s', 'wp-auto-republish' ), date_i18n( $format, $last ) ); ?>
                                </span>
                            <?php } ?>
                        </h3>
                        <div class="inside">
                            <?php do_settings_sections( 'wpar_plugin_default_option' ); ?>
                            <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                        </div>
                    </div>
                    <div id="wpar-display" class="postbox wpar-general">
                        <h3 class="hndle" style="cursor:default;">
                            <span class="wpar-heading">
                                <?php _e( 'Display Settings', 'wp-auto-republish' ); ?>
                            </span>
                        </h3>
                        <div class="inside">
                            <?php do_settings_sections( 'wpar_plugin_republish_info_option' ); ?>
                            <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                        </div>
                    </div>
                    <div id="wpar-query" class="postbox wpar-post" style="display: none;">
                        <h3 class="hndle" style="cursor:default;">
                            <span class="wpar-heading">
                                <?php _e( 'Old Posts Settings', 'wp-auto-republish' ); ?>
                            </span>
                        </h3>
                        <div class="inside">
                            <?php do_settings_sections( 'wpar_plugin_post_query_option' ); ?>
                            <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                        </div>
                    </div>
                    <div id="wpar-post-types" class="postbox wpar-post" style="display: none;">
                        <h3 class="hndle" style="cursor:default;">
                            <span class="wpar-heading">
                                <?php _e( 'Post Types Settings', 'wp-auto-republish' ); ?>
                            </span>
                        </h3>
                        <div class="inside">
                            <?php do_settings_sections( 'wpar_plugin_post_type_option' ); ?>
                            <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                        </div>
                    </div>
                    <?php if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
                        <div id="wpar-single" class="postbox wpar-single" style="display: none;">
                            <h3 class="hndle" style="cursor:default;">
                                <span class="wpar-heading">
                                    <?php _e( 'Single Republish Settings', 'wp-auto-republish' ); ?>
                                </span>
                            </h3>
                            <div class="inside">
                                <?php do_settings_sections( 'wpar_plugin_single_republish_option' ); ?>
                                <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                            </div>
                        </div>
                        <div id="wpar-facebook" class="postbox wpar-social wpar-facebook-box" style="display: none;">
                            <h3 class="hndle" style="cursor:default;">
                                <span class="wpar-heading">
                                    <?php _e( 'Facebook Settings', 'wp-auto-republish' ); ?>
                                </span>
                            </h3>
                            <div class="inside">
                                <?php do_settings_sections( 'wpar_plugin_facebook_option' ); ?>
                                <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?>
                                &nbsp;<span id="wpar-facebook-control" class="wpar-social-control"><input type="button" class="button wpar-add-social-account" data-selector="wpar-facebook" data-account-type="facebook" data-content="wpar-facebook-form" data-processing="<?php _e( 'Processing...', 'wp-auto-republish' ); ?>" value="<?php _e( 'Add New / Re-Authorize Account', 'wp-auto-republish' ); ?>"><span class="nonce-success" style="display: none;color: #068611;padding-left: 15px;"><?php _e( 'Success!', 'wp-auto-republish' ); ?></span><span class="nonce-error" style="display: none;color: #ff0000;padding-left: 15px;"><?php _e( 'Error: Invalid Nonce!', 'wp-auto-republish' ); ?></span><span class="spinner"></span></span>
                                <?php $facebook = apply_filters( 'wpar/display_facebook_accounts', false );
                                if ( $facebook !== false ) { ?>
                                    <input type="button" class="button wpar-db-remove wpar-facebook-btn-remove" style="float: right;" data-account-type="facebook" data-action="wpar_process_delete_social_db" data-notice="<?php _e( 'It will delete all authorized active Facebook accounts and you have to reauthorize all your accounts again. Do you want to continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! All Facebook Accounts deleted successfully!', 'wp-auto-republish' ); ?>" value="<?php _e( 'Delete All Accounts', 'wp-auto-republish' ); ?>"></p>
                                    <?php echo '<span id="wpar-facebook-accounts-list" class="wpar-has-table">' . $facebook . '</span>';
                                } ?>
                            </div>
                            <div id="wpar-facebook-form" style="display: none !important;">
                                <div class="social-form">
                                    <label><?php _e( 'OAuth Redirect URI:', 'wp-auto-republish' ); ?><div><input type="text" id="facebook-callback-url" class="form-control" style="width: 85%" value="https://api.wpautorepublish.com/auth/facebook" readonly /></div></label>
                                    <div style="font-size: 12px;"><?php _e( 'Copy and Paste it in your Facebook App OAuth Redirect URI field.', 'wp-auto-republish' ); ?></div><div class="wpar-clear"></div>
                                    <label><?php _e( 'Enter App ID:', 'wp-auto-republish' ); ?><div><input type="text" id="facebook-app-id" class="form-control" style="width: 85%" value="<?php echo apply_filters( 'wpar/facebook_auth_api_key', '' ); ?>" /></div></label><div class="wpar-clear"></div>
                                    <label><?php _e( 'Enter App Secret:', 'wp-auto-republish' ); ?><div><input type="password" id="facebook-app-secret" class="form-control" style="width: 85%" value="<?php echo apply_filters( 'wpar/facebook_auth_secret_key', '' ); ?>" /></div></label><div class="wpar-clear"></div>
                                    <span style="font-size: 12px;"><?php echo sprintf( __( 'You can find all of your credentials %1$shere%2$s.', 'wp-auto-republish' ), '<a href="https://developers.facebook.com/apps/" target="_blank">', '</a>' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div id="wpar-twitter" class="postbox wpar-twitter-box" style="display: none;">
                            <h3 class="hndle" style="cursor:default;">
                                <span class="wpar-heading">
                                    <?php _e( 'Twitter Settings', 'wp-auto-republish' ); ?>
                                </span>
                            </h3>
                            <div class="inside">
                                <?php do_settings_sections( 'wpar_plugin_twitter_option' ); ?>
                                <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?>
                                &nbsp;<span id="wpar-twitter-control" class="wpar-social-control"><input type="button" class="button wpar-add-social-account" data-selector="wpar-twitter" data-account-type="twitter" data-content="wpar-twitter-form" data-processing="<?php _e( 'Processing...', 'wp-auto-republish' ); ?>" value="<?php _e( 'Add New / Re-Authorize Account', 'wp-auto-republish' ); ?>"><span class="nonce-success" style="display: none;color: #068611;padding-left: 15px;"><?php _e( 'Success!', 'wp-auto-republish' ); ?></span><span class="nonce-error" style="display: none;color: #ff0000;padding-left: 15px;"><?php _e( 'Error: Invalid Nonce!', 'wp-auto-republish' ); ?></span><span class="spinner"></span></span>
                                <?php $twitter = apply_filters( 'wpar/display_twitter_accounts', false );
                                if ( $twitter !== false ) { ?>
                                    <input type="button" class="button wpar-db-remove wpar-twitter-btn-remove" style="float: right;" data-account-type="twitter" data-action="wpar_process_delete_social_db" data-notice="<?php _e( 'It will delete all authorized active Twitter accounts and you have to reauthorize all your accounts again. Do you want to continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! All Twitter Accounts deleted successfully!', 'wp-auto-republish' ); ?>" value="<?php _e( 'Delete All Accounts', 'wp-auto-republish' ); ?>"></p>
                                    <?php echo '<span id="wpar-twitter-accounts-list" class="wpar-has-table">' . $twitter . '</span>';
                                } ?>
                            </div>
                            <div id="wpar-twitter-form" style="display: none !important;">
                                <div class="social-form">
                                    <label><?php _e( 'Callback URL:', 'wp-auto-republish' ); ?><div><input type="text" id="twitter-callback-url" class="form-control" style="width: 85%" value="https://api.wpautorepublish.com/auth/twitter" readonly /></div></label>
                                    <div style="font-size: 12px;"><?php _e( 'Copy and Paste it in your Twitter App Callback URL field.', 'wp-auto-republish' ); ?></div><div class="wpar-clear"></div>
                                    <label><?php _e( 'Enter API Key:', 'wp-auto-republish' ); ?><div><input type="text" id="twitter-app-id" class="form-control" style="width: 85%" value="<?php echo apply_filters( 'wpar/twitter_auth_api_key', '' ); ?>" /></div></label><div class="wpar-clear"></div>
                                    <label><?php _e( 'Enter API Secret:', 'wp-auto-republish' ); ?><div><input type="password" id="twitter-app-secret" class="form-control" style="width: 85%" value="<?php echo apply_filters( 'wpar/twitter_auth_secret_key', '' ); ?>" /></div></label><div class="wpar-clear"></div>
                                    <span style="font-size: 12px;"><?php echo sprintf( __( 'You can find all of your credentials %1$shere%2$s.', 'wp-auto-republish' ), '<a href="https://developer.twitter.com/en/portal/dashboard" target="_blank">', '</a>' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div id="wpar-linkedin" class="postbox wpar-linkedin-box" style="display: none;">
                            <h3 class="hndle" style="cursor:default;">
                                <span class="wpar-heading">
                                    <?php _e( 'Tumblr Settings', 'wp-auto-republish' ); ?>
                                </span>
                            </h3>
                            <div class="inside">
                                <?php do_settings_sections( 'wpar_plugin_linkedin_option' ); ?>
                                <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?>
                                &nbsp;<span id="wpar-linkedin-control" class="wpar-social-control"><input type="button" class="button wpar-add-social-account" data-selector="wpar-linkedin" data-account-type="linkedin" data-content="wpar-linkedin-form" data-processing="<?php _e( 'Processing...', 'wp-auto-republish' ); ?>" value="<?php _e( 'Add New / Re-Authorize Account', 'wp-auto-republish' ); ?>"><span class="nonce-success" style="display: none;color: #068611;padding-left: 15px;"><?php _e( 'Success!', 'wp-auto-republish' ); ?></span><span class="nonce-error" style="display: none;color: #ff0000;padding-left: 15px;"><?php _e( 'Error: Invalid Nonce!', 'wp-auto-republish' ); ?></span><span class="spinner"></span></span>
                                <?php $linkedin = apply_filters( 'wpar/display_linkedin_accounts', false );
                                if ( $linkedin !== false ) { ?>
                                    <input type="button" class="button wpar-db-remove wpar-linkedin-btn-remove" style="float: right;" data-account-type="linkedin" data-action="wpar_process_delete_social_db" data-notice="<?php _e( 'It will delete all authorized active Twitter accounts and you have to reauthorize all your accounts again. Do you want to continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! All Twitter Accounts deleted successfully!', 'wp-auto-republish' ); ?>" value="<?php _e( 'Delete All Accounts', 'wp-auto-republish' ); ?>"></p>
                                    <?php echo '<span id="wpar-linkedin-accounts-list" class="wpar-has-table">' . $linkedin . '</span>';
                                } ?>
                            </div>
                            <div id="wpar-linkedin-form" style="display: none !important;">
                                <div class="social-form">
                                    <label><?php _e( 'Authorized redirect URLs:', 'wp-auto-republish' ); ?><div><input type="text" id="linkedin-callback-url" class="form-control" style="width: 85%" value="https://api.wpautorepublish.com/auth/linkedin" readonly /></div></label>
                                    <div style="font-size: 12px;"><?php _e( 'Copy and Paste it in your Linkedin Authorized redirect URLs field.', 'wp-auto-republish' ); ?></div><div class="wpar-clear"></div>
                                    <label><?php _e( 'Enter OAuth Consumer Key:', 'wp-auto-republish' ); ?><div><input type="text" id="linkedin-app-id" class="form-control" style="width: 85%" value="<?php echo apply_filters( 'wpar/linkedin_auth_api_key', '' ); ?>" /></div></label><div class="wpar-clear"></div>
                                    <label><?php _e( 'Enter Secret Key:', 'wp-auto-republish' ); ?><div><input type="password" id="linkedin-app-secret" class="form-control" style="width: 85%" value="<?php echo apply_filters( 'wpar/linkedin_auth_secret_key', '' ); ?>" /></div></label><div class="wpar-clear"></div>
                                    <span style="font-size: 12px;"><?php echo sprintf( __( 'You can find all of your credentials %1$shere%2$s.', 'wp-auto-republish' ), '<a href="https://www.linkedin.com/developers/apps/" target="_blank">', '</a>' ); ?></span>
                                </div>
                            </div>
                        </div>
                        <div id="wpar-email" class="postbox wpar-email" style="display: none;">
                            <h3 class="hndle" style="cursor:default;">
                                <span class="wpar-heading">
                                    <?php _e( 'Email Settings', 'wp-auto-republish' ); ?>
                                </span>
                            </h3>
                            <div class="inside">
                                <?php do_settings_sections( 'wpar_plugin_email_notify_option' ); ?>
                                <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                            </div>
                        </div>
                    <?php } ?>
                    <div id="wpar-misc" class="postbox wpar-tools" style="display: none;">
                        <h3 class="hndle" style="cursor:default;">
                            <span class="wpar-heading">
                                <?php _e( 'Misc. Options', 'wp-auto-republish' ); ?>
                            </span>
                        </h3>
                        <div class="inside">
                            <?php do_settings_sections( 'wpar_plugin_tools_option' ); ?>
                            <p><?php submit_button( __( 'Save Settings', 'wp-auto-republish' ), 'primary wpar-save', '', false ); ?></p>
                        </div>
                    </div>
                </form>
                <?php if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
                <div id="wpar-logs" class="postbox wpar-logs wpar-has-table" style="display: none;">
                    <h3 class="hndle" style="cursor:default;">
                        <span class="wpar-heading">
                            <?php _e( 'Republish Log', 'wp-auto-republish' ); ?>
                        </span>
                    </h3>
                    <div class="inside" style="padding: 10px 14px 14px;line-height: 1.8;">
                    <?php $log_data = apply_filters( 'wpar/display_republish_logs', false ); ?>
                        <div id="wpar-log-data">
                            <?php echo ( $log_data ) ? $log_data : '<div style="padding: 10px 14px;background-color: #eee;border: 1px solid #cccccc;">' . __( 'No logs found.', 'wp-auto-republish' ) . '</div>'; ?>
                        </div>
                        <?php if ( $log_data !== false ) { ?>
                            <p id="wpar-log-data-control"><?php _e( 'Action', 'wp-auto-republish' ); ?>:
                                <select id="wpar-log-filter-select" style="vertical-align: baseline;">
                                    <option value="default"><?php _e( 'Show All', 'wp-auto-republish' ); ?></option>
                                    <option value="republish"><?php _e( 'Republish Post', 'wp-auto-republish' ); ?></option>
                                    <option value="clone"><?php _e( 'Duplicate Post', 'wp-auto-republish' ); ?></option>
                                    <option value="scheduled"><?php _e( 'Scheduled Post', 'wp-auto-republish' ); ?></option>
                                    <option value="email"><?php _e( 'Email Sent', 'wp-auto-republish' ); ?></option>
                                    <option value="trigger"><?php _e( 'Trigger Publish', 'wp-auto-republish' ); ?></option>
                                    <option value="cache"><?php _e( 'Cache Cleared', 'wp-auto-republish' ); ?></option>
                                    <option value="cron"><?php _e( 'Global Scheduled Queue', 'wp-auto-republish' ); ?></option>
                                    <option value="single_cron"><?php _e( 'Single Scheduled Queue', 'wp-auto-republish' ); ?></option>
                                    <option value="facebook_page"><?php _e( 'Facebook Page Share', 'wp-auto-republish' ); ?></option>
                                    <option value="facebook_group"><?php _e( 'Facebook Group Share', 'wp-auto-republish' ); ?></option>
                                    <option value="twitter"><?php _e( 'Twitter Profile Share', 'wp-auto-republish' ); ?></option>
                                    <option value="linkedin"><?php _e( 'Linkedin Profile Share', 'wp-auto-republish' ); ?></option>
                                </select>&nbsp;&nbsp;<?php _e( 'Entries', 'wp-auto-republish' ); ?>:
                                <select id="wpar-log-entries" style="vertical-align: baseline;">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="25" selected="selected">25</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                    <option value="50">50</option>
                                    <option value="75">75</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                </select>&nbsp;<span id="wpar-filter-control"><input type="button" class="button wpar-log-filter" data-selector="wpar-logs" value="<?php _e( 'Filter', 'wp-auto-republish' ); ?>"><span class="nonce-error" style="display: none;color: #ff0000;padding-left: 15px;"><?php _e( 'Error: Invalid Nonce!', 'wp-auto-republish' ); ?></span><span class="spinner"></span></span>
                                <input type="button" class="button wpar-db-remove" style="float: right;" data-account-type="log-data" data-action="wpar_process_delete_log_data" data-notice="<?php _e( 'It will delete all the log history data related to the republishing events. Do you want to still continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! All republishing logs deleted successfully!', 'wp-auto-republish' ); ?>" value="<?php _e( 'Clear Log', 'wp-auto-republish' ); ?>"><input type="hidden" id="wpar-log-state" ></p>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>
                <div id="wpar-tools" class="postbox wpar-tools" style="display: none;">
				    <h3 class="hndle" style="cursor:default;">
                        <span class="wpar-heading">
                            <?php _e( 'Plugin Tools', 'wp-auto-republish' ); ?>
                        </span>
                    </h3>
				    <div class="inside wpar-inside" style="padding: 10px 20px;">
                        <div class="wpar-tools-box">
                            <span><strong><?php _e( 'Export Settings', 'wp-auto-republish' ); ?></strong></span>
		    	        	<p><?php _e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'wp-auto-republish' ); ?></p>
		    	        	<form method="post">
		    	        		<p><input type="hidden" name="wpar_export_action" value="wpar_export_settings" /></p>
		    	        		<p>
		    	        			<?php wp_nonce_field( 'wpar_export_nonce', 'wpar_export_nonce' ); ?>
		    	        			<?php submit_button( __( 'Export Settings', 'wp-auto-republish' ), 'secondary', 'wpar-export', false ); ?>
                                    <input type="button" class="button wpar-copy" value="<?php _e( 'Copy', 'wp-auto-republish' ); ?>" style="margin-left: -1px;">
                                    <span class="wpar-copied" style="padding-left: 6px;display: none;color: #068611;"><?php _e( 'Copied!', 'wp-auto-republish' ); ?></span>
                                </p>
		    	        	</form>
                        </div>
                        <div class="wpar-tools-box">
                            <span><strong><?php _e( 'Import Settings', 'wp-auto-republish' ); ?></strong></span>
		    	        	<p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'wp-auto-republish' ); ?></p>
		    	        	<form method="post" enctype="multipart/form-data">
		    	        		<p><input type="file" name="import_file" accept=".json"/></p>
		    	        		<p>
		    	        			<input type="hidden" name="wpar_import_action" value="wpar_import_settings" />
		    	        			<?php wp_nonce_field( 'wpar_import_nonce', 'wpar_import_nonce' ); ?>
		    	        			<?php submit_button( __( 'Import Settings', 'wp-auto-republish' ), 'secondary', 'wpar-import', false ); ?>
                                    <input type="button" class="button wpar-paste" value="<?php _e( 'Paste', 'wp-auto-republish' ); ?>">
                                </p>
		    	        	</form>
                        </div>
                        <div class="wpar-tools-box">
                            <span><strong><?php _e( 'Reset Settings', 'wp-auto-republish' ); ?></strong></span>
		    	        	<p style="color: #ff0000;"><strong><?php _e( 'WARNING:', 'wp-auto-republish' ); ?> </strong><?php _e( 'Resetting will delete all custom options to the default settings of the plugin in your database.', 'wp-auto-republish' ); ?></p>
		    	        	<p><input type="button" class="button button-primary wpar-reset" data-action="wpar_process_delete_plugin_data" data-reload="true" data-notice="<?php _e( 'It will delete all the data relating to this plugin settings. You have to re-configure this plugin again. Do you want to still continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! Plugin Settings reset successfully.', 'wp-auto-republish' ); ?>" value="<?php _e( 'Reset Settings', 'wp-auto-republish' ); ?>"></p>
                        </div>
                        <div class="wpar-tools-box">
                            <span><strong><?php _e( 'Remove Post Meta & Actions', 'wp-auto-republish' ); ?></strong></span>
		    	        	<p style="color: #ff0000;"><strong><?php _e( 'WARNING:', 'wp-auto-republish' ); ?> </strong><?php _e( 'Resetting will delete all post metadatas and future action events associated with Post Republish.', 'wp-auto-republish' ); ?></p>
		    	        	<p><input type="button" class="button button-primary wpar-reset" data-action="wpar_process_delete_post_metas" data-reload="false" data-notice="<?php _e( 'It will delete all the post meta data & action events relating to global and single post republishing. It may stop previous scheduled republished event. Leave if you are not sure what you are doing. Do you want to still continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! All post meta datas and republish events deleted successfully!', 'wp-auto-republish' ); ?>" value="<?php _e( 'Clear Post Metas & Events', 'wp-auto-republish' ); ?>"></p>
                        </div>
                        <div>
                            <span><strong><?php _e( 'De-Schedule Posts', 'wp-auto-republish' ); ?></strong></span>
		    	        	<p style="color: #ff0000;"><strong><?php _e( 'WARNING:', 'wp-auto-republish' ); ?> </strong><?php _e( 'It will change the republish date to the original post published date on all posts.', 'wp-auto-republish' ); ?></p>
		    	        	<p><input type="button" class="button button-primary wpar-reset" data-action="wpar_process_deschedule_posts" data-reload="false" data-notice="<?php _e( 'It will change the republish date to the original post published date on all posts. Leave if you are not sure what you are doing. Do you want to still continue?', 'wp-auto-republish' ); ?>" data-success="<?php _e( 'Success! All posts de-scheduled successfully!', 'wp-auto-republish' ); ?>" value="<?php _e( 'De-Schedule Posts', 'wp-auto-republish' ); ?>"></p>
                        </div>
                    </div>
                </div>
                <div id="wpar-help" class="postbox wpar-help" style="display: none;">
                    <h3 class="hndle" style="cursor:default;">
                        <span class="wpar-heading">
                            <?php _e( 'Plugin Help', 'wp-auto-republish' ); ?>
                        </span>
                    </h3>
                    <div class="inside">
                        <h2><?php _e( 'Do you need help with this plugin? Here are some FAQ for you:', 'wp-auto-republish' ); ?></h2>
                        <ol class="help-faq">
                            <li><?php printf( __( 'How this %s plugin works?', 'wp-auto-republish' ), $this->name ); ?></li>
                            <p><?php _e( 'This plugin is mainly based on WordPress Cron system to republish your old evergreen posts. It will generate republish events when plugin is instructed to republish a post. It is designed in a way to easily work with any server enviroment. If still it not works, please contact your hosting provider to increase server resources.', 'wp-auto-republish' ); ?></p>
                        
                            <li><?php _e( 'WordPress Cron is disabled on my website. What can I do?', 'wp-auto-republish' ); ?></li>
                            <p><?php printf( __( 'This plugin is heavily based on WP Cron. If it is disabled on your website which is required by %1$s plugin, please enable native WP Cron or follow this <a href="%2$s" target="_blank">tutorial</a> to enable server level PHP Cron instead with an interval of less than Republish Interval option.', 'wp-auto-republish' ), $this->name, 'https://wpautorepublish.com/docs/how-to-replace-wp-cron-with-a-real-cron-job/' ); ?></p>
                                 
                            <?php if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON !== false ) { ?>
                                <div class="update-message notice inline notice-warning notice-alt">
                                    <p class="cron-warning"><?php _e( 'Native WordPress Cron is currently disabled on your website. Please enable it or follow the upper mentioned tutorial.', 'wp-auto-republish' ); ?></p>
                                </div>
                            <?php } ?>
                            <li><?php _e( 'Plugin sometimes fails or misses to republish a particular post at a specified time. What is the reason?', 'wp-auto-republish' ); ?></li>
                            <p><?php printf( __( 'This plugin is based on WP Cron which depends on the traffic volume of your website. If you have low traffic, there may be chances to miss any republish job. To avoid this, please disable native WP Cron and follow this <a href="%s" target="_blank">tutorial</a> to enable server level PHP Cron instead with an interval of less than Republish Interval option.', 'wp-auto-republish' ), 'https://wpautorepublish.com/docs/how-to-replace-wp-cron-with-a-real-cron-job/' ); ?></p>
                        
                            <li><?php _e( 'Doesn’t changing the timestamp affect permalinks that include dates using this plugin?', 'wp-auto-republish' ); ?></li>
                            <p><?php printf( __( 'If your permalinks structure contains date, please use %1$s instead of %2$s respectively if you are using premium version. If you are using free version then please disable this plugin or upgrade to Premium version to avoid SEO issues.', 'wp-auto-republish' ), '<code>%wpar_year%</code>, <code>%wpar_monthnum%</code>, <code>%wpar_day%</code>, <code>%wpar_hour%</code>, <code>%wpar_minute%</code>, <code>%wpar_second%</code>', '<code>%year%</code>, <code>%monthnum%</code>, <code>%day%</code>, <code>%hour%</code>, <code>%minute%</code>, <code>%second%</code>' ); ?></p>
                            <?php if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
                                <li><?php _e( 'What is Scheduled Republish (Rewrite) Feature. How to deal with it? Is it delete the old post?', 'wp-auto-republish' ); ?></li>
                                <p><?php _e( 'Suppose you have a post published recenlty or some times ago. It may be days, months, years etc. Now you want to republish that post want to make some changes or change some taxonomies. Also you want to republish that post on a particular date in future automatically. You can do it by using this feature. Just click on Rewrite link from post row or post edit screen and it will copy your original post. Now you can make any changes to that copied post and scheduled it for a future date. Plugin will delete the original post when this new post is being published on the previously specified future date.', 'wp-auto-republish' ); ?></p>
                            <?php } ?>
                        
                            <li><?php _e( 'I have some custom taxonomies associated with posts or pages or any custom post types but they are not showing on settings dropdown. OR, Somehow custom post types republishing are now stopped suddenly.', 'wp-auto-republish' ); ?></li>
                            <p><?php _e( 'Free version of this plugin has some limitation. You can republish a particular post of a custom post type upto 3 times. After that plugin doesn\'t republish those posts anymore. You have to use Premium version of this plugin to use it more than 3 times for custom post types. Also, Post and Page do not have such limitations in the free version. Taxonomies, other than Category and Post Tags, are available only on premium version.', 'wp-auto-republish' ); ?></p>
                        
                            <li><?php _e( 'I am using GoDaddy managed hosting and plugin is not working properly. OR, My Hosting Company does not support Server Level Cron Jobs. What to do next?', 'wp-auto-republish' ); ?></li>
                            <p><?php printf( __( 'As if your Hosting does not allow to use server level cron, you have to use WordPress Native Cron method, to get it properly woking. Just follow the FAQ no. 2. Otherwise you can use other external cron services like %1$s with 1 minute interval and use this URL: %2$s to solve this issue.', 'wp-auto-republish' ), '<a href="https://cron-job.org" target="_blank">https://cron-job.org</a>', '<code>' . home_url( 'wp-cron.php?doing_wp_cron' ) . '</code>' ); ?></p>
                        
                            <li><?php _e( 'I have just installed this plugin and followed all previous guides but still it is not working properly. What to do?', 'wp-auto-republish' ); ?></li>
                            <p><?php printf( __( 'At first, properly configure plugin settings. You can know more details about every settings hovering the mouse over the question mark icon next to the settings option. After that, Please wait some time to allow plugin to run republish process with an interval configured by you from plugin settings. If still not working, go to Tools > Plugins Tools > Import Settings > Copy and then open Pastebin.com or GitHub Gist and create a paste or gist with the copied data and send me the link using Contact page or open a support on WordPress.org forums (only for free version users). Here are some common <a href="%s" target="_blank">cron problems</a> related to WordPress.', 'wp-auto-republish' ), 'https://github.com/johnbillion/wp-crontrol/wiki/Cron-events-that-have-missed-their-schedule' ); ?></p>
                        
                            <li><?php _e( 'Plugin is showing a warning notice to disable the plugin after activation. What is the reason?', 'wp-auto-republish' ); ?></li>
                            <p><?php printf( __( 'Currently you are using original post published information in your post permalinks (Settings > Permalinks). But this plugin reassign a current date to republish a post. So, the permalink will be changed after republish. It may cause SEO issues. It will be safe not to use free version of this plugin in such situation. But in the Premium version you can use %1$s instead of %2$s to solve this issue.', 'wp-auto-republish' ), '<code>%wpar_year%</code>, <code>%wpar_monthnum%</code>, <code>%wpar_day%</code>, <code>%wpar_hour%</code>, <code>%wpar_minute%</code>, <code>%wpar_second%</code>', '<code>%year%</code>, <code>%monthnum%</code>, <code>%day%</code>, <code>%hour%</code>, <code>%minute%</code>, <code>%second%</code>' ); ?></p>
                        
                            <li><?php _e( 'Plugin is showing a PHP fatal error after enabling the Premium version. How to fix this?', 'wp-auto-republish' ); ?></li>
                            <p><?php _e( 'Please deactivate the free version of this plugin first from plugins page, then activate the premium version. It should work as expected.', 'wp-auto-republish' ); ?></p>
                        </ol>
                    </div>
                </div>
                <?php if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
                    <div class="coffee-box">
                        <div class="coffee-amt-wrap">
                            <p><select class="coffee-amt">
                                <option value="5usd">$5</option>
                                <option value="6usd">$6</option>
                                <option value="7usd">$7</option>
                                <option value="8usd">$8</option>
                                <option value="9usd">$9</option>
                                <option value="10usd" selected="selected">$10</option>
                                <option value="11usd">$11</option>
                                <option value="12usd">$12</option>
                                <option value="13usd">$13</option>
                                <option value="14usd">$14</option>
                                <option value="15usd">$15</option>
                                <option value=""><?php _e( 'Custom', 'wp-auto-republish' ); ?></option>
                            </select></p>
                            <a class="button button-primary buy-coffee-btn" style="margin-left: 2px;" href="https://www.paypal.me/iamsayan/10usd" data-link="https://www.paypal.me/iamsayan/" target="_blank"><?php _e( 'Buy me a coffee!', 'wp-auto-republish' ); ?></a>
                        </div>
                        <span class="coffee-heading">
                            <?php _e( 'Buy me a coffee!', 'wp-auto-republish' ); ?>
                        </span>
                        <p style="text-align: justify;">
                            <?php printf( __( 'Thank you for using %s. If you found the plugin useful buy me a coffee! Your donation will motivate and make me happy for all the efforts. You can donate via PayPal.', 'wp-auto-republish' ), '<strong>' . $this->name . ' v' . $this->version . '</strong>' ); ?></strong>
                        </p>
                        <p style="text-align: justify;font-size: 12px;font-style: italic;">
                            Developed with <span style="color:#e25555;">♥</span> by <a href="https://www.sayandatta.in" target="_blank" style="font-weight: 500;">Sayan Datta</a> | 
                            <a href="https://www.sayandatta.in/contact/" style="font-weight: 500;">Hire Me</a> | 
                            <a href="https://github.com/iamsayan/wp-auto-republish" target="_blank" style="font-weight: 500;">GitHub</a> | <a href="https://wordpress.org/support/plugin/wp-auto-republish" target="_blank" style="font-weight: 500;">Support</a> | 
                            <a href="https://wordpress.org/support/plugin/wp-auto-republish/reviews/#new-post" target="_blank" style="font-weight: 500;">Rate it</a> (<span style="color:#ffa000;">&#9733;&#9733;&#9733;&#9733;&#9733;</span>) on WordPress.org, if you like this plugin.
                        </p>
                    </div>
                <?php } ?>
            </div>
            <div id="postbox-container-1" class="postbox-container">
            <?php if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
                <div class="postbox">
                    <h3 class="hndle" style="cursor:default;text-align: center;">
                    <?php if ( wpar_load_fs_sdk()->is_trial() ) { ?>
                        <?php _e( 'You are using the Trial Version', 'wp-auto-republish' ); ?>
                    <?php } else { ?> 
                        <?php _e( 'You are using the Premium Version', 'wp-auto-republish' ); ?>
                    <?php } ?> 
                        </h3>
                    <div class="inside">
                        <div class="misc-pub-section" style="text-align:center;">
                            <div style="font-style: italic;">
                            <?php if ( wpar_load_fs_sdk()->is_trial() ) { ?>
                                <?php _e( 'Trial expires in 7 days. It\'s the best time to Upgrade to Premium Version.', 'wp-auto-republish' ); ?>
                           <?php } else { ?> 
                                <?php _e( 'Thanks for purchasing the Premium Version. Happy Blogging!', 'wp-auto-republish' ); ?>
                            <?php } ?></div><br>
                            <div style="font-size: 11px;font-style: italic;padding-bottom: 4px;">
                                <span>Developed with <span style="color:#e25555;">♥</span> by <a href="https://www.sayandatta.in" target="_blank" style="font-weight: 500;">Sayan Datta</a></span>
                            </div>
                            <div style="font-size: 11px;font-style: italic;">
                                <a href="https://www.sayandatta.in/contact/" style="font-weight: 500;">Hire Me</a> | 
                                <a href="https://github.com/iamsayan/wp-auto-republish" target="_blank" style="font-weight: 500;">GitHub</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?> 
            <?php if ( ! wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) { ?>
                <div class="postbox">
                    <h3 class="hndle" style="cursor:default;text-align: center;"><?php _e( 'Upgrade to Premium Now!', 'wp-auto-republish' ); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section" style="text-align:center;">
                            <i><?php _e( 'Upgrade to the premium version and get the following features', 'wp-auto-republish' ); ?></i>:<br>
				            <ul>
				            	<li>• <?php _e( 'Custom Post types & Taxonomies', 'wp-auto-republish' ); ?></li>
				            	<li>• <?php _e( 'Individual & Scheduled Republishing', 'wp-auto-republish' ); ?></li>
				            	<li>• <?php _e( 'Date Time Range Based Republishing', 'wp-auto-republish' ); ?></li>
				            	<li>• <?php _e( 'Custom Post Republish Interval & Title', 'wp-auto-republish' ); ?></li>
				            	<li>• <?php _e( 'Automatic Social Media Share', 'wp-auto-republish' ); ?></li>
                                <li>• <?php _e( 'Automatic Cache Plugin Purge Support', 'wp-auto-republish' ); ?></li>
                                <li>• <?php _e( 'Can use New Dates in Post Permalinks', 'wp-auto-republish' ); ?></li>
                                <li>• <?php _e( 'Change Post Status after Republish', 'wp-auto-republish' ); ?></li>
                                <li>• <?php _e( 'One Click Instant Republish & Clone', 'wp-auto-republish' ); ?></li>
                                <li>• <?php _e( 'Email Notification upon Republishing', 'wp-auto-republish' ); ?></li>
                                <li>• <?php _e( 'Priority Email Support & many more..', 'wp-auto-republish' ); ?></li>
				            </ul>
				            <?php if ( wpar_load_fs_sdk()->is_not_paying() && ! wpar_load_fs_sdk()->is_trial() && ! wpar_load_fs_sdk()->is_trial_utilized() ) { ?>
                                <a class="button button-primary" href="<?php echo wpar_load_fs_sdk()->get_trial_url(); ?>"><?php _e( 'Start Trial', 'wp-auto-republish' ); ?></a>&nbsp;
                            <?php } ?>
                            <a class="button button-primary" href="<?php echo wpar_load_fs_sdk()->get_upgrade_url(); ?>"><?php _e( 'Upgrade Now', 'wp-auto-republish' ); ?></a>
                        </div>
                    </div>
                </div>
            <?php } ?> 
                <div class="postbox">
                    <h3 class="hndle" style="cursor:default;text-align: center;"><?php _e( 'Server Scheduler', 'wp-auto-republish' ); ?></h3>
                    <div class="inside">
                        <div style="padding: 1px 8px 0;">
					        <p>Check <a href="https://www.google.com/search?q=how+to+setup+cron+job" target="_blank">how to setup the Server Level Cron Job</a> or read more about <a href="https://wpautorepublish.com/docs/how-to-replace-wp-cron-with-a-real-cron-job/" target="_blank">Replacing WP Cron with Server Level/External Cron</a>.</p>
                            <?php if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON !== false ) { ?>
                                <div class="update-message notice inline notice-warning notice-alt">
                                    <p class="cron-warning"><?php _e( 'WP Cron is currently disabled. Use Server Level Cron instead.', 'wp-auto-republish' ); ?></p>
                                </div>
                            <?php } ?>
                            <p><?php printf( __( 'The command you want to use is: %s', 'wp-auto-republish' ), '<code style="font-size: 12px;">wget -q -O - ' . home_url( 'wp-cron.php?doing_wp_cron' ) .' >/dev/null 2>&1</code>' ); ?></p>
					        <p><?php printf( __( 'The reasonable time interval is 1 minute. That is %s for Cron interval setting.', 'wp-auto-republish' ), '* * * * *' ); ?></p>
				        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle" style="cursor:default;text-align: center;"><?php _e( 'My Other Plugins!', 'wp-auto-republish' ); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <div style="text-align: center;">
                                <span class="dashicons dashicons-clock" style="font-size: 16px;vertical-align: middle;"></span>
                                <strong><a href="https://wordpress.org/plugins/wp-last-modified-info/" target="_blank">WP Last Modified Info</a></strong>
                            </div>
                            <div style="text-align: center;">
                                <?php _e( 'Display last update date and time on pages and posts very easily with \'dateModified\' Schema Markup.', 'wp-auto-republish' ); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <div style="text-align: center;">
                                <span class="dashicons dashicons-admin-comments" style="font-size: 16px;vertical-align: middle;"></span>
                                <strong><a href="https://wordpress.org/plugins/ultimate-facebook-comments/" target="_blank">Ultimate Social Comments</a></strong>
                            </div>
                            <div style="text-align: center;">
                                <?php _e( 'Ultimate Facebook Comments Solution with instant email notification for any WordPress Website. Everything is customizable.', 'wp-auto-republish' ); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <div style="text-align: center;">
                                <span class="dashicons dashicons-admin-links" style="font-size: 16px;vertical-align: middle;"></span>
                                <strong><a href="https://wordpress.org/plugins/change-wp-page-permalinks/" target="_blank">WP Page Permalink Extension</a></strong>
                            </div>
                            <div style="text-align: center;">
                                <?php _e( 'Add any page extension like .html, .php, .aspx, .htm, .asp, .shtml only to wordpress pages very easily (tested on Yoast SEO, All in One SEO Pack, Rank Math, SEOPresss and Others).', 'wp-auto-republish' ); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="misc-pub-section">
                            <div style="text-align: center;">
                                <span class="dashicons dashicons-megaphone" style="font-size: 16px;vertical-align: middle;"></span>
                                <strong><a href="https://wordpress.org/plugins/simple-posts-ticker/" target="_blank">Simple Posts Ticker</a></strong>
                            </div>
                            <div style="text-align: center;">
                                <?php _e( 'Simple Posts Ticker is a small tool that shows your most recent posts in a marquee style.', 'wp-auto-republish' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </diV>
        </div>
    </div>
</div>