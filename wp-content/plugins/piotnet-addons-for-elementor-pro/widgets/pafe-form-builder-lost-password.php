<?php

class PAFE_Form_Builder_Lost_Password extends \Elementor\Widget_Base {

	public function get_name() {
		return 'pafe-form-builder-lost-password';
	}

	public function get_title() {
		return __( 'Lost Password', 'pafe' );
	}

	public function get_icon() {
		return 'eicon-lock-user';
	}

	public function get_categories() {
		return [ 'pafe-form-builder' ];
	}

	public function get_keywords() {
		return [ 'input', 'form', 'field', 'lost', 'password' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'pafe_form_builder_lost_password_section',
			[
				'label' => __( 'Lost Password', 'pafe' ),
			]
		);

		$this->add_control(
			'pafe_form_builder_lost_password_text',
			[
				'label' => __( 'Text', 'elementor-pro' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default' => __( 'Lost your password?', 'pafe' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pafe_form_builder_lost_password_style',
			[
				'label' => __( 'Text', 'pafe' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pafe_form_builder_lost_password_style_color',
			[
				'label' => __( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Scheme_Color::get_type(),
					'value' => \Elementor\Scheme_Color::COLOR_3,
				],
				'selectors' => [
					// Stronger selector to avoid section style from overwriting
					'{{WRAPPER}} .pafe-form-builder-lost-password__url' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'pafe_form_builder_lost_password_style_typography',
				'scheme' => \Elementor\Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .pafe-form-builder-lost-password__url',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'pafe_form_builder_lost_password_style_shadow',
				'selector' => '{{WRAPPER}} .pafe-form-builder-lost-password__url',
			]
		);

		$this->add_control(
			'pafe_form_builder_lost_password_style_blend_mode',
			[
				'label' => __( 'Blend Mode', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Normal', 'elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .pafe-form-builder-lost-password__url' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->add_responsive_control(
			'pafe_form_builder_lost_password_style_align',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( !empty( $settings['pafe_form_builder_lost_password_text'] ) ) {

		?>	
			<div class="pafe-form-builder-lost-password">
				<a class="pafe-form-builder-lost-password__url" href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="<?php echo $settings['pafe_form_builder_lost_password_text']; ?>"><?php echo $settings['pafe_form_builder_lost_password_text']; ?></a>
			</div>
        <?php

		}

	}

	public function add_wpml_support() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$widgets[ $this->get_name() ] = [
			'conditions' => [ 'widgetType' => $this->get_name() ],
			'fields'     => [
				[
					'field'       => 'pafe_form_builder_lost_password_text',
					'type'        => __( 'Lost Password Text', 'pafe' ),
					'editor_type' => 'LINE'
				],
			],
		];

		return $widgets;
	}
}