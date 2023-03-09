<?php

namespace ApprenticeshipOnlineAssessmentTool\Api;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use WP_Error;
use WP_Post;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * REST_API Handler
 */
class Assessment extends WP_REST_Controller
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
    public function create_or_update_assessment(WP_REST_Request $request)
    {
        $data = [
            'post_type'      => 'aoat_assessment',
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
            $assessmentData = $request->get_params()['assessmentData'];
            // insert post meta
            update_post_meta($post_id, 'assessment_data', $assessmentData);
            update_post_meta($post_id, 'form_id', $request->get_params()['formId']);
            update_post_meta($post_id, 'query_parameter_key', $request->get_params()['queryParameterKey']);
            update_post_meta($post_id, 'loc_filter', $request->get_params()['locFilter']);
            foreach ($assessmentData as $key => $value) {
                update_post_meta($post_id, 'assessment_' . $key, $value);
            }
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
    public function create_or_update_assessment_permissions_check(WP_REST_Request $request)
    {

        if (current_user_can('administrator')) {
            return true;
        }
        if (current_user_can('editor')) {
            return true;
        }
        if (current_user_can('author')) {
            return true;
        }
        if (! is_user_logged_in()) {
            return new WP_Error(403, __("User not logged in", "apprenticeship-online-assessment-tool"));
        }

        return true;
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return array|false|WP_Error|WP_Post|WP_REST_Response
     */
    public function delete_assessment(WP_REST_Request $request)
    {
        return wp_delete_post($request->get_params()['id']);
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function delete_assessment_permissions_check(WP_REST_Request $request)
    {
        if (current_user_can('administrator')) {
            return true;
        }
        if (current_user_can('editor')) {
            return true;
        }
        if (current_user_can('author')) {
            return true;
        }
        if (! is_user_logged_in()) {
            return new WP_Error(403, __("User not logged in", "apprenticeship-online-assessment-tool"));
        }
        $post = get_post($request->get_params()['id']);

        return $post->post_author == get_current_user_id();
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_assessment(WP_REST_Request $request)
    {
        $assessment = get_post($request->get_params()['id']);
        $assessment->assessment_data = get_post_meta($assessment->ID, 'assessment_data', true);

        $formId = get_post_meta($assessment->ID, 'form_id', true);

        $args = [
            'post_type'  => 'aoat_report',
            'meta_key'   => 'form_id',
            'meta_value' => $formId,
        ];
        $query = get_posts($args);

        $assessment->report = $query[0] ?? null;

        $args = [
            'post_type' => 'aoat_form',
            'ID'        => $formId,
        ];
        $query = get_posts($args);

        $assessment->form = $query[0] ?? null;
        if ($assessment->report) {
            $assessment->report->report_data = get_post_meta($assessment->report->ID, 'report_data', true);
        }
        if ($assessment->form) {
            $assessment->form->form_data = get_post_meta($assessment->form->ID, 'form_data', true);
            $assessment->form->form_settings = get_post_meta($assessment->form->ID, 'form_settings', true);
        }

        return rest_ensure_response($assessment);
    }

    /**
     * Checks if a given request has access to read the items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_assessment_permissions_check(WP_REST_Request $request)
    {
        if (current_user_can('administrator')) {
            return true;
        }
        if (current_user_can('editor')) {
            return true;
        }
        if (current_user_can('author')) {
            return true;
        }
        if (! is_user_logged_in()) {
            return new WP_Error(403, __("User not logged in", "apprenticeship-online-assessment-tool"));
        }
        $post = get_post($request->get_params()['id']);

        return $post->post_author == get_current_user_id();
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_assessments(WP_REST_Request $request)
    {

        $formId = get_post_meta($request->get_params()['assessment_id'], 'form_id');

        $assessments = get_posts([
            'post_type'   => 'aoat_assessment',
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'meta_key'    => 'form_id',
            'meta_value'  => $formId,
        ]);

        foreach ($assessments as &$assessment) {
            $assessment->assessment_data = get_post_meta($assessment->ID, 'assessment_data', true);
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
    public function get_excel_assessment(WP_REST_Request $request)
    {
        $assessment_data_filter = $request->get_params()['assessmentData'];

        $args = [
            'post_type'      => 'aoat_form',
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'posts_per_page' => -1,
        ];
        $forms = get_posts($args);

        $filtered_forms = [];
        foreach ($forms as $form) {
            $form->form_settings = get_post_meta($form->ID, 'form_settings', true);

            if ($form->form_settings['enableExcelExport'] ?? false) {
                $filtered_forms[] = $form;
            }
        }

        usort($filtered_forms, function ($a, $b) {
            $exportSortA = $a->form_settings['exportSort'] ?? 0;
            $exportSortB = $b->form_settings['exportSort'] ?? 0;
            if ($exportSortA == $exportSortB) {
                return 0;
            }

            return ($exportSortA < $exportSortB) ? -1 : 1;
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $writer = new Xlsx($spreadsheet);
        $sheet->setCellValue('A1', 'Dimension info');
        $sheet->setCellValue('F1', 'Questions info');
        $sheet->setCellValue('A2', 'DimensionID');
        $sheet->setCellValue('B2', 'DimensionLabel/PageLabel');
        $sheet->setCellValue('C2', 'RelatedtoDim');
        $sheet->setCellValue('D2', 'Exportable (Y/N)');
        $sheet->setCellValue('E2', 'Num of Questions?');
        $sheet->setCellValue('F2', 'QuestionID');
        $sheet->setCellValue('G2', 'QRelatedtoD');

        $sheet->setCellValue('H2', 'QRelatedtoQ');
        $sheet->setCellValue('I2', 'QRelatedtoRole');
        $sheet->setCellValue('J2', 'QuestionType');
        $sheet->setCellValue('K2', 'QuestionLabel');
        $sheet->setCellValue('L2', 'Value');

        $form_key = 0;
        $row = 2;
        $filename = '';
        foreach ($filtered_forms as $filtered_form) {
            $form_key++;
            $this->addAssessmentDataToSheet($sheet, $filtered_form, $form_key, $row, $assessment_data_filter, $filename);
        }

        $filenameForSave = wp_upload_dir()['path'] . $filename;
        $filenameForDownload = wp_upload_dir()['url'] . $filename;
        $writer->save($filenameForSave);

        return rest_ensure_response($filenameForDownload);
    }

    private function addAssessmentDataToSheet($sheet, $form, $form_key, &$row, $assessment_data_filter, &$title)
    {
        $relatedToDimensions = 'All';
        $additionalForms = $form->form_settings['additionalForms'] ?? [];

        $flatListQuestions = [];
        if (count($additionalForms)) {
            $relatedToDimensions = '';
            foreach ($additionalForms as $additionalForm) {
                $form_data = get_post_meta($additionalForm['ID'], 'form_data', true);
                $this->getFlatListQuestions($form_data['items'], $flatListQuestions, 0, 'All');
                $relatedToDimensions .= $additionalForm['post_title'] . ', ';
            }
            $relatedToDimensions .= $form->post_title;
        }

        $args = [
            'post_type'      => 'aoat_assessment',
            'meta_key'       => 'form_id',
            'meta_value'     => $form->ID,
            'orderby'        => 'ID',
            'order'          => 'DESC',
            'posts_per_page' => -1,
        ];
        $assessments = get_posts($args);

        $form_data = get_post_meta($form->ID, 'form_data', true);

        $this->getFlatListQuestions($form_data['items'], $flatListQuestions);
        $firstAssessment = true;
        foreach ($assessments as $assessment) {
            $assessments_data_item = get_post_meta($assessment->ID, 'assessment_data', true);

            foreach ($assessment_data_filter as $filterItemKey => $filterItem) {
                if (! isset($assessments_data_item[$filterItemKey])) {
                    continue 2;
                }
                if ($assessments_data_item[$filterItemKey] != $filterItem) {
                    continue 2;
                }
            }
            if ($firstAssessment) {
                $firstAssessment = false;
                $title = $this->getExportTitle($flatListQuestions, $assessment_data_filter, $assessment);
            }
            $firstRow = true;
            foreach ($flatListQuestions as $flatListQuestion) {
                $row++;
                $sheet->setCellValue('A' . $row, $form_key);
                $sheet->setCellValue('B' . $row, $flatListQuestion['pageLabel'] ?: $form->post_title
                                                                                   . ' - page '
                                                                                   . $flatListQuestion['pageNumber']);
                $sheet->setCellValue('C' . $row, $relatedToDimensions);
                $sheet->setCellValue('D' . $row, ' ');
                $sheet->setCellValue('E' . $row, $firstRow ? count($flatListQuestions) : ' ');
                $sheet->setCellValue('F' . $row, $flatListQuestion['name']);
                $sheet->setCellValue('G' . $row, $form->post_title);

                $sheet->setCellValue('H' . $row, $this->getRelatedQuestions($flatListQuestion));
                $sheet->setCellValue('I' . $row, $this->getConditions($flatListQuestion));
                $sheet->setCellValue('J' . $row, $flatListQuestion['type']);
                $sheet->setCellValue('K' . $row, $this->getLabel($flatListQuestion));
                //$sheet->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                $sheet->setCellValue('L' . $row, $this->getValue($flatListQuestion, $assessments_data_item));
                $firstRow = false;
            }
        }
    }

    private function getFlatListQuestions($form_data_items, &$flatListQuestions, $currentPage = 0, $pageLabel = null)
    {
        foreach ($form_data_items as $form_data_item) {
            if ($form_data_item['disableExportExcel'] ?? false) {
                continue;
            }
            if (isset($form_data_item['items']) && count($form_data_item['items'])) {

                if ($form_data_item['type'] == 'page') {
                    $currentPage++;
                }
                $this->getFlatListQuestions($form_data_item['items'], $flatListQuestions, $currentPage, $pageLabel);
                continue;
            }
            $form_data_item['pageNumber'] = $currentPage;
            $form_data_item['pageLabel'] = $pageLabel;
            $flatListQuestions[] = $form_data_item;
        }
    }

    private function getExportTitle($flatListQuestions, $assessment_data_filter, $assessment)
    {
        $title = ['/AssessmentExport'];
        foreach ($assessment_data_filter as $filterItemKey => $filterItem) {
            foreach ($flatListQuestions as $flatListQuestion) {
                if ($flatListQuestion['key'] == $filterItemKey) {
                    foreach ($flatListQuestion['options'] ?? [] as $option) {
                        if ($option['id'] === $filterItem) {
                            $title[] = $option['name'];
                            break;
                        }
                    }
                }
            }
        }
        $title[] = date('d.m.Y H:i:s');
        $title[] = '.xlsx';

        return implode('-', $title);
    }

    private function getRelatedQuestions($flatListQuestion)
    {

        $relatedQuestions = [];
        foreach ($flatListQuestion['relatedQuestions'] ?? [] as $relatedQuestion) {
            $relatedQuestions[] = $relatedQuestion['name'];
        }

        if (! count($relatedQuestions)) {
            return ' ';
        }

        return implode(',', $relatedQuestions);
    }

    private function getConditions($flatListQuestion)
    {

        $conditions = [];
        foreach ($flatListQuestion['conditions'] ?? [] as $condition) {
            foreach ($condition['selectedOptions'] ?? [] as $selectedOption) {
                $conditions[] = $selectedOption['name'];
            }
        }

        if (! count($conditions)) {
            return ' ';
        }

        return implode(',', $conditions);
    }

    private function getLabel($flatListQuestion)
    {
        $returnValue = $flatListQuestion['label'];
        if ($flatListQuestion['type'] == 'radio_grid') {
            foreach ($flatListQuestion['optionsHorizontal'] as $optionHorizontal) {
                $returnValue .= "\n" . $optionHorizontal['name'];
            }
        }

        return $returnValue;
    }

    private function getValue($flatListQuestion, $assessment_data)
    {
        if ($flatListQuestion['hideValuesInExportExcel'] ?? false) {
            return ' ';
        }

        $value = $assessment_data[$flatListQuestion['key']] ?? null;
        if ($value) {
            if (is_array($value)) {
                return implode(',', array_values($value));
            }

            return $value;
        }

        return ' ';
    }

    /**
     * Retrieves a collection of items.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_last_user_assessment(WP_REST_Request $request)
    {

        $args = [
            'post_type'  => 'aoat_assessment',
            'meta_key'   => 'form_id',
            'meta_value' => $request->get_params()['form_id'],
            'orderby'    => 'ID',
            'order'      => 'DESC',
            'author'     => get_current_user_id(),
        ];
        $query = get_posts($args);

        $assessment = $query[0] ?? null;

        if (! $assessment) {
            return rest_ensure_response(null);
        }

        $assessment->assessment_data = get_post_meta($assessment->ID, 'assessment_data', true);

        return rest_ensure_response($assessment);
    }

    /**
     * Register the routes
     *
     * @return void
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/assessments/create', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'create_or_update_assessment'],
                'permission_callback' => [$this, 'create_or_update_assessment_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/assessments', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_assessments'],
                'permission_callback' => [$this, 'get_assessment_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/assessments/last-by-user', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_last_user_assessment'],
                'permission_callback' => [$this, 'get_assessment_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/assessments/get-excel', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => [$this, 'get_excel_assessment'],
                'permission_callback' => [$this, 'get_assessment_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/assessments/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_assessment'],
                'permission_callback' => [$this, 'get_assessment_permissions_check'],
                'args'                => $this->get_collection_params(),
            ],
        ]);
        register_rest_route($this->namespace, '/assessments/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [$this, 'delete_assessment'],
                'permission_callback' => [$this, 'delete_assessment_permissions_check'],
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

    private function getHorizontalValue($flatListQuestion, $assessment_data)
    {
        if ($flatListQuestion['hideValuesInExportExcel'] ?? false) {
            return ' ';
        }

        $value = $assessment_data[$flatListQuestion['key']] ?? null;
        if ($value) {
            if (is_array($value)) {
                return implode(',', array_values($value));
            }

            return $value;
        }

        return ' ';
    }

    private function getValues($flatListQuestion, $assessments_data)
    {
        if ($flatListQuestion['hideValuesInExportExcel'] ?? false) {
            return ' ';
        }
        $values = [];
        foreach ($assessments_data as $assessment_data) {
            $value = $assessment_data[$flatListQuestion['key']] ?? null;
            if ($value) {
                if (is_array($value)) {
                    $values[] = implode(',', array_values($value));
                    continue;
                }
                $values[] = $value;
            }
        }

        return implode(',', $values);
    }
}
