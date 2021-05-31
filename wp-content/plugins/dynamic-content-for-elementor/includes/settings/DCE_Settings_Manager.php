<?php

namespace DynamicContentForElementor\Includes\Settings;

use Elementor\Core\Files\CSS\Base;
use Elementor\Controls_Manager;
use Elementor\Core\Files\CSS\Global_CSS;
use Elementor\Core\Settings\Base\CSS_Manager;
use Elementor\Core\Responsive\Responsive;

use DynamicContentForElementor\Includes\Settings\Model as DCE_Settings_Model;

if (!defined('ABSPATH')) {
    exit;
}

class DCE_Settings_Manager extends CSS_Manager {

    const PANEL_TAB_SETTINGS = 'settings';
    const META_KEY = '_dce_general_settings';
    
    static public $dir = DCE_PATH . 'includes/settings/global';
    static public $namespace = '\\DynamicContentForElementor\\Includes\\Settings\\';
    static public $settings = [];
    static public $registered_settings = [];

    /**
     * Settings manager constructor.
     *
     * Initializing DCE settings manager.
     *
     * @access public
     */
    public function __construct() {
        parent::__construct();
        //self::init();
        self::add_panel_tabs();
        self::dce_settings_manager();
 
    }

    /**
     * Get manager name.
     *
     * Retrieve settings manager name.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access public
     *
     * @return string settings manager name
     */
    public function get_name() {
        return 'dynamicooo';
    }

    /**
     * Get CSS file name.
     *
     * Retrieve CSS file name for the settings base css manager.
     *
     * @see Elementor\Core\Settings\Base\CSS_Manager
     *
     * @access protected
     *
     * @return string CSS file name
     */    
    protected function get_css_file_name() {
        return 'global';
    }

    /**
     * Get model for CSS file.
     *
     * Retrieve the model for the CSS file.
     *
     * @see Elementor\Core\Settings\Base\CSS_Manager
     *
     * @access protected
     *
     * @param CSS_File $css_file The requested CSS file.     
     * @return CSS_Model
     */    
    protected function get_model_for_css_file( Base $css_file ) {
        return $this->get_model();
    }

    /**
     * Get CSS file for update.
     *
     * Retrieve the CSS file before updating it.
     *
     * @see Elementor\Core\Settings\Base\CSS_Manager
     *
     * @access protected
     *
     * @param int $id Post ID.
     * @return CSS_File
     */
    protected function get_css_file_for_update( $id ) {
        return Global_CSS::create( 'global.css' );
    }

    /**
     * Get model for config.
     *
     * Retrieve the model for settings configuration.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access public
     *
     * @return Model The model object.
     */
    public function get_model_for_config() {
        return $this->get_model();
    }

    private function add_panel_tabs() {
        Controls_Manager::add_tab(self::PANEL_TAB_SETTINGS, __('Settings', 'dynamic-content-for-elementor'));
    }

    protected function dce_settings_manager() {


        $model_controls = DCE_Settings_Model::get_controls_list();
        //$settings_e_site = $this->get_saved_settings(0);
        //var_dump($settings_e_site);
        //var_dump( get_option( 'enable_smoothtransition' ));
        //die();
        
                
        //add_action( 'elementor/page_templates/header-footer/before_content',array($this,'dce_before_page') );
        //if(get_option( 'selector_wrapper' )) var_dump( get_option( 'selector_wrapper' ) );
    }

    public function dce_settings() {
        //$settings_e_site = self::get_saved_settings(0);
        $settings_e_site = $this->get_saved_settings(0);
        
        $default_breakpoints = Responsive::get_default_breakpoints();
        
        
        /*
        var_dump( $default_breakpoints['md'] );
        var_dump( $default_breakpoints['lg'] );
        var_dump( $default_breakpoints['xl'] );
        */
        
        //var_dump( get_option('elementor_viewport_md') );
        //var_dump( get_option('elementor_viewport_lg') );
        //var_dump( get_option('elementor_viewport_xl') );
        
        
        if(get_option('elementor_viewport_md')){
            $breakpointsMd = get_option('elementor_viewport_md');
        }else{
            $breakpointsMd = $default_breakpoints['md'];
        }
        if(get_option('elementor_viewport_lg')){
            $breakpointsLg = get_option('elementor_viewport_lg');
        }else{
            $breakpointsLg = $default_breakpoints['lg'];
        }
        if(get_option('elementor_viewport_xl')){
            $breakpointsXl = get_option('elementor_viewport_xl');
        }else{
            $breakpointsXl = $default_breakpoints['xl'];
        }
        $settings_e_site['elementor_viewport_md'] = $breakpointsMd;
        $settings_e_site['elementor_viewport_lg'] = $breakpointsLg;
        $settings_e_site['elementor_viewport_xl'] = $breakpointsXl;
        
        return $settings_e_site;
    }
    public static function dce_before_page() {
        echo 'prima della pagina';
    }

    /**
     * Get saved settings.
     *
     * Retrieve the saved settings from the database.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access protected
     *
     * @param int $id Post ID
     * @return array
     */
    protected function get_saved_settings($id) {
        $model_controls = DCE_Settings_Model::get_controls_list();

        $settings = [];

        foreach ($model_controls as $tab_name => $sections) {

            foreach ($sections as $section_name => $section_data) {

                foreach ($section_data['controls'] as $control_name => $control_data) {
                    $saved_setting = get_option($control_name, null);

                    if (null !== $saved_setting) {
                        $settings[$control_name] = get_option($control_name);
                    }
                }
            }
        }

        return $settings;
    }

    /**
     * Save settings to DB.
     *
     * Save settings to the database.
     *
     * @see Elementor\Core\Settings\Base\Manager
     *
     * @access protected
     *
     * @param array $settings Settings
     * @param int $id Post ID
     * @return array
     */
    protected function save_settings_to_db(array $settings, $id) {
        $model_controls = DCE_Settings_Model::get_controls_list();

        $one_list_settings = [];

        foreach ($model_controls as $tab_name => $sections) {

            foreach ($sections as $section_name => $section_data) {

                foreach ($section_data['controls'] as $control_name => $control_data) {
                    if (isset($settings[$control_name])) {
                        $one_list_control_name = str_replace('elementor_', '', $control_name);

                        $one_list_settings[$one_list_control_name] = $settings[$control_name];

                        update_option($control_name, $settings[$control_name]);
                    } else {
                        delete_option($control_name);
                    }
                }
            }
        }

        // Save all settings in one list for a future usage
        if (!empty($one_list_settings)) {
            update_option(self::META_KEY, $one_list_settings);
        } else {
            delete_option(self::META_KEY);
        }
    }
    
    public static function init() {
        self::$settings = self::get_settings();
        self::on_settings_registered();
    }
    
    public static function get_settings() {
        $tmpSettings = [];
        $settings = glob(self::$dir. '/DCE_*.php');
        foreach ($settings as $key => $value) {
            $class = pathinfo($value, PATHINFO_FILENAME);
            $tmpSettings[strtolower($class)] = $class;
        }
        return $tmpSettings;
    }
    
    /**
    * On extensions Registered
    *
    * @since 0.0.1
    *
    * @access public
    */
    public static function on_settings_registered() {        
        self::includes();
        self::register_settings();
        
        $settings_controls = \DynamicContentForElementor\Includes\Settings\Model::get_controls_list();
        if (!empty($settings_controls['settings'])) {
            \Elementor\Core\Settings\Manager::add_settings_manager( new \DynamicContentForElementor\Includes\Settings\DCE_Settings_Manager() );
        }
    }
    
    public static function includes() {
        //require_once( self::$dir . '/../DCE_Settings_Prototype.php' ); // obbligatorio in quanto esteso dagli altri
        foreach (self::get_settings() as $key => $value) {
            require_once self::$dir.'/'.$value.'.php';
        }
    }
    
    public static function get_excluded_settings() {
        return json_decode(get_option(SL_PRODUCT_ID . '_excluded_globals', '[]'), true);    
    }
    
    public static function get_active_settings() {
        $excluded_settings = self::get_excluded_settings();
        /*if (empty($excluded_settings)) {
            return self::$settings;
        }*/
        $active_settings = array();
        if (!empty(self::$settings)) {
            foreach (self::$settings as $skey => $setting) {
                if (!isset($excluded_settings[$setting])) {
                    $class = self::$namespace.$setting;
                    if ($class::is_enabled()) {
                        $active_settings[$skey] = $setting;
                    }
                }
            }
        }
        return $active_settings;
    }
    
    
    /**
    * On Controls Registered
    *
    * @since 1.0.4
    *
    * @access public
    */
    public static function register_settings() {        
        $excluded_settings = self::get_excluded_settings();
        foreach (self::$settings as $key => $name) {
            if (!isset($excluded_settings[$name])) { // controllo se lo avevo escluso in quanto non interessante
                $class = self::$namespace . $name;
                //var_dump($aWidgetObjname);
                if ($class::is_enabled() && $class::get_satisfy_dependencies()) {
                    //echo $class;
                    self::$registered_settings[] = new $class();
                }
            }
        }
    }

}