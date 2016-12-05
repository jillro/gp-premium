<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

/**
 * A class to create a dropdown for all google fonts
 */
if ( ! class_exists( 'Generate_Google_Font_Dropdown_Custom_Control' ) ) :
class Generate_Google_Font_Dropdown_Custom_Control extends WP_Customize_Control
{
    public $type = 'gp-customizer-fonts';
	
	public function enqueue() {
		wp_enqueue_script( 'generatepress-customizer-fonts', plugin_dir_url( __FILE__ )  . '/js/fonts-customizer.js', array( 'customize-controls' ), GENERATE_FONT_VERSION, true );
		wp_localize_script( 'generatepress-customizer-fonts', 'gp_customize', array( 'nonce' => wp_create_nonce( 'gp_customize_nonce' ) ) );
	}
	
	public function to_json() {
		parent::to_json();
		
		$number_of_fonts = apply_filters( 'generate_number_of_fonts', 200 );
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'default_fonts_title'] = __( 'Default fonts', 'generate-typography' );
		$this->json[ 'google_fonts_title'] = __( 'Google fonts', 'generate-typography' );
		$this->json[ 'description' ] = __( 'Font family','generate-typography' );
		$this->json[ 'google_fonts' ] = apply_filters( 'generate_typography_customize_list', generate_get_all_google_fonts( $number_of_fonts ) );
		$this->json[ 'default_fonts' ] = generate_typography_default_fonts();
	}
	
	public function content_template() {
		?>
		<label>
			<span class="customize-control-title">{{ data.label }}</span>
			<select {{{ data.link }}}>
				<optgroup label="{{ data.default_fonts_title }}">
					<# for ( var key in data.default_fonts ) { #>
						<# var name = data.default_fonts[ key ].split(',')[0]; #>
						<option value="{{ data.default_fonts[ key ] }}"  <# if ( data.default_fonts[ key ] === data.value ) { #>selected="selected"<# } #>>{{ name }}</option>
					<# } #>
				</optgroup>
				<optgroup label="{{ data.google_fonts_title }}">
					<# for ( var key in data.google_fonts ) { #>
						<option value="{{ data.google_fonts[ key ].name }}"  <# if ( data.google_fonts[ key ].name === data.value ) { #>selected="selected"<# } #>>{{ data.google_fonts[ key ].name }}</option>
					<# } #>
				</optgroup>
			</select>
			<p class="description">{{ data.description }}</p>
		</label>
		<?php
	}
}
endif;

if ( ! class_exists( 'Generate_Select_Control' ) ) :
/**
 * A class to create a dropdown for font weight
 */
class Generate_Select_Control extends WP_Customize_Control
{	
	public $type = 'gp-typography-select';
	public $choices = array();
	
	public function enqueue() {
		wp_enqueue_script( 'generatepress-customizer-fonts', plugin_dir_url( __FILE__ )  . '/js/fonts-customizer.js', array( 'customize-controls' ), GENERATE_FONT_VERSION, true );
	}
	
	public function to_json() {
		parent::to_json();
	
		foreach ( $this->choices as $name => $choice ) {
			$this->choices[ $name ] = $choice;
		}

		$this->json[ 'choices' ] = $this->choices;
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		
	}
	
	public function content_template() {
		?>
		<# if ( ! data.choices )
			return;
		#>

		<label>
			<select {{{ data.link }}}>
				<# jQuery.each( data.choices, function( label, choice ) { #>
					<option value="{{ choice }}" <# if ( choice === data.value ) { #> selected="selected"<# } #>>{{ choice }}</option>
				<# } ) #>
			</select>
			<# if ( data.label ) { #>
				<p class="description">{{ data.label }}</p>
			<# } #>
		</label>
		<?php
	}
}
endif;

if ( !class_exists('Generate_Customize_Slider_Control') ) :
/**
 *	Create our container width slider control
 */
class Generate_Customize_Slider_Control extends WP_Customize_Control
{
	// Setup control type
	public $type = 'gp-typography-slider';
	public $id = '';
	public $default_value = '';
	public $unit = '';
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'id' ] = $this->id;
		$this->json[ 'default_value' ] = $this->default_value;
		$this->json[ 'reset_title' ] = esc_attr__( 'Reset','generate-typography' );
		$this->json[ 'unit' ] = $this->unit;
	}
	
	public function content_template() {
		?>
		<label>
			<p class="description">
				<span class="typography-size-label">
					{{ data.label }}
				</span> 
				<span class="value">
					<input <# if ( '' == data.unit ) { #>style="display:none;"<# } #> name="{{ data.id }}" type="number" {{{ data.link }}} value="{{{ data.value }}}" class="slider-input" /><span <# if ( '' == data.unit ) { #>style="display:none;"<# } #> class="px">{{ data.unit }}</span>
				</span>
			</p>
		</label>
		<div class="slider <# if ( '' !== data.default_value ) { #>show-reset<# } #>"></div>
		<# if ( '' !== data.default_value ) { #><span style="cursor:pointer;" title="{{ data.reset_title }}" class="gp-slider-default-value" data-default-value="{{ data.default_value }}"><span class="gp-customizer-icon-undo" aria-hidden="true"></span><span class="screen-reader-text">{{ data.reset_title }}</span></span><# } #>
		<?php
	}
	
	// Function to enqueue the right jquery scripts and styles
	public function enqueue() {
		
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		
		wp_deregister_script( 'generate-customcontrol-slider-js' );
		wp_register_script( 'generate-customcontrol-slider-js', plugin_dir_url( __FILE__ ) . 'js/customcontrol.slider.js', array( 'jquery-ui-slider' ), GENERATE_FONT_VERSION );
		wp_enqueue_script( 'generate-customcontrol-slider-js' );
		
		wp_enqueue_style('jquery-ui-slider', get_template_directory_uri() . '/inc/css/jquery-ui.structure.css');
		wp_enqueue_style('jquery-ui-slider-theme', get_template_directory_uri() . '/inc/css/jquery-ui.theme.css');
		
	}
}
endif;

if ( ! class_exists( 'Generate_Hidden_Input_Control' ) ) :
/**
 *	Create our hidden input control
 */
class Generate_Hidden_Input_Control extends WP_Customize_Control
{
	// Setup control type
	public $type = 'gp-hidden-input';
	public $id = '';
	
	public function to_json() {
		parent::to_json();
		$this->json[ 'link' ] = $this->get_link();
		$this->json[ 'value' ] = $this->value();
		$this->json[ 'id' ] = $this->id;
	}
	
	public function content_template() {
		?>
		<input name="{{ data.id }}" type="text" {{{ data.link }}} value="{{{ data.value }}}" class="gp-hidden-input" />
		<?php
	}
}
endif;

if ( ! class_exists( 'Generate_Text_Transform_Custom_Control' ) ) :
/**
 * A class to create a dropdown for text-transform
 */
class Generate_Text_Transform_Custom_Control extends WP_Customize_Control
{

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        parent::__construct( $manager, $id, $args );
    }

    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content()
    {
        ?>
        <label>
			<select <?php $this->link(); ?>>
				<?php 
				printf('<option value="%s" %s>%s</option>', 'none', selected($this->value(), 'none', false), 'none');
				printf('<option value="%s" %s>%s</option>', 'capitalize', selected($this->value(), 'capitalize', false), 'capitalize');
				printf('<option value="%s" %s>%s</option>', 'uppercase', selected($this->value(), 'uppercase', false), 'uppercase');
				printf('<option value="%s" %s>%s</option>', 'lowercase', selected($this->value(), 'lowercase', false), 'lowercase');
				?>
            </select>
			<p class="description"><?php echo esc_html( $this->label ); ?></p>
        </label>
        <?php
    }
}
endif;

if ( ! class_exists( 'Generate_Font_Weight_Custom_Control' ) ) :
/**
 * A class to create a dropdown for font weight
 */
class Generate_Font_Weight_Custom_Control extends WP_Customize_Control
{

    public function __construct($manager, $id, $args = array(), $options = array())
    {
        parent::__construct( $manager, $id, $args );
    }

    /**
     * Render the content of the category dropdown
     *
     * @return HTML
     */
    public function render_content()
    {
        ?>
        <label>
			<select <?php $this->link(); ?>>
				<?php 
				printf('<option value="%s" %s>%s</option>', 'normal', selected($this->value(), 'normal', false), 'normal');
				printf('<option value="%s" %s>%s</option>', 'bold', selected($this->value(), 'bold', false), 'bold');
				printf('<option value="%s" %s>%s</option>', '100', selected($this->value(), '100', false), '100');
				printf('<option value="%s" %s>%s</option>', '200', selected($this->value(), '200', false), '200');
				printf('<option value="%s" %s>%s</option>', '300', selected($this->value(), '300', false), '300');
				printf('<option value="%s" %s>%s</option>', '400', selected($this->value(), '400', false), '400');
				printf('<option value="%s" %s>%s</option>', '500', selected($this->value(), '500', false), '500');
				printf('<option value="%s" %s>%s</option>', '600', selected($this->value(), '600', false), '600');
				printf('<option value="%s" %s>%s</option>', '700', selected($this->value(), '700', false), '700');
				printf('<option value="%s" %s>%s</option>', '800', selected($this->value(), '800', false), '800');
				printf('<option value="%s" %s>%s</option>', '900', selected($this->value(), '900', false), '900');	
				?>
            </select>
			<p class="description"><?php echo esc_html( $this->label ); ?></p>
        </label>
        <?php
    }
}
endif;