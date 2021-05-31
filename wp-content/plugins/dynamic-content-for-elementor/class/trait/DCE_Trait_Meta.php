<?php

namespace DynamicContentForElementor;

/**
 * Description of DCE_Trait_Plugin
 *
 */
trait DCE_Trait_Meta {

    public static $meta_fields = [];

    public static function get_acf_types() {
        return array("text", "textarea", "number", "range", "email", "url", "password", "image", "file", "wysiwyg", "oembed", "gallery", "select", "checkbox", "radio", "button_group", "true_false", "link", "post_object", "page_link", "relationship", "taxonomy", "user", "google_map", "date_picker", "date_time_picker", "time_picker", "color_picker", "message", "accordion", "tab", "group", "repeater", "flexible_content", "clone");
    }

    public static function get_pods_types() {
        return array("text", "website", "phone", "email", "password", "paragraph", "wysiwyg", "code", "datetime", "date", "time", "number", "currency", "file", "oembed", "pick", "boolean", "color");
    }

    public static function get_toolset_types() {
        return array("audio", "colorpicker", "email", "embed", "file", "image", "numeric", "phone", "textarea", "textfield", "url", "video", "checkboxes", "checkbox", "date", "radio", "select", "skype", "wysiwyg", "multiple");
    }

    public static function get_acf_field_locations($aacf) {
        if (is_string($aacf)) {
            $aacf = self::get_acf_field_post($aacf);
        }
        if ($aacf) {
            $aacf_group = get_post($aacf->post_parent);
            if ($aacf_group) {
                if ($aacf_group->post_parent == 'acf-field') {
                    // may be in repeater or block or tab
                    $aacf_group = get_post($aacf->post_parent);
                }
                if ($aacf_group->post_parent == 'acf-field-group') {
                    return self::get_acf_group_locations($aacf_group);
                }
            }
        }
        return array();
    }

    public static function get_acf_group_locations($aacf_group) {
        $locations = array();
        if (is_string($aacf_group)) {
            $acf_groups = get_posts(array('post_type' => 'acf-field-group', 'post_excerpt' => $aacf_group, 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
            if (!empty($acf_groups)) {
                $aacf_group = reset($acf_groups);
            } else {
                return false;
            }
        }
        $aacf_meta = maybe_unserialize($aacf_group->post_content);
        if (!empty($aacf_meta['location'])) {
            foreach ($aacf_meta['location'] as $gkey => $gvalue) {
                foreach ($gvalue as $rkey => $rvalue) {
                    $pieces = explode('_', $rvalue['param']);
                    $location = reset($pieces);
                    $locations[$location] = $location;

                    if ($location == 'page') {
                        $locations['post'] = 'post';
                    }
                    if ($location == 'current') {
                        $locations['user'] = 'user';
                    }
                }
            }
        }
        return $locations;
    }

    public static function get_user_metas($grouped = false, $like = '', $info = true) {
        global $wp_meta_keys;

        $userMetas = $userMetasGrouped = array();

        // ACF
        $acf_groups = get_posts(array('post_type' => 'acf-field-group', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
        if (!empty($acf_groups)) {
            foreach ($acf_groups as $aacf_group) {
                $is_user_group = in_array('user', self::get_acf_group_locations($aacf_group));
                $aacf_meta = maybe_unserialize($aacf_group->post_content);
                if ($is_user_group) {
                    $acf = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $aacf_group->ID, 'suppress_filters' => false));
                    if (!empty($acf)) {
                        foreach ($acf as $aacf) {
                            $aacf_meta = maybe_unserialize($aacf->post_content);
                            if ($like) {
                                $pos_key = stripos($aacf->post_excerpt, $like);
                                $pos_name = stripos($aacf->post_title, $like);
                                if ($pos_key === false && $pos_name === false) {
                                    continue;
                                }
                            }
                            $field_name = $aacf->post_title;
                            if ($info) {
                                $field_name .= ' [' . $aacf_meta['type'] . ']';
                            }
                            $userMetas[$aacf->post_excerpt] = $field_name;
                            $userMetasGrouped['ACF'][$aacf->post_excerpt] = $userMetas[$aacf->post_excerpt];
                        }
                    }
                    //echo '<pre>';var_dump($aacf_meta);echo '</pre>';
                }
            }
        }

        // PODS
        /* $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
          if (!empty($pods)) {
          foreach ($pods as $apod) {
          $type = get_post_meta($apod->ID, 'type', true);
          $userMetas[$apod->post_name] = $apod->post_title.' ['.$type.']';
          $userMetasGrouped['PODS'][$apod->post_name] = $userMetas[$apod->post_name];
          }
          } */

        // TOOLSET
        /* $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $userMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $userMetasGrouped['TOOLSET'][$atool['meta_key']] = $userMetas[$atool['meta_key']];
          }
          }
          } */

        // MANUAL
        global $wpdb;
        $table = $wpdb->prefix . 'usermeta';
        //defined( 'CUSTOM_USER_TABLE' );
        if (defined('CUSTOM_USER_META_TABLE')) {
            $table = CUSTOM_USER_META_TABLE;
        }
        $query = 'SELECT DISTINCT meta_key FROM ' . $table;
        if ($like) {
            $query .= " WHERE meta_key LIKE '%" . $like . "%'";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $metas = array();
            foreach ($results as $key => $auser) {
                $metas[$auser->meta_key] = $auser->meta_key;
            }
            ksort($metas);
            //$manual_metas = array_diff_key($metas, $userMetas);
            $manual_metas = $metas;
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 1) == '_') {
                    $ameta = $tmp = substr($ameta, 1);
                    if (in_array($tmp, $manual_metas)) {
                        continue;
                    }
                }
                if (!isset($postMetas[$ameta])) {
                    $userMetas[$ameta] = $ameta;
                    $userMetasGrouped['META'][$ameta] = $ameta;
                }
            }
        }

        //$userMetas = get_user_meta();
        //var_dump($userMetas); die();
        //$userMetasGrouped['NATIVE'] = $userMetas;

        if ($grouped) {
            return $userMetasGrouped;
        }

        return $userMetas;
    }

    public static function get_term_metas($grouped = false, $like = '') {
        global $wp_meta_keys;

        $termMetas = $termMetasGrouped = array();

        // ACF
        $acf_groups = get_posts(array('post_type' => 'acf-field-group', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
        if (!empty($acf_groups)) {
            foreach ($acf_groups as $aacf_group) {
                $is_term_group = in_array('taxonomy', self::get_acf_group_locations($aacf_group));
                $aacf_meta = maybe_unserialize($aacf_group->post_content);
                if ($is_term_group) {
                    $acf = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $aacf_group->ID, 'suppress_filters' => false));
                    if (!empty($acf)) {
                        foreach ($acf as $aacf) {
                            $aacf_meta = maybe_unserialize($aacf->post_content);
                            if ($like) {
                                $pos_key = stripos($aacf->post_excerpt, $like);
                                $pos_name = stripos($aacf->post_title, $like);
                                if ($pos_key === false && $pos_name === false) {
                                    continue;
                                }
                            }
                            $field_name = $aacf->post_title;
                            if ($info) {
                                $field_name .= ' [' . $aacf_meta['type'] . ']';
                            }
                            $termMetas[$aacf->post_excerpt] = $field_name;
                            $termMetasGrouped['ACF'][$aacf->post_excerpt] = $termMetas[$aacf->post_excerpt];
                        }
                    }
                    //echo '<pre>';var_dump($aacf_meta);echo '</pre>';
                }
            }
        }

        // PODS
        /* $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
          if (!empty($pods)) {
          foreach ($pods as $apod) {
          $type = get_post_meta($apod->ID, 'type', true);
          $userMetas[$apod->post_name] = $apod->post_title.' ['.$type.']';
          $userMetasGrouped['PODS'][$apod->post_name] = $userMetas[$apod->post_name];
          }
          } */

        // TOOLSET
        /* $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $userMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $userMetasGrouped['TOOLSET'][$atool['meta_key']] = $userMetas[$atool['meta_key']];
          }
          }
          } */

        // MANUAL
        global $wpdb;
        $query = 'SELECT DISTINCT meta_key FROM ' . $wpdb->prefix . 'termmeta';
        if ($like) {
            $query .= " WHERE meta_key LIKE '%" . $like . "%'";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $metas = array();
            foreach ($results as $key => $aterm) {
                $metas[$aterm->meta_key] = $aterm->meta_key;
            }
            ksort($metas);
            //$manual_metas = array_diff_key($metas, $userMetas);
            $manual_metas = $metas;
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 1) == '_') {
                    $ameta = $tmp = substr($ameta, 1);
                    if (in_array($tmp, $manual_metas)) {
                        continue;
                    }
                }
                if (!isset($postMetas[$ameta])) {
                    $termMetas[$ameta] = $ameta;
                    $termMetasGrouped['META'][$ameta] = $ameta;
                }
            }
        }

        if ($grouped) {
            return $termMetasGrouped;
        }

        return $termMetas;
    }

    public static function get_post_meta($post_id, $meta_key, $single = false, $plugin = true, $fallback = true) {        
        
        $meta_value = false;
        
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        if (!$plugin || $fallback) {
            $meta_value = get_post_meta($post_id, $meta_key, $single);
        }
        
        if ($plugin) {
            // https://docs.elementor.com/article/381-elementor-integration-with-acf
            if (DCE_Helper::is_plugin_active('acf')) { // && in_array($original_type, $acf_types)) {
                //var_dump(array_keys(self::get_acf_fields(false, array('repeater', 'group'))));
                $acf_fields = self::get_acf_fields();
                //echo $meta_key; var_dump($acf_fields);
                if (!empty($acf_fields) && array_key_exists($meta_key, $acf_fields)) {
                    // https://www.advancedcustomfields.com/resources/
                    $meta_value = self::get_acf_field_value($meta_key, $post_id);
                    //var_dump($meta_value);
                    //$render_type = 'plugin';
                }
            }

            // https://docs.elementor.com/article/385-elementor-integration-with-pods
            if (DCE_Helper::is_plugin_active('pods')) { // && in_array($original_type, $pods_types)) {
                $pods_fields = array_keys(self::get_pods_fields());
                //var_dump($pods_fields);
                //var_dump(in_array($meta_key, $pods_fields));
                if (!empty($pods_fields) && in_array($meta_key, $pods_fields, true)) {
                    $meta_value = pods_field_display($meta_key, $post_id);
                    //$render_type = 'plugin';
                }
            }

            // ToolSet
            if (DCE_Helper::is_plugin_active('wpcf')) { // && in_array($original_type, $toolset_types)) {
                $toolset_fields = array_keys(self::get_toolset_fields());
                if (!empty($toolset_fields) && in_array($meta_key, $toolset_fields, true)) {
                    //$meta_value = types_render_field($meta_key, array('post_id' => $post_id));
                    //$render_type = 'plugin';
                }
            }
        }

        //var_dump($meta_value);
        return $meta_value;
    }

    public static function get_post_meta_name($meta_key) {

        // ACF
        if (self::is_plugin_active('acf')) {
            $acf = get_field_object($meta_key);
            if ($acf) {
                return $acf['label'];
            }
        }


        // PODS
        if (self::is_plugin_active('pods')) {
            //$pods = PODS::label($meta_key);
            $pods = get_page_by_path($meta_key, OBJECT, '_pods_field');
            if ($pods) {
                return $pods->post_title;
            }
        }

        /*
          // TOOLSET
          $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $postMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $postMetasGrouped['TOOLSET'][$atool['meta_key']] = $postMetas[$atool['meta_key']];
          }
          }
          } */

        return $meta_key;
    }

    public static function get_meta_type($meta_key, $meta_value = null) {

        $meta_type = 'text';

        // ACF
        if (self::is_plugin_active('acf')) {
            global $wpdb;
            $sql = "SELECT post_content FROM " . $wpdb->prefix . "posts WHERE post_excerpt = '" . $meta_key . "' AND post_type = 'acf-field';";
            $acf_result = $wpdb->get_col($sql);
            if (!empty($acf_result)) {
                $acf_content = reset($acf_result);
                $acf_field_object = maybe_unserialize($acf_content);
                //$acf = get_field_object($meta_key);
                if ($acf_field_object && is_array($acf_field_object) && isset($acf_field_object['type'])) {
                    $meta_type = $acf_field_object['type'];
                    //return $meta_type;
                }
            }
        }

        // PODS
        if (self::is_plugin_active('pods')) {
            //$pods = PODS::label($meta_key);
            $pods = get_page_by_path($meta_key, OBJECT, '_pods_field');
            if ($pods) {
                $meta_type = get_post_meta($apod->ID, 'type', true);
                //return $meta_type;
            }
        }

        /*
          // TOOLSET
          $toolset = get_option('wpcf-fields', false);
          if ($toolset) {
          $toolfields = maybe_unserialize($toolset);
          if (!empty($toolfields)) {
          foreach ($toolfields as $atool) {
          $postMetas[$atool['meta_key']] = $atool['name'].' ['.$atool['type'].']';
          $postMetasGrouped['TOOLSET'][$atool['meta_key']] = $postMetas[$atool['meta_key']];
          }
          }
          } */

        if ($meta_value) {
            if ($meta_type != 'text') {
                switch ($meta_type) {
                    case 'gallery':
                        return 'image';

                    case 'embed':
                        if (strpos($meta_value, 'https://www.youtube.com/') !== false || strpos($meta_value, 'https://youtu.be/') !== false) {
                            return 'youtube';
                        }

                    default:
                        return $meta_type;
                }
            } else {
                if ($meta_key == 'avatar') {
                    return 'image';
                }

                if (is_numeric($meta_value)) {
                    return 'number';
                }
                // Validate e-mail
                if (filter_var($meta_value, FILTER_VALIDATE_EMAIL) !== false) {
                    return 'email';
                }

                // Youtube url
                if (is_string($meta_value)) {
                    if (strpos($meta_value, 'https://www.youtube.com/') !== false || strpos($meta_value, 'https://youtu.be/') !== false) {
                        return 'youtube';
                    }
                    $ext = pathinfo($meta_value, PATHINFO_EXTENSION);
                    if (in_array($ext, array('mp3', 'm4a', 'ogg', 'wav', 'wma')) === true) {
                        return 'audio';
                    }
                    if (in_array($ext, array('mp4', 'm4v', 'webm', 'ogv', 'wmv', 'flv')) === true) {
                        return 'video';
                    }

                    // Validate url
                    if (filter_var($meta_value, FILTER_SANITIZE_URL) !== false) {
                        //return 'url';
                    }
                    if (substr($meta_value, 0, 7) == 'http://' || substr($meta_value, 0, 8) == 'https://') {
                        return 'url';
                    }
                }
            }
        }

        return $meta_type;
    }

    public static function get_user_meta($user_id, $meta_key, $fallback = true, $single = false) {
        $meta_value = false;

        if ($fallback) {
            $meta_value = get_post_meta($user_id, $meta_key, $single);
        }

        // https://docs.elementor.com/article/381-elementor-integration-with-acf
        if (DCE_Helper::is_plugin_active('acf')) { // && in_array($original_type, $acf_types)) {
            //var_dump(array_keys(self::get_acf_fields(array('repeater', 'group'))));
            $acf_fields = array_keys(self::get_acf_fields());
            //var_dump($acf_fields);
            if (!empty($acf_fields) && in_array($meta_key, $acf_fields, true)) {
                // https://www.advancedcustomfields.com/resources/
                $meta_value = get_field($meta_key, 'user_' . $user_id);
                //var_dump($meta_value);
                //$render_type = 'plugin';
            }
        }

        // https://docs.elementor.com/article/385-elementor-integration-with-pods
        if (DCE_Helper::is_plugin_active('pods')) { // && in_array($original_type, $pods_types)) {
            $pods_fields = array_keys(self::get_pods_fields());
            //var_dump($pods_fields);
            //var_dump(in_array($meta_key, $pods_fields));
            if (!empty($pods_fields) && in_array($meta_key, $pods_fields, true)) {
                $meta_value = pods_field_display($meta_key, $user_id);
                //$render_type = 'plugin';
            }
        }

        // ToolSet
        if (DCE_Helper::is_plugin_active('wpcf')) { // && in_array($original_type, $toolset_types)) {
            $toolset_fields = array_keys(self::get_toolset_fields());
            if (!empty($toolset_fields) && in_array($meta_key, $toolset_fields, true)) {
                //$meta_value = types_render_field($meta_key, array('user_id' => $user_id));
                //$render_type = 'plugin';
            }
        }

        //var_dump($meta_value);
        return $meta_value;
    }

    public static function get_post_metas($grouped = false, $like = '', $info = true) {
        global $wp_meta_keys;

        $postMetas = $postMetasGrouped = array();

        // REGISTERED in FUNCTION
        $cpts = self::get_post_types();
        foreach ($cpts as $ckey => $cvalue) {
            $cpt_metas = get_registered_meta_keys($ckey);
            if (!empty($cpt_metas)) {
                foreach ($cpt_metas as $fkey => $actpmeta) {
                    if ($like) {
                        $pos_key = stripos($fkey, $like);
                        if ($pos_key === false) {
                            continue;
                        }
                    }
                    $field_name = $fkey;
                    if ($info) {
                        $field_name .= ' [' . $actpmeta['type'] . ']';
                    }
                    $postMetas[$fkey] = $field_name;
                    $postMetasGrouped['CPT_' . $ckey][$fkey] = $field_name;
                }
            }
        }

        // ACF
        if (self::is_plugin_active('acf')) {
            // ACF
            $acf_groups = get_posts(array('post_type' => 'acf-field-group', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
            if (!empty($acf_groups)) {
                foreach ($acf_groups as $aacf_group) {
                    $is_post_group = in_array('post', self::get_acf_group_locations($aacf_group));
                    $aacf_meta = maybe_unserialize($aacf_group->post_content);
                    if ($is_post_group) {
                        $acf = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $aacf_group->ID, 'suppress_filters' => false));
                        if (!empty($acf)) {
                            foreach ($acf as $aacf) {
                                $aacf_meta = maybe_unserialize($aacf->post_content);
                                if ($like) {
                                    $pos_key = stripos($aacf->post_excerpt, $like);
                                    $pos_name = stripos($aacf->post_title, $like);
                                    if ($pos_key === false && $pos_name === false) {
                                        continue;
                                    }
                                }
                                $field_name = $aacf->post_title;
                                if ($info) {
                                    $field_name .=  ' [' . $aacf_meta['type'] . ']';
                                }
                                $postMetas[$aacf->post_excerpt] = $field_name;
                                $postMetasGrouped['ACF'][$aacf->post_excerpt] = $postMetas[$aacf->post_excerpt];
                            }
                        }
                    }
                }
            }
        }

        // PODS
        if (self::is_plugin_active('pods')) {
            $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
            if (!empty($pods)) {
                foreach ($pods as $apod) {
                    $type = get_post_meta($apod->ID, 'type', true);
                    $field_name = $apod->post_title;
                    if ($info) {
                        $field_name .=  ' [' . $type . ']';
                    }
                    $postMetas[$apod->post_name] = $field_name;
                    $postMetasGrouped['PODS'][$apod->post_name] = $postMetas[$apod->post_name];
                }
            }
        }

        // TOOLSET
        if (self::is_plugin_active('wpcf')) {
            $toolset = get_option('wpcf-fields', false);
            if ($toolset) {
                $toolfields = maybe_unserialize($toolset);
                if (!empty($toolfields)) {
                    foreach ($toolfields as $atool) {
                        $field_name = $atool['name'];
                        if ($info) {
                            $field_name .=  ' [' . $atool['type'] . ']';
                        }
                        $postMetas[$atool['meta_key']] = $field_name;
                        $postMetasGrouped['TOOLSET'][$atool['meta_key']] = $postMetas[$atool['meta_key']];
                    }
                }
            }
        }

        // MANUAL
        global $wpdb;
        $query = 'SELECT DISTINCT meta_key FROM ' . $wpdb->prefix . 'postmeta';
        if ($like) {
            $query .= " WHERE meta_key LIKE '%" . $like . "%'";
        }
        $results = $wpdb->get_results($query);
        if (!empty($results)) {
            $metas = array();
            foreach ($results as $key => $apost) {
                $metas[$apost->meta_key] = $apost->meta_key;
            }
            ksort($metas);
            $manual_metas = array_diff_key($metas, $postMetas);
            foreach ($manual_metas as $ameta) {
                if (substr($ameta, 0, 8) == '_oembed_') {
                    continue;
                }
                /* if (substr($ameta, 0, 1) == '_') {
                  $ameta = $tmp = substr($ameta, 1);
                  if (in_array($tmp, $manual_metas)) {
                  continue;
                  }
                  } */
                if (!isset($postMetas[$ameta])) {
                    $postMetas[$ameta] = $ameta;
                    $postMetasGrouped['NATIVE'][$ameta] = $ameta;
                }
            }
        }

        if ($grouped) {
            return $postMetasGrouped;
        }

        return $postMetas;
    }

    public static function is_post_meta($meta_name = null) {
        $post_fields = array(
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_title',
            'post_excerpt',
            'post_status',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_content_filtered',
            'post_parent',
            'guid',
            'menu_order',
            'post_type',
            'post_mime_type',
            'comment_count',
        );

        if ($meta_name) {
            //$post_fields = self::get_post_fields();
            //var_dump($post_fields);
            if (in_array($meta_name, $post_fields)) { // || isset($post_fields[$meta_name])) {
                return false;
            }
        }
        return true;
    }

    public static function is_userdata($field_name = null) {
        $user_fields = array(
            'locale',
            'syntax_highlighting',
            'avatar',
            'nickname',
            'first_name',
            'last_name',
            'description',
            'rich_editing',
            'role',
            'jabber',
            'aim',
            'yim',
            'show_admin_bar_front',
        );
        if ($field_name) {
            if (in_array($field_name, $user_fields) || !self::is_user_meta($field_name)) {
                return true;
            }
        }
        return false;
    }

    public static function is_user_meta($meta_name = null) {
        $user_fields = array(
            'ID',
            'user_login',
            'user_pass',
            'user_nicename',
            'user_email',
            'user_url',
            'user_registered',
            'user_activation_key',
            'user_status',
            'display_name',
        );
        if ($meta_name) {
            //$post_fields = self::get_post_fields();
            //var_dump($post_fields);
            if (in_array($meta_name, $user_fields)) { // || isset($post_fields[$meta_name])) {
                return false;
            }
        }
        return true;
    }

    public static function is_term_meta($meta_name = null) {
        $term_fields = array(
            'term_id',
            'name',
            'slug',
            'term_group',
            'term_order',
            //'count',
        );
        if ($meta_name) {
            //$post_fields = self::get_post_fields();
            //var_dump($post_fields);
            if (in_array($meta_name, $term_fields)) { // || isset($post_fields[$meta_name])) {
                return false;
            }
        }
        return true;
    }
    
    public static function is_acf($key = '') {
        if ($key) {
            return self::get_acf_field_id($key);
        }        
        return false;
    }

    public static function get_acf_fields($types = array(), $group = false, $select = false) {

        $acf_list = [];

        if (is_string($types)) {
            if ($types == 'dyncontel-acf') {
                $types = array(
                    'text',
                    'textarea',
                    'select',
                    'number',
                    'checkbox',
                    'radio',
                    'color_picker',
                    'date_time_picker',
                    'date_picker',
                    'time_picker',
                    'oembed',
                    'file',
                    'url',
                    'image',
                    'wysiwyg',
                    'true_false',
                    'acf_code_field',
                    'repeater',
                    'google_map',
                    'link',
                    'page_link'
                );
            } else {
                $types = DCE_Helper::str_to_array(',', $types);
            }
        }
        if ($select) {
            $acf_list[0] = 'Select the Field';
        }

        if (DCE_Helper::is_plugin_active('acf')) {
            $tipo = 'acf-field';
            $acf_fields = get_posts(array('post_type' => $tipo, 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'suppress_filters' => false));

            if (!empty($acf_fields)) {
                foreach ($acf_fields as $acf_field) {
                    $acf_field_parent = false;
                    if ($acf_field->post_parent) {
                        if ($acf_field_parent = get_post($acf_field->post_parent)) {
                            $acf_field_parent_settings = maybe_unserialize($acf_field_parent->post_content);
                        }
                    }
                    $acf_field_settings = maybe_unserialize($acf_field->post_content);
                    if (isset($acf_field_settings['type']) && (empty($types) || in_array($acf_field_settings['type'], $types))) {

                        if ($group && $acf_field_parent) {
                            if (empty($acf_list[$acf_field_parent->post_excerpt]) || is_array($acf_list[$acf_field_parent->post_excerpt])) {
                                if (isset($acf_field_parent_settings['type']) && $acf_field_parent_settings['type'] == 'group') {
                                    $acf_list[$acf_field_parent->post_excerpt]['options'][$acf_field_parent->post_excerpt . '_' . $acf_field->post_excerpt] = $acf_field->post_title . ' [' . $acf_field->post_excerpt . '] (' . $acf_field_settings['type'] . ')';
                                } else {
                                    $acf_list[$acf_field_parent->post_excerpt]['options'][$acf_field->post_excerpt] = $acf_field->post_title . ' [' . $acf_field->post_excerpt . '] (' . $acf_field_settings['type'] . ')';
                                }
                                $acf_list[$acf_field_parent->post_excerpt]['label'] = $acf_field_parent->post_title;
                            }
                        } else {
                            if ($acf_field_parent) {
                                if (isset($acf_field_parent_settings['type']) && $acf_field_parent_settings['type'] == 'group') {
                                    $acf_list[$acf_field_parent->post_excerpt . '_' . $acf_field->post_excerpt] = $acf_field_parent->post_title .' > '. $acf_field->post_title . ' [' . $acf_field->post_excerpt . '] (' . $acf_field_settings['type'] . ')'; //.$acf_field->post_content; //post_name,
                                } else {
                                    $acf_list[$acf_field->post_excerpt] = $acf_field_parent->post_title .' > '. $acf_field->post_title . ' [' . $acf_field->post_excerpt . '] (' . $acf_field_settings['type'] . ')'; //.$acf_field->post_content; //post_name,
                                }
                            }
                        }
                    }
                }
            }
        }
        return $acf_list;
    }

    public static function get_acf_field_urlfile($group = false) {
        return self::get_acf_fields(array('file', 'url'), $group);
    }

    public static function get_acf_field_relations() {
        return self::get_acf_fields('relationship', $group);
    }

    public static function get_acf_field_value_relationship_invert($acf_relation_field, $post_id = 0) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        global $wpdb;
        $obj_first = 'p';
        $obj = 'post';
        $post_fields = ', p.post_type, p.post_parent';
        $field_id = $obj == 'term' ? $obj . '_id' : 'ID';
        $search_query = 'SELECT ' . $obj_first . 'm.' . $obj . '_id AS "ID"' . $post_fields . ' FROM ' . $wpdb->prefix . $obj . 'meta ' . $obj_first . 'm, ' . $wpdb->prefix . $obj . 's ' . $obj_first;
        $search_query .= " WHERE " . $obj_first . "." . $field_id . " = " . $obj_first . "m." . $obj . "_id AND " . $obj_first . "m.meta_key LIKE \"" . $acf_relation_field . "\" AND  " . $obj_first . "m.meta_value LIKE '%s:" . strlen($post_id) . ":\"" . $post_id . "\"%'";
        $results = $wpdb->get_results($search_query);
        $obj_ids = array();
        foreach ($results as $key => $aobj) {
            $pid = intval($aobj->ID);
            if ($obj == 'post' && $aobj->post_type == 'revision') {
                if (!in_array(intval($aobj->post_parent), $obj_ids)) {
                    $obj_ids[] = intval($aobj->post_parent);
                }
            } else {
                if (!in_array($pid, $obj_ids)) {
                    $obj_ids[] = $pid;
                }
            }
        }
        return $obj_ids;
    }

    /* public static function get_acf_field_relational_post() {
      $acf_list = [];
      $relational = array("post_object", "relationship"); //,"taxonomy","user");
      $acf_list[0] = __('Select the Field', 'dynamic-content-for-elementor');
      $get_templates = get_posts(array('post_type' => 'acf-field', 'numberposts' => -1));
      if (!empty($get_templates)) {
      foreach ($get_templates as $template) {
      $acf_field_parent = $template->post_parent;
      $arrayField = maybe_unserialize($template->post_content);
      if (in_array($arrayField['type'], $relational)) {
      $acf_list[$template->post_excerpt] = $template->post_title . ' (' . $arrayField['type'] . ')'; //.$template->post_content; //post_name,
      }
      }
      }
      return $acf_list;
      } */

    public static function get_acf_field_relational_post() {
        return self::get_acf_fields(array("post_object", "relationship"));
    }

    public static function get_acf_repeater_fields($key) {        
        if (isset(self::$meta_fields[$key]['fields'])) {
            return self::$meta_fields[$key]['fields'];
        }
        $sub_fields = array();
        if (self::is_plugin_active('acf')) {
            $repeater_id = self::get_acf_field_id($key);
            
            // fix repeater in group
            $tmp = explode('_', $key);
            while (empty($repeater_id) && count($tmp) > 1) {
                array_shift($tmp);
                $key = implode('_', $tmp);
                $repeater_id = self::get_acf_field_id($key);
            }

            if (is_array($repeater_id)) {
                $repeater_id_tmp = 0;
                foreach ($repeater_id as $rid) {
                    $r_post = get_post($rid);
                    $r_conf = maybe_unserialize($r_post->post_content);
                    if (isset($r_conf['type']) && $r_conf['type'] == 'repeater') {
                        $repeater_id_tmp = $rid;
                    }
                }
                if ($repeater_id_tmp) {
                    $repeater_id = $repeater_id_tmp;
                }
            }
            $fields = get_posts(array('post_type' => "acf-field", 'numberposts' => -1, 'post_status' => 'publish', 'post_parent' => $repeater_id));
            if (!empty($fields)) {
                foreach ($fields as $afield) {
                    $settings = maybe_unserialize($afield->post_content);
                    $settings['title'] = $afield->post_title;
                    $sub_fields[$afield->post_excerpt] = $settings;
                }
            }
        }
        self::$meta_fields[$key]['fields'] = $sub_fields;
        return $sub_fields;
    }

    public static function get_acf_field_id($key, $multi = false) {        
        if (isset(self::$meta_fields[$key]['ID'])) {
            return self::$meta_fields[$key]['ID'];
        }
        global $wpdb;
        $query = 'SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type LIKE "acf-field" AND post_excerpt LIKE "' . $key . '"';
        $results = $wpdb->get_results($query);
        if (count($results) > 1) {
            // bad acf configuration
            $field_ids = array();
            foreach ($results as $afields) {
                $field_ids[] = $afields->ID;
            }
            if ($multi) {
                return $field_ids;
            }
            return reset($field_ids);
        }
        $result = $wpdb->get_var($query);
        if ($result) {
            self::$meta_fields[$key]['ID'] = $result;
            return $result;
        }
        return false;
    }

    public static function get_acf_field_settings($key) {
        if (isset(self::$meta_fields[$key]['settings'])) {
            return self::$meta_fields[$key]['settings'];
        }
        $field = self::get_acf_field_post($key);
        if ($field) {
            //var_dump($field);
            $settings = maybe_unserialize($field->post_content);
            self::$meta_fields[$key]['settings'] = $settings;
            return $settings;
        }
        return false;
    }

    public static function get_acf_field_post($key, $multi = false) {
        if (isset(self::$meta_fields[$key]['post'])) {
            return self::$meta_fields[$key]['post'];
        }
        if (is_int($key)) {
            $post = get_post($key);
            self::$meta_fields[$key]['post'] = $post;
            return $post;
        }
        $field_id = self::get_acf_field_id($key, $multi);
        if ($field_id) {
            if (is_array($field_id)) {
                if ($multi) {
                    $posts = get_posts(array('post__in' => $field_id, 'posts_per_page' => -1));
                    self::$meta_fields[$key]['post'] = $posts;
                    return $posts;
                } else {
                    $field_id = reset($field_id);
                }
            }
            if ($field_id) {
                $post = get_post($field_id);
                self::$meta_fields[$key]['post'] = $post;
                return $post;
            }
        }
        self::$meta_fields[$key]['post'] = false;
        return false;
    }

    public static function get_acf_field_value($idField, $id_page = null, $format = true) {

        if (!$id_page) {
            $id_page = get_queried_object_id();
        }
        
        /*
        $queried_object = get_queried_object();
        if (!empty($queried_object) && is_object($queried_object) && get_class($queried_object) == 'WP_Term') {
            $taxonomy = $queried_object->taxonomy;
            $term_id = $queried_object->term_id;  
            $id_page = $taxonomy.'_'.$term_id;
            //var_dump($id_page);
        }
        */
        
        /*
        if (isset($_GET['preview']) && isset($_GET['preview_id'])) {
            $preview_id = $_GET['preview_id'];
            //var_dump($id_page);
            $template = get_post($preview_id);
            if ($template && $template->ID == $preview_id) {
                if ($template->type == 'elementor_library') {
                    $id_page = get_preview_post_id($preview_id);                
                } else {
                    $id_page = $preview_id;
                }
            }
        }
        //var_dump(get_queried_object_id());
        */
        
        $dataACFieldPost = self::get_acf_field_post($idField);

        // field in REPEATER
        if ($dataACFieldPost) {
            $parentID = $dataACFieldPost->post_parent;
            $parent_settings = self::get_acf_field_settings($parentID);

            if (isset($parent_settings['type']) && $parent_settings['type'] == 'repeater') {
                $parent_post = get_post($parentID);
                $row = acf_get_loop('active');
                if (!$row) {
                    if (have_rows($parent_post->post_excerpt, $id_page)) {
                        the_row();
                    }
                }
                $sub_field_value = get_sub_field($idField);
                if ($sub_field_value !== false) {
                    return $sub_field_value;
                }
            }
        }

        // post
        $theField = get_field($idField, $id_page, $format);

        if (!$theField) {

            $locations = self::get_acf_field_locations($dataACFieldPost);
            //var_dump($locations);

            if (is_tax() || is_category() || is_tag() || in_array('taxonomy', $locations)) {
                $term = get_queried_object();
                $theField = get_field($idField, $term, $format);
            }

            if (!$theField && is_author()) {
                $author_id = get_the_author_meta('ID');
                $theField = get_field($idField, 'user_' . $author_id, $format);
            }

            if (!$theField && in_array('user', $locations)) {
                $user_id = get_current_user_id();
                $theField = get_field($idField, 'user_' . $user_id, $format);
            }
            if (!$theField && in_array('options', $locations)) {
                $theField = get_field($idField, 'options', $format);
            }
            if (!$theField && in_array('nav', $locations)) {
                $menu = wp_get_nav_menu_object($id_page);
                $theField = get_field($idField, $menu, $format);
            }
        }

        //if(DCE_Helper::in_the_loop()) $theField = get_field($idField , $id_page, $format);

        return $theField;
    }

    public static function get_pods_fields($t = null) {
        $podsList = [];
        $podsList[0] = __('Select the Field', 'dynamic-content-for-elementor');
        $pods = get_posts(array('post_type' => '_pods_field', 'numberposts' => -1, 'post_status' => 'publish', 'suppress_filters' => false));
        if (!empty($pods)) {
            foreach ($pods as $apod) {
                $type = get_post_meta($apod->ID, 'type', true);
                if (!$t || $type == $t) {
                    $title = $apod->post_title;
                    if (!$t) {
                        $title .= ' [' . $type . ']';
                    }
                    $podsList[$apod->post_name] = $title;
                }
            }
        }
        return $podsList;
    }

    public static function get_toolset_fields($t = null) {
        $toolsetList = [];
        $toolsetList[0] = __('Select the Field', 'dynamic-content-for-elementor');
        $toolset = get_option('wpcf-fields', false);
        if ($toolset) {
            $toolfields = maybe_unserialize($toolset);
            if (!empty($toolfields)) {
                foreach ($toolfields as $atool) {
                    $type = $atool['type'];
                    if (!$t || $type == $t) {
                        $title = $atool['name'];
                        if (!$t) {
                            $title .= ' [' . $type . ']';
                        }
                        $toolsetList[$atool['meta_key']] = $title;
                    }
                }
            }
        }
        return $toolsetList;
    }

    /* public static function get_user_fields($idUser = 1) {
      $userTmp = wp_get_current_user();
      //var_dump($userTmp);
      $userProp = get_object_vars($userTmp);
      $userMeta = get_registered_meta_keys('user');
      //var_dump($userMeta);
      $userFields = array_merge(array_keys($userProp), array_keys($userMeta));
      return $userFields;
      } */



    /* public static function get_user_meta($idUser = 1) {
      $all_userMeta = get_user_meta($idUser);
      //$all_userMeta = get_metadata('user',1);
      //var_dump($all_userMeta); die();
      $ret['none'] = 'None';
      foreach ($all_userMeta as $key => $value) {
      $ret[$key] = $key; //$value;
      }
      return $ret;
      } */
}
