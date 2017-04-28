<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Widget_Cherry_Team extends Widget_Base {

	public function get_name() {
		return 'cherry-team';
	}

	public function get_title() {
		return esc_html__( 'Cherry Team', 'wapu-core' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( 'cherry' );
	}

	public function is_reload_preview_required() {
		return true;
	}

	protected function _get_mapped_control( $name = null ) {

		$mapped_controls = array(
			'media'    => Controls_Manager::MEDIA,
			'text'     => Controls_Manager::TEXT,
			'textarea' => Controls_Manager::TEXTAREA,
			'select'   => Controls_Manager::SELECT,
			'switcher' => Controls_Manager::SWITCHER,
			'slider'   => Controls_Manager::SLIDER,
		);

		if ( isset( $mapped_controls[ $name ] ) ) {
			return $mapped_controls[ $name ];
		} else {
			return false;
		}

	}

	protected function _register_controls() {

		$args = apply_filters( 'cherry_team_members_elementor_get_shortcode_args', array() );

		if ( ! $args ) {
			return;
		}

		$this->start_controls_section(
			'section_team',
			array(
				'label' => esc_html__( 'Cherry Team', 'cherry-team' ),
			)
		);

		foreach ( $args as $name => $arg ) {

			$type = $this->_get_mapped_control( $arg['type'] );

			if ( ! $type ) {
				continue;
			}

			if ( 'id' === $name ) {
				$name = 'post_id';
			}

			$mapped_args = array(
				'label'   => $arg['title'],
				'type'    => $type,
				'default' => array( $arg['value'] ),
			);

			if ( 'switcher' === $arg['type'] ) {
				$mapped_args['default'] = ( 'true' === $arg['value'] ) ? 'yes' : '';
			}

			if ( isset( $arg['options'] ) ) {
				$mapped_args['options'] = $arg['options'];
			}

			if ( isset( $arg['options_cb'] ) ) {
				$mapped_args['options'] = call_user_func( $arg['options_cb'] );
			}

			if ( isset( $arg['min_value'] ) && isset( $arg['max_value'] ) ) {
				$mapped_args['default'] = array(
					'size' => $arg['value'],
				);
				$mapped_args['range'] = array(
					'px' => array(
						'min' => $arg['min_value'],
						'max' => $arg['max_value'],
					),
				);
			}

			$this->add_control( $name, $mapped_args );
		}

		$this->end_controls_section();

	}

	protected function render() {

		$settings       = $this->get_settings();
		$shortcode      = '[cherry_team%s]';
		$shortcode_atts = '';
		$args           = apply_filters( 'cherry_team_members_elementor_get_shortcode_args', array() );

		foreach ( $args as $name => $arg ) {

			if ( ! is_array( $settings[ $name ] ) ) {
				$val = $settings[ $name ];
			} else {
				if ( isset( $settings[ $name ]['size'] ) ) {
					$val = $settings[ $name ]['size'];
				} else {
					$val = $settings[ $name ][0];
				}
			}

			$shortcode_atts .= sprintf( ' %1$s="%2$s"', $name, $val );
		}

		?>
		<div class="elementor-cherry-team"><?php
			echo do_shortcode( sprintf( $shortcode, $shortcode_atts ) );
		?></div>
		<?php
	}

	protected function _content_template() {}

}
