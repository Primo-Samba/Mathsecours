<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Idea
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */

class DCE_Widget_Idea extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-idea';
    }
    
    static public function is_enabled() {
        return false;
    }

    public function get_title() {
        return __('Idea', 'dynamic-content-for-elementor');
    }

    public function get_icon() {
        return 'icon-dyn-idea';
    }
    public function get_script_depends() {
        return [ ];
    }
    static public function get_position() {
        return 9;
    }
    protected function _register_controls() {
        $this->start_controls_section(
                'section_dynamictemplate', [
                'label' => __('Idea', 'dynamic-content-for-elementor'),
            ]
        );
        $this->add_control(
          'html_idea',
          [
             'type'    => Controls_Manager::RAW_HTML,
             'raw' => __( '<div>Questo è un widget che diventerà un\'idea.</div>', 'dynamic-content-for-elementor' ),
           'content_classes' => 'html-idea',
          ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_active_settings();
        if ( empty( $settings ) )
            return;
        //

        // ------------------------------------------
        //


    }

}
