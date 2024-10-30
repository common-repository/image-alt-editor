<?php

//* If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

class Image_Alt_Editor_Edit {

	/**
	 * id
	 * @var bool
	 */
	private $id = false;

	/**
	 * value
	 * @var string
	 */
	private $value = '';


	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

		add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);

		add_action('wp_ajax_image_alt_editor_edit', [$this, 'edit']);
		add_action('wp_ajax_image_alt_editor_remove', [$this, 'remove']);
	}


	/**
	 * Enqueue script and css
	 *
	 * @return void
	 */
	public function enqueue_assets() {

		// CSS //
		wp_enqueue_style('image-alt-editor', IMAGE_ALT_EDITOR_URL_ASSETS . 'image-alt-editor.css');

		// JS //
		wp_enqueue_script('image-alt-editor', IMAGE_ALT_EDITOR_URL_ASSETS . 'image-alt-editor.js', ['jquery'], '1.0');
		wp_localize_script('image-alt-editor', 'IAEadminAjax', admin_url('admin-ajax.php'));
	}


	/**
	 * Action on edit attribute
	 * Action on ajax
	 *
	 * @return void
	 */
	public function edit() {

		$response = [];

		$this->get_datas();

		if ($this->ckeck_datas()) {

			if ($this->save()) {

				$response = [
					'type' => 'success'
				];
			} else {

				$response = [
					'type'    => 'error',
					'message' => __('Technical error, refresh page and try again', 'image-alt-editor')
				];
			}
		} else {
			$response = [
				'type'    => 'error',
				'message' => __('No thumbnail ID detected', 'image-alt-editor')
			];
		}

		wp_send_json($response);
	}


	/**
	 * Remove alt attribute for one media
	 *
	 * @return void
	 */
	public function remove() {

		$response = [];

		$this->get_datas();

		if ($this->ckeck_datas()) {

			$this->value = '';

			if ($this->save()) {

				$response = [
					'type' => 'success'
				];
			} else {

				$response = [
					'type'    => 'error',
					'message' => __('Technical error, refresh page and try again', 'image-alt-editor')
				];
			}
		} else {
			$response = [
				'type'    => 'error',
				'message' => __('No thumbnail ID detected', 'image-alt-editor')
			];
		}

		wp_send_json($response);
	}

	/**
	 * Secure posted datas on ajax
	 *
	 * @return void
	 */
	private function get_datas() {

		if (!empty($_POST['id'])) {

			$this->id = sanitize_key((int) $_POST['id']);

			if (!empty($_POST['value'])) {
				$this->value = sanitize_text_field($_POST['value']);
			}
		}
	}

	/**
	 * Check if media exist
	 *
	 * @return void
	 */
	private function ckeck_datas() {

		if ($this->id === false) {
			return false;
		}

		// Check if thumbnail exist //
		$attachment = get_posts(array(
			'post_type'     => 'attachment',
			'post_per_page' => 1,
			'p'             => $this->id
		));

		if (!empty($attachment)) {
			return true;
		}

		return false;
	}


	/**
	 * Save new media metta for attachement image alt
	 *
	 * @return void
	 */
	private function save() {

		$prev_meta = get_post_meta($this->id, '_wp_attachment_image_alt', true);

		if ($prev_meta === $this->value) {
			return true;
		}

		$update = update_post_meta($this->id, '_wp_attachment_image_alt', $this->value);

		return $update;
	}
}

new Image_Alt_Editor_Edit();
