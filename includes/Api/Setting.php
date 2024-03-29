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
		[
			'label'=> 'Max items per column:',
            'key'=> 'aoat_max_items_per_column',
            'value'=> 4,
		],
		[
			'label'=> 'Page for assessments',
            'key'=> 'aoat_page_for_assessments',
            'value'=> null,
		],
		[
			'label'=> 'After submitting the assessment redirect user to the list of assessment (If checked, please select page for assessment)',
            'key'=> 'aoat_redirect_after_completion',
            'value'=> false,
		],
		[
			'label'=> 'Show link for assessment list (If checked, please select page for assessment)',
            'key'=> 'aoat_show_link_button',
            'value'=> false,
		],
        [
            'label'=> 'Use LOC',
            'key'=> 'aoat_use_loc',
            'value'=> false,
        ],
		[
			'label'=> 'Available colors:',
            'key'=> 'available_colors',
            'value'=> [
	            "#1FBC9C",
	            "#1CA085",
	            "#2ECC70",
	            "#27AF60",
	            "#3398DB",
	            "#2980B9",
	            "#A463BF",
	            "#8E43AD",
	            "#F2C511",
	            "#F39C19",
	            "#E84B3C",
	            "#C0382B",
	            "#DDE6E8",
	            "#BDC3C8",
	            "#3D556E",
	            "",
            ],
		],
        [
            'label' => 'Review notification emails (split by comma):',
            'key'=> 'aoat_review_emails',
            'value'=> '',
        ],
        [
            'label' => 'Review email subject:',
            'key'=> 'aoat_email_subject',
            'value'=> '',
        ],
        [
            'label' => 'Review email content (reserved words: $initialReviewer, $assessment, $reviewer):',
            'key'=> 'aoat_email_content',
            'value'=> '',
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
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [ $this, 'save_setting' ],
                    'permission_callback' => [ $this, 'save_setting_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/settings/',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_settings' ],
                    'permission_callback' => [ $this, 'get_settings_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ]
            ]
        );
        register_rest_route(
            $this->namespace,
            '/settings/pages',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_pages' ],
                    'permission_callback' => [ $this, 'get_pages_permissions_check' ],
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
    public function save_setting( WP_REST_Request $request ) {

    	$settings = $request->get_params()['settings'];
    	foreach ($settings as $setting) {
    		update_option($setting['key'], $setting['value']);
	    }

        return rest_ensure_response( true );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function save_setting_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
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
    public function get_settings( WP_REST_Request $request ) {
    	$settings = [];

    	foreach (self::$available_settings as $available_setting) {
    		$setting = $available_setting;
    		$setting['value'] = get_option($setting['key'], $setting['value']);
		    $settings[] = $setting;
	    }

        return rest_ensure_response( $settings );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function get_settings_permissions_check( WP_REST_Request $request ) {
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
    public function get_pages( WP_REST_Request $request ) {
        return rest_ensure_response( get_pages(['number' => 100]) );
    }

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
    public function get_pages_permissions_check( WP_REST_Request $request ) {
	    if(current_user_can('administrator')) {
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
