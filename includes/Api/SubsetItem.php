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
class SubsetItem extends WP_REST_Controller {

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
            '/subset_items',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_subset_items' ],
                    'permission_callback' => [ $this, 'get_subset_item_permissions_check' ],
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
    public function get_subset_items( WP_REST_Request $request ) {
        $search = $request->get_params()['search'];

	    $subset_items= get_posts([
	        'post_type' => 'loc_subset_item',
            'numberposts'       => 20,
            'orderby' => 'date',
            'order'   => 'DESC',
            's' => $search,
            ]);


        return rest_ensure_response( $subset_items );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function get_subset_item_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
		    return true;
	    }
	    if(current_user_can('editor')) {
		    return true;
	    }
        if(current_user_can('author')) {
            return true;
        }
	    if (!is_user_logged_in()) {
		    return new WP_Error( 403, __( "User not logged in", "apprenticeship-online-subset_item-tool" ) );
	    }
	    $post = get_post($request->get_params()['id']);

	    return $post->post_author == get_current_user_id();
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
