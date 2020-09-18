<?php
namespace ApprenticeshipOnlineAssessmentTool;

/**
 * Frontend Pages Handler
 */
class Frontend {

    public function __construct() {
        add_shortcode( 'aoat-form', [ $this, 'render_frontend' ] );
    }

    /**
     * Render frontend app
     *
     * @param  array $atts
     * @param  string $content
     *
     * @return string
     */
    public function render_frontend( $atts, $content = '' ) {
        wp_enqueue_style( 'apprenticeship-online-assessment-tool-frontend' );
        wp_enqueue_script( 'apprenticeship-online-assessment-tool-frontend' );


        $content .= '
			<script>
				let aoatGetFormUrl = "/wp-json/apprenticeship-online-assessment-tool/v1/forms/' . ($atts['id'] ?: null) . '";
				let aoatSaveAssessmentUrl = "/wp-json/apprenticeship-online-assessment-tool/v1/assessment/create";
			</script>
			<div id="vue-frontend-app"></div>
			';

        return $content;
    }
}
