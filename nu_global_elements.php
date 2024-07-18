<?php
/* 
Plugin Name: Northeastern Global Elements
Plugin URI: https://northeastern.netlify.app/pattern-library/page-chrome/global-elements/
Description: Inserts the Northeastern University global header, footer, and TrustArc cookie consent manager. Requires wp_body_open() under the body tag to display the global header.
Author: Northeastern University ITS Web Solutions
Author URI: https://its.northeastern.edu
Version: 1.2.1
*/ 

/** 
 * Get options values for plugin
 */

$nu_global_elements_options = get_option( 'nu_global_elements_option_name' );

/** 
 * Include global elements CSS, kernl UI and javascript from CDN
 */
echo '
            <link rel="stylesheet" href="https://global-packages.cdn.northeastern.edu/global-elements/dist/css/index.css">
            <script src="https://global-packages.cdn.northeastern.edu/global-elements/dist/js/index.umd.js"></script>
            <script src="https://global-packages.cdn.northeastern.edu/kernl-ui/dist/js/index.umd.js" defer></script>
            ';

/** 
 * Include the global NU header, if it is not disabled in the options
 * 
 * NOTE: There must be a wp_body_open() statement under the <body> tag, 
 * most likely in header.php of the theme. 
 */
if (!isset($nu_global_elements_options['disable_global_header'])){
        add_action('wp_body_open', function() {

        echo '<div
                x-data="NUGlobalElements.header({
                    wordmark: true
                })"
                x-init="init()"
                style="height: 48px; background-color: black"
            ></div>';

    }, 10);
}

/** 
 * Include the global NU footer, if it is not disabled in the options
 */
if (!isset($nu_global_elements_options['disable_global_footer'])){
    add_action('wp_footer', function() {

        echo '<div x-data="NUGlobalElements.footer()" x-init="init()"></div>';

    });
}

/** 
 * Include TrustArc, if it is not disabled in the options
 */

 if (!isset($nu_global_elements_options['disable_trustarc'])){
	if (!isset($nu_global_elements_options['disable_global_footer'])){
    	add_action('wp_footer', function() {
        	echo '<div x-data="NUGlobalElements.trustarc()" x-init="init()"></div>';
   		});
	}
	else {
		add_action('wp_footer', function() {
			echo '<style>#trustarc-no-ge-footer footer {padding-top: .7rem !important; text-align: center !important;}</style>';
        	echo '<div id="trustarc-no-ge-footer" x-data="NUGlobalElements.trustarc()" x-init="init()"></div>';
   		});
	}
}


/**
 * Create plugin settings/options menu item, page and fields
 * 
 * Retrieve options values with:
 * $nu_global_elements_options = get_option( 'nu_global_elements_option_name' ); // Array of All Options
 * $disable_global_header = $nu_global_elements_options['disable_global_header']; // Disable Global Header
 * $disable_global_footer = $nu_global_elements_options['disable_global_footer']; // Disable Global Footer
 * $disable_trustarc = $nu_global_elements_options['disable_trustarc']; // Disable TrustArc
 */

 class NUGlobalElements {
	private $nu_global_elements_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'nu_global_elements_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'nu_global_elements_page_init' ) );
	}

	public function nu_global_elements_add_plugin_page() {
		add_menu_page(
			'NU Global Elements', // page_title
			'NU Global Elements', // menu_title
			'manage_options', // capability
			'nu-global-elements', // menu_slug
			array( $this, 'nu_global_elements_create_admin_page' ), // function
			'dashicons-admin-generic', // icon_url
			99 // position
		);
	}

	public function nu_global_elements_create_admin_page() {
		$this->nu_global_elements_options = get_option( 'nu_global_elements_option_name' ); ?>

		<div class="wrap">
			<h2>NU Global Elements</h2>
			<p>If you already display these elements via a method other than this plugin, you may use these options to avoid displaying them twice.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'nu_global_elements_option_group' );
					do_settings_sections( 'nu-global-elements-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function nu_global_elements_page_init() {
		register_setting(
			'nu_global_elements_option_group', // option_group
			'nu_global_elements_option_name', // option_name
			array( $this, 'nu_global_elements_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'nu_global_elements_setting_section', // id
			'Settings', // title
			array( $this, 'nu_global_elements_section_info' ), // callback
			'nu-global-elements-admin' // page
		);

		add_settings_field(
			'disable_global_header', // id
			'Disable Global Header', // title
			array( $this, 'disable_global_header_callback' ), // callback
			'nu-global-elements-admin', // page
			'nu_global_elements_setting_section' // section
		);

		add_settings_field(
			'disable_global_footer', // id
			'Disable Global Footer', // title
			array( $this, 'disable_global_footer_callback' ), // callback
			'nu-global-elements-admin', // page
			'nu_global_elements_setting_section' // section
		);

		add_settings_field(
			'disable_trustarc', // id
			'Disable TrustArc Cookie Consent Manager', // title
			array( $this, 'disable_trustarc_callback' ), // callback
			'nu-global-elements-admin', // page
			'nu_global_elements_setting_section' // section
		);
	}

	public function nu_global_elements_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['disable_global_header'] ) ) {
			$sanitary_values['disable_global_header'] = $input['disable_global_header'];
		}

		if ( isset( $input['disable_global_footer'] ) ) {
			$sanitary_values['disable_global_footer'] = $input['disable_global_footer'];
		}

		if ( isset( $input['disable_trustarc'] ) ) {
			$sanitary_values['disable_trustarc'] = $input['disable_trustarc'];
		}

		return $sanitary_values;
	}

	public function nu_global_elements_section_info() {
		
	}

	public function disable_global_header_callback() {
		printf(
			'<input type="checkbox" name="nu_global_elements_option_name[disable_global_header]" id="disable_global_header" value="disable_global_header" %s>',
			( isset( $this->nu_global_elements_options['disable_global_header'] ) && $this->nu_global_elements_options['disable_global_header'] === 'disable_global_header' ) ? 'checked' : ''
		);
	}

	public function disable_global_footer_callback() {
		printf(
			'<input type="checkbox" name="nu_global_elements_option_name[disable_global_footer]" id="disable_global_footer" value="disable_global_footer" %s>',
			( isset( $this->nu_global_elements_options['disable_global_footer'] ) && $this->nu_global_elements_options['disable_global_footer'] === 'disable_global_footer' ) ? 'checked' : ''
		);
	}

	public function disable_trustarc_callback() {
		printf(
			'<input type="checkbox" name="nu_global_elements_option_name[disable_trustarc]" id="disable_trustarc" value="disable_trustarc" %s>',
			( isset( $this->nu_global_elements_options['disable_trustarc'] ) && $this->nu_global_elements_options['disable_trustarc'] === 'disable_trustarc' ) ? 'checked' : ''
		);
	}

}
if ( is_admin() )
	$nu_global_elements = new NUGlobalElements();


add_filter( 'plugin_action_links_global-elements-wordpress/nu_global_elements.php', 'nu_global_elements_link' );
function nu_global_elements_link( $links ) {
    // Build and escape the URL.
    $url = esc_url( add_query_arg(
        'page',
        'nu-global-elements',
        get_admin_url() . 'admin.php'
    ) );
    // Create the link.
    $settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
    // Adds the link to the end of the array.
    array_push(
        $links,
        $settings_link
    );
    return $links;
}
