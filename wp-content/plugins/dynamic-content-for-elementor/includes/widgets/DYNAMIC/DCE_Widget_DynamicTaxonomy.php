<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor EDC Dynamic Taxonomy BETA
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */

class DCE_Widget_DynamicTaxonomy extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-dynamic-taxonomy';
    }
    
    static public function is_enabled() {
        return false;
    }

    public function get_title() {
        return __('Dynamic Taxonomy', 'dynamic-content-for-elementor');
    }

    public function get_icon() {
        return 'icon-dynamic_taxonomy';
    }
    public function get_script_depends() {
        return [ ];
    }
    static public function get_position() {
        return 2;
    }
    protected function _register_controls() {
        $this->start_controls_section(
                'section_dynamictemplate', [
                'label' => __('dynamic-taxonomy', 'dynamic-content-for-elementor'),
            ]
        );
        $this->add_control(
          'html_dynamic-taxonomy',
          [
             'type'    => Controls_Manager::RAW_HTML,
             'raw' => __( '<div>Questo è un widget che diventerà un\'dynamic-taxonomy.</div>', 'dynamic-content-for-elementor' ),
           'content_classes' => 'html-dynamic-taxonomy',
          ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_active_settings();
        if ( empty( $settings ) )
            return;

    }

}
