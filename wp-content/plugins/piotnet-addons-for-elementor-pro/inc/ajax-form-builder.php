<?php
	require_once('vendor/autoload.php');
	//require_once('pdf/fpdf.php');
	include('MailChimp.php');
	require_once(__DIR__.'/helper/functions.php');
	//require_once(__DIR__.'/helper/pdf.php');

	add_action( 'wp_ajax_pafe_ajax_form_builder', 'pafe_ajax_form_builder' );
	add_action( 'wp_ajax_nopriv_pafe_ajax_form_builder', 'pafe_ajax_form_builder' );

	function find_element_recursive( $elements, $form_id ) {
		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	function set_val(&$array,$path,$val) {
		for($i=&$array; $key=array_shift($path); $i=&$i[$key]) {
			if(!isset($i[$key])) $i[$key] = array();
		}
		$i = $val;
	}

	function pafe_merge_string(&$string,$string_add) {
		$string = $string . $string_add;
	}

	function pafe_unset_string(&$string) {
		$string = '';
	}

	function pafe_set_string(&$string,$string_set) {
		$string = $string_set;
	}

	function replace_email($content, $fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed', $submit_id = 0 ) {
		$message = $content;

		$message_all_fields = '';

		// $fields_array = array();

		// foreach ($fields as $field) {
		// 	$repeater_id = $field['repeater_id'];
		// 	$repeater_index = $field['repeater_index'];
		// 	$repeater_label = $field['repeater_label'];

		// 	if (!empty($repeater_id)) {
		// 		$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );

		// 		$path = join(",",$repeater_id_array);
		// 		$path = str_replace('|', ',', $path);
		// 		$path = explode(',',$path);

		// 		set_val($fields_array,$path,$field);
		// 	} else {
		// 		$field['repeater'] = false;
		// 		$fields_array[$field['name']] = $field;
		// 	}
		// }

		if (!empty($fields)) {

			// all fields
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($message_all_fields, $repeater_label) !== false) {
						$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
					} else {
						$message_all_fields .= $repeater_label;
						if (strpos($field['name'], 'pafe-end-repeater') === false) {
							$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
						}
					}
					// if ($field['repeater_index'] != ($field['repeater_length'] - 1)) {
					// 	$message .=  '<br />';
					// }
				} else {
					if (strpos($field['name'], 'pafe-end-repeater') === false) {
						$message_all_fields .= $field_label . ': ' . $field_value . '<br />';
					}
				}

			}

			$message = str_replace( '[all-fields]', $message_all_fields, $message );

			// each field

			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_value = $field['value'];
				$field_label = isset($field['label']) ? $field['label'] : '';
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}

				$search_remove_line_if_field_empty = '[field id="' . $field['name'] . '"]' . '[remove_line_if_field_empty]';

				if (empty($field_value)) {
					$lines = explode("\n", $message);
					$lines_found = array();

					foreach($lines as $num => $line){
					    $pos = strpos($line, $search_remove_line_if_field_empty);
					    if($pos !== false) {
					    	$lines_found[] = $line;
					    }
					}

					if (!empty($lines_found)) {
						foreach ($lines_found as $line) {
							$message = str_replace( [ $line . "\n", "\n" . $line ], '', $message );
						}
					}
				}

				$search = '[field id="' . $field['name'] . '"]';
				$message = str_replace($search, $field_value, $message);

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = '<span data-id="' . $repeater_id_string . '"><strong>' . $field['repeater_label'] . ' ' . $repeater_index_1 . ': </strong></span><br>';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						if (strpos($field['name'], 'pafe-end-repeater') === false) {
							$string_add = $field_label . ': ' . $field_value . '<br />';
						}
						pafe_merge_string($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . $field['label'] . ': ' . $field_value . '<br />';
						pafe_merge_string($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = '[repeater id="' . $repeater_id_one . '"]';
						$message = str_replace($search_repeater, $repeater_content, $message);
						pafe_unset_string($repeater_content);
						pafe_unset_string($repeater_id_one);
					}
				}
				
			}
		}

		$search_remove_line_if_field_empty = '"]' . '[remove_line_if_field_empty]'; // fix alert [

		$lines = explode("\n", $message);
		$lines_found = array();

		foreach($lines as $num => $line){
		    $pos = strpos($line, $search_remove_line_if_field_empty);
		    if($pos !== false) {
		    	$lines_found[] = $line;
		    }
		}

		if (!empty($lines_found)) {
			foreach ($lines_found as $line) {
				$message = str_replace( [ $line . "\n", "\n" . $line ], '', $message );
			}
		}

		$message = str_replace( [ "[remove_line_if_field_empty]" ], '', $message );

		$message = str_replace( [ "\r\n", "\n", "\r", "[remove_line_if_field_empty]" ], '<br />', $message );

		if ($payment_status == 'succeeded') {
			$message = str_replace( '[payment_status]', $succeeded, $message );
		}

		if ($payment_status == 'pending') {
			$message = str_replace( '[payment_status]', $pending, $message );
		}

		if ($payment_status == 'failed') {
			$message = str_replace( '[payment_status]', $failed, $message );
		}

		if (!empty($payment_id)) {
			$message = str_replace( '[payment_id]', $payment_id, $message );
		}

		if (!empty($submit_id)) {
			$message = str_replace( '[submit_id]', $submit_id, $message );
		}

		return $message;
	}

	function get_field_name_shortcode($content) {
		$field_name = str_replace('[field id="', '', $content);
		$field_name = str_replace('[repeater id="', '', $field_name); // fix alert ]
		$field_name = str_replace('"]', '', $field_name);
		return trim($field_name);
	}

	function pafe_get_field_value($field_name,$fields, $payment_status = 'succeeded', $payment_id = '', $succeeded = 'succeeded', $pending = 'pending', $failed = 'failed', $multiple = false ) {

		$field_name_first = $field_name;

		if (strpos($field_name, '[repeater id') !== false) { // ] [ [ fix alert
			$field_name = str_replace('id="', "id='", $field_name);
			$field_name = str_replace('"]', "']", $field_name);
			$field_label = isset($field['label']) ? $field['label'] : '';
			$message = $field_name;
			$repeater_content = '';
			$repeater_id_one = '';
			foreach ($fields as $field) {
				$field_value = $field['value'];
				if (isset($field['value_label'])) {
					$field_value = $field['value_label'];
				}
				
				$search = '[field id="' . $field['name'] . '"]';
				$message = str_replace($search, $field_value, $message);

				$repeater_id = $field['repeater_id'];
				$repeater_id_string = '';
				$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
				foreach ($repeater_id_array as $repeater) {
					$repeater_array = explode('|', $repeater);
					array_pop($repeater_array);
					$repeater_id_string .= join(",",$repeater_array);
				}
				$repeater_index = $field['repeater_index']; 
				$repeater_index_1 = $repeater_index + 1;
				$repeater_label = $field['repeater_label'] . ' ' . $repeater_index_1 . '\n';

				$repeater_remove_this_field = false;
				if (isset($field['repeater_remove_this_field'])) {
					$repeater_remove_this_field = true;
				}
				
				if (!empty($repeater_id) && !empty($repeater_label) && $repeater_remove_this_field == false) {
					if (strpos($repeater_content, $repeater_label) !== false) {
						$string_add = $field_label . ': ' . $field_value . '\n';
						pafe_merge_string($repeater_content,$string_add);
					} else {
						$string_add = $repeater_label . $field['label'] . ': ' . $field_value . '\n';
						pafe_merge_string($repeater_content,$string_add);
					}
					if (substr_count($field['repeater_id'],'|') == 2) {
						pafe_set_string($repeater_id_one,$field['repeater_id_one']);
					}
				}

				if (empty($repeater_id)) {
					if (!empty($repeater_id_one) && !empty($repeater_content)) {
						$search_repeater = "[repeater id='" . $repeater_id_one . "']";
						$message = str_replace($search_repeater, $repeater_content, $message);

						pafe_unset_string($repeater_content);
						pafe_unset_string($repeater_id_one);
					}
				}
			}

			$field_value = $message;
		} else {
			$field_name = get_field_name_shortcode($field_name);
			$field_value = '';
			foreach ($fields as $key_field=>$field) {
				if ($fields[$key_field]['name'] == $field_name) {
					// if (!empty($fields[$key_field]['value'])) {
					// 	$field_value = $fields[$key_field]['value'];
					// }

					if (isset($fields[$key_field]['calculation_results'])) {
						$field_value = $fields[$key_field]['calculation_results'];
					} else {
						$field_value = $fields[$key_field]['value'];
						if ($multiple && !empty($fields[$key_field]['value_multiple'])) {
							$field_value = $fields[$key_field]['value_multiple'];
						}
					}
				}
			}
		}

		if (!is_array($field_value)) {
			if (strpos($field_name_first, '[payment_status]') !== false || strpos($field_name_first, '[payment_id]') !== false) {
				if ($payment_status == 'succeeded') {
					$field_value = str_replace( '[payment_status]', $succeeded, $field_name_first );
				}

				if ($payment_status == 'pending') {
					$field_value = str_replace( '[payment_status]', $pending, $field_name_first );
				}

				if ($payment_status == 'failed') {
					$field_value = str_replace( '[payment_status]', $failed, $field_name_first );
				}

				if (!empty($payment_id) && strpos($field_name_first, '[payment_id]') !== false) {
					$field_value = str_replace( '[payment_id]', $payment_id, $field_name_first );
				}
			}

			return trim($field_value);
		} else {
			return $field_value;
		}
		
	}

	function hexToRgb($hex, $alpha = false) {
		$hex      = str_replace('#', '', $hex);
		$length   = strlen($hex);
		$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		if ( $alpha ) {
		   $rgb['a'] = $alpha;
		}
		return $rgb;
	 }

	function getIndexColumn($column) {
		$columnArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

		$columnFirstWord = strtoupper( substr($column, 0, 1) );
		$columnSecondWord = strtoupper( substr($column, 1, 2) );
		$index = 0;
		  
		if($columnSecondWord == '') {
		  $index = array_search($columnFirstWord, $columnArray);
		} else {
		  $index = (array_search($columnFirstWord, $columnArray) + 1)*26 + array_search($columnSecondWord, $columnArray);
		}

		return $index;
	}

	function acf_get_field_key( $field_name, $post_id ) {
		global $wpdb;
		$acf_fields = $wpdb->get_results( $wpdb->prepare( "SELECT ID,post_parent,post_name FROM $wpdb->posts WHERE post_excerpt=%s AND post_type=%s" , $field_name , 'acf-field' ) );
		// get all fields with that name.
		switch ( count( $acf_fields ) ) {
			case 0: // no such field
				return false;
			case 1: // just one result. 
				return $acf_fields[0]->post_name;
		}
		// result is ambiguous
		// get IDs of all field groups for this post
		$field_groups_ids = array();
		$field_groups = acf_get_field_groups( array(
			'post_id' => $post_id,
		) );
		foreach ( $field_groups as $field_group )
			$field_groups_ids[] = $field_group['ID'];
		
		// Check if field is part of one of the field groups
		// Return the first one.
		foreach ( $acf_fields as $acf_field ) {
			if ( in_array($acf_field->post_parent,$field_groups_ids) )
				return $acf_field->post_name;
		}
		return false;
	}

	/**
	 * Save the image on the server.
	 */
	function save_image( $base64_img, $title ) {

		// Upload dir.
		$upload_dir  = wp_upload_dir();
		$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

		// $img             = str_replace( 'data:image/png;base64,', '', $base64_img );
		// $img             = str_replace( ' ', '+', $img );
		// $decoded         = base64_decode( $img );
		// $filename        = $title;
		// $file_type       = 'image/png';
		// $hashed_filename = $title . '_' . md5( $filename . microtime() ) .'.png';
		$file_type       = 'image/png';
		$data_uri = $base64_img;
		$encoded_image = explode(",", $data_uri)[1];
		$decoded = base64_decode($encoded_image);
		$hashed_filename = $title . '_' . md5( $title . microtime() ) .'.png';

		// Save the image in the uploads directory.
		$upload_file = file_put_contents( $upload_path . $hashed_filename, $decoded );

		$attachment = array(
			'post_mime_type' => $file_type,
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $hashed_filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => $upload_dir['url'] . '/' . basename( $hashed_filename )
		);

		$attach_id = wp_insert_attachment( $attachment, $upload_dir['path'] . '/' . $hashed_filename );

		$attach_data = wp_generate_attachment_metadata( $attach_id, $hashed_filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return wp_get_attachment_image_src($attach_id,'full')[0];
	}

	function pafe_ajax_form_builder() {

		global $wpdb;
			if ( !empty($_POST['post_id']) && !empty($_POST['form_id']) && !empty($_POST['fields']) ) {
				$post_id = $_POST['post_id'];
				$form_id = $_POST['form_id'];
				$fields = stripslashes($_POST['fields']);
				$fields = json_decode($fields, true);
				$fields = array_unique($fields, SORT_REGULAR);

				$failed = false;

				$post_url = '';

				$message = '';
				$meta_content = '';

				$upload = wp_upload_dir();
				$upload_dir = $upload['basedir'];
				$upload_dir = $upload_dir . '/piotnet-addons-for-elementor';

				$attachment = array();

				if( !empty($_FILES) ) {
					foreach ($_FILES as $key=>$file) {
						
						for ($i=0; $i < count($file['name']); $i++) { 
							$file_extension = pathinfo( $file['name'][$i], PATHINFO_EXTENSION );
							$filename_goc = str_replace( '.' . $file_extension, '', $file['name'][$i]);
							$filename = $filename_goc . '-' . uniqid() . '.' . $file_extension;
							$filename = wp_unique_filename( $upload_dir, $filename );
							$new_file = trailingslashit( $upload_dir ) . $filename;

							if ( is_dir( $upload_dir ) && is_writable( $upload_dir ) ) {
								$move_new_file = @ move_uploaded_file( $file['tmp_name'][$i], $new_file );
								if ( false !== $move_new_file ) {
									// Set correct file permissions.
									$perms = 0644;
									@ chmod( $new_file, $perms );

									$file_url = $upload['baseurl'] . '/piotnet-addons-for-elementor/' . $filename;

									foreach ($fields as $key_field=>$field) {
										if ($key == $field['name']) {
											if ($fields[$key_field]['attach-files'] == 1) {
												$attachment[] = WP_CONTENT_DIR . '/uploads/piotnet-addons-for-elementor/' . $filename;
											} else {
												$fields[$key_field]['value'] = $fields[$key_field]['value'] . $file_url;
												if ( $i != (count($file['name']) - 1) ) {
													$fields[$key_field]['value'] = $fields[$key_field]['value'] . ' , ';
												}
											}
										}
									}
								}
							}
						}						
					} 
				}

				foreach ($fields as $key_field=>$field) {
					$field_value = $fields[$key_field]['value'];

					if (strpos($field_value, 'data:image/png;base64') !== false) {
						$image_url = save_image( $field_value, $fields[$key_field]['name'] );
						$fields[$key_field]['value'] = $image_url;
					}

					if (isset($fields[$key_field]['attach-files'])) {
						if ($fields[$key_field]['attach-files'] == 1) {
							if (isset($fields[$key_field])) {
								unset($fields[$key_field]);
							}
						}
					}
				}

				$elementor = \Elementor\Plugin::$instance;

				if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
					$meta = $elementor->documents->get( $post_id )->get_elements_data();
				} else {
					$meta = $elementor->db->get_plain_editor( $post_id );
				}

				$form = find_element_recursive( $meta, $form_id );

				$widget = $elementor->elements_manager->create_element_instance( $form );
				$form['settings'] = $widget->get_active_settings();

				$body = array(); // Webhook

				$meta_data = array(); // Webhook

				$fields_data = array(); // Webhook

				if ( ! empty( $form['settings']['form_metadata'] ) ) {
					$form_metadata = $form['settings']['form_metadata'];
					$meta_content .= '<br>---<br><br>';
					foreach ($form_metadata as $meta) {
						if ($meta == 'date') {
							$meta_content .= __('Date','pafe') . ': ' . date_i18n( get_option( 'date_format' ) ) . '<br>';
						}
						if ($meta == 'time') {
							$meta_content .= __('Time','pafe') . ': ' . date_i18n( get_option( 'time_format' ) ) . '<br>';
						}
						if ($meta == 'page_url') {
							$meta_content .= __('Page URL','pafe') . ': ' . $_POST['referrer'] . '<br>';
						}
						if ($meta == 'user_agent') {
							$meta_content .= __('User Agent','pafe') . ': ' . $_SERVER['HTTP_USER_AGENT'] . '<br>';
						}
						if ($meta == 'remote_ip') {
							$meta_content .= __('Remote IP','pafe') . ': ' . $_POST['remote_ip'] . '<br>';
						}
					}
				}

				$meta_data['date']['title'] = __('Date','pafe');
				$meta_data['date']['value'] = date_i18n( get_option( 'date_format' ) );
				$meta_data['time']['title'] = __('Time','pafe');
				$meta_data['time']['value'] = date_i18n( get_option( 'time_format' ) );
				$meta_data['page_url']['title'] = __('Page URL','pafe');
				$meta_data['page_url']['value'] = $_POST['referrer'];
				$meta_data['user_agent']['title'] = __('User Agent','pafe');
				$meta_data['user_agent']['value'] = $_SERVER['HTTP_USER_AGENT'];
				$meta_data['remote_ip']['title'] = __('Remote IP','pafe');
				$meta_data['remote_ip']['value'] = $_POST['remote_ip'];

				if( in_array('webhook', $form['settings']['submit_actions']) && !empty($form['settings']['webhooks_advanced_data']) ) {
					if ($form['settings']['webhooks_advanced_data'] == 'yes') {
						$body['meta'] = $meta_data;
					}
				}

				$status = '';

				$payment_status = 'succeeded';
				$payment_id = '';

				// if (!empty($_POST['stripeToken'])) {

				// 	\Stripe\Stripe::setApiKey(get_option('piotnet-addons-for-elementor-pro-stripe-secret-key'));

				// 	$token = $_POST['stripeToken'];

				// 	$customer_array = array( 
				// 		"source" => $token,
				// 	);

					
				// 	$currency = strtolower($form['settings']['pafe_stripe_currency']);

				// 	if (!empty($_POST['description'])) {
				// 		$customer_array['description'] = esc_sql( $_POST['description'] );
				// 	}

				// 	// Create Customer In Stripe
				// 	$customer = \Stripe\Customer::create($customer_array);

				// 	$fields_metadata = array();

				// 	foreach ($fields as $field) {
				// 		$fields_metadata[$field['name']] = $field['value'];
				// 	}

				// 	if (empty($form['settings']['pafe_stripe_subscriptions'])) {
				// 		$amount = floatval($_POST['amount']) * 100;

				// 		if (!empty($amount)) {
				// 			// Charge Customer
				// 			$charge = \Stripe\Charge::create(array(
				// 				"amount" => $amount,
				// 				"currency" => $currency,
				// 				"description" => $form_id,
				// 				"customer" => $customer->id,
				// 				"metadata" => $fields_metadata,
				// 			));

				// 			$payment_status = $charge->status;
				// 			$payment_id = $charge->id;
				// 		}
				// 	} else {
				// 		$subscriptions = $form['settings']['pafe_stripe_subscriptions_list'];
				// 		$product_name = $form['settings']['pafe_stripe_subscriptions_product_name'];

				// 		if (!empty($subscriptions)) {
				// 			if (!empty($product_name)) {
				// 				if (count($subscriptions) == 1 && empty($form['settings']['pafe_stripe_subscriptions_field_enable'])) {
				// 					$interval = $subscriptions[0]['pafe_stripe_subscriptions_interval'];
				// 					$interval_count = $subscriptions[0]['pafe_stripe_subscriptions_interval_count'];
				// 					if (!empty($interval) && !empty($interval_count)) {
				// 						if (!empty($subscriptions[0]['pafe_stripe_subscriptions_amount_field_enable'])) {
				// 							if (!empty($subscriptions[0]['pafe_stripe_subscriptions_amount_field'])) {
				// 								$amount = floatval( pafe_get_field_value($subscriptions[0]['pafe_stripe_subscriptions_amount_field'], $fields) ) * 100;
				// 							}
				// 						} else {
				// 							if (!empty($subscriptions[0]['pafe_stripe_subscriptions_amount'])) {
				// 								$amount = floatval( $subscriptions[0]['pafe_stripe_subscriptions_amount'] ) * 100;
				// 							}
				// 						}
				// 					}
				// 				} else {
				// 					if (!empty($form['settings']['pafe_stripe_subscriptions_field_enable'])) {
				// 						$plan_value = pafe_get_field_value($form['settings']['pafe_stripe_subscriptions_field'], $fields);
				// 						if (!empty($plan_value)) {
				// 							foreach ($subscriptions as $subscription_item) {
				// 								if (!empty($subscription_item['pafe_stripe_subscriptions_field_enable_repeater']) && !empty($subscription_item['pafe_stripe_subscriptions_field_value'])) {
				// 									if ($plan_value == $subscription_item['pafe_stripe_subscriptions_field_value']) {
				// 										$interval = $subscription_item['pafe_stripe_subscriptions_interval'];
				// 										$interval_count = $subscription_item['pafe_stripe_subscriptions_interval_count'];
				// 										if (!empty($interval) && !empty($interval_count)) {
				// 											if (!empty($subscription_item['pafe_stripe_subscriptions_amount_field_enable'])) {
				// 												if (!empty($subscription_item['pafe_stripe_subscriptions_amount_field'])) {
				// 													$amount = floatval( pafe_get_field_value($subscription_item['pafe_stripe_subscriptions_amount_field'], $fields) ) * 100;
				// 												}
				// 											} else {
				// 												if (!empty($subscription_item['pafe_stripe_subscriptions_amount'])) {
				// 													$amount = floatval( $subscription_item['pafe_stripe_subscriptions_amount'] ) * 100;
				// 												}
				// 											}
				// 										}
				// 									}
				// 								}
				// 							}
				// 						}
				// 					}
				// 				}
								
				// 				if (!empty($amount) && !empty($interval) && !empty($interval_count)) {
				// 					$plan = \Stripe\Plan::create([
				// 						"amount" => $amount,
				// 						"currency" => $currency,
				// 						"interval" => $interval,
				// 						"interval_count" => $interval_count,
				// 						"metadata" => $fields_metadata,
				// 						"product" => [
				// 							"name" => $product_name,
				// 							"metadata" => $fields_metadata,
				// 						],
				// 					]);

				// 					$subscription = \Stripe\Subscription::create([
				// 						"customer" => $customer->id,
				// 						"metadata" => $fields_metadata,
				// 						"items" => [
				// 							[
				// 								"plan" => $plan->id,
				// 							],
				// 						]
				// 					]);

				// 					$payment_status = $subscription->status;
				// 					$payment_id = $subscription->id;
				// 				}
				// 			}
				// 		}
				// 	}

				// 	// Webhook
				// 	$fields_data['payment_id'] = $payment_id;
				// 	$fields_data['payment_status'] = $payment_status;
				// }

				if (!empty($_POST['payment_intent_id'])) {
					
					\Stripe\Stripe::setApiKey(get_option('piotnet-addons-for-elementor-pro-stripe-secret-key'));

					$intent = \Stripe\PaymentIntent::retrieve(
						$_POST['payment_intent_id']
					);

					$charge = $intent;

					$payment_id = $intent->id;
					$payment_status = $intent->status;

					// Webhook
					$fields_data['payment_id'] = $payment_id;
					$fields_data['payment_status'] = $payment_status;
				}

				// Paypal

				if (!empty($_POST['paypal_transaction_id'])) {
					$payment_id = $_POST['paypal_transaction_id'];
					$payment_status = 'succeeded';

					// Webhook
					$fields_data['payment_id'] = $payment_id;
					$fields_data['payment_status'] = $payment_status;
				}

				// Recaptcha

				$recaptcha_check = 1;

				if (!empty($_POST['recaptcha'])) {

					// Build POST request:
				    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
				    $recaptcha_secret = get_option('piotnet-addons-for-elementor-pro-recaptcha-secret-key');
				    $recaptcha_response = $_POST['recaptcha'];

					$recaptcha_request = [
						'body' => [
							'secret' => $recaptcha_secret,
							'response' => $recaptcha_response,
							'remoteip' => $_POST['remote_ip'],
						],
					];

					$recaptcha = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $recaptcha_request );

					$recaptcha = json_decode( wp_remote_retrieve_body( $recaptcha ) );

				    // Take action based on the score returned:
				    if ($recaptcha->score >= 0.5) {
				        // Verified - send email
				    } else {
				        // Not verified - show form error
				        $recaptcha_check = 0;
				    }
				}

				// Honeypot

				foreach ($fields as $key_field=>$field) {
					if ($fields[$key_field]['name'] == 'honeypot') {
						if (!empty($fields[$key_field]['value'])) {
							$recaptcha_check = 0;
						}
					}
				}

				if (!empty($form['settings']['remove_empty_form_input_fields'])) {
					$fields_new = array();
				    foreach ($fields as $field) {
				    	if (!isset($field['calculation_results'])) {
				    		if (!empty($field['value'])) {
					    		$fields_new[] = $field;
					    	}
				    	} else {
				    		if (!empty($field['calculation_results'])) {
					    		$fields_new[] = $field;
					    	}
				    	}
				    }
				    $fields = $fields_new;
				}

				// Filter Hook
					
				$fields = apply_filters( 'pafe/form_builder/fields', $fields );

				// Google Sheets

				if (!empty($form['settings']['pafe_form_google_sheets_connector_enable']) && !empty($form['settings']['pafe_form_google_sheets_connector_field_list']) && !empty($form['settings']['pafe_form_google_sheets_connector_id'])) {
					$row = '';
					$fieldList = $form['settings']['pafe_form_google_sheets_connector_field_list'];
					$columnArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
					$fieldColumns = array();

					for ($i = 0; $i < count($fieldList); $i++) {
		            	$fieldColumns[] = getIndexColumn($fieldList[$i]['pafe_form_google_sheets_connector_field_column']); 
			        }

					for ($z = 0; $z < (max($fieldColumns) + 1); $z++) {
						$value = '';

					 	for ($i = 0; $i < count($fieldList); $i++) {
				            $fieldID = $fieldList[$i]['pafe_form_google_sheets_connector_field_id'];
				            $fieldColumn = $fieldList[$i]['pafe_form_google_sheets_connector_field_column'];

			            	if ($z == getIndexColumn($fieldColumn)) {
			            		for($j=0; $j < count($fieldList); $j++) {
			            			// if ($fields[$j]['name'] == $fieldID) {
			            			// 	$value = $fields[$j]['value'];
			            			// }
			            			$value = pafe_get_field_value($fieldID,$fields,$payment_status, $payment_id);
				        		}
			            	}  
				        }

				        $row .= '"' . $value . '",';
			        }
				   
				    // Submission
				    $row = rtrim($row, ',');
				    // Config
				    $gs_sid = $form['settings']['pafe_form_google_sheets_connector_id']; // Enter your Google Sheet ID here
				    $gs_clid = get_option('piotnet-addons-for-elementor-pro-google-sheets-client-id'); // Enter your API Client ID here
				    $gs_clis = get_option('piotnet-addons-for-elementor-pro-google-sheets-client-secret'); // Enter your API Client Secret here
				    $gs_rtok = get_option('piotnet-addons-for-elementor-pro-google-sheets-refresh-token'); // Enter your OAuth Refresh Token here
				    $gs_atok = false;
				    $gs_url = 'https://sheets.googleapis.com/v4/spreadsheets/' . $gs_sid . '/values/A1:append?includeValuesInResponse=false&insertDataOption=INSERT_ROWS&responseDateTimeRenderOption=SERIAL_NUMBER&responseValueRenderOption=FORMATTED_VALUE&valueInputOption=USER_ENTERED';
				    $gs_body = '{"majorDimension":"ROWS", "values":[[' . $row . ']]}';

				    // HTTP Request Token Refresh

				    $google_sheets_request = [
						'body' => [],
						'headers' => array(
					        'Content-type' => 'application/x-www-form-urlencoded',
					    ),
					];

					$google_sheets = wp_remote_post( 'https://www.googleapis.com/oauth2/v4/token?client_id=' . $gs_clid . '&client_secret=' . $gs_clis . '&refresh_token=' . $gs_rtok . '&grant_type=refresh_token', $google_sheets_request );
					$google_sheets = json_decode( wp_remote_retrieve_body( $google_sheets) );

					if (!empty($google_sheets->access_token)) {
						$gs_atok = $google_sheets->access_token;
						$google_sheets_request_send = [
							'body' => $gs_body,
							'headers' => array(
								'Content-length' => strlen( $gs_body ),
						        'Content-type' => 'application/json',
						        'Authorization' => 'OAuth ' . $gs_atok,
						    ),
						];
						$google_sheets_send = wp_remote_post( $gs_url, $google_sheets_request_send );
					}
				}

				// repeater

				$fields_array = array();

				foreach ($fields as $field) {
					$repeater_id = $field['repeater_id'];
					$repeater_index = $field['repeater_index'];
					$repeater_label = $field['repeater_label'];

					if (!empty($repeater_id)) {
						$repeater_id_array = array_reverse( explode(',', rtrim($repeater_id, ',')) );
						$repeater_id_array_new = array();

						if (strpos(rtrim($repeater_id, ','), ',') !== false) {
							for ($i=0; $i < count($repeater_id_array); $i++) { 
								if ($i != count($repeater_id_array) - 1) {
									$repeater_id_array_new[] = str_replace('|' . $field['name'], '', $repeater_id_array[$i]);
								} else {
									$repeater_id_array_new[] = $repeater_id_array[$i];
								}
							}
						} else {
							$repeater_id_array_new = $repeater_id_array;
						}

						$path = join(",",$repeater_id_array_new);
						$path = str_replace('|', ',', $path);
						$path = explode(',',$path);

						set_val($fields_array,$path,$field['value']);
					} else {
						$field['repeater'] = false;
						$fields_array[$field['name']] = $field;
					}
				}

				array_walk($fields_array, function (& $item) {
					foreach ($item as $key => $value) {
						if (strpos($key, 'index') === 0) {
							$key_new = str_replace('index', '', $key);
							$item[$key_new] = $item[$key];
							unset($item[$key]);
						}
					}
				});

				$form_database_post_id = 0;

				if ($recaptcha_check == 1) {

					// Add to Form Database

					if (empty($form['settings']['form_database_disable'])) {
						$my_post = array(
							'post_title'    => wp_strip_all_tags( 'Piotnet Addons Form Database ' . $form_id ),
							'post_status'   => 'publish',
							'post_type'		=> 'pafe-form-database',
						);

						$form_database_post_id = wp_insert_post( $my_post );

						if (!empty($form_database_post_id)) {

							$my_post_update = array(
								'ID'           => $form_database_post_id,
								'post_title'   => '#' . $form_database_post_id,
							);
							wp_update_post( $my_post_update );

							update_post_meta( $form_database_post_id, 'form_id', $form['settings']['form_id'] );
							update_post_meta( $form_database_post_id, 'form_id_elementor', $form_id );
							update_post_meta( $form_database_post_id, 'post_id', $post_id );

							$repeater = array();
							$fields_database = array();

							foreach ($fields as $field) {

								if (!empty($field['repeater_id'])) {
									if (substr_count($field['repeater_id'],',') == 1) {
										$repeater_id = explode('|', $field['repeater_id']);

										if (!in_array($repeater_id[0], $repeater)) {
											$repeater[$repeater_id[0]] = array(
												'repeater_id' => $repeater_id[0],
												'repeater_label' => $field['repeater_label'],
											);
										}
									}
								} else {
									if (strpos($field['name'], 'pafe-end-repeater') === false) {
										$fields_database[] = array(
											'name' => $field['name'],
											'value' => $field['value'],
										);
									}
								}
							}

							foreach ($repeater as $repeater_item) {
								$fields_database[] = array(
									'name' => $repeater_item['repeater_id'],
									'value' => pafe_get_field_value( '[repeater id="' . $repeater_item['repeater_id'] . '"]', $fields ),
								);
							}

							foreach ($fields_database as $field) {
								update_post_meta( $form_database_post_id, $field['name'], rtrim( str_replace('\n', '
', $field['value']) ));
							}

							if (!empty($charge)) {
								update_post_meta( $form_database_post_id, 'payment_id', $charge->id );
								update_post_meta( $form_database_post_id, 'payment_customer_id', $charge->customer );
								update_post_meta( $form_database_post_id, 'payment_description', $charge->description );
								update_post_meta( $form_database_post_id, 'payment_amount', $charge->amount );
								update_post_meta( $form_database_post_id, 'payment_currency', $charge->currency );
								update_post_meta( $form_database_post_id, 'payment_status', $charge->status );
							}

							if (!empty($_POST['paypal_transaction_id'])) {
								update_post_meta( $form_database_post_id, 'payment_id', $payment_id );
								update_post_meta( $form_database_post_id, 'transaction_id', $payment_id );
								update_post_meta( $form_database_post_id, 'payment_status', $payment_status );
							}
						}

					}

					// End add to Form Database

					// Webhook

					if( in_array('webhook', $form['settings']['submit_actions']) && !empty($form['settings']['webhooks'])) {
						$repeater = array();

						foreach ($fields as $field) {
							$field_name = $field['name'];

							if (strpos($field['name'], 'pafe-end-repeater') === false && empty($field['repeater_id'])) {
								$fields_data[$field_name]['id'] = $field['name'];
								$fields_data[$field_name]['title'] = $field['label'];
								$fields_data[$field_name]['value'] = $field['value'];
							}

							if (!empty($field['repeater_id'])) {
								if (substr_count($field['repeater_id'],',') == 1) {
									$repeater_id = explode('|', $field['repeater_id']);

									if (!in_array($repeater_id[0], $repeater)) {
										$repeater[$repeater_id[0]] = array(
											'repeater_id' => $repeater_id[0],
											'repeater_label' => $field['repeater_label'],
										);
									}
								}

							}
							
						}

						foreach ($repeater as $repeater_item) {
							$fields_data[$repeater_item['repeater_id']]['id'] = $repeater_item['repeater_id'];
							$fields_data[$repeater_item['repeater_id']]['title'] = $repeater_item['repeater_label'];
							$fields_data[$repeater_item['repeater_id']]['value'] = $fields_array[$repeater_item['repeater_id']];
						}

						$body['fields'] = $fields_data;

						$body['form']['id'] = $form['settings']['form_id'];

						$args = [
							'body' => $body,
						];

						$response = wp_remote_post( replace_email($form['settings']['webhooks'], $fields), $args );
					}

					// Submit Post

					if( in_array('submit_post', $form['settings']['submit_actions']) ) {
						$sp_post_type = $form['settings']['submit_post_type'];
						$sp_post_taxonomy = $form['settings']['submit_post_taxonomy'];
						$sp_terms = $form['settings']['submit_post_terms_list'];
						$sp_term_slug = $form['settings']['submit_post_term_slug'];
						$sp_status = $form['settings']['submit_post_status'];
						$sp_title = $form['settings']['submit_post_title'];
						$sp_content = get_field_name_shortcode( $form['settings']['submit_post_content'] );
						$sp_term = get_field_name_shortcode( $form['settings']['submit_post_term'] );
						$sp_featured_image = get_field_name_shortcode( $form['settings']['submit_post_featured_image'] );
						$sp_custom_fields = $form['settings']['submit_post_custom_fields_list'];

						$post_title = $post_content = $post_tags = $post_term = $post_featured_image = '';

						$post_title = replace_email($sp_title, $fields);

						foreach ($fields as $field) {
							// if ($field['name'] == $sp_title) {
							// 	$post_title = $field['value'];
							// }
							if ($field['name'] == $sp_content) {
								$post_content = $field['value'];
							}
							if ($field['name'] == $sp_term) {
								$post_term = $field['value'];
							}
							if ($field['name'] == $sp_featured_image) {
								$post_featured_image = $field['value'];
							}
						}

						if ( !empty($post_title) ) {
							$submit_post = array(
								'post_type'		=> $sp_post_type,
								'post_status'   => $sp_status,
								'post_title'    => wp_strip_all_tags( $post_title ),
								'post_content'  => $post_content,
							);

							if (empty($_POST['edit'])) {
								$submit_post_id = wp_insert_post( $submit_post );
							} else {
								$submit_post_id = intval($_POST['edit']);

								$submit_post = array(
									'ID'            => $submit_post_id,
									'post_type'		=> $sp_post_type,
									'post_title'    => wp_strip_all_tags( $post_title ),
									'post_content'  => $post_content,
								);

								wp_update_post( $submit_post );
							}

							if (!empty($post_featured_image)) {
								$post_featured_image_array = explode(',', $post_featured_image);
								$post_featured_image_id = attachment_url_to_postid( $post_featured_image_array[0] );
								if (!empty($post_featured_image_id)) {
									set_post_thumbnail( $submit_post_id, intval( $post_featured_image_id ) );
								} else {
									// Gives us access to the download_url() and wp_handle_sideload() functions
									require_once( ABSPATH . 'wp-admin/includes/file.php' );

									// URL to the WordPress logo
									$url = $post_featured_image_array[0];
									$timeout_seconds = 15;

									// Download file to temp dir
									$temp_file = download_url( $url, $timeout_seconds );

									if ( !is_wp_error( $temp_file ) ) {

										// Array based on $_FILE as seen in PHP file uploads
										$file = array(
											'name'     => basename($url), // ex: wp-header-logo.png
											'type'     => 'image/png',
											'tmp_name' => $temp_file,
											'error'    => 0,
											'size'     => filesize($temp_file),
										);

										$overrides = array(
											// Tells WordPress to not look for the POST form
											// fields that would normally be present as
											// we downloaded the file from a remote server, so there
											// will be no form fields
											// Default is true
											'test_form' => false,

											// Setting this to false lets WordPress allow empty files, not recommended
											// Default is true
											'test_size' => true,
										);

										// Move the temporary file into the uploads directory
										$results = media_handle_sideload( $file, $submit_post_id );

										if ( !is_wp_error($results) ) {
											$post_featured_image_id = $results;
											if (!empty($post_featured_image_id)) {
												set_post_thumbnail( $submit_post_id, intval( $post_featured_image_id ) );
											}
										}

									}
								}
							}

							if (!empty($sp_post_taxonomy) && empty($sp_terms)) {
								$sp_post_taxonomy = explode('-', $sp_post_taxonomy);
								$sp_post_taxonomy = $sp_post_taxonomy[0];
								if (!empty($sp_term_slug)) {
									wp_set_object_terms( $submit_post_id, $sp_term_slug, $sp_post_taxonomy );
								}
								if (!empty($sp_term)) {
									wp_set_object_terms( $submit_post_id, $post_term, $sp_post_taxonomy );
								}
							}

							if (!empty($sp_terms)) {
								foreach ($sp_terms as $sp_terms_item) {
									$sp_post_taxonomy = explode('|', $sp_terms_item['submit_post_taxonomy']);
									$sp_post_taxonomy = $sp_post_taxonomy[0];
									$sp_term_slug = $sp_terms_item['submit_post_terms_slug'];
									$sp_term = get_field_name_shortcode( $sp_terms_item['submit_post_terms_field_id'] );
									$post_term = '';
									foreach ($fields as $field) {
										if ($field['name'] == $sp_term) {
											if (strpos($field['value'], ',') !== false) {
												$post_term = explode(',', $field['value']);
											} else {
												$post_term = $field['value'];
											}
										}
									}
									
									$terms_array = array();

									if (!empty($sp_term_slug)) {
										$terms_array[] = $sp_term_slug;
									}

									if (!empty($post_term)) {
										if (is_array($post_term)) {
											$terms_array = array_merge($terms_array,$post_term);
										} else {
											$terms_array[] = $post_term;
										}
									}

									wp_set_object_terms( $submit_post_id, $terms_array, $sp_post_taxonomy );
								}
							}

							foreach ($sp_custom_fields as $sp_custom_field) {
								if ( !empty( $sp_custom_field['submit_post_custom_field'] ) ) {
									$custom_field_value = '';
									$meta_type = $sp_custom_field['submit_post_custom_field_type'];

									foreach ($fields as $field) {
										if ($field['name'] == get_field_name_shortcode( $sp_custom_field['submit_post_custom_field_id'] )) {
											$custom_field_value = $field['value'];
											$custom_field_value_array = $field;
										}
									}

									if ($meta_type == 'repeater') {
										foreach ($fields_array as $field_key => $value) {
											if ($field_key == get_field_name_shortcode( $sp_custom_field['submit_post_custom_field_id'] )) {
												$custom_field_value = $value;
											}
										}

										if (!empty($custom_field_value)) {

											array_walk($custom_field_value, function (& $item) {
												foreach ($item as $key => $value) {
													$field_object = get_field_object(acf_get_field_key( $key, $submit_post_id ));
													if (!empty($field_object)) {
														$field_type = $field_object['type'];

														$item_value = $value;

														if ($field_type == 'image') {
															$image_array = explode(',', $item_value);
															$image_id = attachment_url_to_postid( $image_array[0] );
															if (!empty($image_id)) {
																$item_value = $image_id;
															}
														}

														if ($field_type == 'gallery') {
															$images_array = explode(',', $item_value);
															$images_id = array();
															foreach ($images_array as $images_item) {
																if (!empty($images_item)) {
																	$image_id = attachment_url_to_postid( $images_item );
																	if (!empty($image_id)) {
																		$images_id[] = $image_id;
																	}
																}
															}
															if (!empty($images_id)) {
																$item_value = $images_id;
															}
														}

														if ($field_type == 'select' && strpos($item_value, ',') !== false || $meta_type == 'checkbox') {
															$item_value = explode(',', $item_value);
														}

														if ($field_type == 'date') {
															$time = strtotime( $item_value );
															
															if (empty($item_value)) {
																$item_value = '';
															} else {
																$item_value = date('Ymd',$time);
															}
														}

														if ($field_type == 'time') {
															$time = strtotime( $item_value );
															$item_value = date('H:i:s',$time);
														}

														// if ($meta_type == 'google_map') {
														// 	$custom_field_value = array('address' => $custom_field_value_array['value'], 'lat' => $custom_field_value_array['lat'], 'lng' => $custom_field_value_array['lng'], 'zoom' => $custom_field_value_array['zoom']);
														// }

														$item[$key] = $item_value;
													}
												}
											});
										}
									}

									//if (!empty($custom_field_value)) {
										if (function_exists('update_field') && $form['settings']['submit_post_custom_field_source'] == 'acf_field') {

											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												$image_id = attachment_url_to_postid( $image_array[0] );
												if (!empty($image_id)) {
													$custom_field_value = $image_id;
												}
											}

											if ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												$images_id = array();
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														$image_id = attachment_url_to_postid( $images_item );
														if (!empty($image_id)) {
															$images_id[] = $image_id;
														}
													}
												}
												if (!empty($images_id)) {
													$custom_field_value = $images_id;
												}
											}

											if ($meta_type == 'select' && strpos($custom_field_value, ',') !== false || $meta_type == 'checkbox') {
												$custom_field_value = explode(',', $custom_field_value);
											}

											if ($meta_type == 'date') {
												$time = strtotime( $custom_field_value );

												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('Ymd',$time);
												}
											}

											if ($meta_type == 'time') {
												$time = strtotime( $custom_field_value );
												
												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('H:i:s',$time);
												}
											}

											if ($meta_type == 'google_map') {
												$custom_field_value = array('address' => $custom_field_value_array['value'], 'lat' => $custom_field_value_array['lat'], 'lng' => $custom_field_value_array['lng'], 'zoom' => $custom_field_value_array['zoom']);
											}

											update_field( $sp_custom_field['submit_post_custom_field'], $custom_field_value, $submit_post_id );

										} elseif ($form['settings']['submit_post_custom_field_source'] == 'toolset_field') {

											$meta_key = 'wpcf-' . $sp_custom_field['submit_post_custom_field'];

											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												if (!empty($image_array)) {
													update_post_meta( $submit_post_id, $meta_key, $image_array[0] );
												}
											} elseif ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												delete_post_meta( $submit_post_id, $meta_key);
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														add_post_meta( $submit_post_id, $meta_key, $images_item );
													}
												}
											} elseif ($meta_type == 'checkbox') {
												$custom_field_value = explode(',', $custom_field_value);

												$field_toolset = wpcf_admin_fields_get_field($sp_custom_field['submit_post_custom_field']);

												if (isset($field_toolset['data']['options'])){
											        $res = array();
												    foreach ($field_toolset['data']['options'] as $key => $option){
												        if (in_array($option['set_value'], $custom_field_value)){
												            $res[$key] = $option['set_value'];
												        }
												    }   
											        update_post_meta( $submit_post_id, $meta_key , $res );
												}
											} elseif ($meta_type == 'date') {
												$custom_field_value = strtotime( $custom_field_value );
												update_post_meta( $submit_post_id, $meta_key, $custom_field_value );
											} else {

												update_post_meta( $submit_post_id, $meta_key, $custom_field_value );

											}

										} elseif ($form['settings']['submit_post_custom_field_source'] == 'jet_engine_field') {
											if ($meta_type == 'image') {
												$image_array = explode(',', $custom_field_value);
												$image_id = attachment_url_to_postid( $image_array[0] );
												if (!empty($image_id)) {
													$custom_field_value = $image_id;
												}
											}

											if ($meta_type == 'gallery') {
												$images_array = explode(',', $custom_field_value);
												$images_id = '';
												foreach ($images_array as $images_item) {
													if (!empty($images_item)) {
														$image_id = attachment_url_to_postid( $images_item );
														if (!empty($image_id)) {
															$images_id .= $image_id . ',';
														}
													}
												}
												if (!empty($images_id)) {
													$custom_field_value = rtrim($images_id, ',');
												}
											}

											if ($meta_type == 'date') {
												$time = strtotime( $custom_field_value );

												if (empty($custom_field_value)) {
													$custom_field_value = '';
												} else {
													$custom_field_value = date('Y-m-d',$time);
												}
											}

											if ($meta_type == 'select') {
												if (strpos($custom_field_value, ',') !== false) {
													$custom_field_value = explode(',', $custom_field_value);
												}
											}

											if ($meta_type == 'checkbox') {
												$value_array = array();
												$custom_field_value = explode(',', $custom_field_value);
												foreach ($custom_field_value as $item) {
													$value_array[$item] = true;
												}
												$custom_field_value = $value_array;
											}

											if ($meta_type == 'time') {
												$time = strtotime( $custom_field_value );
												$custom_field_value = date('H:i',$time);
											}

											update_post_meta( $submit_post_id, $sp_custom_field['submit_post_custom_field'], $custom_field_value );

										} else {
											update_post_meta( $submit_post_id, $sp_custom_field['submit_post_custom_field'], $custom_field_value );
										}
									//}
								}
							}

							update_post_meta( $submit_post_id, '_submit_button_id', $form_id );
							update_post_meta( $submit_post_id, '_submit_post_id', $post_id );

							$post_url = get_permalink( $submit_post_id );
						}							
					}

					// End Submit Post

					// Mailchimp V3
					if (in_array("mailchimp_v3", $form['settings']['submit_actions'])) {
						$mailchimp_acceptance = true;

						if (!empty($form['settings']['mailchimp_acceptance_field_shortcode_v3'])) {
							$mailchimp_acceptance_value = pafe_get_field_value($form['settings']['mailchimp_acceptance_field_shortcode_v3'],$fields);
							if (empty($mailchimp_acceptance_value)) {
								$mailchimp_acceptance = false;
							}
						}
						if($mailchimp_acceptance){
							$mailchimp_source = $form['settings']['mailchimp_api_key_source_v3'];
							$list_id = $form['settings']['mailchimp_list_id'];
							if($mailchimp_source == 'default'){
								$mailchimp_api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
							}else{
								$mailchimp_api_key = $form['settings']['mailchimp_api_key_v3'];
							}
							$data = [];
							$mailchimp_field_mapping = $form['settings']['mailchimp_field_mapping_list_v3'];
							if(!empty($form['settings']['mailchimp_group_id'])){
								$interests = explode(',', $form['settings']['mailchimp_group_id']);
								foreach($interests as $interest){
									$data['interests'][$interest] = true;
								}
							}
							if(!empty($list_id)){
								foreach($mailchimp_field_mapping as $field){
									if($field['mailchimp_field_mapping_tag_name_v3'] == 'email_address'){
										$memberId =  md5(strtolower(replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id)));
										$data[$field['mailchimp_field_mapping_tag_name_v3']] = replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id);
									}elseif($field['mailchimp_field_mapping_tag_name_v3'] == 'ADDRESS'){
										$data['merge_fields']['ADDRESS']['addr1'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_address_1'],$fields,$payment_status, $payment_id);
										$data['merge_fields']['ADDRESS']['addr2'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_address_1'],$fields,$payment_status, $payment_id);
										$data['merge_fields']['ADDRESS']['city'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_city'],$fields,$payment_status, $payment_id);
										$data['merge_fields']['ADDRESS']['state'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_state'],$fields,$payment_status, $payment_id);
										$data['merge_fields']['ADDRESS']['zip'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_zip'],$fields,$payment_status, $payment_id);
										$data['merge_fields']['ADDRESS']['country'] = replace_email($field['mailchimp_v3_field_mapping_address_field_shortcode_country'],$fields,$payment_status, $payment_id);
									}
									else{
										$data['merge_fields'][$field['mailchimp_field_mapping_tag_name_v3']] = replace_email($field['mailchimp_field_mapping_field_shortcode_v3'],$fields,$payment_status, $payment_id);
									}
								}
								$data['status'] = 'subscribed';
								if(!empty($data['merge_fields']['ADDRESS'])){
									if(empty($data['merge_fields']['ADDRESS']['country']) || empty($data['merge_fields']['ADDRESS']['zip']) || $data['merge_fields']['ADDRESS']['state'] || $data['merge_fields']['ADDRESS']['city']){
										echo "Please enter a valid address.";
									}
								}else{
									$helper = new PAFE_Helper();
									$mailchimp_url = 'https://' . substr($mailchimp_api_key,strpos($mailchimp_api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/'.$list_id.'/members/'.$memberId.'';
									$helper->mailchimp_curl_put_member($mailchimp_url, $mailchimp_api_key, $data);
								}
							}else{
								echo "Please enter list ID.";
							}
						}
					}

					// Mailchimp

					if (in_array("mailchimp", $form['settings']['submit_actions'])) {

						$mailchimp_acceptance = true;

						if (!empty($form['settings']['mailchimp_acceptance_field_shortcode'])) {
							$mailchimp_acceptance_value = pafe_get_field_value($form['settings']['mailchimp_acceptance_field_shortcode'],$fields);
							if (empty($mailchimp_acceptance_value)) {
								$mailchimp_acceptance = false;
							}
						}

						if ($mailchimp_acceptance) {

							$mailchimp_api_key_source = $form['settings']['mailchimp_api_key_source'];

							if ($mailchimp_api_key_source == 'default') {
								$mailchimp_api_key = get_option('piotnet-addons-for-elementor-pro-mailchimp-api-key');
							} else {
								$mailchimp_api_key = $form['settings']['mailchimp_api_key'];
							}

							$mailchimp_audience_id = $form['settings']['mailchimp_audience_id'];

							$mailchimp_field_mapping_list = $form['settings']['mailchimp_field_mapping_list'];

							if (!empty($mailchimp_api_key) && !empty($mailchimp_audience_id) && !empty($mailchimp_field_mapping_list)) {

								$MailChimp = new MailChimpPAFE($mailchimp_api_key);
								
								$merge_fields = array();

								foreach ($mailchimp_field_mapping_list as $item) {
									$key = $item['mailchimp_field_mapping_tag_name'];
									$shortcode = $item['mailchimp_field_mapping_field_shortcode'];
									if (!empty($key)) {

										if (!empty($shortcode)) {
											$merge_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
											if ($key == 'EMAIL' || $key == 'MERGE0') {
												$mailchimp_email = pafe_get_field_value($shortcode,$fields);
											}
										}

										if (!empty($item['mailchimp_field_mapping_address'])) {
											$merge_fields[$key] = array(
												'addr1' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_address_1'],$fields,$payment_status, $payment_id),
												'addr2' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_address_2'],$fields,$payment_status, $payment_id),
												'city' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_city'],$fields,$payment_status, $payment_id),
												'state' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_state'],$fields,$payment_status, $payment_id),
												'zip' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_zip'],$fields,$payment_status, $payment_id),
												'country' => pafe_get_field_value($item['mailchimp_field_mapping_address_field_shortcode_country'],$fields,$payment_status, $payment_id),
											);
										}

									}
								}

								if (!empty($merge_fields) && !empty($mailchimp_email)) {
									$mailchimp_result = $MailChimp->post("lists/$mailchimp_audience_id/members", [
										'email_address' => $mailchimp_email,
										'merge_fields'  => $merge_fields,
										'status'        => 'subscribed',
									]);

									if ($MailChimp->success()) {
										// print_r($mailchimp_result);	
									} else {
										// echo $MailChimp->getLastError();
									}
								}
							}
						}
					}

					//Mailpoet
					if (in_array("mailpoet", $form['settings']['submit_actions'])) {
						$mailpoet_acceptance = true;
						if (!empty($form['settings']['mailpoet_acceptance_field_shortcode'])) {
							
							$mailpoet_acceptance_value = pafe_get_field_value($form['settings']['mailpoet_acceptance_field_shortcode'],$fields);
							if (empty($mailpoet_acceptance_value)) {
								$mailpoet_acceptance = false;
							}
						}
						if (class_exists(\MailPoet\API\API::class)) {
							if($mailpoet_acceptance == true){
								$mailpoet_api = \MailPoet\API\API::MP('v1');
								$mailpoet_field_mapping_list = $form['settings']['mailpoet_field_mapping_list'];
								$mailpoet_list = $form['settings']['mailpoet_select_list'];
								foreach($mailpoet_field_mapping_list as $item){
									$data[$item['mailpoet_field_mapping_tag_name']] = pafe_get_field_value($item['mailpoet_field_mapping_field_shortcode'],$fields,$payment_status, $payment_id);
								}
								$mailpoet_send_confirmation_email = !empty($form['settings']['mailpoet_send_confirmation_email']) ? true : false;
								$mailpoet_schedule_welcome_email = !empty($form['settings']['mailpoet_send_welcome_email']) ? true : false;
								$mailpoet_skip_subscriber_notification = !empty($form['settings']['skip_subscriber_notification']) ? true : false;
								$options = [
									'send_confirmation_email' => $mailpoet_send_confirmation_email,
									'schedule_welcome_email' => $mailpoet_schedule_welcome_email,
									'skip_subscriber_notification' => $mailpoet_skip_subscriber_notification
								];
								$result = $mailpoet_api->addSubscriber($data, $mailpoet_list, $options);
							}
						}else{
							echo "Please install Mailpoet plugin.";
						}
					}

					//Mailerlite V2
					if (in_array("mailerlite_v2", $form['settings']['submit_actions'])) {
						$mailerlite_acceptance = true;
						if (!empty($form['settings']['mailerlite_api_acceptance_field_shortcode'])) {
							$mailerlite_acceptance_value = pafe_get_field_value($form['settings']['mailerlite_api_acceptance_field_shortcode'],$fields);
							if (empty($mailerlite_acceptance_value)) {
								$mailerlite_acceptance = false;
							}
						}
						if ($mailerlite_acceptance) {
							$mailerlite_api = $form['settings']['mailerlite_api_key_source_v2'];
						}
						if ($mailerlite_api == 'default') {
							$mailerlite_api_key = get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key');
						}else{
							$mailerlite_api_key = $form['settings']['mailerlite_api_key_v2'];
						}
						$mailerlite_api_group = $form['settings']['mailerlite_api_group'];
						$mailerlite_api_url = !empty($mailerlite_api_group) ? 'https://api.mailerlite.com/api/v2/groups/'.$mailerlite_api_group.'/subscribers' : 'https://api.mailerlite.com/api/v2/subscribers';
						$mailerlite_field_mapping_list = $form['settings']['mailerlite_api_field_mapping_list_v2'];
						if(!empty($mailerlite_field_mapping_list)){
							$mailerlite_data = [];
							foreach($mailerlite_field_mapping_list as $item){
								if($item['mailerlite_api_field_mapping_tag_name_v2'] == 'name' || $item['mailerlite_api_field_mapping_tag_name_v2'] == 'email'){
									$mailerlite_data[$item['mailerlite_api_field_mapping_tag_name_v2']] = pafe_get_field_value($item['mailerlite_api_field_mapping_field_shortcode_v2'],$fields,$payment_status, $payment_id);
								}else{
									$mailerlite_data['fields'][$item['mailerlite_api_field_mapping_tag_name_v2']] =  pafe_get_field_value($item['mailerlite_api_field_mapping_field_shortcode_v2'],$fields,$payment_status, $payment_id);
								}
							}
						}
						$curl = curl_init();
						curl_setopt_array($curl, array(
							CURLOPT_URL => $mailerlite_api_url,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => "",
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 30,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => "POST",
							CURLOPT_POSTFIELDS => json_encode($mailerlite_data),
							CURLOPT_HTTPHEADER => array(
								"content-type: application/json",
								"x-mailerlite-apikey: ".$mailerlite_api_key.""
							),
						));

						$response = curl_exec($curl);
						$err = curl_error($curl);

						curl_close($curl);
					}

					//PDF Genrenator
					if (in_array("pdfgenerator", $form['settings']['submit_actions'])) {
						$pdf_generator_list = $form['settings']['pdfgenerator_field_mapping_list'];
						$pdf_page_size = $form['settings']['pdfgenerator_size'];
						$pfd_font_ratio = 0.9;
						$pdf = new PAFE_Helper();
						$pdf->AddPage('', $pdf_page_size);
						
						$pdf_color = hexToRgb($form['settings']['pdfgenerator_color']);
						
						$pdf->AddFont('dejaVu','','DejaVuSans.ttf',true);
						$pdf->SetFont('dejaVu','',$form['settings']['pdfgenerator_font_size']['size']* $pfd_font_ratio);
						
						$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
						
						if( $form['settings']['pdfgenerator_background_image_enable'] == 'yes'){
							if(isset( $form['settings']['pdfgenerator_background_image']['url'])){
								$pdf_generator_image =  $form['settings']['pdfgenerator_background_image']['url'];
							}
						}
						
						if(!empty($pdf_generator_image)){
							$pdf->Image($pdf_generator_image,0,0,210);
						}
						if(!empty($form['settings']['pdfgenerator_title'])){
							$pdf->SetFont('dejaVu','',$form['settings']['pdfgenerator_title_font_size']['size'] * $pfd_font_ratio);	
							$pdf->Cell(0,5,replace_email($form['settings']['pdfgenerator_title'],$fields,$payment_status, $payment_id),0,1,strtoupper(substr($form['settings']['pdfgenerator_title_text_align'],0,1)));
							$pdf->Ln(15);
						}
						
						if($form['settings']['pdfgenerator_set_custom']=='yes'){
							foreach($pdf_generator_list as $item){
								if($item['custom_font'] == 'yes'){
									$pdf->SetFont('dejaVu','',$item['font_size']['size'] * $pfd_font_ratio);	
									$pdf_color = hexToRgb($item['color']);
									$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
								}else{
									$pdf->SetFont('dejaVu','',$form['settings']['pdfgenerator_font_size']['size'] * $pfd_font_ratio);
									$pdf_color = hexToRgb($form['settings']['pdfgenerator_color']);
									$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
								}
								if($form['settings']['pdfgenerator_size'] == 'a3'){
									$item_x = floatval($item['pdfgenerator_set_x']['size']) * 2.97;
									$item_y = floatval($item['pdfgenerator_set_y']['size']) * 4.2;
									$item_width = floatval($item['pdfgenerator_width']['size']) * 2.97;
									$image_height = floatval($item['pdfgenerator_height']['size']) * 4.2;
									$item_image_x = floatval($item['pdfgenerator_image_set_x']['size']) * 2.97;
									$item_image_y = floatval($item['pdfgenerator_image_set_y']['size']) * 4.2;
								}elseif($form['settings']['pdfgenerator_size'] == 'a4'){
									$item_x = floatval($item['pdfgenerator_set_x']['size']) * 2.1;
									$item_y = floatval($item['pdfgenerator_set_y']['size']) * 2.97;
									$item_width = floatval($item['pdfgenerator_width']['size']) * 2.1;
									$image_height = floatval($item['pdfgenerator_height']['size']) * 2.97;
									$item_image_x = floatval($item['pdfgenerator_image_set_x']['size']) * 2.1;
									$item_image_y = floatval($item['pdfgenerator_image_set_y']['size']) * 2.97;
								}elseif($form['settings']['pdfgenerator_size'] == 'a5'){
									$item_x = floatval($item['pdfgenerator_set_x']['size']) * 1.48;
									$item_y = floatval($item['pdfgenerator_set_y']['size']) * 2.1;
									$item_width = floatval($item['pdfgenerator_width']['size']) * 1.48;
									$image_height = floatval($item['pdfgenerator_height']['size']) * 2.1;
									$item_image_x = floatval($item['pdfgenerator_image_set_x']['size']) * 1.48;
									$item_image_y = floatval($item['pdfgenerator_image_set_y']['size']) * 2.1;
								}elseif($form['settings']['pdfgenerator_size'] == 'letter'){
									$item_x = floatval($item['pdfgenerator_set_x']['size']) * 2.159;
									$item_y = floatval($item['pdfgenerator_set_y']['size']) * 2.794;
									$item_width = floatval($item['pdfgenerator_width']['size']) * 2.159;
									$image_height = floatval($item['pdfgenerator_height']['size']) * 2.794;
									$item_image_x = floatval($item['pdfgenerator_image_set_x']['size']) * 2.159;
									$item_image_y = floatval($item['pdfgenerator_image_set_y']['size']) * 2.794;
								}else{
									$item_x = floatval($item['pdfgenerator_set_x']['size']) * 2.159;
									$item_y = floatval($item['pdfgenerator_set_y']['size']) * 3.556;
									$item_width = floatval($item['pdfgenerator_width']['size']) * 2.159;
									$image_height = floatval($item['pdfgenerator_height']['size']) * 3.556;
									$item_image_x = floatval($item['pdfgenerator_image_set_x']['size']) * 2.159;
									$item_image_y = floatval($item['pdfgenerator_image_set_y']['size']) * 3.556;
								}
								$type = $item['pdfgenerator_field_type'];
								if($type == 'image'){
									$pdf_image_url = !empty(replace_email($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id)) ? replace_email($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id) : plugins_url().'/piotnet-addons-for-elementor-pro/assets/images/signature.png';
									$pdf->Image($pdf_image_url, $item_image_x, $item_image_y, $item_width, $image_height);
								}elseif($type == 'image-upload'){
									$pdf_image_url = !empty($item['pdfgenerator_image_field']['url']) ? $item['pdfgenerator_image_field']['url'] : plugins_url().'/piotnet-addons-for-elementor-pro/assets/images/signature.png';
									$pdf->Image($pdf_image_url, $item_image_x, $item_image_y, $item_width);
								}
								else{
									if($item['auto_position'] == 'yes'){
										$pdf_txt = replace_email($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id);
										$pdf->WriteHTML(false, $pdf_txt);
										
									}else{
										$pdf_txt = replace_email($item['pdfgenerator_field_shortcode'],$fields,$payment_status, $payment_id);
										$pdf->WriteHTML2($pdf_txt,$item_width,$item_x, $item_y);
									}
								}
							}
						}else{
							$pdf->SetFont('dejaVu','',$form['settings']['pdfgenerator_heading_field_mapping_font_size']['size'] * $pfd_font_ratio);
							$pdf_color = hexToRgb($form['settings']['pdfgenerator_heading_field_mapping_color']);
							$pdf->SetTextColor($pdf_color['r'], $pdf_color['g'], $pdf_color['b']);
							if(strtoupper(substr($form['settings']['pdfgenerator_heading_field_mapping_text_align'],0,1)) == 'L'){
								$image_alignment = 0;
							}elseif(strtoupper(substr($form['settings']['pdfgenerator_heading_field_mapping_text_align'],0,1)) == 'C'){
								$image_alignment = 50;
							}else{
								$image_alignment = 100;
							}
							foreach($fields as $item){
								if($form['settings']['pdfgenerator_heading_field_mapping_show_label'] == 'yes' && !empty($item['label'])){
									$pdf_text = $item['label'] .': '. replace_email($item['value'],$fields,$payment_status, $payment_id).'<br>';
								}else{
									$pdf_text = replace_email($item['value'],$fields,$payment_status, $payment_id).'<br>';
								}
								if(!empty($item['type']) && $item['type'] == 'signature' || !empty($item['type']) && $item['type'] == 'image'){
									if($form['settings']['pdfgenerator_heading_field_mapping_show_label'] == 'yes' && !empty($item['label'])){
										$pdf->Cell(0,5,$item['label'],0,2, strtoupper(substr($form['settings']['pdfgenerator_heading_field_mapping_text_align'],0,1)));
										$pdf->Image($item['value'], $image_alignment);
									}else{
										$pdf->Image($item['value'], $image_alignment);
									}
									
								}else{
									$pdf->WriteHTML(false ,$pdf_text);
									//$pdf->Cell(100,1,'',0,1);
								}
							}
						}
						
						$pdf->Output('F', $form_database_post_id . '.pdf', true, $upload_dir . '/');

						$attachment[] = WP_CONTENT_DIR . '/uploads/piotnet-addons-for-elementor/' . $form_database_post_id . '.pdf';
					}

					// MailerLite

					if (in_array("mailerlite", $form['settings']['submit_actions'])) {

						$mailerlite_api_key_source = $form['settings']['mailerlite_api_key_source'];

						if ($mailerlite_api_key_source == 'default') {
							$mailerlite_api_key = get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key');
						} else {
							$mailerlite_api_key = $form['settings']['mailerlite_api_key'];
						}

						$mailerlite_group_id = $form['settings']['mailerlite_group_id'];

						$mailerlite_email = pafe_get_field_value( $form['settings']['mailerlite_email_field_shortcode'], $fields );

						$mailerlite_field_mapping_list = $form['settings']['mailerlite_field_mapping_list'];

						if ( !empty($mailerlite_email) && !empty($mailerlite_api_key) && !empty($mailerlite_group_id) ) {

							$mailerlite_url = 'https://api.mailerlite.com/api/v2/groups/' . $mailerlite_group_id . '/subscribers';

							$mailerlite_body = array(
								'email' => $mailerlite_email,
							);

							if (!empty($mailerlite_field_mapping_list)) {
								$mailerlite_fields = array();
								foreach ($mailerlite_field_mapping_list as $item) {
									$key = $item['mailerlite_field_mapping_tag_name'];
									$shortcode = $item['mailerlite_field_mapping_field_shortcode'];
									if (!empty($key) && !empty($shortcode)) {
										if ($key != 'email') {
											$mailerlite_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
										}
									}
								}

								$mailerlite_body['fields'] = $mailerlite_fields;
							}

							$mailerlite_request_data = [
								'headers' => array(
									'X-MailerLite-ApiKey' => $mailerlite_api_key,
									'Content-Type' => 'application/json',
							    ),
							    'body' => json_encode( $mailerlite_body ),
							];

							$mailerlite_request = wp_remote_post( $mailerlite_url, $mailerlite_request_data );

						}
					}

					//Get Response

					if(in_array("getresponse", $form['settings']['submit_actions'])){
						$getresponse_api_key_source = $form['settings']['getresponse_api_key_source'];
						$form['settings']['pafe_getresponse_list'];
						if($getresponse_api_key_source == 'default'){
							$getresponse_api_key = get_option('piotnet-addons-for-elementor-pro-getresponse-api-key');
						}else{
							$getresponse_api_key = $form['settings']['getresponse_api_key'];
						}
						$getresponse_url_add_contact = "https://api.getresponse.com/v3/contacts/";
						$items = $form['settings']['getresponse_field_mapping_list'];
						if(!empty($items)){
							$get_response_fields = array();
							foreach($items as $item){
								$key = $item['getresponse_field_mapping_tag_name'];
								$shortcode = $item['getresponse_field_mapping_field_shortcode'];
								if (!empty($key) && !empty($shortcode)) {
									if($key == 'email' || $key ==  "name"){
										$get_response_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
									}elseif($item['getresponse_field_mapping_multiple'] == 'yes'){
										$get_response_fields['customFieldValues'][] = array(
											'customFieldId' => $key,
											'values' =>  pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id, 'succeeded', 'pending', 'failed', true),
										);
									}
									else{
										$get_response_fields['customFieldValues'][] = array(
											'customFieldId' => $key,
											'value' =>  array(
												pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id)
											)
										);
									}
								}
							}
							$get_response_fields['ipAddress'] = $_POST['remote_ip'];
							$get_response_fields['campaign'] = [
								'campaignId' => $form['settings']['getresponse_campaign_id']
							];
							$get_response_fields['dayOfCycle'] = $form['settings']['getresponse_date_of_cycle'];
							$data = json_encode($get_response_fields);
							$ch = curl_init($getresponse_url_add_contact);
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
							curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
								'Content-Type: application/json',
								'X-Auth-Token: api-key '.$getresponse_api_key,
							));
							
							$result =  curl_exec($ch);
						}
						
					}
					//Zoho CRM
					if (in_array("zohocrm", $form['settings']['submit_actions'])) {
						$zoho_acceptance = true;
						$helper = new PAFE_Helper();
						if (!empty($form['settings']['zoho_acceptance_field_shortcode'])) {
							
							$zoho_acceptance_value = pafe_get_field_value($form['settings']['zoho_acceptance_field_shortcode'],$fields);
							if (empty($zoho_acceptance_value)) {
								$zoho_acceptance = false;
							}
						}
						if($zoho_acceptance == true){
							$zoho_access_token = get_option('zoho_access_token');
							$zoho_refresh_token = get_option('zoho_refresh_token');
							$zoho_api_domain = get_option('zoho_api_domain');
							$zoho_request_url = $zoho_api_domain.'/crm/v2/'.$form['settings']['zohocrm_module'];
							$zoho_mapping_fields = $form['settings']['zohocrm_fields_map'];
							$data = [];
							if(!empty($zoho_mapping_fields)){
								foreach($zoho_mapping_fields as $item){
									$data[$item['zohocrm_tagname']] = replace_email($item['zohocrm_shortcode'],$fields,$payment_status, $payment_id);
								}
							}
							$zoho_result = $helper->zohocrm_post_record($data, $zoho_request_url, $zoho_access_token);
							$zoho_result = json_decode($zoho_result);
							if(!empty($zoho_result->code) && $zoho_result->code == 'INVALID_TOKEN'){
								$helper->zoho_refresh_token();
								$zoho_access_token = get_option('zoho_access_token');
								$zoho_result = $helper->zohocrm_post_record($data, $zoho_request_url, $zoho_access_token);
								echo $zoho_result;
							}
						}
					}

					//Activecampaign
					if (in_array("activecampaign", $form['settings']['submit_actions'])) {
						$activecampaign_api_key_source = $form['settings']['activecampaign_api_key_source'];

						if ($activecampaign_api_key_source == 'default') {
							$activecampaign_api_key = get_option('piotnet-addons-for-elementor-pro-activecampaign-api-key');
							$activecampaign_api_url = get_option('piotnet-addons-for-elementor-pro-activecampaign-api-url');
						} else {
							$activecampaign_api_key = $form['settings']['activecampaign_api_key'];
							$activecampaign_api_url = $form['settings']['activecampaign_api_url'];
						}

						if (!empty($form['settings']['activecampaign_field_mapping_list'])) {
							$activecampaign_fields = array();
							foreach ($form['settings']['activecampaign_field_mapping_list'] as $item) {
								$key = $item['activecampaign_field_mapping_tag_name'];
								$shortcode = $item['activecampaign_field_mapping_field_shortcode'];
								if (!empty($key) && !empty($shortcode)) {
									if (strlen(strstr($key, '%')) > 0) {
										if(strlen(strstr($key, '@multiple')) > 0){
											$key = str_replace('@multiple', '', $key);
											$activecampaign_fields['field[' . $key . ']'] = '||'.str_replace(',', '||', pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id)).'||';
										}else{
											$activecampaign_fields['field[' . $key . ']'] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
										}
									} else {
										$activecampaign_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
									}

								}
							}

							$activecampaign_list_id = 'p[' . $form['settings']['activecampaign_list'] . ']';
							$activecampaign_fields[$activecampaign_list_id] = $form['settings']['activecampaign_list'];
							$activecampaign_status = 'status[' . $form['settings']['activecampaign_list'] . ']';
							$activecampaign_fields[$activecampaign_status] = 1;
							$activecampaign_instantresponders = 'instantresponders[' . $form['settings']['activecampaign_list'] . ']';
							$activecampaign_fields[$activecampaign_instantresponders] = 1;
						}

						$activecampaign_params = array(
							'api_key'      => $activecampaign_api_key,
							'api_action'   => 'contact_add',
							'api_output'   => 'serialize',
						);

						$activecampaign_query = "";
						foreach( $activecampaign_params as $key => $value ) $activecampaign_query .= urlencode($key) . '=' . urlencode($value) . '&';
						$activecampaign_query = rtrim($activecampaign_query, '& ');

						$activecampaign_data = "";
						foreach( $activecampaign_fields as $key => $value ) $activecampaign_data .= urlencode($key) . '=' . urlencode($value) . '&';
						$activecampaign_data = rtrim($activecampaign_data, '& ');

						$activecampaign_api_url = rtrim($activecampaign_api_url, '/ ');

						if ( !function_exists('curl_init') ) die('CURL not supported. (introduced in PHP 4.0.2)');

						if ( $activecampaign_params['api_output'] == 'json' && !function_exists('json_decode') ) {
							die('JSON not supported. (introduced in PHP 5.2.0)');
						}

						$activecampaign_api = $activecampaign_api_url . '/admin/api.php?' . $activecampaign_query;

						$activecampaign_request = curl_init($activecampaign_api);
						curl_setopt($activecampaign_request, CURLOPT_HEADER, 0);
						curl_setopt($activecampaign_request, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($activecampaign_request, CURLOPT_POSTFIELDS, $activecampaign_data);
						curl_setopt($activecampaign_request, CURLOPT_FOLLOWLOCATION, true);

						$activecampaign_response = (string)curl_exec($activecampaign_request);

						curl_close($activecampaign_request);

						// if ( !$activecampaign_response ) {
						// 	die('Nothing was returned. Do you have a connection to Email Marketing server?');
						// }
					}
					// MailerLite

					if (in_array("mailerlite", $form['settings']['submit_actions'])) {

						$mailerlite_api_key_source = $form['settings']['mailerlite_api_key_source'];

						if ($mailerlite_api_key_source == 'default') {
							$mailerlite_api_key = get_option('piotnet-addons-for-elementor-pro-mailerlite-api-key');
						} else {
							$mailerlite_api_key = $form['settings']['mailerlite_api_key'];
						}

						$mailerlite_group_id = $form['settings']['mailerlite_group_id'];

						$mailerlite_email = pafe_get_field_value( $form['settings']['mailerlite_email_field_shortcode'], $fields );

						$mailerlite_field_mapping_list = $form['settings']['mailerlite_field_mapping_list'];

						if ( !empty($mailerlite_email) && !empty($mailerlite_api_key) && !empty($mailerlite_group_id) ) {

							$mailerlite_url = 'https://api.mailerlite.com/api/v2/groups/' . $mailerlite_group_id . '/subscribers';

							$mailerlite_body = array(
								'email' => $mailerlite_email,
							);

							if (!empty($mailerlite_field_mapping_list)) {
								$mailerlite_fields = array();
								foreach ($mailerlite_field_mapping_list as $item) {
									$key = $item['mailerlite_field_mapping_tag_name'];
									$shortcode = $item['mailerlite_field_mapping_field_shortcode'];
									if (!empty($key) && !empty($shortcode)) {
										if ($key != 'email') {
											$mailerlite_fields[$key] = pafe_get_field_value($shortcode,$fields,$payment_status, $payment_id);
										}
									}
								}

								$mailerlite_body['fields'] = $mailerlite_fields;
							}

							$mailerlite_request_data = [
								'headers' => array(
									'X-MailerLite-ApiKey' => $mailerlite_api_key,
									'Content-Type' => 'application/json',
							    ),
							    'body' => json_encode( $mailerlite_body ),
							];

							$mailerlite_request = wp_remote_post( $mailerlite_url, $mailerlite_request_data );

						}
					}

					// Booking

					if (in_array("booking", $form['settings']['submit_actions'])) {

						$pafe_form_booking = array();

						foreach ($fields as $key => $field) {
							if (!empty($field['booking'])) {

								$booking = $field['booking'];
								foreach ($booking as $booking_key => $booking_item) {
									$booking_item = json_decode($booking_item, true);
									if ( !empty($booking_item['pafe_form_booking_date_field']) ) {
										$date = date( "Y-m-d", strtotime( replace_email($booking_item['pafe_form_booking_date_field'], $fields) ) );
									}
									$pafe_form_booking = array_merge($pafe_form_booking, array($booking_item) );
								}
							}
						}

						$pafe_form_booking = array_unique($pafe_form_booking, SORT_REGULAR);

						foreach ($pafe_form_booking as $booking) {

							if ( empty($booking['pafe_form_booking_date_field']) ) {
								$date = date( "Y-m-d", strtotime( $booking['pafe_form_booking_date'] ) );
							} else {									
								$date = date( "Y-m-d", strtotime( replace_email($booking['pafe_form_booking_date_field'], $fields) ) );
							}

							$slot_availble = 0;
							$slot = $booking['pafe_form_booking_slot'];
							$slot_query = new WP_Query(array(  
								'posts_per_page' => -1 , 
								'post_type' => 'pafe-form-booking',
								'meta_query' => array(                  
							       'relation' => 'AND',                 
								        array(
								            'key' => 'pafe_form_booking_id',                
								            'value' => $booking['pafe_form_booking_id'],                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                  
								        ),
								        array(
								            'key' => 'pafe_form_booking_slot_id',                  
								            'value' => $booking['pafe_form_booking_slot_id'],                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                  
								        ),
								        array(
								            'key' => 'pafe_form_booking_date',                  
								            'value' => $date,                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                
								        ),
								        array(
								            'key' => 'payment_status',                  
								            'value' => 'succeeded',                  
								            'type' => 'CHAR',                  
								            'compare' => '=',                
								        ),
								),	
							));

							$slot_reserved = 0;

							if ($slot_query->have_posts()) {
								while($slot_query->have_posts()) {
									$slot_query->the_post();
									$slot_reserved += intval( get_post_meta(get_the_ID(), 'pafe_form_booking_quantity', true) );
								}
							}

							wp_reset_postdata();

							$slot_availble = $slot - $slot_reserved;

							$booking_slot = 1;

							if (!empty($booking['pafe_form_booking_slot_quantity_field'])) {
								$booking_slot = intval( replace_email($booking['pafe_form_booking_slot_quantity_field'], $fields) );
							}

							if ($slot_availble >= $booking_slot && !empty($slot_availble) && !empty($booking_slot)) {
								$booking_post = array( 
									'post_title'    =>  '#' . $form_database_post_id . ' ' . $booking['pafe_form_booking_title'],
									'post_status'   => 'publish',
									'post_type'		=> 'pafe-form-booking',
								);

								$form_booking_posts_id = wp_insert_post( $booking_post );

								if (empty($form_database_post_id)) {
									$form_database_post_id = $form_booking_posts_id;
									$booking_post = array(
										'ID' => $form_booking_posts_id,
										'post_title' =>  '#' . $form_booking_posts_id . ' ' . $booking['pafe_form_booking_title'],
									);
									wp_update_post( $booking_post );
								}

								foreach ($booking as $key_booking => $booking_data) {
									update_post_meta( $form_booking_posts_id, $key_booking, $booking_data );
								}

								update_post_meta( $form_booking_posts_id, 'pafe_form_booking_date', $date );
								update_post_meta( $form_booking_posts_id, 'pafe_form_booking_quantity', $booking_slot );
								update_post_meta( $form_booking_posts_id, 'order_id', $form_database_post_id );
								update_post_meta( $form_booking_posts_id, 'payment_status', $payment_status );
							} else {
								$failed = true;
							}
						}
					}

					// Replace redirect

					$redirect = '';

					if (in_array("redirect", $form['settings']['submit_actions'])) {
						$redirect = replace_email($form['settings']['redirect_to'], $fields);
					}

					// Woocommerce Add to Cart

					if (in_array("woocommerce_add_to_cart", $form['settings']['submit_actions'])) {
						if ( class_exists( 'WooCommerce' ) ) {  
				        	if (!empty($_POST['product_id']) && !empty($form['settings']['woocommerce_add_to_cart_price'])) {
				        		if (strpos($_POST['product_id'], 'field id') !== false) {
				        			$product_id = intval( pafe_get_field_value( str_replace('\"', '"', $_POST['product_id']),$fields ) );
			        			} else {
			        				$product_id = intval( $_POST['product_id'] );
			        			}
				        		
				        		$cart_item_data = array();
				        		$cart_item_data['fields'] = array();

				        		$fields_cart = $fields;

				        		if (!empty($form['settings']['woocommerce_add_to_cart_custom_order_item_meta_enable'])) {
				        			$fields_cart = array();
									foreach ($form['settings']['woocommerce_add_to_cart_custom_order_item_list'] as $item) {
										if (!empty($item['woocommerce_add_to_cart_custom_order_item_field_shortcode'])) {
											foreach ($fields as $key_field=>$field) {
												if (strpos($item['woocommerce_add_to_cart_custom_order_item_field_shortcode'], '[repeater id') !== false) { // fix alert ]
													if ($fields[$key_field]['repeater_id_one'] == get_field_name_shortcode( $item['woocommerce_add_to_cart_custom_order_item_field_shortcode'] )) {
														if (!isset($fields_cart[$fields[$key_field]['repeater_id_one']])) {
															$fields_cart[$fields[$key_field]['repeater_id_one']] = array(
																'label' => $fields[$key_field]['repeater_label'],
																'name' => $fields[$key_field]['repeater_id_one'],
																'value' => str_replace( '\n', '<br>', pafe_get_field_value( '[repeater id="' . $fields[$key_field]['repeater_id_one'] . '"]',$fields,$payment_status, $payment_id) ),
															);
														}
													}
												} else {
													if ($fields[$key_field]['name'] == get_field_name_shortcode( $item['woocommerce_add_to_cart_custom_order_item_field_shortcode'] )) {
														if (empty($item['woocommerce_add_to_cart_custom_order_item_remove_if_field_empty'])) {
															$fields_cart[] = $field;
														} else {
															if (!empty($field['value'])) {
																$fields_cart[] = $field;
															}
														}
													}
												}
											}
										}
									}
								}

								foreach ($fields as $key_field=>$field) {
									if ($fields[$key_field]['name'] == get_field_name_shortcode( $form['settings']['woocommerce_add_to_cart_price'] )) {
										if (isset($fields[$key_field]['calculation_results'])) {
											$cart_item_data['pafe_custom_price'] = $fields[$key_field]['calculation_results'];
										} else {
											$cart_item_data['pafe_custom_price'] = $fields[$key_field]['value'];
										}
									}
								}

			        			foreach ($fields_cart as $key_field=>$field) {
									$field_value = $fields_cart[$key_field]['value'];
									if (isset($fields_cart[$key_field]['value_label'])) {
										$field_value = $fields_cart[$key_field]['value_label'];
									}

									$cart_item_data['fields'][] = array(
										'label' => $fields_cart[$key_field]['label'],
										'name' => $fields_cart[$key_field]['name'],
										'value' => $field_value,
									);
								}

								global $woocommerce;

								$woocommerce->cart->add_to_cart( $product_id, 1, 0, array(), $cart_item_data );

				        	}
				    	}
					}

					// Remote Request

					if (in_array('remote_request', $form['settings']['submit_actions']) && !empty($form['settings']['remote_request_url'])) {

						$wp_args = array();

						if (!empty($form['settings']['remote_request_arguments_list'])) {
							foreach ($form['settings']['remote_request_arguments_list'] as $item) {
								if (!empty($item['remote_request_arguments_parameter']) && !empty($item['remote_request_arguments_value'])) {
									$wp_args[$item['remote_request_arguments_parameter']] = replace_email($item['remote_request_arguments_value'], $fields);
								}
							}
						}

						if (!empty($form['settings']['remote_request_body_list'])) {
							$wp_args['body'] = array();
							foreach ($form['settings']['remote_request_body_list'] as $item) {
								if (!empty($item['remote_request_body_parameter']) && !empty($item['remote_request_body_value'])) {
									$wp_args['body'][$item['remote_request_body_parameter']] = replace_email($item['remote_request_body_value'], $fields);
								}
							}
						}

		    //     		if (!empty($form['settings']['remote_request_header_list'])) {
						// 	$wp_args['headers'] = array();
						// 	foreach ($form['settings']['remote_request_header_list'] as $item) {
						// 		if (!empty($item['remote_request_header_parameter']) && !empty($item['remote_request_header_value'])) {
						// 			$wp_args['headers'][$item['remote_request_header_parameter']] = replace_email($item['remote_request_header_value'], $fields);
						// 		}
						// 	}
						// }

						$res = wp_remote_request(replace_email($form['settings']['remote_request_url'], $fields), $wp_args);

					}

					// Register
					$register_message = '';

					if (in_array("register", $form['settings']['submit_actions'])) {
						if (!empty($form['settings']['register_email']) && !empty($form['settings']['register_username']) && !empty($form['settings']['register_password'])) {
							$register_email = replace_email($form['settings']['register_email'], $fields);
							$register_username = replace_email($form['settings']['register_username'], $fields);
							$register_password = replace_email($form['settings']['register_password'], $fields);
							$register_password_confirm = replace_email($form['settings']['register_password_confirm'], $fields);
							$register_first_name = replace_email($form['settings']['register_first_name'], $fields);
							$register_last_name = replace_email($form['settings']['register_last_name'], $fields);
							$register_message = '';

							if (!empty($register_password_confirm) && $register_password != $register_password_confirm) {
								$register_message = replace_email($form['settings']['register_password_confirm_message'], $fields);
								$failed = true;
							} else {
								if (!empty($register_email) && !empty($register_username) && !empty($register_password)) {
									$register_user = wp_create_user($register_username,$register_password,$register_email);
									if (is_wp_error($register_user)){ // if there was an error creating a new user
										$failed = true;
								        $register_message = $register_user->get_error_message();
								    } else {
								    	wp_update_user( array(
								    		'ID' => $register_user,
								    		'role' => $form['settings']['register_role']
							    		));

							    		if (!empty($form['settings']['register_user_meta_list'])) {
							    			foreach ($form['settings']['register_user_meta_list'] as $user_meta_item) {
							    				if (!empty($user_meta_item['register_user_meta_key']) && !empty($user_meta_item['register_user_meta_field_id'])) {
							    					update_user_meta( $register_user, $user_meta_item['register_user_meta_key'], replace_email($user_meta_item['register_user_meta_field_id'], $fields));
							    				}
							    			}
							    		}

								    	if (!empty($register_first_name) && !empty($register_last_name)) {
									    	wp_update_user( array(
									    		'ID' => $register_user,
									    		'first_name' => $register_first_name,
									    		'last_name' => $register_last_name
								    		)); // Update the user with the first name and last name
									    }

									    /* Automatically log in the user and redirect the user to the home page */
										$register_creds = array( // credientials for newley created user
										    'user_login' => $register_username,
										    'user_password' => $register_password,
										    'remember' => true,
										);

										$register_signon = wp_signon($register_creds); //sign in the new user
								    }
								} else {
									$failed = true;
								}
							}
						} else {
							$failed = true;
						}
					}

					if (in_array("login", $form['settings']['submit_actions'])) {
						if (!empty($form['settings']['login_username']) && !empty($form['settings']['login_username']) && !empty($form['settings']['login_password'])) {
							$login_username = replace_email($form['settings']['login_username'], $fields);
							$login_password = replace_email($form['settings']['login_password'], $fields);
							$login_remember = replace_email($form['settings']['login_remember'], $fields);
							$register_message = '';

							if (!empty($login_username) && !empty($login_password)) {
								$login_creds = array(
								    'user_login' => $login_username,
								    'user_password' => $login_password,
								);

								if (!empty($login_remember)) {
									$login_creds['remember'] = true;
								}

								$login_signon = wp_signon($login_creds);

								if (is_wp_error($login_signon)){
									$failed = true;
							        $register_message = $login_signon->get_error_message();
							    }

							} else {
								$failed = true;
							}
						} else {
							$failed = true;
						}
					}

					if (in_array("update_user_profile", $form['settings']['submit_actions'])) {
						if (is_user_logged_in()) {
							if (!empty($form['settings']['update_user_meta_list'])) {
								$user_id = get_current_user_id();

								foreach ($form['settings']['update_user_meta_list'] as $user_meta) {
									if (!empty($user_meta['update_user_meta']) && !empty($user_meta['update_user_meta_field_shortcode'])) {

										$user_meta_key = $user_meta['update_user_meta'];
										$user_meta_value = '';

										if ($user_meta['update_user_meta'] == 'meta' || $user_meta['update_user_meta'] == 'acf') {
											if (!empty($user_meta['update_user_meta_key'])) {
												$user_meta_key = $user_meta['update_user_meta_key'];
											}
										}

										if ($user_meta_key == 'password') {
											if (!empty($user_meta['update_user_meta_field_shortcode_confirm_password'])) {
												if (pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields) != pafe_get_field_value($user_meta['update_user_meta_field_shortcode_confirm_password'], $fields)) {
													$failed = true;
													$register_message = $user_meta['wrong_password_message'];
												} else {
													$login_password = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);

													if (!empty($login_password)) {
														wp_set_password( $login_password, $user_id );

														$current_user = wp_get_current_user();

														$login_creds = array(
														    'user_login' => $current_user->user_login,
														    'user_password' => $login_password,
														);

														$login_signon = wp_signon($login_creds);
													}
												}
											}
										} else {

											if ( $user_meta['update_user_meta'] == 'acf' ) {
												$meta_type = $user_meta['update_user_meta_type'];

												$custom_field_value = pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields);

												if ($meta_type == 'image') {
													$image_array = explode(',', $custom_field_value);
													$image_id = attachment_url_to_postid( $image_array[0] );
													if (!empty($image_id)) {
														$custom_field_value = $image_id;
													}
												}

												if ($meta_type == 'gallery') {
													$images_array = explode(',', $custom_field_value);
													$images_id = array();
													foreach ($images_array as $images_item) {
														if (!empty($images_item)) {
															$image_id = attachment_url_to_postid( $images_item );
															if (!empty($image_id)) {
																$images_id[] = $image_id;
															}
														}
													}
													if (!empty($images_id)) {
														$custom_field_value = $images_id;
													}
												}

												if ($meta_type == 'select' && strpos($custom_field_value, ',') !== false || $meta_type == 'checkbox') {
													$custom_field_value = explode(',', $custom_field_value);
												}

												if ($meta_type == 'date') {
													$time = strtotime( $custom_field_value );

													if (empty($custom_field_value)) {
														$custom_field_value = '';
													} else {
														$custom_field_value = date('Ymd',$time);
													}
												}

												if ($meta_type == 'time') {
													$time = strtotime( $custom_field_value );
													
													if (empty($custom_field_value)) {
														$custom_field_value = '';
													} else {
														$custom_field_value = date('H:i:s',$time);
													}
												}

												// if ($meta_type == 'google_map') {
												// 	$custom_field_value = array('address' => $custom_field_value_array['value'], 'lat' => $custom_field_value_array['lat'], 'lng' => $custom_field_value_array['lng'], 'zoom' => $custom_field_value_array['zoom']);
												// }

												update_field( $user_meta_key, $custom_field_value, 'user_' . $user_id );
											} else {
												update_user_meta( $user_id, $user_meta_key, pafe_get_field_value($user_meta['update_user_meta_field_shortcode'], $fields) );
											}
										}
									}
								}
							}
						}
					}

					// Action Hook

					do_action('pafe/form_builder/new_record',$fields);

					// Email

					if (in_array("email", $form['settings']['submit_actions']) && $failed == false) {

						$to = replace_email($form['settings']['email_to'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$to = replace_email( $form['settings']['email_to'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$subject = replace_email($form['settings']['email_subject'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$subject = replace_email($form['settings']['email_subject'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$message = replace_email($form['settings']['email_content'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$message = replace_email($form['settings']['email_content'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$reply_to = $form['settings']['email_reply_to'];
						if (empty($reply_to)) {
							$reply_to = $form['settings']['email_from'];
						}
						$reply_to = replace_email($reply_to, $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['email_from'] ) ) {
							$headers[] = 'From: ' . replace_email($form['settings']['email_from_name'], $fields, '', '', '', '', '', $form_database_post_id ) . ' <' . replace_email($form['settings']['email_from'], $fields, '', '', '', '', '', $form_database_post_id ) . '>';
							$headers[] = 'Reply-To: ' . $reply_to;
						}

						if ( ! empty( $form['settings']['email_to_cc'] ) ) {
							$headers[] = 'Cc: ' . replace_email($form['settings']['email_to_cc'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						if ( ! empty( $form['settings']['email_to_bcc'] ) ) {
							$headers[] = 'Bcc: ' . replace_email($form['settings']['email_to_bcc'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						$headers[] = 'Content-Type: text/html; charset=UTF-8';

						if (!empty($post_url)) {
							$subject = str_replace( '[post_url]', $post_url, $subject );
							$message = str_replace( '[post_url]', '<a href="' . $post_url . '">' . $post_url . '</a>', $message );
						}

						$status = wp_mail( $to, $subject, $message . $meta_content, $headers, $attachment );

						// if ( ! empty( $form['settings']['email_to_bcc'] ) ) {
						// 	$bcc_emails = explode( ',', replace_email($form['settings']['email_to_bcc'], $fields, '', '', '', '', '', $form_database_post_id ) );
						// 	foreach ( $bcc_emails as $bcc_email ) {
						// 		wp_mail( trim( $bcc_email ), $subject, $message . $meta_content, $headers, $attachment );
						// 	}
						// }

					}

					if (in_array("email2", $form['settings']['submit_actions']) && $failed == false) {

						// $to = replace_email($form['settings']['email_to_2'], $fields);

						// $subject = replace_email($form['settings']['email_subject_2'], $fields);

						// $message = replace_email($form['settings']['email_content_2'], $fields);

						$to = replace_email($form['settings']['email_to_2'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$to = replace_email( $form['settings']['email_to_2'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$subject = replace_email($form['settings']['email_subject_2'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$subject = replace_email($form['settings']['email_subject_2'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$message = replace_email($form['settings']['email_content_2'], $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['pafe_stripe_status_succeeded'] ) && ! empty( $form['settings']['pafe_stripe_status_pending'] ) && ! empty( $form['settings']['pafe_stripe_status_failed'] ) ) {
							$message = replace_email($form['settings']['email_content_2'], $fields, $payment_status, $payment_id, $form['settings']['pafe_stripe_status_succeeded'], $form['settings']['pafe_stripe_status_pending'], $form['settings']['pafe_stripe_status_failed'], $form_database_post_id );
						}

						$reply_to = $form['settings']['email_reply_to_2'];
						if (empty($reply_to)) {
							$reply_to = $form['settings']['email_from_2'];
						}
						$reply_to = replace_email($reply_to, $fields, '', '', '', '', '', $form_database_post_id );

						if ( ! empty( $form['settings']['email_from_2'] ) ) {
							$headers[] = 'From: ' . replace_email($form['settings']['email_from_name_2'], $fields, '', '', '', '', '', $form_database_post_id ) . ' <' . replace_email($form['settings']['email_from_2'], $fields, '', '', '', '', '', $form_database_post_id ) . '>';
							$headers[] = 'Reply-To: ' . $reply_to;
						}

						if ( ! empty( $form['settings']['email_to_cc_2'] ) ) {
							$headers[] = 'Cc: ' . replace_email($form['settings']['email_to_cc_2'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						if ( ! empty( $form['settings']['email_to_bcc_2'] ) ) {
							$headers[] = 'Bcc: ' . replace_email($form['settings']['email_to_bcc_2'], $fields, '', '', '', '', '', $form_database_post_id );
						}

						$headers[] = 'Content-Type: text/html; charset=UTF-8';

						if (!empty($post_url)) {
							$subject = str_replace( '[post_url]', $post_url, $subject );
							$message = str_replace( '[post_url]', '<a href="' . $post_url . '">' . $post_url . '</a>', $message );
						}

						$status = wp_mail( $to, $subject, $message, $headers, $attachment );

						// if ( ! empty( $form['settings']['email_to_bcc_2'] ) ) {
						// 	$bcc_emails = explode( ',', replace_email($form['settings']['email_to_bcc_2'], $fields, '', '', '', '', '', $form_database_post_id ) );
						// 	foreach ( $bcc_emails as $bcc_email ) {
						// 		wp_mail( trim( $bcc_email ), $subject, $message, $headers, $attachment );
						// 	}
						// }

					}

					foreach ($attachment as $attachment_item) {
						unlink($attachment_item);
					}

					$failed_status = 0;

					if ($failed) {
						$redirect = '';
						$failed_status = 1;
					}

					if ($failed == false && empty($status)) {
						$status = 1;
					}

					$register_message = str_replace(',', '###', $register_message);

					echo $payment_status . ',' . $status . ',' . $payment_id . ',' . $post_url . ',' . $redirect . ',' . $register_message . ',' . $failed_status;
					// echo '<br>';
					// echo $message;

				} // End $recaptcha_check = 1;
			}
		wp_die();
	}
?>