<?php
namespace ApprenticeshipOnlineAssessmentTool;

require_once __DIR__ . '/Helper.php';

/**
 * Admin Pages Handler
 */
class Admin {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );


	    add_action( 'admin_action_aoat_duplicate_form', [ $this, 'duplicate_form'] );
	    add_filter( 'post_row_actions', [ $this, 'duplicate_form_link'], 10, 2 );
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
            $submenu[ $slug ][] = [ __( 'Add form', 'apprenticeship-online-assessment-tool' ), $capability, 'admin.php?page=' . $slug . '#/forms/create' ];
            $submenu[ $slug ][] = [ __( 'Reports', 'apprenticeship-online-assessment-tool' ), $capability, 'admin.php?page=' . $slug . '#/reports' ];
            $submenu[ $slug ][] = [ __( 'Settings', 'apprenticeship-online-assessment-tool' ), $capability, 'admin.php?page=' . $slug . '#/settings' ];
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


	    $data = [
		    'ajax_url'   => admin_url('admin-ajax.php'),
		    'nonce'      => wp_create_nonce('wp_rest'),
	    	'aoatGetFormUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/"),
	    	'aoatSaveFormUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/create"),
	    	'aoatViewFormUrl' => admin_url('admin.php?page=apprenticeship-online-assessment-tool#/forms/'),
	        'aoatGetReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/"),
	    	'aoatSaveReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/create"),
	    	'aoatViewReportUrl' => admin_url('admin.php?page=apprenticeship-online-assessment-tool#/reports/'),
	    	'aoatDeleteReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/"),
	    	'aoatDuplicateReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/duplicate/"),
	    	'aoatActivateReportUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/reports/activate/"),
	    	'aoatSaveSettingsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings/save"),
	    	'aoatGetSettingsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings"),
	    	'aoatGetPagesUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/settings/pages?per_page=3"),
            'aoatGetSubsetItemsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/subset_items"),
	    ];
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

	function duplicate_form(){
		if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'aoat_duplicate_form' == $_REQUEST['action'] ) ) ) {
			wp_die('No post to duplicate has been supplied!');
		}

		/*
		 * Nonce verification
		 */
		if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) )
			return;

		/*
		 * get the original post id
		 */
		$post_id = (isset($_GET['post']) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$new_post = Helper::duplicate_post($post_id);

		/*
		 * finally, redirect to the edit post screen for the new draft
		 */
		wp_redirect( admin_url('admin.php?page=apprenticeship-online-assessment-tool#/forms/' . $new_post->ID) );
		exit;
	}

	/*
	 * Add the duplicate link to action list for post_row_actions
	 */
	function duplicate_form_link( $actions, $post ) {
		if (current_user_can('editor') || current_user_can('administrator')) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=aoat_duplicate_form&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}
		return $actions;
	}
}
