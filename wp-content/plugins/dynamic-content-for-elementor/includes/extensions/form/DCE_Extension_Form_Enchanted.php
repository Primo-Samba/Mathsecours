<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

function _dce_extension_form_enchanted($field) {
    switch ($field) {
        case 'enabled':
            return true;
        case 'docs':
            return 'https://www.dynamic.ooo/widget/enchanted-form-for-elementor-pro-form/';
        case 'description' :
            return __('Add Select2, Password Check, Icons and more in Elementor PRO Form', 'dynamic-content-for-elementor');
    }
}

if (!DCE_Helper::is_plugin_active('elementor-pro')) {

    class DCE_Extension_Form_Enchanted extends DCE_Extension_Prototype {

        public $name = 'Form Enchanted';
        private $is_common = false;
        public static $depended_plugins = ['elementor-pro'];

        static public function is_enabled() {
            return _dce_extension_form_enchanted('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_enchanted('description');
        }

        public function get_docs() {
            return _dce_extension_form_enchanted('docs');
        }

    }

} else {

    class DCE_Extension_Form_Enchanted extends DCE_Extension_Prototype {

        public $name = 'Form Enchanted';
        public static $depended_plugins = ['elementor-pro'];
        public static $docs = 'https://www.dynamic.ooo/';
        private $is_common = false;
        public $has_action = false;

        static public function is_enabled() {
            return _dce_extension_form_enchanted('enabled');
        }

        public static function get_description() {
            return _dce_extension_form_enchanted('description');
        }

        public function get_docs() {
            return _dce_extension_form_enchanted('docs');
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
            return 'dce_form_enchanted';
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
            return __('Form Enchanted', 'dynamic-content-for-elementor');
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

            add_action('elementor/widget/print_template', function($template, $widget) {
                if ('form' === $widget->get_name()) {
                    $template = false;
                }
                return $template;
            }, 10, 2);

            add_action("elementor_pro/forms/render_field/submit", array($this, '_render_submit'));
            add_action("elementor_pro/forms/render_field/reset", array($this, '_render_reset'));
            add_action("elementor/element/form/section_button_style/after_section_end", array($this, '_add_form_reset_style'));

            if (!is_admin()) {
                wp_register_script(
                        'jquery-elementor-select2',
                        ELEMENTOR_ASSETS_URL . 'lib/e-select2/js/e-select2.full.min.js',
                        [
                            'jquery',
                        ],
                        '4.0.6-rc.1',
                        true
                );

                wp_register_style(
                        'elementor-select2',
                        ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2.min.css',
                        [],
                        '4.0.6-rc.1'
                );

                wp_register_style(
                        'font-awesome',
                        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/font-awesome.min.css',
                        [],
                        '4.7.0'
                );
                wp_register_style(
                        'fontawesome',
                        ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/fontawesome.min.css',
                        [],
                        '5.9.0'
                );
            }
        }

        public function _render_form($content, $widget) {
            $new_content = $content;
            if ($widget->get_name() == 'form') {
                $new_content = $this->_add_icon($new_content, $widget);
                $new_content = $this->_add_description($new_content, $widget);
                $new_content = $this->_add_select2($new_content, $widget);
                $new_content = $this->_add_password_visibility($new_content, $widget);
                $new_content = $this->_add_inline_align($new_content, $widget);
                $new_content = $this->_add_action($new_content, $widget);
                $new_content = $this->_add_wysiwyg($new_content, $widget);
                $new_content = $this->_add_address($new_content, $widget);
                $new_content = $this->_add_length($new_content, $widget);
                $new_content = $this->_add_reset($new_content, $widget);
                $new_content = $this->_add_submit($new_content, $widget);
                $new_content = $this->_add_onchange($new_content, $widget);                
            }
            return $new_content;
        }

        public function _add_inline_align($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();
            $add_css = $add_js = '';
            $has_js = false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield["field_type"] == 'radio' || $afield["field_type"] == 'checkbox') {
                    if (!empty($afield['inline_align'])) {
                        $has_js = true;
                        $add_js .= "jQuery('.elementor-field-group-" . $afield['custom_id'] . "').addClass('elementor-repeater-item-" . $afield['_id'] . "');";
                        $add_css .= ".elementor-field-group-" . $afield['custom_id'] . ".elementor-repeater-item-" . $afield['_id'] . " .elementor-subgroup-inline{width: 100%; justify-content: ".$afield['inline_align'].";}";
                    }
                }
            }
            if ($has_js) {
                $add_js = '<script>jQuery(document).ready(function(){' . $add_js . '});</script>';
                $add_css = '<style>' . $add_css . '</style>';
                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($this->get_name() . '-' . $widget->get_id() . '-inline', $add_js);
                $add_css = \DynamicContentForElementor\DCE_Assets::dce_enqueue_style($this->get_name() . '-' . $widget->get_id() . '-inline', $add_css);
                return $new_content . $add_js . $add_css;
            }
            return $new_content;
        }

        public function _add_range_slider($content, $widget) {
            wp_register_script(
                    'nouislider',
                    ELEMENTOR_ASSETS_URL . 'lib/nouislider/nouislider' . $suffix . '.js',
                    [],
                    '13.0.0',
                    true
            );
        }
        
        public function _add_submit($content, $widget) {
            //elementor-field-type-reset
            $settings = $widget->get_settings_for_display();
            $settings['button_text']; // submit button text
            
            $submits = explode('elementor-field-type-submit', $content);
            if (count($submits) > 2) {
                list($more, $original) = explode('>', end($submits), 2);
                list($original, $more) = explode('</div>', $original, 2);                

                foreach ($submits as $skey => $asubmit) {
                    if ($skey && $skey < count($submits)) {
                        // remove label
                        $pieces = explode('<label', $asubmit, 2);
                        if (count($pieces) == 2) {
                            $more = explode('</label>', end($pieces), 2);
                            $new_content .= 'elementor-field-type-submit' . reset($pieces) . end($more);
                        } else {
                            $new_content .= 'elementor-field-type-submit' . $asubmit;
                        }
                    } else {
                        if ($skey) {
                            $new_content = 'elementor-field-type-submit' . $asubmit;
                        } else {
                            $new_content = $asubmit;
                        }
                    }
                }
                $content = $new_content;
            }
            
            return $content;
        }
        public function _render_submit($instance, $item_index = 0, $form = null) {
            $btn_class = '';
            if ( ! empty( $instance['button_size'] ) ) {
                $btn_class .= ' elementor-size-' . $instance['button_size'];
            }
            if ( ! empty( $instance['button_type'] ) ) {
                $btn_class .= ' elementor-button-' . $instance['button_type'];
            }

            if ( ! empty( $instance['button_hover_animation'] ) ) {
                $btn_class .= ' elementor-animation-' . $instance['button_hover_animation'];
            }

            ?>
            <button type="submit" class="elementor-button<?php echo $btn_class; ?>">
                    <span>
                            <?php if ( ! empty( $instance['field_icon'] ) ) : ?>
                                    <span class="elementor-align-icon-left elementor-button-icon">
                                            <?php Icons_Manager::render_icon( $instance['field_icon'], [ 'aria-hidden' => 'true' ] ); ?>
                                            <?php if ( empty( $instance['field_label'] ) ) : ?>
                                                    <span class="elementor-screen-only"><?php _e( 'Submit', 'elementor-pro' ); ?></span>
                                            <?php endif; ?>
                                    </span>
                            <?php endif; ?>
                            <?php if ( ! empty( $instance['field_label'] ) ) : ?>
                                    <span class="elementor-button-text"><?php echo $instance['field_label']; ?></span>
                            <?php endif; ?>
                    </span>
            </button>
            <?php
            return true;
        }
        
        public function _add_reset($content, $widget) {
            //elementor-field-type-reset
            $settings = $widget->get_settings_for_display();

            $resets = explode('elementor-field-type-reset', $content);
            if (count($resets) > 1) {
                foreach ($resets as $rkey => $areset) {
                    if ($rkey) {
                        // remove label
                        $pieces = explode('<label', $areset, 2);
                        if (count($pieces) == 2) {
                            $more = explode('</label>', end($pieces), 2);
                            $new_content .= 'elementor-field-type-reset' . reset($pieces) . end($more);
                        } else {
                            $new_content .= 'elementor-field-type-reset' . $areset;
                        }
                    } else {
                        $new_content = $areset;
                    }
                }
                $content = $new_content;

                /* if ( $settings['reset_button_hover_animation'] ) {
                  $content = str_replace('elementor-button-reset', 'elementor-button-reset elementor-animaton-' . $settings['reset_button_hover_animation'], $content);
                  } */

                //$content = str_replace('elementor-button-reset', 'elementor-button-reset elementor-size-sm', $content);
            }

            return $content;
        }

        public function _render_reset($item, $item_index = 0, $form = null) {
            ?>
            <input type="reset" class="elementor-button-reset elementor-button elementor-size-<?php echo $item['button_size']; ?>" value="<?php echo $item['field_label']; ?>">
            <?php
            return true;
        }

        public function _add_action($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();

            if (!empty($settings['form_method']) && $settings['form_method'] != 'ajax') {

                foreach ($settings['form_fields'] as $key => $afield) {
                    $new_content = str_replace('form_fields[' . $afield['custom_id'] . ']', $afield['custom_id'], $new_content);
                }

                if ($settings['form_method'] == 'get') {
                    $new_content = str_replace('method="post"', 'method="' . $settings['form_method'] . '"', $new_content);
                }
                if (!empty($settings['form_action']['url'])) {
                    $new_content = str_replace('<form ', '<form action="' . $settings['form_action']['url'] . '" ', $new_content);
                } else {
                    $new_content = str_replace('<form ', '<form action="" ', $new_content); // current page
                }

                if ($settings['form_action']['custom_attributes']) {
                    $attr_str = '';
                    $attrs = DCE_Helper::str_to_array(',', $settings['form_action']['custom_attributes']);
                    if (!empty($attrs)) {
                        foreach ($attrs as $anattr) {
                            list($attr, $value) = explode('|', $anattr, 2);
                            $attr_str .= $attr . '="' . $value . '" ';
                        }
                    }
                    if ($attr_str) {
                        $new_content = str_replace('<form ', '<form ' . $attr_str, $new_content);
                    }
                }

                if (!empty($settings['form_action']['is_external'])) {
                    $new_content = str_replace('<form ', '<form target="_blank" ', $new_content);
                }
                if (!empty($settings['form_action']['nofollow'])) {
                    $new_content = str_replace('<form ', '<form rel="nofollow" ', $new_content);
                }

                $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_action';
                ob_start();
                ?>
                <script id="<?php echo $jkey; ?>">
                    (function ($) {
                <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                            var <?php echo $jkey; ?> = function ($scope, $) {
                                if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php } ?>
                                jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-form').off();
                <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                                }
                            };
                            $(window).on("elementor/frontend/init", function () {
                                elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php echo $jkey; ?>);
                            });
                <?php } ?>
                    })(jQuery, window);
                </script>
                <?php
                $add_js = ob_get_clean();

                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);
                return $new_content . $add_js;
            }
            return $new_content;
        }

        public function _add_length($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield['field_type'] == 'text' || $afield['field_type'] == 'textarea') {
                    if (!empty($afield['field_maxlength'])) {
                        $content = str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" maxlength="' . $afield['field_maxlength'] . '"', $content);
                    }
                    if (!empty($afield['field_minlength'])) {
                        $content = str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" minlength="' . $afield['field_minlength'] . '"', $content);
                    }
                }
            }
            return $content;
        }

        public function _add_wysiwyg($content, $widget) {
            $settings = $widget->get_settings_for_display();
            $has_wysiwyg = false;
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_wysiwyg';
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ob_start();
                ?>
                <script id="<?php echo $jkey; ?>">
                    (function ($) {
                        var <?php echo $jkey; ?> = function ($scope, $) {
                            if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php
                foreach ($settings['form_fields'] as $key => $afield) {
                    if ($afield["field_type"] == 'textarea') {
                        if (!empty($afield['field_wysiwyg'])) {
                            $has_wysiwyg = true;
                            //wp_editor('', 'form-field-'.$afield['custom_id']);
                            ?>
                                            tinymce.init({
                                                //mode : "exact",
                                                selector: '.elementor-element-<?php echo $widget->get_id(); ?> #form-field-<?php echo $afield['custom_id']; ?>',
                                                //theme: "modern",
                                                //skin: "lightgray",
                                                menubar: false,
                                                branding: false,
                                                //statusbar: false,
                                                plugins: "lists, link, paste",
                                                setup: function (editor) {
                                                    editor.on('change', function () {
                                                        console.log('change');
                                                        //console.log(editor.getContent());
                                                        tinymce.triggerSave();
                                                    });
                                                },
                                            });
                            <?php
                        }
                    }
                }
                ?>
                            }
                        };
                        $(window).on("elementor/frontend/init", function () {
                            elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php echo $jkey; ?>);
                        });
                    })(jQuery, window);
                </script>
                <?php
                $add_js = ob_get_clean();
                if ($has_wysiwyg) {
                    $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);

                    wp_enqueue_script('tinymce_js', includes_url('js/tinymce/') . 'wp-tinymce.php', array('jquery'), false, true);

                    return $content . $add_js;
                }
            }
            return $content;
        }

        public function _add_address($content, $widget) {
            //https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-addressform
            $settings = $widget->get_settings_for_display();
            $has_address = false;
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_address';
            if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                ob_start();
                ?>
                <script id="<?php echo $jkey; ?>">
                    var placeSearch, autocomplete;

                    function dce_geolocate() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function (position) {
                                var geolocation = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude
                                };
                                var circle = new google.maps.Circle(
                                        {center: geolocation, radius: position.coords.accuracy});
                                autocomplete.setBounds(circle.getBounds());
                            });
                        }
                    }


                    function dce_init_autocomplete() {
                <?php
                foreach ($settings['form_fields'] as $key => $afield) {
                    if ($afield["field_type"] == 'text') {
                        if (!empty($afield['field_address'])) {
                            $has_address = true;
                            //wp_editor('', 'form-field-'.$afield['custom_id']);
                            ?>
                                    console.log('autocomplete');
                                    autocomplete = new google.maps.places.Autocomplete(document.getElementById('form-field-<?php echo $afield['custom_id']; ?>'), {types: ['geocode']});
                                    autocomplete.setFields(['address_component']);
                            <?php
                            //$content = str_replace('id="form-field-' . $afield['custom_id'] . '"', 'id="form-field-' . $afield['custom_id'] . '" onFocus="dce_geolocate()"', $content);
                        }
                    }
                }
                ?>
                    }
                </script>
                <?php
                $add_js = ob_get_clean();
                if ($has_address) {
                    //$add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);

                    global $wp_scripts;
                    if (!empty($wp_scripts->registered['google-maps-api'])) {
                        //var_dump($wp_scripts->registered['google-maps-api']->src);
                        $wp_scripts->registered['google-maps-api']->src .= '&libraries=places&callback=dce_init_autocomplete';
                    }
                    wp_enqueue_script('google-maps-api');

                    return $content . $add_js;
                }
            }
            return $content;
        }

        public function _add_onchange($content, $widget) {
            $settings = $widget->get_settings_for_display();

            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_onchange';
            ob_start();
            ?>
            <script id="<?php echo $jkey; ?>">
                (function ($) {
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                        var <?php echo $jkey; ?> = function ($scope, $) {
                            if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php
            }
            $has_onchange = false;
            foreach ($settings['form_fields'] as $key => $afield) {
                //if ($afield["field_type"] == 'select') {
                if (!empty($afield['field_onchange'])) {
                    $has_onchange = true;
                    ?>
                    jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-field-group-<?php echo $afield['custom_id']; ?> input, .elementor-element-<?php echo $widget->get_id(); ?> .elementor-field-group-<?php echo $afield['custom_id']; ?> select').on('change', function () {
                        var field = jQuery(this).closest('.elementor-field-group');
                        if (field.siblings('.dce-form-step-bnt-next').length) {
                            // step
                            field.siblings('.dce-form-step-bnt-next').find('button').trigger('click');
                        } else {
                            // submit
                            jQuery(this).closest('form').find('.elementor-field-type-submit button').trigger('click');
                        }
                    });
                    <?php
                }
            }
            ?>
                            jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .select2-selection__arrow').remove();
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                            }
                        };
                        $(window).on("elementor/frontend/init", function () {
                            elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php echo $jkey; ?>);
                        });
            <?php } ?>
                })(jQuery, window);
            </script>
            <?php
            $add_js = ob_get_clean();
            if ($has_onchange) {

                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);

                return $content . $add_js;
            }
            return $content;
        }

        public function _add_select2($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();

            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_select2';
            ob_start();
            ?>
            <script id="<?php echo $jkey; ?>">
                (function ($) {
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                        var <?php echo $jkey; ?> = function ($scope, $) {
                            if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php
            }
            $has_select2 = false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield["field_type"] == 'select') {
                    if (!empty($afield['field_select2'])) {
                        $has_select2 = true;
                        ?>
                                        if (jQuery.fn.select2) {
                                            var field2 = jQuery('.elementor-element-<?php echo $widget->get_id(); ?> #form-field-<?php echo $afield['custom_id']; ?>');
                                            var classes = field2.attr('class');
                                            var $select2 = field2.select2({
                                                //containerCssClass: classes,
                        <?php if (!empty($afield['field_select2_placeholder'])) { ?>placeholder: '<?php echo $afield['field_select2_placeholder']; ?>',<?php } ?>
                                                        });
                                                        //console.log(classes);
                                                        $select2.data('select2').$container.find('.select2-selection').addClass(classes);
                                                    }
                        <?php
                    }
                }
            }
            ?>
                                        jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .select2-selection__arrow').remove();
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                                        }
                                    };
                                    $(window).on("elementor/frontend/init", function () {
                                        elementorFrontend.hooks.addAction("frontend/element_ready/form.default", <?php echo $jkey; ?>);
                                    });
            <?php } ?>
                            })(jQuery, window);
            </script>
            <?php
            $add_js = ob_get_clean();
            if ($has_select2) {

                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);
                wp_enqueue_script('jquery-elementor-select2');
                wp_enqueue_style('elementor-select2');

                return $new_content . $add_js;
            }
            return $new_content;
        }

        public function _add_password_visibility($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_psw';
            ob_start();
            ?>
            <script id="<?php echo $jkey; ?>">
                (function ($) {
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                        var <?php echo $jkey; ?> = function ($scope, $) {
                            if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php
            }
            $has_psw = false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if ($afield["field_type"] == 'password') {
                    if (!empty($afield['field_psw_visiblity'])) {
                        $has_psw = true;
                        ?>
                                        jQuery('.elementor-element-<?php echo $widget->get_id(); ?> #form-field-<?php echo $afield['custom_id']; ?>').addClass('dce-form-password-toggle');
                        <?php
                    }
                }
            }
            if ($has_psw) {
                wp_enqueue_style('font-awesome');
                ?>
                                jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .dce-form-password-toggle').each(function () {
                                    jQuery(this).wrap('<div class="elementor-field-input-wrapper elementor-field-input-wrapper-<?php echo $afield['custom_id']; ?>"></div>');
                                    jQuery(this).parent().append('<span class="fa far fa-eye-slash field-icon dce-toggle-password"></span>');
                                    jQuery(this).next('.dce-toggle-password').on('click', function () {
                                        var input_psw = jQuery(this).prev();
                                        if (input_psw.attr('type') == 'password') {
                                            input_psw.attr('type', 'text');
                                        } else {
                                            input_psw.attr('type', 'password');
                                        }
                                        jQuery(this).toggleClass('fa-eye').toggleClass('fa-eye-slash');
                                    });
                                });
                <?php
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
            $add_js = ob_get_clean();
            if ($has_psw) {
                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);
                return $new_content . $add_js;
            }
            return $new_content;
        }

        public function _add_icon($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();

// Using the reader to dynamically get the icons array. It's resource intensive and you must cache the result.
            $css_path = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/css/fontawesome.css';
            $icons_fa = new \Awps\FontAwesomeReader($css_path);
            /*
              $css_path = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/css/regular.css';
              $icons_far    = new \Awps\FontAwesomeReader( $css_path );
              $css_path = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/css/solid.css';
              $icons_fas    = new \Awps\FontAwesomeReader( $css_path );
              $css_path = ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/css/brands.css';
              $icons_fab    = new \Awps\FontAwesomeReader( $css_path );
             */
// .... or better use the static class
//$icons = new \Awps\FontAwesome();

            $add_css = '<style>';
            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_icon';
            ob_start();
            ?>
            <script id="<?php echo $jkey; ?>">
                (function ($) {
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                        var <?php echo $jkey; ?> = function ($scope, $) {
                            if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php
            }
            $has_icon = false;
            foreach ($settings['form_fields'] as $key => $afield) {
//if ($afield["field_type"] == 'select') {
                if (!empty($afield['field_icon'])) {
                    wp_enqueue_style('fontawesome');
//var_dump($afield['field_icon']);
                    $fa_classes = explode(' ', $afield['field_icon']['value']);
                    $fa_family = reset($fa_classes);
                    $fa_class = end($fa_classes);
                    $fa_family_name = 'Font Awesome 5 Free';
                    $fa_weight = 400;
                    $fa_unicode = $icons_fa->getIconUnicode($fa_class);
                    switch ($fa_family) {
                        case 'far':
//$fa_unicode = $icons_far->getIconUnicode($fa_class);
                            break;
                        case 'fas':
                            $fa_weight = 900;
//$fa_unicode = $icons_fas->getIconUnicode($fa_class);
                            break;
                        case 'fab':
                            $fa_family_name = "Font Awesome 5 Brands";
//$fa_unicode = $icons_fab->getIconUnicode($fa_class);
                            break;
                        default:
                            $fa_unicode = $icons_fa->getIconUnicode($fa_class);
                    }
                    $has_icon = true;
                    if ($afield['field_icon_position'] == 'elementor-field-label') {
                        $add_css .= ".elementor-element-" . $widget->get_id() . " .elementor-field-group-" . $afield['custom_id'] . " .elementor-field-label:before { content: '" . $fa_unicode . "'; font-family: FontAwesome, \"" . $fa_family_name . "\"; font-weight: " . $fa_weight . "; margin-right: 5px; }";
                    }
                    if ($afield['field_icon_position'] == 'elementor-field') {
                        echo "jQuery('.elementor-element-" . $widget->get_id() . " #form-field-" . $afield['custom_id'] . "').wrap('<div class=\"elementor-field-input-wrapper elementor-field-input-wrapper-" . $afield['custom_id'] . "\"></div>');";
                        switch ($afield['field_type']) {
                            case 'textarea':
                                $add_css .= ".elementor-element-" . $widget->get_id() . " .elementor-field-input-wrapper-" . $afield['custom_id'] . ":after { content: '" . $fa_unicode . "'; font-family: FontAwesome, \"" . $fa_family_name . "\"; font-weight: " . $fa_weight . "; position: absolute; top: 5px; left: 16px; }";
                                break;
                            default:
                                $add_css .= ".elementor-element-" . $widget->get_id() . " .elementor-field-input-wrapper-" . $afield['custom_id'] . ":after { content: '" . $fa_unicode . "'; font-family: FontAwesome, \"" . $fa_family_name . "\"; font-weight: " . $fa_weight . "; position: absolute; top: 50%; transform: translateY(-50%); left: 16px; }";
                        }
                        $add_css .= ".elementor-element-" . $widget->get_id() . " #form-field-" . $afield['custom_id'] . ", .elementor-element-" . $widget->get_id() . " .elementor-field-group-" . $afield['custom_id'] . " .elementor-field-textual { padding-left: 42px; }";
                    }
                }
//}
            }
            $add_css .= '</style>';
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
            $add_js = ob_get_clean();
            if ($has_icon) {
                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);
                return $new_content . $add_css . $add_js;
            }
            return $new_content;
        }

        public function _add_description($content, $widget) {
            $new_content = $content;
            $settings = $widget->get_settings_for_display();
            $add_css = '<style>.elementor-element.elementor-element-' . $widget->get_id() . ' .elementor-field-group { align-self: flex-start; }</style>';

            $jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_description';
            ob_start();
            ?>
            <script id="<?php echo $jkey; ?>">
                (function ($) {
            <?php if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                        var <?php echo $jkey; ?> = function ($scope, $) {
                            if ($scope.hasClass("elementor-element-<?php echo $widget->get_id(); ?>")) {
                <?php
            }
            $has_description = false;
            foreach ($settings['form_fields'] as $key => $afield) {
                if (!empty($afield['field_description']) && $afield['field_description_position'] != 'no-description') {
                    $has_description = true;
                    $field_description = str_replace("'", "\\'", $afield['field_description']);
                    $field_description = preg_replace('/\s+/', ' ', trim($field_description));
                    if ($afield['field_description_position'] == 'elementor-field-label') {
                        if ($afield['field_description_tooltip']) {
                            ?>
                                            jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-field-group-<?php echo $afield['custom_id']; ?> .elementor-field-label').addClass('dce-tooltip').addClass('elementor-field-label-description');
                                            jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-field-group-<?php echo $afield['custom_id']; ?> .elementor-field-label').append('<span class="dce-tooltiptext dce-tooltip-<?php echo $afield['field_description_tooltip_position']; ?>"><?php echo $field_description; ?></span>');
                        <?php } else { ?>
                                            jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-field-group-<?php echo $afield['custom_id']; ?> .elementor-field-label').wrap('<abbr class=\"elementor-field-label-description elementor-field-label-description-<?php echo $afield['custom_id']; ?>" title="<?php echo $field_description; ?>"></abbr>');
                            <?php
                        }
                    }
                    if ($afield['field_description_position'] == 'elementor-field') {
                        ?>
                                        jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-field-group-<?php echo $afield['custom_id']; ?>').append('<div class="elementor-field-input-description elementor-field-input-description-<?php echo $afield['custom_id']; ?>"><?php echo $field_description; ?></div>');
                        <?php
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
            $add_js = ob_get_clean();
            if ($has_description) {
                $add_js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($jkey, $add_js);
                return $new_content . $add_css . $add_js;
            }
            return $new_content;
        }

        public function _add_date_i18n() {
            ?>
            <script>
                jQuery(document).ready(function () {
                    const fp = document.querySelector("#myInput")._flatpickr;
                    const Russian = require("flatpickr/dist/l10n/ru.js").default.ru;
                    //set translations
                    fp.localize(Russian);
                    /*flatpickr(".flatpickr-input"                 , {
                     //dateFormat: 'Y-m-                 d',
                     //altFormat: 'd/m/                 Y',
                     "locale": Russian // locale for this instance o                 nly
                     });            */
                });
            </script>
            <?php
        }

        public static function _add_to_form(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form' && $control_id == 'form_fields') {
                $control_data['fields']['form_fields_enchanted_tab'] = array(
                    "type" => "tab",
                    "tab" => "enchanted",
                    "label" => '<i class="fa fa-magic" aria-hidden="true"></i>', //__('Enchanted', 'dynamic-content-for-elementor'),
                    "tabs_wrapper" => "form_fields_tabs",
                    "name" => "form_fields_enchanted_tab",
                    'condition' => [
                        'field_type!' => 'step',
                    ],
                );
            }

            $control_data = self::_add_form_length($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_select2($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_wysiwyg($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_address($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_password_visibility($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_inline_align($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_onchange($element, $control_id, $control_data, $options);
            /*             * ********* */
            $control_data = self::_add_form_icon($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_description($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_btn_style($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_width($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_reset($element, $control_id, $control_data, $options);
            $control_data = self::_add_form_submit($element, $control_id, $control_data, $options);

            $control_data = self::_add_form_action($element, $control_id, $control_data, $options);

            return $control_data;
        }

        public static function _add_form_width(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    if (isset($control_data['fields']['width'])) {
                        $control_data['fields']['width']['options'][90] = '90%';
                        $control_data['fields']['width']['options'][83] = '83%';
                        $control_data['fields']['width']['options'][70] = '70%';
                        $control_data['fields']['width']['options'][30] = '30%';
                        $control_data['fields']['width']['options'][16] = '16%';
                        $control_data['fields']['width']['options'][14] = '14%';
                        $control_data['fields']['width']['options'][12] = '12%';
                        $control_data['fields']['width']['options'][11] = '11%';
                        $control_data['fields']['width']['options'][10] = '10%';
//$control_data['fields']['width']['options'][5] = '5%';
                        ksort($control_data['fields']['width']['options']);
//echo '<pre>';var_dump($control_data['fields']['width']['options']);echo '</pre>';
                    }
                }
            }

            return $control_data;
        }

        public static function _add_form_select2(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {

                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_select2'] = array(
                        'name' => 'field_select2',
                        'label' => __('Enable Select2', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'return_value' => 'true',
                        //'default' => 'true',
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'select',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                    $control_data['fields']['field_select2_placeholder'] = array(
                        'name' => 'field_select2_placeholder',
                        'label' => __('Placeholder', 'elementor'),
                        'type' => Controls_Manager::TEXT,
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'select',
                                ],
                                [
                                    'name' => 'field_select2',
                                    'value' => 'true',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }

// apply same style
                if ($control_id == 'field_text_color') {
                    $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = 'color: {{VALUE}};';
                    $control_data['selectors']['{{WRAPPER}} ..select2-container--default .select2-selection--multiple .select2-selection__rendered'] = 'color: {{VALUE}};';
                }
                if (strpos($control_id, 'field_typography') === 0) {
                    //var_dump($control_id); var_dump($control_data); //die();
                    if (!empty($control_data['selectors'])) {
                        $values = reset($control_data['selectors']);
                        $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = $values;
                        $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single .select2-selection__rendered'] = $values;
                        $control_data['selectors']['{{WRAPPER}} .select2-container--default .select2-selection--single, {{WRAPPER}} .select2-container--default .select2-selection--multiple'] = 'height: auto;';
                    }
                }
                if ($control_id == 'field_background_color') {
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'background-color: {{VALUE}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'background-color: {{VALUE}};';
                    $control_data['selectors']['{{WRAPPER}} .mce-panel'] = 'background-color: {{VALUE}};';
                }
                if ($control_id == 'field_border_color') {
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-color: {{VALUE}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-color: {{VALUE}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-color: {{VALUE}};';
                }
                if ($control_id == 'field_border_width') {
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                }
                if ($control_id == 'field_border_radius') {
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                    $control_data['selectors']['{{WRAPPER}} .elementor-field-group .mce-panel'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                }
            }


            return $control_data;
        }

        public static function _add_form_length(Controls_Stack $element, $control_id, $control_data, $options = []) {
            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_maxlength'] = array(
                        'name' => 'field_maxlength',
                        'label' => __('Max Length', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::NUMBER,
                        'min' => 0,
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'operator' => 'in',
                                    'value' => ['text', 'textarea'],
                                ],
                            ],
                        ],
                        'description' => __('Max character length', 'dynamic-content-for-elementor'),
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                    $control_data['fields']['field_minlength'] = array(
                        'name' => 'field_minlength',
                        'label' => __('Min Length', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::NUMBER,
                        'min' => 0,
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'operator' => 'in',
                                    'value' => ['text', 'textarea'],
                                ],
                            ],
                        ],
                        'description' => __('Min character length', 'dynamic-content-for-elementor'),
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
            }
            return $control_data;
        }

        public static function _add_form_address(Controls_Stack $element, $control_id, $control_data, $options = []) {
            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_address'] = array(
                        'name' => 'field_address',
                        'label' => __('Enable Address Autocomplete', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'return_value' => 'true',
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'text',
                                ],
                            ],
                        ],
                        'description' => __('Required Google Maps API', 'dynamic-content-for-elementor'),
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
            }
            return $control_data;
        }

        public static function _add_form_wysiwyg(Controls_Stack $element, $control_id, $control_data, $options = []) {
            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_wysiwyg'] = array(
                        'name' => 'field_wysiwyg',
                        'label' => __('Enable WYSIWYG', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'return_value' => 'true',
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'textarea',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
            }
            return $control_data;
        }

        public static function _add_form_onchange(Controls_Stack $element, $control_id, $control_data, $options = []) {
            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_onchange'] = array(
                        'name' => 'field_onchange',
                        'label' => __('Submit on Change', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'condition' => [
                            'field_type' => ['radio', 'select'], //'text', 'textarea', 'acceptance'],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
            }
            return $control_data;
        }

        public static function _add_form_btn_style(Controls_Stack $element, $control_id, $control_data, $options = []) {
            if ($element->get_name() == 'form') {
                if ($control_id == 'button_text_padding') {
                    $element->add_control(
                            'button_margin',
                            [
                                'label' => __('Margin', 'elementor'),
                                'type' => Controls_Manager::DIMENSIONS,
                                'size_units' => ['px', 'em', '%'],
                                'selectors' => [
                                    '{{WRAPPER}} .elementor-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                                ],
                            ]
                    );
                }
            }
            return $control_data;
        }

        public static function _add_form_password_visibility(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {

                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_psw_visiblity'] = array(
                        'name' => 'field_psw_visiblity',
                        'label' => __('Enable Password Visible', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'return_value' => 'true',
                        'default' => 'true',
                        'conditions' => [
                            'terms' => [
                                [
                                    'name' => 'field_type',
                                    'value' => 'password',
                                ],
                            ],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
                /*
                  // apply same style
                  if ($control_id == 'field_background_color') {
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'background-color: {{VALUE}};';
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'background-color: {{VALUE}};';
                  }
                  if ($control_id == 'field_border_color') {
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-color: {{VALUE}};';
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-color: {{VALUE}};';
                  }
                  if ($control_id == 'field_border_width') {
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                  }
                  if ($control_id == 'field_border_radius') {
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                  $control_data['selectors']['{{WRAPPER}} .elementor-field-group .elementor-select-wrapper .select2 .elementor-field-textual'] = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
                  }
                 */
            }


            return $control_data;
        }

        public static function _add_form_icon(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {

                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_icon_position'] = array(
                        'name' => 'field_icon_position',
                        'label' => __('Icon', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'no-icon' => [
                                'title' => __('No Icon', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-times',
                            ],
                            'elementor-field-label' => [
                                'title' => __('On Label', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-tag',
                            ],
                            'elementor-field' => [
                                'title' => __('On Input', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-square-o',
                            ]
                        ],
                        'toggle' => false,
                        'default' => 'no-icon',
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                    $control_data['fields']['field_icon'] = array(
                        'name' => 'field_icon',
                        'label' => __('Select Icon', 'elementor'),
                        'type' => Controls_Manager::ICONS,
                        'label_block' => true,
                        'fa4compatibility' => 'icon',
                        'condition' => [
                            'field_icon_position!' => 'no-icon',
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }

                if ($control_id == 'field_background_color') {
                    $element->add_control(
                            'field_icon_color',
                            [
                                'label' => __('Icon Color', 'elementor-pro'),
                                'type' => Controls_Manager::COLOR,
                                'selectors' => [
                                    '{{WRAPPER}} .elementor-field-input-wrapper:after' => 'color: {{VALUE}};',
                                ],
                                'separator' => 'before',
                            ]
                    );
                }
                if ($control_id == 'mark_required_color') {
                    $element->add_control(
                            'label_icon_color',
                            [
                                'label' => __('Icon Color', 'elementor-pro'),
                                'type' => Controls_Manager::COLOR,
                                'selectors' => [
                                    '{{WRAPPER}} .elementor-field-label:before' => 'color: {{VALUE}};',
                                ],
                            ]
                    );
                }
            }

            return $control_data;
        }

        public static function _add_form_description(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {

                if ($control_id == 'form_fields') {
                    $control_data['fields']['field_description_position'] = array(
                        'name' => 'field_description_position',
                        'label' => __('Description', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'no-description' => [
                                'title' => __('No Description', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-times',
                            ],
                            'elementor-field-label' => [
                                'title' => __('On Label', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-tag',
                            ],
                            'elementor-field' => [
                                'title' => __('Below Input', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-square-o',
                            ]
                        ],
                        'toggle' => false,
                        'default' => 'no-description',
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                    $control_data['fields']['field_description_tooltip'] = array(
                        'name' => 'field_description_tooltip',
                        'label' => __('Display as Tooltip', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'condition' => [
                            'field_description_position' => 'elementor-field-label',
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                    $control_data['fields']['field_description_tooltip_position'] = array(
                        'name' => 'field_description_tooltip_position',
                        'label' => __('Tooltip Position', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'top' => [
                                'title' => __('Top', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-angle-up',
                            ],
                            'left' => [
                                'title' => __('Left', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-angle-left',
                            ],
                            'bottom' => [
                                'title' => __('Bottom', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-angle-down',
                            ],
                            'right' => [
                                'title' => __('Right', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-angle-right',
                            ],
                        ],
                        'toggle' => false,
                        'default' => 'top',
                        'condition' => [
                            'field_description_position' => 'elementor-field-label',
                            'field_description_tooltip!' => '',
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                    $control_data['fields']['field_description'] = array(
                        'name' => 'field_description',
                        'label' => __('Description HTML', 'elementor'),
                        'type' => Controls_Manager::TEXTAREA,
                        'label_block' => true,
                        'fa4compatibility' => 'icon',
                        'condition' => [
                            'field_description_position!' => 'no-description',
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }

                if ($control_id == 'field_background_color') {
                    $element->add_control(
                            'field_description_color',
                            [
                                'label' => __('Description Color', 'elementor-pro'),
                                'type' => Controls_Manager::COLOR,
                                'selectors' => [
                                    '{{WRAPPER}} .elementor-field-input-description' => 'color: {{VALUE}};',
                                ],
                                'separator' => 'before',
                            ]
                    );
                    $element->add_group_control(
                            Group_Control_Typography::get_type(), [
                        'name' => 'field_description_typography',
                        'label' => __('Typography', 'dynamic-content-for-elementor'),
                        'selector' => '{{WRAPPER}} .elementor-field-input-description',
                            ]
                    );
                    /* }
                      if ($control_id == 'mark_required_color') { */
                    $element->add_control(
                            'label_description_color',
                            [
                                'label' => __('Label Description Color', 'elementor-pro'),
                                'type' => Controls_Manager::COLOR,
                                'selectors' => [
                                    '{{WRAPPER}} .elementor-field-label-description .elementor-field-label' => "display: inline-block;",
                                    '{{WRAPPER}} .elementor-field-label-description:after' => "
                                            content: '?';
                                            display: inline-block;
                                            border-radius: 50%;
                                            padding: 2px 0;
                                            height: 1.2em;
                                            line-height: 1;
                                            font-size: 80%;
                                            width: 1.2em;
                                            text-align: center;
                                            margin-left: 0.2em;
                                            color: {{VALUE}};",
                                ],
                                'separator' => 'before',
                                'default' => '#ffffff',
                            ]
                    );
                    $element->add_control(
                            'label_description_bgcolor',
                            [
                                'label' => __('Label Description Background Color', 'elementor-pro'),
                                'type' => Controls_Manager::COLOR,
                                'selectors' => [
                                    '{{WRAPPER}} .elementor-field-label-description:after' => 'background-color: {{VALUE}};',
                                ],
                                'default' => '#666666',
                            ]
                    );
                }
                if ($control_id == 'label_spacing') {                    
                    $control_data['selectors']['body.rtl {{WRAPPER}} .elementor-labels-inline .elementor-field-group > abbr'] = 'padding-left: {{SIZE}}{{UNIT}};'; // for the label position = inline option
                    $control_data['selectors']['body:not(.rtl) {{WRAPPER}} .elementor-labels-inline .elementor-field-group > abbr'] = 'padding-right: {{SIZE}}{{UNIT}};'; // for the label position = inline option
                    $control_data['selectors']['body {{WRAPPER}} .elementor-labels-above .elementor-field-group > abbr'] = 'padding-bottom: {{SIZE}}{{UNIT}};'; // for the label position = above option
                }
            }

            return $control_data;
        }

        public static function _add_form_inline_align(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {

                if ($control_id == 'form_fields') {
                    $control_data['fields']['inline_align'] = array(
                        'name' => 'inline_align',
                        'label' => __('Inline align', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'flex-start' => [
                                'title' => __('Left', 'elementor-pro'),
                                'icon' => 'eicon-text-align-left',
                            ],
                            'center' => [
                                'title' => __('Center', 'elementor-pro'),
                                'icon' => 'eicon-text-align-center',
                            ],
                            'flex-end' => [
                                'title' => __('Right', 'elementor-pro'),
                                'icon' => 'eicon-text-align-right',
                            ],
                            'space-around' => [
                                'title' => __('Around', 'elementor-pro'),
                                'icon' => 'eicon-text-align-justify',
                            ],
                            'space-evenly' => [
                                'title' => __('Evenly', 'elementor-pro'),
                                'icon' => 'eicon-text-align-justify',
                            ],
                            'space-between' => [
                                'title' => __('Between', 'elementor-pro'),
                                'icon' => 'eicon-text-align-justify',
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} {{CURRENT_ITEM}} .elementor-subgroup-inline' => 'width: 100%; justify-content: {{VALUE}};',
                        //'{{WRAPPER}} .elementor-subgroup-inline' => 'justify-content: {{VALUE}};',
                        ],
                        'render_type' => 'ui',
                        'condition' => [
                            'field_type' => ['checkbox', 'radio'],
                            'inline_list!' => '',
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
            }


            return $control_data;
        }

        public static function _add_form_action(Controls_Stack $element, $control_id, $control_data, $options = []) {

            if ($element->get_name() == 'form') {
                if ($control_id == 'form_id') {
                    $element->add_control(
                            'form_method',
                            [
                                'label' => __('Method', 'elementor-pro'),
                                'type' => Controls_Manager::CHOOSE,
                                'options' => [
                                    'ajax' => [
                                        'title' => __('AJAX (Default)', 'dynamic-content-for-elementor'),
                                        'icon' => 'fa fa-retweet',
                                    ],
                                    'post' => [
                                        'title' => __('POST', 'dynamic-content-for-elementor'),
                                        'icon' => 'fa fa-cog',
                                    ],
                                    'get' => [
                                        'title' => __('GET', 'dynamic-content-for-elementor'),
                                        'icon' => 'fa fa-link',
                                    ]
                                ],
                                'description' => __('WARNING: all standard Ajax Actions will not works', 'dynamic-content-for-elementor'),
                                'toggle' => false,
                                'default' => 'ajax',
                            ]
                    );
                    $element->add_control(
                            'form_action',
                            [
                                'label' => __('Action', 'elementor-pro'),
                                'type' => Controls_Manager::URL,
                                'condition' => [
                                    'form_method!' => 'ajax',
                                ]
                            ]
                    );
                    $element->add_control(
                            'form_action_hide',
                            [
                                'type' => Controls_Manager::RAW_HTML,
                                'label' => __('WARNING', 'dynamic-content-for-elementor'),
                                'raw' => __('All configured Ajax "Actions After Submit" here above will not works!', 'dynamic-content-for-elementor'), //__('<style>.elementor-control.elementor-control-type-section.elementor-control-section_integration, .elementor-control.elementor-control-type-section.elementor-control-section_form_options{display: none;}</style>', 'dynamic-content-for-elementor'),
                                'condition' => [
                                    'form_method!' => 'ajax',
                                ]
                            ]
                    );

                    $control_data['separator'] = 'before';
                }
            }

            return $control_data;
        }

        public static function _add_form_reset(Controls_Stack $element, $control_id, $control_data, $options = []) {
            //echo 'adsa: '; var_dump($control_id); //die();
            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    $control_data["fields"]["field_type"]["options"]['reset'] = __('Reset', 'dynamic-content-for-elementor');
                }
            }
            return $control_data;
        }
        public static function _add_form_submit(Controls_Stack $element, $control_id, $control_data, $options = []) {
            //echo 'adsa: '; var_dump($control_id); //die();
            if ($element->get_name() == 'form') {
                if ($control_id == 'form_fields') {
                    $control_data["fields"]["field_type"]["options"]['submit'] = __('Submit', 'dynamic-content-for-elementor');
                    
                    $control_data['fields']['button_size'] = array(
                        'name' => 'button_size',
                        'label' => __('Size', 'elementor'),
                        'type' => Controls_Manager::SELECT,
                        'default' => 'sm',
                        'options' => DCE_Helper::get_button_sizes(),
                        'condition' => [
                            'field_type' => ['submit', 'reset'],
                        ],
                        "tabs_wrapper" => "form_fields_tabs",
                        "inner_tab" => "form_fields_enchanted_tab",
                        "tab" => "enchanted",
                    );
                }
                
                /*if ($control_id == 'button_css_id') {
                    $element->add_control(
                    'form_submit_hide', [
                        'label' => __('Hide Submit Button', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::CHOOSE,
                        'options' => [
                            'flex' => [
                                'title' => __('SHOW', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-eye',
                            ],
                            'none' => [
                                'title' => __('HIDE', 'dynamic-content-for-elementor'),
                                'icon' => 'fa fa-eye-slash',
                            ],
                        ],
                        'separator' => 'before',
                        'default' => 'flex',
                        'selectors' => [
                            '{{WRAPPER}} form > .elementor-form-fields-wrapper > .elementor-field-type-submit:last-child' => 'display: {{VALUE}};',
                            '{{WRAPPER}} form > .elementor-form-fields-wrapper > .dce-form-step:last-child > .elementor-field-type-submit:last-child' => 'display: {{VALUE}};',
                        ],
                            ]
                    );
                }*/
            }
            return $control_data;
        }

        public function _add_form_reset_style($element, $args = array()) {
            //var_dump($element->get_type());
            if ($element->get_name() == 'form') {

                $element->start_controls_section(
                        'section_reset_button_style',
                        [
                            'label' => __('Reset Button', 'elementor-pro'),
                            'tab' => Controls_Manager::TAB_STYLE,
                        ]
                );

                $element->start_controls_tabs('tabs_reset_button_style');

                $element->start_controls_tab(
                        'tab_reset_button_normal',
                        [
                            'label' => __('Normal', 'elementor-pro'),
                        ]
                );

                $element->add_control(
                        'reset_button_background_color',
                        [
                            'label' => __('Background Color', 'elementor-pro'),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset' => 'background-color: {{VALUE}};',
                            ],
                        ]
                );

                $element->add_control(
                        'reset_button_text_color',
                        [
                            'label' => __('Text Color', 'elementor-pro'),
                            'type' => Controls_Manager::COLOR,
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset' => 'color: {{VALUE}};',
                                '{{WRAPPER}} .elementor-button.elementor-button-reset svg' => 'fill: {{VALUE}};',
                            ],
                        ]
                );

                $element->add_group_control(
                        Group_Control_Typography::get_type(),
                        [
                            'name' => 'reset_button_typography',
                            'selector' => '{{WRAPPER}} .elementor-button.elementor-button-reset',
                        ]
                );

                $element->add_group_control(
                        Group_Control_Border::get_type(), [
                    'name' => 'reset_button_border',
                    'selector' => '{{WRAPPER}} .elementor-button.elementor-button-reset',
                        ]
                );

                $element->add_control(
                        'reset_button_border_radius',
                        [
                            'label' => __('Border Radius', 'elementor-pro'),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => ['px', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                );

                $element->add_control(
                        'reset_button_text_padding',
                        [
                            'label' => __('Text Padding', 'elementor-pro'),
                            'type' => Controls_Manager::DIMENSIONS,
                            'size_units' => ['px', 'em', '%'],
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                            ],
                        ]
                );

                $element->end_controls_tab();
                $element->start_controls_tab(
                        'tab_reset_button_hover',
                        [
                            'label' => __('Hover', 'elementor-pro'),
                        ]
                );

                $element->add_control(
                        'reset_button_background_hover_color',
                        [
                            'label' => __('Background Color', 'elementor-pro'),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'background-color: {{VALUE}};',
                            ],
                        ]
                );

                $element->add_control(
                        'reset_button_hover_color',
                        [
                            'label' => __('Text Color', 'elementor-pro'),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'color: {{VALUE}};',
                            ],
                        ]
                );

                $element->add_control(
                        'reset_button_hover_border_color',
                        [
                            'label' => __('Border Color', 'elementor-pro'),
                            'type' => Controls_Manager::COLOR,
                            'selectors' => [
                                '{{WRAPPER}} .elementor-button.elementor-button-reset:hover' => 'border-color: {{VALUE}};',
                            ],
                            'condition' => [
                                'reset_button_border_border!' => '',
                            ],
                        ]
                );

                /* $element->add_control(
                  'reset_button_hover_animation',
                  [
                  'label' => __( 'Animation', 'elementor-pro' ),
                  'type' => Controls_Manager::HOVER_ANIMATION,
                  ]
                  ); */

                $element->end_controls_tab();

                $element->end_controls_tabs();

                $element->end_controls_section();
            }
        }

    }

}