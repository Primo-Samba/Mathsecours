<?php
	add_action( 'wp_ajax_pafe_form_booking', 'pafe_form_booking' );
	add_action( 'wp_ajax_nopriv_pafe_form_booking', 'pafe_form_booking' );

	function find_element_recursive_form_booking( $elements, $form_id ) {
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

	function pafe_form_booking() {
		$post_id = $_POST['post_id'];
		$element_id = $_POST['element_id'];
		$date = $_POST['date'];

		if (!empty($element_id) && !empty($post_id)) {
			$elementor = \Elementor\Plugin::$instance;

			if ( version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
				$meta = $elementor->documents->get( $post_id )->get_elements_data();
			} else {
				$meta = $elementor->db->get_plain_editor( $post_id );
			}

			$form_bookings = find_element_recursive_form_booking( $meta, $element_id );

			$widget = $elementor->elements_manager->create_element_instance( $form_bookings );
			$settings = $widget->get_active_settings();

			require_once( __DIR__ . '/../inc/templates/template-form-booking.php' );

			pafe_template_form_booking($settings, $element_id, $post_id, $date);
		}

		wp_die(); 
	}