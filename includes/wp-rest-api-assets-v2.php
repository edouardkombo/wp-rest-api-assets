<?php
/**
 * WP REST API Assets routes
 *
 * @package WP_API_Assets
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_REST_Api_Assets' ) ) :


    /**
     * WP REST Api Assets class.
     *
     * WP REST API Assets support for WP API v2.
     *
     * @package WP_API_Assets
     * @since 1.2.0
     */
    class WP_REST_Api_Assets {


    	public $scriptsAndStyles = "";

	    /**
	     * Get WP API namespace.
	     *
	     * @since 1.2.0
	     * @return string
	     */
        public static function get_api_namespace() {
            return 'wp/v2';
        }


	    /**
	     * Get WP API Assets namespace.
	     *
	     * @since 1.2.1
	     * @return string
	     */
	    public static function get_plugin_namespace() {
		    return 'wp-rest-api-assets/v2';
	    }


        /**
         * Register assets routes for WP API v2.
         *
         * @since  1.2.0
         * @return array
         */
        public function register_routes() {

            register_rest_route( self::get_plugin_namespace(), '/assets', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_assets' ),
                )
            ) );

            register_rest_route( self::get_plugin_namespace(), '/assets/permanent', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_permanent_assets' ),
                    'args'     => array(
                        'context' => array(
                        'default' => 'view',
                        ),
                    ),
                )
            ) );

            register_rest_route( self::get_plugin_namespace(), '/assets/permanent/(?P<type>[a-zA-Z0-9_-]+)', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_permanent_asset' ),
                    'args'     => array(
                        'context' => array(
                            'default' => 'view',
                        ),
                    ),
                )
            ) );

            register_rest_route( self::get_plugin_namespace(), '/assets/optional', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_optional_assets' ),
                    'args'     => array(
                        'context' => array(
                            'default' => 'view',
                        ),
                    ),
                )
            ) );

            register_rest_route( self::get_plugin_namespace(), '/assets/optional/(?P<url>[a-zA-Z0-9_-]+)', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_url_optional_assets' ),
                    'args'     => array(
                        'context' => array(
                            'default' => 'view',
                        ),
                    ),
                )
            ) );

            register_rest_route( self::get_plugin_namespace(), '/assets/optional/(?P<url>[a-zA-Z0-9_-]+)/(?P<type>[a-zA-Z0-9_-]+)', array(
                array(
                    'methods'  => WP_REST_Server::READABLE,
                    'callback' => array( $this, 'get_url_type_optional_assets' ),
                    'args'     => array(
                        'context' => array(
                            'default' => 'view',
                        ),
                    ),
                )
            ) );

        }


        /**
         * Get assets.
         *
         * @since  1.2.0
         * @return array All registered assets
         */
        public function get_assets() {

            return apply_filters( 'rest_menus_format_menus', $this->scriptsAndStyles );
        }

        /**
         * Get permanent assets.
         *
         * @since  1.2.0
         * @return array All permanent assets
         */
        public function get_permanent_assets() {

            return apply_filters( 'rest_menus_format_menus', $this->scriptsAndStyles->permanent );
        }

        /**
         * Get permanent asset by type (scripts or styles).
         *
         * @since  1.2.0
         * @return array All permanent assets scripts or styles
         */
        public function get_permanent_asset( $request ) {

            $params     = $request->get_params();
            $type       = $params['type'];

            return apply_filters( 'rest_menus_format_menus', $this->scriptsAndStyles->permanent->{$type} );
        }

        /**
         * Get optional assets.
         *
         * @since  1.2.0
         * @return array All optional assets
         */
        public function get_optional_assets() {

            return apply_filters( 'rest_menus_format_menus', $this->scriptsAndStyles->optional );
        }

        /**
         * Get optional asset by url.
         *
         * @since  1.2.0
         * @return array All optional assets per url
         */
        public function get_url_optional_assets( $request ) {

            $params     = $request->get_params();
            $url       = $params['url'];

            return apply_filters( 'rest_menus_format_menus', $this->scriptsAndStyles->optional->{$url} );
        }

        /**
         * Get optional asset by url and type (scripts or styles)
         *
         * @since  1.2.0
         * @return array All optional assets per url and type
         */
        public function get_url_type_optional_assets( $request ) {

            $params     = $request->get_params();
            $url       = $params['url'];
            $type       = $params['type'];

            return apply_filters( 'rest_menus_format_menus', $this->scriptsAndStyles->optional->{$url}->{$type} );
        }
    }

endif;
