<?php
/**
 * Clear Single Post Cache.
 *
 * @since      1.1.0
 * @package    WP Auto Republish
 * @subpackage Wpar\Core\Premium
 * @author     Sayan Datta <hello@sayandatta.in>
 */

namespace Wpar\Core\Premium;

use Wpar\Helpers\Hooker;

defined( 'ABSPATH' ) || exit;

/**
 * Single post cache class.
 */
class SingleCache
{
	use Hooker;

	/**
	 * Register functions.
	 */
	public function register()
	{
		$this->action( 'wpar/single_post_cache', 'purge_single_post_cache', 10, 2 );
	}

	/**
	 * Purge single post cache.
	 * 
	 * @param int    $post_id  Post ID
	 * @param object $post     WP Post Object
	 */
	public function purge_single_post_cache( $post_id, $post )
	{
		# wordpress default cache
		if ( function_exists( 'clean_post_cache' ) ) {
			clean_post_cache( $post_id );
		}
			
		# Purge all W3 Total Cache
		if ( function_exists( 'w3tc_flush_post' ) ) {
			w3tc_flush_post( $post_id );
		}
		
		# Purge WP Super Cache
		if ( function_exists( 'wp_cache_post_change' ) ) {
			wp_cache_post_change( $post_id );
		}
		
		# Purge WP Rocket
		if ( function_exists( 'rocket_clean_post' ) ) {
			rocket_clean_post( $post_id );
		}
		
		# Purge Wp Fastest Cache
		if( function_exists( 'wpfc_clear_post_cache_by_id' ) ) {
			wpfc_clear_post_cache_by_id( $post_id );
		}
		
		# Purge Cachify
		if ( function_exists( 'cachify_remove_post_cache' ) ) {
			cachify_remove_post_cache( $post_id );
		}
		
		# Purge Comet Cache
		if ( class_exists( 'comet_cache' ) && method_exists( 'comet_cache', 'clearPost' ) ) {
			\comet_cache::clearPost( $post_id );
		}
		
		# Purge Zen Cache
		if ( class_exists( 'zencache' ) && method_exists( 'zencache', 'clearPost' ) ) {
			\zencache::clearPost( $post_id );
		}
		
		# Purge LiteSpeed Cache 
		if( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_post' ) ) {
			\LiteSpeed_Cache_API::purge_post( $post_id );
		}
		
		# Purge Cache Enabler
		if ( has_action( 'ce_clear_post_cache' ) ) {
			\do_action( 'ce_clear_post_cache', $post_id );
		}

		# Purge Hyper Cache
		if ( class_exists( 'HyperCache' ) ) {
			$hC = new \HyperCache;
			if ( method_exists( $hC, 'clean_post' ) ) {
			    $hC->clean_post( $post_id );
			}
		}

		# Purge Autoptimize Cache
		if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
			\autoptimizeCache::clearall();
		}

		# Purge SG Optimizer
		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			sg_cachepress_purge_cache( get_permalink( $post ) );
		}
		
		# Purge Godaddy Managed WordPress Hosting (Varnish + APC)
		if ( class_exists( 'WPaaS\Plugin' ) && method_exists( 'WPass\Plugin', 'vip' ) ) {
			$this->godaddy_purge_varnish( 'BAN', get_permalink( $post ) );
		}
		
		# Purge WP Engine
		if ( class_exists( 'WpeCommon' ) ) {
			$wpe_methods = [
				'purge_varnish_cache',
			];

			// More agressive clear/flush/purge behind a filter
			if ( $this->do_filter( 'flush_wpengine_aggressive', false ) ) {
				$wpe_methods = array_merge( $wpe_methods, [ 'purge_memcached', 'clear_maxcdn_cache' ] );
			}

			// Filtering the entire list of WpeCommon methods to be called (for advanced usage + easier testing)
			$wpe_methods = $this->do_filter( 'flush_wpengine_methods', $wpe_methods );

			foreach ( $wpe_methods as $wpe_method ) {
				if ( method_exists( 'WpeCommon', $wpe_method ) ) {
					\WpeCommon::$wpe_method( $post_id );
				}
			}
		}
		
		# Purge Breeze Cache
		if ( class_exists( 'Breeze_PurgeCache' ) && method_exists( 'Breeze_PurgeCache', 'purge_post_on_update' ) ) {
			\Breeze_PurgeCache::purge_post_on_update( $post_id );
		}
	
		# Purge Swift Cache
		if ( class_exists( 'Swift_Performance_Cache' ) && method_exists( 'Swift_Performance_Cache', 'clear_post_cache' ) ) {
			\Swift_Performance_Cache::clear_post_cache( $post_id );
		}
		
		# Purge nGinx Helper Cache
		if ( defined( 'NGINX_HELPER_BASENAME' ) ) {
			\do_action( 'rt_nginx_helper_purge_all' );
		}

		# Purge Proxy Cache
		if ( class_exists( 'VarnishPurger' ) ) {
			$vP = new \VarnishPurger();
			if ( method_exists( $vP, 'purgePost' ) ) {
			    $vP->purgePost( $post_id );
			}
		}

		# Purge nGinx Cache
		if ( class_exists( 'NginxCache' ) ) {
			$nC = new \NginxCache();
			if ( method_exists( $nC, 'purge_zone_once' ) ) {
			    $nC->purge_zone_once();
			}
		}

		# flush opcache if available
		if( function_exists( 'opcache_reset' ) ) {
			opcache_reset();
		}

		# Purge Pagely
		if ( class_exists( 'PagelyCachePurge' ) ) {
			$purge_pagely = new \PagelyCachePurge();
			if ( method_exists( $purge_pagely, 'purgeAll' ) ) {
				$purge_pagely->purgeAll();
			}
		}
		
		# Purge Pressidum
		if ( defined( 'WP_NINUKIS_WP_NAME' ) && class_exists( 'Ninukis_Plugin' ) ) {
			$purge_pressidum = \Ninukis_Plugin::get_instance();
			if ( method_exists( $purge_pressidum, 'purgeAllCaches' ) ) {
				$purge_pressidum->purgeAllCaches();
			}
		}
		
		# Purge Savvii
		if ( defined( '\Savvii\CacheFlusherPlugin::NAME_DOMAINFLUSH_NOW' ) ) {
			$purge_savvii = new \Savvii\CacheFlusherPlugin();
			if ( method_exists( $purge_savvii, 'domainflush' ) ) {
				$purge_savvii->domainflush();
			}
		}
	}
	
	/**
	 * GoDaddy purge vernish cache.
	 */
	private function godaddy_purge_varnish( $method, $url = null )
	{
		$url = empty( $url ) ? home_url() : $url;
		$host = parse_url( $url, PHP_URL_HOST );
		$url = set_url_scheme( str_replace( $host, \WPaas\Plugin::vip(), $url ), 'http' );
		wp_cache_flush();
		update_option( 'gd_system_last_cache_flush', time() ); # purge apc
		wp_remote_request( esc_url_raw( $url ), [ 'method' => $method, 'blocking' => false, 'headers' => [ 'Host' => $host ] ] );
	}
}