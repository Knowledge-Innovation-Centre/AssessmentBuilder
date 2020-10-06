<?php
namespace ApprenticeshipOnlineAssessmentTool;

/**
 * Admin Pages Handler
 */
class Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register our menu page
     *
     * @return void
     */
    public function admin_menu() {
        global $submenu;

        $capability = 'manage_options';
        $slug       = 'apprenticeship-online-assessment-tool';

        $hook = add_menu_page( __( 'Assessment Tool', 'apprenticeship-online-assessment-tool' ), __( 'Assessment Tool', 'apprenticeship-online-assessment-tool' ), $capability, $slug, [ $this, 'plugin_page' ], 'dashicons-text' );

        if ( current_user_can( $capability ) ) {
            $submenu[ $slug ][] = array( __( 'Add form', 'apprenticeship-online-assessment-tool' ), $capability, 'admin.php?page=' . $slug . '#/forms/create' );
            $submenu[ $slug ][] = array( __( 'Settings', 'apprenticeship-online-assessment-tool' ), $capability, 'admin.php?page=' . $slug . '#/settings' );
        }

        add_action( 'load-' . $hook, [ $this, 'init_hooks'] );
    }

    /**
     * Initialize our hooks for the admin page
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Load scripts and styles for the app
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'apprenticeship-online-assessment-tool-admin' );
        wp_enqueue_style( 'apprenticeship-online-assessment-tool-admin-extend' );
        wp_enqueue_script( 'apprenticeship-online-assessment-tool-admin' );


	    $data = array(
	    	'aoatGetFormUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/"),
	    	'aoatSaveFormUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/create"),
	    	'aoatViewFormUrl' => admin_url('admin.php?page=apprenticeship-online-assessment-tool#/forms/'),
	        'aoatGetReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/"),
	    	'aoatSaveReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/create"),
	    	'aoatViewReportUrl' => admin_url('admin.php?page=apprenticeship-online-assessment-tool#/reports/'),
	    	'aoatDeleteReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/"),
	    	'aoatSaveSettingsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings/save"),
	    	'aoatGetSettingsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings"),
	    );
	    wp_localize_script( 'apprenticeship-online-assessment-tool-admin', 'aoat_config', $data );
    }

    /**
     * Render our admin page
     *
     * @return void
     */
    public function plugin_page() {
        echo '<div class="wrap"><div id="vue-admin-app"></div></div>';
    }
}
