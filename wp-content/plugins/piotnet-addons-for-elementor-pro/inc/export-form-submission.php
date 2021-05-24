<?php
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename=pafe-form-database.csv");
    header("Content-Transfer-Encoding: binary");

	if ( defined( 'ABSPATH' ) ) {
		include_once(ABSPATH . "wp-load.php");
	} else {
		include '../../../../wp-load.php';
	}

	$args = array(
		'post_type' => 'pafe-form-database',
		'posts_per_page' => -1,
	);

	if (!empty($_GET['post_status'])) {
		if ($_GET['post_status'] != 'all') {
			$args['post_status'] = esc_sql($_GET['post_status']);
		}
	}

	if (!empty($_GET['post_type'])) {
		$args['post_type'] = esc_sql($_GET['post_type']);
	}

	if (!empty($_GET['m'])) {
		$args['m'] = esc_sql($_GET['m']);
	}

	if (!empty($_GET['form_id'])) {
		$args['meta_key'] = 'form_id';
		$args['meta_value'] = str_replace('+', ' ', esc_sql($_GET['form_id']));
	}

	$query = new WP_Query($args);
	$field_id = array();
	$fields = array();
	$th = array();

	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
		$metas = get_post_meta(get_the_ID());

		foreach($metas as $key=>$value) {
		    if (!in_array($key,$field_id)) {
		    	$field_id[] = $key;
		    }
		}

	endwhile; endif;

	foreach($field_id as $id) {
		if ($id != '_elementor_controls_usage' && $id != '_edit_lock' && $id != 'form_id_elementor' && $id != 'post_id') {
			$th[] = $id;
		}
	}

	$fields[] = $th;

	if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();
		$tr = array();
		foreach($field_id as $id) {
			if ($id != '_elementor_controls_usage' && $id != '_edit_lock' && $id != 'form_id_elementor' && $id != 'post_id') {
				$meta_value = get_post_meta(get_the_ID(),$id,true);
				$tr[] = $meta_value;
			}
		}
		$fields[] = $tr;
	endwhile; endif;

	function export_form_submission($fields) {
		$output = fopen('php://output', 'w');

		fwrite($output, "\xEF\xBB\xBF");

		foreach ($fields as $line) {
			fputcsv($output, $line);
		}
		
		return ob_get_clean();
	}

	echo export_form_submission($fields);
?>