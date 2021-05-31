<?php

namespace DynamicContentForElementor\Includes\Settings;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class DCE_Settings_Prototype {
    
    public static $name = 'Global Settings Prototype';

    public function get_name() {
        return 'dce_settings_prototype';
    }
    
    public static function is_enabled() {
        return true;
    }
    
    public static function get_satisfy_dependencies() {
        return true;
    }

    public function get_css_wrapper_selector() {
        return 'body';
    }
    
    /*public function dce_add_class($classes) {
            $classes[] = 'dce-smoothtransition';
            return $classes;
    }*/

    public static function get_controls() {        
        return [];
    }

}
