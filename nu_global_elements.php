<?php
/* 
Plugin Name: Northeastern Global Elements
Plugin URI: https://github.com/ITS-Digital-Technology/global-elements-wordpress
Description: Inserts the Northeastern University global header, footer, and TrustArc cookie consent manager. Requires wp_body_open() under the body tag to display the global header.
Author: Northeastern University ITS Web Solutions
Author URI: https://its.northeastern.edu
Version: 1.3.0
*/ 

if (!defined('ABSPATH')) { exit; }

const NU_GLOBAL_ELEMENTS_PLUGIN_VER = "1.3.0";
const NU_GLOBAL_ELEMENTS_PLUGIN_MANIFEST_URL = "https://its-digital-technology.github.io/global-elements-wordpress/manifest/info.json";

/** 
 * Get options values for plugin
 */

$nu_global_elements_options = get_option( 'nu_global_elements_option_name' );

/** 
 * Include global elements CSS, kernl UI and javascript from CDN
 */
add_action('wp_head', function() {
	echo '
            <link rel="stylesheet" href="https://global-packages.cdn.northeastern.edu/global-elements/dist/css/index.css">
            <script src="https://global-packages.cdn.northeastern.edu/global-elements/dist/js/index.umd.js"></script>
            <script src="https://global-packages.cdn.northeastern.edu/kernl-ui/dist/js/index.umd.js" defer></script>
            ';
});

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

	if (isset($nu_global_elements_options['disable_global_footer'])){
		add_action('wp_footer', function() {
			echo '<style>#trustarc-global-element footer {padding-top: .6rem !important;} #trustarc-global-element footer a {color: #ccc; text-decoration: none;}</style>';
		});
	}
	add_action('wp_footer', function() {
		echo '<div id="trustarc-global-element" x-data="NUGlobalElements.trustarc()" x-init="init()"></div>';
   	});
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

/** 
 * Check for updates to this plugin and if available allow updating through WP admin plugin manager
 */


if( ! class_exists( 'UpdateChecker' ) ) {

	class UpdateChecker{

		public $plugin_slug;
		public $version;
		public $cache_key;
		public $cache_allowed;

		public function __construct() {

			$this->plugin_slug = plugin_basename( __DIR__ );
			$this->version = NU_GLOBAL_ELEMENTS_PLUGIN_VER;
			$this->cache_key = 'global_elements_updater';
			$this->cache_allowed = false;

			add_filter( 'plugins_api', array( $this, 'info' ), 20, 3 );
			add_filter( 'site_transient_update_plugins', array( $this, 'update' ) );
			add_action( 'upgrader_process_complete', array( $this, 'purge' ), 10, 2 );

		}

		public function request(){

			$remote = get_transient( $this->cache_key );

			if( false === $remote || ! $this->cache_allowed ) {

				$remote = wp_remote_get(
					NU_GLOBAL_ELEMENTS_PLUGIN_MANIFEST_URL,
					array(
						'timeout' => 10,
						'headers' => array(
							'Accept' => 'application/json'
						)
					)
				);

				if(
					is_wp_error( $remote )
					|| 200 !== wp_remote_retrieve_response_code( $remote )
					|| empty( wp_remote_retrieve_body( $remote ) )
				) {
					return false;
				}

				set_transient( $this->cache_key, $remote, DAY_IN_SECONDS );

			}

			$remote = json_decode( wp_remote_retrieve_body( $remote ) );

			return $remote;

		}


		function info( $res, $action, $args ) {

			// do nothing if you're not getting plugin information right now
			if( 'plugin_information' !== $action ) {
				return $res;
			}

			// do nothing if it is not our plugin
			if( $this->plugin_slug !== $args->slug ) {
				return $res;
			}

			// get updates
			$remote = $this->request();

			if( ! $remote ) {
				return $res;
			}

			$res = new stdClass();

			$res->name = $remote->name;
			$res->slug = $remote->slug;
			$res->version = $remote->version;
			$res->tested = $remote->tested;
			$res->requires = $remote->requires;
			$res->author = $remote->author;
			$res->author_profile = $remote->author_profile;
			$res->download_link = $remote->download_url;
			$res->trunk = $remote->download_url;
			$res->requires_php = $remote->requires_php;
			$res->last_updated = $remote->last_updated;

			$res->sections = array(
				'description' => $remote->sections->description,
				'installation' => $remote->sections->installation,
				'changelog' => $remote->sections->changelog
			);

			if( ! empty( $remote->banners ) ) {
				$res->banners = array(
					'low' => $remote->banners->low,
					'high' => $remote->banners->high
				);
			}

			return $res;

		}

		public function update( $transient ) {

			if ( empty($transient->checked ) ) {
				return $transient;
			}

			$remote = $this->request();

			if(
				$remote
				&& version_compare( $this->version, $remote->version, '<' )
				&& version_compare( $remote->requires, get_bloginfo( 'version' ), '<=' )
				&& version_compare( $remote->requires_php, PHP_VERSION, '<' )
			) {
				$res = new stdClass();
				$res->slug = $this->plugin_slug;
				$res->plugin = plugin_basename( __FILE__ ); 
				$res->new_version = $remote->version;
				$res->tested = $remote->tested;
				$res->package = $remote->download_url;

				$transient->response[ $res->plugin ] = $res;

	    }

			return $transient;

		}

		public function purge( $upgrader, $options ){

			if (
				$this->cache_allowed
				&& 'update' === $options['action']
				&& 'plugin' === $options[ 'type' ]
			) {
				// just clean the cache when new plugin version is installed
				delete_transient( $this->cache_key );
			}

		}


	}

	new UpdateChecker();

}
