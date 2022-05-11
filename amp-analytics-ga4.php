<?php
/**
 * Plugin Name: AMP GA4 Compatibility.
 *
 * @package   AMP_Google_Tag_Manager
 * @author    Weston Ruter, Google
 * @license   GPL-2.0-or-later
 * @copyright 2019 Google Inc.
 *
 * @wordpress-plugin
 * Plugin Name: AMP GA4 Compatibility.
 * Description: A temporary solution to based on <a href="https://github.com/analytics-debugger/google-analytics-4-for-amp">Google Analytics 4 for AMP</a> by <a href="https://github.com/thyngster">David Vallejo</a>
 * Plugin URI: https://rtcamp.com/
 * Version: 0.1.0
 * Author: Weston Ruter, Google, Milind, rtCamp
 * Author URI: https://weston.ruter.net/
 * License: GNU General Public License v2 (or later)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Gist Plugin URI: 
 */

namespace AMP_Google_Analytics_GA4;

const OPTION_NAME = 'amp_ga4_container_id';
define( 'AMP_ANALYTICS_GA4_URL', plugin_dir_url( __FILE__ ) );


/**
 * Get publisher ID.
 *
 * @return string ID.
 */
function get_pub_id() {
	return get_option( OPTION_NAME, '' );
}

/**
 * Filter plugin action links to add settings.
 *
 * @param string[] $action_links Action links.
 * @return string[] Action links.
 */
function filter_plugin_action_links( $action_links ) {
	$action_links['settings'] = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'options-reading.php' ) . '#' . OPTION_NAME ),
		esc_html__( 'Settings', 'amp-analytics-ga4' )
	);
	return $action_links;
}
add_filter( 'plugin_action_links_' . str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ ), __NAMESPACE__ . '\filter_plugin_action_links' );

/**
 * Register setting.
 */
function register_setting() {
	\register_setting(
		'reading',
		OPTION_NAME,
		[
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
			'description'       => esc_html__( 'Analytics GA4', 'amp-auto-ads' ),
		]
	);

	add_settings_field(
		OPTION_NAME,
		esc_html__( 'Analytics GA4 Publisher ID', 'amp-auto-ads' ),
		function () {
			printf(
				'<p><input id="%s" name="%s" value="%s"></p><p class="description">%s</p>',
				esc_attr( OPTION_NAME ),
				esc_attr( OPTION_NAME ),
				esc_attr( get_pub_id() ),
				esc_html__( 'This is used for AMP GA4 Analytics. eg: G-XXXXYYYY' )
			);
		},
		'reading',
		'default',
		[
			'label_for' => OPTION_NAME,
		]
	);
}
add_action( 'admin_init', __NAMESPACE__ . '\register_setting' );

/**
 * Print amp-analytics.
 */
function print_component() {
	printf(
		'<amp-analytics type="googleanalytics" config="%1$s" data-credentials="include">
			<script type="application/json">
			{
				"vars": {
							"GA4_MEASUREMENT_ID": "%2$s",
							"GA4_ENDPOINT_HOSTNAME": "www.google-analytics.com",
							"DEFAULT_PAGEVIEW_ENABLED": true,    
							"GOOGLE_CONSENT_ENABLED": false,
							"WEBVITALS_TRACKING": false,
							"PERFORMANCE_TIMING_TRACKING": false
				}
			}
			</script>
		</amp-analytics>',
		esc_url( AMP_ANALYTICS_GA4_URL . 'ga4.json' ),
		esc_attr( get_pub_id() )
	);
}

add_action(
	'wp_footer',
	function () {
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			print_component();
		}
	}
);

// Classic mode.
add_filter(
	'amp_post_template_data',
	function( $data ) {
		$data['amp_component_scripts'] = array_merge(
			$data['amp_component_scripts'],
			array(
				'amp-analytics' => true,
			)
		);
		return $data;
	}
);

add_action( 'amp_post_template_footer', __NAMESPACE__ . '\print_component' );
