<?php

/**
 * Set deactivation & uninstall methods
 *
 * @Loaded on 'register_activation_hook'
 * @Loaded on 'register_deactivation_hook'
 * @Loaded on 'register_uninstall_hook'
 */

/**
 * Security
 */
defined('ABSPATH') or die('Cheatin&#8217; uh?');


class Image_Alt_Editor_Activate_Deactivate {

	/**
	 * List of action
	 * @var array
	 */
	protected $actions = [
		'activation'   => [
			'check_versions',
		],
		'deactivation' => [],
		'uninstall'    => []
	];


	/**
	 * Current singleton instance
	 */
	public static $instance = null;

	/**
	 * Get only one instance of our stuff
	 * @return AffilGoodActivateDeactivate
	 */
	public static function get_instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/**
	 * Init action for each register hook
	 *
	 * @param  mixed $action
	 * @return void
	 */
	public function init($action) {

		if (!empty($this->actions[$action])) {

			foreach ($this->actions[$action] as $method) {

				$method = '' . $method;

				if (method_exists($this, $method)) {
					call_user_func([$this, $method]);
				}
			}
		}
	}

	/**
	 * Check WP & PHP version
	 *
	 * @author  Léo Fontin
	 * @since   1.0
	 */
	public function check_versions() {

		global $wp_version;

		// Check PHP version
		if (0 > version_compare(phpversion(), IMAGE_ALT_EDITOR_PHP_MIN)) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(sprintf(__('<strong>%1$s</strong> requires PHP %2$s minimum, your website is actually running version %3$s.', 'image-alt-editor'), 'Image Alt Editor', '<code>' . IMAGE_ALT_EDITOR_PHP_MIN . '</code>', '<code>' . phpversion() . '</code>'));
		}

		// Check WordPress version
		if (0 > version_compare($wp_version, IMAGE_ALT_EDITOR_WP_MIN)) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(sprintf(__('<strong>%1$s</strong> requires WordPress %2$s minimum, your website is actually running version %3$s.', 'image-alt-editor'), 'Image Alt Editor', '<code>' . IMAGE_ALT_EDITOR_WP_MIN . '</code>', '<code>' . $wp_version . '</code>'));
		}
	}
}
