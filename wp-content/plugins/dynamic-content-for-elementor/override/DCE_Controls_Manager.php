<?php

namespace Elementor;

use \Elementor\Controls_Manager;

/**
 * Description of DCE_Controls_Manager
 *
 * @author fra
 */
class DCE_Controls_Manager extends Controls_Manager {
    
    public static $dce_token_types = [
        Controls_Manager::TEXT, 
        
        Controls_Manager::TEXTAREA,
        Controls_Manager::WYSIWYG,
        
        //Controls_Manager::CODE, 
        Controls_Manager::NUMBER,
        Controls_Manager::URL, 
        Controls_Manager::COLOR, 
        Controls_Manager::SLIDER, 
        
        Controls_Manager::MEDIA,
        Controls_Manager::GALLERY, 
        
        //'form-fields-repeater',
        
        /*
        Controls_Manager::SELECT, 
        Controls_Manager::SELECT2,
        Controls_Manager::SWITCHER,                 
        Controls_Manager::DIMENSIONS,
        Controls_Manager::CHOOSE,
        //Controls_Manager::FONT,
        Controls_Manager::IMAGE_DIMENSIONS,
        Controls_Manager::ANIMATION,
        Controls_Manager::DATE_TIME,
        Controls_Manager::ICON,
        Controls_Manager::ICONS,
        Controls_Manager::REPEATER,       
        */
        
    ];
    
    /* public function __construct($controls_manager) {
      $this->_clone_controls_manager($controls_manager);
      } */

    // get init data from original control_manager
    public function _clone_controls_manager($controls_manager) {
        
        $controls = $controls_manager->get_controls();
        foreach ($controls as $key => $value) {
            $this->controls[$key] = $value;
        }

        $control_groups = $controls_manager->get_control_groups();
        foreach ($control_groups as $key => $value) {
            $this->control_groups[$key] = $value;
        }
        //$this->control_groups = $controls_manager->get_control_groups();
        //var_dump($this->control_groups); die();

        $this->stacks = $controls_manager->get_stacks();
        $this->tabs = $controls_manager::get_tabs();
    }

    public $excluded_extensions = array();

    public function set_excluded_extensions($extensions) {
        $this->excluded_extensions = $extensions;
    }

    /**
     * Add control to stack.
     *
     * This method adds a new control to the stack.
     *
     * @since 1.0.0
     * @access public
     *
     * @param Controls_Stack $element      Element stack.
     * @param string         $control_id   Control ID.
     * @param array          $control_data Control data.
     * @param array          $options      Optional. Control additional options.
     *                                     Default is an empty array.
     *
     * @return bool True if control added, False otherwise.
     */
    public function add_control_to_stack(Controls_Stack $element, $control_id, $control_data, $options = []) {

        if ($element->get_name() == 'form') {
            if (\DynamicContentForElementor\DCE_Helper::is_plugin_active('elementor-pro')) {
                $form_extensions = \DynamicContentForElementor\DCE_Extensions::get_form_extensions();
                foreach($form_extensions as $akey => $a_form_ext) {
                    $exc_ext = !isset($this->excluded_extensions[$a_form_ext]);
                    $a_form_ext_class = \DynamicContentForElementor\DCE_Extensions::$namespace.$a_form_ext;
                    if (method_exists($a_form_ext_class, '_add_to_form')) {
                        $control_data = $a_form_ext_class::_add_to_form($element, $control_id, $control_data, $options);
                    }
                }
            }
        }
        
        if ($element->get_name() == 'video') {
            $extensions = \DynamicContentForElementor\DCE_Extensions::get_extensions();
            foreach($extensions as $akey => $a_ext) {
                $exc_ext = !isset($this->excluded_extensions[$a_ext]);
                $a_ext_class = \DynamicContentForElementor\DCE_Extensions::$namespace.$a_ext;
                if (method_exists($a_ext_class, '_add_to_video')) {
                    $control_data = $a_ext_class::_add_to_video($element, $control_id, $control_data, $options);
                }
            }
        }
                
        // avoid EPRO Popup condition issue
        if (!in_array($element->get_name(), array('popup_triggers', 'popup_timing'))) { 
            //$exc_ext_token = !isset($this->excluded_extensions['DCE_Extension_Token']);
            //if ($exc_ext_token) {
                //add Dynamic Tags to $control_data
                $control_data = self::_add_dynamic_tags($control_data);
            //}
        }

        
        return parent::add_control_to_stack($element, $control_id, $control_data, $options);
    }
    
    public static function _add_dynamic_tags($control_data) {
        if (!empty($control_data)) {
            foreach ($control_data as $key => $acontrol) {
                if ($key != 'dynamic') {
                    if (is_array($acontrol)) {
                        $control_data[$key] = self::_add_dynamic_tags($acontrol);
                    }         
                }
            }
        }
        if (isset($control_data['type']) && !is_array($control_data['type'])) {
            $control_obj = \Elementor\Plugin::$instance->controls_manager->get_control( $control_data['type'] );
            if ($control_obj) {
                $dynamic_settings = $control_obj->get_settings( 'dynamic' );
                if (!empty($dynamic_settings)) {
                    if (in_array($control_data['type'], self::$dce_token_types) ) {
                        if (!isset($control_data['dynamic'])) {
                            $control_data['dynamic']['active'] = true;              
                        } else {
                            if (isset($control_data['dynamic']['active'])) {
                                // natively
                                if (!$control_data['dynamic']['active']) {
                                    $control_data['dynamic']['active'] = true;
                                }
                            } else {
                                // active => false, so no force them
                            }
                        }
                    } else {
                        //var_dump($control_data['type']);
                    }
                }
            }
        }
        return $control_data;
    }
    
    /**
        * Render controls.
        *
        * Generate the final HTML for all the registered controls using the element
        * template.
        *
        * @since 1.0.0
        * @access public
        */
       /*public function render_controls() {
               foreach ( $this->get_controls() as $control ) {                                       
                    if ($control->get_type() == 'ooo_query') {
                       ob_start();
                    }
                    $control->print_template();
                    if ($control->get_type() == 'ooo_query') {
                       $template = ob_get_clean();
                       //$template = str_replace('tmpl-elementor-control-'.esc_attr( $control->get_type() ).'-content', 'tmpl-elementor-control-'.esc_attr( $control->get_type() ).'-content elementor-control-dynamic', $template);
                       echo $template;
                    }
               }
       }*/
}
