<?php
/**
 * Plugin Name: WP REST API Assets
 * Plugin URI:  https://github.com/edouardkombo/wp-rest-api-assets
 * Description: With all plugins and current theme assets (scripts and styles).
 *
 * Version:     1.3.0
 *
 * Author:      Edouard Kombo
 * Author URI:  https://github.com/edouardkombo
 *
 * Text Domain: wp-rest-api-assets
 *
 * @package WP_Rest_Api_Assets
 */

/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// WP API v2.
include_once 'includes/wp-rest-api-assets-v2.php';
include_once 'admin/index.php';

if ( ! function_exists ( 'wp_rest_api_assets_init' ) ) :

    /**
     * Get assets in json format by searching inside files
     *
     * If custom file is there, it should be requested by default, otherwise priority is to original json file
     */
    function wraa_get_assets() {

        $originalFile = __DIR__ . '/original.json';
        $customFile = __DIR__ . '/custom.json';

        if (file_exists($originalFile)) {
            $result = (string)file_get_contents($originalFile);
        }

        if (file_exists($customFile)) {
            $result = (string) file_get_contents($customFile);
        }

        return  (empty($result)) ? NULL : json_decode($result);
    }

	/**
	 * Init JSON REST API Assets routes.
	 *
	 * @since 1.0.0
	 */
    function wraa_wp_rest_api_assets_init() {

        $all_the_scripts_and_styles = wraa_get_assets();

        if ( ! defined( 'JSON_API_VERSION' ) &&
             ! in_array( 'json-rest-api/plugin.php', get_option( 'active_plugins' ) ) ) {
			     $class = new WP_REST_Api_Assets();
	             $class->scriptsAndStyles = $all_the_scripts_and_styles;
			     add_filter( 'rest_api_init', array( $class, 'register_routes' ) );
        } else {
            $class = new WP_JSON_Menus();
            add_filter( 'json_endpoints', array( $class, 'register_routes' ) );
        }
    }

	add_action( 'init', 'wraa_wp_rest_api_assets_init' );
    add_action( 'wp_head', 'wraa_wp_rest_api_assets_init' );
	do_action('wraa_wp_rest_api_assets_init');



endif;
