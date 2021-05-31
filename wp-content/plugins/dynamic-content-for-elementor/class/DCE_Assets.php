<?php

namespace DynamicContentForElementor;

use MatthiasMullie\Minify;
use Elementor\Core\Files\CSS;

/**
 * Main Plugin Class
 *
 * Register new elementor widget.
 *
 * @since 0.0.1
 */
class DCE_Assets {

    public static $dce_styles = [];
    public static $dce_scripts = [];
    public static $styles = array(
        // -----------------------------------------------------------
        // Global Settings CSS
        // ...spostato in wp_enqueue_script
        // -----------------------------------------------------------
        'dce-style' => '/assets/css/style.css',
        //'dce-style-base' => '/assets/css/base.css',
        'dce-animations' => '/assets/css/dce-animations.css',
        //ACF
        'dce-acf' => '/assets/css/elements-acf.css',
        'dce-acfRelationship' => '/assets/css/elements-acfRelationship.css',
        'dce-acfSlider' => '/assets/css/elements-acfSlider.css',
        'dce-acfGallery' => '/assets/css/elements-acfGallery.css',
        'dce-acfRepeater' => '/assets/css/elements-acfRepeater.css',
        'dce-acfGooglemap' => '/assets/css/elements-googleMap.css',
        //PODS
        'dce-pods' => '/assets/css/elements-pods.css',
        'dce-pods-gallery' => '/assets/css/dce-pods-gallery.css',
        //DYNAMICPOSTS
        'dce-dynamicPosts' => '/assets/css/elements-dynamicPosts.css',
        'dce-dynamicPosts_slick' => '/assets/css/elements-dynamicPosts_slick.css',
        'dce-dynamicPosts_swiper' => '/assets/css/elements-dynamicPosts_swiper.css',
        'dce-dynamicPosts_timeline' => '/assets/css/elements-dynamicPosts_timeline.css',
        //DYNAMICUSERS
        'dce-dynamicUsers' => '/assets/css/elements-dynamicUsers.css',
        //POST widgets
        'dce-iconFormat' => '/assets/css/elements-iconFormat.css',
        'dce-nextPrev' => '/assets/css/elements-nextPrev.css',
        'dce-list' => '/assets/css/elements-list.css',
        'dce-featuredImage' => '/assets/css/elements-featuredImage.css',
        //POPUP & fire-MODAL
        'dce-modalWindow' => '/assets/css/elements-modalWindow.css',
        'dce-modal' => '/assets/css/dce-modal.css',
        //DOCUMENT
        'dce-pageScroll' => '/assets/css/elements-pageScroll.css',
        // REVEAL
        'dce-reveal' => '/assets/css/dce-reveal.css',
        //CREATIVE
        'dce-threesixtySlider' => '/assets/css/elements-threesixtySlider.css',
        'dce-twentytwenty' => '/assets/css/elements-twentytwenty.css',
        'dce-parallax' => '/assets/css/elements-parallax.css',
        'dce-filebrowser' => '/assets/css/elements-filebrowser.css',
        'dce-animatetext' => '/assets/css/elements-animateText.css',
        // WebGL
        'dce-imagesDistortion' => '/assets/css/elements-webglDistortionImage.css',
        //WOOCOMMERCE (todo)
        'dce-woocommerce' => '/assets/css/dce-woocommerce.css',
        //INTERFACE
        'dce-animatedOffcanvasMenu' => '/assets/css/dce-animatedoffcanvasmenu.css',
        'dce-cursorTracker' => '/assets/css/dce-cursorTracker.css',
        //..TODO
        'dce-bubbles' => '/assets/css/elements-bubbles.css',
        //'dce-fullpage' => '/assets/css/elements-fullpage.css',
        //'dce-pagePiling' => '/assets/css/elements-pagePiling.css',
        //'dce-posterSlider' => '/assets/css/elements-posterSlider.css',
        //'dce-swiper' => '/assets/css/elements-swiper.css',
        //'dce-dualView' => '/assets/css/elements-dualView.css',
        // aggiunti 1.8.8 -------------------
        'dce-acfRelationship' => '/assets/css/elements-acfRelationship.css',
        'dce-title' => '/assets/css/elements-title.css',
        'dce-breadcrumbs' => '/assets/css/elements-breadcrumbs.css',
        'dce-date' => '/assets/css/elements-date.css',
        'dce-addtofavorites' => '/assets/css/elements-addToFavorites.css',
        'dce-terms' => '/assets/css/elements-terms.css',
        'dce-content' => '/assets/css/elements-content.css',
        'dce-excerpt' => '/assets/css/elements-excerpt.css',
        'dce-readmore' => '/assets/css/elements-readmore.css',
        'dce-bgCanvas' => '/assets/css/elements-webglBgCanvas.css',
        'dce-svg' => '/assets/css/elements-svg.css',
            // ----------------------------------
    );
    public static $vendorsCss = array(
        'dce-photoSwipe_default' => '/assets/lib/photoSwipe/photoswipe.min.css',
        'dce-photoSwipe_skin' => '/assets/lib/photoSwipe/default-skin/default-skin.min.css',
        'dce-justifiedGallery' => '/assets/lib/justifiedGallery/css/justifiedGallery.min.css',
        'dce-file-icon' => '/assets/lib/file-icon/file-icon-vivid.min.css',
        'animatecss' => '/assets/lib/animate/animate.min.css',
        'datatables' => '/assets/lib/datatables/datatables.min.css',
        'plyr' => '/assets/lib/plyr/plyr.css',
            //'dce-tooltip' => '/assets/css/dce-tooltip.css',
    );
    public static $minifyCss = 'css/dce-frontend.min.css';
    public static $vendorsJs = array(
        'datatables' => '/assets/lib/datatables/datatables.min.js',
        'plyr' => '/assets/lib/plyr/plyr.min.js',
        // -----------------------------------------------------------
        // Widgets Libs
        'wow' => '/assets/lib/wow/wow.min.js',
        'isotope' => '/assets/lib/isotope/isotope.pkgd.min.js',
        'infinitescroll' => '/assets/lib/infiniteScroll/infinite-scroll.pkgd.min.js',
        //'imagesloaded' => '/assets/lib/imagesLoaded/imagesloaded.pkgd.min.js',
        'jquery-slick' => '/assets/lib/slick/slick.min.js',
        //'jquery-swiper' => '/assets/lib/swiper/js/swiper.min.js',
        'velocity' => '/assets/lib/velocity/velocity.min.js',
        'velocity-ui' => '/assets/lib/velocity/velocity.ui.min.js',
        'diamonds' => '/assets/lib/diamonds/jquery.diamonds.js',
        'homeycombs' => '/assets/lib/homeycombs/jquery.homeycombs.js',
        'photoswipe' => '/assets/lib/photoSwipe/photoswipe.min.js',
        'photoswipe-ui' => '/assets/lib/photoSwipe/photoswipe-ui-default.min.js',
        'tilt-lib' => '/assets/lib/tilt/tilt.jquery.min.js',
        'dce-jquery-visible' => '/assets/lib/visible/jquery-visible.min.js',
        'jquery-easing' => '/assets/lib/jquery-easing/jquery-easing.min.js',
        'justifiedGallery-lib' => '/assets/lib/justifiedGallery/js/jquery.justifiedGallery.min.js',
        'dce-parallaxjs-lib' => '/assets/lib/parallaxjs/parallax.min.js',
        'dce-threesixtyslider-lib' => '/assets/lib/threesixty-slider/threesixty.min.js',
        'dce-jqueryeventmove-lib' => '/assets/lib/twentytwenty/jquery.event.move.js',
        'dce-twentytwenty-lib' => '/assets/lib/twentytwenty/jquery.twentytwenty.js',
        'dce-anime-lib' => '/assets/lib/anime/anime.min.js',
        //'dce-aframe' => '/assets/lib/aframe/aframe-v0.8.2.min.js',
        //'dce-aframe' => '/assets/lib/aframe/aframe-v1.0.3.min.js',
        // -----------------------------------------------------------
        // WEB-GL
        'dce-distortion-lib' => '/assets/lib/distortion/distortion-lib.js',
        'dce-threejs-lib' => '/assets/lib/threejs/three.min.js', //'/assets/lib/threejs/three.min.js',
        'dce-threejs-figure' => '/assets/lib/threejs/figure.js',
        'dce-threejs-EffectComposer' => '/assets/lib/threejs/postprocessing/EffectComposer.js',
        'dce-threejs-RenderPass' => '/assets/lib/threejs/postprocessing/RenderPass.js',
        'dce-threejs-ShaderPass' => '/assets/lib/threejs/postprocessing/ShaderPass.js',
        'dce-threejs-BloomPass' => '/assets/lib/threejs/postprocessing/BloomPass.js',
        'dce-threejs-FilmPass' => '/assets/lib/threejs/postprocessing/FilmPass.js',
        'dce-threejs-HalftonePass' => '/assets/lib/threejs/postprocessing/HalftonePass.js',
        'dce-threejs-DotScreenPass' => '/assets/lib/threejs/postprocessing/DotScreenPass.js',
        'dce-threejs-GlitchPass' => '/assets/lib/threejs/postprocessing/GlitchPass.js',
        'dce-threejs-CopyShader' => '/assets/lib/threejs/shaders/CopyShader.js',
        'dce-threejs-HalftoneShader' => '/assets/lib/threejs/shaders/HalftoneShader.js',
        'dce-threejs-RGBShiftShader' => '/assets/lib/threejs/shaders/RGBShiftShader.js',
        'dce-threejs-DotScreenShader' => '/assets/lib/threejs/shaders/DotScreenShader.js',
        'dce-threejs-ConvolutionShader' => '/assets/lib/threejs/shaders/ConvolutionShader.js',
        'dce-threejs-FilmShader' => '/assets/lib/threejs/shaders/FilmShader.js',
        'dce-threejs-ColorifyShader' => '/assets/lib/threejs/shaders/ColorifyShader.js',
        'dce-threejs-VignetteShader' => '/assets/lib/threejs/shaders/VignetteShader.js',
        'dce-threejs-DigitalGlitch' => '/assets/lib/threejs/shaders/DigitalGlitch.js',
        'dce-threejs-PixelShader' => '/assets/lib/threejs/shaders/PixelShader.js',
        'dce-threejs-LuminosityShader' => '/assets/lib/threejs/shaders/LuminosityShader.js',
        'dce-threejs-SobelOperatorShader' => '/assets/lib/threejs/shaders/SobelOperatorShader.js',
        'dce-threejs-AsciiEffect' => '/assets/lib/threejs/effects/AsciiEffect.js',
        // WebGL Distortion
        'data-gui' => '/assets/lib/threejs/libs/dat.gui.min.js',
        'displacement-sktech' => '/assets/lib/threejs/sketch.js',
        // -----------------------------------------------------------
        // GSAP
        'dce-tweenMax-lib' => '/assets/lib/greensock/TweenMax.min.js',
        'dce-tweenLite-lib' => '/assets/lib/greensock/TweenLite.min.js',
        'dce-timelineLite-lib' => '/assets/lib/greensock/TimelineLite.min.js',
        'dce-timelineMax-lib' => '/assets/lib/greensock/TimelineMax.min.js',
        'dce-morphSVG-lib' => '/assets/lib/greensock/plugins/MorphSVGPlugin.min.js',
        'dce-splitText-lib' => '/assets/lib/greensock/utils/SplitText.min.js',
        'dce-textPlugin-lib' => '/assets/lib/greensock/plugins/TextPlugin.min.js',
        'dce-svgdraw-lib' => '/assets/lib/greensock/plugins/DrawSVGPlugin.min.js',
        //'dce-attr-lib' => 'https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/plugins/AttrPlugin.min.js',
        // -----------------------------------------------------------
        // CANVAS
        'dce-bgcanvas' => '/assets/js/dce-bgcanvas.js',
        // -----------------------------------------------------------
        // Vario o abbandonati
        //'dce-charming-lib' => '/assets/lib/charming/charming.min.js',
        //'dce-pagepiling-lib' => '/assets/lib/pagepiling/jquery.pagepiling.min.js',
        //'dce-fullpage-lib' => '/assets/lib/fullpage/jquery.fullpage.min.js',
        //'dce-extension-lib' => '/assets/lib/fullpage/jquery.fullpage.extensions.min.js',
        // -----------------------------------------------------------
        // Extension Advanced
        'dce-rellaxjs-lib' => '/assets/lib/rellax/rellax.min.js',
        'dce-clipboard-js' => '/assets/lib/clipboard.js/clipboard.min.js',
        'dce-revealFx' => '/assets/lib/reveal/revealFx.js',
        // -----------------------------------------------------------
        // Document
        'scrollify' => '/assets/lib/scrollify/jquery.scrollify.js',
        'inertiaScroll' => '/assets/lib/inertiaScroll/jquery-inertiaScroll.js',
        'dce-lax-lib' => '/assets/lib/lax/lax.min.js',
        'dce-scrolling' => '/assets/js/elements-documentScrolling.js', // provvi
        // -----------------------------------------------------------
        // GoogleMap
        'google-maps-api' => 'https://maps.googleapis.com/maps/api/js?key=dce_api_gmaps', // gmaps-js / google-maps-api
        'google-maps-markerclusterer' => 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js', // uael-google-maps-cluster
        'dce-google-maps' => '/assets/js/google-maps.js',
    );
    public static $scripts = array(
        'dce-main' => '/assets/js/main.js',
        'dce-cookie' => '/assets/js/dce-cookie.js',
        'dce-ajaxmodal' => '/assets/js/ajaxmodal.js',
        'dce-settings' => '/assets/js/dce-settings.js',
        'dce-animatetext' => '/assets/js/elements-animateText.js',
        'dce-popup' => '/assets/js/elements-popup.js',
        'dce-acfgallery' => '/assets/js/elements-acfgallery.js',
        'dce-acfslider' => '/assets/js/elements-acfslider.js',
        'dce-parallax' => '/assets/js/elements-parallax.js',
        //'dce-swiper' => '/assets/js/elements-swiper.js',
        'dce-threesixtyslider' => '/assets/js/elements-threesixtyslider.js',
        'dce-twentytwenty' => '/assets/js/elements-twentytwenty.js',
        'dce-tilt' => '/assets/js/elements-tilt.js',
        'dce-acf_posts' => '/assets/js/elements-acfposts.js',
        'dce-acf_repeater' => '/assets/js/elements-acfrepeater.js',
        'dce-content' => '/assets/js/elements-content.js',
        'dce-dynamic_users' => '/assets/js/elements-dynamicusers.js',
        'dce-acf_fields' => '/assets/js/elements-acf.js',
        'dce-modalwindow' => '/assets/js/elements-modalwindow.js',
        'dce-nextPrev' => '/assets/js/dce-nextprev.js',
        //'dce-youtube' => '/assets/js/dce-youtube.js',
        'dce-rellax' => '/assets/js/elements-rellax.js',
        'dce-reveal' => '/assets/js/elements-reveal.js',
        //'dce-dualView' => '/assets/js/elements-dualView.js',
        'dce-svgmorph' => '/assets/js/dce-svgmorph.js',
        'dce-svgdistortion' => '/assets/js/dce-svgdistortion.js',
        'dce-svgfe' => '/assets/js/dce-svgfe.js',
        'dce-svgblob' => '/assets/js/dce-svgblob.js',
        // WebgL Distortion
        'dce-imagesdistortion' => '/assets/js/elements-distortionImage.js',
        // Document Scrolling
        'dce-scrolling' => '/assets/js/elements-documentScrolling.js',
        //'dce-poster-slider' => '/assets/js/poster-slider.js',
        //'dce-fullpage' => '/assets/js/elements-fullpage.js',
        //'dce-pagepiling' => '/assets/js/elements-pagepiling.js',
        'dce-animated-offcanvas-menu-js' => '/assets/js/dce-animatedoffcanvasmenu.js',
        'dce-cursorTracker' => '/assets/js/dce-cursorTracker.js',
        'dce-advancedvideo' => '/assets/js/dce-advancedvideo.js',
        // Form
        'dce-form-step' => '/assets/js/dce-form-step.js',
        'dce-form-summary' => '/assets/js/dce-form-summary.js',
            // -----------------------------------------------------------
            // Global Settings JS
            // ... spostato in wp_enqueue_script
    );
    public static $minifyJs = 'js/dce-frontend.min.js';

    public function __construct() {
        $this->init();
    }

    public function init() {

        // inject custom css and js
        add_action('wp_head', [$this, 'dce_head']);
        add_action('wp_footer', [$this, 'dce_footer'], 100);

        // add custom body class
        add_filter('body_class', function($classes) {
            $classes[] = 'elementor-dce';
            return $classes;
        });

        // force jquery in head
        add_action('wp_enqueue_scripts', function() {
            wp_enqueue_script('jquery');
        });

        // Admin Style
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));

        // REGENERATE STYLE
        add_action('elementor/core/files/clear_cache', array($this, 'regenerate_assets'));
        //add_action( 'elementor/core/files/clear_cache', array($this, 'regenerate_style') );
        //add_action( 'elementor/core/files/clear_cache', array($this, 'regenerate_script') );

        // -------------------- OCEANWP SCRIPT
        add_action('elementor/frontend/after_enqueue_scripts', function() {
            $theme = wp_get_theme();
            if ('OceanWP' == $theme->name || 'oceanwp' == $theme->template) {
                $dir = OCEANWP_JS_DIR_URI;
                $theme_version = OCEANWP_THEME_VERSION;
                wp_enqueue_script('oceanwp-main', $dir . 'main.min.js', array('jquery'), $theme_version, true);
            }
        });

        // -------------------- SCRIPT
        add_action('elementor/frontend/after_register_scripts', array($this, 'dce_frontend_register_script'));
        add_action('elementor/frontend/after_enqueue_scripts', [$this, 'dce_frontend_enqueue_scripts']);

        // -------------------- STYLE
        //add_action( 'elementor/preview/enqueue_styles', array( $this, 'dce_preview_style') );
        add_action('elementor/frontend/after_register_styles', array($this, 'dce_frontend_register_style'));
        add_action('elementor/frontend/after_enqueue_styles', array($this, 'dce_frontend_enqueue_style'));

        //
        // -------------------- EDITOR
        add_action('elementor/editor/after_enqueue_scripts', array($this, 'dce_editor'));
        add_action('elementor/preview/enqueue_styles', array($this, 'dce_preview'));

        // -------------------- GLOBAL
        // Global enqueue Script and Style
        add_action('wp_enqueue_scripts', array($this, 'dce_globals_stylescript'));

        // ELEMENTOR Style
        /* add_action('elementor/frontend/after_register_styles', function() {
          //wp_register_style( 'dynamic-content-elements-style', plugins_url( '/assets/css/dynamic-content-elements.css', DCE__FILE__ ) );

          //wp_register_style('dce-style', plugins_url('/assets/css/style.css', DCE__FILE__));
          //wp_register_style('animatecss', plugins_url('/assets/lib/animate/animate.min.css', DCE__FILE__));


          // photoswipe
          //wp_register_style( 'photoswipe', plugins_url( '/assets/css/photoSwipe/photoswipe.min.css.css', DCE__FILE__ ) );
          //wp_register_style( 'photoswipe-default-skin', plugins_url( '/assets/photoSwipe/default-skin/default-skin.min.css', DCE__FILE__ ) );
          }); */

        /* add_action('elementor/frontend/after_enqueue_styles', function() {
          wp_enqueue_style('dashicons');
          wp_enqueue_style('animatecss');
          wp_enqueue_style('dce-style');
          }); */
    }

    static public function dce_globals_stylescript() {
        $is_in_editor = \DynamicContentForElementor\DCE_Helper::is_edit_mode();
        //
        // -------------------- GLOBAL
        if (get_option('enable_smoothtransition') || $is_in_editor) {

            // -----------------------------------------------------------
            // Global Settings CSS LIB
            wp_enqueue_style('animsition-base', DCE_URL . 'assets/lib/animsition/css/animsition.css');
            wp_enqueue_style('dce-animations');
            // -----------------------------------------------------------
            // Global Settings JS
            // 'dce-barbajs-lib' => '/assets/lib/barbajs/barba.min.js',
            // 'dce-barbajs' => '/assets/js/global-barbajs.js',
            // 'dce-swup-lib' => '/assets/lib/swup/swup.js',
            // 'dce-swup-lib-swupMergeHeadPlugin' => '/assets/lib/swup/plugins/swupMergeHeadPlugin.js',
            // 'dce-swup-lib-swupGaPlugin' => '/assets/lib/swup/plugins/swupGaPlugin.min.js',
            // 'dce-swup-lib-swupGtmPlugin' => '/assets/lib/swup/plugins/swupGtmPlugin.min.js',
            // Global Settings JS LIB
            wp_enqueue_script('dce-animsition-lib', DCE_URL . 'assets/lib/animsition/js/animsition.min.js', array('jquery'), '4.0.2', true);
        }
        if (get_option('enable_trackerheader') || $is_in_editor) {
            // Global Settings JS LIB
            wp_enqueue_script('dce-trackerheader-lib', DCE_URL . 'assets/lib/headroom/headroom.min.js', array('jquery'), '0.11.0', true);
        }
        if (get_option('enable_trackerheader') || get_option('enable_smoothtransition') || $is_in_editor) {
            wp_enqueue_script('dce-globalsettings', DCE_URL . 'assets/js/global-settings.js', array('jquery'), DCE_VERSION, false);
            wp_enqueue_style('dce-globalsettings', DCE_URL . 'assets/css/dce-globalsettings.css');


            $settings_controls = (new \DynamicContentForElementor\Includes\Settings\DCE_Settings_Manager)->dce_settings();
            wp_localize_script('dce-globalsettings', 'dceGlobalSettings', $settings_controls);
        }
        /*
          if (!$is_in_editor) {
          if (get_option('enable_trackerheader') || get_option('enable_smoothtransition')) {
          // questa è una prova per forzare il global css di elementor in tutte le pagine...
          //$wp_styles->registered[$elementor_css_handle];
          $eglobal = 'elementor-global';
          if (!wp_script_is($eglobal)) {
          $upload_dir = wp_upload_dir();
          wp_enqueue_style($eglobal, $upload_dir['baseurl'] . '/elementor/css/global.css');
          }
          }
          }
         */

        /*
          // Global Settings CSS LIB
          wp_enqueue_style( 'animsition-base', DCE_URL . 'assets/lib/animsition/css/animsition.css' );
          wp_enqueue_script( 'dce-animsition-lib', DCE_URL . 'assets/lib/animsition/js/animsition.min.js', array('jquery'), '4.0.2', true);

          // Global Settings JS LIB
          wp_enqueue_script( 'dce-trackerheader-lib', DCE_URL . 'assets/lib/headroom/headroom.min.js', array('jquery'), '0.11.0', true );

          wp_enqueue_script( 'dce-globalsettings', DCE_URL . 'assets/js/global-settings.js', array('jquery'), DCE_VERSION, false );
          wp_enqueue_style( 'dce-globalsettings', DCE_URL . 'assets/css/dce-globalsettings.css', array('elementor-global') );

          // questa è una prova per forzare in global...
          wp_enqueue_style('elementor-global');


          $settings_controls = (new \DynamicContentForElementor\Includes\Settings\DCE_Settings_Manager)->dce_settings();
          wp_localize_script('dce-globalsettings', 'dceGlobalSettings', $settings_controls); */
    }

    static public function dce_frontend_enqueue_style() {
        // @FISH
        if (file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss) && !WP_DEBUG) {
            //echo 'css minimizzato'; die();
            wp_enqueue_style('dce-all-css');
        } else {
            /* $styles = self::get_enabled_css();
              foreach (self::$styles as $key => $value) {
              if (in_array($key, $styles)) {
              wp_enqueue_style($key);
              }
              } */
            wp_enqueue_style('dce-style');
        }
        wp_enqueue_style('dashicons'); // @ Marco, serve?
        //self::$dce_styles = [];
        //wp_enqueue_style('dce-photoSwipe_default');
        //wp_enqueue_style('dce-photoSwipe_skin');
        //
        //wp_enqueue_style('dce-file-icon');
        //wp_enqueue_style('dce-pageanimations');
        /*
          if ( DCE_Helper::is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
          wp_enqueue_style('woocommerce-layout');
          wp_enqueue_style('woocommerce-smallscreen');
          wp_enqueue_style('woocommerce-general');
          wp_enqueue_style('woocommerce_prettyPhoto_css');
          //wp_enqueue_script('oceanwp-woocommerce');
          }
         */
    }

    public static function regenerate_assets() {
        self::regenerate_style();
        self::regenerate_script();
    }

    public static function clean_assets($asset = '') {
        if (!$asset || $asset == 'css') {
            if (is_file(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss)) {
                unlink(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss);
            }
            $mins = glob(DCE_ELEMENTOR_UPLOADS_PATH . 'css/min/*');
            if (!empty($mins)) {
                foreach ($mins as $amin) {
                    unlink($amin);
                }
            }
        }
        if (!$asset || $asset == 'js') {
            if (is_file(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs)) {
                unlink(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs);
            }
            $mins = glob(DCE_ELEMENTOR_UPLOADS_PATH . 'js/min/*');
            if (!empty($mins)) {
                foreach ($mins as $amin) {
                    unlink($amin);
                }
            }
        }
        return true;
    }

    public static function regenerate_style($cache = false) {
        if (file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss)) {
            if ($cache) {
                return true;
            }
            self::clean_assets('css');
        }
        if (!file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss)) {
            $css_enabled = self::get_enabled_css();
            //var_dump($css_enabled); die();
            if (!is_dir(DCE_ELEMENTOR_UPLOADS_PATH . 'css/min/')) {
                mkdir(DCE_ELEMENTOR_UPLOADS_PATH . 'css/min/', 0755, true);
            }
            // MINIFY CSS
            foreach (self::$styles as $key => $value) {
                if (in_array($key, $css_enabled)) {
                    $value = str_replace('/assets/css', 'assets/css', $value);
                    $fileName = basename($value);
                    $pezzi = explode('.', $fileName);
                    array_pop($pezzi);
                    $fileName = implode('.', $pezzi);
                    $asset_path = DCE_PATH . $value;
                    if (is_file($asset_path) && filesize($asset_path)) {
                        $minifier = new Minify\CSS();
                        $minifier->add(DCE_PATH . $value);
                        // save minified file to disk
                        $minifier->minify(DCE_ELEMENTOR_UPLOADS_PATH . 'css/min/' . $fileName . '.min.css');
                    }
                }
            }
            touch(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss);
            $mins = glob(DCE_ELEMENTOR_UPLOADS_PATH . 'css/min/*');
            //var_dump($mins);
            foreach ($mins as $amin) {
                file_put_contents(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss, PHP_EOL . '/*' . basename($amin) . '*/' . PHP_EOL, FILE_APPEND | LOCK_EX);
                file_put_contents(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss, file_get_contents(DCE_ELEMENTOR_UPLOADS_PATH . 'css/min/' . basename($amin)), FILE_APPEND | LOCK_EX);
            }
        }
    }

    public static function get_enabled_css() {

        $widget_manager = new DCE_Widgets();
        $widget_manager->on_widgets_registered();

        $extension_manager = new DCE_Extensions();
        $extension_manager->on_extensions_registered();

        $document_manager = new DCE_Documents();
        $document_manager->on_documents_registered();

        self::$dce_styles[] = 'dce-style';

        return self::$dce_styles;
    }

    public static function get_enabled_js() {

        $widget_manager = new DCE_Widgets();
        $widget_manager->on_widgets_registered();

        $extension_manager = new DCE_Extensions();
        $extension_manager->on_extensions_registered();

        $document_manager = new DCE_Documents();
        $document_manager->on_documents_registered();

        self::$dce_scripts[] = 'dce-settings';
        self::$dce_scripts[] = 'dce-main';

        return self::$dce_scripts;
    }

    public static function add_depends($element) {
        $w_styles = $element->get_style_depends();
        if (!empty($w_styles)) {
            self::$dce_styles = array_merge(self::$dce_styles, $w_styles);
        }
        $w_scripts = $element->get_script_depends();
        if (!empty($w_scripts)) {
            self::$dce_scripts = array_merge(self::$dce_scripts, $w_scripts);
        }
    }

    public static function regenerate_script($cache = false) {

        // delete all template js
        $upload_dir = wp_get_upload_dir();
        $js_dir = $upload_dir['basedir'] . '/elementor/js';
        DCE_Helper::rm_dir($js_dir);

        if (file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs)) {
            if ($cache) {
                return true;
            }
            self::clean_assets('js');
        }
        if (!file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs)) {
            $js_enabled = self::get_enabled_js();
            //var_dump($js_enabled); die();
            if (!is_dir(DCE_ELEMENTOR_UPLOADS_PATH . 'js/min/')) {
                mkdir(DCE_ELEMENTOR_UPLOADS_PATH . 'js/min/', 0755, true);
            }
            // MINIFY
            foreach (self::$scripts as $key => $value) {
                if (in_array($key, $js_enabled)) {
                    $value = str_replace('/assets/js', 'assets/js', $value);
                    $fileName = basename($value);
                    $pezzi = explode('.', $fileName);
                    array_pop($pezzi);
                    $fileName = implode('.', $pezzi);
                    $asset_path = DCE_PATH . $value;
                    if (is_file($asset_path) && filesize($asset_path)) {
                        $minifier = new Minify\JS();
                        $minifier->add($asset_path);
                        // save minified file to disk
                        $minifier->minify(DCE_ELEMENTOR_UPLOADS_PATH . 'js/min/' . $fileName . '.min.js');
                    }
                }
            }
            touch(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs);
            $mins = glob(DCE_ELEMENTOR_UPLOADS_PATH . 'js/min/*');
            foreach ($mins as $amin) {
                file_put_contents(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs, PHP_EOL . '/*' . basename($amin) . '*/' . PHP_EOL, FILE_APPEND | LOCK_EX);
                file_put_contents(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs, ';' . file_get_contents(DCE_ELEMENTOR_UPLOADS_PATH . 'js/min/' . basename($amin)), FILE_APPEND | LOCK_EX);
            }
        }
    }

    public function dce_frontend_register_style() {

        if (WP_DEBUG) {
            $styles = self::get_enabled_css();
            foreach (self::$styles as $key => $value) {
                if (in_array($key, $styles)) {
                    if (substr($value, 0, 4) != 'http') {
                        $min_value = false;
                        if (!SCRIPT_DEBUG) {
                            $min_value = str_replace('assets/css/', 'assets/css/min/', $value);
                            $pieces = explode('.', $min_value);
                            $ext = array_pop($pieces);
                            if ($ext == 'css') {
                                $min_value = implode('.', $pieces) . '.min.css';
                            }
                            $min_value = plugins_url($min_value, DCE__FILE__);
                        }
                        $value = plugins_url($value, DCE__FILE__);
                        if ($min_value && is_file($min_value)) {
                            $value = $min_value;
                        }
                    }
                    wp_register_style($key, $value);
                }
            }
        } else {
            if (!file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyCss)) {
                self::regenerate_style();
            }
            wp_register_style('dce-all-css', DCE_ELEMENTOR_UPLOADS_URL . self::$minifyCss);
        }

        foreach (self::$vendorsCss as $key => $value) {
            /* if (substr($value, 0, 4) != 'http') {
              $value = plugins_url($value, DCE__FILE__);
              } */
            wp_register_style($key, plugins_url($value, DCE__FILE__));
        }
    }

    public function dce_frontend_register_script() {
        $dce_apis = self::get_dce_apis();

        if (WP_DEBUG) {
            $scripts = self::get_enabled_js();
            foreach (self::$scripts as $key => $value) {
                if (in_array($key, $scripts)) {
                    // setting configurated api key
                    if (!empty($dce_apis)) {
                        foreach ($dce_apis as $api_key => $api_value) {
                            $value = str_replace($api_key, $api_value, $value);
                        }
                    }
                    if (substr($value, 0, 4) != 'http') {
                        $min_value = false;
                        if (!SCRIPT_DEBUG) {
                            $min_value = str_replace('assets/js/', 'assets/js/min/', $value);
                            $pieces = explode('.', $min_value);
                            $ext = array_pop($pieces);
                            if ($ext == 'js') {
                                $min_value = implode('.', $pieces) . '.min.js';
                            }
                            $min_value = plugins_url($min_value, DCE__FILE__);
                        }
                        $value = plugins_url($value, DCE__FILE__);
                        if ($min_value && is_file($min_value)) {
                            $value = $min_value;
                        }
                    }
                    wp_register_script($key, $value);
                }
            }
        } else {
            if (!file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs)) {
                self::regenerate_script();
            }
            wp_register_script('dce-all-js', DCE_ELEMENTOR_UPLOADS_URL . self::$minifyJs);
        }

        foreach (self::$vendorsJs as $key => $value) {
            // setting configurated api key
            if (!empty($dce_apis)) {
                foreach ($dce_apis as $api_key => $api_value) {
                    if (strpos($value, $api_key) === false) {
                        continue;
                    }
                    $value = str_replace($api_key, $api_value, $value);
                }
            }
            if (substr($value, 0, 4) != 'http') {
                $value = plugins_url($value, DCE__FILE__);
            }
            wp_register_script($key, $value);
        }
    }

    //
    public function dce_frontend_enqueue_scripts() {
        if (file_exists(DCE_ELEMENTOR_UPLOADS_PATH . self::$minifyJs) && !WP_DEBUG) {
            wp_enqueue_script('dce-all-js');
        } else {
            /* $scripts = self::get_enabled_js();
              //var_dump($scripts);
              foreach (self::$scripts as $key => $value) {
              if (in_array($key, $scripts)) {
              wp_enqueue_script($key);
              }
              } */
            wp_enqueue_script('dce-settings');
        }

        //self::$dce_scripts = [];

        if (DCE_Helper::is_plugin_active('woocommerce/woocommerce.php')) {
            //plugin is activated
            //self::dce_wc_enqueue_scripts();
        }
    }

    public function dce_head() {
        //ob_start(array($this, 'dce_callback'));
        self::add_head_fontend_js();
    }

    public function dce_footer() {
        //ob_end_flush();
        if (!empty(DCE_Elements::$elements['widget'])) {
            $template_id = DCE_Elements::get_main_template_id();
            if ($template_id) {
                $widgets = get_post_meta($template_id, 'dce_widgets', true);
                if (empty($widgets)) {
                    $widgets = DCE_Elements::$elements['widget'];
                } else {
                    foreach (DCE_Elements::$elements['widget'] as $wkey => $awidget) {
                        $widgets[$wkey] = $awidget;
                    }
                }
                update_post_meta($template_id, 'dce_widgets', DCE_Elements::$elements['widget']);
            }
        }
        self::add_footer_fontend_css();
        self::add_footer_fontend_js();
    }

    public static function dce_enqueue_script($handle, $js = '', $element_id = false) {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            self::$dce_scripts[$handle] = $js;
            return '';
        } else {
            if (is_array($js)) {
                $js = $js['script'];
            }
        }
        /*
          $tmp = explode('elementorFrontend.hooks.addAction(', $js, 2);
          if ($element_id && count($tmp) > 1) {
          $tmp = explode(',', end($tmp), 2);
          $hook = reset($tmp);
          $hook = str_replace('"', '', $hook);
          $hook = str_replace("'", '', $hook);
          $hook = trim($hook);

          //$hook = 'frontend/element_ready/widget';
          ob_start();
          ?>
          <script>
          //. 'jQuery(window).on("elementor/frontend/init", function () {'
          jQuery(document).ready(function(){
          console.log("editor hook");
          console.log("<?php echo $hook; ?>");
          console.log(".elementor-element-<?php echo $element_id; ?>");
          $scope = jQuery(".elementor-element-<?php echo $element_id; ?>");
          console.log($scope);
          //. 'elementorFrontend.hooks.removeAction("'.$hook.'");' //, function(){'
          if (typeof <?php echo $handle; ?> == 'function') {
          <?php echo $handle; ?>($scope, $);
          } else {
          jQuery(window).trigger("elementor/frontend/init");
          }
          //elementorFrontend.actions[];
          //. '}); '
          //. 'setTimeout(function(){'
          //. 'elementorFrontend.hooks.doAction("'.$hook.'", $scope, $); '
          //. '}, 100);'
          });
          </script>
          <?php
          $js = $js.ob_get_clean();
          }
         */
        return $js;
    }
    
    public static function dce_enqueue_style($handle, $css = '', $element_id = false) {
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            if (empty(self::$dce_styles[$handle])) {
                self::$dce_styles[$handle] = $css;
            } else {
                self::$dce_styles[$handle] .= $css;
            }
            return '';
        }
        return $css;
    }

    public static function add_head_fontend_js() {
        $template_id = DCE_Elements::get_main_template_id();
        //var_dump($template_id); die();
        if ($template_id) {
            $widgets = get_post_meta($template_id, 'dce_widgets', true);
            if (!empty($widgets)) {
                //var_dump($widgets);
                if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                    if (isset($widgets['dyncontel-panorama'])) {
                        echo '<script src="' . DCE_URL . 'assets/lib/aframe/aframe.min.js?ver=1.0.4"></script>';
                    }
                }
            }
        }
    }

    public static function add_footer_fontend_js($inline = true) {
        $js = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $script_keys = array_keys(self::$scripts);
            $vendor_keys = array_keys(self::$vendorsJs);
            foreach (self::$dce_scripts as $skey => $ascript) {
                if (is_numeric($skey) || in_array($ascript, $script_keys) || in_array($ascript, $vendor_keys)) {
                    unset(self::$dce_scripts[$skey]);
                }
            }
            if (!empty(self::$dce_scripts)) {
                if ($inline) {
                    foreach (self::$dce_scripts as $jkey => $jscript) {
                        $tmp = explode('-', $jkey);
                        $element_id = array_pop($tmp);
                        $element_type = implode('-', $tmp);
                        $fnc = str_replace('-', '_', $jkey);
                        $element_hook = $element_type . '.default';
                        /*
                          $element = DCE_Helper::get_element_by_id($element_id);
                          $widget_type = 'section';
                          if ($element) {
                          $widget_type = $element['el_type'].'.default';
                          }
                         */

                        if (is_array($jscript)) {
                            $fnc = $jscript['type'] . '_' . $jscript['name'] . '_' . $jscript['id'];
                            if (!empty($jscript['sub'])) {
                                $fnc .= '_' . $jscript['sub'];
                            }
                            $js .= '<script id="dce-' . $jkey . '">
                                ( function( $ ) {
                                    var dce_' . $fnc . ' = function( $scope, $ ) {
                                        console.log("' . $fnc . '");
                                        ' . self::remove_script_wrapper($jscript['script']) . '
                                    };
                                    $( window ).on( \'elementor/frontend/init\', function() {
                                        elementorFrontend.hooks.addAction( \'frontend/element_ready/' . $jscript['name'] . '.default\', dce_' . $fnc . ' );
                                    } );
                                } )( jQuery, window );
                                </script>';
                        } else {
                            if (!empty(strip_tags($jscript))) {
                                if (strpos($jscript, '<script') !== false) {
                                    if (strpos($jscript, '<script>') !== false) {
                                        $js .= str_replace('<script', '<script id="dce-' . $jkey . '"', $jscript);
                                    } else {
                                        $js .= $jscript;
                                    }
                                } else {
                                    $js .= '<script id="' . $jkey . '">' . $jscript . '</script>';
                                }
                            }
                        }
                    }
                } else {
                    $post_id = DCE_Elements::get_main_template_id();
                    $upload_dir = wp_get_upload_dir();
                    //var_dump($upload_dir);
                    $js_file = 'post-' . $post_id . '.js';
                    $js_dir = $upload_dir['basedir'] . '/elementor/js/';
                    $js_baseurl = $upload_dir['baseurl'] . '/elementor/js/';
                    $js_path = $js_dir . $js_file;
                    if (is_file($js_path)) {
                        $file_modified_date = filemtime($js_path);
                        if (get_the_modified_date("U", $post_id) > $file_modified_date) {
                            unlink($js_path);
                        }
                    }
                    if (!is_file($js_path)) {
                        // create folder (if not exist)
                        if (!is_dir($js_dir)) {
                            mkdir($js_dir, 0755, true);
                        }
                        // create the file
                        $js_file_content = '';
                        foreach (self::$dce_scripts as $jkey => $jscript) {
                            if (strpos($jscript, '<script') !== false) {
                                $jscript = str_replace('<script>', '', $jscript);
                                $jscript = str_replace('</script>', '', $jscript);
                            }
                            if (!empty($jscript)) {
                                $js_file_content .= '// ' . $jkey . PHP_EOL . $jscript;
                            }
                        }
                        if (!empty($js_file_content)) {
                            //var_dump($js_file_content);
                            file_put_contents($js_path, $js_file_content);
                        }
                    }
                    if (is_file($js_path)) {
                        $js_url = $js_baseurl . $js_file;
                        //var_dump($js_url);
                        echo '<script type="text/javascript" src="' . $js_url . '"></script>';
                    }
                }
            }
        }
        echo $js;
    }
    
    public static function add_footer_fontend_css($inline = true) {
        $css = '';
        if (!\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            $style_keys = array_keys(self::$styles);
            $vendor_keys = array_keys(self::$vendorsCss);
            foreach (self::$dce_styles as $skey => $astyle) {
                if (is_numeric($skey) || in_array($astyle, $style_keys) || in_array($astyle, $vendor_keys)) {
                    unset(self::$dce_styles[$skey]);
                }
            }
            if (!empty(self::$dce_styles)) {
                if ($inline) {
                    foreach (self::$dce_styles as $ckey => $cstyle) {
                        if ($cstyle) {
                            $css .= '<style id="dce-' . $ckey . '">' . self::remove_style_wrapper($cstyle) . '</style>';
                        }
                    }
                } else {
                    // to file
                }
            }
            //var_dump(self::$dce_styles);
        }
        echo $css;
    }

    public static function remove_script_wrapper($script) {
        $script = str_replace('<script>', '', $script);
        $script = str_replace('</script>', '', $script);

        $script = str_replace('jQuery(document).ready(', 'setTimeout(', $script);
        return $script;
    }
    
    public static function remove_style_wrapper($style) {
        $style = str_replace('<style>', '', $style);
        $style = str_replace('</style>', '', $style);
        return $style;
    }

    // Woocommerce script
    public function dce_wc_enqueue_scripts() {
        // In preview mode it's not a real Product page - enqueue manually.
        /* if ( Plugin::elementor()->preview->is_preview_mode( $this->get_main_id() ) ) { */

        if (current_theme_supports('wc-product-gallery-zoom')) {
            wp_enqueue_script('zoom');
        }
        if (current_theme_supports('wc-product-gallery-slider')) {
            wp_enqueue_script('flexslider');
        }
        if (current_theme_supports('wc-product-gallery-lightbox')) {
            wp_enqueue_script('photoswipe-ui-default');
            wp_enqueue_style('photoswipe-default-skin');
            //add_action( 'wp_footer', 'woocommerce_photoswipe' );
        }
        wp_enqueue_script('wc-single-product');
        wp_enqueue_script('woocommerce');


        wp_enqueue_style('photoswipe');
        wp_enqueue_style('photoswipe-default-skin');
        wp_enqueue_style('photoswipe-default-skin');
        wp_enqueue_style('woocommerce_prettyPhoto_css');
        /* } */
    }

    /**
     * Enqueue admin styles
     *
     * @since 0.0.3
     *
     * @access public
     */
    public function enqueue_admin_styles() {
        //var_dump($hook); die();
        //$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        // Register styles
        // prima o poi dobbiamo minimizzare tutto e fare così per gestire i files assets per bene ;)
        wp_register_style('dce-admin-css', DCE_URL . 'assets/css/admin.min.css', [], DCE_VERSION);
        //'dce-admin', plugins_url('/assets/css/admin' . $suffix . '.css', DCE__FILE__), [], DCE_VERSION
        wp_enqueue_script('dce-admin-js', DCE_URL . 'assets/js/admin.min.js', [], DCE_VERSION);

        // select2
        wp_enqueue_style('dce-select2', DCE_URL . 'assets/css/select2.min.css', [], '4.0.7'); // '3.5.4'); versione vecchia per compatibilità con wpml
        wp_enqueue_script('dce-select2', DCE_URL . 'assets/js/select2.full.min.js', array('jquery'), '4.0.7', true); // '3.5.4'); versione vecchia per compatibilità con wpml
        //echo 'in admin'; die();
        // Enqueue styles Admin
        wp_enqueue_style('dce-admin-css');
    }

    /**
     * Enqueue admin styles
     *
     * @since 0.7.0
     *
     * @access public
     */
    public function dce_editor() {
        // Register styles
        wp_register_style(
                'dce-style-icons', plugins_url('/assets/css/dce-icon.css', DCE__FILE__), [], DCE_VERSION
        );
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');

        // Register styles
        wp_register_style(
                'dce-style-editor', plugins_url('/assets/css/dce-editor.css', DCE__FILE__), [], DCE_VERSION
        );
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-editor');

        wp_register_script(
                'dce-script-editor', plugins_url('/assets/js/dce-editor.js', DCE__FILE__), [], DCE_VERSION
        );
        wp_enqueue_script('dce-script-editor');

        wp_register_script(
                'dce-script-editor-activate', plugins_url('/assets/js/dce-editor-activate.js', DCE__FILE__), [], DCE_VERSION
        );
        wp_enqueue_script('dce-script-editor-activate');
        //
        //$this->dce_wc_enqueue_scripts();
        //
    }

    /**
     * Enqueue admin styles
     *
     * @since 1.0.3
     *
     * @access public
     */
    public function dce_preview() {
        wp_register_style(
                'dce-preview', plugins_url('/assets/css/dce-preview.css', DCE__FILE__), [], DCE_VERSION
        );
        // Enqueue DCE Elementor Style
        wp_enqueue_style('dce-preview');
    }

    static public function dce_icon() {
        // Register styles
        wp_register_style(
                'dce-style-icons', plugins_url('/assets/css/dce-icon.css', DCE__FILE__), [], DCE_VERSION
        );
        // Enqueue styles Icons
        wp_enqueue_style('dce-style-icons');
    }

    static public function get_dce_apis() {
        return get_option(SL_PRODUCT_ID . '_apis', array());
    }

    static public function wp_print_styles($handle = false, $print = true) {
        $styles = '';
        if ($handle) { 
            if (!empty(self::$styles[$handle])) { 
                //var_dump($handle); 
                $styles .= '<link rel="stylesheet" id="'.$handle.'" href="'.DCE_URL.self::$styles[$handle].'" type="text/css" media="all" />';
            }
        }
        if ($print) {
            echo $styles;
        }
        return $styles;
    }

    

}
