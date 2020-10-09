<?php
namespace ApprenticeshipOnlineAssessmentTool\Api;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST_API Handler
 */
class Form extends WP_REST_Controller {

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
            '/forms/create',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'create_or_update_form' ],
                    'permission_callback' => [ $this, 'create_or_update_form_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/forms/(?P<id>\d+)',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_form' ],
                    'permission_callback' => [ $this, 'get_form_permissions_check' ],
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
    public function create_or_update_form( WP_REST_Request $request ) {
    	$data = [
		    'post_type' => 'aoat_form',
		    'post_title' => $request->get_params()['title'],
		    'post_content' => '',
		    'post_status' => 'publish',
		    'comment_status' => 'closed',   // if you prefer
		    'ping_status' => 'closed',      // if you prefer
	    ];

    	if ($request->get_params()['id']) {
    		$data['ID'] = $request->get_params()['id'];
	    }

    	$post_id = wp_insert_post($data);


	    if ($post_id) {
		    // insert post meta
		    update_post_meta($post_id, 'form_data', $request->get_params()['formData']);
		    update_post_meta($post_id, 'form_settings', $request->get_params()['formSettings']);
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
    public function create_or_update_form_permissions_check( WP_REST_Request $request ) {
        return true;
    }

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
    public function get_form( WP_REST_Request $request ) {
	    $form= get_post($request->get_params()['id']);
	    $form->form_data = get_post_meta($form->ID, 'form_data');
	    $form->form_settings = get_post_meta($form->ID, 'form_settings');
	    $args = [
		    'post_type' => 'aoat_report',
		    'meta_key' => 'form_id',
		    'meta_value' => $form->ID,
	    ];
	    $query = get_posts($args);

	    $form->reports = $query;
	    $form->settings = [];

	    foreach (Setting::$available_settings as $setting) {
	    	$form->settings[$setting['key']] = (int)get_option($setting['key'], $setting['value']);
	    }

        return rest_ensure_response( $form );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function get_form_permissions_check( WP_REST_Request $request ) {
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
