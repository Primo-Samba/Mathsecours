<?php
/** Loads the WordPress Environment and Template */
define('WP_USE_THEMES', false);
require ('../../../../wp-blog-header.php');

$element_id = empty($_GET['element_id']) ? 0 : $_GET['element_id'];
$md5 = empty($_GET['md5']) ? 0 : $_GET['md5'];

if ($element_id && $md5) {
    
    function glob_recursive($base, $pattern, $flags = 0) {
	if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
		$base .= DIRECTORY_SEPARATOR;
	}

        $files = glob($base . $pattern, $flags);

        foreach (glob($base . '*', GLOB_ONLYDIR | GLOB_NOSORT | GLOB_MARK) as $dir) {
            $dirFiles = glob_recursive($dir, $pattern, $flags);
            if ($dirFiles !== false) {
                $files = array_merge($files, $dirFiles);
            }
        }

        return $files;
    }

    // static settings
    $widget = \DynamicContentForElementor\DCE_Helper::get_elementor_element_by_id($element_id);

    $settings = $widget->get_settings_for_display();
    
    $everyonehidden = false;
    if (!empty($settings['private_access'])) {
        $current_user = wp_get_current_user();
        if ($current_user && $current_user->ID) {
            $user_roles = $current_user->roles; // possibile avere più ruoli
            if (!is_array($user_roles)) {
                $user_roles = array($user_roles);
            }
            if (is_array($settings['user_role'])) {
                $tmp_role = array_intersect($user_roles, $settings['user_role']);
                if (!empty($tmp_role)) {
                    $everyonehidden = TRUE;
                }
            }
        } else {
            if (in_array('visitor', $settings['user_role'])) {
                $everyonehidden = TRUE;
            }
        }
    }
    if ($everyonehidden) {
        $baseDir = false;
        switch ($settings['path_selection']) {
                case 'custom':
                    $baseDir = $settings['folder_custom'];
                    break;
                case 'uploads':
                    $baseDir = $settings['folder'];
                    $baseTitle = $settings['folder'];
                    if ($settings['subfolder_' . $settings['folder']]) {
                        $baseDir .= $settings['subfolder_' . $settings['folder']];
                    }
                    break;
        }

        if ($baseDir) {
            $folder = \DynamicContentForElementor\Widgets\DCE_Widget_FileBrowser::getRootDir($baseDir, $settings);
            $files = glob_recursive($folder,'*');
            //var_dump($folder);
            //var_dump($files);
            //var_dump($md5);
            foreach($files as $afile) {
                $afile_md5 = md5($afile);
                //echo $afile.' => '.$afile_md5.'<br>';
                if ($afile_md5 == $md5) {

                    status_header(200);
                    global $wp_query;
                    $wp_query->is_page = $wp_query->is_singular = true;
                    $wp_query->is_404 = false;

                    $file_name = urlencode(basename($afile));
                    //var_dump($filename);

                    header("Content-Type: ".mime_content_type($afile));
                    header("Content-Disposition: attachment; filename=".$file_name);
                    header("Content-Length: " . filesize($afile));
                    readfile($afile);

                    exit();

                }
            }
        }
    } else {
        
        if (!is_user_logged_in()){
            //$file_url = add_query_arg('login', 'true');
            wp_redirect( wp_login_url() ); exit;
        }        
        
        if (!empty($settings['user_redirect'])) {
            $location = $settings['user_redirect']['url'];
            wp_redirect($location);
            exit();
        }
        
    }
}

status_header(403);
nocache_headers();
global $wp_query;
$wp_query->is_page = $wp_query->is_singular = false;
$wp_query->is_404 = true;
//echo '404 - FILE NOT FOUND';
get_template_part( 'template-parts/404' );
//include( get_query_template( '404' ) );