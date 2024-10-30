<?php

/**
 * Plugin Name: Image Alt Editor
 * Description: Edit quickly image alt attributes
 * Version: 1.02
 * 
 * Author: Léo Fontin
 *
 * Licence: GPLv2
 * Licence URI : https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Text Domain: image-alt-editor
 * Domain Path: languages
 */

define('IMAGE_ALT_EDITOR_VERSION', '1.01');
define('IMAGE_ALT_EDITOR_PHP_MIN', '5.6');
define('IMAGE_ALT_EDITOR_WP_MIN', '4.9');
define('IMAGE_ALT_EDITOR_SLUG', 'image_alt_editor');
define('IMAGE_ALT_EDITOR_PATH_ROOT', realpath(plugin_dir_path(__FILE__)) . '/');
define('IMAGE_ALT_EDITOR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IMAGE_ALT_EDITOR_URL_ASSETS', IMAGE_ALT_EDITOR_PLUGIN_URL . 'assets/');
define('IMAGE_ALT_EDITOR_INT_MAX', PHP_INT_MAX - 10);

class Image_Alt_Editor {

	/**
	 * @var null
	 */
	private static $instance = null;

	/**
	 * @return Image_Alt_Editor|null
	 */
	public static function get_instance(){

		if(is_null(self::$instance)){
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 *
	 */
	public function __construct() {

		// Load language files
		add_action('plugins_loaded', array($this, 'load_languages'));

		// For activation and deactivation purposes
		if (is_admin()) {

			// Add activation rules
			register_activation_hook(__FILE__, array($this, 'activation'));

			// Add deactivation rules
			register_deactivation_hook(__FILE__, array($this, 'deactivation'));

			// Add uninstall rules
			register_uninstall_hook(__FILE__, array($this, 'uninstall'));
		}

		// Plugin initialization
		add_action('plugins_loaded', array($this, 'init'));
	}


	/**
	 * Load languages and textdomain
	 * 
	 * @return void
	 */
	public static function load_languages() {
		$location = basename(dirname(__FILE__)) . '/languages';
		load_plugin_textdomain('image-alt-editor', false, $location);
	}

	/**
	 * Actions on activation plugin hook
	 *
	 * @return void
	 */
	public function activation() {
		require_once IMAGE_ALT_EDITOR_PATH_ROOT . 'lib/activate-deactivate.php';
		$activation = Image_Alt_Editor_Activate_Deactivate::get_instance();
		$activation->init('activation');
	}

	/**
	 * Actions on deactivation plugin hook
	 *
	 * @return void
	 */
	public function deactivation() {
		require_once IMAGE_ALT_EDITOR_PATH_ROOT . 'lib/activate-deactivate.php';
		$deactivation = Image_Alt_Editor_Activate_Deactivate::get_instance();
		$deactivation->init('deactivation');
	}

	/**
	 * Actions on uninstall plugin hook
	 *
	 * @return void
	 */
	public function uninstall() {
		require_once IMAGE_ALT_EDITOR_PATH_ROOT . 'lib/activate-deactivate.php';
		$uninstall = Image_Alt_Editor_Activate_Deactivate::get_instance();
		$uninstall->init('uninstall');
	}


	/**
	 * Init plugin
	 *
	 * @return void
	 */
	public static function init() {

		if (is_admin()) {
			require IMAGE_ALT_EDITOR_PATH_ROOT . 'lib/edit.php';
			require IMAGE_ALT_EDITOR_PATH_ROOT . 'lib/field.php';
		}
	}
}

Image_Alt_Editor::get_instance();
