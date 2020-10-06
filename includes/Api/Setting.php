<?php
namespace ApprenticeshipOnlineAssessmentTool\Api;

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Setting extends WP_REST_Controller {

	public static $available_settings = [
		[
			'label' => 'Max pages:',
			'key'=> 'aoat_max_pages',
			'value'=> 5,
		],
		[
			'label'=> 'Max questions per page:',
            'key'=> 'aoat_max_questions_per_page',
            'value'=> 10,
		],
	];

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
            '/settings/save',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'callback'            => array( $this, 'save_setting' ),
                    'permission_callback' => array( $this, 'save_setting_permissions_check' ),
                    'args'                => $this->get_collection_params(),
                )
            )
        );
        register_rest_route(
            $this->namespace,
            '/settings/',
            array(
                array(
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_settings' ),
                    'permission_callback' => array( $this, 'get_settings_permissions_check' ),
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
    public function save_setting( $request ) {

    	$settings = $request->get_params()['settings'];
    	foreach ($settings as $setting) {
    		if (get_option($setting['key'])) {
				update_option($setting['key'], $setting['value']);
				continue;
		    }
    		add_option($setting['key'], $setting['value']);
	    }

        $response = rest_ensure_response( true );

        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function save_setting_permissions_check( $request ) {
        return true;
    }
    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_settings( $request ) {
    	$settings = [];

    	foreach (self::$available_settings as $available_setting) {
    		$setting = $available_setting;
    		$setting['value'] = get_option($setting['key'], $setting['value']);
		    $settings[] = $setting;
	    }

        $response = rest_ensure_response( $settings );

        return $response;
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_settings_permissions_check( $request ) {
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
