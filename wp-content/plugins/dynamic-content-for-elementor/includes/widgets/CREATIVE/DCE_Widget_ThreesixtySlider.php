<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Elementor Threesixty-Slider
 *
 * Elementor widget for Dynamic Content for Elementor
 *
 */

class DCE_Widget_ThreesixtySlider extends DCE_Widget_Prototype {

    public function get_name() {
        return 'dyncontel-threesixtyslider';
    }  
    static public function is_enabled() {
        return true;
    }
    public function get_title() {
        return __('Threesixty 360', 'dynamic-content-for-elementor');
    }
    public function get_description() {
      return __('Generate a rotation through a series of images', 'dynamic-content-for-elementor');
    }
    public function get_docs() {
        return 'https://www.dynamic.ooo/widget/threesixty-360/';
    }
    public function get_icon() {
        return 'icon-dyn-360';
    }
    public function get_script_depends() {
        return [ 'dce-threesixtyslider-lib', 'dce-threesixtyslider'];
    }
    public function get_style_depends() {
        return [ 'dce-threesixtySlider' ];
    }
    static public function get_position() {
        return 3;
    }
    protected function _register_controls() {
        $this->start_controls_section(
            'section_threesixtyslider', [
                'label' => __('ThreesixtySlider', 'dynamic-content-for-elementor'),
            ]
        );
        $this->add_responsive_control(
            'width', [
                'label' => __('Width', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 400,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'size_units' => [ 'px','%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                    ],
                    '%' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'render_type' => 'template',
                'selectors' => [
                    '{{WRAPPER}} .dce-threesixty' => 'max-width: {{SIZE}}{{UNIT}};', //'height: {{SIZE}}{{UNIT}};',
                    //'{{WRAPPER}} .dce-threesixty' => 'padding-bottom: {{SIZE}}{{UNIT}};'
                ],
                'frontend_available' => true,
            ]
        );
        /*$this->add_responsive_control(
            'height', [
                'label' => __('Height', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 400,
                    'unit' => 'px',
                ],
                
                'size_units' => [ 'px','%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    //'{{WRAPPER}} .dce-threesixty img' => 'width: {{SIZE}}{{UNIT}};', //'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .dce-threesixty' => 'padding-bottom: {{SIZE}}{{UNIT}};'
                ],
                'frontend_available' => true,
            ]
        );*/
        $pathimg = plugins_url('/assets/lib/threesixty-slider/imagesCube/', DCE__FILE__);
        $pathimg = str_replace(get_home_url(), '', $pathimg);
        $this->add_control(
            'pathimages', [
                'label' => __('Path images', 'dynamic-content-for-elementor'),
                'label_block' => true,
                'description' => __('The absolute path from root folder of the images for the 360.<br>The images in the folder must be called with the sequential number (ex: 1.png, or 1.svg or 1.jpg.So 2.jpg, 3.jpg, 4.jpg etc.)', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::TEXT,
                'default' => $pathimg,
                'placeholder' => '/wp-content/uploads/360/',
                'frontend_available' => true,
            ]
        );
        /*$this->add_control(
          'format_file',
          [
             'label'       => __( 'Format files', 'dynamic-content-for-elementor' ),
             'type' => Controls_Manager::SELECT,
             'default' => 'svg',
             'options' => [
                'png'  => __( 'PNG', 'dynamic-content-for-elementor' ),
                'jpg' => __( 'JPG', 'dynamic-content-for-elementor' ),
                'svg' => __( 'SVG', 'dynamic-content-for-elementor' ),
             ],
             'frontend_available' => true,
          ]
        );
        $this->add_control(
          'total_frame',
          [
             'label'   => __( 'Total frames', 'dynamic-content-for-elementor' ),
             'description'=> 'Total no. of image you have for slider',
             'type'    => Controls_Manager::NUMBER,
             'default' => 36,
             'min'     => 5,
             'max'     => 180,
             'step'    => 1,
             'frontend_available' => true,
          ]
        );
        $this->add_control(
          'end_frame',
          [
             'label'   => __( 'End frame', 'dynamic-content-for-elementor' ),
             'description'=> 'End frame for the auto spin animation',
             'type'    => Controls_Manager::NUMBER,
             'default' => 36,
             'min'     => 5,
             'max'     => 180,
             'step'    => 1,
             'frontend_available' => true,
          ]
        );*/
        
        /*$this->add_control(
          'current_frame',
          [
             'label'   => __( 'Current frame', 'dynamic-content-for-elementor' ),
             'description'=> 'This the start frame for auto spin',
             'type'    => Controls_Manager::NUMBER,
             'default' => 1,
             'min'     => 1,
             'max'     => 180,
             'step'    => 1,
             'frontend_available' => true,
          ]
        );*/
        $this->add_control(
            'navigation', [
                'label' => __('Navigation', 'dynamic-content-for-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'responsive', [
                'label' => __('Responsive', 'dynamic-content-for-elementor'),
                //'type' => Controls_Manager::SWITCHER,
                'type' => Controls_Manager::HIDDEN,
                'default' => 'yes',
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );

        $this->end_controls_section();
    }

    public function render() {
        $settings = $this->get_settings_for_display();
        
        //$path = $settings('pathimages'); //$this->countFolder();
        //$path = get_home_path().'/assets/lib/threesixty-slider/imagestest360/';
        //echo "Path: ".$path; // Return "Path: /var/www/htdocs/"
        //echo plugins_url('/assets/lib/threesixty-slider/imagestest360/', DCE__FILE__);
        $pathimages = $settings['pathimages'];        
        if (strpos($settings['pathimages'], get_home_url()) === false) {
            $settings['pathimages'] = get_home_url().$settings['pathimages'];
        } else {           
            $pathimages = str_replace(get_home_url(), '', $pathimages);
        }
        if (substr($pathimages, 0, 1) == '/') {
            $pathimages = substr($pathimages, 1);
        }
        $path = ABSPATH.$pathimages;
        //var_dump($path);
        $images = glob($path.'*.*');
        $settings['total_frame'] = 0;
        foreach ($images as $image) {
            $pieces = explode('.', $image);
            $ext = strtolower(array_pop($pieces));
            if (in_array($ext, array('svg', 'jpg', 'png'))) {
                $settings['total_frame']++;
                $settings['format_file'] = $ext;
            }
        } 
        $settings['end_frame'] = $settings['total_frame'];
        ?>
        <div class="threesixty dce-threesixty" 
            data-pathimages="<?php echo $settings['pathimages']; ?>" 
            data-format_file="<?php echo $settings['format_file']; ?>"
            data-total_frame="<?php echo $settings['total_frame']; ?>"
            data-end_frame="<?php echo $settings['end_frame']; ?>"
            >
            <div class="spinner">
                <span>0%</span>
            </div>
            <ol class="threesixty_images"></ol>
        </div>
        <?php
    }

    /*protected function _content_template() {
      ?>
      <div class="threesixty dce-threesixty">
          <div class="spinner">
            <span>0%</span>
          </div>
          <ol class="threesixty_images"></ol>
      </div>
      <?php
    }*/

}