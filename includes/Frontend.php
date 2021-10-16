<?php
namespace ApprenticeshipOnlineAssessmentTool;

use WP_Query;

/**
 * Frontend Pages Handler
 */
class Frontend {

	public function __construct() {
		add_shortcode( 'aoat-form', [ $this, 'render_frontend' ] );
		add_shortcode( 'aoat-assessment-list', [ $this, 'render_assessment_list' ] );
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
		wp_enqueue_script( 'apprenticeship-online-assessment-tool-frontend-pdf' );
		$data = [
			'upload_url' => admin_url('async-upload.php'),
			'ajax_url'   => admin_url('admin-ajax.php'),
			'nonce'      => wp_create_nonce('wp_rest'),
			'aoatGetFormUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/" . ($atts['id'] ?: null)),
			'aoatSaveAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/create"),
            'aoatGetAssessmentsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/"),
			'aoatGetUserUrl' => get_rest_url(null, "/wp/v2/users/me?context=edit"),
			'aoatGetSettingsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings"),
		];
		wp_localize_script( 'apprenticeship-online-assessment-tool-frontend', 'aoat_config', $data );

		$content .= '<div id="vue-frontend-app"></div>';

		return $content;
	}

	public function render_assessment_list( $atts, $content = '' ) {
		$form = get_post($atts['id']);
		$user = wp_get_current_user();

		if (!is_user_logged_in()) {
			return rest_ensure_response( [] );
		}

		$args = [
			'post_type' => 'aoat_assessment',
			'meta_key' => 'form_id',
			'post_status' => 'any',
			'meta_value' => $form->ID,
			'post_author' => $user->ID,
		];
		$assessments = new WP_Query($args);

		$content = '';
		// Add content if we found posts via our query
		if ( $assessments->have_posts() ) {

			// Open div wrapper around loop
			$content .= '<div class="aoat-assessment-list">';

			// Loop through posts
			while ( $assessments->have_posts() ) {

				// Sets up post data so you can use functions like get_the_title(), get_permalink(), etc
				$assessments->the_post();

				// This is the output for your entry so what you want to do for each post.
				$content .= '<div><a href="' . get_the_permalink() . '">' . get_the_title() . '</a> - <a href="' . get_delete_post_link() . '">Delete</a> </div>';

			}

			// Close div wrapper around loop
			$content .= '</div>';

			// Restore data
			wp_reset_postdata();

		} else {
			$content .= 'No assessments yet.';
		}

		// Return your shortcode output
		return $content;

	}

	function aoat_custom_template($content) {

		wp_enqueue_style( 'apprenticeship-online-assessment-tool-frontend' );
		wp_enqueue_script( 'apprenticeship-online-assessment-tool-frontend' );
		wp_enqueue_script( 'apprenticeship-online-assessment-tool-frontend-pdf' );

		global $post;

		$data = [
			'aoatSaveAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/create"),
			'aoatGetFormUrl' => null,
			'aoatGetAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/" . ($post->ID ?: null)),
			'aoatGetAssessmentsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/"),
			'aoatGetMediaUrl' => get_rest_url(null, "/wp/v2/media/"),
			'aoatGetUserUrl' => get_rest_url(null, "/wp/v2/users/me"),
			'nonce'      => wp_create_nonce('wp_rest'),
			'aoatGetSettingsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings"),
            'aoatGetReportsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports"),
		];
		wp_localize_script( 'apprenticeship-online-assessment-tool-frontend', 'aoat_config', $data );

		/* Checks for single template by post type */
		if ( $post->post_type == 'aoat_assessment' ) {
			$content = '<div id="vue-frontend-app"></div>';
		}

		return $content;

	}

	function aoat_upload_file(){
		$uploadedFileId = null;
		foreach ($_FILES as $file => $array) {
			$uploadedFileId = $this->insert_attachment($file,null);
		}
		echo $uploadedFileId;
		wp_die();
		echo json_encode(rest_ensure_response( get_post($uploadedFileId) ));
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
