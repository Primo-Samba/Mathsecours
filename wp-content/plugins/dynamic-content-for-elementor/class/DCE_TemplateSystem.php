<?php

namespace DynamicContentForElementor;

/**
 * Widgets Class
 *
 * Register new elementor widget.
 *
 * @since 0.0.1
 */
class DCE_TemplateSystem {

    public static $instance;
    public static $template_id;
    public static $options = array();
    private $types_registered = [];
    private $taxonomyes_registered = [];
    static public $supported_types = array(
        'elementor_library',
        'oceanwp_library',
        'ae_global_templates'
    );
    static public $excluded_cpts = array(
        // JET
        'jet-engine',
        'jet-menu',
        'jet-popup',
        'jet-smart-filters',
    );
    static public $excluded_taxonomies = array(
        // CORE
        'nav_menu',
        'link_category',
        'post_format',
        // ELEMENTOR
        'elementor_library_type',
        'elementor_library_category',
        'elementor_font_type',
        // YOAST
        'yst_prominent_words',
        // WOOCOMMERCE
        'product_shipping_class',
        'product_visibility',
        'action-group',
        'pa_*',
        // LOCO
        'translation_priority',
        // FLAMINGO
        'flamingo_contact_tag',
        'flamingo_inbound_channel',
    );
    static public $template_styles = array();

    /**
     * Constructor
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function __construct() {
        $this->init();
        self::$instance = $this;
    }

    public function init() {

        add_action('elementor/init', array($this, 'dce_elementor_init'));

        self::$options = get_option(DCE_OPTIONS);
        $dce_template_disable = get_option('dce_template_disable');

        if (!$dce_template_disable) {

            // Setup
            add_action('init', array($this, 'dce_setup'));

            //$this->add_dynamic_tags();

            if (!is_admin()) {
                //\Elementor\Frontend::instance()->remove_content_filter();


                /* add_filter( 'elementor/frontend/builder_content_data', function($data, $post_id) {                    
                  $templates_ids = DCE_Elements::get_template_ids();
                  //var_dump($post_id); var_dump($templates_ids); die();
                  //if ($post_id)
                  if (!empty($templates_ids['dce']) && !empty($templates_ids['post']) && $templates_ids['post'] == $post_id) {
                  //return false;
                  }
                  return $data;
                  }, 10, 2); */

                self::add_content_filter();
            }

            // usa il gancio all'interno di DCE > template > archive.php
            add_action('dce_before_content_inner', array($this, 'dce_add_template_before_content'));
            add_action('dce_after_content_inner', array($this, 'dce_add_template_after_content'));
            // manage template fulwidth
            $this->dce_template_init();

            if (!is_admin()) { // necessary, if remove it will work also in admin result
                // per fare che la pagina dell'autore usi il loop su tutti i tipi non solo i post..
                add_action('pre_get_posts', array($this, 'enfold_customization_author_archives'));
            }

            //add_action( 'get_header', array($this, 'dce_get_header'),10,1 );
            //add_action( 'get_footer', array($this, 'dce_get_footer'),10,1 );

            DCE_Metabox::initTemplateSystem();
        }
    }

    public function add_content_filter() {
        $dce_template_disable = get_option('dce_template_disable');
        if (!$dce_template_disable) {
            if (!is_admin()) {
                add_filter('the_content', array($this, 'dce_elementor_remove_content_filter'), 1);
                add_filter('the_content', array($this, 'dce_filter_the_content_in_the_main_loop'), 999999);
            }
        }
    }

    public function remove_content_filter() {
        remove_filter('the_content', array($this, 'dce_filter_the_content_in_the_main_loop'), 999999);
    }

    /**
     * Init elementor finction
     *
     * @since 0.0.1
     *
     * @access public
     */
    public function dce_elementor_init() {
        // -------------------- DCE Shortcode
        add_shortcode('dce-elementor-template', array($this, 'dce_elementor_add_shortcode_template'));

        // loop dynamic css fix
        add_action("elementor/frontend/the_content", array($this, '_css_class_fix'));
        add_action("elementor/frontend/widget/after_render", array($this, '_fix_style'));
        add_action("elementor/frontend/column/after_render", array($this, '_fix_style'));
        add_action("elementor/frontend/section/after_render", array($this, '_fix_style'));

        // INIT HOOK
        do_action('dynamic_content_for_elementor/init');
    }

    public function dce_setup() {

        // COLUMNS
        $args = array(
            'public' => true,
                //'_builtin' => false,
        );
        // Column Template for terms
        $taxonomyesRegistered = get_taxonomies($args, 'names', 'and');
        $dceExcludedTaxonomies = self::$excluded_taxonomies;
        $taxonomyesRegistered = array_diff($taxonomyesRegistered, $dceExcludedTaxonomies);
        foreach ($taxonomyesRegistered as $chiave) {
            add_filter('manage_edit-' . $chiave . '_columns', array($this, 'dce_taxonomy_columns_head'));
            add_filter('manage_' . $chiave . '_custom_column', array($this, 'dce_taxonomy_columns_content'), 10, 3);
        }

        // Column Template for postsType
        $typesRegistered = DCE_Helper::get_types_registered();
        foreach ($typesRegistered as $chiave) {
            add_filter('manage_' . $chiave . '_posts_columns', array($this, 'dce_columns_head'));
            add_action('manage_' . $chiave . '_posts_custom_column', array($this, 'dce_columns_content'), 10, 2);
        }

        /* if (function_exists('is_product')) {
          //add_filter('body_class', array($this, 'wooc_class'), 13);
          add_action('wp_loaded', array($this, 'add_product_container_class'));
          } */
    }

    public static function add_product_container_class() {
        // Use html_compress($html) function to minify html codes.
        ob_start(function($html) {
            return str_replace('elementor elementor-', 'product elementor elementor-', $html);
        });
    }

    // Add columns Template ----------------------------------------------
    public function dce_columns_head($columns) {
        $columns['dce_template'] = 'Dynamic Content';
        return $columns;
    }

    public function dce_columns_content($column_name, $post_ID) {
        if ($column_name == 'dce_template') {
            // show content of 'directors_name' column
            $datopagina = get_post_meta($post_ID, 'dyncontel_elementor_templates', true);
            if ($datopagina) {
                // http://localhost:8888/marco/wp-admin/post.php?post=925&action=edit
                if ($datopagina != 1) {
                    echo '<a href="' . get_permalink($datopagina) . '" target="blank">' . get_the_title($datopagina) . '</a> - ';
                    echo '<a href="' . admin_url('post.php?post=' . $datopagina . '&action=edit') . '" target="blank">' . __('Edit') . '</a>';
                } else {
                    echo '<b>NO</b>';
                }
            } else {
                echo '-';
            }
        }
    }

    public function dce_taxonomy_columns_head($columns) {
        $columns['dce_template'] = 'Template';
        return $columns;
    }

    public function dce_taxonomy_columns_content($content, $column_name, $term_id = false) {
        if ('dce_template' == $column_name && $term_id) {
            $head_term = get_term_meta($term_id, 'dynamic_content_head', true);
            $block_term = get_term_meta($term_id, 'dynamic_content_block', true);
            $single_term = get_term_meta($term_id, 'dynamic_content_single', true);

            if ($head_term) {
                if ($head_term != 1) {
                    $content .= '<b>HEAD:</b> <a href="' . get_permalink($head_term) . '" target="blank">' . get_the_title($head_term) . '</a> - ';
                    $content .= '<a href="' . admin_url('post.php?post=' . $head_term . '&action=edit') . '" target="blank">' . __('Edit') . '</a><br>';
                } else {
                    $content = '<b>NO</b>';
                }
            }
            if ($block_term) {
                if ($head_term != 1) {
                    $content .= '<b>BLOCK:</b> <a href="' . get_permalink($block_term) . '" target="blank">' . get_the_title($block_term) . '</a> - ';
                    $content .= '<a href="' . admin_url('post.php?post=' . $block_term . '&action=edit') . '" target="blank">' . __('Edit') . '</a><br>';
                } else {
                    $content = '<b>NO</b>';
                }
            }
            if ($single_term) {
                if ($head_term != 1) {
                    $content .= '<b>SINGLE:</b> <a href="' . get_permalink($single_term) . '" target="blank">' . get_the_title($single_term) . '</a> - ';
                    $content .= '<a href="' . admin_url('post.php?post=' . $single_term . '&action=edit') . '" target="blank">' . __('Edit') . '</a><br>';
                } else {
                    $content = '<b>NO</b>';
                }
            }
            if (!$head_term && !$block_term && !$single_term)
                $content = ' - ';
        }
        return $content;
    }

    /**
     * Enqueue admin styles
     *
     * @since 0.5.0
     *
     * @access public
     */
    public function dce_elementor_add_shortcode_template($atts) {
        $atts = shortcode_atts(
                array(
                    'id' => '',
                    'post_id' => '',
                    'author_id' => '',
                    'user_id' => '',
                    'term_id' => '',
                    'ajax' => '',
                    'loading' => '',
                    'inlinecss' => false,
                ),
                $atts,
                'dce-elementor-template'
        );

        $atts['id'] = intval($atts['id']);
        $atts['post_id'] = intval($atts['post_id']);
        $atts['author_id'] = intval($atts['author_id']);
        $atts['user_id'] = intval($atts['user_id']);
        $atts['term_id'] = intval($atts['term_id']);

        if ($atts['id'] !== '') {
            global $wp_query;
            $original_queried_object = $wp_query->queried_object;
            $original_queried_object_id = $wp_query->queried_object_id;
            if (!empty($atts['post_id'])) {
                global $post;
                $original_post = $post;
                $post = get_post($atts['post_id']);
                if ($post) {
                    $wp_query->queried_object = $post;
                    $wp_query->queried_object_id = $atts['post_id'];
                }
            }
            if (!empty($atts['author_id'])) {
                global $authordata;
                $original_author = $authordata;
                $authordata = get_user_by('ID', $atts['author_id']);
                if ($authordata) {
                    $wp_query->queried_object = $authordata;
                    $wp_query->queried_object_id = $atts['author_id'];
                }
            }
            if (!empty($atts['user_id'])) {
                global $user;
                global $current_user;
                $original_user = $current_user;
                $user = $current_user = get_user_by('ID', $atts['user_id']);
                /* if ($user) {
                  $wp_query->queried_object = $user;
                  $wp_query->queried_object_id = $atts['user_id'];
                  } */
            }

            if (!empty($atts['term_id'])) {
                global $term;
                $term = get_term($atts['term_id']);
                if ($term) {
                    $wp_query->queried_object = $term;
                    $wp_query->queried_object_id = $atts['term_id'];
                }
            }

            //echo '..................... Inline CSS ...................';
            //var_dump($atts['inlinecss']);
            $inlinecss = ($atts['inlinecss'] == 'true');
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                $inlinecss = true;
            }
            //var_dump(wp_doing_ajax());
            if (!empty($atts['ajax']) && WP_DEBUG) {

                if (empty(DCE_Elements::$elements)) {
                    add_action("elementor/frontend/widget/after_render", function($widget = false) {
                        $styles = $widget->get_style_depends();
                        //var_dump($styles); die();
                        if (!empty($styles)) {
                            foreach ($styles as $key => $style) {
                                if (wp_doing_ajax()) {
                                    self::$template_styles[] = $style;
                                } else {
                                    DCE_Assets::wp_print_styles($style);
                                }
                            }
                        }
                    });
                    if (wp_doing_ajax()) {
                        add_action("elementor/frontend/the_content", function($content = false) {
                            $styles = self::$template_styles;
                            $add_styles = '';
                            if (!empty($styles)) {
                                foreach ($styles as $key => $style) {
                                    $add_styles .= DCE_Assets::wp_print_styles($style, false);
                                }
                            }
                            $template = \Elementor\Plugin::$instance->documents->get_current();        
                            $id = $template->get_main_id();
                            // add also current document file
                            return $content . $add_styles;
                        });
                    }
                }
            }

            $dce_default_template = $atts['id'];

            if (!empty($atts['loading']) && $atts['loading'] == 'lazy') {
                $attributes = wp_json_encode($atts);
                //$attributes = str_replace('"', "'", $attributes);
                $attributes = wp_slash($attributes);
                //$attributes = '['.$attributes.']';
                $optionals = '';
                if (!empty($atts['post_id'])) {
                    $optionals .= ' data-post="' . $atts['post_id'] . '"';
                }
                if (!empty($atts['user_id'])) {
                    $optionals .= ' data-user="' . $atts['user_id'] . '"';
                }
                if (!empty($atts['term_id'])) {
                    $optionals .= ' data-term="' . $atts['term_id'] . '"';
                }
                if (!empty($atts['author_id'])) {
                    $optionals .= ' data-author="' . $atts['author_id'] . '"';
                }
                $pagina_temlate = '<div class="dce-elementor-template-placeholder" data-id="' . $atts['id'] . '"' . $optionals . '></div>';
            } else {
                $pagina_temlate = self::get_template($dce_default_template, $inlinecss);
            }

            if (!empty($atts['post_id'])) {
                $post = $original_post;
            }
            if (!empty($atts['author_id'])) {
                $authordata = $original_author;
            }
            if (!empty($atts['user_id'])) {
                $user = $original_user;
                $current_user = $original_user;
            }
            $wp_query->queried_object = $original_queried_object;
            $wp_query->queried_object_id = $original_queried_object_id;
            return $pagina_temlate;
        }
    }

    private function dce_template_init() {
        // SINGE File template (blank page)
        //add_filter('single_template', array($this, 'dce_filter_singletemplate'));
        //add_filter('attachment_template', array($this, 'dce_filter_singletemplate'), 99);
        add_filter('template_include', array($this, 'dce_filter_statictemplate'), 999999); // SINGLE
        //ARCHIVIO file template (boxed/full/canvas)
        //add_filter('template_include', array($this, 'dce_filter_archivetemplate'), 999999); // ARCHIVE ... new for woocommerce
        add_filter('archive_template', array($this, 'dce_filter_archivetemplate'), 999999); // ARCHIVE
        //add_filter('woocommerce_shop_loop', array($this, 'aaa'));
    }

    public function themeslug_filter_front_page_template($template) {
        return is_home() ? '' : $template;
    }

    // ************************************** Woocommerce (to do)
    public static function wooc_class($classes) {

        $classes[] = 'woocommerce';


        return $classes;
    }

    public function enfold_customization_author_archives($query) {
        if ($query->is_author && $query->post_type == 'post') {
            $query->set('post_type', 'any');
            $query->set('posts_per_page', -1); //200);
        }
        remove_action('pre_get_posts', 'enfold_customization_author_archives');
    }

    public function dce_add_template_before_content() {

        global $post;
        global $default_template;

        if ($post) {
            $cptype = $post->post_type;
        } else {
            // in caso di nessun post associato a questa taxonomy
            $taxObject = get_taxonomy(get_queried_object()->taxonomy);
            // leggo le proprietà della taxonomy e ricavo il primo type associato (sarebbe bello confrontare tutto l'array)
            $postTypeArray = $taxObject->object_type;
            $cptype = $postTypeArray[0];
        }

        //echo "template ..........";

        $dce_default_template = '';
        $inlinecss = false;
        $dce_elementor_templates = '';
        $pagina_temlate = "";

        if ($cptype != '') {

            // 4- Type
            if (!empty(self::$options['dyncontel_before_field_archive' . $cptype])) {
                $dce_default_template = self::$options['dyncontel_before_field_archive' . $cptype];
            }

            if (isset(get_queried_object()->taxonomy)) {
                $taxo = get_queried_object()->taxonomy;
                //echo $taxo;
                // 3 - Taxonomy
                if (isset(self::$options['dyncontel_before_field_archive_taxonomy_' . $taxo]) && self::$options['dyncontel_before_field_archive_taxonomy_' . $taxo] > 0) {
                    $dce_default_template = self::$options['dyncontel_before_field_archive_taxonomy_' . $taxo];
                }

                // 2 - Termine
                //echo 'is_home '.is_home().'<br>';
                //echo 'is_tax '.is_tax().'<br>';
                //echo 'is_post_type_archive '.is_post_type_archive ().'<br>';
                //$terms_list_of_post = wp_get_post_terms(get_the_ID(), $taxo, array("fields" => "all"));
                /* $terms_list_of_post = get_terms( array(
                  'taxonomy' => $taxo,
                  'hide_empty' => false,
                  ) ); */
                $termine_id = get_queried_object()->term_id;
                if (/* count($terms_list_of_post) > 0 && */!is_post_type_archive()) {
                    //foreach ($terms_list_of_post as $term_single) {
                    //
                    $dce_default_template_term = get_term_meta($termine_id, 'dynamic_content_head', true);
                    //echo 'termmmm '.get_queried_object()->term_id;
                    if (!empty($dce_default_template_term)) {
                        $dce_default_template = $dce_default_template_term;
                    }
                    //}
                }
            }
            $default_template = $dce_default_template;
            if ($dce_default_template)
                echo do_shortcode('[dce-elementor-template id="' . $dce_default_template . '"]');
        } else {

            //_e('Sorry, there are no posts in this taxonomy ', 'dynamic-content-for-elementor');
            // empty archive template
            /* if ($empty_archive_template)
              echo do_shortcode('[dce-elementor-template id="' . $dce_default_template . '"]');
              } */
        }
    }

    public function dce_add_template_after_content() {
        //
        global $post;
        //
        global $default_template;
        //
        if ($post) {
            $cptype = $post->post_type;
        } else {
            // in caso di nessun post associato a questa taxonomy
            $taxObject = get_taxonomy(get_queried_object()->taxonomy);
            // leggo le proprietà della taxonomy e ricavo il primo type associato (sarebbe bello confrontare tutto l'array)
            $postTypeArray = $taxObject->object_type;
            $cptype = $postTypeArray[0];
        }
        //echo "template ..........";

        $dce_default_template = '';
        $dce_elementor_templates = '';
        $pagina_temlate = "";

        if ($cptype != '') {

            // 4- Type
            if (!empty(self::$options['dyncontel_after_field_archive' . $cptype])) {
                $dce_default_template = self::$options['dyncontel_after_field_archive' . $cptype];
            }

            if (isset(get_queried_object()->taxonomy)) {
                $taxo = get_queried_object()->taxonomy;
                //echo $taxo;
                // 3 - Taxonomy
                if (!empty(self::$options['dyncontel_after_field_archive_taxonomy_' . $taxo])) {
                    $dce_default_template = self::$options['dyncontel_after_field_archive_taxonomy_' . $taxo];
                }

                // 2 - Termine
                //echo 'is_home '.is_home().'<br>';
                //echo 'is_tax '.is_tax().'<br>';
                //echo 'is_post_type_archive '.is_post_type_archive ().'<br>';
                //$terms_list_of_post = wp_get_post_terms(get_the_ID(), $taxo, array("fields" => "all"));
                /* $terms_list_of_post = get_terms( array(
                  'taxonomy' => $taxo,
                  'hide_empty' => false,
                  ) ); */

                /*
                  $termine_id = get_queried_object()->term_id;
                  if (!is_post_type_archive()) {

                  $dce_default_template_term = get_term_meta($termine_id, 'dynamic_content_head', true);

                  if (!empty($dce_default_template_term)) {
                  $dce_default_template = $dce_default_template_term;
                  }

                  } */
            }
            $default_template = $dce_default_template;
            if ($dce_default_template)
                echo do_shortcode('[dce-elementor-template id="' . $dce_default_template . '"]');
        } else {

            //_e('Sorry, there are no posts in this taxonomy ', 'dynamic-content-for-elementor');
            // empty archive template
            /* if ($empty_archive_template)
              echo do_shortcode('[dce-elementor-template id="' . $dce_default_template . '"]');
              } */
        }
    }

    // ------------------------------------------------------------------------------- Filter ARCHIVE Template
    public function dce_filter_archivetemplate($single_template) {
        global $post;

        if (!is_author()) {
            // 1 - verifico se il tipo di post ha il template
            $typesRegistered = DCE_Helper::get_types_registered();


            foreach ($typesRegistered as $chiave) {

                if (isset($post->post_type) && $post->post_type == $chiave) {
                    if (!empty(self::$options['dyncontel_field_archive' . $chiave]) && !empty(self::$options['dyncontel_field_archive' . $chiave . '_template'])) {
                        //var_dump(is_404()); die();
                        if (!is_404()) {
                            //echo 'USO ARCHIVE '.self::$options['dyncontel_field_archive' . $chiave . '_template'];
                            $single_template = DCE_PATH . 'template/archive.php';
                            //$single_template = DCE_PATH . '/../elementor/modules/page-templates/templates/header-footer.php';
                        }
                    }
                }
            }
        } else if (is_author()) {
            if ((!empty(self::$options['dyncontel_field_archiveuser_template']) &&
                    !empty(self::$options['dyncontel_field_archiveuser'])) ||
                    !empty(self::$options['dyncontel_before_field_archiveuser']) ||
                    !empty(self::$options['dyncontel_after_field_archiveuser'])
            ) {
                $single_template = DCE_PATH . 'template/user.php';
            }
        }

        return $single_template;
    }

    // --------------------------------------------------------------- Filter SINGLE Template
    public function dce_filter_statictemplate($my_template) {
        //
        global $post;
        //
        if (is_singular() && !get_page_template_slug()) {

            // 2 - verifico se una tassonomia associata ha il blank
            $postTaxonomyes = DCE_Helper::get_post_terms($post->ID);
            if (!empty($postTaxonomyes)) {
                foreach ($postTaxonomyes as $tKey => $aTaxo) {
                    $aTaxName = $aTaxo->taxonomy;
                    if (!empty(self::$options['dyncontel_field_single_taxonomy_' . $aTaxName . '_blank'])) {
                        $_blank = self::$options['dyncontel_field_single_taxonomy_' . $aTaxName . '_blank'];
                        if ($_blank == 1 || $_blank == '1') {
                            $_blank = 'header-footer';
                        } // retrocompatibility
                        $my_template = ELEMENTOR_PATH . '/modules/page-templates/templates/' . $_blank . '.php';
                        break;
                    }
                }
            }

            // 1 - verifico se il tipo di post ha il blank
            $typesRegistered = DCE_Helper::get_types_registered();
            foreach ($typesRegistered as $chiave) {
                if (isset($post->post_type) && $post->post_type == $chiave && $chiave != 'product') {
                    if (!empty(self::$options['dyncontel_field_single' . $chiave . '_blank'])) {
                        $_blank = self::$options['dyncontel_field_single' . $chiave . '_blank'];
                        if ($_blank == 1 || $_blank == '1') {
                            $_blank = 'header-footer';
                        } // retrocompatibility
                        $my_template = ELEMENTOR_PATH . 'modules/page-templates/templates/' . $_blank . '.php';
                        break;
                    }
                }
            }
        }

        $datopagina = get_post_meta(get_the_ID(), 'dyncontel_elementor_templates', true);

        // -------------------------------------------------------- PRODUCT Archive Taxonomy
        // 2 - verifico se una tassonomia associata ha il template
        $taxonomyesRegistered = get_taxonomies(array('public' => true));
        $taxoRegistred = array_diff($taxonomyesRegistered, self::$excluded_taxonomies);

        if (isset(get_queried_object()->taxonomy)) {
            $taxo = get_queried_object()->taxonomy;
            //$tax = get_query_var( 'taxonomy' );
            //echo $taxo;

            foreach ($taxoRegistred as $chiave) {

                if (isset($taxo) && $taxo == $chiave) {
                    if (!empty(self::$options['dyncontel_field_archive_taxonomy_' . $chiave]) && !empty(self::$options['dyncontel_field_archive_taxonomy_' . $chiave . '_template'])) {
                        if (!is_404()) {
                            //echo 'USO ARCHIVE '.$chiave.self::$options['dyncontel_field_archive_taxonomy_' . $chiave . '_template'];
                            $my_template = DCE_PATH . '/template/archive.php';
                            //$my_template = DCE_PATH . '/../elementor/modules/page-templates/templates/header-footer.php';
                        }
                    }
                }
            }
        }
        // -------------------------------------------------------- PRODUCT WOOCOMMERCE
        if (function_exists('is_product')) {
            // In caso di SINGLE Wooc
            if (is_product()) {
                if (!empty(self::$options['dyncontel_field_singleproduct_blank'])) {
                    if (!get_page_template_slug()) {
                        $my_template = DCE_PATH . '/template/woocommerce.php';
                    }
                    //$my_template = DCE_PATH . '/../elementor/modules/page-templates/templates/header-footer.php';
                    //wc_get_template( 'single-product.php' );
                    //$my_template = '';
                }
                if ($datopagina != 1 && !empty(self::$options['dyncontel_field_singleproduct'])) {
                    $my_template = DCE_PATH . '/template/woocommerce.php';
                }
            }
            // In caso di Archive Wooc
            if (is_product_category() || is_product_tag()) {

                if (!empty(self::$options['dyncontel_field_archiveproduct']) && !empty(self::$options['dyncontel_field_archiveproduct_blank'])) {
                    if (!get_page_template_slug()) {
                        if (!is_404()) {
                            $my_template = DCE_PATH . '/template/archive.php';
                        }
                    }
                    //$my_template = DCE_PATH . '/../elementor/modules/page-templates/templates/header-footer.php';
                    //$my_template = '';
                }
                if ($datopagina != 1 && !empty(self::$options['dyncontel_field_archiveproduct'])) {
                    if (!is_404()) {
                        $my_template = DCE_PATH . '/template/archive.php';
                    }
                }
            }
        }
        // -------------------------------------------------------- ATTACHMENTS
        //var_dump($single_template); die();
        if (is_attachment() && !get_page_template_slug()) {
            if (!empty(self::$options['dyncontel_field_singleattachment_blank'])) {
                $_blank = self::$options['dyncontel_field_singleattachment_blank'];
                if ($_blank == 1 || $_blank == '1') {
                    $_blank = 'header-footer';
                } // retrocompatibility
                $my_template = ELEMENTOR_PATH . 'modules/page-templates/templates/' . $_blank . '.php';
            }
        }
        // -------------------------------------------------------- SEARCH
        if (is_search()) {
            if (!empty(self::$options['dyncontel_field_archivesearch_template']) && !empty(self::$options['dyncontel_field_archivesearch'])) {
                $my_template = DCE_PATH . '/template/page_template/search.php';
            }
        }
        // -------------------------------------------------------- AUTHOR (è in Archive)
        if (is_author()) {
            if (!empty(self::$options['dyncontel_field_archiveuser_template']) && !empty(self::$options['dyncontel_field_archiveuser'])) {
                $single_template = DCE_PATH . '/template/user.php';
            }
        }
        // -------------------------------------------------------- In caso di HOME
        if (is_home() || ( function_exists('is_shop') && is_shop() )) {

            // Le home's di Archivio
            if (!empty(self::$options['dyncontel_field_archive' . get_post_type()]) && !empty(self::$options['dyncontel_field_archive' . get_post_type() . '_template'])) {
                if (!is_404()) {
                    $my_template = DCE_PATH . '/template/archive.php';
                }
            }

            // La home page
            if (is_page() && !get_page_template_slug()) {
                if (!empty(self::$options['dyncontel_field_singlepage_blank'])) {
                    $_blank = self::$options['dyncontel_field_singlepage_blank'];
                    if ($_blank == 1 || $_blank == '1') {
                        $_blank = 'header-footer';
                    } // retrocompatibility
                    $my_template = ELEMENTOR_PATH . 'modules/page-templates/templates/' . $_blank . '.php';
                }
            }
        }

        //if(is_shop()) $my_template = DCE_PATH . '/template/archive.php';
        //echo $my_template;
        return $my_template;
    }

    //
    // ---------------------------------------------------- The_Content (questo è il cuore dell'applicazione @p)
    //
    public function dce_elementor_remove_content_filter($content) {
        if (!empty(\Elementor\Frontend::THE_CONTENT_FILTER_PRIORITY)) {
            if (self::get_template_id()) {
                //echo ' -- pre elementor -- ';
                \Elementor\Frontend::instance()->remove_content_filter();
                global $wp_filter;
                if (!empty($wp_filter['the_content']->callbacks[\Elementor\Frontend::THE_CONTENT_FILTER_PRIORITY])) {
                    foreach ($wp_filter['the_content']->callbacks[\Elementor\Frontend::THE_CONTENT_FILTER_PRIORITY] as $key => $value) {
                        // 0000000025e4d7f4000000000dad223aapply_builder_in_content
                        if (strpos($key, 'apply_builder_in_content') !== false) {
                            unset($wp_filter['the_content']->callbacks[\Elementor\Frontend::THE_CONTENT_FILTER_PRIORITY][$key]);
                        }
                    }
                }
            }
        }
        //echo '<pre>';var_dump($wp_filter['the_content']->callbacks);echo '</pre>'; die();                    
        return $content;
    }

    public function dce_filter_the_content_in_the_main_loop($content) {
        //echo ' -- post elementor -- '; die();
        //if (!self::get_post_template_id()) {
        // if current post has not his Elementor Template
        $dce_default_template = self::get_template_id();
        if ($dce_default_template) {
            $content = self::get_template($dce_default_template);

            // fix Elementor PRO Post Content Widget
            static $did_posts = [];
            $did_posts[get_the_ID()] = true;

            add_filter('elementor/widget/render_content', array($this, 'fix_elementor_pro_post_content_widget'), 11, 2);
        }
        //}
        return $content;
    }

    public function fix_elementor_pro_post_content_widget($content, $widget = false) {
        if ($widget && $widget->get_name() == 'theme-post-content') {
            //return $widget->get_name();
            return get_the_content();
        }
        return $content;
    }

    public static function get_post_template_id() {
        if (is_singular()) {
            $queried_object = get_queried_object();
            if (!empty($queried_object) && get_class($queried_object) == 'WP_Post') {
                $post = get_post();
                if ($post) {
                    $post_id = $post->ID;
                    // check the Post if is built with Elementor
                    $elementor_data = get_post_meta($post->ID, '_elementor_edit_mode', true);
                    if ($elementor_data) {
                        $template_id = $post_id;
                        return $template_id;
                    }
                }
            }
        }
        return false;
    }

    public static function get_template_id($head = false) {

        if (self::$template_id) {
            return self::$template_id;
        }

        $dce_template = 0;
        /// Check if we're inside the main loop in a single post page.
        if (DCE_Helper::in_the_loop() && is_main_query() || $head) {

            global $post;

            $cptype = false;
            if ($post) {
                $cptype = $post->post_type;
                $cptaxonomy = get_post_taxonomies($post->ID);
            }

            $dce_default_template = '';
            $pagina_temlate = "";

            // ciclo i termini e ne ricavo l'id del template
            $taxonomyesRegistered = get_taxonomies(array('public' => true));

            //if ($cptype != 'elementor_library' && $cptype != 'oceanwp_library' && $cptype != 'ae_global_templates') {
            if ($cptype && !in_array($cptype, self::$supported_types)) {
                /* if(is_home()){
                  echo $cptype;
                  } */

                // ------ SINGULAR
                if (is_singular()) {
                    //
                    $custom_template = false;

                    // 1 - se nella pagina il metabox template è impostato diversamente da "default"
                    $datopagina = get_post_meta(get_the_ID(), 'dyncontel_elementor_templates', true);
                    //echo 'dato '.$datopagina;
                    if ($datopagina) {
                        if ($datopagina > 1) {
                            $dce_default_template = $datopagina; //get_the_ID();
                            //echo 'il metabox è diverso da default oppure NO... '.$dce_default_template;
                            //$tenpdyn = self::get_template($dce_default_template);
                            $dce_template = $dce_default_template;
                            $custom_template = true;
                        } else {
                            $custom_template = true;
                        }
                    }

                    // 2 - se esiste un template associato a un termine associato
                    if (!$custom_template) {
                        // leggo le txonomy del post (I)
                        foreach ($cptaxonomy as $chiave) {
                            // leggo il temine del post (II)
                            $terms_list_of_post = wp_get_post_terms(get_the_ID(), $chiave, array("fields" => "all"));
                            //
                            if (count($terms_list_of_post) > 0) {
                                //echo 'terms_list_of_post: ' . count($terms_list_of_post) . '<br /><br />';
                                //
                                foreach ($terms_list_of_post as $term_single) {
                                    //$term_single->term_id; //do something here
                                    //$dce_singleTerm_template = get_term_meta($term_single->term_id, 'dynamic_content_single', true);
                                    //$dce_blockTerm_template = get_term_meta($term_single->term_id, 'dynamic_content_block', true);
                                    $dce_default_template = get_term_meta($term_single->term_id, 'dynamic_content_single', true);
                                    if (!empty($dce_default_template)) {
                                        if ($dce_default_template > 1) {
                                            // cisì li sommo -----------------
                                            //$tenpdyn .= $pagina_temlate;
                                            // così uso solo l'ultimo --------
                                            //$tenpdyn = self::get_template($dce_default_template);
                                            $dce_template = $dce_default_template;
                                            $custom_template = true;
                                        }
                                        //$tenpdyn = 'sono basato su default'.get_option( 'dyncontel_options' )['dyncontel_field_single'.$cptype]; //dyncontel_field_single'.$cptype;
                                    }
                                }
                            }
                        }
                    }

                    // 3 - se esiste un template associato alla tassonomia collegata
                    if (!$custom_template) {
                        foreach ($cptaxonomy as $aTaxo) {
                            if (isset(self::$options['dyncontel_field_single_taxonomy_' . $aTaxo]) && is_taxonomy_hierarchical($aTaxo)) {

                                // leggo il temine del post (II)
                                $terms_list_of_post = wp_get_post_terms(get_the_ID(), $aTaxo, array("fields" => "all"));
                                if (count($terms_list_of_post) > 0) {
                                    //var_dump($terms_list_of_post);
                                    //echo count($terms_list_of_post);
                                    //echo $aTaxo;
                                    $dce_default_template = self::$options['dyncontel_field_single_taxonomy_' . $aTaxo];
                                    if (!empty($dce_default_template)) {
                                        //echo 'connnnn'.$dce_default_template;
                                        if ($dce_default_template > 1) {
                                            // così li sommo -----------------
                                            //$tenpdyn .= $pagina_temlate;
                                            // così uso solo l'ultimo --------
                                            //$tenpdyn = self::get_template($dce_default_template);
                                            $dce_template = $dce_default_template;
                                            $custom_template = true;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // 4 - se esiste un template associato al post type
                    if (!$custom_template && isset(self::$options['dyncontel_field_single' . $cptype])) {

                        // altrimenti il dato è prelevato dai settings di "DynamicaContent"
                        $dce_default_template = self::$options['dyncontel_field_single' . $cptype];
                        //echo $dce_default_template.' '.$cptype;
                        //var_dump(self::$options);
                        //se il term ha un valore diverso da default ....
                        //$dce_default_template = get_term_meta($tag->term_id, 'dynamic_content_single', true)
                        if (!empty($dce_default_template)) {

                            if ($cptype != 'product' || !function_exists('is_product')) {
                                //
                                if ($dce_default_template > 1) {
                                    //$tenpdyn = 'sono basato su default'.get_option( 'dyncontel_options' )['dyncontel_field_single'.$cptype]; //dyncontel_field_single'.$cptype;
                                    //$tenpdyn = self::get_template($dce_default_template);
                                    $dce_template = $dce_default_template;
                                    $custom_template = true;
                                }
                            }
                        }
                    }

                    //if($datopagina == 1) $tenpdyn = $content;
                }

                // ------ ENTRY o archive Blocks --------------
                if (is_archive() || is_home()) {
                    if (!is_author()) {
                        //echo 'sono nella pagina archivio<br>';
                        //$tenpdyn = "sono ARCHIVIO";
                        // 4 - Type
                        if (!empty(self::$options['dyncontel_field_archive' . $cptype])) {
                            $dce_default_template = self::$options['dyncontel_field_archive' . $cptype];
                        }

                        //
                        if (!is_post_type_archive() && !is_home()) {
                            // ---------------------------------------
                            // qui sono nell'archivio del termine
                            // ---------------------------------------
                            //echo "qui sono nell'archivio del termine ";
                            //var_dump(is_archive());
                            // 3 - Taxonomy
                            foreach ($cptaxonomy as $chiave) {
                                // 3 - Taxonomy
                                if (isset(self::$options['dyncontel_field_archive_taxonomy_' . $chiave])) {
                                    $dce_default_template_taxo = self::$options['dyncontel_field_archive_taxonomy_' . $chiave];
                                    if (!empty($dce_default_template_taxo) && $dce_default_template_taxo > 0) {
                                        $dce_default_template = $dce_default_template_taxo;
                                    }
                                }
                            }
                            if (is_tax() || is_category() || is_tag()) {
                                //var_dump(get_queried_object());
                                $termine_id = get_queried_object()->term_id;
                                $chiave = get_queried_object()->taxonomy;
                                if (in_array($chiave, $cptaxonomy)) {
                                    // 3 bis - Taxonomy Current
                                    if (isset(self::$options['dyncontel_field_archive_taxonomy_' . $chiave])) {
                                        $dce_default_template_taxo = self::$options['dyncontel_field_archive_taxonomy_' . $chiave];
                                        if (!empty($dce_default_template_taxo) && $dce_default_template_taxo > 0) {
                                            $dce_default_template = $dce_default_template_taxo;
                                        }
                                    }
                                }

                                // 2 - Termine
                                //$terms_list_of_post = wp_get_post_terms(get_the_ID(), $chiave, array("fields" => "all"));
                                /* if ( count($terms_list_of_post) ) {
                                  //echo 'terms_list_of_post: ' . $chiave . ' - '. count($terms_list_of_post) . '<br /><br />';
                                  foreach ($terms_list_of_post as $term_single) { */
                                $dce_default_template_term = get_term_meta($termine_id, 'dynamic_content_block', true);
                                //echo 'termine'.$dce_default_template_term;
                                if (!empty($dce_default_template_term) && $dce_default_template_term > 1) {
                                    $dce_default_template = $dce_default_template_term;
                                }
                                /* }
                                  } */
                            }
                        } else {
                            // ---------------------------------------
                            // qui sono nella home page dell'archivio
                            // ---------------------------------------

                            foreach ($cptaxonomy as $chiave) {
                                //dyncontel_field_archive_taxonomy_xxxx
                                //echo $chiave.' - '.self::$options['dyncontel_field_archive_taxonomy_' . $chiave];
                                // 3 - Tayonomy
                                if (isset(self::$options['dyncontel_field_archive_taxonomy_' . $chiave])) {
                                    $dce_default_template_taxo = self::$options['dyncontel_field_archive_taxonomy_' . $chiave];
                                    if (!empty($dce_default_template_taxo) && $dce_default_template_taxo > 0) {
                                        $dce_default_template = $dce_default_template_taxo;
                                    }
                                }
                            }
                            foreach ($cptaxonomy as $chiave) {
                                // 2 - Termine

                                $terms_list_of_post = wp_get_post_terms(get_the_ID(), $chiave, array("fields" => "all"));
                                if (count($terms_list_of_post) > 0) {

                                    foreach ($terms_list_of_post as $term_single) {
                                        if ($term_single->taxonomy != 'post_format')
                                        //echo $term_single->taxonomy;
                                            $dce_default_template_term = get_term_meta($term_single->term_id, 'dynamic_content_block', true);
                                        //echo  $dce_default_template_term;
                                        if (!empty($dce_default_template_term) && $dce_default_template_term > 1) {
                                            $dce_default_template = $dce_default_template_term;
                                        }
                                    }
                                }
                            }
                        }


                        // se ho specificato un template per il block nell'archive glielo forzo
                        /* $dce_default_template_block = get_post_meta(get_the_ID(), 'dynamic_content_block', true);
                          if ($dce_default_template_block) {
                          $dce_default_template = $dce_default_template_block;
                          } */


                        // > conclusione
                        if (!empty($dce_default_template)) {
                            //echo 'connnnn'.$dce_default_template;
                            if ($dce_default_template > 1) {
                                //$tenpdyn = self::get_template($dce_default_template);
                                $dce_template = $dce_default_template;
                            }
                            //$tenpdyn = 'sono basato su default'.get_option( 'dyncontel_options' )['dyncontel_field_archive'.$cptype]; //dyncontel_field_single'.$cptype;
                            //$tenpdyn = $pagina_temlate;
                        }
                        //echo $tenpdyn;
                    }
                }
                if (is_attachment()) {
                    //echo 'sono nella pagina attachment<br>';
                    $dce_default_template = self::$options['dyncontel_field_singleattachment'];
                    if (!empty($dce_default_template)) {
                        if ($dce_default_template > 1) {
                            //$tenpdyn = self::get_template($dce_default_template);
                            $dce_template = $dce_default_template;
                        }
                        //$tenpdyn = $pagina_temlate;
                    }
                }
                if (is_author()) {
                    //echo 'sono nella pagina user<br>';
                    $dce_default_template = self::$options['dyncontel_field_archiveuser'];
                    if (!empty($dce_default_template)) {
                        if ($dce_default_template > 1) {
                            //$tenpdyn = self::get_template($dce_default_template);
                            $dce_template = $dce_default_template;
                        }
                        //$tenpdyn = $pagina_temlate;
                    }
                }
                if (is_search()) {
                    //echo 'sono nella pagina search<br>';
                    $dce_default_template = self::$options['dyncontel_field_archivesearch'];
                    if (!empty($dce_default_template)) {
                        if ($dce_default_template > 1) {
                            //$tenpdyn = self::get_template($dce_default_template);
                            $dce_template = $dce_default_template;
                        }
                        //$tenpdyn = $pagina_temlate;
                    }
                }
            } // end/ se non sono in un "my template" Elementor oppure OceanWP
        }

        self::$template_id = $dce_template;
        return $dce_template;
    }

    public static function get_template($template, $inline_css = false) {
        $get_id = apply_filters('wpml_object_id', $template, 'elementor_library', true);
        // Check if the template is created with Elementor
        $elementor = get_post_meta($get_id, '_elementor_edit_mode', true);
        $pagina_template = '';
        // If Elementor
        if (class_exists('Elementor\Plugin') && $elementor) {
            // Dalla versione 0.1.0 (consigliavano questo) .. ma ha dei limiti ..per tutti i siti fino ad oggi ho fatto così ... e funzione per i template, ma non per i contenuti diretti.
            $start = microtime(true);
            // Dalla versione 0.6.0 dopo ore di ragionamenti vado ad usare questo per generare il contenuto di Elementor. Questo mi permette di usare un contenuto Elementor dentro a un contenuto nel template ... vedi (elementor/includes/frontend.php)
            $pagina_template = \Elementor\Plugin::instance()->frontend->get_builder_content($get_id, $inline_css);
            //$pagina_template = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($get_id, $inline_css);

            $pagina_template = self::_css_class_fix($pagina_template, $get_id);
            //var_dump(microtime(true)-$start);
        } else {
            //echo '<div style="margin: 40px; font-size:50px;">contenuto nativo</div>';
            //var_dump($get_id);
            $post_n = get_post($get_id);
            $content_n = apply_filters('the_content', $post_n->post_content);
            echo $content_n;
        }

        return $pagina_template;
    }

    public function _fix_style($element) {
        //if (DCE_Helper::in_the_loop()) {
        $css = '';
        $settings = $element->get_settings_for_display();
        //$settings = $element->get_settings();
        //var_dump($element->get_id());
        //var_dump(get_the_ID());

        //$post_id = get_the_ID();
        $element_id = $element->get_id();
        $element_controls = $element->get_controls();
        //var_dump($element_controls);
        $queried_object_type = DCE_Helper::get_queried_object_type();
        $queried_object_id = get_queried_object_id();
        
        if (DCE_Helper::is_plugin_active('advanced-custom-fields-pro')) {
            $row = acf_get_loop('active');
            if ($row) {
                $queried_object_type = 'row';                
                $queried_object_id = get_row_index();
            }
        }

        if (!empty($settings['__dynamic__'])) {
            //var_dump($settings['__dynamic__']);
            foreach ($settings['__dynamic__'] as $dkey => $dsetting) {
                //$rkeys = array('desktop' => $dkey, 'tablet' => $dkey.'_tablet', 'mobile' => $dkey.'_mobile');
                $tmp = explode('_', $dkey);
                $device = array_pop($tmp);
                $rkeys = array('desktop' => $dkey);
                if ($device == 'tablet' || $device == 'mobile') {
                    $rkeys = array($device => $dkey);
                }
                foreach ($rkeys as $rkey => $rvalue) {
                    //$selector = '.elementor .dce-elementor-' . $post_id;
                    $selector = '.elementor .dce-elementor-' . $queried_object_type . '-' . $queried_object_id;

                    if ($rkey != 'desktop') {
                        $selector = '[data-elementor-device-mode="' . $rkey . '"] ' . $selector;
                    }
                    if (isset($element_controls[$rvalue])) {
                        if (!empty($element_controls[$dkey]['selectors'])) {
                            foreach ($element_controls[$dkey]['selectors'] as $skey => $svalue) {
                                $rule_value = false;
                                $rule_selector = str_replace('{{WRAPPER}}', $selector . ' .elementor-element.elementor-element-' . $element_id, $skey);
                                if (!empty($settings[$rvalue])) {
                                    if (is_array($settings[$rvalue])) {
                                        if (!empty($settings[$rvalue]['url'])) {
                                            $rule_value = str_replace('{{URL}}', $settings[$rvalue]['url'], $svalue);
                                        }
                                        // TODO (other replacement)
                                    } else {
                                        $rule_value = str_replace('{{VALUE}}', $settings[$rvalue], $svalue);
                                    }
                                }
                                if ($rule_value) {
                                    $css .= $rule_selector . '{' . $rule_value . '}';
                                } else {
                                    //var_dump($dkey);
                                }
                            }
                        }
                        //var_dump($css);
                        // RESPONSIVE
                    }
                }
            }
        }

        if ($css) {
            $css = '<style>' . $css . '</style>';
            if (!wp_doing_ajax()) {
                $css = \DynamicContentForElementor\DCE_Assets::dce_enqueue_style('template-fix-' . $element->get_id() . '-inline', $css);
            }
            echo $css;
        }

        //}
    }

    public static function _css_class_fix($content = '', $template_id = 0) {
        if ($content) {
            $template_html_id = DCE_Helper::get_template_id_by_html($content);
            if ($template_id && $template_id != $template_html_id) {
                $content = str_replace('class="elementor elementor-' . $template_html_id . ' ', 'class="elementor elementor-' . $template_id . ' ', $content);
            } else {
                $template_id = $template_html_id;
            }

            /*
              $document = \Elementor\Plugin::$instance->documents->get_doc_for_frontend( $template_id );
              if ($document) {
              $document_id = $document->get_id();
              if ($template_id != $document_id) {
              $content = str_replace('class="elementor elementor-'.$document_id.'"', 'class="elementor elementor-'.$template_id.'"', $content);
              }
              }

              $template_system_id = self::get_template_id();
              if ($template_id != $template_system_id) {
              $content = str_replace('class="elementor elementor-'.$template_system_id.'"', 'class="elementor elementor-'.$template_id.'"', $content);
              }
             */

            if ($template_id) {
                /* if (DCE_Helper::in_the_loop()) {
                  $post_id = get_the_ID();
                  if (strpos($content, 'dce-elementor-'.$post_id) === false) {
                  $content = str_replace('class="elementor elementor-'.$template_id.' ', 'class="elementor elementor-'.$template_id.' dce-elementor-'.$post_id.' ', $content);
                  $content = str_replace('class="elementor elementor-'.$template_id.'"', 'class="elementor elementor-'.$template_id.' dce-elementor-'.$post_id.'"', $content);

                  //$content = str_replace('data-elementor-id="', 'data-post-id="'.$post_id.'" data-elementor-id="', $content); // popup broke css

                  //$content = str_replace('data-id="', 'data-post-id="'.$post_id.'" data-id="', $content);
                  $pieces = explode('data-elementor-id="', $content);
                  foreach($pieces as $pkey => $apiece) {
                  if ($pkey) {
                  list($eid, $more) = explode('"', $apiece, 2);
                  $new_content .= 'data-elementor-id="'.$eid.'" data-post-id="'.$post_id.'"'.$more;
                  } else {
                  $new_content = $apiece;
                  }
                  }
                  $content = $new_content;
                  $content = str_replace('data-post-id="'.$post_id.'" data-post-id="'.$post_id.'"', 'data-post-id="'.$post_id.'"', $content);
                  //$content = str_replace('data-post-id="'.$post_id.'" data-post-id="', 'data-post-id="'.$post_id.'-', $content);
                  $content = str_replace('data-post-id="'.$post_id.'" data-post-id="', 'data-post-id="', $content);
                  }
                  } */

                $queried_object = get_queried_object();
                $queried_object_id = get_queried_object_id();
                $queried_object_type = DCE_Helper::get_queried_object_type();
                if ($queried_object_type == 'post') {
                    $queried_object_id = get_the_ID();
                }
                
                if (DCE_Helper::is_plugin_active('advanced-custom-fields-pro')) {
                    $row = acf_get_loop('active');
                    if ($row) {
                        $queried_object_type = 'row';                
                        $queried_object_id = get_row_index();
                    }
                }
                
                //if (strpos($content, 'dce-elementor-'.$queried_object_type.'-'.$queried_object_id) === false) {
                $content = str_replace('class="elementor elementor-' . $template_id . ' ', 'class="elementor elementor-' . $template_id . ' dce-elementor-' . $queried_object_type . '-' . $queried_object_id . ' ', $content);
                $content = str_replace('class="elementor elementor-' . $template_id . '"', 'class="elementor elementor-' . $template_id . ' dce-elementor-' . $queried_object_type . '-' . $queried_object_id . '"', $content);
                $pieces = explode('data-elementor-id="', $content, 2);
                foreach ($pieces as $pkey => $apiece) {
                    if ($pkey) {
                        list($eid, $more) = explode('"', $apiece, 2);
                        $new_content .= 'data-elementor-id="' . $eid . '" data-' . $queried_object_type . '-id="' . $queried_object_id . '" data-obj-id="' . $queried_object_id . '"' . $more;
                    } else {
                        $new_content = $apiece;
                    }
                }
                $content = $new_content;
                
                $content = str_replace('data-' . $queried_object_type . '-id="' . $queried_object_id . '" data-' . $queried_object_type . '-id="' . $queried_object_id . '"', 'data-' . $queried_object_type . '-id="' . $queried_object_id . '"', $content);
                $content = str_replace('data-' . $queried_object_type . '-id="' . $queried_object_id . '" data-' . $queried_object_type . '-id="', 'data-' . $queried_object_type . '-id="', $content);
                $content = str_replace('data-' . $queried_object_type . '-id="' . $queried_object_id . '" data-obj-id="' . $queried_object_id . '" data-' . $queried_object_type . '-id="' . $queried_object_id . '" data-obj-id="' . $queried_object_id . '"', 'data-' . $queried_object_type . '-id="' . $queried_object_id . '" data-obj-id="' . $queried_object_id . '"', $content);
                //}
            }
        }
        return $content;
    }

    public function dce_get_header($name) {
        require DCE_PATH . 'template/header.php';
        $templates = [];
        $name = (string) $name;
        if ('' !== $name) {
            $templates[] = "header-{$name}.php";
        }
        $templates[] = 'header.php';
        // Avoid running wp_head hooks again
        remove_all_actions('wp_head');
        ob_start();
        // It cause a `require_once` so, in the get_header it self it will not be required again.
        locate_template($templates, true);
        $header = ob_get_clean();
        //var_dump($header); die();
        echo '<header id="site-header" itemscope="itemscope" itemtype="http://schema.org/WPHeader">' . $header . '</header>';
    }

    public function dce_get_footer($name) {
        require DCE_PATH . 'template/footer.php';
        $templates = [];
        $name = (string) $name;
        if ('' !== $name) {
            $templates[] = "footer-{$name}.php";
        }
        $templates[] = 'footer.php';
        ob_start();
        // It cause a `require_once` so, in the get_header it self it will not be required again.
        locate_template($templates, true);
        $footer = ob_get_clean();
        //var_dump($footer); die();
        echo '<footer id="footer" class="site-footer" itemscope="itemscope" itemtype="http://schema.org/WPFooter">' . $footer . '</footer>';
    }

}
