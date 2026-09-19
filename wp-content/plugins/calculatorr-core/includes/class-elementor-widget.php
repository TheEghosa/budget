<?php
/**
 * One widget that can be any calculator, chosen from a dropdown.
 *
 * A separate widget class per calculator would put a hundred entries in the
 * Elementor panel, which would make the panel harder to use with every
 * calculator we add rather than easier.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Calculatorr_Elementor_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'calculatorr';
	}

	public function get_title() {
		return 'Calculator';
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( 'general' );
	}

	public function get_keywords() {
		return array( 'calculator', 'calculatorr', 'tool' );
	}

	protected function register_controls() {
		$options = array();

		foreach ( Calculatorr_Registry::instance()->all() as $slug => $config ) {
			$options[ $slug ] = $config['title'];
		}

		$this->start_controls_section(
			'content',
			array( 'label' => 'Calculator' )
		);

		$this->add_control(
			'slug',
			array(
				'label'   => 'Which calculator',
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => key( $options ),
			)
		);

		$this->add_control(
			'mode',
			array(
				'label'       => 'How much to show',
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => array(
					'widget' => 'Just the calculator',
					'page'   => 'Calculator plus explainer, FAQ and related links',
				),
				'default'     => 'widget',
				'description' => 'The full page version is what the generated pages use. Pick the calculator on its own when you are placing it inside a layout you have built yourself.',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$config   = Calculatorr_Registry::instance()->get( $settings['slug'] );

		if ( ! $config ) {
			return;
		}

		/* The renderer loads the runtime and its configuration itself, which
		   is what the widget used to do by hand and got half right: it
		   enqueued the scripts and never passed the configuration, so a
		   formula that threw inside a widget was never reported. */
		$renderer = Calculatorr_Renderer::instance();

		echo ( 'page' === $settings['mode'] )
			? $renderer->render_page( $config )
			: $renderer->render_widget( $config );
	}
}
