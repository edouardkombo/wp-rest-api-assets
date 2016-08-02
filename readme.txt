=== WP REST API Assets ===
Contributors: edouardkombo
Tags: wp-api, wp-rest-api, json-rest-api, json, assets, rest, api
Requires at least: 3.6.0
Tested up to: 4.4.2
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Extends WordPress WP REST API with all plugins and current theme assets (scripts and css required).

== Description ==

This plugin extends the [WordPress JSON REST API](https://wordpress.org/plugins/json-rest-api/) with assets (scripts and styles) required by WordPress active plugins and current theme

The new routes available will be:

* `/wp-json/wp-rest-api-assets/v2/assets` GET ALL THE ASSETS.
* `/wp-json/wp-rest-api-assets/v2/assets/permanent` GET ALL PERMANENT ASSETS.
* `/wp-json/wp-rest-api-assets/v2/assets/permanent/{type}` REPLACE BY 'SCRIPTS' OR 'STYLES' TO FETCH THEM.
* `/wp-json/wp-rest-api-assets/v2/assets/optional` GET ALL OPTIONAL ASSETS.
* `/wp-json/wp-rest-api-assets/v2/assets/optional/{url}` GET ALL OPTIONAL ASSETS PER PAGE URL.
* `/wp-json/wp-rest-api-assets/v2/assets/optional/{url}/{type}` GET ALL OPTIONAL ASSETS SCRIPTS OR STYLES PER PAGE URL.

You can alter the data arrangement of each asset in the back office of the plugin.


== Installation ==

This plugin requires having [WP API](https://wordpress.org/plugins/json-rest-api/) installed and activated or it won't be of any use.

Install the plugin as you would with any WordPress plugin in your `wp-content/plugins/` directory or equivalent.

Once installed, activate WP API Assets from WordPress plugins dashboard page and you're ready to go, WP API will respond to new routes and endpoints to your registered assets.


== Frequently Asked Questions ==

= Is this an official extension of WP API? =

No, it was developed for our needs.

== Screenshots ==

![ScreenShot](https://github.com/edouardkombo/wp-rest-api-assets/blob/master/screen1.png)

![ScreenShot](https://github.com/edouardkombo/wp-rest-api-assets/blob/master/screen2.png)
