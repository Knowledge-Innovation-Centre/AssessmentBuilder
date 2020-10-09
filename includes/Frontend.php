<?php
namespace ApprenticeshipOnlineAssessmentTool;

/**
 * Frontend Pages Handler
 */
class Frontend {

	public function __construct() {
		add_shortcode( 'aoat-form', [ $this, 'render_frontend' ] );
		add_filter('the_content', [ $this, 'aoat_custom_template']);


		add_action('wp_ajax_aoat_upload_file', [ $this, 'aoat_upload_file']);
		add_action('wp_ajax_nopriv_aoat_upload_file', [ $this, 'aoat_upload_file']);

		add_action('rest_prepare_user', [ $this, 'rest_prepare_user'], 10, 3 );
	}

	public function rest_prepare_user( $response, $user, $request ) {
		$response->data[ 'first_name' ] = get_user_meta( $user->ID, 'first_name', true );
		$response->data[ 'last_name' ] = get_user_meta( $user->ID, 'last_name', true );

		return $response;
	}

	/**
	 * Render frontend app
	 *
	 * @param  array $atts
	 * @param  string $content
	 *
	 * @return string
	 */
	public function render_frontend( $atts, $content = '' ) {
		wp_enqueue_style( 'apprenticeship-online-assessment-tool-frontend' );
		wp_enqueue_script( 'apprenticeship-online-assessment-tool-frontend' );
		$data = [
			'upload_url' => admin_url('async-upload.php'),
			'ajax_url'   => admin_url('admin-ajax.php'),
			'nonce'      => wp_create_nonce('wp_rest'),
			'aoatGetFormUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/" . ($atts['id'] ?: null)),
			'aoatSaveAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessment/create"),
			'aoatGetUserUrl' => get_rest_url(null, "/wp/v2/users/me"),
		];
		wp_localize_script( 'apprenticeship-online-assessment-tool-frontend', 'aoat_config', $data );

		$content .= '<div id="vue-frontend-app"></div>';

		return $content;
	}

	function aoat_custom_template($content) {

		wp_enqueue_style( 'apprenticeship-online-assessment-tool-frontend' );
		wp_enqueue_script( 'apprenticeship-online-assessment-tool-frontend' );

		global $post;

		$data = [
			'aoatSaveAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessment/create"),
			'aoatGetFormUrl' => null,
			'aoatGetAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/" . ($post->ID ?: null)),
			'aoatGetMediaUrl' => get_rest_url(null, "/wp/v2/media/"),
			'aoatGetUserUrl' => get_rest_url(null, "/wp/v2/users/me"),
			'nonce'      => wp_create_nonce('wp_rest'),
		];
		wp_localize_script( 'apprenticeship-online-assessment-tool-frontend', 'aoat_config', $data );

		/* Checks for single template by post type */
		if ( $post->post_type == 'aoat_assessment' ) {
			$content = '<div id="vue-frontend-app"></div>';
		}

		return $content;

	}

	function aoat_upload_file(){
		$uploadedFile = null;
		foreach ($_FILES as $file => $array) {
			$uploadedFile = $this->insert_attachment($file,null);
		}
		echo $uploadedFile;
		wp_die();
	}

	function  insert_attachment($file_handler,$post_id) {

		// check to make sure its a successful upload
		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		return media_handle_upload( $file_handler, $post_id );
	}
}
