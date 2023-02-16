<?php

namespace ApprenticeshipOnlineAssessmentTool;

/**
 * Scripts and Styles Class
 */
class Assets
{
    function __construct()
    {

        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'register'], 5);
        } else {
            add_action('wp_enqueue_scripts', [$this, 'register'], 5);
        }
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register()
    {
        $this->register_scripts($this->get_scripts());
        $this->register_styles($this->get_styles());
    }

    /**
     * Register scripts
     *
     * @param array $scripts
     *
     * @return void
     */
    private function register_scripts($scripts)
    {
        foreach ($scripts as $handle => $script) {
            $deps = isset($script['deps']) ? $script['deps'] : false;
            $in_footer = isset($script['in_footer']) ? $script['in_footer'] : false;
            $version = isset($script['version']) ? $script['version'] : APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_VERSION;

            wp_register_script($handle, $script['src'], $deps, $version, $in_footer);
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts()
    {
        $scripts = [
            'apprenticeship-online-assessment-tool-runtime'  => [
                'src'       => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/js/runtime.js',
                'in_footer' => true,
            ],
            'apprenticeship-online-assessment-tool-vendor'   => [
                'src'       => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/js/vendors.js',
                'in_footer' => true,
            ],
            'apprenticeship-online-assessment-tool-frontend' => [
                'src'       => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/js/frontend.js',
                'deps'      => ['jquery'],
                'in_footer' => true,
            ],
            'apprenticeship-online-assessment-tool-admin'    => [
                'src'       => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/js/admin.js',
                'deps'      => ['jquery'],
                'in_footer' => true,
            ],
            'apprenticeship-online-assessment-tool-frontend' => [
                'src'       => 'http://localhost:8080/frontend.js',
                'deps'      => ['jquery'],
                'version'   => '',
                'in_footer' => true,
            ],
            'apprenticeship-online-assessment-tool-admin'    => [
                'src'       => 'http://localhost:8080/admin.js',
                'deps'      => ['jquery'],
                'version'   => '',
                'in_footer' => true,
            ],
        ];

        return $scripts;
    }

    /**
     * Register styles
     *
     * @param array $styles
     *
     * @return void
     */
    public function register_styles($styles)
    {
        foreach ($styles as $handle => $style) {
            $deps = isset($style['deps']) ? $style['deps'] : false;

            wp_register_style($handle, $style['src'], $deps, APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_VERSION);
        }
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles()
    {

        $styles = [
            'apprenticeship-online-assessment-tool-style'        => [
                'src' => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/css/style.css',
            ],
            'apprenticeship-online-assessment-tool-frontend'     => [
                'src' => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/css/frontend.css',
            ],
            'apprenticeship-online-assessment-tool-admin'        => [
                'src' => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/css/admin.css',
            ],
            'apprenticeship-online-assessment-tool-admin-extend' => [
                'src' => APPRENTICESHIP_ONLINE_ASSESSMENT_TOOL_ASSETS . '/css/admin.extend.css',
            ],
        ];

        return $styles;
    }
}
