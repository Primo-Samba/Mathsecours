<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use DynamicContentForElementor\DCE_Helper;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Visibility extenstion
 *
 * Conditional Visibility Widgets & Rows/Sections
 *
 * @since 1.0.1
 */
class DCE_Extension_Video extends DCE_Extension_Prototype {

    public $name = 'Advanced Video Controls';
    public static $docs = 'https://www.dynamic.ooo/widget/advanced-video-controls/';
    private $is_common = false;
    public $has_action = false;

    static public function is_enabled() {
        return true;
    }

    public function get_docs() {
        return self::$docs;
    }

    public static function get_description() {
        return __('Advanced Video features for Video Widget', 'dynamic-content-for-elementor');
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
        return 'dce_video';
    }

    /**
     * Add Actions
     *
     * @since 0.5.5
     *
     * @access private
     */
    protected function add_actions() {

        add_action("elementor/frontend/section/before_render", function($element) {
            $settings = $element->get_settings_for_display();
            $frontend_settings = $element->get_frontend_settings();
            if (empty($frontend_settings['background_video_link']) && $settings['background_video_link']) {
                ob_start();
            }
        }, 10, 1);
        add_action("elementor/frontend/section/after_render", function($element) {
            $settings = $element->get_settings_for_display();
            $frontend_settings = $element->get_frontend_settings();
            if (empty($frontend_settings['background_video_link']) && $settings['background_video_link']) {
                /*$element->remove_render_attribute( '_wrapper', 'data-settings' );
                $frontend_settings['background_video_link'] = $settings['background_video_link'];
                $element->add_render_attribute( '_wrapper', 'data-settings', wp_json_encode( $frontend_settings ) );*/

                $content = ob_get_contents();
                ob_end_clean();
                if (strpos($content, 'background_video_link') === false) {
                    $content = str_replace('&quot;background_background&quot;:&quot;video&quot;', '&quot;background_background&quot;:&quot;video&quot;,&quot;background_video_link&quot;:&quot;'. wp_slash($settings['background_video_link']).'&quot;', $content);
                }
                
                echo $content;
            }
        }, 10, 1);

        add_action("elementor/widget/render_content", array($this, '_render_video'), 10, 2);

        add_action('elementor/element/video/section_video_style/before_section_start', [$this, 'add_control_section_to_video'], 10, 2);

        //@p
        add_action('elementor/element/video/section_video/before_section_end', function($element, $args) {

            // Make the video_type available in the frontend
            $element->update_control('video_type', array(
                'frontend_available' => true,
            ));
            $element->update_control('video_type', array(
                'frontend_available' => true,
            ));
            $element->update_control('autoplay', array(
                'frontend_available' => true,
            ));
            $element->update_control('mute', array(
                'frontend_available' => true,
            ));
            $element->update_control('loop', array(
                'frontend_available' => true,
            ));
            $element->update_control('lightbox', array(
                'frontend_available' => true,
            ));

            //$this->add_controls( $element, $args );
        }, 10, 2);

        /*
          add_action( 'elementor/widget/print_template', function( $template, $widget ) {
          if ( 'video' === $widget->get_name() ) {
          $template = false;
          }
          return $template;
          }, 10, 2 );
         */
    }

    public function add_control_section_to_video($element, $args) {
        //var_dump($element); die();
        $element->start_controls_section(
                'dce_video_section',
                [
                    'label' => __('Advanced', 'dynamic-content-for-elementor'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                    'condition' => [
                        'video_type' => ['youtube', 'vimeo', 'hosted'],
                    ]
                ]
        );

        $element->add_control(
                'dce_video_custom_controls',
                [
                    'label' => __('Use custom controls', 'dynamic-content-for-elementor'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'frontend_available' => true,
                    'render_type' => 'template',
                    'selectors' => [
                        '{{WRAPPER}} .plyr, #elementor-lightbox-{{ID}} .plyr' => 'height: auto;',
                        //body.elementor-kit-1217 input:focus:not([type="button"]):not([type="submit"]), body.elementor-kit-1217 textarea:focus, body.elementor-kit-1217 .elementor-field-textual:focus
                        '{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"]), #elementor-lightbox-{{ID}} input:focus:not([type="button"]):not([type="submit"])' => 'background-color: transprent; border: none; box-shadow: none;',
                        '{{WRAPPER}} .plyr input[type="range"]::-moz-range-track, #elementor-lightbox-{{ID}} .plyr input[type="range"]::-moz-range-track, {{WRAPPER}} .plyr input[type="range"]::-moz-range-thumb' => 'box-shadow: none;'
                    ],
                /* 'condition' => [
                  'controls!' => '',
                  ] */
                ]
        );
        
        $element->add_control(
                'dce_video_custom_controls_hover',
                [
                    'label' => __('Show Controls on Hover', 'dynamic-content-for-elementor'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'render_type' => 'template',
                    'selectors' => [
                        '{{WRAPPER}} .plyr:not(:hover) .plyr__controls, {{WRAPPER}} .plyr:not(:hover) .plyr__control' => 'opacity: 0; transition: 0.3s;',
                    ],
                    'condition' => [
                        'dce_video_custom_controls!' => ''
                    ]
                ]
        );
        
        $element->add_control(
                'dce_video_custom_controls_nodx',
                [
                    'label' => __('Prevent Video Download', 'dynamic-content-for-elementor'),
                    'type' => \Elementor\Controls_Manager::SWITCHER,
                    'render_type' => 'template',
                    'selectors' => [
                        '{{WRAPPER}} .plyr__video-wrapper:after' => 'content: ""; display: block; position: absolute; left: 0; top: 0; width: 100%; height: 100%;',
                    ],
                    'condition' => [
                        'dce_video_custom_controls!' => '',
                        'video_type' => 'hosted',
                    ]
                ]
        );
        
        
        /* $element->add_control(
          'acf_text_before', [
          'label' => __('Custom title', 'dynamic-content-for-elementor'),
          'type' => Controls_Manager::TEXT,
          'default' => '',
          ]
          ); */
        $element->add_control(
                'dce_video_controls', [
            'label' => __('Show controls:', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'separator' => 'before',
            'label_block' => true,
            'options' => [
                'play-large' => __('play-large', 'dynamic-content-for-elementor'), // The large play button in the center
                'restart' => __('restart', 'dynamic-content-for-elementor'), // Restart playback
                'rewind' => __('rewind', 'dynamic-content-for-elementor'), // Rewind by the seek time (default 10 seconds)
                'play' => __('play', 'dynamic-content-for-elementor'), // Play/pause playback
                'fast-forward' => __('fast-forward', 'dynamic-content-for-elementor'), // Fast forward by the seek time (default 10 seconds)
                'progress' => __('progress', 'dynamic-content-for-elementor'), // The progress bar and scrubber for playback and buffering
                'current-time' => __('current-time', 'dynamic-content-for-elementor'), // The current time of playback
                'duration' => __('duration', 'dynamic-content-for-elementor'), // The full duration of the media
                'mute' => __('mute', 'dynamic-content-for-elementor'), // Toggle mute
                'volume' => __('volume', 'dynamic-content-for-elementor'), // Volume control
                'captions' => __('captions', 'dynamic-content-for-elementor'), // Toggle captions
                'settings' => __('settings', 'dynamic-content-for-elementor'), // Settings menu
                'pip' => __('pip', 'dynamic-content-for-elementor'), // Picture-in-picture (currently Safari only)
                'airplay' => __('airplay', 'dynamic-content-for-elementor'), // Airplay (currently Safari only)
                'download' => __('download', 'dynamic-content-for-elementor'), // Show a download button with a link to either the current source or a custom URL you specify in your options
                'fullscreen' => __('fullscreen', 'dynamic-content-for-elementor'), // Toggle fullscreen
            ],
            'default' => ['mute', 'play-large', 'play', 'progress', 'current-time', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
            'frontend_available' => true,
            'condition' => [
                'dce_video_custom_controls!' => ''
            ]
                ]
        );

        $element->end_controls_section();

        // --------------------------------- STYLE
        $element->start_controls_section(
                'dce_video_style',
                [
                    'label' => __('Custom controls', 'dynamic-content-for-elementor'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                    'condition' => [
                        'dce_video_custom_controls!' => '',
                    //'controls!' => '',
                    ]
                ]
        );
        $element->add_control(
                'dce_video_color',
                [
                    'label' => __('Controls', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    /* 'condition' => [
                      'video_type!' => 'vimeo',
                      ], */
                    'selectors' => [
                        '{{WRAPPER}} .plyr--video .plyr__control_item svg, #elementor-lightbox-{{ID}} .plyr--video .plyr__control_item svg' => 'fill: {{VALUE}}',
                        '{{WRAPPER}} .plyr--video .plyr__controls, #elementor-lightbox-{{ID}} .plyr--video .plyr__controls' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .plyr--video .plyr__progress__buffer, #elementor-lightbox-{{ID}} .plyr--video .plyr__progress__buffer, {{WRAPPER}} .plyr--video .plyr__control--overlaid, #elementor-lightbox-{{ID}} .plyr--video .plyr__control--overlaid' => 'background-color: {{VALUE}}',
                    //'{{WRAPPER}} .plyr--full-ui.plyr--video input[type="range"]::-moz-range-track' => 'box-shadow: 0 0 0 5px {{VALUE}};'
                    ]
                ]
        );
        $element->add_control(
                'dce_video_bgcolor',
                [
                    'label' => __('Backgrounds buttons Controls', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    /* 'condition' => [
                      'video_type!' => 'vimeo',
                      ], */
                    'selectors' => [
                        '{{WRAPPER}} .plyr--video .plyr__controls .plyr__control, #elementor-lightbox-{{ID}} .plyr--video .plyr__controls .plyr__control' => 'background-color: {{VALUE}}',
                    //'{{WRAPPER}} .plyr--full-ui.plyr--video input[type="range"]::-moz-range-track' => 'box-shadow: 0 0 0 5px {{VALUE}};'
                    ]
                ]
        );
        $element->add_control(
                'dce_video_color_hover',
                [
                    'label' => __('Controls Color Hover', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .plyr--video .plyr__controls:hover, #elementor-lightbox-{{ID}} .plyr--video .plyr__controls:hover' => 'color: {{VALUE}}',
                        '{{WRAPPER}} .plyr--video .plyr__control.plyr__tab-focus, #elementor-lightbox-{{ID}} .plyr--video .plyr__control.plyr__tab-focus, {{WRAPPER}} .plyr--video .plyr__control:hover, #elementor-lightbox-{{ID}} .plyr--video .plyr__control:hover, {{WRAPPER}} .plyr--video .plyr__control[aria-expanded="true"], #elementor-lightbox-{{ID}} .plyr--video .plyr__control[aria-expanded="true"]' => 'background-color: {{VALUE}}'
                    ]
                ]
        );

        $element->add_control(
                'dce_video_progress_color',
                [
                    'label' => __('Progress Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .plyr--full-ui input[type="range"], #elementor-lightbox-{{ID}} .plyr--full-ui input[type="range"]' => 'color: {{VALUE}}',
                    ]
                ]
        );
        $element->add_control(
                'dce_video_controlsbackground_color',
                [
                    'label' => __('Controls Background Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .plyr--video .plyr__controls, #elementor-lightbox-{{ID}}' => 'background: {{VALUE}}',
                    ]
                ]
        );
        $element->add_responsive_control(
                'dce_video_videostyle_border', [
            'label' => __('Controls Padding', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', 'em', '%'],
            'selectors' => [
                '{{WRAPPER}} .plyr--video .plyr__controls, #elementor-lightbox-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
                ]
        );
        // ------------------------------- PLAY CONTROL
        $element->add_control(
                'dce_video_play_heading',
                [
                    'label' => __('Play Control', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $element->add_control(
                'dce_video_play_color',
                [
                    'label' => __('Background Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .plyr--video .plyr__control.plyr__control--overlaid' => 'background-color: {{VALUE}}',
                    ]
                ]
        );
        $element->add_control(
                'dce_video_play_icon_color',
                [
                    'label' => __('Icon Color', 'elementor'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .plyr__control.plyr__control--overlaid svg' => 'fill: {{VALUE}}',
                    ]
                ]
        );
        $element->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'dce_video_play_border',
            'label' => __('Border', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .plyr__control.plyr__control--overlaid',
                ]
        );
        $element->add_responsive_control(
                'dce_video_play_size', [
            'label' => __('Size', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 10,
                    'max' => 140,
                    'step' => 1,
                ]
            ],
            'render_type' => 'ui',
            'selectors' => [
                '{{WRAPPER}} .plyr__control.plyr__control--overlaid svg' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                '{{WRAPPER}} .plyr__control--overlaid' => 'padding: {{SIZE}}{{UNIT}} !important; display: block;'
            ],
                ]
        );
        $element->add_responsive_control(
                'dce_video_play_radius', [
            'label' => __('Radius', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 140,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ]
            ],
            'render_type' => 'ui',
            'selectors' => [
                '{{WRAPPER}} .plyr__control.plyr__control--overlaid' => 'border-radius: {{SIZE}}{{UNIT}};'
            ],
                ]
        );
        //iconUrl svg
        //seekTime s
        //volume 0-1
        // ------------------------------- PLAY CONTROL
        $element->add_control(
                'dce_video_videostyle_heading',
                [
                    'label' => __('Video style', 'dynamic-content-for-elementor'),
                    'type' => Controls_Manager::HEADING,
                    'separator' => 'before',
                ]
        );
        $element->add_control(
            'dce_video_videostyle_bgcolor',
            [
                'label' => __( 'Background color', 'elementor' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                /*'condition' => [
                    'video_type!' => 'vimeo',
                ],*/
                'selectors' => [
                    '{{WRAPPER}} .plyr--video' => 'background-color: {{VALUE}}',
                ]
            ]
        );
        $element->add_responsive_control(
                'dce_video_videostyle_radius', [
            'label' => __('Radius', 'dynamic-content-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => '',
            ],
            'size_units' => ['px', '%'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 140,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ]
            ],
            'render_type' => 'ui',
            'selectors' => [
                '{{WRAPPER}} .plyr' => 'border-radius: {{SIZE}}{{UNIT}};'
            ],
                ]
        );
        $element->add_group_control(
                Group_Control_Box_Shadow::get_type(), [
            'name' => 'dce_video_videostyle_shadow',
            'selector' => '{{WRAPPER}} .plyr',
                ]
        );
        $element->add_group_control(
                Group_Control_Border::get_type(), [
            'name' => 'dce_video_videostyle_border',
            'label' => __('Border', 'dynamic-content-for-elementor'),
            'selector' => '{{WRAPPER}} .plyr',
                ]
        );

        $element->end_controls_section();
    }

    /*
      public static function _add_to_video(\Elementor\Widget_Video $element, $control_id, $control_data, $options = []) {
      if ($element->get_name() == 'video' && $control_id == 'play_icon_color') {
      $control_data["selectors"]["{{WRAPPER}} .plyr__control--overlaid"] =  "color: {{VALUE}}";
      }
      return $control_data;
      }
     */

    public function get_script_depends() {
        return ['plyr', 'dce-advancedvideo'];
    }

    public function get_style_depends() {
        return ['plyr'];
    }

    public function _render_video($content, $widget) {
        if ($widget->get_name() == 'video') {
            $settings = $widget->get_settings();
            //var_dump($settings['dce_video_custom_controls']);
            if ($settings['dce_video_custom_controls']) {
                wp_enqueue_script('plyr');
                wp_enqueue_script('dce-advancedvideo');
                wp_enqueue_style('plyr');

                /* $video_container = '.elementor-element-'.$widget->get_id();
                  $video_selector = $video_container.' .elementor-wrapper';
                  if ($settings['video_type'] == 'hosted') {
                  $video_selector .= ' .elementor-video';
                  }

                  if ($settings['lightbox']) {
                  $video_container = '#elementor-lightbox-'.$widget->get_id();
                  }
                  if ($settings['video_type'] == 'hosted') {
                  $video_selector = $video_container.' video';
                  } else {
                  $video_selector = $video_container.' .iframe-wrapper';
                  }

                  // add custom js
                  ob_start();
                  ?>
                  <script>
                  jQuery(document).ready(function () {
                  //alert('<?php echo $video_selector; ?>');
                  <?php if ($settings['show_image_overlay']) { ?>jQuery('.elementor-element-<?php echo $widget->get_id(); ?> .elementor-custom-embed-image-overlay').on('click', function(){<?php } ?>
                  //alert('<?php echo $video_selector; ?>');
                  <?php if ($settings['lightbox']) { ?>
                  setTimeout(function(){
                  <?php } ?>
                  <?php if ($settings['video_type'] != 'hosted') { ?>
                  jQuery('<?php echo $video_container; ?> iframe').wrap('<div class="iframe-wrapper"></div>');
                  <?php } ?>
                  const player_<?php echo $widget->get_id(); ?> = new Plyr('<?php echo $video_selector; ?>',
                  {
                  <?php if ($settings['autoplay']) { ?>'autoplay': true,<?php } ?>
                  <?php if ($settings['mute']) { ?>'muted': true,<?php } ?>
                  <?php if ($settings['loop']) { ?>'loop': { active: true },<?php } ?>
                  }
                  );
                  <?php if ($settings['lightbox']) { ?>
                  }, 100);
                  <?php } ?>
                  <?php if ($settings['show_image_overlay']) { ?>});<?php } ?>
                  });
                  </script>
                  <?php
                  $js = ob_get_clean();
                  //var_dump($js); die();
                  //wp_add_inline_script('elementor-pro-frontend', $js);
                  $js = \DynamicContentForElementor\DCE_Assets::dce_enqueue_script($this->get_name().'-'.$widget->get_id(), $js);

                  $css = '';
                  if (!$settings['controls']) {
                  $css .= $video_container.' .plyr--video .plyr__controls, '.$video_container.' .plyr--video .plyr__control { visibility: hidden !important; }';
                  }

                  //if ($settings['video_type'] != 'youtube' || $settings['lightbox']) {
                  $css .= $video_container.' .plyr { height: auto; }';
                  //}


                  $control_color = ($settings['video_type'] == 'vimeo') ? $settings['color'] : $settings['dce_video_color'];
                  if (!empty($control_color)) {
                  $css .= $video_container.' .plyr__control svg { fill: '.$control_color.'; }'
                  . $video_container.' .plyr--video .plyr__controls { color: '.$control_color.'; }';
                  }
                  if (!empty($settings['dce_video_color_hover'])) {
                  $css .= $video_container.' .plyr--video .plyr__controls:hover { color: '.$settings['dce_video_color_hover'].'; }';
                  $css .= $video_container.' .plyr--video .plyr__control.plyr__tab-focus, '.$video_container.' .plyr--video .plyr__control:hover, '.$video_container.' .plyr--video .plyr__control[aria-expanded="true"] { background: '.$settings['dce_video_color_hover'].'; }';
                  }
                  if (!empty($settings['dce_video_play_icon_color'])) {
                  $css .= $video_container.' .plyr__control--overlaid { background: '.$settings['dce_video_play_icon_color'].'; }';
                  $dce_video_play_icon_color_hover = $settings['dce_video_play_icon_color'];
                  if (substr($settings['dce_video_play_icon_color'],0,1) != '#') {
                  $tmp = explode(',', $dce_video_play_icon_color_hover);
                  array_pop($tmp);
                  $dce_video_play_icon_color_hover = implode(',', $tmp).')';
                  }
                  $css .= $video_container.' .plyr__control.plyr__control--overlaid:hover { background: '.$dce_video_play_icon_color_hover.'; }';
                  }
                  if (!empty($settings['dce_video_progress_color'])) {
                  $css .= $video_container.' .plyr--full-ui input[type="range"] { color: '.$settings['dce_video_progress_color'].'; }';
                  }

                  if ($css) {
                  $css = '<style>'.$css.'</style>';
                  } */
                //$content .= $js.$css;
            }
        }
        return $content;
    }

}
