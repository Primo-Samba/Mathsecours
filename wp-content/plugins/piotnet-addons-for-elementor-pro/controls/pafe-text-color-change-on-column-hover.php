<?php

class PAFE_Text_Color_Change_On_Column_Hover extends \Elementor\Widget_Base {

	public function __construct() {
		parent::__construct();
		$this->init_control();
	}

	public function get_name() {
		return 'pafe-text-color-change-on-column-hover';
	}

	public function register_controls( $element, $args ) {

		$element->start_controls_section(
			'pafe_text_color_change_on_column_hover_section',
			[
				'label' => __( 'PAFE Text Color Change On Column Hover', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			'pafe_text_color_change_on_column_hover',
			[
				'label' => __( 'Text Color Change On Column Hover', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'dynamic' => [
  					'active' => true,
  				],
				'selectors' => [
					'{{WRAPPER}}:hover *' => 'color: {{VALUE}}!important;',
				],
			]
		);

		$element->end_controls_section();
	}

	protected function init_control() {
		add_action( 'elementor/element/section/section_typo/after_section_end', [ $this, 'register_controls' ], 10, 2 );
		add_action( 'elementor/element/column/section_typo/after_section_end', [ $this, 'register_controls' ], 10, 2 );
	}

}