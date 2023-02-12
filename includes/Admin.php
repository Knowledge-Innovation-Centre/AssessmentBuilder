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

        add_filter( 'manage_aoat_assessment_posts_columns', [ $this, 'quick_edit_custom_posts_columns'] );
        add_action( 'manage_aoat_assessment_posts_custom_column', [ $this,'quick_edit_custom_column_display'], 10, 2 );

        add_action( 'quick_edit_custom_box',  [ $this,'quick_edit_fields'], 10, 2 );

        add_action( 'save_post', [ $this,'quick_edit_save_post'], 10, 2 );

        add_action( 'admin_print_footer_scripts-edit.php', [ $this,'quick_edit_javascript'] );
        add_filter('post_row_actions', [ $this,'quick_edit_set_data'], 10, 2);
    }

    public function quick_edit_custom_posts_columns( $posts_columns ) {
        $posts_columns['query_parameter_key'] = __( 'Query parameter key:', 'apprenticeship-online-assessment-tool' );
        return $posts_columns;
    }

    public function quick_edit_custom_column_display( $column_name, $post_id ) {
        if ( 'query_parameter_key' == $column_name ) {
            $time_recorded = get_post_meta( $post_id, 'query_parameter_key', true );

            if ( $time_recorded || $time_recorded === '0' ) {
                echo esc_html( $time_recorded );
            } else {
                esc_html_e( 'N/A', 'apprenticeship-online-assessment-tool' );
            }
        }
    }

    public function quick_edit_fields( $column_name, $post_type ) {
        if ( 'query_parameter_key' != $column_name ){
            return;
        }
        if ( 'aoat_assessment' != $post_type ){
            return;
        }

        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php esc_html_e( 'Query parameter key:', 'apprenticeship-online-assessment-tool'); ?></span>
                    <span class="input-text-wrap">
                    <input type="text" value="" name="query_parameter_key" class="text_review_key" >
                </span>
                </label>
            </div>
        </fieldset>
        <?php
    }
    public function quick_edit_save_post( $post_id, $post ) {
        // if called by autosave, then bail here
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return;

        // if this "post" post type?
        if ( $post->post_type != 'aoat_assessment' )
            return;

        // does this user have permissions?
        if ( ! current_user_can( 'edit_post', $post_id ) )
            return;

        // update!
        if ( isset( $_POST['query_parameter_key'] ) ) {
            update_post_meta( $post_id, 'query_parameter_key', $_POST['query_parameter_key'] );
        }
    }

    public function quick_edit_javascript() {
        $current_screen = get_current_screen();

        if ( $current_screen->id != 'edit-aoat_assessment' || $current_screen->post_type != 'aoat_assessment' ) {
            return;
        }

        // Ensure jQuery library loads
        wp_enqueue_script( 'jquery' );
        ?>
        <script type="text/javascript">
            jQuery( function( $ ) {
                console.log('zun');
                $( '#the-list' ).on( 'click', '.editinline', function( e ) {
                    inlineEditPost.revert();
                    const data = $(this).data( 'query_parameter_key' );

                        $( '.text_review_key' ).val(data );

                });
            });
        </script>
        <?php
    }
    public function quick_edit_set_data( $actions, $post ) {
        $found_value = get_post_meta( $post->ID, 'query_parameter_key', true );

        if ( $found_value || $found_value === '0') {
            if ( isset( $actions['inline hide-if-no-js'] ) ) {
                $new_attribute = sprintf( 'data-query_parameter_key="%s"', esc_attr( $found_value ) );
                $actions['inline hide-if-no-js'] = str_replace( 'class=', "$new_attribute class=", $actions['inline hide-if-no-js'] );
            }
        }

        return $actions;
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
	    	'aoatGetFormsUrl' => get_rest_url(null, "/apprenticeship-online-assessment-tool/v1/forms/"),
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
