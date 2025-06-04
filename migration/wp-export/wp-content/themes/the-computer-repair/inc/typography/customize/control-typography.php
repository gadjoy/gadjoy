<?php
/**
 * Typography control class.
 *
 * @since  1.0.0
 * @access public
 */

class The_Computer_Repair_Control_Typography extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'typography';

	/**
	 * Array 
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );

		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'color'       => esc_html__( 'Font Color', 'the-computer-repair' ),
				'family'      => esc_html__( 'Font Family', 'the-computer-repair' ),
				'size'        => esc_html__( 'Font Size',   'the-computer-repair' ),
				'weight'      => esc_html__( 'Font Weight', 'the-computer-repair' ),
				'style'       => esc_html__( 'Font Style',  'the-computer-repair' ),
				'line_height' => esc_html__( 'Line Height', 'the-computer-repair' ),
				'letter_spacing' => esc_html__( 'Letter Spacing', 'the-computer-repair' ),
			)
		);
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'the-computer-repair-ctypo-customize-controls' );
		wp_enqueue_style(  'the-computer-repair-ctypo-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {

			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : ''
			);

			if ( 'family' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_families();

			elseif ( 'weight' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();

			elseif ( 'style' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
		}
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && data.family.choices ) { #>

			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select {{{ data.family.link }}}>

					<# _.each( data.family.choices, function( label, choice ) { #>
						<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.weight && data.weight.choices ) { #>

			<li class="typography-font-weight">

				<# if ( data.weight.label ) { #>
					<span class="customize-control-title">{{ data.weight.label }}</span>
				<# } #>

				<select {{{ data.weight.link }}}>

					<# _.each( data.weight.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.style && data.style.choices ) { #>

			<li class="typography-font-style">

				<# if ( data.style.label ) { #>
					<span class="customize-control-title">{{ data.style.label }}</span>
				<# } #>

				<select {{{ data.style.link }}}>

					<# _.each( data.style.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.size ) { #>

			<li class="typography-font-size">

				<# if ( data.size.label ) { #>
					<span class="customize-control-title">{{ data.size.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.size.link }}} value="{{ data.size.value }}" />

			</li>
		<# } #>

		<# if ( data.line_height ) { #>

			<li class="typography-line-height">

				<# if ( data.line_height.label ) { #>
					<span class="customize-control-title">{{ data.line_height.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.line_height.link }}} value="{{ data.line_height.value }}" />

			</li>
		<# } #>

		<# if ( data.letter_spacing ) { #>

			<li class="typography-letter-spacing">

				<# if ( data.letter_spacing.label ) { #>
					<span class="customize-control-title">{{ data.letter_spacing.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.letter_spacing.link }}} value="{{ data.letter_spacing.value }}" />

			</li>
		<# } #>

		</ul>
	<?php }

	/**
	 * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
	 *
	 * @todo Integrate with Google fonts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_fonts() { return array(); }

	/**
	 * Returns the available font families.
	 *
	 * @todo Pull families from `get_fonts()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_font_families() {

		return array(
			'' => __( 'No Fonts', 'the-computer-repair' ),
        'Abril Fatface' => __( 'Abril Fatface', 'the-computer-repair' ),
        'Acme' => __( 'Acme', 'the-computer-repair' ),
        'Anton' => __( 'Anton', 'the-computer-repair' ),
        'Architects Daughter' => __( 'Architects Daughter', 'the-computer-repair' ),
        'Arimo' => __( 'Arimo', 'the-computer-repair' ),
        'Arsenal' => __( 'Arsenal', 'the-computer-repair' ),
        'Arvo' => __( 'Arvo', 'the-computer-repair' ),
        'Alegreya' => __( 'Alegreya', 'the-computer-repair' ),
        'Alfa Slab One' => __( 'Alfa Slab One', 'the-computer-repair' ),
        'Averia Serif Libre' => __( 'Averia Serif Libre', 'the-computer-repair' ),
        'Bangers' => __( 'Bangers', 'the-computer-repair' ),
        'Boogaloo' => __( 'Boogaloo', 'the-computer-repair' ),
        'Bad Script' => __( 'Bad Script', 'the-computer-repair' ),
        'Bitter' => __( 'Bitter', 'the-computer-repair' ),
        'Bree Serif' => __( 'Bree Serif', 'the-computer-repair' ),
        'BenchNine' => __( 'BenchNine', 'the-computer-repair' ),
        'Cabin' => __( 'Cabin', 'the-computer-repair' ),
        'Cardo' => __( 'Cardo', 'the-computer-repair' ),
        'Courgette' => __( 'Courgette', 'the-computer-repair' ),
        'Cherry Swash' => __( 'Cherry Swash', 'the-computer-repair' ),
        'Cormorant Garamond' => __( 'Cormorant Garamond', 'the-computer-repair' ),
        'Crimson Text' => __( 'Crimson Text', 'the-computer-repair' ),
        'Cuprum' => __( 'Cuprum', 'the-computer-repair' ),
        'Cookie' => __( 'Cookie', 'the-computer-repair' ),
        'Chewy' => __( 'Chewy', 'the-computer-repair' ),
        'Days One' => __( 'Days One', 'the-computer-repair' ),
        'Dosis' => __( 'Dosis', 'the-computer-repair' ),
        'Droid Sans' => __( 'Droid Sans', 'the-computer-repair' ),
        'Economica' => __( 'Economica', 'the-computer-repair' ),
        'Fredoka One' => __( 'Fredoka One', 'the-computer-repair' ),
        'Fjalla One' => __( 'Fjalla One', 'the-computer-repair' ),
        'Francois One' => __( 'Francois One', 'the-computer-repair' ),
        'Frank Ruhl Libre' => __( 'Frank Ruhl Libre', 'the-computer-repair' ),
        'Gloria Hallelujah' => __( 'Gloria Hallelujah', 'the-computer-repair' ),
        'Great Vibes' => __( 'Great Vibes', 'the-computer-repair' ),
        'Handlee' => __( 'Handlee', 'the-computer-repair' ),
        'Hammersmith One' => __( 'Hammersmith One', 'the-computer-repair' ),
        'Inconsolata' => __( 'Inconsolata', 'the-computer-repair' ),
        'Indie Flower' => __( 'Indie Flower', 'the-computer-repair' ),
        'IM Fell English SC' => __( 'IM Fell English SC', 'the-computer-repair' ),
        'Julius Sans One' => __( 'Julius Sans One', 'the-computer-repair' ),
        'Josefin Slab' => __( 'Josefin Slab', 'the-computer-repair' ),
        'Josefin Sans' => __( 'Josefin Sans', 'the-computer-repair' ),
        'Kanit' => __( 'Kanit', 'the-computer-repair' ),
        'Lobster' => __( 'Lobster', 'the-computer-repair' ),
        'Lato' => __( 'Lato', 'the-computer-repair' ),
        'Lora' => __( 'Lora', 'the-computer-repair' ),
        'Libre Baskerville' => __( 'Libre Baskerville', 'the-computer-repair' ),
        'Lobster Two' => __( 'Lobster Two', 'the-computer-repair' ),
        'Merriweather' => __( 'Merriweather', 'the-computer-repair' ),
        'Monda' => __( 'Monda', 'the-computer-repair' ),
        'Montserrat' => __( 'Montserrat', 'the-computer-repair' ),
        'Muli' => __( 'Muli', 'the-computer-repair' ),
        'Marck Script' => __( 'Marck Script', 'the-computer-repair' ),
        'Noto Serif' => __( 'Noto Serif', 'the-computer-repair' ),
        'Open Sans' => __( 'Open Sans', 'the-computer-repair' ),
        'Overpass' => __( 'Overpass', 'the-computer-repair' ),
        'Overpass Mono' => __( 'Overpass Mono', 'the-computer-repair' ),
        'Oxygen' => __( 'Oxygen', 'the-computer-repair' ),
        'Orbitron' => __( 'Orbitron', 'the-computer-repair' ),
        'Patua One' => __( 'Patua One', 'the-computer-repair' ),
        'Pacifico' => __( 'Pacifico', 'the-computer-repair' ),
        'Padauk' => __( 'Padauk', 'the-computer-repair' ),
        'Playball' => __( 'Playball', 'the-computer-repair' ),
        'Playfair Display' => __( 'Playfair Display', 'the-computer-repair' ),
        'PT Sans' => __( 'PT Sans', 'the-computer-repair' ),
        'Philosopher' => __( 'Philosopher', 'the-computer-repair' ),
        'Permanent Marker' => __( 'Permanent Marker', 'the-computer-repair' ),
        'Poiret One' => __( 'Poiret One', 'the-computer-repair' ),
        'Quicksand' => __( 'Quicksand', 'the-computer-repair' ),
        'Quattrocento Sans' => __( 'Quattrocento Sans', 'the-computer-repair' ),
        'Raleway' => __( 'Raleway', 'the-computer-repair' ),
        'Rubik' => __( 'Rubik', 'the-computer-repair' ),
        'Rokkitt' => __( 'Rokkitt', 'the-computer-repair' ),
        'Russo One' => __( 'Russo One', 'the-computer-repair' ),
        'Righteous' => __( 'Righteous', 'the-computer-repair' ),
        'Slabo' => __( 'Slabo', 'the-computer-repair' ),
        'Source Sans Pro' => __( 'Source Sans Pro', 'the-computer-repair' ),
        'Shadows Into Light Two' => __( 'Shadows Into Light Two', 'the-computer-repair'),
        'Shadows Into Light' => __( 'Shadows Into Light', 'the-computer-repair' ),
        'Sacramento' => __( 'Sacramento', 'the-computer-repair' ),
        'Shrikhand' => __( 'Shrikhand', 'the-computer-repair' ),
        'Tangerine' => __( 'Tangerine', 'the-computer-repair' ),
        'Ubuntu' => __( 'Ubuntu', 'the-computer-repair' ),
        'VT323' => __( 'VT323', 'the-computer-repair' ),
        'Varela Round' => __( 'Varela Round', 'the-computer-repair' ),
        'Vampiro One' => __( 'Vampiro One', 'the-computer-repair' ),
        'Vollkorn' => __( 'Vollkorn', 'the-computer-repair' ),
        'Volkhov' => __( 'Volkhov', 'the-computer-repair' ),
        'Yanone Kaffeesatz' => __( 'Yanone Kaffeesatz', 'the-computer-repair' )
		);
	}

	/**
	 * Returns the available font weights.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_weight_choices() {

		return array(
			'' => esc_html__( 'No Fonts weight', 'the-computer-repair' ),
			'100' => esc_html__( 'Thin',       'the-computer-repair' ),
			'300' => esc_html__( 'Light',      'the-computer-repair' ),
			'400' => esc_html__( 'Normal',     'the-computer-repair' ),
			'500' => esc_html__( 'Medium',     'the-computer-repair' ),
			'700' => esc_html__( 'Bold',       'the-computer-repair' ),
			'900' => esc_html__( 'Ultra Bold', 'the-computer-repair' ),
		);
	}

	/**
	 * Returns the available font styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_style_choices() {

		return array(
			'' => esc_html__( 'No Fonts Style', 'the-computer-repair' ),
			'normal'  => esc_html__( 'Normal', 'the-computer-repair' ),
			'italic'  => esc_html__( 'Italic', 'the-computer-repair' ),
			'oblique' => esc_html__( 'Oblique', 'the-computer-repair' )
		);
	}
}
