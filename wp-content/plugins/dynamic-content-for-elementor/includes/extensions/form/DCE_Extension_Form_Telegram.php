<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use DynamicContentForElementor\DCE_Helper;
use DynamicContentForElementor\DCE_Tokens;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_telegram($field) {
    switch ($field) {
        case 'enabled':
            return false;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/dynamic-email-for-elementor-pro-form/';
        case 'description' :
            return __('Add Telegram Actions to Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro') || !class_exists('ElementorPro\Modules\Forms\Classes\Action_Base')) {

    class DCE_Extension_Form_Telegram extends DCE_Extension_Prototype {

        public $name = 'Form Telegram';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_telegram('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_telegram('description');
        }

        public function get_docs() {
            return _dce_extension_form_telegram('docs');
        }

    }

} else {
    
    class DCE_Extension_Form_Telegram extends \ElementorPro\Modules\Forms\Classes\Action_Base {

        public $name = 'Form Telegram';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo';

        static public function is_enabled() {
            return _dce_extension_form_telegram('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_telegram('description');
        }

        public function get_docs() {
            return _dce_extension_form_telegram('docs');
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
            return 'dce_form_telegram';
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
            return __('Telegram', 'dynamic-content-for-elementor');
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
                    'section_dce_form_telegram',
                    [
                        'label' => $this->get_label(), //__('DCE', 'dynamic-content-for-elementor'),
                        'condition' => [
                            'submit_actions' => $this->get_name(),
                        ],
                    ]
            );

            $repeater_fields = new \Elementor\Repeater();
            $repeater_fields->add_control(
                    'dce_form_telegram_enable',
                    [
                        'label' => __('Enable Message', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'default' => 'yes',
                        'description' => __('You can temporary disable it without delete settings and reactivate it next time', 'dynamic-content-for-elementor'),
                        'separator' => 'after',
                    ]
            );
            $repeater_fields->add_control(
                    'dce_form_telegram_condition_field', [
                'label' => __('Condition', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'description' => __('Write here the ID of the form field to check, or leave empty to always send this email', 'dynamic-content-for-elementor'),
                    ]
            );
            $repeater_fields->add_control(
                    'dce_form_telegram_condition_status', [
                'label' => __('Condition Status', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'empty' => [
                        'title' => __('Empty', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-circle-o',
                    ],
                    'valued' => [
                        'title' => __('Valued', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-dot-circle-o',
                    ],
                    'lt' => [
                        'title' => __('Less than', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-angle-left',
                    ],
                    'gt' => [
                        'title' => __('Greater than', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-angle-right',
                    ],
                    'equal' => [
                        'title' => __('Equal to', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-check-circle-o ',
                    ],
                    'contain' => [
                        'title' => __('Contain', 'dynamic-content-for-elementor'),
                        'icon' => 'fa fa-inbox ',
                    ]
                ],
                'default' => 'valued',
                'toggle' => false,
                'label_block' => true,
                'condition' => [
                    'dce_form_telegram_condition_field!' => '',
                ],
                    ]
            );
            $repeater_fields->add_control(
                    'dce_form_telegram_condition_value', [
                'label' => __('Condition Value', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'description' => __('A value to compare the value of the field', 'dynamic-content-for-elementor'),
                'condition' => [
                    'dce_form_telegram_condition_field!' => '',
                    'dce_form_telegram_condition_status' => ['lt', 'gt', 'equal', 'contain'],
                ],
                    ]
            );

          

            $repeater_fields->add_control(
                    'dce_form_telegram_content',
                    [
                        'label' => __('Message', 'elementor-pro'),
                        'type' => Controls_Manager::WYSIWYG,
                        'default' => '[all-fields]',
                        'placeholder' => '[all-fields]',
                        'description' => sprintf(__('By default, all form fields are sent via %s shortcode. To customize sent fields, copy the shortcode that appears inside each field and paste it above.', 'elementor-pro'), '<code>[all-fields]</code>'),
                        'label_block' => true,
                        'render_type' => 'none',
                        'condition' => [
                            'dce_form_email_content_type_advanced' => 'text',
                        ],
                    ]
            );

            $repeater_fields->add_control(
                    'dce_form_telegram_attachments',
                    [
                        'label' => __('Add Upload files as Attachments', 'dynamic-content-for-elementor'),
                        'type' => \Elementor\Controls_Manager::SWITCHER,
                        'description' => __('Send all Uploaded Files as Email Attachments', 'dynamic-content-for-elementor'),
                        'separator' => 'before',
                    ]
            );


            $widget->add_control(
                    'dce_form_telegram_repeater', [
                'label' => __('Messages', 'dynamic-content-for-elementor'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'title_field' => '{{{ dce_form_email_subject }}}',
                'fields' => $repeater_fields->get_controls(),
                'description' => __('Send all Email you need', 'dynamic-content-for-elementor'),
                    ]
            );

            $widget->add_control(
                    'dce_form_telegram_help', [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => '<div id="elementor-panel__editor__help" class="p-0"><a id="elementor-panel__editor__help__link" href="'.$this->get_docs().'" target="_blank">'.__( 'Need Help', 'elementor' ).' <i class="eicon-help-o"></i></a></div>',
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

            $fields = DCE_Helper::get_form_data($record);

            $post_id = $_POST['post_id'];
            $form_id = $_POST['form_id'];

            if (!empty($fields['submitted_on_id'])) {
                // force post for Dynamic Tags and Widgets
                $submitted_on_id = $fields['submitted_on_id'];
                global $post, $wp_query;
                $post = get_post($submitted_on_id);
                $wp_query->queried_object = $post;
                $wp_query->queried_object_id = $submitted_on_id;
            }

            //$documenta_data = DCE_Helper::get_elementor_data($post_id);
            $document = \Elementor\Plugin::$instance->documents->get( $post_id );
            if ( $document ) {
                $form = \ElementorPro\Modules\Forms\Module::find_element_recursive( $document->get_elements_data(), $form_id );
                //$form = DCE_Helper::get_element_by_id($form_id, $post_id);
                $widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $form );
                $settings = $widget->get_settings_for_display();
            } else {
                $settings = $record->get('form_settings');
            }
            $settings = DCE_Helper::get_dynamic_value($settings, $fields);            

            $this->dce_elementor_form_telegram($fields, $settings, $ajax_handler, $record);
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

        function dce_elementor_form_telegram($fields, $settings = null, $ajax_handler = null, $record = null) {
            /*
            https://telegram-bot-sdk.readme.io/docs/initial-setup
            https://irazasyed.github.io/telegram-bot-sdk/usage/available-methods-examples/
            https://telegram-bot-sdk.readme.io/reference#senddocument
            */
            
            foreach ($settings['dce_form_email_repeater'] as $mkey => $amail) {

                if ($amail['dce_form_email_enable']) {

                    $condition_satisfy = true;
                    if (!empty($amail['dce_form_email_condition_field'])) {
                        $field_value = $fields[$amail['dce_form_email_condition_field']];
                        switch ($amail['dce_form_email_condition_status']) {
                            case 'empty':
                                if (!empty($field_value)) {
                                    $condition_satisfy = false;
                                }
                                break;
                            case 'valued':
                                if (empty($field_value)) {
                                    $condition_satisfy = false;
                                }
                                break;
                            case 'lt':
                                if (empty($field_value) || $field_value > $amail['dce_form_email_condition_value']) {
                                    $condition_satisfy = false;
                                }
                                break;
                            case 'gt':
                                if (empty($field_value) || $field_value < $amail['dce_form_email_condition_value']) {
                                    $condition_satisfy = false;
                                }
                                break;
                            case 'equal':
                                if ($field_value != $amail['dce_form_email_condition_value']) {
                                    $condition_satisfy = false;
                                }
                            case 'contain':
                                $field_type = DCE_Helper::get_field_type($amail['dce_form_email_condition_field'], $settings);                                                                
                                if ($field_type == 'checkbox') {
                                    $field_value = DCE_Helper::str_to_array(', ', $field_value);
                                }
                                if (is_array($fields[$amail['dce_form_email_condition_field']])) {
                                    if (!in_array($amail['dce_form_email_condition_value'], $field_value)) {
                                        $condition_satisfy = false;
                                    }
                                } else {
                                    if (strpos($field_value, $amail['dce_form_email_condition_value']) === false) {
                                        $condition_satisfy = false;
                                    }
                                }
                                break;
                        }
                    }

                    if ($condition_satisfy) {


                        $line_break = "\n";
                        
                        $settings_raw = $record->get('form_settings');
                        // from message textarea with dynamic token
                        $dce_form_email_content = $settings_raw['dce_form_email_repeater'][$mkey]['dce_form_email_content'];
                        $attachments = $this->get_email_attachments($dce_form_email_content, $fields, $amail);
                        $dce_form_email_content = $this->remove_attachment_tokens($dce_form_email_content, $fields);
                        $dce_form_email_content = $this->replace_content_shortcodes($dce_form_email_content, $record, $line_break);
                        $dce_form_email_content = DCE_Helper::get_dynamic_value($dce_form_email_content, $fields);

                        // generate the TEXT/PLAIN version
                        $dce_form_email_content_txt = $dce_form_email_content;
                        $dce_form_email_content_txt = str_replace('</p>', '</p><br /><br />', $dce_form_email_content_txt);
                        $dce_form_email_content_txt = str_replace('<br />', "\n", $dce_form_email_content_txt);
                        $dce_form_email_content_txt = str_replace('<br>', "\n", $dce_form_email_content_txt);
                        $dce_form_email_content_txt = strip_tags($dce_form_email_content_txt);

                        if ($send_html) {                                
                            add_action( 'phpmailer_init', [$this, 'set_wp_mail_altbody'] );                                                                
                        } else {
                            $dce_form_email_content = $dce_form_email_content_txt;
                            $dce_form_email_content_txt = '';
                        }

                        $dce_form_email_content = apply_filters('elementor_pro/forms/wp_mail_message', $dce_form_email_content);
                        
                            
                        self::$txt = $dce_form_email_content_txt;
                        //$phpmailer->AltBody = $dce_form_email_content_txt;
                                
                        // replace single fields shorcode
                        $dce_form_email_content = DCE_Helper::replace_setting_shortcodes($dce_form_email_content, $fields);

                        
                        
                        /* if (!empty($email_fields['dce_form_email_to_bcc'])) {
                          $bcc_emails = explode(',', $email_fields['dce_form_email_to_bcc']);
                          foreach ($bcc_emails as $bcc_email) {
                          wp_mail(trim($bcc_email), $email_fields['dce_form_email_subject'], $dce_form_email_content, $headers);
                          }
                          } */

                        /**
                         * Elementor form mail sent.
                         *
                         * Fires when an email was sent successfully.
                         *
                         * @since 1.0.0
                         *
                         * @param array       $settings Form settings.
                         * @param Form_Record $record   An instance of the form record.
                         */
                        do_action('elementor_pro/forms/telegram_sent', $amail, $record);
                        //do_action('dynamic_content_for_elementor/forms/mail_sent', $settings, $record);

                        if (!$email_sent) {
                            $ajax_handler->add_error_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::get_default_message(\ElementorPro\Modules\Forms\Classes\Ajax_Handler::SERVER_ERROR, $amail));
                        } else {
                            if ($amail['dce_form_email_attachments'] && $amail['dce_form_email_attachments_delete']) {
                                $remove_uploaded_files = true;
                            }
                        }
                    }
                }
            }           
        }

        public function remove_attachment_tokens($dce_form_email_content, $fields) {
            $attachments_tokens = explode(':attachment]', $dce_form_email_content);
            foreach ($attachments_tokens as $akey => $avalue) {
                $pieces = explode('[form:', $avalue);
                if (count($pieces) > 2) {
                    $field = end($pieces);
                    if (isset($fields[$field])) {
                        $dce_form_email_content = str_replace('[form:'.$field.':attachment]', '', $dce_form_email_content);
                    }
                }
            }
            return $dce_form_email_content;
        }

        public function get_email_attachments($dce_form_email_content, $fields, $amail) {
            $attachments = array();
            $pdf_attachment = '<!--[dce_form_pdf:attachment]-->';
            $pdf_form = '[form:pdf]';
            $pos_pdf_token = strpos($dce_form_email_content, $pdf_attachment);
            $pos_pdf_form = strpos($dce_form_email_content, $pdf_form);
            if ($pos_pdf_token !== false || $pos_pdf_form !== false) {
                // add PDF as attachment
                global $dce_form;
                if (isset($dce_form['pdf']) && isset($dce_form['pdf']['path'])) {
                    $pdf_path = $dce_form['pdf']['path'];
                    $attachments[] = $pdf_path;
                }
                $dce_form_email_content = str_replace($pdf_attachment, '', $dce_form_email_content);
                $dce_form_email_content = str_replace($pdf_form, '', $dce_form_email_content);
            }

            $attachments_tokens = explode(':attachment]', $dce_form_email_content);
            foreach ($attachments_tokens as $akey => $avalue) {
                $pieces = explode('[form:', $avalue);
                if (count($pieces) > 1) {
                    $field = end($pieces);
                    if (isset($fields[$field])) {
                        $file_path = DCE_Helper::url_to_path($fields[$field]);
                        if (is_file($file_path)) {
                            $attachments[] = $file_path;
                        }
                    }
                }
            }
            if ($amail['dce_form_email_attachments']) {
                if (!empty($fields) && is_array($fields)) {
                    foreach ($fields as $akey => $adatas) {
                        $afield = DCE_Helper::get_field($akey, $settings);
                        if ($afield) {
                            if ($afield['field_type'] == 'upload') {
                                $files = DCE_Helper::str_to_array(',', $adatas);
                                if (!empty($files)) {
                                    foreach($files as $adata) {
                                        if (filter_var($adata, FILTER_VALIDATE_URL)) {
                                            //$adata = str_replace(get_bloginfo('url'), WP, $value);
                                            $filename = DCE_Helper::url_to_path($adata);
                                            if (is_file($filename)) {
                                                if (!in_array($filename, $attachments)) {
                                                    $attachments[] = $file_path;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $attachments;
        }

        /**
         * @param string      $email_content
         * @param Form_Record $record
         *
         * @return string
         */
        public function replace_content_shortcodes($email_content, $record, $line_break) {
            
            $all_fields_shortcode = '[all-fields]';
            $text = $this->get_shortcode_value($all_fields_shortcode, $email_content, $record, $line_break);
            $email_content = str_replace($all_fields_shortcode, $text, $email_content);
            
            $all_valued_fields_shortcode = '[all-fields|!empty]';
            $text = $this->get_shortcode_value($all_valued_fields_shortcode, $email_content, $record, $line_break, false);
            $email_content = str_replace($all_fields_shortcode, $text, $email_content);                        
            
            return $email_content;
        }
        
        public function get_shortcode_value($shortcode, $email_content, $record, $line_break, $show_empty = true) {
            $text = '';
            if (false !== strpos($email_content, $shortcode)) {
                foreach ($record->get('fields') as $field) {
                    $formatted = '';
                    if (!empty($field['title'])) {
                        $formatted = sprintf('%s: %s', $field['title'], $field['value']);
                    } elseif (!empty($field['value'])) {
                        $formatted = sprintf('%s', $field['value']);
                    }
                    if (( 'textarea' === $field['type'] ) && ( '<br>' === $line_break )) {
                        $formatted = str_replace(["\r\n", "\n", "\r"], '<br />', $formatted);
                    }
                    if (!$show_empty && empty($field['value'])) continue;
                    $text .= $formatted . $line_break;
                }
            }
            return $text;
        }

        public static function add_dce_email_template_type() {
            // Add Email Template Type
            include_once( DCE_PATH .'modules/theme-builder/documents/DCE_Email.php' );
            $dce_email = '\ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email';
            \Elementor\Plugin::instance()->documents->register_document_type( $dce_email::get_name_static(), \ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email::get_class_full_name() );
            \Elementor\TemplateLibrary\Source_Local::add_template_type( \ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email::get_name_static() );
            add_filter( 'elementor_pro/editor/localize_settings', '\ElementorPro\Modules\ThemeBuilder\Documents\DCE_Email::dce_add_more_types' );
        }

    }



}