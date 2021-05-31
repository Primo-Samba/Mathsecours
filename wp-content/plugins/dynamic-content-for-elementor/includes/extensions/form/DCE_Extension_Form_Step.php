<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_step($field) {
    switch ($field) {
        case 'enabled':
            return true;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/form-steps-for-elementor-pro-form/';
        case 'description' :
            return __('Add Steps to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro')) {

    class DCE_Extension_Form_Step extends DCE_Extension_Prototype {

        public $name = 'Form Steps';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        public function get_script_depends() {
            return ['dce-form-step', 'dce-form-summary'];
        }

        static public function is_enabled() {
            return _dce_extension_form_step('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_step('description');
        }

        public function get_docs() {
            return _dce_extension_form_step('docs');
        }

    }

} else {

    class DCE_Extension_Form_Step extends DCE_Extension_Prototype {

        public $name = 'Form Steps';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/';
        private $is_common = false;
        public $has_action = false;

        static public function is_enabled() {
            return _dce_extension_form_step('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_step('description');
        }

        public function get_docs() {
            return _dce_extension_form_step('docs');
        }

        public static function get_plugin_depends() {
            return self::$depended_plugins;
        }

        static public function get_satisfy_dependencies($ret = false) {
            return true;
        }

        /**
         * Get Name
         *
         * Return the action name
         *
         * @access public
         * @return string
         */
        public function get_name() {
            return 'dce_form_step';
        }

        /**
         * Get Label
         *
         * Returns the action label
         *
         * @access public
         * @return string
         */
        public function get_label() {
            return __('Form Steps', 'dynamic-content-for-elementor');
        }

        public function get_script_depends() {
            return ['dce-form-step', 'dce-form-summary'];
        }

        /**
         * Add Actions
         *
         * @since 0.5.5
         *
         * @access private
         */
        protected function add_actions() {
            
            add_action("elementor/frontend/widget/before_render", array($this, 'start_element'));
            add_action("elementor/widget/render_content", array($this, '_render_form'), 10, 2);
            add_action("elementor/frontend/widget/after_render", array($this, 'end_element'));
            
            //add_action( 'elementor-pro/forms/pre_render', array($this, '_pre_render_form'), 10, 2 );

            add_action('elementor/element/form/section_steps_settings/before_section_end', [$this, 'add_control_section_to_steps'], 10, 2);
            add_action('elementor/element/form/section_form_style/before_section_start', [$this, 'add_control_section_to_form'], 10, 2);
            //add_action("elementor/frontend/widget/before_render", array($this, '_before_render_form'), 10, 2);

            add_action('elementor/widget/print_template', function($template, $widget) {
                if ('form' === $widget->get_name()) {
                    $template = false;
                }
                return $template;
            }, 10, 2);

            add_action('elementor/editor/after_enqueue_scripts', function() {
                wp_register_script(
                        'dce-script-editor-form', plugins_url('/assets/js/dce-editor-form.js', DCE__FILE__), [], DCE_VERSION
                );
                wp_enqueue_script('dce-script-editor-form');
            });
        }
        
        public function start_element($widget = false) {
            if ('form' === $widget->get_name()) {
                $settings = $widget->get_settings_for_display();
                if ($settings['step_type'] == 'none') {
                    ob_start();
                }
            }
        }
        public function end_element($widget = false) {
            if ('form' === $widget->get_name()) {
                $settings = $widget->get_settings_for_display();
                if ($settings['step_type'] == 'none') {
                    $content = ob_get_clean();
                    // FIX Indicators
                    $content = str_replace('"step_type":"none"', '"step_type":"text"', $content);
                    $content = str_replace('&quot;step_type&quot;:&quot;none&quot;', '&quot;step_type&quot;:&quot;text&quot;', $content);
                    $content .= '<style>.elementor .elementor-element.elementor-element-'.$widget->get_id().' .e-form__indicators { display: none !important; }</style>';
                    echo $content;
                }
            }
        }
        
        

        public function add_control_section_to_steps($element, $args) {

            $element->add_control(
                    'dce_step_legend',
                    [
                        'label' => __('Use Label as Legend', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                    ]
            );

            $element->add_control(
                    'dce_step_show',
                    [
                        'label' => __('Show All steps', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                    ]
            );

            $element->add_control(
                    'dce_step_scroll',
                    [
                        'label' => __('Scroll to Top on Step change', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'condition' => [
                            'dce_step_show' => '',
                        ],
                    ]
            );
            
            $element->add_control(
                    'dce_step_progressbar',
                    [
                        'label' => __('Enable Clickable Step Indicator', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'condition' => [
                            'dce_step_show' => '',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_summary',
                    [
                        'label' => __('Enable Step Summary', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'frontend_available' => true,
                        'condition' => [
                            'dce_step_show' => '',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_text',
                    [
                        'label' => __('Summary Submit Button', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::TEXT,
                        'default' => __('Submit Form', 'dynamic-content-for-elementor'),
                        'condition' => [
                            'dce_step_show' => '',
                            'dce_step_summary!' => '',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_help', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __('Need Help', 'elementor') . ' <i class="eicon-help-o"></i></a></div>',
                'separator' => 'before',
                    ]
            );
        }

        public function add_control_section_to_form($element, $args) {

            $section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), 'section_steps_settings');
            if (is_wp_error($section_exists)) {
                $element->start_controls_section(
                        'dce_step_section',
                        [
                            'label' => __('Steps', 'dynamic-content-for-elementor'),
                            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                        ]
                );
                $this->add_control_section_to_steps($element, $args);
                $element->end_controls_section();
            }

            $element->start_controls_section(
                    'dce_step_section_style',
                    [
                        'label' => __('Steps', 'dynamic-content-for-elementor'),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_padding', [
                'label' => __('Padding', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .dce-form-step > .elementor-form-fields-wrapper, {{WRAPPER}} .elementor-field-type-step' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_margin', [
                'label' => __('Margin', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .dce-form-step > .elementor-form-fields-wrapper, {{WRAPPER}} .elementor-field-type-step' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );

            // Border ----------------
            $element->add_control(
                    'dce_step_heading_border',
                    [
                        'label' => __('Border', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $element->add_group_control(
                    Group_Control_Border::get_type(), [
                'name' => 'dce_step_border',
                'label' => __('Border', 'dynamic-content-for-elementor'),
                'selector' => '{{WRAPPER}} .dce-form-step, {{WRAPPER}} .elementor-field-type-step',
                    ]
            );
            $element->add_control(
                    'dce_step_border_radius', [
                'label' => __('Border Radius', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .dce-form-step' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );

            // Background ----------------
            $element->add_control(
                    'dce_step_heading_background',
                    [
                        'label' => __('Background', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $element->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'dce_step_background',
                        'types' => ['classic', 'gradient'],
                        'selector' => '{{WRAPPER}} .dce-form-step',
                    ]
            );

            // Title ----------------
            $element->add_control(
                    'dce_step_heading_title',
                    [
                        'label' => __('Title', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                        'condition' => [
                            'dce_step_legend!' => '',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_title_align',
                    [
                        'label' => __('Alignment', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'title' => __('Left', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-left',
                            ],
                            'center' => [
                                'title' => __('Center', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-center',
                            ],
                            'right' => [
                                'title' => __('Right', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-right',
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step legend' => 'display: block; text-align: {{VALUE}};',
                        ],
                        'condition' => [
                            'dce_step_legend!' => '',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_title_color',
                    [
                        'label' => __('Color', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step legend' => 'color: {{VALUE}};',
                        ],
                        'condition' => [
                            'dce_step_legend!' => '',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(), [
                'name' => 'dce_step_title_typography',
                'label' => __('Typography', 'dynamic-content-for-elementor'),
                'selector' => '{{WRAPPER}} .dce-form-step legend',
                'condition' => [
                    'dce_step_legend!' => '',
                ],
                    ]
            );
            $element->add_control(
                    'dce_step_title_space',
                    [
                        'label' => __('Space', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SLIDER,
                        'default' => [
                            'size' => 10,
                        ],
                        'range' => [
                            'px' => [
                                'min' => -50,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step legend' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'dce_step_legend!' => '',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'dce_step_text_shadow',
                        'selector' => '{{WRAPPER}} .dce-form-step legend',
                        'condition' => [
                            'dce_step_legend!' => '',
                        ],
                    ]
            );

            /* $element->add_control(
              'border_popover_toggle',
              [
              'label' => __( 'Border', 'plugin-domain' ),
              'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
              'label_off' => __( 'Default', 'your-plugin' ),
              'label_on' => __( 'Custom', 'your-plugin' ),
              'return_value' => 'yes',
              'default' => 'yes',
              ]
              ); */

            $element->end_controls_section();

            $element->start_controls_section(
                    'dce_step_section_button',
                    [
                        'label' => __('Steps Navigation Buttons', 'dynamic-content-for-elementor'),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                        'condition' => [
                            'dce_step_show' => '',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_button_size',
                    [
                        'label' => __('Size', 'elementor-pro'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'sm',
                        'options' => DCE_Helper::get_button_sizes(),
                    ]
            );

            $element->start_controls_tabs('dce_step_tabs_button_style');

            $element->start_controls_tab(
                    'dce_step_tab_button_normal',
                    [
                        'label' => __('Normal', 'elementor-pro'),
                    ]
            );

            $element->add_control(
                    'dce_step_button_background_color',
                    [
                        'label' => __('Background Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_button_text_color',
                    [
                        'label' => __('Text Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button svg' => 'fill: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_button_typography',
                        'selector' => '{{WRAPPER}} .dce-step-elementor-button.elementor-button',
                    ]
            );

            $element->add_group_control(
                    Group_Control_Border::get_type(), [
                'name' => 'dce_step_button_border',
                'selector' => '{{WRAPPER}} .dce-step-elementor-button.elementor-button',
                    ]
            );

            $element->add_control(
                    'dce_step_button_border_radius',
                    [
                        'label' => __('Border Radius', 'elementor-pro'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_button_text_padding',
                    [
                        'label' => __('Text Padding', 'elementor-pro'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );

            $element->end_controls_tab();

            $element->start_controls_tab(
                    'dce_step_tab_button_hover',
                    [
                        'label' => __('Hover', 'elementor-pro'),
                    ]
            );

            $element->add_control(
                    'dce_step_button_background_hover_color',
                    [
                        'label' => __('Background Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button:hover' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_button_hover_color',
                    [
                        'label' => __('Text Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button:hover' => 'color: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_button_hover_border_color',
                    [
                        'label' => __('Border Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-elementor-button.elementor-button:hover' => 'border-color: {{VALUE}};',
                        ],
                        'condition' => [
                            'dce_step_button_border_border!' => '',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_button_hover_animation',
                    [
                        'label' => __('Animation', 'elementor-pro'),
                        'type' => Controls_Manager::HOVER_ANIMATION,
                    ]
            );

            $element->end_controls_tab();

            $element->end_controls_tabs();


            $element->add_control(
                    'dce_step_button_css_class',
                    [
                        'label' => __('Custom Classes', 'elementor-pro'),
                        'type' => Controls_Manager::TEXT,
                        'default' => '',
                        //'title' => __('Add your custom classes WITHOUT the dot key. e.g: my-class', 'dynamic-content-for-elementor'),
                        'label_block' => true,
                        //'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'elementor-pro'),
                        'separator' => 'before',
                    ]
            );

            $element->end_controls_section();







            $element->start_controls_section(
                    'dce_step_section_progressbar',
                    [
                        'label' => __('Steps ProgressBar', 'dynamic-content-for-elementor'),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                        'condition' => [
                            'dce_step_show' => '',
                            'dce_step_progressbar!' => '',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_size',
                    [
                        'label' => __('Size', 'elementor-pro'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'sm',
                        'options' => DCE_Helper::get_button_sizes(),
                    ]
            );

            $element->start_controls_tabs('dce_step_tabs_progressbar_style');

            $element->start_controls_tab(
                    'dce_step_tab_progressbar_normal',
                    [
                        'label' => __('Normal', 'elementor-pro'),
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_background_color',
                    [
                        'label' => __('Background Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_text_color',
                    [
                        'label' => __('Text Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button svg' => 'fill: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_progressbar_typography',
                        'selector' => '{{WRAPPER}} .dce-form-step-progressbar .elementor-button',
                    ]
            );

            $element->add_group_control(
                    Group_Control_Border::get_type(), [
                'name' => 'dce_step_progressbar_border',
                'selector' => '{{WRAPPER}} .dce-form-step-progressbar .elementor-button',
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_border_radius',
                    [
                        'label' => __('Border Radius', 'elementor-pro'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_text_padding',
                    [
                        'label' => __('Text Padding', 'elementor-pro'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );

            $element->end_controls_tab();

            $element->start_controls_tab(
                    'dce_step_tab_progressbar_active',
                    [
                        'label' => __('Active', 'elementor-pro'),
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_background_hover_color',
                    [
                        'label' => __('Background Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button:hover' => 'background-color: {{VALUE}};',
                            '{{WRAPPER}} .dce-form-step-progressbar.dce-step-active-progressbar .elementor-button' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_hover_color',
                    [
                        'label' => __('Text Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button:hover' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .dce-form-step-progressbar.dce-step-active-progressbar .elementor-button' => 'color: {{VALUE}};',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_progressbar_hover_border_color',
                    [
                        'label' => __('Border Color', 'elementor-pro'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-progressbar .elementor-button:hover' => 'border-color: {{VALUE}};',
                            '{{WRAPPER}} .dce-form-step-progressbar.dce-step-active-progressbar .elementor-button' => 'border-color: {{VALUE}};',
                        ],
                        'condition' => [
                            'dce_step_progressbar_border_border!' => '',
                        ],
                    ]
            );

            $element->end_controls_tab();

            $element->end_controls_tabs();

            $element->end_controls_section();


            // SUMMARY
            $element->start_controls_section(
                    'dce_step_section_summary',
                    [
                        'label' => __('Summary', 'dynamic-content-for-elementor'),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                        'condition' => [
                            'dce_step_summary!' => '',
                            'dce_step_show' => '',
                        ],
                    ]
            );

            $element->add_control(
                    'dce_step_summary_title',
                    [
                        'label' => __('Title', 'elementor'),
                        'type' => Controls_Manager::HEADING,
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_title_align',
                    [
                        'label' => __('Alignment', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'title' => __('Left', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-left',
                            ],
                            'center' => [
                                'title' => __('Center', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-center',
                            ],
                            'right' => [
                                'title' => __('Right', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-right',
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-summary-title' => 'text-align: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_summary_title_typography',
                        'selector' => '{{WRAPPER}} .dce-step-summary-title',
                    ]
            );
            $element->add_control(
                    'dce_step_summary_title_color',
                    [
                        'label' => __('Title Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-step-summary-title' => 'color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_title_margin', [
                'label' => __('Margin', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .dce-step-summary-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );

            $element->add_control(
                    'dce_step_summary_step',
                    [
                        'label' => __('Step', 'elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );


            $element->add_responsive_control(
                    'dce_step_summary_step_padding',
                    [
                        'label' => __('Padding', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_step_margin',
                    [
                        'label' => __('Margin', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'default' => [
                            'top' => '0',
                            'right' => '0',
                            'bottom' => '15',
                            'left' => '0',
                            'unit' => 'px',
                            'isLinked' => false,
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_step_radius',
                    [
                        'label' => __('Border Radius', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );

            $element->start_controls_tabs('dce_step_summary_step_style');
            $element->start_controls_tab(
                    'dce_step_summary_step_normal',
                    [
                        'label' => __('Normal', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_step_background_color',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'dce_step_summary_step_border',
                        'selector' => '{{WRAPPER}} .dce-form-step-summary',
                    ]
            );

            $element->end_controls_tab();

            $element->start_controls_tab(
                    'dce_step_summary_step_filled',
                    [
                        'label' => __('Filled', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_step_background_color_filled',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary.dce-step-filled-summary' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'dce_step_summary_step_border_filled',
                        'selector' => '{{WRAPPER}} .dce-form-step-summary.dce-step-filled-summary',
                    ]
            );
            /* $element->add_control(
              'dce_step_summary_step_text_color_filled',
              [
              'label' => __('Text Color', 'elementor'),
              'type' => Controls_Manager::COLOR,
              'separator' => 'before',
              'selectors' => [
              '{{WRAPPER}} .dce-form-step-summary.dce-step-filled-summary' => 'color: {{VALUE}};',
              ],
              ]
              ); */
            $element->end_controls_tab();
            $element->start_controls_tab(
                    'dce_step_summary_step_active',
                    [
                        'label' => __('Active', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_step_background_color_active',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary.dce-step-active-summary' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'dce_step_summary_step_border_active',
                        'selector' => '{{WRAPPER}} .dce-form-step-summary.dce-step-active-summary',
                    ]
            );
            /* $element->add_control(
              'dce_step_summary_step_text_color_active',
              [
              'label' => __('Text Color', 'elementor'),
              'type' => Controls_Manager::COLOR,
              'selectors' => [
              '{{WRAPPER}} .dce-form-step-summary.dce-step-active-summary' => 'color: {{VALUE}};',
              ],
              ]
              ); */
            $element->end_controls_tab();
            $element->end_controls_tabs();

            /* $element->add_control(
              'dce_step_summary_step_border_color',
              [
              'label' => __('Border Color', 'elementor'),
              'type' => Controls_Manager::COLOR,
              'condition' => [
              'dce_step_summary_step_border_border!' => '',
              ],
              'selectors' => [
              '{{WRAPPER}} .dce-form-step-summary' => 'border-color: {{VALUE}};',
              ],
              ]
              ); */
            $element->add_control(
                    'dce_step_summary_step_text_color',
                    [
                        'label' => __('Text Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'separator' => 'before',
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-step-summary' => 'color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_summary_step_text_typography',
                        'selector' => '{{WRAPPER}} .dce-form-step-summary',
                    ]
            );
            $element->add_control(
                    'dce_step_summary_step_label_color',
                    [
                        'label' => __('Label Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'separator' => 'before',
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-summary-field-label' => 'color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_summary_step_label_typography',
                        'selector' => '{{WRAPPER}} .dce-form-summary-field-label',
                    ]
            );
            $element->add_control(
                    'dce_step_summary_step_title_color',
                    [
                        'label' => __('Title Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'separator' => 'before',
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-summary-step-title' => 'color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_summary_step_title_typography',
                        'selector' => '{{WRAPPER}} .dce-form-summary-step-title',
                    ]
            );
            $element->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'dce_step_summary_step_title_border',
                        'selector' => '{{WRAPPER}} .dce-form-summary-step-title',
                    ]
            );
            /* $element->add_control(
              'dce_step_summary_step_title_border_color',
              [
              'label' => __('Border Color', 'elementor'),
              'type' => Controls_Manager::COLOR,
              'condition' => [
              'dce_step_summary_step_title_border_border!' => '',
              ],
              'selectors' => [
              '{{WRAPPER}} .dce-form-summary-step-title' => 'border-color: {{VALUE}};',
              ],
              ]
              ); */
            $element->add_responsive_control(
                    'dce_step_summary_step_title_padding',
                    [
                        'label' => __('Padding', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-summary-step-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_step_title_margin',
                    [
                        'label' => __('Margin', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .dce-form-summary-step-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );


            $element->add_control(
                    'dce_step_summary_edit_btn',
                    [
                        'label' => __('Edit Button', 'elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_edit_btn_align',
                    [
                        'label' => __('Alignment', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'title' => __('Left', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-left',
                            ],
                            'right' => [
                                'title' => __('Right', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-right',
                            ],
                        ],
                        'toggle' => false,
                        'default' => 'right',
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit' => 'float: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_summary_edit_btn_typography',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-edit',
                    ]
            );
            $element->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'dce_step_summary_edit_btn_text_shadow',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-edit',
                    ]
            );
            $element->start_controls_tabs('dce_step_summary_edit_btn_tabs_button_style');
            $element->start_controls_tab(
                    'dce_step_summary_edit_btn_tab_button_normal',
                    [
                        'label' => __('Normal', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_edit_btn_text_color',
                    [
                        'label' => __('Text Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit' => 'fill: {{VALUE}}; color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_edit_btn_background_color',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->end_controls_tab();
            $element->start_controls_tab(
                    'dce_step_summary_edit_btn_tab_button_hover',
                    [
                        'label' => __('Hover', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_edit_btn_hover_color',
                    [
                        'label' => __('Text Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit:hover, {{WRAPPER}} .elementor-button.elementor-button-edit:focus' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .elementor-button.elementor-button-edit:hover svg, {{WRAPPER}} .elementor-button.elementor-button-edit:focus svg' => 'fill: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_edit_btn_background_hover_color',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit:hover, {{WRAPPER}} .elementor-button.elementor-button-edit:focus' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_edit_btn_hover_border_color',
                    [
                        'label' => __('Border Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'condition' => [
                            'border_border!' => '',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit:hover, {{WRAPPER}} .elementor-button.elementor-button-edit:focus' => 'border-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'dce_step_summary_edit_btn_border',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-edit',
                        'separator' => 'before',
                    ]
            );
            $element->add_control(
                    'dce_step_summary_edit_btn_border_radius',
                    [
                        'label' => __('Border Radius', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_edit_btn_text_padding',
                    [
                        'label' => __('Padding', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_edit_btn_text_margin',
                    [
                        'label' => __('Margin', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-edit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );


            $element->add_control(
                    'dce_step_summary_submit_btn',
                    [
                        'label' => __('Submit Button', 'elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_submit_btn_align',
                    [
                        'label' => __('Alignment', 'elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'title' => __('Left', 'elementor'),
                                'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                'title' => __('Center', 'elementor'),
                                'icon' => 'eicon-text-align-center',
                            ],
                            'right' => [
                                'title' => __('Right', 'elementor'),
                                'icon' => 'eicon-text-align-right',
                            ],
                            'justify' => [
                                'title' => __('Justified', 'elementor'),
                                'icon' => 'eicon-text-align-justify',
                            ],
                        ],
                        'default' => '',
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_size',
                    [
                        'label' => __('Size', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'sm',
                        'options' => DCE_Helper::get_button_sizes(),
                        'style_transfer' => true,
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'dce_step_summary_submit_btn_typography',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit',
                    ]
            );
            $element->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'dce_step_summary_submit_btn_text_shadow',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit',
                    ]
            );
            $element->start_controls_tabs('dce_step_summary_submit_btn_tabs_button_style');
            $element->start_controls_tab(
                    'dce_step_summary_submit_btn_tab_button_normal',
                    [
                        'label' => __('Normal', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_text_color',
                    [
                        'label' => __('Text Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'default' => '',
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit' => 'fill: {{VALUE}}; color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_background_color',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->end_controls_tab();
            $element->start_controls_tab(
                    'dce_step_summary_submit_btn_tab_button_hover',
                    [
                        'label' => __('Hover', 'elementor'),
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_hover_color',
                    [
                        'label' => __('Text Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit:hover, {{WRAPPER}} .elementor-button.elementor-button-submit:focus' => 'color: {{VALUE}};',
                            '{{WRAPPER}} .elementor-button.elementor-button-submit:hover svg, {{WRAPPER}} .elementor-button.elementor-button-submit:focus svg' => 'fill: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_background_hover_color',
                    [
                        'label' => __('Background Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit:hover, {{WRAPPER}} .elementor-button.elementor-button-submit:focus' => 'background-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_hover_border_color',
                    [
                        'label' => __('Border Color', 'elementor'),
                        'type' => Controls_Manager::COLOR,
                        'condition' => [
                            'dce_step_summary_submit_btn_border_border!' => '',
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit:hover, {{WRAPPER}} .elementor-button.elementor-button-submit:focus' => 'border-color: {{VALUE}};',
                        ],
                    ]
            );
            $element->end_controls_tab();
            $element->end_controls_tabs();
            $element->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'dce_step_summary_submit_btn_border',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit',
                        'separator' => 'before',
                    ]
            );
            $element->add_control(
                    'dce_step_summary_submit_btn_border_radius',
                    [
                        'label' => __('Border Radius', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Box_Shadow::get_type(),
                    [
                        'name' => 'dce_step_summary_submit_btn_button_box_shadow',
                        'selector' => '{{WRAPPER}} .elementor-button.elementor-button-submit',
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_submit_btn_text_padding',
                    [
                        'label' => __('Padding', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_step_summary_submit_btn_text_margin',
                    [
                        'label' => __('Margin', 'elementor'),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => ['px', 'em', '%'],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-button.elementor-button-submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
            );



            $element->end_controls_section();
        }

        public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = []) {
            //echo 'adsa: '; var_dump($control_id); //die();
            if ($element->get_name() == 'form' && $control_id == 'form_fields') {
                //var_dump($control_data); die();

                $control_data["fields"]["field_type"]["options"]['step'] = __('Step', 'dynamic-content-for-elementor');

                if ($control_id == 'form_fields') {

                    $btn_type = empty($control_data['fields']['next_button']) ? Controls_Manager::TEXT : Controls_Manager::HIDDEN;
                    $control_data['fields']['dce_step_next'] = array(
                        'name' => 'dce_step_next',
                        'label' => __('Text Next', 'dynamic-content-for-elementor'),
                        'type' => $btn_type,
                        'default' => __('Next', 'dynamic-content-for-elementor'),
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'step',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "tab" => "content",
                    );
                    $control_data['fields']['dce_step_prev'] = array(
                        'name' => 'dce_step_prev',
                        'label' => __('Text Prev', 'dynamic-content-for-elementor'),
                        'type' => $btn_type,
                        'default' => __('Prev', 'dynamic-content-for-elementor'),
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'step',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "tab" => "content",
                    );

                    //$control_data['fields']['field_html']['conditions']['terms']['value'] = array('html','step');
                    $control_data['fields']['field_step'] = array(
                        'name' => 'field_step',
                        'label' => __('HTML', 'elementor-pro'),
                        'type' => Controls_Manager::TEXTAREA,
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'step',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "tab" => "content",
                    );

                    if (!empty($control_data['fields']['next_button'])) {
                        // reenable width
                        $step_index = array_search("step", $control_data['fields']['width']['conditions']['terms'][0]['value']);
                        if ($step_index !== false) {
                            unset($control_data['fields']['width']['conditions']['terms'][0]['value'][$step_index]);
                        }
                    }
                }
            }

            /* if ($element->get_name() == 'form' && ($control_id == 'step_type' || $control_id == 'step_icon_shape')) {
              $control_data['condition'] = array('dce_step_show' => '');
              } */
            /*if ($element->get_name() == 'form' && $control_id == 'step_type') {
                $control_data['default'] = 'none';
            }*/

            if ($element->get_name() == 'form' && $control_id == 'column_gap') {
                if (isset($control_data['selectors']['{{WRAPPER}} .elementor-field-group'])) {
                    $control_data['selectors']['{{WRAPPER}} .elementor-form-steps legend'] = $control_data['selectors']['{{WRAPPER}} .elementor-field-group'];
                }
                $control_data['selectors']['{{WRAPPER}} .elementor-form-steps .elementor-form-fields-wrapper'] = 'margin: 0;';
                //$control_data['selectors']['{{WRAPPER}} .elementor-form-steps > .elementor-form-fields-wrapper'] = 'left: calc( {{SIZE}}{{UNIT}}/2 ); position: relative; margin: 0;';
            }

            return $control_data;
        }

        public function _progressbar($widget) {
            $settings = $widget->get_settings_for_display();
            if (empty($settings['dce_step_progressbar'])) {
                return '';
            }

            // FIELDS
            $steps = array();
            if (!empty($settings['form_fields'])) {
                foreach ($settings['form_fields'] as $key => $afield) {
                    if ($afield["field_type"] == 'step') {
                        $steps[] = $afield;
                        // TODO: remove it from form_fields
                    }
                }
            }

            $bar = '';
            if (!empty($steps)) {
                $bar .= '<ol class="dce-form-progressbar">';
                foreach ($steps as $key => $astep) {
                    $bar .= '<li id="dce-form-step-' . $astep['custom_id'] . '-progressbar" class="dce-form-step-progressbar' . (!$key ? ' dce-step-active-progressbar' : '') . '">';
                    $bar .= '<a class="elementor-button elementor-button-progressbar elementor-size-' . $settings['dce_step_progressbar_size'] . '" href="#" data-target="' . $astep['custom_id'] . '">';
                    $bar .= $astep['field_label'];
                    $bar .= '</a>';
                    $bar .= '</li>';
                }
                $bar .= '</ol>';
            }
            return $bar;
        }

        public function _summary($widget) {
            $settings = $widget->get_settings_for_display();
            if (empty($settings['dce_step_summary'])) {
                return '';
            }

            // FIELDS
            $steps = array();
            if (!empty($settings['form_fields'])) {
                foreach ($settings['form_fields'] as $key => $afield) {
                    if ($afield["field_type"] == 'step') {
                        $steps[] = $afield;
                    }
                }
            }

            $bar = '';
            if (!empty($steps)) {
                $bar .= '<div class="dce-form-summary-wrapper">';
                $bar .= '<h3 class="dce-step-summary-title">' . $settings['form_name'] . '</h3>';
                $bar .= '<ul class="dce-form-summary dce-no-list">';
                foreach ($settings['form_fields'] as $key => $afield) {
                    $field_name = $afield['field_label'];
                    if (!$field_name) {
                        if (!empty($afield['placeholder'])) {
                            $field_name = $afield['placeholder'];
                        }
                    }
                    if (!$field_name) {
                        $field_name = $afield['custom_id'];
                    }
                    if ($afield["field_type"] == 'step') {
                        if ($key) {
                            $bar .= '</ul></li>';
                        }
                        $bar .= '<li id="dce-form-step-' . $afield['custom_id'] . '-summary" class="dce-form-step-summary' . (!$key ? ' dce-step-filled-summary dce-step-active-summary' : '') . '">'
                                . '<a class="elementor-button elementor-button-edit elementor-size-xs dce-form-step-summary-edit" data-target="' . $afield['custom_id'] . '" data-element="' . $widget->get_id() . '" href="#elementor-element-' . $widget->get_id() . '" rel="nofollow">Edit</a>'
                                . '<h4 class="dce-form-summary-step-title">' . $field_name . '</h4>'
                                . '<ul>';
                    } else {
                        if (in_array($afield["field_type"], array('text', 'textarea', 'select', 'upload', 'radio', 'checkbox', 'email', 'url', 'tel', 'acceptance', 'number', 'date', 'time', 'amount'))) {
                            $bar .= '<li id="dce-summary-form-field-' . $afield['custom_id'] . '" class="dce-form-step-field-summary"><label class="dce-form-summary-field-label">' . $field_name . ':</label> <span class="dce-form-summary-field-value" id="dce-summary-value-form-field-' . $afield['custom_id'] . '-' . $widget->get_id() . '">' . $afield['field_value'] . '</span></li>';
                        }
                    }
                }
                $bar .= '</ul></li>';
                $bar .= '</ul>';
                if (!empty($settings['dce_step_summary_submit_btn_text'])) {
                    $bar .= '<div class="elementor-button-wrapper' . ($settings['dce_step_summary_submit_btn_align'] ? ' elementor-align-' . $settings['dce_step_summary_submit_btn_align'] : '') . '"' . (\Elementor\Plugin::$instance->editor->is_edit_mode() ? '' : ' style="display: none;"') . '>';
                    $bar .= '<button class="elementor-button elementor-button-submit elementor-size-' . $settings['dce_step_summary_submit_btn_size'] . '">' . $settings['dce_step_summary_submit_btn_text'] . '</button>';
                    $bar .= '</div>';
                }
                $bar .= '</div>';
                //wp_enqueue_script('dce-form-summary');
				wp_enqueue_script('dce-form-summary', plugins_url('/assets/js/dce-form-summary.js', DCE__FILE__), [], DCE_VERSION	);
            }
            return $bar;
        }
        
        public function _pre_render_form($instance, $form) {
            //$instance = $widget->get_settings_for_display();
            return $instance;
        }

        public function _render_form($content, $widget) {
            $new_content = $content;
            if ($widget->get_name() == 'form') {
                $settings = $widget->get_settings_for_display();

                //var_dump($settings['form_fields']); die();
                // FIELDS
                $steps = array();
                if (!empty($settings['form_fields'])) {
                    foreach ($settings['form_fields'] as $key => $afield) {
                        if (!$key && $afield["field_type"] != 'step') {
                            break;
                        }
                        if ($afield["field_type"] == 'step') {
                            $steps[] = $afield;
                        }
                    }
                }

                if (!empty($steps)) {

                    if (!$settings['dce_step_show']) {
                        $content = str_replace('class="elementor-form"', 'class="elementor-form elementor-form-steps"', $content);
                    }
                    $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_steps';
                    // add custom js
                    ob_start();
                    ?>
                    <script id="<?php echo $jkey; ?>">
                        (function ($) {
                    <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                                var <?php echo $jkey; ?> = function ($scope, $) {
                                    if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                    <?php } ?>

                                    //return false;
                                    /* START - DCE SCRIPT */
                                    var form_id = '<?php echo $widget->get_id(); ?>';
                                    var settings = <?php echo json_encode($settings); ?>;
                                    //jQuery('.elementor-field-type-step').hide();
                                    var step_last = false;
                                    var epro_10 = <?php echo (version_compare(ELEMENTOR_PRO_VERSION, '2.10.0') >= 0) ? 'true' : 'false'; ?>;
                                    console.log(epro_10);
                                    if (settings['form_fields'].length) {
                                        jQuery(settings.form_fields).each(function (index, afield) {
                                            //console.log(index);
                                            //console.log(afield);
                                            if (!index && afield.field_type == 'step') {
                                                if (jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper .elementor-field-group-' + afield.custom_id).hasClass('e-form__step')) {
                                                    //alert('epro steps');
                                                    //console.log(FormSteps);
                                                    //var elementSettings = get_Dyncontel_ElementSettings($scope);
                                                    //console.log(elementSettings);
                                                    //console.log(elementorModules);
                                                    //console.log(elementorProFrontend);
                                                    //var form = jQuery('.elementor-element-' + form_id + ' form');
                                                    //console.log(jQuery._data( '.elementor-element-' + form_id + ' form', "events" ));
                                                    //console.log(ElementorProFrontendConfig);
                                                    epro_10 = true;
                                                }
                                            }



                                            if (epro_10) {
                                                var field = jQuery('.elementor-element-' + form_id + ' .elementor-field-group-' + afield.custom_id);
                                                if (afield.field_type == 'step') {
                                                    field.addClass("dce-form-step");
                                                    field.addClass("dce-form-step" + afield.custom_id);
                                                    field.attr('data-custom_id', afield.custom_id);
                                                    field.attr('id', 'dce-form-step-' + afield.custom_id);
                                                    // fix prev next button text
                    <?php if ($settings['dce_step_legend']) { ?>
                                                        // legend
                                                        field.prepend('<legend class="elementor-step-legend elementor-column elementor-col-100">' + afield.field_label + '</legend>');
                    <?php } ?>
                                                }

                                            }


                                            if (!epro_10) {
                                                var width = afield.width;
                                                if (!width) {
                                                    width = 100;
                                                }
                                                if (afield.field_type == 'step') {
                                                    jQuery('.elementor-element-' + form_id + ' .elementor-form > .elementor-form-fields-wrapper').append('<fieldset id="dce-form-step-' + afield.custom_id + '" data-custom_id="' + afield.custom_id + '" class="dce-form-step elementor-column elementor-field-group-' + afield.custom_id + ' elementor-col-' + width + '"></fieldset>');
                                                    if (!step_last) {
                                                        // first step
                                                        jQuery('#dce-form-step-' + afield.custom_id).addClass('dce-step-active');
                                                    }
                                                    jQuery('#dce-form-step-' + afield.custom_id).append('<div class="elementor-field-type-step elementor-field-group elementor-column elementor-field-group-' + afield.custom_id + ' elementor-col-100">' + afield.field_step + '</div>');
                                                }
                                                if (afield.field_type == 'step' && step_last) {
                    <?php if ($settings['dce_step_legend']) { ?>
                                                        // legend
                                                        jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).prepend('<legend class="elementor-step-legend elementor-column elementor-col-100">' + step_last.field_label + '</legend>');
                    <?php
                    }
                    $btn_class = '';
                    if ($settings['dce_step_button_css_class']) {
                        $btn_class .= $settings['dce_step_button_css_class'] . ' ';
                    }
                    if ($settings['dce_step_button_hover_animation']) {
                        $btn_class .= 'elementor-animation-' . $settings['dce_step_button_hover_animation'] . ' ';
                    }
                    if (!$settings['dce_step_show']) {
                        ?>
                                                        // clear
                                                        jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).append('<div class="elementor-field-group elementor-column elementor-col-100"></div>');
                                                        // prev
                                                        if (step_last.dce_step_prev) {
                                                            jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + afield.custom_id).append('<div class="elementor-field-group elementor-column elementor-col-50 dce-form-step-bnt-previous"><button type="button" class="<?php echo $btn_class; ?>elementor-button dce-step-elementor-button elementor-button-previous elementor-size-<?php echo $settings['dce_step_button_size']; ?>" data-target="' + step_last.custom_id + '"><span><span class="elementor-button-text">' + step_last.dce_step_prev + '</span></span></button></div>');
                                                        } else {
                                                            jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + afield.custom_id).append('<div class="elementor-field-group elementor-column elementor-col-50 dce-form-step-bnt-previous"></div>');
                                                        }

                                                        // first prev empty
                                                        //alert('#dce-form-step-'+step_last.custom_id);
                                                        if (jQuery('#dce-form-step-' + step_last.custom_id).hasClass('dce-step-active')) {
                                                            jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).append('<div class="elementor-field-group elementor-column elementor-col-50 dce-form-step-bnt-previous"></div>');
                                                        } else {
                                                            // prev to bottom
                                                            jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id + ' .dce-form-step-bnt-previous').appendTo('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id);
                                                        }
                                                        // next
                                                        jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).append('<div class="elementor-field-group elementor-column elementor-col-50 dce-form-step-bnt-next"><button type="button" class="<?php echo $btn_class; ?>elementor-button dce-step-elementor-button elementor-button-next elementor-size-<?php echo $settings['dce_step_button_size']; ?>" data-target="' + afield.custom_id + '"><span><span class="elementor-button-text">' + step_last.dce_step_next + '</span></span></button></div>');

                    <?php } ?>
                                                    // bugfix for flex on Chrome
                                                    jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).wrapInner('<div class="elementor-form-fields-wrapper elementor-form-fields-wrapper-' + step_last.custom_id + ' elementor-labels-above elementor-column elementor-col-100"></div>');
                                                }
                                                if (afield.field_type == 'step') {
                                                    step_last = afield;
                                                }
                                                if (afield.field_type == 'step') {
                                                    jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper div.elementor-field-group-' + afield.custom_id).remove();
                                                } else {
                                                    jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper .elementor-field-group-' + afield.custom_id).appendTo('#dce-form-step-' + step_last.custom_id);
                                                }
                                                if (afield.field_type == 'honeypot') {
                                                    jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #form-field-' + afield.custom_id).parent().appendTo('#dce-form-step-' + step_last.custom_id);
                                                }
                                            }
                                        });

                                        if (!epro_10 && step_last) {
                    <?php if ($settings['dce_step_legend']) { ?>
                                                // legend
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).prepend('<legend>' + step_last.field_label + '</legend>');
                    <?php } ?>

                                            // submit
                    <?php if (!$settings['dce_step_show']) { ?>
                                                // prev to bottom
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id + ' .dce-form-step-bnt-previous').appendTo('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id);
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).append('<div class="elementor-field-group elementor-column elementor-col-50 dce-form-step-bnt-next"></div>');
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper .elementor-field-group.elementor-field-type-submit').appendTo('#dce-form-step-' + step_last.custom_id + ' .dce-form-step-bnt-next');
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper .elementor-field-group.elementor-field-type-submit').removeClass('elementor-field-group');
                                                //jQuery('.elementor-element-'+form_id+' .elementor-form-fields-wrapper .elementor-field-group.elementor-field-type-submit').addClass('elementor-col-50');
                    <?php } else { ?>
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper .elementor-field-group.elementor-field-type-submit').appendTo('.elementor-element-' + form_id + ' .elementor-form > .elementor-form-fields-wrapper');
                                                jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper .elementor-field-group.elementor-field-type-submit').removeClass('elementor-field-group').addClass('elementor-field-group-submit');
                    <?php } ?>
                                            jQuery('.elementor-element-' + form_id + ' .elementor-form-fields-wrapper #dce-form-step-' + step_last.custom_id).wrapInner('<div class="elementor-form-fields-wrapper elementor-form-fields-wrapper-' + step_last.custom_id + ' elementor-labels-above elementor-column elementor-col-100"></div>');
                                        }

                                        if (epro_10) {
                    <?php if ($settings['dce_step_show']) { ?>
                                                jQuery('.elementor-element-' + form_id + ' .e-form__buttons__wrapper.elementor-field-type-next, .elementor-element-' + form_id + ' .e-form__buttons__wrapper.elementor-field-type-previous').hide();
                    <?php } else { ?>
                                                jQuery('.elementor-element-' + form_id + ' .elementor-field-type-previous > .e-form__buttons__wrapper__button-previous, .elementor-element-' + form_id + ' .elementor-field-type-next > .e-form__buttons__wrapper__button-next').each(function () {
                                                    if (jQuery(this).hasClass('e-form__buttons__wrapper__button-next')) {
                                                        var target_id = jQuery(this).closest('.dce-form-step').next().attr('data-custom_id');
                                                        //jQuery(this).addClass('elementor-button-next');
                                                        jQuery(this).after('<button type="button" class="<?php echo $btn_class; ?>elementor-button dce-step-elementor-button elementor-button-next elementor-size-<?php echo $settings['dce_step_button_size']; ?>" data-target="' + target_id + '"><span><span class="elementor-button-text">' + jQuery(this).val() + '</span></span></button>');
                                                    } else {
                                                        var target_id = jQuery(this).closest('.dce-form-step').prev().attr('data-custom_id');
                                                        //jQuery(this).addClass('elementor-button-prev');
                                                        jQuery(this).after('<button type="button" class="<?php echo $btn_class; ?>elementor-button dce-step-elementor-button elementor-button-previous elementor-size-<?php echo $settings['dce_step_button_size']; ?>" data-target="' + target_id + '"><span><span class="elementor-button-text">' + jQuery(this).val() + '</span></span></button>');
                                                    }
                                                    jQuery(this).hide();
                                                    //console.log(target_id);
                                                    jQuery(this).attr('data-target', target_id);
                                                });
                    <?php } ?>
                                        }

                                        jQuery('.elementor-element-' + form_id + ' .elementor-button-previous').each(function () {
                                            //jQuery(this).off();
                                            jQuery(this).on('click', function () {
                                                var target = jQuery(this).attr('data-target');
                                                var step = jQuery(this).closest('.dce-form-step');
                                                //var step_prev = jQuery('.elementor-element-' + form_id + ' #dce-form-step-'+target);
                                                //if (step_prev) {
                                                dce_show_step(target, '<?php echo $widget->get_id(); ?>', 'previous', <?php echo !empty($settings['dce_step_scroll']) ? 'true' : 'false'; ?>);
                                                //} else {

                                                //}
                                                return false;
                                            });
                                        });
                                        jQuery('.elementor-element-' + form_id + ' .elementor-button-next').each(function () {
                                            //jQuery(this).off();
                                            jQuery(this).on('click', function () {
                                                var target = jQuery(this).attr('data-target');
                                                var step = jQuery(this).closest('.dce-form-step');
                                                var next = dce_validate_step(step);
                                                if (next) {
                                                    //dce_replace_field_shortcode(target, '<?php echo $widget->get_id(); ?>');
                                                    dce_show_step(target, '<?php echo $widget->get_id(); ?>', 'next', <?php echo !empty($settings['dce_step_scroll']) ? 'true' : 'false'; ?>);
                                                }
                                                return false;
                                            });
                                        });
                                        jQuery('.elementor-element-' + form_id + ' .dce-form-step-progressbar .elementor-button-progressbar').each(function () {
                                            jQuery(this).on('click', function () {
                                                var target = jQuery(this).attr('data-target');
                                                var next = true;
                                                jQuery(this).closest('.dce-form-step-progressbar').prevAll().each(function () {
                                                    var custom_id = jQuery(this).find('.elementor-button').attr('data-target');
                                                    //console.log(custom_id);
                                                    next = dce_validate_step(jQuery('#dce-form-step-' + custom_id));
                                                });
                                                if (next) {
                                                    dce_show_step(target, '<?php echo $widget->get_id(); ?>', <?php echo !empty($settings['dce_step_scroll']) ? 'true' : 'false'; ?>);
                                                }
                                                return false;
                                            });
                                        });


                                    }
                    <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                                    }
                                };
                                $(window).on('elementor/frontend/init', function () {
                                    elementorFrontend.hooks.addAction('frontend/element_ready/form.default', <?php echo $jkey; ?>);
                                });
                    <?php } ?>
                        })(jQuery, window);
                    </script>
                    <?php
                    $js = ob_get_clean();
                    //var_dump($js); die();
                    //wp_enqueue_script('dce-form-step');
					wp_enqueue_script('dce-form-step', plugins_url('/assets/js/dce-form-step.js', DCE__FILE__), [], DCE_VERSION	);
					
                    $js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $js, $widget->get_id());
                    $content .= $js;

                    if (version_compare(ELEMENTOR_PRO_VERSION, '2.10.0') >= 0 && $settings['dce_step_show']) {
                        $css = '<style>.elementor-element-' . $widget->get_id() . ' .e-form__buttons__wrapper.elementor-field-type-next, .elementor-element-' . $widget->get_id() . ' .e-form__buttons__wrapper.elementor-field-type-previous { display: none; }</style>';
                        $content .= $css;
                    }

                    $new_content = $this->_progressbar($widget) . $content . $this->_summary($widget);                                        
                }
            }

            return $new_content;
        }

    }

}
