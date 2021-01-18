<?php
/*
Plugin Name: ApprenticeshipQ Online Assessment Tool
Plugin URI: https://knowledgeinnovation.eu/
Description: A WordPress ApprenticeshipQ Online Assessment Tool plugin
Version: 0.1
Author: Jure Jager
Author URI: https://jager-web.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: apprenticeship-online-assessment-tool
Domain Path: /languages
*/

/**
 * Copyright (c) YEAR Your Name (email: Email). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Apprenticeship_Online_Assessment_Tool class
 *
 * @class Apprenticeship_Online_Assessment_Tool The class that holds the entire Apprenticeship_Online_Assessment_Tool plugin
 */
final class Apprenticeship_Online_Assessment_Tool {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '1.2.0';

    /**
     * Holds various class instances
     *
     * @var array
     */
    private $container = array();

    /**
     * Constructor for the Apprenticeship_Online_Assessment_Tool class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    /**
     * Initializes the Apprenticeship_Online_Assessment_Tool() class
     *
     * Checks for an existing Apprenticeship_Online_Assessment_Tool() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Apprenticeship_Online_Assessment_Tool();
        }

        return $instance;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
        }

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     *
     * @return mixed
     */
    public function __isset( $prop ) {
        return isset( $this->{$prop} ) || isset( $this->container[ $prop ] );
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_VERSION', $this->version );
        define( 'APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_FILE', __FILE__ );
        define( 'APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_PATH', dirname( APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_FILE ) );
        define( 'APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_INCLUDES', APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_PATH . '/includes' );
        define( 'APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_URL', plugins_url( '', APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_FILE ) );
        define( 'APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS', APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_URL . '/assets' );
    }

    /**
     * Load the plugin after all plugis are loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        $installed = get_option( 'apprenticeship_online_assessment_tool_installed' );

        if ( ! $installed ) {
            update_option( 'apprenticeship_online_assessment_tool_installed', time() );
        }

        update_option( 'apprenticeship_online_assessment_tool_version', APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {
    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

        require_once APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_INCLUDES . '/Assets.php';

        if ( $this->is_request( 'admin' ) ) {
            require_once APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_INCLUDES . '/Admin.php';
        }

        if ( $this->is_request( 'frontend' ) ) {
            require_once APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_INCLUDES . '/Frontend.php';
        }

        if ( $this->is_request( 'ajax' ) ) {
            // require_once APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_INCLUDES . '/class-ajax.php';
        }

        require_once APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_INCLUDES . '/Api.php';
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {

        add_action( 'init', array( $this, 'init_classes' ) );

        add_action( 'init', array( $this, 'register_post_types' ) );
	    add_action( 'pre_get_posts', array( $this, 'archive_meta_query'), 1 );

        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );
    }

    public function register_post_types() {
	    register_post_type('aoat_form',
		    array(
			    'labels'      => array(
				    'name'          => __('Forms', 'apprenticeship-online-assessment-tool'),
				    'singular_name' => __('Form', 'apprenticeship-online-assessment-tool'),
			    ),
			    'public'      => true,
			    'has_archive' => true,
			    'show_in_menu' => 'apprenticeship-online-assessment-tool'
		    )
	    );
	    register_post_type('aoat_assessment',
		    array(
			    'labels'      => array(
				    'name'          => __('Assessments', 'apprenticeship-online-assessment-tool'),
				    'singular_name' => __('Assessment', 'apprenticeship-online-assessment-tool'),
			    ),
			    'public'      => true,
			    'has_archive' => true,
			    'show_in_menu' => 'apprenticeship-online-assessment-tool'
		    )
	    );
	    register_post_type('aoat_report',
		    array(
			    'labels'      => array(
				    'name'          => __('Reports', 'apprenticeship-online-report-tool'),
				    'singular_name' => __('Report', 'apprenticeship-online-report-tool'),
			    ),
			    'public'      => true,
			    'has_archive' => true,
			    'show_in_menu' => 'apprenticeship-online-report-tool'
		    )
	    );

	    add_filter( 'admin_url', array( $this, 'aoat_change_add_new_for_form'), 10, 2 );

	    add_filter('get_edit_post_link', array( $this,'get_edit_post_link_178416'), 99, 3);
    }

	function aoat_change_add_new_for_form( $url, $path ){
		if( $path === 'post-new.php?post_type=aoat_form' ) {
			$url = admin_url('admin.php?page=apprenticeship-online-assessment-tool#/forms/create'); // or any other url
		}
		if( $path === 'post-new.php?post_type=aoat_report' ) {
			$url = admin_url('admin.php?page=apprenticeship-online-assessment-tool#/reports/create'); // or any other url
		}

		return $url;
	}

	function get_edit_post_link_178416($link, $post_id, $context) {
		global $current_screen;

		if (isset($current_screen) && $current_screen->id == 'edit-aoat_form' && $context == 'display') {
			return admin_url('admin.php?page=apprenticeship-online-assessment-tool#/forms/' . $post_id);
		} else {
			return $link;
		}
	}
	function archive_meta_query( $query ) {

		$user = wp_get_current_user();

		if (current_user_can('administrator')) {
		    return $query;
        }

        if (wp_is_json_request()) {
		    return $query;
        }

		if (isset($query->query['post_type']) && $query->query['post_type'] == 'aoat_assessment'){
			if ( is_user_logged_in()) {
				$query->set( 'author', $user->ID );
				$query->set( 'posts_per_page', -1 );
			} else {
				// do not show anything
				$query->set( 'author', 99999999 );
				$query->set( 'posts_per_page', -1 );
			}
		}

		return $query;
	}

    /**
     * Instantiate the required classes
     *
     * @return void
     */
    public function init_classes() {

        if ( $this->is_request( 'admin' ) ) {
            $this->container['admin'] = new ApprenticeshipOnlineAssessmentTool\Admin();
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->container['frontend'] = new ApprenticeshipOnlineAssessmentTool\Frontend();
        }

        if ( $this->is_request( 'ajax' ) ) {
            // $this->container['ajax'] =  new ApprenticeshipOnlineAssessmentTool\Ajax();
        }

        $this->container['api'] = new ApprenticeshipOnlineAssessmentTool\Api();
        $this->container['assets'] = new ApprenticeshipOnlineAssessmentTool\Assets();
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'apprenticeship-online-assessment-tool', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin' :
                return is_admin();

            case 'ajax' :
                return defined( 'DOING_AJAX' );

            case 'rest' :
                return defined( 'REST_REQUEST' );

            case 'cron' :
                return defined( 'DOING_CRON' );

            case 'frontend' :
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

} // Apprenticeship_Online_Assessment_Tool

$apprenticeshipOnlineAssessmentTool = Apprenticeship_Online_Assessment_Tool::init();
