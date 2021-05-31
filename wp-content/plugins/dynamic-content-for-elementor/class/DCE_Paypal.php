<?php 
namespace DynamicContentForElementor;

// https://github.com/paypal/ipn-code-samples

class DCE_Paypal {
    /** @var bool Indicates if the sandbox endpoint is used. */
    private $use_sandbox = false;
    /** @var bool Indicates if the local certificates are used. */
    private $use_local_certs = true;
    /** Production Postback URL */
    const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';
    /**
     * Sets the IPN verification to sandbox mode (for use when testing,
     * should not be enabled in production).
     * @return void
     */
    public function useSandbox()
    {
        $this->use_sandbox = true;
    }
    /**
     * Sets curl to use php curl's built in certs (may be required in some
     * environments).
     * @return void
     */
    public function usePHPCerts()
    {
        $this->use_local_certs = false;
    }
    /**
     * Determine endpoint to post the verification data to.
     *
     * @return string
     */
    public function getPaypalUri()
    {
        if ($this->use_sandbox) {
            return self::SANDBOX_VERIFY_URI;
        } else {
            return self::VERIFY_URI;
        }
    }
    /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyIPN()
    {
        if ( ! count($_POST)) {
            throw new Exception("Missing POST Data");
        }
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
                if ($keyval[0] === 'payment_date') {
                    if (substr_count($keyval[1], '+') === 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
                }
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        // Build the body of the verification post request, adding the _notify-validate command.
        $req = 'cmd=_notify-validate';
        $get_magic_quotes_exists = false;
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }
        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
        $ch = curl_init($this->getPaypalUri());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        
        if ($this->use_local_certs) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        } else {
            curl_setopt($ch, CURLOPT_SSLVERSION, 6);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
        }
        
        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
        /*if ($this->use_local_certs) {
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/cert/cacert.pem");
        }*/
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'User-Agent: PHP-IPN-Verification-Script',
            'Connection: Close',
        ));
        $res = curl_exec($ch);
        if ( ! ($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL error: [$errno] $errstr");
        }
        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) {
            throw new \Exception("PayPal responded with http code $http_code");
        }
        curl_close($ch);
        // Check if PayPal verifies the IPN data, and if so, return true.
        if ($res == self::VALID) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getStatus() {
        return isset($_POST['payment_status']) ? $_POST['payment_status'] : false ;
    }
    
    public static function isCompleted($post_id = null) {
        $payment_status = false;
        if ($post_id) {
            $payment_status = get_post_meta($post_id, 'paypal_payment_status', true);
        }
        if (isset($_POST['payment_status'])) {
            $payment_status = $_POST['payment_status'];
        }
        return $payment_status == 'Completed';
    }
    
    public static function isFromPaypal($widget) {
        $settings = $widget->get_settings_for_display();
        $referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : false; // HTTPS (paypal) to HTTP (maybe your site) return false value
        $referer_paypal = ($referer && strpos($referer, 'paypal.com') !== false) ? true : $settings['dce_form_paypal_sandbox'];
        return $referer_paypal;
    }
    
    public static function savePost($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        // save all data from PayPal
        if (!empty($_POST)) {
            foreach ($_POST as $meta_key => $meta_value) {
                update_post_meta($post_id, 'paypal_'.$meta_key, $meta_value);
            }
        }
    }
    
    public static function getUserId() {
        $user_id = get_current_user_id();
        if (!$user_id) {                            
            if (isset($_POST['payer_email'])) {
                $payer_email = $_POST['payer_email'];
                // get user from payer data
                $payer = get_user_by('email', $payer_email);
                if ($payer) {
                    $user_id = $payer->ID;
                } else {
                    $db_ins = array();
                    $db_ins['user_email'] = $payer_email;
                    if (isset($_POST['payer_email'])) {
                        $db_ins['user_login'] = $_POST['payer_id'];
                    }
                    if (isset($_POST['first_name'])) {
                        $db_ins['first_name'] = $_POST['first_name'];
                    }
                    if (isset($_POST['last_name'])) {
                        $db_ins['last_name'] = $_POST['last_name'];
                    }
                    $user_id = wp_insert_user($db_ins);
                }
            }
        }
        return $user_id;
    }
    
    public static function getElementId() {
        $element_id = $ref = null;
        if (isset($_GET['ref'])) {
            $ref = $_GET['ref'];
        }
        $referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : false;
        if (wp_doing_ajax() && $referer) {
            $pieces = explode('?ref=', $referer, 2);
            if (count($pieces) > 1) {
                $ref = end($pieces);
            }
        }
        if ($ref) {
            $pieces = explode('-', $ref);
            if (count($pieces) > 1) {
                $element_id = $pieces[0];
                $post_id = $pieces[1];

                $paypal_time_get = end($pieces);
                $paypal_time = get_post_meta($post_id, 'paypal_time', true);

                $paypal_post = get_post($post_id);
                if (!$paypal_post) {
                    return false;
                } 
                if (get_post_status($post_id) != 'draft') {
                    return false;
                }
                if ($paypal_time != $paypal_time_get) {
                    return false;
                }
            }

        }
        return $element_id;
    }
    
    public static function getPostId() {
        $post_id = $ref = null;
        if (isset($_GET['ref'])) {
            $ref = $_GET['ref'];
        }
        $referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : false;
        if (wp_doing_ajax() && $referer) {
            $pieces = explode('?ref=', $referer, 2);
            if (count($pieces) > 1) {
                $ref = end($pieces);
            }
        }
        if ($ref) {
            $pieces = explode('-', $ref);
            if (count($pieces) > 1) {
                $post_id = $pieces[1];

                $paypal_time_get = end($pieces);
                $paypal_time = get_post_meta($post_id, 'paypal_time', true);

                $paypal_post = get_post($post_id);
                if (!$paypal_post) {
                    return false;
                } 
                if (get_post_status($post_id) != 'draft') {
                    return false;
                }
                if ($paypal_time != $paypal_time_get) {
                    return false;
                }
            }

        }
        return $post_id;
    }
}
