<?php
namespace ApprenticeshipOnlineAssessmentTool\Api;

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Assessment extends WP_REST_Controller {

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
            '/assessment/create',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'create_or_update_assessment' ),
                    'permission_callback' => array( $this, 'create_or_update_assessment_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/assessments/(?P<id>\d+)',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_assessment' ),
                    'permission_callback' => array( $this, 'get_assessment_permissions_check' ),
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
    public function create_or_update_assessment( $request ) {
    	$data = array (
		    'post_type' => 'aoat_assessment',
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
	    	$assessmentData = $request->get_params()['assessmentData'];
		    // insert post meta
		    update_post_meta($post_id, 'assessment_data', $assessmentData);
		    foreach($assessmentData as $key => $value) {
		        update_post_meta($post_id, 'assessment_' . $key, $value);
		    }
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
    public function create_or_update_assessment_permissions_check( $request ) {
        return true;
    }
    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_assessment( $request ) {
	    $assessment= get_post($request->get_params()['id']);
	    $assessment->assessment_data = get_post_meta($assessment->ID, 'assessment_data');

        $response = rest_ensure_response( $assessment );

        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_assessment_permissions_check( $request ) {
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
