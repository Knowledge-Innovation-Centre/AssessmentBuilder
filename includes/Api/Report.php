<?php
namespace ApprenticeshipOnlineAssessmentTool\Api;

use ApprenticeshipOnlineAssessmentTool\Helper;
use WP_Error;
use WP_Query;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST_API Handler
 */
class Report extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->namespace = 'apprenticeship-online-assessment-tool/v1';
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/reports/create',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'create_or_update_report' ],
                    'permission_callback' => [ $this, 'create_or_update_report_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/reports/duplicate',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'duplicate_report' ],
                    'permission_callback' => [ $this, 'duplicate_report_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/reports/activate',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'activate_report' ],
                    'permission_callback' => [ $this, 'activate_report_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/reports/(?P<id>\d+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_report' ],
                    'permission_callback' => [ $this, 'get_report_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/reports/(?P<id>\d+)',
            [
                [
                    'methods'             => WP_REST_Server::DELETABLE,
                    'callback'            => [ $this, 'delete_report' ],
                    'permission_callback' => [ $this, 'delete_report_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
    }

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function create_or_update_report( WP_REST_Request $request ) {

    	$id = $request->get_params()['id'];
    	$formId = $request->get_params()['formId'];

    	$data = [
		    'post_type' => 'aoat_report',
		    'post_title' => $request->get_params()['title'],
		    'post_content' => '',
		    'comment_status' => 'closed',   // if you prefer
		    'ping_status' => 'closed',      // if you prefer
	    ];

    	if ($id) {
    		$data['ID'] = $id;
		    $post_id = wp_update_post($data);
	    } else {

		    $args = array(
			    'post_type' => 'aoat_report',
			    'meta_query' => array(
				    array(
					    'key' => 'form_id',
					    'value' => $formId,
				    )
			    )
		    );
		    $otherReports = new WP_Query($args);
    		if ($otherReports->post_count) {
		        $data['post_status'] = 'draft';
		    } else {
		        $data['post_status'] = 'publish';
		    }

		    $post_id = wp_insert_post($data);
	    }

	    if ($post_id) {
		    // insert post meta
		    update_post_meta($post_id, 'report_data', $request->get_params()['reportData']);
		    update_post_meta($post_id, 'report_settings', $request->get_params()['reportSettings']);
		    update_post_meta($post_id, 'form_id', $formId);
	    }

        return rest_ensure_response( get_post($post_id) );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function create_or_update_report_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
		    return true;
	    }
	    if(current_user_can('editor')) {
		    return true;
	    }

	    return new WP_Error( 403, __( "Permission denied", "apprenticeship-online-assessment-tool" ) );
    }

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function get_report( WP_REST_Request $request ) {
	    $report= get_post($request->get_params()['id']);
	    $report->report_data = get_post_meta($report->ID, 'report_data', true);
	    $report->report_settings = get_post_meta($report->ID, 'report_settings', true);
	    $report->form_id = get_post_meta($report->ID, 'form_id', true);

        return rest_ensure_response( $report );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function get_report_permissions_check( WP_REST_Request $request ) {
	    if(is_user_logged_in()) {
		    return true;
	    }

	    return new WP_Error( 403, __( "Permission denied", "apprenticeship-online-assessment-tool" ) );
    }

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function duplicate_report( WP_REST_Request $request ) {
	    $post_id = $request->get_params()['id'];

	    $new_post = Helper::duplicate_post($post_id, 'draft');

	    return rest_ensure_response( $new_post );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function duplicate_report_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
		    return true;
	    }
	    if(current_user_can('editor')) {
		    return true;
	    }

	    return new WP_Error( 403, __( "Permission denied", "apprenticeship-online-assessment-tool" ) );
    }

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function activate_report( WP_REST_Request $request ) {
	    $post_id = $request->get_params()['id'];
	    $post = get_post($post_id);
	    $formId = get_post_meta($post->ID, 'form_id', true);

	    $args = array(
		    'post_type' => 'aoat_report',
		    'meta_query' => array(
			    array(
				    'key' => 'form_id',
				    'value' => $formId,
			    )
		    )
	    );
	    $otherPosts = new WP_Query($args);

	    foreach ($otherPosts as $otherPost) {
	        $postData = array( 'ID' => $otherPost->ID, 'post_status' => 'draft' );
	        wp_update_post($postData);
	    }


	    $postData = array( 'ID' => $post->ID, 'post_status' => 'publish' );
	    wp_update_post($postData);
	    $args = [
		    'post_type' => 'aoat_report',
		    'meta_key' => 'form_id',
		    'post_status' => 'any',
		    'meta_value' => $formId,
		    'orderby' => 'ID',
		    'order' => 'ASC',
	    ];
	    $posts = get_posts($args);

	    return rest_ensure_response( $posts );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function activate_report_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
		    return true;
	    }
	    if(current_user_can('editor')) {
		    return true;
	    }

	    return new WP_Error( 403, __( "Permission denied", "apprenticeship-online-assessment-tool" ) );
    }

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return array|false|WP_Error|\WP_Post|WP_REST_Response
	 */
    public function delete_report( WP_REST_Request $request ) {
        return wp_delete_post($request->get_params()['id']);
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function delete_report_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
		    return true;
	    }
	    if(current_user_can('editor')) {
		    return true;
	    }

	    return new WP_Error( 403, __( "Permission denied", "apprenticeship-online-assessment-tool" ) );
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return [];
    }
}
