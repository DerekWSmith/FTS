<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://whitetower.com.au/
 * @since      1.0.0
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      First_To_Site_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FIRST_TO_SITE_VERSION' ) ) {
			$this->version = FIRST_TO_SITE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'first-to-site';

		$this->load_dependencies();
		$this->set_locale();
		$this->set_cpt();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_project_hooks();
		$this->define_email_hooks();
		$this->set_cronjob();
		$this->set_notification();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - First_To_Site_Loader. Orchestrates the hooks of the plugin.
	 * - First_To_Site_i18n. Defines internationalization functionality.
	 * - First_To_Site_Admin. Defines all hooks for the admin area.
	 * - First_To_Site_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-i18n.php';

		/**
		 * The class responsible for defining cronjob functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-cronjob.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-first-to-site-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-first-to-site-public.php';

		$this->loader = new First_To_Site_Loader();
		
		/**
		 * The functions responsible for registering all Custom post type that in the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-cpt.php';

		/**
		 * The functions related to Project.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-project.php';

		/**
		 * The functions related to Email.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-email.php';

		/**
		 * The functions related to Notifications.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-first-to-site-notifications.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the First_To_Site_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new First_To_Site_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Define the locale for this plugin for cronjob.
	 *
	 * Uses the First_To_Site_cronjob class in order to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_cronjob() {

		$plugin_cronjob = new First_To_Site_Cronjob();
	
		$args = array(false);

		if(!wp_next_scheduled('load_email_expire_cron',$args)){
			wp_schedule_event(time(), 'hourly', 'load_email_expire_cron', $args);
		}

		$this->loader->add_action( 'load_email_expire_cron', $plugin_cronjob, 'load_email_expire_checker' );
		// $this->loader->add_action( 'init', $plugin_cronjob, 'load_email_expire_checker' );
	}

	private function set_notification() {

		$notification = new First_To_Site_Notification();
	
		$this->loader->add_action( 'wp_insert_post', $notification, 'new_project_create_notification', 10, 3 ); // Send notification when a new project is created
		// $this->loader->add_filter('acf/update_value/name=doc_upload_architectural_drawing', $notification, 'project_document_need_attention_notification', 10, 3);
		// $this->loader->add_filter('acf/update_value/key=field_619ed6e51e56f', $notification, 'project_document_need_attention_notification', 10, 3);
		$this->loader->add_filter('acf/update_value/name=status', $notification, 'project_document_need_attention_notification', 10, 3);

		if(!wp_next_scheduled('daily_notification_cron', array(false))){
			wp_schedule_event(time(), 'daily', 'daily_notification_cron',  array(false));
		}

		$this->loader->add_action( 'daily_notification_cron', $notification, 'daily_notification_loader' );

	}

	/**
	 * Define the custom post type for this plugin.
	 *
	 * Uses the First_To_Site_cpt class in order to set the custom post type and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_cpt() {

		$plugin_cpt = new First_To_Site_cpt();

		/**
		 * Register custom post types
		 */
		$this->loader->add_action( 'init', $plugin_cpt, 'project' );
		$this->loader->add_action( 'init', $plugin_cpt, 'service_level_agreement' );
		$this->loader->add_action( 'init', $plugin_cpt, 'vendor' );
		$this->loader->add_action( 'init', $plugin_cpt, 'email' );
		$this->loader->add_action( 'init', $plugin_cpt, 'email_template' );

		/**
		 * Register custom taxonomy
		 */
		$this->loader->add_action( 'init', $plugin_cpt, 'project_status');
		$this->loader->add_action( 'init', $plugin_cpt, 'document_status');

	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new First_To_Site_Admin( $this->get_plugin_name(), $this->get_version() );
		
		/**
		 * Actions
		 */
		// $this->loader->add_action( 'init', $plugin_admin, 'blockusers_init' ); 

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin, 'fts_login_style' ); // Login page style
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_slug_options'  );
		$this->loader->add_action( 'login_header', $plugin_admin, 'custom_login_header'  );

		$this->loader->add_action( 'login_footer', $plugin_admin, 'login_background'  );
		$this->loader->add_action( 'login_footer', $plugin_admin, 'custom_login_footer'  );
		/**
		 * Filters
		 */
		$this->loader->add_filter( 'login_redirect', $plugin_admin, 'login_redirect', 10, 3 ); // Redirect user after login
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'remove_footer_msg' );
		$this->loader->add_filter( 'login_headerurl', $plugin_admin, 'custom_login_headerurl' );

		$this->loader->add_filter( 'login_headertext', $plugin_admin, 'custom_login_message' );
		$this->loader->add_filter( 'default_hidden_columns', $plugin_admin, 'hide_admin_user_list_columns', 10, 2 ); // Hide admin user table columns

		/**
		 * Hide wordpress dashboard elements (menu, metabox and plugins pages) for certain user role
		 */
		$this->loader->add_filter( 'wp_mail_smtp_admin_adminbarmenu_has_access', $plugin_admin, 'remove_smtp_from_adminbar' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'remove_admin_bar_logo', 999 ); // Remove logo from admin bar
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'add_admin_bar_title', 1000 ); // Remove tiele from admin bar
		$this->loader->add_action( 'admin_head', $plugin_admin, 'hide_notices_from_usr', 1 ); // Block Wordpress notices for non-admin
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'members_remove_menu_pages', 100 ); 
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'hide_wp_mail_smtp_dashboard_widgets', 20 );
		$this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'hide_publish_metabox' );
		$this->loader->add_action( 'do_meta_boxes', $plugin_admin, 'hide_project_status_metabox' );
		$this->loader->add_filter('screen_options_show_screen',$plugin_admin, 'remove_screen_options');

		$this->loader->remove_action( "admin_color_scheme_picker", "admin_color_scheme_picker" ); // Don't allow user change color scheme manually

		/**
		 * Custom sorting methods for custom columns
		 */ 
		$this->loader->add_action( 'pre_get_posts', $plugin_admin, 'custom_post_columns_orderby' );

		/**
		 * Custom project list columns
		 */
		$this->loader->add_filter ( 'manage_project_posts_columns', $plugin_admin, 'add_project_custom_columns' );
		$this->loader->add_action ( 'manage_project_posts_custom_column', $plugin_admin, 'project_custom_column_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-project_sortable_columns', $plugin_admin, 'project_column_register_sortable' );

		/**
		 * Custom email list columns
		 */
		$this->loader->add_filter ( 'manage_email_posts_columns', $plugin_admin, 'add_email_custom_columns' );
		$this->loader->add_action ( 'manage_email_posts_custom_column', $plugin_admin, 'email_custom_column_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-email_sortable_columns', $plugin_admin, 'email_column_register_sortable' );

		/**
		 * Custom Service_level_agreem list columns
		 */
		$this->loader->add_filter ( 'manage_service_level_agreem_posts_columns', $plugin_admin, 'add_service_level_agreem_custom_columns' );
		$this->loader->add_action ( 'manage_service_level_agreem_posts_custom_column', $plugin_admin, 'service_level_agreem_custom_column_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-service_level_agreem_sortable_columns', $plugin_admin, 'service_level_agreem_column_register_sortable' );

		/**
		 * Custom vendor list columns
		 */
		$this->loader->add_filter ( 'manage_vendor_posts_columns', $plugin_admin, 'add_vendor_custom_columns' );
		$this->loader->add_action ( 'manage_vendor_posts_custom_column', $plugin_admin, 'vendor_custom_column_content', 10, 2 );
		$this->loader->add_filter( 'manage_edit-vendor_sortable_columns', $plugin_admin, 'vendor_column_register_sortable' );

		$this->loader->add_action('pre_get_posts', $plugin_admin, 'query_set_only_author' );
		$this->loader->add_filter('views_edit-project', $plugin_admin, 'fix_project_counts');
		$this->loader->add_filter('views_upload', $plugin_admin, 'fix_media_counts');

		$this->loader->add_filter( 'bulk_actions-edit-project', $plugin_admin, 'remove_from_bulk_actions' );
		$this->loader->add_filter( 'bulk_actions-edit-email_template', $plugin_admin, 'remove_from_bulk_actions' );
		$this->loader->add_filter( 'bulk_actions-edit-email', $plugin_admin, 'remove_from_bulk_actions' );
		$this->loader->add_filter( 'bulk_actions-edit-vendor', $plugin_admin, 'remove_from_bulk_actions' );
		$this->loader->add_filter( 'bulk_actions-edit-service_level_agreem', $plugin_admin, 'remove_from_bulk_actions' );

		$this->loader->add_action('acf/input/admin_footer', $plugin_admin, 'my_acf_input_admin_footer');

	}

	
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_project_hooks() {
		$plugin_project = new First_To_Site_Project();

		/**
		 * Actions
		 */
		$this->loader->add_action( 'admin_init', $plugin_project, 'fts_plugin_has_parents', 10 ); // Check Plugins dependency
		// $this->loader->add_action( 'acf/upload_prefilter/name=upload', $plugin_project, 'wpse_set_attachment_category', 10, 1);

		$this->loader->add_action( 'wp_ajax_delete_project_message_callback',$plugin_project, 'delete_project_message_callback');
		$this->loader->add_action( 'wp_ajax_add_project_message_callback',$plugin_project, 'add_project_message_callback');

		/**
		 * Filters
		 */
		$this->loader->add_filter( 'acf/load_field/key=field_619f12d641a7f', $plugin_project, 'acf_load_field_message_for_project_summary', 10);
		$this->loader->add_filter( 'acf/load_field/key=field_619f120ac4ab3', $plugin_project, 'load_project_summary_field');

		$this->loader->add_filter( 'acf/load_field/key=field_61f72d2412a57', $plugin_project, 'acf_load_field_message_for_email_log', 10);
		$this->loader->add_filter( 'acf/load_field/key=field_61f72d1012a56', $plugin_project, 'load_email_log_field');
		$this->loader->add_filter( 'acf/prepare_field/key=field_61f72d1012a56', $plugin_project, 'hide_email_log_field_for_customer' );

		$this->loader->add_filter( 'comments_clauses', $plugin_project, 'exclude_project_messages', 10, 1 );
		$this->loader->add_filter( 'comment_feed_where', $plugin_project, 'exclude_project_messages_from_feed_where' );

		$this->loader->add_filter('acf/load_field/key=field_61dcd1b8134d2', $plugin_project, 'project_message_display',10 ); // Add project message area
	
		// Exclude customer user roles from editing status field
		$this->loader->add_filter( 'acf/prepare_field/name=status', $plugin_project, 'status_edit_exclude');
		
		$this->loader->add_filter( 'acf/update_value/name=upload', $plugin_project, 'update_status_after_file_reupload', 10, 4);
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_email_hooks() {
		$plugin_email = new First_To_Site_Email();

		/**
		 * Actions
		 */
		$this->loader->add_action( 'acf/save_post', $plugin_email, 'email_send_after_post_save', 10 ); // Send the email base on the Email CPT content
	
		/**
		 * Filters
		 */
		$this->loader->add_filter('acf/load_field/key=field_61bc201aa698e', $plugin_email, 'acf_load_field_message_for_email_buttons', 10);
	
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new First_To_Site_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_head', $plugin_public, 'auth_redirect' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    First_To_Site_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
