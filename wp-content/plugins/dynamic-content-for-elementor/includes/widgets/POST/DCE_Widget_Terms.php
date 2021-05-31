<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use DynamicContentForElementor\DCE_Helper;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Elementor Posts-Terms
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */
class DCE_Widget_Terms extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-terms';
    }

    static public function is_enabled() {
        return true;
    }

    public function get_title() {
        return __('Terms & Taxonomy', 'dynamic-content-for-elementor');
    }

    public function get_description() {
        return __('Write a taxonomy for your article', 'dynamic-content-for-elementor');
    }

    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/terms-and-taxonomy/';
    }

    public function get_icon() {
        return 'icon-dyn-terms';
    }
    public function get_style_depends() {
        return [
            'dce-terms'
        ];
    }
    static public function get_position() {
        return 3;
    }

    protected function _register_controls() {

        $this->start_controls_section(
                'section_content', [
            'label' => __('Terms', 'dynamic-content-for-elementor'),
                ]
        );
        $this->add_control(
                'taxonomy', [
            'label' => __('Taxonomy', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            //'options' => get_post_taxonomies( $post->ID ),
            'options' => ['auto' => __('Dynamic', 'dynamic-content-for-elementor')] + get_taxonomies(array('public' => true)),
            'default' => 'category',
                ]
        );
        /*$this->add_control(
                'only_parent_terms', [
            'label' => __('Only parent terms', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => '',
            'label_on' => __('Yes', 'dynamic-content-for-elementor'),
            'label_off' => __('No', 'dynamic-content-for-elementor'),
            'return_value' => 'yes',
        );*/

        $this->add_control(
                'only_parent_terms', [
            'label' => __('Show only', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'both' => [
                    'title' => __('Both', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-tags',
                ],
                'yes' => [
                    'title' => __('Parents', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-sitemap',
                ],
                'children' => [
                    'title' => __('Children', 'dynamic-content-for-elementor'),
                    'icon' => 'fa fa-child',
                ]
            ],
            'toggle' => false,
            'default' => 'both',
                ]
        );

        $this->add_control(
                'html_tag', [
            'label' => __('HTML Tag', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::HIDDEN,
            'options' => [
                'h1' => __('H1', 'dynamic-content-for-elementor'),
                'h2' => __('H2', 'dynamic-content-for-elementor'),
                'h3' => __('H3', 'dynamic-content-for-elementor'),
                'h4' => __('H4', 'dynamic-content-for-elementor'),
                'h5' => __('H5', 'dynamic-content-for-elementor'),
                'h6' => __('H6', 'dynamic-content-for-elementor'),
                'p' => __('p', 'dynamic-content-for-elementor'),
                'div' => __('div', 'dynamic-content-for-elementor'),
                'span' => __('span', 'dynamic-content-for-elementor'),
            ],
            'default' => 'div',
                ]
        );
        $this->add_control(
                'separator', [
            'label' => __('Separator', 'dynamic-content-for-elementor'),
            //'description' => __('Separator caracters.','dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => ', ',
            'condition' => [
                    //'block_enable' => ''
                ]
            ]
        );
        $this->add_control(
                    'use_termdescription',
                    [
                        'label' => __('Show term description', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        'separator' => 'before',
                        'return_value' => 'yes',
                    ]
            );
        $this->add_control(
                    'heading_spaces',
                    [
                        'label' => __('Space', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
        $this->add_responsive_control(
                'space', [
            'label' => __('Separator Space', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .dce-separator' => 'padding: 0 {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'separator!' => '',
                //'block_enable' => ''
            ]
                ]
        );
        $this->add_responsive_control(
                'terms_space', [
            'label' => __('Items Horizontal Space', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .dce-terms ul li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
            ],
            
                ]
        );
        $this->add_responsive_control(
                'terms_space_vertical', [
            'label' => __('Items Vertical Space', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
                'unit' => 'px',
            ],
            'tablet_default' => [
                'unit' => 'px',
            ],
            'mobile_default' => [
                'unit' => 'px',
            ],
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 100,
                ]
            ],
            'selectors' => [
                '{{WRAPPER}} .dce-terms ul li' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
            ],
            
                ]
        );
        $this->add_control(
                'text_before', [
            'label' => __('Text before', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'separator' => 'before',
            'default' => '',
                ]
        );
        $this->add_control(
                'text_after', [
            'label' => __('Text after', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
                ]
        );
        $this->add_responsive_control(
                'align', [
            'label' => __('Block Alignment', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __('Left', 'dynamic-content-for-elementor'),
                    'icon' => 'eicon-h-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'dynamic-content-for-elementor'),
                    'icon' => 'eicon-h-align-center',
                ],
                'flex-end' => [
                    'title' => __('Right', 'dynamic-content-for-elementor'),
                    'icon' => 'eicon-h-align-right',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .dce-terms ul, {{WRAPPER}} .dce-terms ul.dce-image-block li' => 'justify-content: {{VALUE}};',
            ],
                ]
        );
        $this->add_responsive_control(
                'text_align', [
            'label' => __('Text Alignment', 'dynamic-content-for-elementor'),
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
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .dce-terms ul' => 'text-align: {{VALUE}};',
            ],
            'condition' => [
                        'use_termdescription!' => ''
                    ]
                ]
        );
        /*
        $this->add_responsive_control(
                'v_align_blocks', [
            'label' => __('Vertical Alignment (Flex)', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __('Top', 'dynamic-content-for-elementor'),
                    'icon' => 'eicon-v-align-top',
                ],
                'center' => [
                    'title' => __('Middle', 'dynamic-content-for-elementor'),
                    'icon' => 'eicon-v-align-middle',
                ],
                'flex-end' => [
                    'title' => __('Down', 'dynamic-content-for-elementor'),
                    'icon' => 'eicon-v-align-bottom',
                ],
            ],
            'separator' => 'after',
            'selectors' => [
                '{{WRAPPER}} .dce-wrapper' => 'display: flex; flex-direction: row; align-items: {{VALUE}};',
            ],
            'condition' => [
                'posts_style' => 'flexgrid',
                'image_position!' => 'alternate',
                'templatemode_enable' => '',
            ],
                ]
        );
        */
        $this->add_control(
                'link_to', [
            'label' => __('Link to', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'none',
            'options' => [
                'none' => __('None', 'dynamic-content-for-elementor'),
                'term' => __('Term', 'dynamic-content-for-elementor'),
            ],
                ]
        );

        $this->end_controls_section();

        if (DCE_Helper::is_plugin_active('acf')) {
            $this->start_controls_section(
                    'section_image', [
                'label' => __('Term', 'dynamic-content-for-elementor'),
                    ]
            );
            $this->add_control(
                    'heading_image_acf',
                    [
                        'label' => __('Image', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $this->add_control(
                    'image_acf_enable',
                    [
                        'label' => __('Enable', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        
                        'return_value' => 'yes',
                    ]
            );
            /*$this->add_control(
                    'acf_field_image', [
                'label' => __('Field Image', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => $this->get_acf_field(),
                'groups' => DCE_Helper::get_acf_fields('image',true,'Select acf flield Image'),
                'default' => '',
                'condition' => [
                    'image_acf_enable' => 'yes',
                ]
                    ]
            );*/
            $this->add_control(
                'acf_field_image',
                [
                    'label' => __('Image Field', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'term',
                    'condition' => [
                        'image_acf_enable!' => '',
                    ]
                ]
            );
            
            $this->add_group_control(
                    Group_Control_Image_Size::get_type(), [
                'name' => 'imgsize',
                'label' => __('Image Size', 'dynamic-content-for-elementor'),
                'default' => 'large',
                'render_type' => 'template',
                'condition' => [
                    'image_acf_enable' => 'yes',
                ]
                    ]
            );
            $this->add_control(
                    'block_enable', [
                'label' => __('Block', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'return_value' => 'block',
                'selectors' => [
                    '{{WRAPPER}} .dce-terms img' => 'display: {{VALUE}};',
                ],
                'render_type' => 'template',
                'condition' => [
                    'image_acf_enable' => 'yes',
                ],
                    ]
            );
            $this->add_responsive_control(
                'block_grid', [
                'label' => __('Columns', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'tablet_default' => '3',
                'mobile_default' => '1',
                'options' => [
                    ''  => 'Auto',
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7'
                ],
                //'frontend_available' => true,
                //'prefix_class' => 'columns-',
                //'render_type' => 'template',
                'selectors' => [
                    //'{{WRAPPER}} .dce-image-block li' => 'width: calc( 100% / {{VALUE}} );',
                    '{{WRAPPER}} .dce-image-block li' => 'flex: 0 1 calc( 100% / {{VALUE}} );',
                ],
                'condition' => [
                    'block_enable!' => '',
                ],
                    ]
            );

            $this->add_responsive_control(
                    'image_acf_size', [
                'label' => __('Size (Max-Width)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '',
                    'unit' => '%',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 800,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .dce-terms img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'image_acf_enable' => 'yes',
                ],
                    ]
            );
            
            $this->add_responsive_control(
                'image_acf_space', [
                    'label' => __('Shift X', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                        'unit' => 'px',
                    ],
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-terms img' => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'image_acf_enable' => 'yes',
                    ],
                ]
            );
            $this->add_responsive_control(
                'image_acf_shift', [
                    'label' => __('Shift Y', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                        'unit' => 'px',
                    ],
                    'size_units' => ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-terms img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'image_acf_enable' => 'yes',
                    ],
                ]
            );
            $this->add_control(
                    'heading_color_acf',
                    [
                        'label' => __('Color', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::HEADING,
                        'separator' => 'before',
                    ]
            );
            $this->add_control(
                    'color_acf_enable',
                    [
                        'label' => __('Enable', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SWITCHER,
                        
                        'return_value' => 'yes',
                    ]
            );
            /*$this->add_control(
                    'acf_field_color', [
                'label' => __('ACF Field Color', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SELECT,
                //'options' => $this->get_acf_field(),
                'groups' => DCE_Helper::get_acf_fields('color_picker',true,'Select Field Color'),
                'default' => '',
                'condition' => [
                    'color_acf_enable!' => '',
                ]
                    ]
            );
            $this->add_control(
                'acf_field_color_hover', [
                    'label' => __('ACF Field Color Hover', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SELECT,
                    //'options' => $this->get_acf_field(),
                    'groups' => DCE_Helper::get_acf_fields('color_picker',true,'Select Field Color'),
                    'default' => '',
                    'condition' => [
                        'color_acf_enable!' => '',
                        'acf_field_color!' => '',
                        'link_to!' => 'none',
                    ]
                ]
            );*/
            $this->add_control(
                    'acf_field_color', [
                'label' => __('ACF Field Color', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::HIDDEN,
                'condition' => [
                    'color_acf_enable!' => '',
                ]
                    ]
            );            
            $this->add_control(
                'acf_field_color_hover', [
                    'label' => __('ACF Field Color Hover', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HIDDEN,
                    'condition' => [
                        'color_acf_enable!' => '',
                        'acf_field_color!' => '',
                        'link_to!' => 'none',
                    ]
                ]
            );
            /*$this->add_control(
                    'acf_field_color_dyn', [
                'label' => __('Color', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'color_acf_enable!' => '',
                ],
            //'selectors' => [
            //    '{{WRAPPER}} .dce-term-item' => 'color: {{VALUE}};',
            //],
                    ]
            );*/
            $this->add_control(
                'acf_field_color_dyn',
                [
                    'label' => __('Color Field', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'term',
                    'condition' => [
                        'color_acf_enable!' => '',
                    ]
                ]
            );
            /*$this->add_control(
                    'acf_field_color_hover_dyn', [
                'label' => __('Color Hover', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'acf_field_color_dyn!' => '',
                ],
            //'selectors' => [
            //    '{{WRAPPER}} .dce-term-item:hover' => 'color: {{VALUE}};',
            //],
                    ]
            );*/
            $this->add_control(
                'acf_field_color_hover_dyn',
                [
                    'label' => __('Color Hover Field', 'dynamic-content-for-elementor'),
                    'type' => 'ooo_query',
                    'placeholder' => __('Select the Field', 'dynamic-content-for-elementor'),
                    'label_block' => true,
                    'query_type' => 'metas',
                    'object_type' => 'term',
                    'condition' => [
                        'color_acf_enable!' => '',
                        'acf_field_color_dyn!' => ['', null],
                    ]
                ]
            );

            $this->add_control(
                'acf_field_color_mode', [
                    'label' => __('Mode', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'text' => [
                            'title' => __('Text', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-font',
                        ],
                        'background' => [
                            'title' => __('Background', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-square',
                        ],
                        'border' => [
                            'title' => __('Border Bottom', 'dynamic-content-for-elementor'),
                            'icon' => 'fa fa-minus',
                        ],
                    ],
                    'toggle' => false,
                    'default' => 'text',
                    'condition' => [
                        'color_acf_enable!' => '',
                    ]
                ]
            );
            // .dce-terms .dce-term-item.dce-term-mode-background
            $this->add_responsive_control(
                'acf_field_colorbg_padding', [
                    'label' => __('Padding', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%', 'rem'],
                    'default' => [],
                    'selectors' => [
                        '{{WRAPPER}} .dce-term-item.dce-term-mode-background' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition' => [
                        'color_acf_enable!' => '',
                        'acf_field_color_mode' => ['background'],
                    ]
                ]
            );
            $this->add_control(
                'acf_field_colorborderradius_width',
                [
                    'label' => __('Radius', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => 1
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-term-item.dce-term-mode-background' => 'border-radius: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'color_acf_enable!' => '',
                        'acf_field_color_mode' => ['background'],
                    ]
                ]
            );
            $this->add_control(
                    'acf_field_colorborder_width',
                    [
                        'label' => __('Width', 'dynamic-content-for-elementor'),
                        'type' => Controls_Manager::SLIDER,
                        'default' => [
                            'size' => '',
                        ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 20,
                                'step' => 1
                            ],
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .dce-term-item.dce-term-mode-border' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'color_acf_enable!' => '',
                            'acf_field_color_mode' => ['border'],
                        ],
                    ]
            );

            $this->end_controls_section();
        }

        // ----------------------------------------- [STYLE]
        $this->start_controls_section(
                'section_style', [
            'label' => __('Terms', 'dynamic-content-for-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'color', [
            'label' => __('Text Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .dce-terms' => 'color: {{VALUE}};',
                '{{WRAPPER}} .dce-terms a' => 'color: {{VALUE}};',
            ],
                ]
        );
        $this->add_control(
                'color_hover', [
            'label' => __('Text Color Hover', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .dce-terms a:hover' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'link_to!' => 'none',
            ],
                ]
        );
        $this->add_control(
                'color_separator', [
            'label' => __('Separator color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .dce-separator' => 'color: {{VALUE}};',
            ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'typography',
            'label' => __('Typography', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .dce-terms .dce-term-item',
                ]
        );
        
        $this->add_control(
                'hover_animation', [
            'label' => __('Hover Animation', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::HOVER_ANIMATION,
            'condition' => [
                'link_to!' => 'none',
            ],
                ]
        );


        $this->add_control(
                'description_heading',
                [
                    'label' => __('Description', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'use_termdescription!' => ''
                    ]
                ]
        );
        $this->add_control(
                'decription_color', [
            'label' => __('Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .dce-terms .dce-term-description' => 'color: {{VALUE}};',
                '{{WRAPPER}} .dce-terms .dce-term-description a' => 'color: {{VALUE}};',
            ],
            'condition' => [
                        'use_termdescription!' => ''
                    ]
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => __('typography_description', 'dynamic-content-for-elementor'),
            'label' => __('Typography', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .dce-terms .dce-term-description',
            'condition' => [
                        'use_termdescription!' => ''
                    ]
                ]
        );
        $this->add_responsive_control(
                'decription_space', [
                    'label' => __('Space', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => '',
                        'unit' => 'px',
                    ],
                    'size_units' => ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .dce-terms .dce-term-description' => 'margin-top: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'use_termdescription!' => ''
                    ],
                ]
            );
        /* ------------------ Text Before ------------ */
        $this->add_control(
                'txbefore_heading',
                [
                    'label' => __('Text before', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'text_before!' => '',
                    ]
                ]
        );
        $this->add_control(
                'tx_before_color', [
            'label' => __('Text Before Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .dce-terms span.tx-before' => 'color: {{VALUE}};',
                '{{WRAPPER}} .dce-terms a span.tx-before' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'text_before!' => '',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'typography_tx_before',
            'label' => __('Font Before', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .dce-terms span.tx-before',
            'condition' => [
                'text_before!' => '',
            ]
                ]
        );



        /* ------------------ Text After ------------ */
        $this->add_control(
                'txafter_heading',
                [
                    'label' => __('Text after', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
                        'text_after!' => '',
                    ]
                ]
        );
        $this->add_control(
                'tx_after_color', [
            'label' => __('Text After Color', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .dce-terms span.tx-after' => 'color: {{VALUE}};',
                '{{WRAPPER}} .dce-terms a span.tx-after' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'text_after!' => '',
            ]
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(), [
            'name' => 'typography_tx_after',
            'label' => __('Font After', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .dce-terms span.tx-after',
            'condition' => [
                'text_after!' => '',
            ]
                ]
        );
        $this->end_controls_section();
        // ------------------------------------------------ SETTINGS 
        $this->start_controls_section(
                'section_dce_settings', [
            'label' => __('Dynamic Content', 'dynamic-content-for-elementor'),
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
                    'label_off' => __('other', 'dynamic-content-for-elementor'),
                    'return_value' => 'yes',
                ]
        );
        /* $this->add_control(
          'other_post_source', [
          'label' => __('Select from other source post', 'dynamic-content-for-elementor'),
          'type' => Controls_Manager::SELECT,

          'groups' => DCE_Helper::get_all_posts(get_the_ID(),true),
          'label_block' => true,
          'default' => '',
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
        /* $this->add_control(
          'go_to_page',
          [
          'type'    => Controls_Manager::RAW_HTML,
          'raw' => '<a target="_blank" class="dce-go-to-page-template dce-btn" href="#">
          <i class="fa fa-pencil"></i>'. __( 'Edit Page', 'dynamic-content-for-elementor' ).'</a>',
          'content_classes' => 'dce-btn-go-page',
          'separator' => 'after',
          //'render_type' => 'template',
          'condition' => [
          'other_post_source!' => '',
          ],
          ]
          ); */
        /* $this->add_control(
          'mod_page',
          [
          'type' => Controls_Manager::BUTTON,
          'label' => __( 'Modify', 'dynamic-content-for-elementor' ),
          'label_block' => true,
          'show_label' => false,
          'text' => __( 'View page', 'dynamic-content-for-elementor' ),
          'separator' => 'none',
          'event' => 'dceMain:previewPage',
          'condition' => [
          'other_post_source!' => 0,
          'data_source' => '',
          ],
          ]
          ); */
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        if (empty($settings))
            return;
        //
        // ------------------------------------------
        $id_page = DCE_Helper::get_the_id($settings['other_post_source']);
        // ------------------------------------------

        $taxonomy = $settings['taxonomy'];
        $taxonomyAuto = [];

        if (empty($taxonomy))
            return;

        if ($taxonomy == 'auto') {

            $taxonomyAuto = get_post_taxonomies($id_page);
        } else {

            $taxonomyAuto = $taxonomy;
        };
        $animation_class = !empty($settings['hover_animation']) ? 'elementor-animation-' . $settings['hover_animation'] : '';
        $html = '';
        if (is_array($taxonomyAuto)) {

            /* $term_list = array();
              foreach ( $taxonomyAuto as $taxo ) {
              echo $taxo;
              $autoTerms = get_the_terms( $id_page, $taxo );
              $tmpTerm = $term_list;
              foreach ( $autoTerms as $t ) {
              $term_list = array_push($term_list, $t);
              }
              //$term_list = array_merge($autoTerms, $tmpTerm);
              } */
            $term_list = \DynamicContentForElementor\DCE_Helper::get_the_terms_ordered($id_page, reset($taxonomyAuto));
        } else {
            $term_list = \DynamicContentForElementor\DCE_Helper::get_the_terms_ordered($id_page, $taxonomyAuto);
        }
        if (empty($term_list) || is_wp_error($term_list)) {
            if (is_admin()) {

                $html = '<div class="dce-terms '.$animation_class.'">';//sprintf('<%1$s class="dce-terms %2$s">', $settings['html_tag'], $animation_class);
                $html .= '<ul>';

                $html .= '<li><a href="#">Term</a><span class="dce-separator dce-term-item">' . $settings['separator'] . '</span></li>';
                $html .= '<li><a href="#">Term</a><span class="dce-separator dce-term-item">' . $settings['separator'] . '</span></li>';
                $html .= '<li><a href="#">Term</a></li>';

                $html .= '</ul>';
                $html .= '</div>'; //sprintf('</%s>', $settings['html_tag']);
                echo $html;
            }

            return;
        } else {
            //$html = sprintf( '<%1$s class="dce-terms %2$s">', $settings['html_tag'], $animation_class );


            $separator = '';
            $conta = 0;


            $html = '<div class="dce-terms '.$animation_class.'">'; //sprintf('<%1$s class="dce-terms %2$s">', $settings['html_tag'], $animation_class);


            if ($settings['text_before'] != "")
                $html .= '<span class="tx-before">' . __($settings['text_before'], 'dynamic-content-for-elementor_texts') . '</span>';
            //echo 'terms: ';
            $imageBlock_class = '';
            if(!empty($settings['block_enable'])){
                $imageBlock_class = ' class="dce-image-block"';
            }else{
                $imageBlock_class = ' class="dce-image-inline"';
            }
            $html .= '<ul'.$imageBlock_class.'>';
            foreach ($term_list as $term) {
                
                if (!empty($settings["only_parent_terms"])) {
                    if ($settings["only_parent_terms"] == 'yes') {
                        if ($term->parent) continue;
                    }
                    if ($settings["only_parent_terms"] == 'children') {
                        if (!$term->parent) continue;
                    }
                }
                // se il termina non ha genitore è il padre..
                //if ($settings["only_parent_terms"] || !$term->parent) {
                    //echo '->only:'.$settings["only_parent_terms"]  .' ->p:'. $term->parent;
                $color_str = '';
                $colorHover_str = '';
                $colorModeStr = '';
                $image_acf = '';
                $typeField = '';
                $imageSrc = '';


                $html .= '<li>';    
                    //
                    
                    if ($conta > 0) 
                        if($settings['separator'] != '') $html .= '<span class="dce-separator dce-term-item">' . $settings['separator'] . '</span>';

                    if (DCE_Helper::is_plugin_active('acf')) {
                        // inizio vuoto lo style in linea per gestire il colore ACF
                        
                        
                        // --------------------- Image ACF
                        if ($settings['image_acf_enable'] && $settings['acf_field_image']) {

                            //$imageField = get_field($settings['acf_field_image'], 'term_' . $term->term_id);
                            $imageField = get_term_meta($term->term_id, $settings['acf_field_image'], true);
                            //var_dump($imageField);
                            if ($imageField) {
                                //echo $typeField.': '.$imageField;
                                if (is_numeric($imageField)) {
                                    //echo 'id: '.$imageField;
                                    $typeField = 'image';
                                    $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField, 'imgsize', $settings);
                                } else if (is_string($imageField)) {
                                    //echo 'url: '.$imageField;
                                    $typeField = 'image_url';
                                    $imageSrc = $imageField;
                                } else if (is_array($imageField)) {
                                    //echo 'array: '.$imageField;
                                    $typeField = 'image_array';
                                    $imageSrc = Group_Control_Image_Size::get_attachment_image_src($imageField['ID'], 'imgsize', $settings);
                                }
                            }

                            

                            if (isset($imageSrc) && $imageSrc) {
                                $html .= '<span class="dce-term-wrap">';
                                $image_acf = '<img src="' . $imageSrc . '" />';
                                //
                                $html .= $image_acf;
                            }


                        }
                        // --------------------- Color ACF
                        if ($settings['color_acf_enable']) {
                            
                            $colorField_mode = $settings['acf_field_color_mode'];

                            // Normal Color
                            $colorField = false;
                            if ($settings['acf_field_color_dyn']) {
                                //$colorField = $settings['acf_field_color_dyn'];
                                $colorField = get_term_meta($term->term_id, $settings['acf_field_color_dyn'], true);
                            } else {
                                if ($settings['acf_field_color']) {
                                    $idField_color = $settings['acf_field_color'];
                                    $colorField = get_field($idField_color, 'term_' . $term->term_id);
                                }
                            }
                            //var_dump($colorField);
                            if( $colorField ){                               
                                if($colorField_mode == 'text'){
                                    $color_str = ' style="color:'.$colorField.';"';
                                    $colorModeStr = ' dce-term-mode-text';
                                }else if($colorField_mode == 'background'){
                                    $color_str = ' style="background-color:'.$colorField.';"';
                                    $colorModeStr = ' dce-term-mode-background';
                                }else if($colorField_mode == 'border'){
                                    $color_str = ' style="border-bottom-color:'.$colorField.';"';
                                    $colorModeStr = ' dce-term-mode-border';
                                }
                            }

                            // Hover Color
                            $colorField_hover = false;
                            if ($settings['acf_field_color_hover_dyn']) {
                                //$colorField_hover = $settings['acf_field_color_hover_dyn'];
                                $colorField_hover = get_term_meta($term->term_id, $settings['acf_field_color_hover_dyn'], true);
                            } else {
                                if ($settings['acf_field_color_hover']) {
                                    $idField_color_hover = $settings['acf_field_color_hover'];
                                    $colorField_hover = get_field($idField_color_hover, 'term_' . $term->term_id);
                                }
                            }
                            if( $colorField_hover ){
                                if($colorField_mode == 'text'){
                                    $colorHover_str = " onmouseover=\"this.style.color='".$colorField_hover."'\" onmouseout=\"this.style.color='".$colorField."'\"";
                                }else if($colorField_mode == 'background'){
                                    $colorHover_str = " onmouseover=\"this.style.background='".$colorField_hover."'\" onmouseout=\"this.style.background='".$colorField."'\"";
                                }else if($colorField_mode == 'border'){
                                    $colorHover_str = " onmouseover=\"this.style.borderBottomColor='".$colorField_hover."'\" onmouseout=\"this.style.borderBottomColor='".$colorField."'\"";
                                }
                            }
                        }
                    }
                    switch ($settings['link_to']) {
                        case 'term' :

                            $html .= sprintf('<a href="%1$s" class="dce-term-item term%3$s%5$s"%4$s%6$s>%2$s</a>', esc_url(get_term_link($term)), 
                                $term->name, 
                                $term->term_id, 
                                $color_str,
                                $colorModeStr,
                                $colorHover_str
                                );
                            $conta ++;

                            break;

                        case 'none' :
                        default:

                            $html .= sprintf('<span class="dce-term-item term%1$s%4$s"%3$s>%2$s</span>', 
                                $term->term_id, 
                                $term->name, 
                                $color_str,
                                $colorModeStr
                            );

                            $conta ++;

                            break;
                    }
                if( $settings['use_termdescription'] ){
                    $html .= '<div class="dce-term-description">'.term_description($term).'</div>';
                }
                if (isset($imageSrc) && $imageSrc) $html .= '</span>';  // end wrap with image (FLEX)
                
                $html .= '</li>';
            } 
            $html .= '</ul>';
            if ($settings['text_after'] != "")
                $html .= '<span class="tx-after">' . __($settings['text_after'], 'dynamic-content-for-elementor_texts') . '</span>';

            $html .= '</div>'; //sprintf('</%s>', $settings['html_tag']);
        }
        //$html = substr( $html, 0, -2);


        echo $html;
        //}
    }

    protected function _content_template() {
        //global $post;
        /*
          ?>
          <#
          var taxonomy = settings.taxonomy;

          var all_terms = [];
          <?php
          $taxonomies = get_taxonomies( array( 'public' => true ) );
          foreach ( $taxonomies as $taxonomy ) {
          printf( 'all_terms["%1$s"] = [];', $taxonomy );
          $terms = get_the_terms( $post->ID, $taxonomy );
          if ( $terms ) {
          $i = 0;
          foreach ( $terms as $term ) {
          printf( 'all_terms["%1$s"][%2$s] = [];', $taxonomy, $i );
          printf( 'all_terms["%1$s"][%2$s] = { slug: "%3$s", name: "%4$s", url: "%5$s" };', $taxonomy, $i, $term->slug, $term->name, esc_url( get_term_link( $term ) ) );
          $i++;
          }
          }
          }
          ?>
          var post_terms = all_terms[ settings.taxonomy ];

          var terms = '';
          var i = 0;

          switch( settings.link_to ) {
          case 'term':
          while ( all_terms[ settings.taxonomy ][i] ) {
          terms += "<a href='" + all_terms[ settings.taxonomy ][i].url + "'>" + all_terms[ settings.taxonomy ][i].name + "</a>, ";
          i++;
          }
          break;
          case 'none':
          default:
          while ( all_terms[ settings.taxonomy ][i] ) {
          terms += all_terms[ settings.taxonomy ][i].name + ", ";
          i++;
          }
          break;
          }
          terms = terms.slice(0, terms.length-2);

          var animation_class = '';
          if ( '' !== settings.hover_animation ) {
          animation_class = 'elementor-animation-' + settings.hover_animation;
          }

          var html = '<' + settings.html_tag + ' class="dce-terms ' + animation_class + '">';
          html += terms;
          html += '</' + settings.html_tag + '>';

          print( html );
          #>

          <?php
         */
    }

    

}