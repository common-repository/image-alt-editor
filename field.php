<?php

//* If this file is called directly, abort.
if (!defined('ABSPATH')) {
	die;
}

class Image_Alt_Editor_Field {

	/**
	 * support_thumbnail
	 * @var bool
	 */
	private $support_thumbnail = 'undefined';

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		$this->add_list_column();
		$this->create_list_column();
	}

	/**
	 * set_current_post_type
	 *
	 * @return void
	 */
	public function set_support_thumbnail() {

		if ($this->support_thumbnail === 'undefined') {

			global $pagenow;

			switch ($pagenow) {

				case 'edit.php':
					$name = 'post';
					if ('edit.php' === $pagenow && isset($_GET['post_type'])) {
						$name = sanitize_text_field($_GET['post_type']);
					}
					$this->support_thumbnail = post_type_supports($name, 'thumbnail');
					break;

				case 'upload.php';
					$this->support_thumbnail = true;
					break;
			}
		}
	}

	/**
	 * Check if CPT support thumbnail
	 *
	 * @return void
	 */
	public function check_supports_thumbnail() {
		$this->set_support_thumbnail();
		return $this->support_thumbnail;
	}

	/**
	 * Create new CPT admin list column
	 *
	 * @return void
	 */
	public function add_list_column() {
		add_filter('manage_posts_columns', [$this, 'add_list_column_callback']);
		add_filter('manage_pages_columns', [$this, 'add_list_column_callback']);
		add_filter('manage_media_columns', [$this, 'add_list_column_callback']);
	}

	/**
	 * Callback for admin column header
	 *
	 * @param  mixed $columns
	 * @return void
	 */
	public function add_list_column_callback($columns) {

		if (!$this->check_supports_thumbnail()) {
			return $columns;
		}

		$columns['image-alt-editor'] = __('Image Alt', 'image-alt-editor');

		return $columns;
	}


	/**
	 * Init admin column content
	 *
	 * @return void
	 */
	public function create_list_column() {
		add_action('manage_posts_custom_column', [$this, 'create_list_column_callback_post'], 10, 2);
		add_action('manage_pages_custom_column', [$this, 'create_list_column_callback_post'], 10, 2);
		add_action('manage_media_custom_column', [$this, 'create_list_column_callback_media'], 10, 2);
	}

	/**
	 * Create admin CPT column content
	 *
	 * @param  mixed $column
	 * @param  mixed $post_id
	 * @return void
	 */
	public function create_list_column_callback_post($column, $post_id) {

		if (!$this->check_supports_thumbnail()) {
			return;
		}

		switch ($column) {

			case 'image-alt-editor':
				$thumb_id = get_post_thumbnail_id($post_id);
				echo $this->create_field($thumb_id);
				break;
		}
	}

	/**
	 * Create admin MEDIA column content
	 *
	 * @param  mixed $column
	 * @param  mixed $post_id
	 * @return void
	 */
	public function create_list_column_callback_media($column, $post_id) {

		switch ($column) {

			case 'image-alt-editor':
				echo $this->create_field($post_id);
				break;
		}
	}

	/**
	 * Create admin edit field
	 *
	 * @param  mixed $thumb_id
	 * @return void
	 */
	public function create_field($thumb_id) {

		$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);

		if (!empty($thumb_id)) {

			$hidden_form = (!empty($alt)) ? 'is-hidden' : '';
			$hidden_label = (empty($alt)) ? 'is-hidden' : '';

			$html = '<div class="image-alt-editor-column">';

			$html .= '<div class="image-alt-editor-label ' . $hidden_label . '">';
			$html .= '<div><i>' . __('Alt attribute', 'image-alt-editor') . ' :</i> ' . $alt . '</div>';
			$html .= '<div class="row-actions">';
			$html .= '<span class="edit"><a class="image-alt-editor-action-change">' . __('Edit', 'image-alt-editor') . '</a></span>  | ';
			$html .= '<span class="delete"><a class="image-alt-editor-action-remove" data-id="' . $thumb_id . '">' . __('Remove', 'image-alt-editor') . '</a></span>';
			$html .= '</div>';
			$html .= '</div>';

			$html .= '<div class="image-alt-editor-form ' . $hidden_form . '">';
			$html .= '<input type="text" value="' . $alt . '" data-id="' . $thumb_id . '" class="image-alt-editor-field"/>';
			$html .= '<button class="button image-alt-editor-button">ok</button>';
			$html .= '<span><i class="dashicons dashicons-editor-help"></i> ' . __('Complete field with your image alt attribute', 'image-alt-editor') . '</span>';
			$html .= '</div>';

			$html .= '</div>';
		} else {
			$html = '<i>' . __('No thumbnail', 'image-alt-editor') . '</i>';
		}

		return $html;
	}
}

new Image_Alt_Editor_Field();
