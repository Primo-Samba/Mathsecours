<?php
/** Loads the WordPress Environment and Template */
define('WP_USE_THEMES', false);
require ('../../../../wp-blog-header.php');


$element_id = \DynamicContentForElementor\DCE_Paypal::getElementId();
$post_id = \DynamicContentForElementor\DCE_Paypal::getPostId();

if ($element_id && $post_id) {
    
    // static settings
    $widget = \DynamicContentForElementor\DCE_Helper::get_elementor_element_by_id($element_id);
    
    // dynamic settings
    // populate post for dynamic data
    global $post;
    $post = get_post($post_id);
 
    // create an instance of widget to get his dynamic data
    //$widget = new \DynamicContentForElementor\Widgets\DCE_Widget_Calendar($data, array());
    $settings = $widget->get_settings_for_display();
    
    
    // paypal verification
    if (isset($_POST['verify_sign'])) {
        $ipn = new DCE_Paypal();
        if ($settings['dce_form_paypal_sandbox']) {
            $ipn->useSandbox();
        }
        //$ipn->usePHPCerts(); // ssl validation
        $ipn_verified = $ipn->verifyIPN();
        if (!$ipn_verified) {
            $error = __('ERROR: Paypal not verified the payment...Please contact the administrator.', 'dynamic-content-for-elementor');
            $verified = false;
        }
    }
  
    if ($verified) {                     
        // associate payment to user
        if ($user_id) {
            $user_id = DCE_Paypal::getUserId();
            wp_update_post( array(
                'ID' => $post_id,
                'post_author' => $user_id,
            ) );
        }
        DCE_Paypal::savePost($post_id);
    }
    
}