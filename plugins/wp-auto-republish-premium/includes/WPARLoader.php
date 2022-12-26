<?php
/**
 * Register all classes
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar;

/**
 * WPAR Main Class.
 */
final class WPARLoader
{
	/**
	 * Store all the classes inside an array
	 * 
	 * @return array Full list of classes
	 */
	public static function get_services() 
	{
		$services = [
			Pages\Dashboard::class,
			Base\Enqueue::class,
			Base\Actions::class,
			Base\Localization::class,
			Base\AdminNotice::class,
			Base\RatingNotice::class,
			Base\DonateNotice::class,
			Base\PluginTools::class,
			Base\MiscActions::class,
			Core\FetchPosts::class,
			Core\PostRepublish::class,
			Core\SiteCache::class,
			Core\RepublishInfo::class,
			Tools\HealthCheck::class,
			Tools\DatabaseTable::class,
			Tools\MigrateActions::class,
		];

		$premium_services = [];

		if ( wpar_load_fs_sdk()->can_use_premium_code__premium_only() ) {
	        $premium_services = [
	        	Core\Premium\AdminColumnView::class,
	        	Core\Premium\DashboardWidget::class,
				Core\Premium\ExtendedActions::class,
				Core\Premium\EventLogger::class,
	        	Core\Premium\PostMetaBox::class,
				Core\Premium\Notification::class,
				Core\Premium\OneClickRepublish::class,
				Core\Premium\PostStatusFilters::class,
				Core\Premium\RewritePermainks::class,
				Core\Premium\SingleCache::class,
				Core\Premium\SingleRepublish::class,
				Core\Premium\Social\FacebookShare::class,
				Core\Premium\Social\TwitterShare::class,
				Core\Premium\Social\LinkedinShare::class,
				Core\Premium\Social\SocialActions::class,
				Core\Premium\Rules\PostType::class,
				Core\Premium\Rules\RuleMetaBox::class,
				Core\Premium\Rules\RuleActions::class,
				Core\Premium\Rules\GetPosts::class,
				Core\Premium\Rules\ColumnView::class
			];
	    }

		return array_merge( $services, $premium_services );
	}

	/**
	 * Loop through the classes, initialize them, 
	 * and call the register() method if it exists
	 */
	public static function register_services() 
	{
		foreach ( self::get_services() as $class ) {
			$service = self::instantiate( $class );
			if ( method_exists( $service, 'register' ) ) {
				$service->register();
			}
		}
	}

	/**
	 * Initialize the class
	 * 
	 * @param  class $class    class from the services array
	 * @return class instance  new instance of the class
	 */
	private static function instantiate( $class )
	{
		$service = new $class();

		return $service;
	}
}