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
 * Description: A temporary solution to based on <a target="_new" href="https://github.com/analytics-debugger/google-analytics-4-for-amp">Google Analytics 4 for AMP</a> by <a target="_new" href="https://github.com/thyngster">David Vallejo</a>
 * Plugin URI: https://rtcamp.com/
 * Version: 0.1.0
 * Author: Weston Ruter, Google, Milind, rtCamp
 * Author URI: https://weston.ruter.net/
 * License: GNU General Public License v2 (or later)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Gist Plugin URI: 
 */

namespace AMP_Google_Analytics_GA4;

const GTM_CONTAINER_ID = 'G-D37H7GJ96N'; // 👈👈👈 This must be populated with your appropriate value.
define( 'AMP_ANALYTICS_GA4_URL', plugin_dir_url( __FILE__ ) );

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
		esc_attr( GTM_CONTAINER_ID )
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
