<?php
/**
 * Advanced Custom Fields: Auto JSON Sync Directory Structure.
 *
 * @since 0.1.0
 * @package ACF_Auto_JSON_Sync
 */

/**
 * Advanced Custom Fields: Auto JSON Sync Directory Structure.
 *
 * @since 0.1.0
 * @author Jason Witt
 */
class ACF_AJS_Directory_Structure {

	/**
	 * The ACF JSON directory path.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @var string
	 */
	protected $acf_json_dir;

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {
		$this->acf_json_dir = apply_filters( 'afc_ajs_json_directory', trailingslashit( get_template_directory() ) . 'acf-json' );
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'acf/settings/save_json',  array( $this, 'set_acf_json_save_directory' ) );
		add_filter( 'acf/settings/load_json',  array( $this, 'set_acf_json_load_directory' ) );
	}

	/**
	 * Instantiate.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function init() {

		// The ACF json directory.
		$this->maybe_create_directories();
		$this->maybe_create_file();
	}

	/**
	 * Get WP Filesystem.
	 *
	 * @author Jason Witt
	 * @since 0.1.0
	 *
	 * @return object $wp_filesystem The WP Filesystem.
	 */
	public function get_wp_filesystem() {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		global $wp_filesystem;

		return $wp_filesystem;
	}

	/**
	 * Set ACF json Save Directory.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @param string $path The Current ACF json directory path.
	 *
	 * @return string $path The path to the new ACF json save directory.
	 */
	public function set_acf_json_save_directory( $path ) {

		// Override the default ACF json save directory path.
		$path = $this->acf_json_dir;

		return $path;
	}

	/**
	 * Set ACF json Load Directory.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @param string $paths The Current ACF json directory path.
	 *
	 * @return string $paths Array of the ACF load directories.
	 */
	public function set_acf_json_load_directory( $paths ) {

		// Remove original path.
		unset( $paths[0] );

		// Add the custom paths.
		$paths[] = $this->acf_json_dir;

		return $paths;
	}

	/**
	 * Create ACF directories.
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @return bool Return if directory exists.
	 */
	private function maybe_create_directories() {

		// Bail early if directory exists.
		if ( file_exists( $this->acf_json_dir ) ) {
			return;
		}

		// Create the directory if it doesn't exist.
		wp_mkdir_p( $this->acf_json_dir );
	}

	/**
	 * Create index.php file
	 *
	 * @since 0.1.0
	 * @author Jason Witt
	 *
	 * @return bool return Return if file exists.
	 */
	private function maybe_create_file() {

		$file          = trailingslashit( $this->acf_json_dir ) . 'index.php';
		$wp_filesystem = $this->get_wp_filesystem();
		$content       = "<?php if ( ! defined( 'WPINC' ) ) { wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) ); }";

		// Bail eraly if file exists.
		if ( file_exists( $file ) ) {
			return;
		}

		// Create the file.
		if ( wp_is_writable( $this->acf_json_dir ) ) {
			$wp_filesystem->put_contents( $file, $content );
		}
	}
}
