<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Template
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */

class DCE_Widget_Template extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-template';
    }
    
    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('Dynamic Template', 'dynamic-content-for-elementor');
    }

    public function get_icon() {
        return 'icon-dyn-layout';
    }
    public function get_description() {
        return __('Include every element of your site in a template without having to redo it');
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/dynamic-template/';
    }

    /**
     * A list of scripts that the widgets is depended in
     * @since 1.3.0
     * */
    public function get_script_depends() {
        return [ ];
    }
    static public function get_position() {
        return 4;
    }
    protected function _register_controls() {
        $this->start_controls_section(
                'section_dynamictemplate', [
                'label' => __('Template', 'dynamic-content-for-elementor'),
            ]
        );
        /*$this->add_control(
          'dynamic_template', [
            'label' => __('Select Template', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT2,
            'label_block' => true,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => DCE_Helper::get_all_template(),
            'default' => ''
          ]
        );*/
        $this->add_control(
                'dynamic_template',
                [
                    'label' => __('Select Template', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Template Name', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'object_type' => 'elementor_library',
                ]
        );
        $this->add_control(
            'data_source',
            [
              'label' => __( 'Source', 'dynamic-content-for-elementor' ),
              'description' => __( 'Select the data source', 'dynamic-content-for-elementor' ),
              'type' => Controls_Manager::SWITCHER,
              'default' => 'yes',
              'label_on' => __( 'Same', 'dynamic-content-for-elementor' ),
              'label_off' => __( 'other', 'dynamic-content-for-elementor' ),
              'return_value' => 'yes',
              'separator' => 'before'
            ]
        );
        /*$this->add_control(
            'other_post_source', [
              'label' => __('Select source from other post', 'dynamic-content-for-elementor'),
              'type' => Controls_Manager::SELECT,
              
              'groups' => DCE_Helper::get_all_posts(get_the_ID(),true),
              'default' => '',
              'condition' => [
                'data_source' => '',
              ], 
            ]
        );*/
        $this->add_control(
                'other_post_source',
                [
                    'label' => __('Select from other source post', 'dynamic-content-for-elementor'),
                    'type' 		=> 'ooo_query',
                    'placeholder'	=> __( 'Post Title', 'dynamic-content-for-elementor' ),
                    'label_block' 	=> true,
                    'query_type'	=> 'posts',
                    'condition' => [
                        'data_source' => '',
                    ],
                ]
        );
        $this->add_control(
                'other_user_id',
                [
                    'label' => __('Select other User', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Force User content', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'users',
                    'condition' => [
                        'data_source' => '',
                    ],
                ]
        );
        $this->add_control(
                'other_author_id',
                [
                    'label' => __('Select other Author', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Force Author content', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'users',
                    'condition' => [
                        'data_source' => '',
                    ],
                ]
        );
        
        
        $this->end_controls_section();


       
    }

    protected function render() {
      $settings = $this->get_settings_for_display();
        if ( empty( $settings ) )
            return;
        //
        // ------------------------------------------
        $id_page = DCE_Helper::get_the_id();
        //

        $dce_default_template = $settings[ 'dynamic_template' ];
        //echo $dce_default_template;
        
        $inlinecss = '';
        if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
            $inlinecss = ' inlinecss="true"';
        }
        $post_id = '';
        if (empty($settings['data_source'])) {
            if ($settings['other_post_source']) {
                $post_id .= ' post_id="'.$settings['other_post_source'].'"';
            }
            if ($settings['other_user_id']) {
                $post_id .= ' user_id="'.$settings['other_user_id'].'"';
            }
            if ($settings['other_author_id']) {
                $post_id .= ' author_id="'.$settings['other_author_id'].'"';
            }
        }
        ?>
        <div class="dce-template">
        <?php
        if (!empty($dce_default_template)) {
            echo do_shortcode('[dce-elementor-template id="' . $dce_default_template . '"'.$post_id.$inlinecss.']');
        }
        ?>
        </div>
        <?php
    }
}
