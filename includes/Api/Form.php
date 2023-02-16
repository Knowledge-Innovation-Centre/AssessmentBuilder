<?php

namespace ApprenticeshipOnlineAssessmentTool\Api;

use WP_Error;
use WP_Query;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST_API Handler
 */
class Form extends WP_REST_Controller
{
    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->namespace = 'apprenticeship-online-assessment-tool/v1';
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function create_or_update_form(WP_REST_Request $request)
    {
        $data = [
            'post_type'      => 'aoat_form',
            'post_title'     => $request->get_params()['title'],
            'post_content'   => '',
            'post_status'    => 'publish',
            'comment_status' => 'closed',   // if you prefer
            'ping_status'    => 'closed',      // if you prefer
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

        return rest_ensure_response(get_post($post_id));
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function create_or_update_form_permissions_check(WP_REST_Request $request)
    {
        if (current_user_can('administrator')) {
            return true;
        }
        if (current_user_can('editor')) {
            return true;
        }

        return new WP_Error(403, __("Permission denied", "apprenticeship-online-assessment-tool"));
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_form(WP_REST_Request $request)
    {
        $form = get_post($request->get_params()['id']);
        $form->form_data = get_post_meta($form->ID, 'form_data', true);
        $form->form_settings = get_post_meta($form->ID, 'form_settings', true);
        $args = [
            'post_type'   => 'aoat_report',
            'meta_key'    => 'form_id',
            'post_status' => 'any',
            'meta_value'  => $form->ID,
            'orderby'     => 'ID',
            'order'       => 'ASC',
        ];
        $query = get_posts($args);

        $form->reports = $query;

        return rest_ensure_response($form);
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_form_assessments(WP_REST_Request $request)
    {
        $form = get_post($request->get_params()['id']);

        $page = $request->get_params()['page'] ?? 1;
        $posts_per_page = $request->get_params()['posts_per_page'] ?? -1;

        $args = [
            'paged'          => $page,
            'posts_per_page' => $posts_per_page,
            'post_type'      => 'aoat_assessment',
            'meta_key'       => 'form_id',
            'post_status'    => 'any',
            'meta_value'     => $form->ID,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ];
        $assessments = get_posts($args);

        foreach ($assessments as $assessment) {
            $assessment->assessment_data = get_post_meta($assessment->ID, 'assessment_data', true);

            $assessment->user = 'No user';
            if ($assessment->post_author && $user = get_userdata($assessment->post_author)) {
                $assessment->user = $user->first_name . ' ' . $user->last_name;
            }
        }

        return rest_ensure_response($assessments);
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_form_assessments_count(WP_REST_Request $request)
    {
        $form = get_post($request->get_params()['id']);

        $args = [
            'post_type'      => 'aoat_assessment',
            'meta_key'       => 'form_id',
            'post_status'    => 'any',
            'meta_value'     => $form->ID,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'posts_per_page' => -1,
        ];
        $assessments = new WP_Query($args);

        return rest_ensure_response($assessments->post_count);
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_form_permissions_check(WP_REST_Request $request)
    {
        if (is_user_logged_in()) {
            return true;
        }

        return new WP_Error(403, __("Permission denied", "apprenticeship-online-assessment-tool"));
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_forms(WP_REST_Request $request)
    {

        $posts_per_page = $request->get_params()['posts_per_page'] ?? 10;
        $args = [
            'post_type'      => 'aoat_form',
            'post_status'    => 'any',
            'posts_per_page' => $posts_per_page,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ];
        $forms = get_posts($args);

        foreach ($forms as $key => $form) {
            $forms[$key]->form_data = get_post_meta($form->ID, 'form_data', true);
            $forms[$key]->form_settings = get_post_meta($form->ID, 'form_settings', true);
        }

        return rest_ensure_response($forms);
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_forms_permissions_check(WP_REST_Request $request)
    {
        if (is_user_logged_in()) {
            return true;
        }

        return new WP_Error(403, __("Permission denied", "apprenticeship-online-assessment-tool"));
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/forms/create', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_or_update_form'],
                'permission_callback' => [$this, 'create_or_update_form_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/forms/', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_forms'],
                'permission_callback' => [$this, 'get_forms_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_form'],
                'permission_callback' => [$this, 'get_form_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)/assessments', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_form_assessments'],
                'permission_callback' => [$this, 'get_form_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)/assessments_count', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_form_assessments_count'],
                'permission_callback' => [$this, 'get_form_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
    }

    /**
     * Retrieves the query params for the items collection.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params()
    {
        return [];
    }
}
