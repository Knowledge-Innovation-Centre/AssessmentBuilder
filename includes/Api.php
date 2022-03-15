<?php
namespace ApprenticeshipOnlineAssessmentTool;

use WP_REST_Controller;

/**
 * REST_API Handler
 */
class Api extends WP_REST_Controller {

    /**
     * [__construct description]
     */
    public function __construct() {
        $this->includes();

        add_action( 'rest_api_init', [ $this, 'register_routes' ] );
    }

    /**
     * Include the controller classes
     *
     * @return void
     */
    private function includes() {
        if ( !class_exists( __NAMESPACE__ . '\Api\Form'  ) ) {
            require_once __DIR__ . '/Api/Form.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Report'  ) ) {
            require_once __DIR__ . '/Api/Report.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Assessment'  ) ) {
            require_once __DIR__ . '/Api/Assessment.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\Setting'  ) ) {
            require_once __DIR__ . '/Api/Setting.php';
        }
        if ( !class_exists( __NAMESPACE__ . '\Api\SubsetItem'  ) ) {
            require_once __DIR__ . '/Api/SubsetItem.php';
        }
    }

    /**
     * Register the API routes
     *
     * @return void
     */
    public function register_routes() {
        (new Api\Form())->register_routes();
        (new Api\Assessment())->register_routes();
        (new Api\Report())->register_routes();
        (new Api\Setting())->register_routes();
        (new Api\SubsetItem())->register_routes();
    }

}
