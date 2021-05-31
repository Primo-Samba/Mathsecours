<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor ACF-GoogleMaps
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_GoogleMaps extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-acf-google-maps';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('ACF Google Maps', 'dynamic-content-for-elementor');
    }

    public function get_description() {
        return __('Build a map using data from a Google Maps ACF', 'dynamic-content-for-elementor');
    }

    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/acf-maps/';
    }

    public function get_icon() {
        return 'icon-dyn-map';
    }

    public function get_script_depends() {
        return ['dce-google-maps', 'google-maps-markerclusterer', 'google-maps-api'];
    }
    public function get_style_depends() {
        return ['dce-acfGooglemap'];
    }
    static public function get_position() {
        return 4;
    }

    public function get_plugin_depends() {
        return array('acf');
    }

    protected function _register_controls() {
        $taxonomies = \DynamicContentForElementor\DCE_Helper::get_taxonomies();

        $this->start_controls_section(
                'section_map', [
            'label' => __('ACF Google Maps', 'dynamic-content-for-elementor'),
                ]
        );

        $this->add_control(
                'map_data_type', [
            'label' => __('Data Type', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'acfmap',
            'options' => [
                'acfmap' => __('ACF Map Field', 'dynamic-content-for-elementor'),
                'address' => __('Address', 'dynamic-content-for-elementor'),
                'latlng' => __('Latitude Longitude', 'dynamic-content-for-elementor'),
            ],
            'frontend_available' => true,
                ]
        );
        /*$this->add_control(
            'acf_mapfield', [
                'label' => __('ACF Map', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => DCE_Helper::get_acf_fields('google_map'),
                'frontend_available' => true,
                'label_block' => true,
                'condition' => [
                    'map_data_type' => 'acfmap',
                ],
            ]
        );*/
        $this->add_control(
                'acf_mapfield',
                [
                    'label' => __('ACF Map', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'google_map',
                    'frontend_available' => true,
                    'condition' => [
                        'map_data_type' => 'acfmap',
                    ],
                ]
        );
        
        
        // --------------------------------- [ Use Query post ]
        $this->add_control(
                'use_query_heading',
                [
                    'label' => __('Multilocations from CPT, Relationship, Repeater, Post', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'map_data_type' => 'acfmap',
                        'acf_mapfield!' => ['', null],
                    ]
                ]
        );
        $this->add_control(
            'use_query', [
                'label' => __('Use Query', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                
                'condition' => [
                        'map_data_type' => 'acfmap',
                        'acf_mapfield!' => ['', null],
                    ]
            ]
        );
        $this->add_control(
            'auto_zoom', [
                'label' => __('Force automatic Zoom', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'frontend_available' => true,                
                'condition' => [
                        'map_data_type' => 'acfmap',
                        'acf_mapfield!' => ['', null],
                    ]
            ]
        );
        $this->add_control(
                'address', [
            'label' => __('Manual address', 'dynamic-content-for-elementor'),
            //'description' => __('Only works if the ACF field is not set', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,

            //'placeholder' => $default_address,
            'default' => 'Venice', //$default_address,
            'label_block' => true,
            //'readonly' => true,
            'condition' => [
                'map_data_type' => 'address',
            ],
                ]
        );
        $this->add_control(
            'latitudine', [
                'label' => __('Manual Latitude', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                //'placeholder' => $default_address,
                'default' => '45.4371908', //$default_address,
                //'readonly' => true,
                'condition' => [
                    'map_data_type' => 'latlng',
                ],
            ]
        );
        $this->add_control(
            'longitudine', [
                'label' => __('Manual Longitude', 'dynamic-content-for-elementor'),
                //'description' => __('Only works if the ACF field is not set', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                //'placeholder' => $default_address,
                'default' => '12.3345898', //$default_address,
                //'readonly' => true,
                'condition' => [
                    'map_data_type' => 'latlng',
                ],
            ]
        );
        $this->add_control(
            'zoom', [
                'label' => __('Zoom Level', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'separator' => 'before',
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                /*'condition' => [
                    'auto_zoom' => '',
                ],*/
                
            //'frontend_available' => true,
            ]
        );

        $this->add_responsive_control(
                'height', [
            'label' => __('Height', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 300,
            ],
            'range' => [
                'px' => [
                    'min' => 40,
                    'max' => 1440,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} #el-wgt-map-{{ID}}' => 'height: {{SIZE}}{{UNIT}};',
            ],
                //'frontend_available' => true,
                ]
        );

        $this->add_control(
                'prevent_scroll', [
            'label' => __('Scroll', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
            'label_on' => __('Yes', 'dynamic-content-for-elementor'),
            'label_off' => __('No', 'dynamic-content-for-elementor'),
            'render_type' => 'template',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'enable_infoWindow', [
            'label' => __('Enable Info Window', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'separator' => 'before',
            'render_type' => 'template',
            'frontend_available' => true,
                ]
        );
        

        $this->add_control(
                'view', [
            'label' => __('View', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::HIDDEN,
            'default' => 'traditional',
                ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
                'section_mapInfoWIndow', [
            'label' => __('Info Window', 'dynamic-content-for-elementor'),
            'condition' => [
                'enable_infoWindow' => 'yes'
            ]
                ]
        );
        /* $this->add_control(
          'custom_infoWindow_template',
          [
          'label' => __('Template', 'dynamic-content-for-elementor'),
          'type' => 'ooo_query',
          'placeholder' => __('Select Template', 'dynamic-content-for-elementor'),
          'label_block' => true,
          'query_type' => 'posts',
          'object_type' => 'elementor_library',
          'description' => 'Use a Elementor Template as content of InfoWindow.',
          'condition' => [
          'use_query' => 'yes',
          'infoWindow_click_to_post' => '',
          ],
          ]
          ); */
        $this->add_control(
                'infoWindow_click_to_post', [
            'label' => __('Link to post', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                'map_data_type' => 'acfmap',
                'acf_mapfield!' => ['', null],
                'use_query!' => '',
            ],
                ]
        );
        /* $this->add_control(
          'infoWindow_heading_content',
          [
          'label' => __( 'InfoWindow Content', 'dynamic-content-for-elementor' ),
          'type' => Controls_Manager::HEADING,
          'separator' => 'before',
          'condition' => [
          'infoWindow_click_to_post' => '',
          ],
          ]
          );
          $this->add_control(
          'infoWindow_title', [
          'label' => __('Title', 'dynamic-content-for-elementor'),
          'type' => Controls_Manager::SWITCHER,
          'label_on' => __('Yes', 'dynamic-content-for-elementor'),
          'label_off' => __('No', 'dynamic-content-for-elementor'),
          'frontend_available' => true,
          'condition' => [
          'acf_mapfield!' => ['', null],
          'use_query' => 'yes',
          ],
          ]
          );
          $this->add_control(
          'infoWindow_image', [
          'label' => __('Image', 'dynamic-content-for-elementor'),
          'type' => Controls_Manager::SWITCHER,
          'label_on' => __('Yes', 'dynamic-content-for-elementor'),
          'label_off' => __('No', 'dynamic-content-for-elementor'),
          'frontend_available' => true,
          'condition' => [
          'acf_mapfield!' => ['', null],
          'use_query' => 'yes',
          ],
          ]
          ); */

        /*$this->add_control(
                'custom_infoWindow_style', [
            'label' => __('Style', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'text' => [
                    'title' => __('Simple Text', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-font',
                ],
                'text_wysiwyg' => [
                    'title' => __('HTML Wysiwyg', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-align-left',
                ],
            ],
            'toggle' => false,
            'default' => 'text',
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query' => '',
            ],
                ]
        );*/

        $this->add_control(
                'custom_infoWindow_render', [
            'label' => __('Render', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'simple' => [
                    'title' => __('Simple mode', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-align-left',
                ],
                'html' => [
                    'title' => __('HTML with Tokens', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-code',
                ],
            ],
            'toggle' => false,
            'default' => 'simple',
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
            ],
                ]
        );
        // --------- HTML
        $this->add_control(
                'infoWindow_query_html',
                [
                    'label' => __('HTML', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::CODE,
                    'separator' => 'before',
                    'deafult' => '[post:ID|get_the_post_thumbnail(thumbnail)]<h4>[post:title]</h4>[post:excerpt]<br><a href="[post:permalink]">READ MORE</a>',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'html',
                    ],
                ]
        );
        /*$this->add_control(
                'custom_infoWindow',
                [
                    'label' => __('Custom text', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::CODE,
                    'frontend_available' => true,
                    'label_block' => true,
                    'condition' => [
                        'custom_infoWindow_style' => 'text',
                        'use_query' => '',
                    ],
                ]
        );*/
        $this->add_control(
                'custom_infoWindow_wysiwig',
                [
                    'label' => __('Custom text', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::WYSIWYG,
                    'frontend_available' => true,
                    'label_block' => true,
                    'condition' => [
                        //'custom_infoWindow_style' => 'text_wysiwyg',
                        'use_query' => '',
                    ],
                ]
        );

        $this->add_control(
                'infoWindow_heading_style',
                [
                    'label' => __('InfoWindow Style', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                    ],
                ]
        );
        $this->add_responsive_control(
                'infoWindow_align', [
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
                'justify' => [
                    'title' => __('Justified', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-align-justify',
                ],
            ],
            'prefix_class' => 'align-dce-',
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c' => 'text-align: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'infowindow_typography',
            'label' => __('Typography', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c',
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_textColor', [
            'label' => __('Text Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query' => '',
            ],
                ]
        );
        
        // --------- IMAGE
        $this->add_control(
                'infoWindow_heading_style_image',
                [
                    'label' => __('Image', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'simple',
                    ],
                ]
        );
        $this->add_control(
                'infowindow_query_show_image', [
            'label' => __('Show Image', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infowindow_query_extendimage', [
            'label' => __('Extend Image (BG mode)', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'frontend_available' => true,
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_responsive_control(
                'infowindow_query_bgimage_height', [
            'label' => __('Height', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 100,
            ],
            'sizes_unit' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 360,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image-bg' => 'height: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_extendimage!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_query_image_float', [
            'label' => __('Float', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_image!' => '',
                'custom_infoWindow_render' => 'simple',
            //'infowindow_query_extendimage!' => ''
            ],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image, {{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-textzone' => 'float: left;',
            ],
                ]
        );

        $this->add_responsive_control(
                'infowindow_query_image_size', [
            'label' => __('Distribution Size (%)', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 50,
            ],
            'size_units' => ['%'],
            'range' => [
                '%' => [
                    'min' => 10,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-image' => 'width: {{SIZE}}%;',
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-textzone' => 'width: calc( 100% - {{SIZE}}% );',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infoWindow_query_image_float!' => '',
                'infowindow_query_show_image!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                //'frontend_available' => true,
                ]
        );
        // --------- TITLE
        $this->add_control(
                'infoWindow_heading_style_title',
                [
                    'label' => __('Title', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'simple',
                    ],
                ]
        );
        $this->add_control(
                'infowindow_query_show_title', [
            'label' => __('Show Title', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'infowindow_query_typography_title',
            'label' => __('Title Typography', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title',
            'separator' => 'before',
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_title!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infowindow_query_color_title', [
            'label' => __('Title Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_title!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infowindow_query_bgcolor_title', [
            'label' => __('Title BackgroundColor', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_title!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infowindow_query_padding_title', [
            'label' => __('Title Padding', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_title!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        // --------- CONTENT
        $this->add_control(
                'infoWindow_heading_style_content',
                [
                    'label' => __('Content', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'simple',
                    ],
                ]
        );
        $this->add_control(
                'infowindow_query_show_content', [
            'label' => __('Show Content', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'infowindow_query_typography_content',
            'label' => __('Content Typography', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content',
            'separator' => 'before',
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_content!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infowindow_query_color_content', [
            'label' => __('Content Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_content!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );

        $this->add_control(
                'infowindow_query_padding_content', [
            'label' => __('Content Padding', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'infowindow_query_show_content!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        // --------- READMORE
        $this->add_control(
                'infoWindow_heading_style_readmore',
                [
                    'label' => __('Read more', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'simple',
                    ],
                ]
        );
        $this->add_control(
                'infowindow_query_show_readmore', [
            'label' => __('Show Readmore', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
            'condition' => [
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
                ]
        );
        $this->add_control(
                'infowindow_query_readmore_text', [
            'label' => __('Text button', 'dynamic-content-for-elementor'),
            //'description' => __('Separator caracters.','dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Read More', 'dynamic-content-for-elementor'),
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->start_controls_tabs('readmore_colors');

        $this->start_controls_tab(
                'infowindow_query_readmore_colors_normal',
                [
                    'label' => __('Normal', 'dynamic-content-for-elementor'),
                    'condition' => [
                        'infowindow_query_show_readmore!' => '',
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'simple',
                    ]
                ]
        );
        $this->add_control(
                'infowindow_query_readmore_color', [
            'label' => __('Text Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );

        $this->add_control(
                'infowindow_query_readmore_bgcolor', [
            'label' => __('Background Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'infowindow_query_readmore_border',
            'label' => __('Border', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn',
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab(
                'infowindow_query_readmore_colors_hover',
                [
                    'label' => __('Hover', 'dynamic-content-for-elementor'),
                    'condition' => [
                        'infowindow_query_show_readmore!' => '',
                        'infoWindow_click_to_post' => '',
                        'use_query!' => '',
                        'custom_infoWindow_render' => 'simple',
                    ]
                ]
        );

        $this->add_control(
                'infowindow_query_readmore_color_hover', [
            'label' => __('Text Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );

        $this->add_control(
                'infowindow_query_readmore_bgcolor_hover', [
            'label' => __('Background Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->add_control(
                'infowindow_query_readmore_hover_border_color', [
            'label' => __('Border Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infowindow_query_readmore_border_border!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn:hover' => 'border-color: {{VALUE}};',
            ],
                ]
        );
        $this->end_controls_tab();

        $this->end_controls_tabs();



        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'infowindow_query_typography_readmore',
            'label' => __('ReadMore Typography', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .dce-iw-readmore-btn',
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->add_responsive_control(
                'infowindow_query_readmore_align', [
            'label' => __('ReadMore Alignment', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'toggle' => true,
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
                ]
            ],
            'default' => 'left',
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-wrapper' => 'text-align: {{VALUE}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->add_responsive_control(
                'infowindow_query_readmore_padding', [
            'label' => __('ReadMore Padding', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );
        $this->add_responsive_control(
                'infowindow_query_readmore_margin', [
            'label' => __('ReadMore Margin', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );

        $this->add_control(
                'infowindow_query_readmore_border_radius', [
            'label' => __('ReadMore Border Radius', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'infowindow_query_box_shadow_readmore',
            'label' => __('ReadMore Box Shadow', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c .dce-iw-readmore-btn',
            'condition' => [
                'infowindow_query_show_readmore!' => '',
                'infoWindow_click_to_post' => '',
                'use_query!' => '',
                'custom_infoWindow_render' => 'simple',
            ]
                ]
        );

        // --------- PANEL
        $this->add_control(
                'infoWindow_heading_style_panel',
                [
                    'label' => __('Panel', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'infoWindow_click_to_post' => '',
                    ],
                ]
        );
        $this->add_responsive_control(
                'infoWindow_panel_maxwidth', [
            'label' => __('Max Width', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'labe_block' => false,
            'range' => [
                'px' => [
                    'min' => 40,
                    'max' => 1440,
                ],
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'infoWindow_bgColor', [
            'label' => __('Background Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after' => 'background: {{VALUE}} !important;',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );

        $this->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'infoWindow_border',
            'label' => __('Border', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c, {{WRAPPER}} .gm-style .gm-style-iw-t::after',
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_padding', [
            'label' => __('Padding panel', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'default' => [
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_padding_wrap', [
            'label' => __('Padding wrapper', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em'],
            'default' => [
                'unit' => 'px',
            ],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-d' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_control(
                'infoWindow_border_radius', [
            'label' => __('Border Radius', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%'],
            'selectors' => [
                '{{WRAPPER}} .gm-style .gm-style-iw-c' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'infoWindow_box_shadow',
            'selector' => '{{WRAPPER}} .gm-style .gm-style-iw-c',
            'condition' => [
                'infoWindow_click_to_post' => '',
            ],
                ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
                'section_mapMarker', [
            'label' => __('Marker', 'dynamic-content-for-elementor'),
                ]
        );
        /*$this->add_control(
                'acf_markerfield', [
            'label' => __('Map (ACF)', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'label_block' => true,
            'options' => DCE_Helper::get_acf_fields('image'),
            'default' => '0',
            'condition' => [
            ],
                ]
        );*/
        $this->add_control(
                'acf_markerfield',
                [
                    'label' => __('Map (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'image',
                ]
        );
        $this->add_control(
                'imageMarker', [
            'label' => __('Marker Image', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => '', //'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
            ],
            'frontend_available' => true,
            'condition' => [
                'acf_markerfield' => ['', null],
            ],
                ]
        );
        
        $this->add_control(
            'marker_width', [
                'label' => __('Force Width', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'frontend_available' => 'true',
            ]
        );
        $this->add_control(
            'marker_height', [
                'label' => __('Force Height', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'frontend_available' => 'true',
            ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'section_mapStyles', [
            'label' => __('Styles', 'dynamic-content-for-elementor'),
                ]
        );
        $this->add_control(
                'map_type', [
            'label' => __('Map Type', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'roadmap',
            'options' => [
                'roadmap' => __('Roadmap', 'dynamic-content-for-elementor'),
                'satellite' => __('Satellite', 'dynamic-content-for-elementor'),
                'hybrid' => __('Hybrid', 'dynamic-content-for-elementor'),
                'terrain' => __('Terrain', 'dynamic-content-for-elementor'),
            ],
            'frontend_available' => true,
                ]
        );
        // --------------------------------- [ ACF Type of style ]
        $this->add_control(
                'style_select', [
            'label' => __('Style', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => __('None', 'dynamic-content-for-elementor'),
                'custom' => __('Custom', 'dynamic-content-for-elementor'),
                'prestyle' => __('Snazzy Style', 'dynamic-content-for-elementor'),
            ],
            'frontend_available' => true,
            'condition' => [
                'map_type' => 'roadmap',
            ],
                ]
        );
        $this->add_control(
                'snazzy_select', [
            'label' => __('Snazzy Style', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT2,
            'options' => $this->snazzymaps(),
            'frontend_available' => true,
            'condition' => [
                'map_type' => 'roadmap',
                'style_select' => 'prestyle',
            ],
                ]
        );
        $this->add_control(
                'style_map', [
            'label' => __('Copy Snazzy Json Style Map', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => __('', 'dynamic-content-for-elementor'),
            'description' => 'To better manage the graphic styles of the map go to: <a href="https://snazzymaps.com/" target="_blank">snazzymaps.com</a>',
            'frontend_available' => true,
            'condition' => [
                'map_type' => 'roadmap',
                'style_select' => 'custom',
            ],
                ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'section_mapControls', [
            'label' => __('Controls', 'dynamic-content-for-elementor'),
                ]
        );
        $this->add_control(
                'maptypecontrol', [
            'label' => __('Map Type Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'pancontrol', [
            'label' => __('Pan Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );

        $this->add_control(
                'rotatecontrol', [
            'label' => __('Rotate Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'scalecontrol', [
            'label' => __('Scale Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'streetviewcontrol', [
            'label' => __('Street View Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'zoomcontrol', [
            'label' => __('Zoom Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'fullscreenControl', [
            'label' => __('Full Screen Control', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->add_control(
                'markerclustererControl', [
            'label' => __('Marker Clusterer', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'frontend_available' => true,
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'section_cpt', [
            'label' => __('Multilocations Query', 'dynamic-content-for-elementor'),
            'condition' => [
                'map_data_type' => 'acfmap',
                'acf_mapfield!' => ['', null],
                'use_query!' => '',
            ]
                ]
        );
        
        // --------------------------------- [ Query Type ]
        $this->add_control(
                'query_type', [
            'label' => __('Query Type', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'get_cpt' => [
                    'title' => 'Custom Post Type',
                    'icon' => 'fa fa-files-o',
                ],
                'acf_relations' => [
                    'title' => 'ACF Relations',
                    'icon' => 'fa fa-american-sign-language-interpreting',
                ],
                'acf_repeater' => [
                    'title' => 'ACF Repeater',
                    'icon' => 'fa fa-ellipsis-v',
                ],
                'specific_posts' => [
                    'title' => 'From Specific Post',
                    'icon' => 'fa fa-list-ul',
                ],
                
            ],
            'default' => 'get_cpt',
            'condition' => [
                'use_query' => 'yes',
            ],
                ]
        );
        // --------------------------------- [ Custom Post Type ]
        $this->add_control(
                'post_type', [
            'label' => __('Post Type', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => DCE_Helper::get_post_types(),
            'multiple' => true,
            'label_block' => true,
            'default' => 'post',
            'condition' => [
                'use_query' => 'yes',
                'query_type' => ['get_cpt'],
            ],
                ]
        );
        $this->add_control(
                'taxonomy', [
            'label' => __('Taxonomy', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => ['' => __('None', 'dynamic-content-for-elementor')] + get_taxonomies(array('public' => true)),
            'condition' => [
                'use_query' => 'yes',
                'query_type' => ['get_cpt'],
            ],
                ]
        );
        $this->add_control(
                'category', [
            'label' => __('Terms ID', 'dynamic-content-for-elementor'),
            'description' => __('Comma separated list of category ids', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::HIDDEN,
            'condition' => [
                'use_query' => 'yes',
                'query_type' => ['get_cpt'],
            ],
                ]
        );
        $this->add_control(
                'terms_current_post', [
            'label' => __('Use Dynamic Current Post Terms (Archive)', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'description' => __('Filter results by taxonomy terms associated to current post', 'dynamic-content-for-elementor'),
            'condition' => [
                'taxonomy!' => '',
                'query_type' => ['get_cpt', 'dynamic_mode'],
                'use_query' => 'yes',
            //'terms_from_acf' => ''
            ],
                ]
        );
        foreach ($taxonomies as $tkey => $atax) {
            if ($tkey) {
                $this->add_control(
                        'terms_' . $tkey, [
                    'label' => __('Terms', 'dynamic-content-for-elementor'), //.' '.$atax,
                    'type' => Controls_Manager::SELECT2,
                    //'groups' => \DynamicContentForElementor\DCE_Helper::get_taxonomies_terms(),
                    'options' => ['' => __('All', 'dynamic-content-for-elementor')] + \DynamicContentForElementor\DCE_Helper::get_taxonomy_terms($tkey), // + ['dce_current_post_terms' => __('Dynamic Current Post Terms', 'dynamic-content-for-elementor')],
                    'description' => __('Filter results by selected taxonomy term', 'dynamic-content-for-elementor'),
                    'multiple' => true,
                    'label_block' => true,
                    'condition' => [
                        'use_query' => 'yes',
                        'query_type' => ['get_cpt', 'dynamic_mode'],
                        'taxonomy' => $tkey,
                        'terms_current_post' => '',
                    ],
                    'render_type' => 'template',
                    'use_query' => 'yes',
                        ]
                );
            }
        }
        // --------------------------------- [ ACF relations ]
        /*$this->add_control(
                'acf_relationship', [
            'label' => __('Relations (ACF)', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => DCE_Helper::get_acf_fields('relationship'),
            'label_block' => true,
            'default' => '0',
            'condition' => [
                'use_query' => 'yes',
                'query_type' => 'acf_relations',
            ],
                ]
        );*/
        $this->add_control(
                'acf_relationship',
                [
                    'label' => __('Relations (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'relationship',
                    'condition' => [
                        'use_query' => 'yes',
                        'query_type' => 'acf_relations',
                    ],
                ]
        );
        // --------------------------------- [ ACF repeater ]
        /*$this->add_control(
            'acf_repeater', [
                'label' => __('Repeater (ACF)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => DCE_Helper::get_acf_fields('repeater'),
                'label_block' => true,
                'default' => '0',
                'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                ],
            ]
        );*/
        $this->add_control(
                'acf_repeater',
                [
                    'label' => __('Repeater (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'repeater',
                    'condition' => [
                        'use_query' => 'yes',
                        'query_type' => 'acf_repeater',
                    ],
                ]
        );
        /*$this->add_control(
            'acf_repeater_iwimage', [
                'label' => __('InfoWindow Image (ACF)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => DCE_Helper::get_acf_fields('image'),
                'label_block' => true,
                'separator' => 'before',
                'default' => '0',
                'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
            ]
        );*/
        $this->add_control(
                'acf_repeater_iwimage',
                [
                    'label' => __('InfoWindow Image (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'image',
                    'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
                ]
        );
        /*$this->add_control(
            'acf_repeater_iwtitle', [
                'label' => __('InfoWindow Title Repeater (ACF)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => DCE_Helper::get_acf_fields('text'),
                'label_block' => true,
                'default' => '0',
                'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
            ]
        );*/
        $this->add_control(
                'acf_repeater_iwtitle',
                [
                    'label' => __('InfoWindow Title Repeater (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'text',
                    'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
                ]
        );
        /*$this->add_control(
            'acf_repeater_iwcontent', [
                'label' => __('InfoWindow Content Repeater (ACF)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => DCE_Helper::get_acf_fields(['textarea','wysiwyg']),
                'label_block' => true,
                'default' => '0',
                'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
            ]
        );*/
        $this->add_control(
                'acf_repeater_iwcontent',
                [
                    'label' => __('InfoWindow Content Repeater (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'textarea,wysiwyg',
                    'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
                ]
        );
        /*$this->add_control(
            'acf_repeater_iwlink', [
                'label' => __('InfoWindow ReadMore Link Repeater (ACF)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => get_post_taxonomies( $post->ID ),
                'options' => DCE_Helper::get_acf_fields(['url','link','page_link']),
                'label_block' => true,
                'default' => '0',
                'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
            ]
        );*/
        $this->add_control(
                'acf_repeater_iwlink',
                [
                    'label' => __('InfoWindow ReadMore Link Repeater (ACF)', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'acf',
                    'object_type' => 'url,link,page_link',
                    'condition' => [
                    'use_query' => 'yes',
                    'query_type' => 'acf_repeater',
                    'enable_infoWindow!' => '',
                    'custom_infoWindow_render' => 'simple'
                ],
                ]
        );
        // --------------------------------- [ Specific Pages ]
        /* $this->add_control(
          'specific_pages', [
          'label' => __('Posts', 'dynamic-content-for-elementor'),
          'type' => Controls_Manager::SELECT2,
          'options' => DCE_Helper::get_all_posts(),
          'multiple' => true,
          'label_block' => true,
          'condition' => [
          'query_type' => 'specific_posts',
          ],
          ]
          ); */
        $this->add_control(
                'specific_pages',
                [
                    'label' => __('Posts', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Post Title', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'multiple' => true,
                    'label_block' => true,
                    'condition' => [
                        'use_query' => 'yes',
                        'query_type' => 'specific_posts',
                    ],
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'section_dce_settings', [
            'label' => __('Dynamic content', 'dynamic-content-for-elementor'),
            'tab' => Controls_Manager::TAB_SETTINGS,
                ]
        );
        $this->add_control(
                'data_source',
                [
                    'label' => __('Source', 'dynamic-content-for-elementor'),
                    'description' => __('Select the data source', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'label_on' => __('Same', 'dynamic-content-for-elementor'),
                    'label_off' => __('Other', 'dynamic-content-for-elementor'),
                    'return_value' => 'yes',
                ]
        );
        /* $this->add_control(
          'other_post_source', [
          'label' => __('Select from other source post', 'dynamic-content-for-elementor'),
          'type' => Controls_Manager::SELECT,
          'options' => DCE_Helper::get_all_posts(),
          'condition' => [
          'data_source' => '',
          ],
          ]
          ); */
        $this->add_control(
                'other_post_source',
                [
                    'label' => __('Select from other source post', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Post Title', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'posts',
                    'condition' => [
                        'data_source' => '',
                    ],
                ]
        );
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display(null, true);
        if (empty($settings))
            return;
        //
        //
        // ------------------------------------------
        $id_page = DCE_Helper::get_the_id($settings['other_post_source']);
        // ------------------------------------------
        //
        $zoom = $settings['zoom']['size'];
        if (!$zoom) {
            $zoom = 10;
        }

        //var_dump($settings['acf_markerfield']);
        //$imageMarker = get_field($settings['acf_markerfield'], $id_page);
        $imageMarker = '';
        
        // individuo se il campo acf selezionato non è un type repeater ....
        $is_repeater = false;
        $imageMarkerType = DCE_Helper::get_acf_field_post($settings['acf_markerfield']);

        if(isset($imageMarkerType->post_parent)){
            $field_settings = DCE_Helper::get_acf_field_settings($imageMarkerType->post_parent);

            if(isset($field_settings['type']) && $field_settings['type'] == 'repeater'){
                $is_repeater = true;
            }
        }
        //if(isset($field_settings['type'])) var_dump($field_settings['type']);
        // ...............
        
        if(!$is_repeater){
            $imageMarker = DCE_Helper::get_acf_field_value($settings['acf_markerfield'], $id_page);
            
            if (is_string($imageMarker)) {
                if (is_numeric($marker_img)) {
                    $imageSrc = wp_get_attachment_image_src($imageMarker, 'full');
                    $imageMarker = $imageSrc[0];
                    //echo 'uuu: '.$imageSrc;
                } else {
                    $imageSrc = $imageMarker;
                }
                //echo 'url: '.$imageMarker;
            } else if (is_numeric($imageMarker)) {
                //echo 'id: '.$imageMarker;
                $imageSrc = wp_get_attachment_image_src($imageMarker, 'full');
                $imageMarker = $imageSrc[0];
            } else if (is_array($imageMarker)) {
                //echo 'array: '.$imageMarker;
                $imageSrc = wp_get_attachment_image_src($imageMarker['ID'], 'full');
                $imageMarker = $imageSrc[0];
            }
        }
        if ($imageMarker == '') {
            $imageMarker = $settings['imageMarker']['url'];
        }

        //$imageMarker = $settings['imageMarker'];
        //var_dump( $imageMarker );
        $infoWindow_str = '';
        // se la infoWindow è abilitata..
        // Le possibilità: statico, da ACF singolo, da ACF Query
        if ($settings['enable_infoWindow']) {

            $infoWindow_str = $settings['custom_infoWindow_wysiwig'];
            /*switch ($settings['custom_infoWindow_style']) {
                case 'text_wysiwyg':
                    $infoWindow_str = $settings['custom_infoWindow_wysiwig'];
                    break;
                case 'text':
                    $infoWindow_str = $settings['custom_infoWindow'];
                    break;
                default:
                    $infoWindow_str = '';
            }*/
            $infoWindow_str = DCE_Helper::get_dynamic_value($infoWindow_str);
            $infoWindow_str = htmlspecialchars($infoWindow_str, ENT_QUOTES);

            /* if($settings['acf_mapfield']){

              }else{
              $infoWindow_str = $settings['address'];
              } */
        }

        if (!$settings['use_query']) {

            $map_data_type = $settings['map_data_type'];
            $indirizzo = $settings['address'];

            $lat = $settings['latitudine'];
            $lng = $settings['longitudine'];

            if ($settings['acf_mapfield']) {
                //$location = get_field($settings['acf_mapfield'], $id_page);
                $location = DCE_Helper::get_acf_field_value($settings['acf_mapfield'], $id_page);

                //$location = unserialize(get_post_meta( $id_page, $settings['acf_mapfield'], true ));
                //var_dump($location);
                //echo $id_page;
                if (!empty($location)) {
                    $indirizzo = $location['address'];
                    $lat = $location['lat'];
                    $lng = $location['lng'];
                }
            }
            //$indirizzo = $location['address'];
            //echo $indirizzo;
            /*
              echo $settings['height']['size'];
              echo $settings['zoom']['size'];
             */
            //$location['address'] = $indirizzo;
            //echo $indirizzo;
        } else {
            /* -------------------------- Query ------------------------ */
            // ARGS
            if ($settings['query_type'] == 'specific_posts') {
                $args = array(
                    'post_type' => 'any',
                    //'posts_per_page'    => -1,
                    'post__in' => $settings['specific_pages'],
                    'post_status' => 'publish',
                    'order_by' => 'post__in',
                );
                //acf_relationship
            } else if ($settings['query_type'] == 'acf_relations') {

                //$relations_ids = get_field($settings['acf_relationship'], $id_page, false);
                $relations_ids = DCE_Helper::get_acf_field_value($settings['acf_relationship'], $id_page, false);
                //var_dump($relations_ids);
                //$relations_ids = unserialize(get_post_meta( $id_page, $settings['acf_relationship'] ));
                if (!empty($relations_ids)) {

                    $args = array(
                        'post_type' => 'any',
                        'posts_per_page' => -1,
                        'post__in' => $relations_ids,
                        'post_status' => 'publish',
                        'orderby' => 'menu_order',
                    );
                }else{
                    $args = array();
                }

            } else if ($settings['query_type'] == 'acf_repeater') {

                //$relations_ids = get_field($settings['acf_relationship'], $id_page, false);
                //$relations_ids = DCE_Helper::get_acf_field_value($settings['acf_repeater'], $id_page, false);
                // REPEATER .........

                //var_dump($sub_f);
                // Ciclo il ripetitore e popolo l'array $repeaters_ids, per poterlo elaborare.
                if (have_rows($settings['acf_repeater'], $id_page)) {
                    //
                    $row_id = 0;
                    $sub_fields = DCE_Helper::get_acf_repeater_fields( $settings['acf_repeater'] );
                    $repeaters_ids = [];
                    if (!empty($sub_fields)) {
                        // loop through the rows of data
                        while ( have_rows($settings['acf_repeater'], $id_page ) ) {
                            the_row();
                            
                            //the_sub_field($settings['acf_mapfield']);
                            $fieldsMap = get_sub_field($settings['acf_mapfield']); 
                            $markerImg = get_sub_field($settings['acf_markerfield']);
                            $fieldIwTitle = get_sub_field($settings['acf_repeater_iwtitle']);
                            $fieldIwImage = get_sub_field($settings['acf_repeater_iwimage']);
                            $fieldIwContent = get_sub_field($settings['acf_repeater_iwcontent']);
                            $fieldIwLink = get_sub_field($settings['acf_repeater_iwlink']);
                            
                            // marker ---------
                            if(!empty($markerImg)){
                                if (is_string($markerImg)) {
                                    if (is_numeric($markerImg)) {
                                        $imageSrc = wp_get_attachment_image_src($markerImg, 'full');
                                        $markerImg = $imageSrc[0];
                                        //echo 'uuu: '.$imageSrc;
                                    } else {
                                        //echo 'url: '.$markerImg;
                                        $imageSrc = $markerImg;
                                    }
                                } else if (is_numeric($markerImg)) {
                                    //echo 'id: '.$markerImg;
                                    $imageSrc = wp_get_attachment_image_src($markerImg, 'full');
                                    $markerImg = $imageSrc[0];
                                } else if (is_array($markerImg)) {
                                    //echo 'array: '.$markerImg;
                                    $imageSrc = wp_get_attachment_image_src($markerImg['ID'], 'full');
                                    $markerImg = $imageSrc[0]; 
                                    
                                }
                            }
                            
                            if(empty($markerImg)){
                                $markerImg = $imageMarker;
                            }
                            
                            
                            foreach ($fieldsMap as $key => $acfvalue) {
                                //echo $key.' '.$acfvalue.'<br>';
                                $repeaters_ids[$row_id][$key] = $acfvalue;
                            }
                            $repeaters_ids[$row_id]['custommarker'] = $markerImg;
                            
                            $repeaters_ids[$row_id]['iwtitle'] = $fieldIwTitle;
                            $repeaters_ids[$row_id]['iwimage'] = $fieldIwImage;
                            $repeaters_ids[$row_id]['iwcontent'] = $fieldIwContent;
                            $repeaters_ids[$row_id]['iwlink'] = $fieldIwLink;
                            //var_dump($repeaters_ids[$row_id]);
                            $row_id++;
                            
                        };
                    }
                }



            } else if ($settings['query_type'] == 'get_cpt') {
                $terms_query = 'all';
                $taxquery = array();
                if ($settings['category'] != '') {
                    $terms_query = explode(',', $settings['category']);
                }
                if ($settings['terms_current_post']) {

                    if (is_single()) {
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => true));
                        //var_dump($terms_list);
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }

                    if (is_archive()) {
                        if (is_tax()) {
                            $queried_object = get_queried_object();
                            $terms_query = array($queried_object->term_id);
                        }
                    }
                }
                if (isset($settings['terms_' . $settings['taxonomy']]) && !empty($settings['terms_' . $settings['taxonomy']])) {
                    $terms_query = $settings['terms_' . $settings['taxonomy']];
                    // add current post terms id
                    $dce_key = array_search('dce_current_post_terms', $terms_query);
                    if ($dce_key !== false) {
                        //var_dump($dce_key);
                        unset($terms_query[$dce_key]);
                        $terms_list = wp_get_post_terms($id_page, $settings['taxonomy'], array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'all', 'hide_empty' => true));
                        if (!empty($terms_list)) {
                            $terms_query = array();
                            foreach ($terms_list as $akey => $aterm) {
                                if (!in_array($aterm->term_id, $terms_query)) {
                                    $terms_query[] = $aterm->term_id;
                                }
                            }
                        }
                    }
                }
                if ($settings['taxonomy'] != "")
                    $taxquery = array(
                        array(
                            'taxonomy' => $settings['taxonomy'],
                            'field' => 'id',
                            'terms' => $terms_query
                        )
                    );
                $args = array(
                    'post_type' => $settings['post_type'],
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'tax_query' => $taxquery,
                );
            }

            // if mi trovo in 'acf_repeater' typo di query ...
            if ($settings['query_type'] == 'acf_repeater') {
                // REPEATER FIELDS:
                // La mappa
                /*
                $indirizzo = $map_field['address'];
                $lat = $map_field['lat'];
                $lng = $map_field['lng'];
                */
                // Infowindow: Image, Title, Content


                // Marker

                // ciclo il ripetitore e ricavo lo script con address_list array che definisce i dati della mappa
                /*foreach ($repeaters_ids as $row_id => $row_fields) {
                        
                        var_dump($row_fields['lat']);
                    }*/
                ?>
                
                <script>
                var address_list_<?php echo $this->get_id(); ?> = [<?php
                    //
                    foreach ($repeaters_ids as $row_id => $row_fields) {


                        $indirizzo = $row_fields['address'];
                        $lat = $row_fields['lat'];
                        $lng = $row_fields['lng'];
                        
                        $postlink = $row_fields['iwlink'];
                        $postInfowindow = '';
                        $marker_img = $row_fields['custommarker'];;
                        
                        // infowindow ---------
                        if ($settings['custom_infoWindow_render'] == 'html') {
                            $postInfowindow = '<div class="dce-iw-textzone">' . DCE_Helper::get_dynamic_value($settings['infoWindow_query_html']) . '</div>';
                            $postInfowindow = preg_replace("/\r|\n/", "", $postInfowindow);
                        } else {
                            $postTitle = $row_fields['iwtitle'];

                            $postImage = '';
                            $postContent = '';
                            $postReadMore = '';

                            if ($settings['infowindow_query_show_title']) {
                                $postTitle = '<div class="dce-iw-title">' . $postTitle . '</div>';
                            }

                            // to do ... elaborare il size dell'immagine.........


                            if ($settings['infowindow_query_show_image']) {
                                if (!empty($row_fields['iwimage'])) {
                                    $postImage = $row_fields['iwimage'];
                                    if (is_string($postImage)) {
                                        if (is_numeric($postImage)) {
                                            $imageSrc = wp_get_attachment_image_src($postImage, 'full');
                                            $postImage = $imageSrc[0];
                                            //echo 'uuu: '.$imageSrc;
                                        } else {
                                            //echo 'url: '.$postImage;
                                            $imageSrc = $postImage;
                                        }
                                    } else if (is_numeric($postImage)) {
                                        //echo 'id: '.$postImage;
                                        $imageSrc = wp_get_attachment_image_src($postImage, 'full');
                                        $postImage = $imageSrc[0];
                                    } else if (is_array($postImage)) {
                                        //echo 'array: '.$postImage;
                                        $imageSrc = wp_get_attachment_image_src($postImage['ID'], 'full');
                                        $postImage = $imageSrc[0];
                                    }
                                
                                    if ($settings['infowindow_query_extendimage']) {
                                        $postImage = '<div class="dce-iw-image dce-iw-image-bg" style="background: url(' . $postImage . ') no-repeat center center; background-size: cover;"></div>';
                                    } else {
                                        $postImage = '<div class="dce-iw-image"><img src="' . $postImage . '" /></div>';
                                    }
                                }
                            }
                            if ($settings['infowindow_query_show_content']) {
                                
                                if (!empty($row_fields['iwcontent'])) {
                                    //$postContent = preg_replace( "/\r|\n/", "", $the_content );
                                    $postContent = '<div class="dce-iw-content">' . $row_fields['iwcontent'] . '</div>';
                                }
                            }
                            if ($settings['infowindow_query_show_readmore'] && !empty($postlink)) {
                                $postReadMore = '<div class="dce-iw-readmore-wrapper"><a href="' . $postlink . '" class="dce-iw-readmore-btn">' . __($settings['infowindow_query_readmore_text'], 'dynamic-content-for-elementor' . '_texts') . '</a></div>';
                            }
                            //$map_field = get_post_meta( $id_page, $settings['acf_mapfield'] );
                            //$marker_img = get_field($settings['acf_markerfield']);

                            //$marker_img = get_post_meta( $id_page, $settings['acf_markerfield'] );

                            $postInfowindow = $postImage . '<div class="dce-iw-textzone">' . $postTitle . $postContent . $postReadMore . '</div>';
                        }

                                
                        if ($row_id > 0) {
                            echo ', ';
                        }
                        echo '{"address":"' . $indirizzo . '",';
                        echo '"lat":"' . $lat . '",';
                        echo '"lng":"' . $lng . '",';
                        echo '"marker":"' . $marker_img . '",';
                        echo '"postLink":"' . $postlink . '",';
                        echo '"infoWindow": "' . strval(addslashes($postInfowindow)) . '"}';
                        
                    }  
                ?>];
                </script>

                <?php
            }else{
                // if in query_type e sono in tipo post. (query, relation, post)
                //
                //var_dump($args);
                // QUERY - RELATIONSHIP - POST
                if(isset($args) && count($args) ){

                    $p_query = new \WP_Query($args);
                    //echo $p_query->found_posts;
                    $counter = 0;
                    //var_dump($args);                    
                    if ($p_query->have_posts()) :
                        global $wp_query;
                        $original_queried_object = get_queried_object();
                        $original_queried_object_id = get_queried_object_id();
                        ?>
                        <script>
                        var address_list_<?php echo $this->get_id(); ?> = [<?php
                        while ($p_query->have_posts()) {
                            $p_query->the_post();
                            $wp_query->queried_object_id = $id_page = get_the_ID();
                            $wp_query->queried_object = get_post();

                            //$map_field = get_field($settings['acf_mapfield'], get_the_ID());
                            $map_field = DCE_Helper::get_acf_field_value($settings['acf_mapfield'], get_the_ID());

                            if (!empty($map_field)) {
                                //var_dump($map_field);
                                $indirizzo = $map_field['address'];
                                $lat = $map_field['lat'];
                                $lng = $map_field['lng'];
                                
                                // link to post ---------
                                $postlink = get_the_permalink($id_page);
                                
                                // infowindow ---------
                                if ($settings['custom_infoWindow_render'] == 'html') {
                                    $postInfowindow = '<div class="dce-iw-textzone">' . DCE_Helper::get_dynamic_value($settings['infoWindow_query_html']) . '</div>';
                                    $postInfowindow = preg_replace("/\r|\n/", "", $postInfowindow);
                                } else {
                                    $postTitle = get_the_title($id_page);

                                    $postImage = '';
                                    $postContent = '';
                                    $postReadMore = '';

                                    if ($settings['infowindow_query_show_title']) {
                                        $postTitle = '<div class="dce-iw-title">' . get_the_title($id_page) . '</div>';
                                    }

                                    // to do ... elaborare il size dell'immagine.........


                                    if ($settings['infowindow_query_show_image']) {
                                        if (!empty(get_the_post_thumbnail($id_page))) {
                                            if ($settings['infowindow_query_extendimage']) {
                                                $postImage = '<div class="dce-iw-image dce-iw-image-bg" style="background: url(' . get_the_post_thumbnail_url($id_page) . ') no-repeat center center; background-size: cover;"></div>';
                                            } else {
                                                $postImage = '<div class="dce-iw-image">' . get_the_post_thumbnail($id_page) . '</div>';
                                            }
                                        }
                                    }
                                    if ($settings['infowindow_query_show_content']) {
                                        $getpost = get_post($id_page); // specific post
                                        $the_content = apply_filters('the_content', $getpost->post_content);
                                        if (!empty($the_content)) {
                                            //$postContent = preg_replace( "/\r|\n/", "", $the_content );
                                            $postContent = '<div class="dce-iw-content">' . preg_replace("/\r|\n/", "", $getpost->post_content) . '</div>';
                                        }
                                    }
                                    if ($settings['infowindow_query_show_readmore']) {
                                        $postReadMore = '<div class="dce-iw-readmore-wrapper"><a href="' . $postlink . '" class="dce-iw-readmore-btn">' . __($settings['infowindow_query_readmore_text'], 'dynamic-content-for-elementor' . '_texts') . '</a></div>';
                                    }
                                    //$map_field = get_post_meta( $id_page, $settings['acf_mapfield'] );
                                    //$marker_img = get_field($settings['acf_markerfield']);
                                    
                                    //$marker_img = get_post_meta( $id_page, $settings['acf_markerfield'] );

                                    $postInfowindow = $postImage . '<div class="dce-iw-textzone">' . $postTitle . $postContent . $postReadMore . '</div>';
                                }
                                // marker ---------
                                $marker_img = DCE_Helper::get_acf_field_value($settings['acf_markerfield'], $id_page);
                                if (is_string($marker_img)) {
                                    if (is_numeric($marker_img)) {
                                        $imageSrc = wp_get_attachment_image_src($marker_img, 'full');
                                        $marker_img = $imageSrc[0];
                                        //echo 'uuu: '.$imageSrc;
                                    } else {
                                        //echo 'url: '.$marker_img;
                                        $imageSrc = $marker_img;
                                    }
                                } else if (is_numeric($marker_img)) {
                                    //echo 'id: '.$marker_img;
                                    $imageSrc = wp_get_attachment_image_src($marker_img, 'full');
                                    $marker_img = $imageSrc[0];
                                } else if (is_array($marker_img)) {
                                    //echo 'array: '.$marker_img;
                                    $imageSrc = wp_get_attachment_image_src($marker_img['ID'], 'full');
                                    $marker_img = $imageSrc[0];
                                }
                                if ($marker_img == '') {
                                    $marker_img = $imageMarker;
                                }
                                
                                if ($counter > 0) {
                                    echo ', ';
                                }
                                echo '{"address":"' . $indirizzo . '",';
                                echo '"lat":"' . $lat . '",';
                                echo '"lng":"' . $lng . '",';
                                echo '"marker":"' . $marker_img . '",';
                                echo '"postLink":"' . $postlink . '",';
                                echo '"infoWindow": "' . strval(addslashes($postInfowindow)) . '"}';
                                //var_dump($map_field);
                                $counter++;
                            }
                        }
                        ?>];
                        </script>
                        <?php
                        // Reset the post data to prevent conflicts with WP globals                        
                        $wp_query->queried_object = $original_queried_object;
                        $wp_query->queried_object_id = $original_queried_object_id;                        
                        wp_reset_postdata();
                    endif;
                }else{
                    ?>
                    <script>
                            var address_list_<?php echo $this->get_id(); ?> = [];
                    </script>
                <?php
                }
            } // end if not 'acf_repeater' ..cioè tutti i query_type che sono di tipo post

            /* ----------------------------------------------------------------- END Query */
        } // end if use_query
        //var_dump($counter);
        ?>

        <style>
            #el-wgt-map-<?php echo $this->get_id(); ?>{
                width: 100%;
                background-color: #ccc;
            }
        </style>
        <span id="debug" style="display: none;"></span>
        <?php //echo $settings['acf_mapfield']; ?>
        <div id='el-wgt-map-<?php echo $this->get_id(); ?>' 
             data-address='<?php echo $indirizzo; ?>' 
             data-lat='<?php echo $lat; ?>' 
             data-lng='<?php echo $lng; ?>' 
             data-zoom='<?php echo $zoom; ?>' 
             data-imgmarker='<?php echo $imageMarker; ?>' 
             data-infowindow='<?php echo $infoWindow_str ?>'
             >
        </div>
        <?php
    }

    protected function _content_template() {
        
    }

    protected function snazzymaps() {
        $snazzy_list = [];
        $snazzy_styles = glob(DCE_PATH . 'assets/maps_style/*.json');
        if (!empty($snazzy_styles)) {
            foreach ($snazzy_styles as $key => $value) {
                $snazzy_name = basename($value);
                $snazzy_name = str_replace('.json', '', $snazzy_name);
                $snazzy_name = str_replace('_', ' ', $snazzy_name);
                $snazzy_name = ucfirst($snazzy_name);
                $snazzy_url = str_replace('.json', '', $value);
                $snazzy_url = str_replace(DCE_PATH, DCE_URL, $snazzy_url);
                $snazzy_list[$snazzy_url] = $snazzy_name;
            }
        }
        // ciclo la cartellina maps_style in assets e ricavo la lista dei files ....
        //$snazzy_list[DCE_URL.'assets/maps_style/extra_light'] = 'Extra Light';
        //$snazzy_list[DCE_URL.'assets/maps_style/extra_black'] = 'Extra black';

        return $snazzy_list;
    }

}
