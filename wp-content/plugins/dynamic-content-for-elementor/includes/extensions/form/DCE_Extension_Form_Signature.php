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

function _dce_extension_form_signature($field) {
    switch ($field) {
        case 'enabled':
            return false;
        case 'docs':
            return 'https://www.dynamic.ooo/';
        case 'description' :
            return __('Add Amount Signature to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro')) {

    class DCE_Extension_Form_Signature extends DCE_Extension_Prototype {

        public $name = 'Form Signature';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_signature('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_signature('description');
        }

        public function get_docs() {
            return _dce_extension_form_signature('docs');
        }

    }

} else {

    class DCE_Extension_Form_Signature extends DCE_Extension_Prototype {

        public $name = 'Form Signature';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/';
        private $is_common = false;
        public $has_action = false;

        static public function is_enabled() {
            return _dce_extension_form_signature('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_signature('description');
        }

        public function get_docs() {
            return _dce_extension_form_signature('docs');
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
            return 'dce_form_signature';
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
            return __('Form Signature', 'dynamic-content-for-elementor');
        }

        /**
         * Add Actions
         *
         * @since 0.5.5
         *
         * @access private
         */
        protected function add_actions() {
            add_action("elementor/widget/render_content", array($this, '_render_form'), 10, 2);
            add_action('elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2);
        }

        public function add_control_section_to_form($element, $args) {

            //https://github.com/szimek/signature_pad
            
            $element->start_controls_section(
                    'dce_amount_section_style',
                    [
                        'label' => __('Amount', 'dynamic-content-for-elementor'),
                        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    ]
            );
            $element->add_control(
                    'dce_amount_heading_input',
                    [
                        'label' => __('Input', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                    ]
            );
            $element->add_responsive_control(
                    'dce_amount_align',
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
                            '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'text-align: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_amount_opacity', [
                'label' => __('Opacity (%)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'opacity: {{SIZE}};',
                ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_amount_padding', [
                'label' => __('Padding', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );
            $element->add_responsive_control(
                    'dce_amount_margin', [
                'label' => __('Margin', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );
            
            $element->add_control(
                    'dce_amount_color',
                    [
                        'label' => __('Color', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(), [
                'name' => 'dce_amount_typography',
                'label' => __('Typography', 'dynamic-content-for-elementor'),
                'selector' => '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)',
                    ]
            );
            $element->add_responsive_control(
                    'dce_amount_position',
                    [
                        'label' => __('Position', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'row-reverse' => [
                                'title' => __('Left', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-left',
                            ],
                            'row' => [
                                'title' => __('Right', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-right',
                            ],
                        ],
                        'default' => 'row',
                        'selectors' => [
                            '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount' => 'flex-direction: {{VALUE}};',
                        ],
                    ]
            );
            /*$element->add_responsive_control(
                    'dce_amount_display',
                    [
                        'label' => __('Display', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'left' => [
                                'title' => __('Inline', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-left',
                            ],
                            'center' => [
                                'title' => __('Block', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-align-center',
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-field-type-amount input' => 'text-align: {{VALUE}};',
                        ],
                    ]
            );*/
            
            $element->add_control(
                    'dce_amount_space',
                    [
                        'label' => __('Width', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SLIDER,
                        'default' => [
                            'size' => 100,
                        ],
                        'range' => [
                            '%' => [
                                'min' => 20,
                                'max' => 100,
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'flex-basis: {{SIZE}}%; max-width: {{SIZE}}%;',
                        ],
                    ]
            );

            // Border ----------------
            /*$element->add_control(
                    'dce_amount_heading_border',
                    [
                        'label' => __('Border', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );*/
            $element->add_group_control(
                    Group_Control_Border::get_type(), [
                'name' => 'dce_amount_border',
                'label' => __('Border', 'dynamic-content-for-elementor'),
                'selector' => '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)',
                    ]
            );
            $element->add_control(
                    'dce_amount_border_radius', [
                'label' => __('Border Radius', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                    ]
            );

            // Background ----------------
            /*$element->add_control(
                    'dce_amount_heading_background',
                    [
                        'label' => __('Background', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );*/
            $element->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'dce_amount_background',
                        'types' => ['classic', 'gradient'],
                        'selector' => '{{WRAPPER}} .elementor-field-type-amount.elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper)',
                    ]
            );

            // Title ----------------
            $element->add_control(
                    'dce_amount_heading_title',
                    [
                        'label' => __('Label', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $element->add_responsive_control(
                    'dce_amount_title_align',
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
                            '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label' => 'width: 100%; text-align: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_control(
                    'dce_amount_title_color',
                    [
                        'label' => __('Color', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label' => 'color: {{VALUE}};',
                        ],
                    ]
            );
            $element->add_group_control(
                    Group_Control_Typography::get_type(), [
                'name' => 'dce_amount_title_typography',
                'label' => __('Typography', 'dynamic-content-for-elementor'),
                'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label',
                    ]
            );            
            $element->add_group_control(
                    Group_Control_Text_Shadow::get_type(),
                    [
                        'name' => 'dce_amount_text_shadow',
                        'selector' => '{{WRAPPER}} .elementor-field-group.elementor-field-type-amount > label.elementor-field-label',
                    ]
            );

            $element->end_controls_section();
        }

        public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = []) {
            //echo 'adsa: '; var_dump($control_id); //die();
            if ($element->get_name() == 'form') {


                if ($control_id == 'form_fields') {
                    $control_data["fields"]["field_type"]["options"]['amount'] = __('Amount', 'dynamic-content-for-elementor');

                    if ($control_id == 'form_fields') {
                        $control_data['fields']['dce_amount_expression'] = array(
                            'name' => 'dce_amount_expression',
                            'label' => __('Amount Expression', 'dynamic-content-for-elementor'),
                            'type' => Controls_Manager::TEXT,
                            'placeholder' => __('[form:field_1] * [form:field_2] + 1.4', 'dynamic-content-for-elementor'),
                            'label_block' => true,
                            'conditions' => [
                                'terms' => [
                                    [
                                        'name' => 'field_type',
                                        'value' => 'amount',
                                    ],
                                ],
                            ],
                            "tabs_wrapper" => "form_fields_tabs",
                            "tab" => "content",
                        );
                    }
                }
                if ($control_id == '') {
                    
                }
            }

            return $control_data;
        }

        public function _render_form($content, $widget) {
            $new_content = $content;
            if ($widget->get_name() == 'form') {
                $settings = $widget->get_settings_for_display();

                // FIELDS
                $fields = $form_fields = array();
                if (!empty($settings['form_fields'])) {
                    foreach ($settings['form_fields'] as $key => $afield) {
                        $form_fields[$afield['custom_id']] = $afield;
                        if ($afield["field_type"] == 'amount') {
                            $fields[] = $afield;
                            $field_class = 'elementor-field-group-' . $afield['custom_id'];
                            $pieces = explode($field_class, $new_content, 2);
                            if (count($pieces) > 1) {
                                $tmp = explode('</div>', end($pieces), 2);
                                if (count($tmp) > 1) {
                                    $amount_field = '<input value="0" type="hidden" name="form_fields[' . $afield['custom_id'] . ']" id="dce-form-field-' . $afield['custom_id'] . '"><input class="elementor-field" value="0" type="text" name="form_fields[dce-' . $afield['custom_id'] . ']" id="form-field-' . $afield['custom_id'] . '" disabled>';
                                    $new_content = reset($pieces) . $field_class . reset($tmp) . $amount_field . '</div>' . end($tmp);
                                }
                            }
                        }
                    }
                }

                if (!empty($fields)) {
                    $has_amount = false;
                    // add custom js
                    $fields = array();
                    $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_amount';
                    ob_start();
                    ?>
                    <script id="<?php echo $jkey; ?>">
                        (function ($) {
                    <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                                var <?php echo $jkey; ?> = function ($scope, $) {
                                    if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                        <?php
                    }
                    if (!empty($settings['form_fields'])) {
                        foreach ($settings['form_fields'] as $key => $afield) {
                            if ($afield["field_type"] == 'amount') {
                                $has_amount = true;
                                $js_expression = $afield['dce_amount_expression'];
                                $fields_name = array();
                                $pieces = explode('[form:', $js_expression);
                                foreach ($pieces as $apiece) {
                                    $tmp = explode(']', $apiece, 2);
                                    if (count($tmp) > 1) {
                                        $field_name = reset($tmp);
                                        if (isset($form_fields[$field_name])) {
                                            $field_input_name = '.elementor-element-' . $widget->get_id() . ' .elementor-field-group-' . $field_name . ' ';
                                            if ($form_fields[$field_name]['field_type'] == 'select') {
                                                $field_input_name .= 'select';
                                            } else {
                                                $field_input_name .= 'input';
                                            }
                                            $fields_name[] = $exp_field_input_name = $field_input_name;
                                            if ($form_fields[$field_name]['field_type'] == 'radio') {
                                                $exp_field_input_name .= ':checked';
                                            }
                                            $js_expression = str_replace('[form:' . $field_name . ']', '(parseFloat(jQuery("' . $exp_field_input_name . '").val())||0)', $js_expression);
                                        } else {
                                            $js_expression = str_replace('[form:' . $field_name . ']', '0', $js_expression);
                                        }
                                    }
                                }
                                if (!empty($fields_name)) {
                                    ?>
                                                    console.log('<?php echo implode(', ', $fields_name); ?>');
                                                    jQuery('<?php echo implode(', ', $fields_name); ?>').on('change', function () {
                                                        jQuery('#form-field-<?php echo $afield['custom_id']; ?>, #dce-form-field-<?php echo $afield['custom_id']; ?>').val(<?php echo $js_expression; ?>);
                                                    });
                                                    jQuery('<?php echo reset($fields_name); ?>').trigger('change');
                                    <?php
                                }
                            }
                        }
                    }
                    if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                        ?>
                                    }
                                };
                                $(window).on("elementor/frontend/init", function () {
                                    elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php echo $jkey; ?>);
                                });
                    <?php } ?>
                        })(jQuery, window);
                    </script>
                    <?php
                    $js = ob_get_clean();

                    if ($has_amount) {
                        $js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $js);
                        $new_content .= $js;
                    }
                }
            }

            return $new_content;
        }

    }

}
