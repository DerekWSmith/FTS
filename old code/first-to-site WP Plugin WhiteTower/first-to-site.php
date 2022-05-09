<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://whitetower.com.au/
 * @since             1.0.0
 * @package           First_To_Site
 *
 * @wordpress-plugin
 * Plugin Name:       First to site
 * Plugin URI:        https://whitetower.com.au/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Whitetower Digital
 * Author URI:        https://whitetower.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       first-to-site
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FIRST_TO_SITE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-first-to-site-activator.php
 */
function activate_first_to_site() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-first-to-site-activator.php';
	First_To_Site_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-first-to-site-deactivator.php
 */
function deactivate_first_to_site() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-first-to-site-deactivator.php';
	First_To_Site_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_first_to_site' );
register_deactivation_hook( __FILE__, 'deactivate_first_to_site' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-first-to-site.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_first_to_site() {

	$plugin = new First_To_Site();
	$plugin->run();

}
run_first_to_site();


function required_plugin_notice() {
	?>
	<div class="error">
		<p>Sorry, But Sportspicks requires Advanced Custom Fields Pro and Members to be installed and activated</p>
	</div>
	<?php 
}
