<?php

namespace ApprenticeshipOnlineAssessmentTool;

use WP_Query;

/**
 * Frontend Pages Handler
 */
class Frontend
{
    public function __construct()
    {
        add_shortcode('aoat-form', [$this, 'render_frontend']);
        add_shortcode('aoat-assessment-list', [$this, 'render_assessment_list']);
        add_filter('the_content', [$this, 'aoat_custom_template']);

        add_action('wp_ajax_aoat_upload_file', [$this, 'aoat_upload_file']);
        add_action('wp_ajax_nopriv_aoat_upload_file', [$this, 'aoat_upload_file']);

        add_action('wp_ajax_aoat_send_reviewer_mail', [$this, 'aoat_send_reviewer_mail']);

        add_action('rest_prepare_user', [$this, 'rest_prepare_user'], 10, 3);
    }

    function aoat_custom_template($content)
    {

        wp_enqueue_style('apprenticeship-online-assessment-tool-frontend');
        wp_enqueue_script('apprenticeship-online-assessment-tool-frontend');
        wp_enqueue_script('apprenticeship-online-assessment-tool-frontend-pdf');

        global $post;

        $data = [
            'aoatSaveAssessmentUrl'     => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/create"),
            'aoatGetFormUrl'            => null,
            'ajax_url'                  => admin_url('admin-ajax.php'),
            'aoatGetAssessmentUrl'      => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/"
                                                              . ($post->ID ?: null)),
            'aoatGetAssessmentsUrl'     => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/"),
            'aoatGetLastAssessmentUrl'  => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/last-by-user"),
            'aoatGetExcelAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/get-excel"),
            'aoatGetMediaUrl'           => get_rest_url(null, "/wp/v2/media/"),
            'aoatGetUserUrl'            => get_rest_url(null, "/wp/v2/users/me"),
            'nonce'                     => wp_create_nonce('wp_rest'),
            'aoatGetSettingsUrl'        => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings"),
            'aoatGetReportsUrl'         => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports"),
        ];
        wp_localize_script('apprenticeship-online-assessment-tool-frontend', 'aoat_config', $data);

        /* Checks for single template by post type */
        if ($post->post_type == 'aoat_assessment') {
            $content = '<div id="vue-frontend-app"></div>';
        }

        return $content;
    }

    function aoat_send_reviewer_mail()
    {
        $assessment = get_post($_POST['assessment_id']);
        $review_assessment = get_post($_POST['review_assessment_id']);
        $review_assessment_display_name = get_the_author_meta('display_name', $review_assessment->post_author);

        $email = get_the_author_meta('user_email', $assessment->post_author);
        $display_name = get_the_author_meta('display_name', $assessment->post_author);

        $emailContent = get_option('aoat_email_content');
        $emailSubject = get_option('aoat_email_subject');
        $cc = explode(',', trim(get_option('aoat_review_emails')));

        $emailContent = str_replace('$initialReviewer', $display_name, $emailContent);
        $emailContent = str_replace('$assessment', $assessment->post_title, $emailContent);
        $emailContent = str_replace('$reviewer', $review_assessment_display_name, $emailContent);
        $emailContent = nl2br($emailContent);

        $headers = ['Content-Type: text/html; charset=UTF-8'];

        foreach ($cc as $ccEmail) {
            echo $ccEmail;
            $headers[] = 'Cc: ' . $ccEmail;
        }
        wp_mail($email, $emailSubject, $emailContent, $headers);
        echo 'ok';
        wp_die();
    }

    function aoat_upload_file()
    {
        $uploadedFileId = null;
        foreach ($_FILES as $file => $array) {
            $uploadedFileId = $this->insert_attachment($file, null);
        }
        echo $uploadedFileId;
        wp_die();
    }

    function insert_attachment($file_handler, $post_id)
    {

        // check to make sure its a successful upload
        if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) {
            __return_false();
        }

        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');

        return media_handle_upload($file_handler, $post_id);
    }

    public function render_assessment_list($atts, $content = '')
    {
        $form = get_post($atts['id']);
        $user = wp_get_current_user();

        if (! is_user_logged_in()) {
            return rest_ensure_response([]);
        }
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = [
            'post_type'   => 'aoat_assessment',
            'meta_key'    => 'form_id',
            'post_status' => 'any',
            'meta_value'  => $form->ID,
            'post_author' => $user->ID,
            'paged'       => $paged,
        ];
        $assessments = new WP_Query($args);

        $content = '';
        // Add content if we found posts via our query
        if ($assessments->have_posts()) {

            // Open div wrapper around loop
            $content .= '<div class="aoat-assessment-list">';

            // Loop through posts
            while ($assessments->have_posts()) {

                // Sets up post data so you can use functions like get_the_title(), get_permalink(), etc
                $assessments->the_post();

                $queryParameterKey = get_post_meta(get_the_ID(), 'query_parameter_key', true);

                // This is the output for your entry so what you want to do for each post.
                $content .= '<div><a href="' . get_the_permalink() . '">' . get_the_title() . '</a>';

                $page = get_post_meta($form->ID, 'form_settings', true);

                if (isset($page["pageForm"])) {
                    $content .= ' <a href="'
                                . get_permalink($page["pageForm"]["ID"])
                                . '?edit_assessment='
                                . get_the_ID()
                                . ($queryParameterKey ? '&' . $queryParameterKey . '=1' : '')
                                . '"><button>Edit</button></a>';
                }
                $content .= ' <a href="' . get_delete_post_link() . '"><button>Delete</button></a> </div>';
            }

            // Close div wrapper around loop
            $content .= '
                <div class="aoat-mt-5 nav-previous alignleft">'
                        . get_previous_posts_link('Newer')
                        . '</div>
                <div class="aoat-mt-5 nav-next alignright">'
                        . get_next_posts_link('Older', $assessments->max_num_pages)
                        . '</div>';
            $content .= '</div>';

            // Restore data
            wp_reset_postdata();
        } else {
            $content .= 'No assessments yet.';
        }

        // Return your shortcode output
        return $content;
    }

    /**
     * Render frontend app
     *
     * @param array  $atts
     * @param string $content
     *
     * @return string
     */
    public function render_frontend($atts, $content = '')
    {
        wp_enqueue_style('apprenticeship-online-assessment-tool-frontend');
        wp_enqueue_script('apprenticeship-online-assessment-tool-frontend');
        wp_enqueue_script('apprenticeship-online-assessment-tool-frontend-pdf');
        $data = [
            'upload_url'                => admin_url('async-upload.php'),
            'ajax_url'                  => admin_url('admin-ajax.php'),
            'nonce'                     => wp_create_nonce('wp_rest'),
            'isFilterForExport'         => $atts['filter-for-export'] ?: false,
            'aoatGetFormUrl'            => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/"
                                                              . ($atts['id']
                    ?: null)),
            'aoatSaveAssessmentUrl'     => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/create"),
            'aoatGetLastAssessmentUrl'  => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/last-by-user"),
            'aoatGetExcelAssessmentUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/get-excel"),
            'aoatGetAssessmentsUrl'     => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/assessments/"),
            'aoatGetUserUrl'            => get_rest_url(null, "/wp/v2/users/me?context=edit"),
            'aoatGetSettingsUrl'        => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings"),
        ];
        wp_localize_script('apprenticeship-online-assessment-tool-frontend', 'aoat_config', $data);

        $content .= '<div id="vue-frontend-app"></div>';

        return $content;
    }

    public function rest_prepare_user($response, $user, $request)
    {
        $response->data['first_name'] = get_user_meta($user->ID, 'first_name', true);
        $response->data['last_name'] = get_user_meta($user->ID, 'last_name', true);

        return $response;
    }
}
