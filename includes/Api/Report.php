<?php
namespace ApprenticeshipOnlineAssessmentTool\Api;

use WP_REST_Controller;

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
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_or_update_report' ),
                    'permission_callback' => array( $this, 'create_or_update_report_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/reports/(?P<id>\d+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_report' ),
                    'permission_callback' => array( $this, 'get_report_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_or_update_report( $request ) {
    	$data = array (
		    'post_type' => 'aoat_report',
		    'post_title' => $request->get_params()['title'],
		    'post_content' => '',
		    'post_status' => 'publish',
		    'comment_status' => 'closed',   // if you prefer
		    'ping_status' => 'closed',      // if you prefer
	    );

    	if ($request->get_params()['id']) {
    		$data['ID'] = $request->get_params()['id'];
	    }

    	$post_id = wp_insert_post($data);


	    if ($post_id) {
		    // insert post meta
		    update_post_meta($post_id, 'report_data', $request->get_params()['reportData']);
		    update_post_meta($post_id, 'report_settings', $request->get_params()['reportSettings']);
		    update_post_meta($post_id, 'form_id', $request->get_params()['formId']);
	    }

        $response = rest_ensure_response( get_post($post_id) );

        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function create_or_update_report_permissions_check( $request ) {
        return true;
    }
    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_report( $request ) {
	    $report= get_post($request->get_params()['id']);
	    $report->report_data = get_post_meta($report->ID, 'report_data');
	    $report->report_settings = get_post_meta($report->ID, 'report_settings');
	    $report->form_id = get_post_meta($report->ID, 'form_id');

        $response = rest_ensure_response( $report );

        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_report_permissions_check( $request ) {
        return true;
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
