<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_max($field) {
    switch ($field) {
        case 'enabled':
            return false;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/';
        case 'description' :
            return __('Add Max submission count to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro') || !class_exists('ElementorPro\Modules\Forms\Classes\Action_Base')) {

    class DCE_Extension_Form_Max extends DCE_Extension_Prototype {

        public $name = 'Form Max submissions';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_max('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_max('description');
        }

        public function get_docs() {
            return _dce_extension_form_max('docs');
        }

    }

} else {

    class DCE_Extension_Form_Max extends \ElementorPro\Modules\Forms\Classes\Action_Base {

        public $name = 'Form Max submissions';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/widget/';
        public $has_action = true;

        /**
         * Constructor
         *
         * @since 0.0.1
         *
         * @access public
         */
        public function __construct() {
            $this->init();
        }

        static public function is_enabled() {
            return _dce_extension_form_max('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_max('description');
        }

        public function get_docs() {
            return _dce_extension_form_max('docs');
        }

        public static function get_plugin_depends() {
            return self::$depended_plugins;
        }

        static public function get_satisfy_dependencies($ret = false) {
            return true;
        }
        
        public function get_script_depends() {
            return [];
        }
        public function get_style_depends() {
            return [];
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
            return 'dce_form_max';
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
            return __('Limit Max Submissions', 'dynamic-content-for-elementor');
        }

        public function init($param = null) {
            if (_dce_extension_form_max('enabled')) {
                //add_action( 'elementor_pro/init', function() {
                //add_action("elementor/frontend/widget/before_render", array($this, '_before_render_form'));
                add_action("elementor/widget/render_content", array($this, '_render_form'), 10, 2);
                //add_action( 'elementor_pro/forms/render/item', [ $this, 'set_field_value' ], 10, 3 );
                //add_filter( 'elementor_pro/forms/render/item/text', array($this, 'set_field_value'), 10, 3 );
                //});
            }
        }

        public function _render_form($content, $widget) {
            $new_content = $content;
            if ($widget->get_name() == 'form') {
                
            }
            return $new_content;
        }

        /**
         * Register Settings Section
         *
         * Registers the Action controls
         *
         * @access public
         * @param \Elementor\Widget_Base $widget
         */
        public function register_settings_section($widget) {
            $widget->start_controls_section(
                    'section_dce_form_max',
                    [
                        'label' => $this->get_label(),
                        'condition' => [
                            'submit_actions' => $this->get_name(),
                        ],
                    ]
            );
            
            $widget->add_control(
                    'dce_form_max_total', [
                'label' => __('Total Max submissions', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                'description' => __('Leave 0 for infinite.', 'dynamic-content-for-elementor').'<br>'.__('NOTE: if you previously set 250 and now you would like to allow 10 more submissions, then increase the submission limit to 260 (250 current submissions + 10)', 'dynamic-content-for-elementor'),
                    ]
            );
            
            $widget->add_control(
                    'dce_form_max_time', [
                'label' => __('Max per Time', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                    ]
            );
            $widget->add_control(
                    'dce_form_max_time_length', [
                'label' => __('Time', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => [
                    'hour' => __('Hour', 'dynamic-content-for-elementor'),
                    'day' => __('Day', 'dynamic-content-for-elementor'),
                    'week' => __('Week', 'dynamic-content-for-elementor'),
                    'month' => __('Month', 'dynamic-content-for-elementor'),
                    'year' => __('Year', 'dynamic-content-for-elementor'),
                ],
                'condition' => [
                    'dce_form_max_time!' => '',
                ],
                    ]
            );
            
            $widget->add_control(
                    'dce_form_max_user', [
                'label' => __('Max per User', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'description' => __('Available only for Logged User', 'dynamic-content-for-elementor'),
                'min' => 0,
                    ]
            );
            
            $widget->add_control(
                    'dce_form_max_field', [
                'label' => __('Max per Unique field', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 0,
                    ]
            );
            $widget->add_control(
                    'dce_form_max_field_field', [
                'label' => __('Unique field', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'condition' => [
                    'dce_form_max_field!' => '',
                ],
                    ]
            );

            $widget->add_control(
                    'dce_form_max_add', [
                'label' => __('Unit per submission', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 1,
                'placeholder' => '[form:my_num_field] * 2',
                'label_block' => true,
                'separator' => 'before',
                    ]
            );
            
            $widget->add_control(
                    'dce_form_max_message', [
                'label' => __('Sorry Message', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Sorry, we have reached max sumsission number for this form', 'dynamic-content-for-elementor'),
                'separator' => 'before',
                    ]
            );
            $widget->add_control(
                    'dce_form_max_no_btn', [
                'label' => __('Remove submit button', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                    ]
            );
            $widget->add_control(
                    'dce_form_max_no_msg', [
                'label' => __('Use different Message for Closed form', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'condition' => [                    
                    'dce_form_max_no_btn' => '',
                ],
                    ]
            );
            $widget->add_control(
                    'dce_form_max_no_message', [
                'label' => __('Closed form Message', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Sorry, we have reached max sumsission number for this form', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_max_no_btn' => '',
                    'dce_form_max_no_msg!' => '',
                ],
                    ]
            );
            
            $widget->add_control(
                    'dce_form_max_help', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="' . $this->get_docs() . '" target="_blank">' . __('Need Help', 'elementor') . ' <i class="eicon-help-o"></i></a></div>',
                'separator' => 'before',
                    ]
            );

            $widget->end_controls_section();
        }

        /**
         * Run
         *
         * Runs the action after submit
         *
         * @access public
         * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
         * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
         */
        public function run($record, $ajax_handler) {
            $settings = $record->get('form_settings');

            $fields = DCE_Helper::get_form_data($record);

            $this->dce_elementor_form_max($fields, $settings, $ajax_handler);
        }

        /**
         * On Export
         *
         * Clears form settings on export
         * @access Public
         * @param array $element
         */
        public function on_export($element) {
            $tmp = array();
            if (!empty($element)) {
                foreach ($element as $key => $value) {
                    if (substr($key, 0, 4) == 'dce_') {
                        $element[$key];
                    }
                }
            }
        }

        function dce_elementor_form_max($record, $settings = null, $ajax_handler = null) {
            $fields = array();
            if (is_object($record)) {
                // from add action
                $data = $record->get_formatted_data(true);
                foreach ($data as $label => $value) {
                    $fields[$label] = sanitize_text_field($value);
                }
                $fields['form_name'] = $record->get_form_settings('form_name');
            } else {
                // from form extension
                $fields = $record;
                /* $form_record = new \ElementorPro\Modules\Forms\Classes\Form_Record();
                  $record = $form_record; */
                $fields['form_name'] = $settings['form_name'];
            }

            if ($ajax_handler->is_success) {
                $message_html = __('Sorry, we have reached max sumsission number for this form', 'dynamic-content-for-elementor');
                wp_send_json_error([
                    'message' => $message_html,
                    'data' => $ajax_handler->data,
                ]);
                die();
            }
        }

    }

}