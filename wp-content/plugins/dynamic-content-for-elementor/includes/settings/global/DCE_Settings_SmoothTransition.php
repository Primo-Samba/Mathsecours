<?php

namespace DynamicContentForElementor\Includes\Settings;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class DCE_Settings_SmoothTransition extends DCE_Settings_Prototype {
    
    public static $name = 'Smooth Transition';
    
    public static function is_enabled() {
        return true;
    }
    
    public function __construct() {
        if (get_option('enable_smoothtransition')) {
            add_filter('body_class', array($this, 'dce_add_class'), 10);
        }
    }

    public function get_name() {
        return 'dce-settings_smoothtransition';
    }

    public function get_css_wrapper_selector() {
        return 'body.dce-smoothtransition';
    }

    /* public function dce_add_class($classes) {
      $classes[] = 'dce-smoothtransition';
      return $classes;
      } */
    
    public static function dce_add_class($classes) {
        $classes[] = 'dce-smoothtransition';
        
        if(get_option('smoothtransition_enable_overlay')){
            $classes[] = 'smoothtransition-overlay';
        }
        return $classes;
    }

    public static function get_controls() {
        $wrapper = 'body.dce-smoothtransition';
        $target_smoothTransition = '';
        $selector_wrapper = get_option('selector_wrapper');
        if ($selector_wrapper) {
            $target_smoothTransition = ' ' . $selector_wrapper;
        }

        return [
            'label' => __('Smooth Transition', 'dynamic-content-for-elementor'),
            'controls' => [
                'enable_smoothtransition' => [
                    'label' => __('<h3>Enable smooth transition</h3>', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => __('No', 'dynamic-content-for-elementor'),
                    'label_on' => __('yes', 'dynamic-content-for-elementor'),
                    'return_value' => 'yes',
                    'default' => '',
                    'frontend_available' => true,
                    'separator' => 'before',
                    //'render_type' => 'template',

                    
                ],
                'selector_wrapper' => [
                    'label' => __('Selector Wrapper', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'default' => '',
                    'label_block' => true,
                    'placeholder' => 'Write CSS selector (ex: #wrapper)',
                    'frontend_available' => true,
                    'dynamic' => [
                        'active' => false,
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                    ],
                ],
                //
                'dce_smoothtransition_class_debug' => [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => __('<div class="dce-class-debug">...</div>', 'dynamic-content-for-elementor'),
                    'content_classes' => 'dce_class_debug',
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                    ],
                ],
                'dce_smoothtransition_class_controller' => [
                    'label' => __('Controller', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HIDDEN,
                    'default' => '',
                    'frontend_available' => true,
                    'selectors' => [
                        $wrapper.$target_smoothTransition  => '
                            position: relative;
                            opacity: 0;
                            will-change: opacity; 
                            -webkit-animation-fill-mode: both; 
                                    animation-fill-mode: both;',


                        $wrapper.'.elementor-editor-active'.$target_smoothTransition.', '.$wrapper.'.elementor-editor-preview'.$target_smoothTransition  => 'opacity: 1;'
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                    ],
                ],
                //
                'dce_smoothtransition_settings_note' => [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => __('<span style="font-size: 11px; font-weight: 400; font-style: italic;"><i class="fa fa-life-ring" aria-hidden="true"></i> The selector wrapper is very important for the proper functioning of the transitions. indicates the part of the page that needs to be transformed. <a href="https://docs.dynamic.ooo/article/149-html-structure-of-themes" target="_blank">This article can help you.</a></span>', 'dynamic-content-for-elementor'),
                    'content_classes' => '',
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                    ],
                ],
                'a_class' => [
                    'label' => __('Target [a href] CLASS', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::TEXTAREA,
                    'label_block' => true,
                    'row' => 3,
                    'default' => 'a:not([target="_blank"]):not([href=""]):not([href^="uploads"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.no-transition):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox):not(.is-lightbox):not(.elementor-icon):not(.download-link):not([href*="elementor-action"]):not(.dialog-close-button):not([data-elementor-open-lightbox="yes"])',
                    'placeholder' => 'a:not([target="_blank"]):not([href=""]):not([href^="uploads"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.no-transition):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox):not(.is-lightbox):not(.elementor-icon):not(.download-link):not([href*="elementor-action"]):not(.dialog-close-button)',
                    'frontend_available' => true,
                    'separator' => 'before',
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                ],
                
                // ----------------------- ANIMATIONS
                // OUT
                 'dce_smoothtransition_animation_out_heading' => [
                    'label' => __('Animation OUT', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                     'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ]
                ],
                'dce_smoothtransition_style_out' => [
                    'label' => __('Style of transition OUT', 'dynamic-content-for-elementor'),

                    'type' => Controls_Manager::SELECT,
                    'default' => 'exitToFade',
                    'groups' => DCE_Helper::get_anim_close(),
                    'frontend_available' => true,
                    'selectors' => [
                        $wrapper.$target_smoothTransition.'.dce-anim-style-out' => 'animation-name: {{VALUE}}, fade-out; -webkit-animation-name: {{VALUE}}, fade-out;'
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ]
                ],
                'smoothtransition_speed_out' => [
                    'label'     => __( 'Speed Out', 'dynamic-content-for-elementor' ),
                    'type'      => Controls_Manager::SLIDER,
                    'default'   => [
                        'size'  => 500,
                    ],
                    'range'   => [
                        'px'  => [
                            'min'   => 0,
                            'max'   => 2000,
                            'step'  => 10,
                        ],
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                    'frontend_available' => true
                ],
                'smoothtransition_timingFuncion_out' => [
                    'label' => __('Timing function OUT', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'groups' => DCE_Helper::get_anim_timingFunctions(),
                    'default' => 'ease-in-out',
                    'frontend_available' => true,
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                    'selectors' => [
                        $wrapper.$target_smoothTransition.'.dce-anim-style-out' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};'
                    ]
                ],

                // IN
                'dce_smoothtransition_animation_in_heading' => [
                    'label' => __('Animation IN', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                     'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ]
                ],



                'dce_smoothtransition_style_in' => [
                    'label' => __('Style of transition IN', 'dynamic-content-for-elementor'),

                    'type' => Controls_Manager::SELECT,
                    'default' => 'enterFromFade',
                    'groups' => DCE_Helper::get_anim_open(),
                    'selectors' => [
                        $wrapper.$target_smoothTransition.'.dce-anim-style-in' => 'animation-name: {{VALUE}}, fade-in; -webkit-animation-name: {{VALUE}}, fade-in;'
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ]
                ],
                'smoothtransition_speed_in' => [
                    'label'     => __( 'Speed In', 'dynamic-content-for-elementor' ),
                    'type'      => Controls_Manager::SLIDER,
                    'default'   => [
                        'size'  => 500,
                    ],
                    'range'   => [
                        'px'  => [
                            'min'   => 0,
                            'max'   => 2000,
                            'step'  => 10,
                        ],
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                    'frontend_available' => true
                ],
                'smoothtransition_timingFuncion_in' => [
                    'label' => __('Timing function IN', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'groups' => DCE_Helper::get_anim_timingFunctions(),
                    'default' => 'ease-in-out',
                    'frontend_available' => true,
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                    'selectors' => [
                        $wrapper.$target_smoothTransition.'.dce-anim-style-in' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};',
                    ]
                ],
                /* OVERLAY */
                'smoothtransition_overlay_heading' => [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => '<strong><i class="fa fa-copy" aria-hidden="true"></i> '.__('Overlay effect', 'dynamic-content-for-elementor').'</strong>',
                    'content_classes' => '',
                    'separator' => 'before',
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                ],
                'smoothtransition_enable_overlay' => [
                    'label' => __('Use overlay', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => __('No', 'dynamic-content-for-elementor'),
                    'label_on' => __('Yes', 'dynamic-content-for-elementor'),
                    'return_value' => 'yes',
                    'default' => '',
                    'frontend_available' => true,
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
    
                ],
                'smoothtransition_overlay_style' => [
                    'label' => __('Overlay Style', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'left',
                    'options' => [
                        'left' => 'Left',
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                        //'diagonal' => 'Diagonal',
                        //'circle' => 'Circle',
                    ],  
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'smoothtransition_enable_overlay!' => '',
                    ],
                    'selectors' => [
                        $wrapper.'.smoothtransition-overlay:after' => 'animation-name: dce-overlay-out-{{VALUE}}; -webkit-animation-name: dce-overlay-out-{{VALUE}};',
                        $wrapper.'.smoothtransition-overlay.overlay-out:after' => 'animation-name: dce-overlay-in-{{VALUE}}; -webkit-animation-name: dce-overlay-in-{{VALUE}};',
                    ],
                    'frontend_available' => true
                ],
                'smoothtransition_overlay_color' => [
                    'label' => __('overlay Color', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'smoothtransition_enable_overlay!' => '',
                    ],
                    'selectors' => [
                        $wrapper.'.smoothtransition-overlay:after' => 'background-color: {{VALUE}};',
                    ]

                ],
                'smoothtransition_overlay_image' => [
                    'label' => __('Ovarlay Image', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'smoothtransition_enable_overlay!' => '',
                    ],
                    'selectors' => [
                        $wrapper.'.smoothtransition-overlay:after', $wrapper.'.smoothtransition-overlay.overlay-out:after' => 'background-image: url({{URL}});',
                    ]
                ],
                
                
      
                /*'smoothtransition_enable_overlay_dual' => [
                    'label' => __('Dual overlay', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => __('No', 'dynamic-content-for-elementor'),
                    'label_on' => __('Yes', 'dynamic-content-for-elementor'),
                    'return_value' => 'yes',
                    'default' => '',
                    'frontend_available' => true,
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'smoothtransition_enable_overlay!' => '',
                    ],
    
                ],*/


                'smoothtransition_loading_heading' => [
                    'type' => Controls_Manager::RAW_HTML,
                    'raw' => '<strong><i class="fa fa-spinner" aria-hidden="true"></i> '.__('Loading Spin', 'dynamic-content-for-elementor').'</strong>',
                    'content_classes' => '',
                    'separator' => 'before',
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                ],
                'smoothtransition_debug_loading' => [
                    'label' => __('Loading debug', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'label_off' => __('ON', 'dynamic-content-for-elementor'),
                    'label_on' => __('OFF', 'dynamic-content-for-elementor'),
                    'return_value' => 'yes',
                    'default' => '',
                    'frontend_available' => true,
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                    'selectors' => [
                        $wrapper.'.elementor-editor-active'.$target_smoothTransition.', '.$wrapper.'.elementor-editor-preview'.$target_smoothTransition  => 'opacity: 1;',
                        $wrapper.'.elementor-editor-active .animsition-loading, '.$wrapper.'.elementor-editor-preview .animsition-loading' => 'display: none;',
                    ]
                ],

                'smoothtransition_loading_mode' => [
                    'label' => __('Loading Mode', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'circle' => [
                            'title' => __('Circle', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-circle-o-notch',
                        ],
                        'image' => [
                            'title' => __('Image', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-picture-o',
                        ],
                        'none' => [
                            'title' => __('None', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-ban',
                        ],
                    ],
                    'render_type' => 'template',
                    'default' => 'circle',

                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => ''
                    ],
                    'frontend_available' => true
                ],
                'smoothtransition_loading_style' => [
                    'label' => __('Loading Animation Style', 'dynamic-content-for-elementor'),

                    'type' => Controls_Manager::SELECT,
                    'default' => 'fade',
                    'options' => [
                        'rotate' => 'Rotate',
                        'pulse' => 'pulse',
                        'fade' => 'Fade',
                        'none' => 'None',
                    ],  
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        'smoothtransition_loading_mode' => 'image'
                    ],
                    'frontend_available' => true
                ],
                'smoothtransition_loading_image' => [
                    'label' => __('Loading Image', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        'smoothtransition_loading_mode' => 'image'
                    ],
                    'selectors' => [
                        $wrapper.' .animsition-loading.loading-mode-image' => 'background-image: url({{URL}});',
                    ]
                ],

                'smoothtransition_loading_color_circle' => [
                    'label' => __('Circle Color', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::COLOR,

                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        'smoothtransition_loading_mode' => 'circle'
                    ],
                    'selectors' => [
                        $wrapper.' .animsition-loading' => 'border-top-color: {{VALUE}}; border-right-color: {{VALUE}}; border-bottom-color: {{VALUE}};',
                    ]

                ],
                'smoothtransition_loading_color_progress' => [
                    'label' => __('Circle Progress Color', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::COLOR,

                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        'smoothtransition_loading_mode' => 'circle'
                    ],
                    'selectors' => [
                        $wrapper.' .animsition-loading' => 'border-left-color: {{VALUE}};',
                    ]
                ],
                'smoothtransition_loading_size' => [
                    'label'     => __( 'Size', 'dynamic-content-for-elementor' ),
                    'type'      => Controls_Manager::SLIDER,
                    'default'   => [
                        'size'  => 32,
                    ],
                    'size_units' => [ 'px', 'vw','vh' ],
                    'range'   => [
                        'px'  => [
                            'min'   => 0,
                            'max'   => 500,
                            'step'  => 1,
                        ],

                        'vw'  => [
                            'min'   => 0,
                            'max'   => 100,
                            'step'  => 1,
                        ],
                        'vh'  => [
                            'min'   => 0,
                            'max'   => 100,
                            'step'  => 1,
                        ],
                    ],
                    'condition' => [
                        'smoothtransition_loading_mode!' => 'none',
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        //'smoothtransition_loading_extendimage' => ''
                    ],
                    'selectors' => [
                        $wrapper.' .animsition-loading' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2);',
                    ]
                ],
                'smoothtransition_loading_extendimage' => [
                    'label' => __('Extend image', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,

                    'default' => '',
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        'smoothtransition_loading_mode' => 'image'
                    ],
                    'selectors' => [
                        $wrapper.' .animsition-loading.loading-mode-image' => 'width: 100%; height: 100%; margin: 0; top: 0; left: 0; background-size: cover;',
                    ]
                ],
                'smoothtransition_loading_weight' => [
                    'label'     => __( 'Circle Weight', 'dynamic-content-for-elementor' ),
                    'type'      => Controls_Manager::SLIDER,
                    'default'   => [
                        'size'  => 5,
                    ],
                    'range'   => [
                        'px'  => [
                            'min'   => 1,
                            'max'   => 50,
                            'step'  => 1,
                        ],
                    ],
                    'condition' => [
                        'enable_smoothtransition' => 'yes',
                        'dce_smoothtransition_class_controller!' => '',
                        'smoothtransition_loading_mode' => 'circle'
                    ],
                    'selectors' => [
                        $wrapper.' .animsition-loading' => 'border-width: {{SIZE}}{{UNIT}};',
                    ]
                ],
                'responsive_smoothtransition' => [
                    'label' => __('Apply smoothTransition on:', 'dynamic-content-for-elementor'),
                        'description' => __('Responsive mode will take effect only on preview or live page, and not while editing in Elementor.', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SELECT2,
                        'multiple' => true,
                        'separator' => 'before',
                        'label_block' => true,
                        'options' => [
                            'desktop' => __('Desktop', 'dynamic-content-for-elementor'),
                            'tablet' => __('Tablet', 'dynamic-content-for-elementor'),
                            'mobile' => __('Mobile', 'dynamic-content-for-elementor'),
                        ],
                        'default' => ['desktop', 'tablet', 'mobile'],
                        'frontend_available' => true,
                        'render_type' => 'template',
                        'condition' => [
                            'enable_smoothtransition' => 'yes',
                            'dce_smoothtransition_class_controller!' => '',
                        ],
                ],
            ]
        ];
    }

}
