<?php
/**
 * AgenWebsite Page Settings Tools
 *
 * @author AgenWebsite
 * @version 0.1.0
 */

if( !class_exists('wp_aw_admin') ):

/**
 * class for manage agenwebsite's plugins for woocommerce
 */
class wp_aw_admin{

	/**
	 * Class version
	 *
	 * @var string
	 */
	 public $version = '0.0.1';

    /**
     * Parameters for add_submenu_page
     *
     *  add_submenu_page(
     *      'woocommerce',		// The parent slug for submenu in menu woocommerce
     *      'Page Title',	// The text to be displayed in the title tags of the page when the menu is selected
     *      'Menu Title',	// The text to be used for the menu
     *      'capability',	// The capability (or role) required for this menu to be displayed to the user. Administraot|manage_options|etc
     *      'menu-slug',	// The slug name to refer to this menu by (should be unique for this menu).
     *      'plugin_options_display_page' // The function to be called to output the content for this page.
     *  );
     *
     */
    protected $_submenu = array();
		
    /**
     * Initial Options definition:
     *   'tab' => array(
     *      'label',
     *      'sections' => array(
     *          'fields' => array(
     *             'option1',
     *             'option2',
     *              ...
     *          )
     *      )
     *   )
     *
     * @var array
     */
    public $options = array();		

    /**
     * Options group name
     *
     * @var string
     */
    public $option_group = 'tool_group';

    /**
     * Option name
     *
     * @var string
     */
    public $option_name = 'tool_options';

    /**
     * Additional Links
     *
     * @var array
     */
    public $additional_links = array();

    /**
     * After Submit
     *
     * @var string
     */
    public $plugin_url;

    /**
     * Constructor
     *
     * @param array $submenu   Parameters for add_submenu_page
     * @param array $options   Array of plugin options
     *
     */
	 public function __construct( $submenu, $options, $option_group = false, $option_name = false, $plugin_url, $localize, $additional_links ){
         $this->_submenu = apply_filters( 'aw_tool_submenu', $submenu );
         $this->options = apply_filters( 'aw_tool_options', $options );

         if( $option_group ){
             $this->option_group = $option_group; 
         }

         if( $option_name ){
             $this->option_name = $option_name; 
         }

         $this->additional_links = $additional_links;
         $this->plugin_url = $plugin_url;
         $this->localize = $localize;

         // Add menu item
         add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

         // Register settings
         add_action( 'admin_init', array( $this, 'register_settings' ) );

         // Include JS and CSS files
         add_action( 'admin_enqueue_scripts', array( $this, 'tool_assets') );

     }

    /**
     * Create new submenu page
     *
     * @return void
     * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
     */
    public function add_submenu_page() {
        $submenu = $this->_submenu;
        add_submenu_page(
            $submenu[0],
            $submenu[1],
            $submenu[2],
            $submenu[3],
            $submenu[4],
            array( $this, isset($submenu[5]) ? $submenu[5] : 'plugin_options_display_page' )
        );
    }

    /**
     * Generate and retrieve HTML for the admin logo branding.
     *
     * @return HTML for the admin logo branding.
     */
    public function get_admin_logo(){
        $html = '<span class="logo alignright">' . "\n";
        $html .= '<a href="' . esc_url( 'http://agenwebsite.com/' ) . '" target="_blank"><img id="logo" src="' . esc_url( $this->plugin_url . '/assets/images/logo.png' ) . '" /></a>' . "\n";
        $html .= '</span>' . "\n";
        echo $html;
    }

    /**
     * Generate and retrieve HTML for the usefull links.
     *
     * @return HTML for the usefull links.
     */
	public function get_usefull_links(){
        if( is_array( $this->additional_links ) ):
            $html = '<ul class="useful-links">' . "\n";
            foreach( $this->additional_links as $link => $data ){
                $html .= '<li class="' . $link . '"><a href="' . esc_url( $data['url'] ) . '" target="_blank">' . __( $data['label'], $this->localize ) . '</a></li>' . "\n";
            }
            $html .= '</ul>' . "\n";
            echo $html;
        endif;
    }

    /**
     * Generate admin page
     *
     * @return void
     */
    public function plugin_options_display_page(){
        $page = $this->_get_tab();

        $html = '<div class="wrap" id="aw_admin">' . "\n";
            $html .= '<div id="icon-themes" class="icon32"></div>' . "\n";

            ob_start();
            do_action('agenwebsite_tools_before_tabs');
            $html .= ob_get_clean();

            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

				foreach($this->options as $k => $tab){
                    $class = ( $page == $k ) ? ' nav-tab-active' : '';
                    $html .= '<a class="nav-tab' . $class . '" href="' . add_query_arg( 'tool_page', $k ) . '">' . __( $tab['label'], $this->localize ) .  '</a>' . "\n";
                }

                ob_start();
				$this->get_admin_logo();
                $html .= ob_get_clean();

            $html .= '</h2>' . "\n";

            ob_start();
			$this->get_usefull_links();
			$html .= ob_get_clean();

			$html .= '<form action="options.php" method="post">' . "\n";

				// Get settings fields
				ob_start();
                settings_errors();
				do_settings_sections( $this->option_name );
				settings_fields( $this->option_group );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input type="hidden" name="tool_page" value="' . $page . '" />' . "\n";
					$html .= '<input class="button-primary" type="submit" name="save_options" value="' . __( 'Simpan', $this->localize ) . '" />' . "\n";

				$html .= '</p>' . "\n";
	
            $html .= '</form>' . "\n";

        $html .= '</div>' . "\n";

        echo $html;

    }

    /**
     * Get the active tab. If the page isn't provided, the function
     * will return the first tab name
     *
     * @return string
     */
	protected function _get_tab(){
        if( isset($_POST['tool_page']) && $_POST['tool_page'] != '' ) {
            return $_POST['tool_page'];
        } elseif( isset($_GET['tool_page']) && $_GET['tool_page'] != '' ) {
            return $_GET['tool_page'];
        } else {
            $tabs = array_keys( $this->options );
            return $tabs[0];
        }
    }

	/**
	 * Load settings JS & CSS
	 *
	 * @return void
	 */
	public function tool_assets(){
        global $pagenow;

        if( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == $this->_submenu[4] ) {

            wp_enqueue_script( 'aw-admin-js', $this->plugin_url . '/assets/js/aw-admin.js', array( 'jquery' ), $this->version, true );

            wp_enqueue_style( 'aw-admin', $this->plugin_url . '/assets/css/aw-admin.css' );

            do_action( 'aw_panel_enqueue' );

        }

    }
		
    /**
     * Register a new settings option group
     *
     * @return void
     * @link http://codex.wordpress.org/Function_Reference/register_setting
     * @link http://codex.wordpress.org/Function_Reference/add_settings_section
     * @link http://codex.wordpress.org/Function_Reference/add_settings_field
     */
	public function register_settings(){
        $page = $this->_get_tab();
        $tab = isset( $this->options[$page] ) ? $this->options[$page] : array();

        if( !empty( $tab['sections'] ) ){
            // Add section and field
            foreach( $tab['sections'] as $section_name => $section ){
                // Add the section
                add_settings_section( 
					$section_name, 
					__( $section['title'], $this->localize ), 
					array( $this, 'display_section_content' ), 
					$this->option_name 
				);

				// Add the fields
				foreach( $section['fields'] as $option_name => $option ){
					$option['id'] = $option_name;
					$option['label_for'] = $option_name;

                    // Register setting group
					register_setting(
						$this->option_group,
						$option_name,
						array( $this, 'tool_sanitize' )
					);

                    add_settings_field(
                        $option_name,
						__( $option['title'], $this->localize ),
						array( $this, 'tool_field_content' ),
						$this->option_name,
						$section_name,
						$option
                    );
                }
            }
        }
    }

    /**
     * Display sections content
     *
     * @return void
     */	
	public function display_section_content( $section ){
        $page = $this->_get_tab();
        if( isset( $this->options[$page]['sections'][$section['id']]['description'] ) ){
            echo '<p class="section_description">' . __( $this->options[$page]['sections'][$section['id']]['description'], $this->localize ) . '</p>';
        }
    }

    /**
     * Sanitize the option's value
     *
     * @param array $input
     * @return array
     */
	public function tool_sanitize( $input ){
		return apply_filters('aw_tool_sanitize', $input);
	}

    /**
     * Generate HTML for displaying field
     *
     * @return void
     */		
	public function tool_field_content( $field ){

        $html = '';

        if( ! isset( $field['type'] ) || ( $field['type'] == '' ) ){
            $field['type'] = 'text'; // Default to "text" field type
        }
        
        if( is_array($field['type']) ){
            if( is_object($field['type'][0]) ){
                $obj = new $field['type'][0];
                $method = $field['type'][1];
                $html .= $obj->{$method}($field);
            }
        } else {
            if( method_exists( $this, 'generate_' . $field['type'] . '_html') ){
                $html .= $this->{'generate_' . $field['type'] . '_html'}( $field );
            } else{
                $html .= $this->{'generate_text_html'}( $field );
            }
        }

        echo $html;

    }

    /**
	 * Generate Text Input HTML.
	 *
	 * @param  mixed $data
	 * @return string
	 */    
    public function generate_text_html($data){

        $defaults = array(
            'title'         => '',
            'disabled'      => false,
            'class'         => '',
            'css'           => '',
            'placeholder'   => '',
            'type'          => 'text',
            'description'   => '',
            'std'           => '',
            'id'            => '',
            'label_for'     => '',
            'custom_attributes' => array()
        );

        $data = wp_parse_args($data, $defaults);
        $value = get_option( $data['id'], $data['std'] );

        ob_start();
        ?>
        <input type="text" class="regular-text <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" id="<?php echo $data['id'];?>" name="<?php echo $data['id'];?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
        <?php echo $this->get_description_html( $data ); ?>

        <?php
        return ob_get_clean();
    }

	/**
	 * Generate Textarea HTML.
	 *
	 * @param  mixed $data
	 * @return string
	 */    
    public function generate_textarea_html($data){

        $defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'description'       => '',
            'std'               => '',
            'id'                => '',
            'label_for'         => '',
			'custom_attributes' => array()
		);

        $data = wp_parse_args($data, $defaults);
        $value = get_option( $data['id'], $data['std'] );

        ob_start();
        ?>
        <textarea rows="3" cols="20" class="regular-text <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $data['id'] ); ?>" id="<?php echo esc_attr( $data['id'] ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo esc_textarea( $value ); ?></textarea>
        <?php echo $this->get_description_html( $data ); ?>
        <?php
        return ob_get_clean();
    }

	/**
	 * Generate Checkbox HTML.
	 *
	 * @param  mixed $data
	 * @return string
	 */    
    public function generate_checkbox_html($data){

        $defaults = array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'description'       => '',
            'std'               => '',
            'id'                => '',
            'label_for'         => '',
			'custom_attributes' => array()
		);

        $data = wp_parse_args( $data, $defaults );
        $value = get_option( $data['id'], $data['std'] );

        if ( ! $data['label'] || $data['label'] == '' ) {
			$data['label'] = $data['title'];
		}

        ob_start();
        ?>
        <label for="<?php echo esc_attr( $data['label_for'] ); ?>">
        <input type="<?php echo $data['type'];?>" id="<?php echo $data['id'];?>" name="<?php echo $data['id'];?>" class="<?php echo esc_attr( $data['class'] );?>" style="<?php echo esc_attr( $data['css'] );?>" value="1" <?php checked( $value, true, true)?> <?php echo $this->get_custom_attribute_html($data);?> <?php disabled( $data['disabled'], true ); ?> /> <?php echo wp_kses_post( $data['label'] ); ?></label><br/>
        <?php echo $this->get_description_html( $data );?>

        <?php
        return ob_get_clean();
    }

	/**
	 * Generate Radio HTML.
	 *
	 * @param  mixed $data
	 * @return string
	 */    
    public function generate_radio_html($data){

        $defaults = array(
			'title'             => '',
			'label'             => '',
			'class'             => '',
			'css'               => '',
			'type'              => 'text',
			'description'       => '',
            'std'               => '',
            'id'                => '',
            'label_for'         => '',
            'options'           => array(),
			'custom_attributes' => array()
		);

        $data = wp_parse_args( $data, $defaults );
        $value = get_option( $data['id'], $data['std'] );

        ob_start();
        ?>
        <fieldset>
        <?php if( !empty($data['options']) ):?>
            <?php foreach($data['options'] as $key => $label):?>
                <input type="radio" name="<?php echo $data['id'];?>" id="<?php echo $key;?>" class="<?php echo $data['class'];?>" style="<?php echo $data['css'];?>" value="<?php echo $key;?>" <?php echo $this->get_custom_attribute_html($data);?> <?php checked($value, $key, true);?> />
                <label for="<?php echo $key;?>"><?php echo $label;?></label><br>
            <?php endforeach;?>
        <?php endif;?>
        </fieldset>
        <?php
        return ob_get_clean();
    }

	/**
	 * Get custom attributes
	 *
	 * @param  array $data
	 * @return string
	 */
    public function get_custom_attribute_html( $data ){

		$custom_attributes = array();

		if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {

			foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		return implode( ' ', $custom_attributes );

    }

	/**
	 * Get HTML for descriptions
	 *
	 * @param  array $data
	 * @return string
	 */
    public function get_description_html($data){
		return '<p class="description">' . wp_kses_post( $data['description'] ) . '</p>' . "\n";
    }
    
}
endif;