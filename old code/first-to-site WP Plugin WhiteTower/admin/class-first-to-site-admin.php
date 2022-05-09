<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://whitetower.com.au/
 * @since      1.0.0
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/admin
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in First_To_Site_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The First_To_Site_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name.'-font', plugin_dir_url( __DIR__ ) . 'assets/fonts/Montserrat/stylesheet.css', array(), $this->version, 'all' );

		$dashboard_dependency = array(
			$this->plugin_name.'-font',
			'tailwind',
			'acf-global',
			'acf-input'
		);

		if( is_user_logged_in() && !current_user_can( 'administrator' ) ) {
			wp_enqueue_style( 'tailwind', plugin_dir_url( __FILE__ ) . 'css/tailwind.min.css', '', $this->version, 'all');
			wp_enqueue_style( 'tailwind-element', plugin_dir_url( __FILE__ ) . 'css/tailwind.element.min.css', 'tailwind', $this->version, 'all');

			wp_enqueue_style( $this->plugin_name.'-dashboard', plugin_dir_url( __FILE__ ) . 'css/dashboard.css', $dashboard_dependency , $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name.'-dashboard--project', plugin_dir_url( __FILE__ ) . 'css/dashboard--project.css', $dashboard_dependency , $this->version, 'all' );

		}

		if( is_user_logged_in() && current_user_can( 'customer' ) ) {
			wp_enqueue_style( $this->plugin_name.'-dashboard--customer', plugin_dir_url( __FILE__ ) . 'css/dashboard--customer.css', array($this->plugin_name.'-dashboard'), $this->version, 'all' );
		}
		else if( is_user_logged_in() && current_user_can( 'project_admin' ) ) {
			wp_enqueue_style( $this->plugin_name.'-dashboard--project-admin', plugin_dir_url( __FILE__ ) . 'css/dashboard--project-admin.css', array($this->plugin_name.'-dashboard'), $this->version, 'all' );
		}

		

		//First to site
		// wp_admin_css_color( 'first-to-site', __( 'First to site' ),	plugin_dir_url( __FILE__ ) . 'css/first-to-site.css',	array( '#ffffff', '#000000', '#d54e21' , '#4b90cf')	);
		// wp_admin_css_color( 'first-to-site-dashboard--admin', __( 'First to site dashboard admin' ), $theme_dir . '/first-to-site-dashboard--admin.css',  array( '#3a3a3a', '#fff', '#d54e21' , '#4b90cf') );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in First_To_Site_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The First_To_Site_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/first-to-site-admin.js', array( 'jquery' ), $this->version, false );
		
		wp_enqueue_script( 'tailwind-js', plugin_dir_url( __FILE__ ) . 'js/tailwind.index.min.js', array( 'jquery' ), $this->version, false );
		

		global $pagenow;
		if (
			is_admin() && $pagenow=='post.php'
		) {
			global $post;
			wp_enqueue_script('jquery-blockui', plugin_dir_url( __FILE__ ) . 'js/jquery-blockui/jquery.blockUI.min.js', array('jquery'), true, true);
			wp_enqueue_script( 'first-to-site-project', plugin_dir_url( __FILE__ ) . 'js/first-to-site-project.js', array( 'jquery', 'jquery-blockui' ), $this->version, false );
			wp_localize_script('first-to-site-project', 'ajax', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'add_project_message_nonce' => wp_create_nonce('add-project-message'),
				'delete_project_message_nonce' => wp_create_nonce('delete-project-message'),
				'post_id' => $post->ID,
				'delete_note' => 'Are you sure you wish to delete this note? This action cannot be undone.',
			]);
		}
	}
	
	function blockusers_init() { if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) { wp_redirect( home_url().'/wp-admin/edit.php?post_type=project' ); exit; } } 

	/**
	 * Redirect user after login
	 */
	public function login_redirect( $url, $request, $user ) {
		if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
			$url = admin_url();
		}
		return $url;
	}
	
	/**
	 * Style for login page
	 *
	 * @return void
	 */
	public function fts_login_style() {
		wp_enqueue_style( $this->plugin_name.'-font', plugin_dir_url( __DIR__ ) . 'assets/fonts/Montserrat/stylesheet.css', array(), $this->version, 'all' );

		 wp_enqueue_style( 'first-to-site-login', plugin_dir_url( __FILE__ ) . '/css/login.css' ,array('login'));
	}

	/**
	 * Remove WP logo and comments from the Toolbar.
	 */
	function remove_admin_bar_logo( $wp_admin_bar ) {
		// Remove wp-logo and comments from the menu bar.

		if( is_user_logged_in() && !current_user_can( 'administrator' ) ) {

			$wp_admin_bar->remove_node( 'wp-logo' );  
			$wp_admin_bar->remove_node( 'site-name' );  

			// $wp_admin_bar->remove_node( 'comments' );  
			// $wp_admin_bar->remove_node( 'updates' );  
			// $wp_admin_bar->remove_node( 'abc-main-menu' );  
			// $wp_admin_bar->remove_node( 'wp-mail-smtp-menu' );  
			// $wp_admin_bar->remove_node( 'new-content' );  
			// $wp_admin_bar->remove_node( 'debug-bar' );  
			$nodes = $wp_admin_bar->get_nodes();
			foreach( $nodes as $node )
			{
				if( $node->id == 'menu-toggle')
					continue;

				// 'top-secondary' is used for the User Actions right side menu
				if( 
					'top-secondary' != $node->parent && 
					'my-account' != $node->parent && 
					'user-actions' != $node->parent &&
					'top-secondary' != $node->id
				)
				{
					$wp_admin_bar->remove_menu( $node->id );
				}
			}
		}
	}
	
	function remove_smtp_from_adminbar ()  {
		return "__return_false";
	}

	/**
	 * Hide Metaboxes For All Post Types
	 */
	function hide_publish_metabox() {
		$post_types = get_post_types( '', 'names' );

		if( is_user_logged_in() && !current_user_can( 'administrator' ) ) {
			if( ! empty( $post_types ) ) {
				foreach( $post_types as $type ) {
					remove_meta_box( 'submitdiv', $type, 'side' );
				}
			}
		}
	}

	/**
	 * Hide project status For customer
	 */
	function hide_project_status_metabox() {
		if( is_user_logged_in() && current_user_can( 'customer' ) ) {
			
			remove_meta_box( 'tagsdiv-project_status', 'project', 'side' );
			remove_meta_box( 'commentsdiv', 'project', 'normal' );

		}
	}

    function remove_from_bulk_actions( $actions ){
		if( is_user_logged_in() && !current_user_can( 'administrator' ) ) {
			
			return array();
		}
		return $actions;
    }

	/**
	 * Hide screen options
	 */
	function remove_screen_options() { 
		if(!current_user_can('administrator')) {
		return false;
		}
		return true; 
	}
	/**
	 * Add Website title to admin bar
	 */
	function add_admin_bar_title( $wp_admin_bar ) {
		
		// Add FTS site tiele
		$wp_admin_bar->add_menu( array(
			'id'    => 'hero-title',
			'parent' => null,
			'group'  => null,
			'title' => 'FTS', //you can use img tag with image link. it will show the image icon Instead of the title.
			'href'  => admin_url(),
			'meta' => [
				'title' => __( 'FTS', 'textdomain' ), //This title will show on hover
			]
		) );

		// Add project menu item
		$wp_admin_bar->add_menu( array(
			'id'    => 'add-project',
			'parent' => null,
			'group'  => null,
			'title' => '<span class="ab-icon"></span> <span class="ab-label">Add A Project</span>', //you can use img tag with image link. it will show the image icon Instead of the title.
			'href'  => admin_url('post-new.php?post_type=project'),
			'meta' => [
				'title' => __( 'Create new project', 'textdomain' ), //This title will show on hover
			]
		) );
	}

	function members_remove_menu_pages() {
		$user = wp_get_current_user();
		if (!($user->roles[0] == 'administrator')) {
			remove_menu_page( 'import.php');
			remove_menu_page( 'tools.php');
			remove_menu_page( 'options-general.php' );
			remove_menu_page( 'edit.php' );
			remove_menu_page( 'edit.php?post_type=elementor_library' );
			remove_menu_page('edit.php?post_type=page');
			remove_menu_page('edit-comments.php');

			//Hide "WP Mail SMTP".
			remove_menu_page('wp-mail-smtp');
			//Hide "WP Mail SMTP → Settings".
			remove_submenu_page('wp-mail-smtp', 'wp-mail-smtp');
			//Hide "WP Mail SMTP → Email Log".
			remove_submenu_page('wp-mail-smtp', 'wp-mail-smtp-logs');
			//Hide "WP Mail SMTP → Email Reports".
			remove_submenu_page('wp-mail-smtp', 'wp-mail-smtp-reports');
			//Hide "WP Mail SMTP → Tools".
			remove_submenu_page('wp-mail-smtp', 'wp-mail-smtp-tools');
			//Hide "WP Mail SMTP → About Us".
			remove_submenu_page('wp-mail-smtp', 'wp-mail-smtp-about');

			remove_menu_page('aiowpsec');
			remove_menu_page('members');
			remove_menu_page('smush');
			remove_menu_page('Wordfence');
			remove_menu_page('WFLS');

			remove_menu_page('edit.php?post_type=acf-field-group');
		}
	}

	function hide_wp_mail_smtp_dashboard_widgets() {
		$screen = get_current_screen();
		if ( !$screen ) {
			return;
		}
	
		//Remove the "WP Mail SMTP" widget.
		remove_meta_box('wp_mail_smtp_reports_widget_lite', 'dashboard', 'normal');
	}
	/**
	 * Remove footers greeting message
	 *
	 * @return void
	 */
	function remove_footer_msg () {
		return '';
	}

	/**
	 * Hide wordpress notices for non-administrator user
	 *
	 * @return void
	 */
	function hide_notices_from_usr(){
		$user = wp_get_current_user();
		if (!($user->roles[0] == 'administrator')) {
			remove_all_actions( 'admin_notices' );
		}
	}

	// Hide slug edit field
	function hide_slug_options() {
		global $post, $pagenow;
		$hide_slugs = "<style type=\"text/css\">#slugdiv, #edit-slug-box, [for=\"slugdiv-hide\"] { display: none; }</style>\n";
		if (
			is_admin() && 
			!current_user_can( 'administrator') && 
			( $pagenow=='post-new.php' OR $pagenow=='post.php' )
		) 
		{
			print($hide_slugs);
		}
	}
	function custom_login_headerurl() {
		
		return '';
	}

	function custom_login_message() {
		
		if(!empty($_REQUEST['action']) && isset($_REQUEST['action'])) {
			if($_REQUEST['action'] == 'register')
				$msg = 'Register';
			elseif($_REQUEST['action'] == 'lostpassword')
				$msg = 'Forgot Password'; 
		}
		else 
			$msg = 'Sign in';

		return $msg;
	}


	function login_background() {

		if(!empty($_REQUEST['action']) && isset($_REQUEST['action'])) {
			if($_REQUEST['action'] == 'register')
				$bg_url = plugin_dir_url(__DIR__).'assets/images/login-bg-2.jpg';
			elseif($_REQUEST['action'] == 'lostpassword')
				$bg_url = plugin_dir_url(__DIR__).'assets/images/login-bg-3.png'; 
		}
		else 
			$bg_url = plugin_dir_url(__DIR__).'assets/images/login-bg-1.png';

		?>
		<div class="login-background" style="background-image:url(<?php echo $bg_url; ?>);"></div>
		<?php
	}

	function custom_login_header() {
		?>
		<div class="row">
			<nav class="header">
				<div class="site-logo">FTS</div>
			</nav>
		</div>
		<div class="row main">
		<?php
	}

	function custom_login_footer() {
		?>
			</div>
		<?php
	}

	function hide_admin_user_list_columns( $hidden, $screen ) {
		if (!current_user_can( 'administrator') ) {

			if( isset( $screen->id ) && 'users' == $screen->id){
				$hidden[] = 'wfls_2fa_status';
				$hidden[] = 'wfls_last_login';
			}   
		}
		return $hidden;
	}
	
	function custom_post_columns_orderby( $query ) {
		global $pagenow;

		if ( is_admin() && 'edit.php' == $pagenow && 'project' == $_GET['post_type'] ) {

			$orderby = $query->get('orderby');
		
			if ($orderby != '') {
				if ( 'owner_name' == $orderby ) {
					$query->set('meta_key','owner_information_owner_first_name');
					$query->set('meta_type','text');
					$query->set('orderby','meta_value');
				}
				elseif ( 'address' == $orderby ) {
					$query->set('meta_key','location_street_name');
					$query->set('meta_type','text');
					$query->set('orderby','meta_value');
				}
			}

		}
		elseif ( is_admin() && 'edit.php' == $pagenow && 'email' == $_GET['post_type'] ) {

			$orderby = $query->get('orderby');
		
			if ($orderby != '') {
				if ( 'recipient' == $orderby ) {
					$query->set('meta_key','recipient');
					$query->set('meta_type','text');
					$query->set('orderby','meta_value');
				}
				elseif ( 'status' == $orderby ) {
					$query->set('meta_key','status');
					$query->set('meta_type','text');
					$query->set('orderby','meta_value');
				}
				elseif ( 'message' == $orderby ) {
					$query->set('meta_key','template');
					$query->set('meta_type','text');
					$query->set('orderby','meta_value');
				}
			}

		}
		elseif ( is_admin() && 'edit.php' == $pagenow && 'service_level_agreem' == $_GET['post_type'] ) {

			$orderby = $query->get('orderby');
		
			if ($orderby != '') {
				if ( 'timeframe' == $orderby ) {
					$query->set('meta_key','timeframe');
					$query->set('meta_type','number');
					$query->set('orderby','meta_value');
				}
			}

		}
		else {
			return;
		}
	
	}

	function add_project_custom_columns ( $columns ) {
		$user = wp_get_current_user(); // getting & setting the current user 
		$roles = ( array ) $user->roles; // obtaining the role 
		$custom_array = array(
			'author' => __ ( 'Project Author' ),
			'assigned_admin' => __ ( 'Assigned Admin' ),
			'address' => __ ( 'Address' ),
			'owner_name' => __ ( 'Owner' ),
			'status' => __ ( 'Status' ),
			'last_edited' => __ ( 'Last Edited' ),
		);


		if(in_array('customer', $roles)) {
			unset($columns['taxonomy-project_status']);
			unset($columns['date']);

			unset($custom_array['owner-name']);
			unset($custom_array['author']);
		}
		elseif(in_array('project_admin', $roles)) {
			unset($columns['taxonomy-project_status']);
			unset($columns['date']);
		}
		
		return array_merge ( $columns, $custom_array );
	}

	function project_custom_column_content ( $column, $post_id ) {
		switch ( $column ) {
			case 'assigned_admin':
				$admin = get_field('project_manger', $post_id); // return User array
				
				if( $admin )
					echo get_the_author_meta('display_name', $admin['ID']);
				else 
					echo 'unassigned';
			break;
			case 'author':
				$author_id = get_post_field( 'post_author', $post_id );
				echo get_the_author_meta('display_name', $author_id);
			break;
			case 'owner_name': 
				$first_name = get_field('owner_information_owner_first_name', $post_id);
				$last_name = get_field('owner_information_owner_last_name', $post_id);
				$name = $first_name.' '.$last_name;
				echo $name;
			break;
			case 'street_name':
				$street_name = get_field('location_street_name', $post_id);
				echo $street_name;
			break;
			case 'suburb':
				$suburb = get_field('location_suburb', $post_id);
				echo $suburb;
			break;
			case 'address':
				$address = '';
				if ($lot_no = get_field('location_lot_no', $post_id)) {
					$address .= $lot_no . ', ';
				}
				if ($street_no = get_field('location_street_no', $post_id)) {
					$address .= $street_no . ' ';
				}
				if ($street_name =  get_field('location_street_name', $post_id)) {
					$address .=  $street_name .' ';
				}
				if ($street_type = get_field('location_street_type', $post_id)) {
					$address .= '(' . $street_type . '), ';
					
				}
				if ($suburb = get_field('location_suburb', $post_id)) {
					$address .= $suburb . ' ';
				}
				if ($state = get_field('location_state', $post_id)) {
					$address .= $state . ' ';
				}
				if ($postcode = get_field('location_postcode', $post_id)) {
					$address .= $postcode . ' ';
				}
				echo $address;
			break;
			case 'status':
				$term_obj_list = get_the_terms( $post_id, 'project_status' );
				if(!empty($term_obj_list)) {
					if (count($term_obj_list)>0){
						$status_class = join(' ', wp_list_pluck($term_obj_list, 'slug'));
						$status = join(', ', wp_list_pluck($term_obj_list, 'name'));
						$status_html = '<span class="'. $status_class .'">'. $status .'</span>';
						echo $status_html;
					}
				}
			break;
			case 'last_edited':
				printf( __( '%s', 'first-to-site' ), get_the_modified_date( 'd/m/y' ));
			break;
		}
	}

	function project_column_register_sortable( $columns ) {
		$columns['address'] = 'address';
		$columns['owner_name'] = 'owner_name';
		$columns['status'] = 'status';
		$columns['last_edited'] = 'last_edited';
		return $columns;
	}
	
	function add_email_custom_columns ( $columns ) {
		$user = wp_get_current_user(); // getting & setting the current user 
		$roles = ( array ) $user->roles; // obtaining the role 
		$custom_array = array(
			'recipient' => __ ( 'Recipient' ),
			'message' => __ ( 'Message' ),
			'sla' => __ ( 'SLA' ),
			'status' => __ ( 'Status' ),
			'last_edited' => __ ( 'Last Edited' ),
		);


		if(!in_array('administrator', $roles)) {
			unset($columns['date']);
		}
		
		return array_merge ( $columns, $custom_array );
	}

	function email_custom_column_content ( $column, $post_id ) {
		switch ( $column ) {
			case 'recipient': 
				$cc = get_field('cc', $post_id);
				echo $cc;
			break;
			case 'message':
				$template = get_field('template', $post_id);
				echo $template->post_content;
			break;
			case 'sla':
				$sla = get_field('sla', $post_id);
				$sla = get_post($sla);
				echo $sla->post_title;
			break;
			case 'status':
				$status = get_field('sla-status', $post_id);
				switch( $status ){
					case 'late':
						echo 'Late';
					break;
					case 'on-time':
						echo 'On-Time';
					break;
				}
			break;
			case 'last_edited':
				printf( __( '%s', 'first-to-site' ), get_the_modified_date( 'd/m/y' ));
			break;
		}
	}

	function email_column_register_sortable( $columns ) {
		$columns['recipient'] = 'recipient';
		$columns['sla'] = 'sla';
		$columns['status'] = 'status';
		$columns['last_edited'] = 'last_edited';
		return $columns;
	}

	function add_service_level_agreem_custom_columns ( $columns ) {
		$user = wp_get_current_user(); // getting & setting the current user 
		$roles = ( array ) $user->roles; // obtaining the role 
		$custom_array = array(
			'timeframe' => __ ( 'Timeframe' ),
			'vendor' => __ ( 'Vendor' ),
			'status' => __ ( 'Status' ),
			'last_edited' => __ ( 'Last Edited' ),
		);


		if(!in_array('administrator', $roles)) {
			unset($columns['date']);
		}
		
		return array_merge ( $columns, $custom_array );
	}

	function service_level_agreem_custom_column_content ( $column, $post_id ) {
		switch ( $column ) {
			case 'timeframe': 
				$timeframe = get_field('sla_time_frame', $post_id);
				echo $timeframe. ' day';
				if(intval($timeframe)>1) { echo 's'; }
			break;
			case 'vendor':
				$vendor = get_field('sla_vendor', $post_id);
				echo $vendor->post_title;
			break;
			case 'status':
				$status = get_field('sla_status', $post_id);
				echo $status;
			break;
			case 'last_edited':
				printf( __( '%s', 'first-to-site' ), get_the_modified_date( 'd/m/y' ));
			break;
		}
	}

	function service_level_agreem_column_register_sortable( $columns ) {
		$columns['timeframe'] = 'timeframe';
		$columns['vendor'] = 'vendor';
		$columns['status'] = 'status';
		$columns['last_edited'] = 'last_edited';
		return $columns;
	}

	function add_vendor_custom_columns ( $columns ) {
		$user = wp_get_current_user(); // getting & setting the current user 
		$roles = ( array ) $user->roles; // obtaining the role 
		$custom_array = array(
			'address' => __ ( 'Address' ),
			'status' => __ ( 'Status' ),
			'sla' => __ ( 'SLA' ),
			'mobile' => __ ( 'Mobile' ),
			'email' => __ ( 'email' ),
			'last_edited' => __ ( 'Last Edited' ),
		);


		if(!in_array('administrator', $roles)) {
			unset($columns['date']);
		}
		
		return array_merge ( $columns, $custom_array );
	}

	function vendor_custom_column_content ( $column, $post_id ) {
		switch ( $column ) {
			case 'address': 
				$address = get_field('vendor_address', $post_id);
				echo $address;
			break;
			case 'mobile':
				$mobile = get_field('vendor_mobile', $post_id);
				echo $mobile;
			break;
			case 'email':
				$email = get_field('vendor_email', $post_id);
				echo $email;
			break;
			case 'sla':
				$sla = get_field('sla', $post_id);
				$sla = get_post($sla);
				echo $sla->post_title;
			break;
			case 'status':
				$status = get_field('vendor_status', $post_id);
				echo $status;
			break;
			case 'last_edited':
				printf( __( '%s', 'first-to-site' ), get_the_modified_date( 'd/m/y' ));
			break;
		}
	}

	function vendor_column_register_sortable( $columns ) {
		$columns['address'] = 'address';
		$columns['mobile'] = 'mobile';
		$columns['status'] = 'status';
		$columns['sla'] = 'sla';

		$columns['last_edited'] = 'last_edited';
		return $columns;
	}

	/**
	 * User only allow to view it's own project
	 *
	 * @param [type] $wp_query
	 * @return void
	 */
	function query_set_only_author( $wp_query ) {
		global $current_user, $pagenow;
		$roles = ( array ) $current_user->roles; // obtaining the role 
		
		// 
		if( 
			is_admin() && 
			in_array('customer', $roles) && 
			( 'post.php' != $pagenow && 'post-new.php' != $pagenow)
		) {
			$wp_query->set( 'author', $current_user->ID );
		}
	}

	function fix_project_counts($views) {
		global $current_user;
		$roles = ( array ) $current_user->roles; // obtaining the role 
		
		if( is_admin() && in_array('customer', $roles)) {

			global $wp_query;
			unset($views['mine']);
			$types = array(
			array( 'status' =>  NULL ),
			array( 'status' => 'publish' ),
			array( 'status' => 'draft' ),
			array( 'status' => 'pending' ),
			array( 'status' => 'trash' )
			);
		
			foreach( $types as $type ) {
			$query = array(
				'author'   => $current_user->ID,
				'post_type'   => 'project',
				'post_status' => $type['status']
			);
			
			$result = new WP_Query($query);
		
			if( $type['status'] == NULL ):
			
				$class = ($wp_query->query_vars['post_status'] == NULL) ? ' class="current"' : '';
				$views['all'] = sprintf(__('<a href="%s"'. $class .'>All <span class="count">(%d)</span></a>', 'all'),
				admin_url('edit.php?post_type=project'),
				$result->found_posts);
		
			elseif( $type['status'] == 'publish' ):
		
				$class = ($wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';
				$views['publish'] = sprintf(__('<a href="%s"'. $class .'>Published <span class="count">(%d)</span></a>', 'publish'),
				admin_url('edit.php?post_status=publish&post_type=project'),
				$result->found_posts);
		
			elseif( $type['status'] == 'draft' ):
		
				$class = ($wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';
				$views['draft'] = sprintf(__('<a href="%s"'. $class .'>Draft'. ((sizeof($result->posts) > 1) ? "s" : "") .' <span class="count">(%d)</span></a>', 'draft'),
				admin_url('edit.php?post_status=draft&post_type=project'),
				$result->found_posts);
		
			elseif( $type['status'] == 'pending' ):
		
				$class = ($wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';
				$views['pending'] = sprintf(__('<a href="%s"'. $class .'>Pending <span class="count">(%d)</span></a>', 'pending'),
					admin_url('edit.php?post_status=pending&post_type=project'),
					$result->found_posts);
		
			elseif( $type['status'] == 'trash' ):
		
				$class = ($wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';
				$views['trash'] = sprintf(__('<a href="%s"'. $class .'>Trash <span class="count">(%d)</span></a>', 'trash'),
					admin_url('edit.php?post_status=trash&post_type=project'),
					$result->found_posts);
		
			endif;
			}
		}
		return $views;
	}

	function fix_media_counts($views) {
		global $wpdb, $current_user, $post_mime_types, $avail_post_mime_types;
		$roles = ( array ) $current_user->roles; // obtaining the role 
		
		if( is_admin() && !current_user_can('edit_others_posts') && in_array('customer', $roles)) {

		$views = array();
		$_num_posts = array();
		$count = $wpdb->get_results( "
	  
		  SELECT post_mime_type, COUNT( * ) AS num_posts
		  FROM $wpdb->posts
		  WHERE post_type = 'attachment'
		  AND post_author = $current_user->ID
		  AND post_status != 'trash'
		  GROUP BY post_mime_type
	  
	   ", ARRAY_A );
	  
	   foreach( $count as $row )
		  $_num_posts[$row['post_mime_type']] = $row['num_posts'];
		  $_total_posts = array_sum($_num_posts);
		  $detached = isset( $_REQUEST['detached'] ) || isset( $_REQUEST['find_detached'] );
	  
	   if ( !isset( $total_orphans ) )
		  $total_orphans = $wpdb->get_var("
	  
			  SELECT COUNT( * )
			  FROM $wpdb->posts
			  WHERE post_type = 'attachment'
			  AND post_author = $current_user->ID
			  AND post_status != 'trash'
			  AND post_parent < 1
	  
		  ");
	  
		$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
	   
		foreach ( $matches as $type => $reals )
	  
		  foreach ( $reals as $real )
	  
			$num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];
			$class = ( empty($_GET['post_mime_type']) && !$detached && !isset($_GET['status']) ) ? ' class="current"' : '';
			$views['all'] = "<a href='upload.php'$class>" . sprintf( __('All <span class="count">(%s)</span>', 'uploaded files' ), number_format_i18n( $_total_posts )) . '</a>';
	  
		 foreach ( $post_mime_types as $mime_type => $label ) {
	  
		  $class = '';
		  if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
			continue;
	  
		  if ( !empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']) )
			$class = ' class="current"';
	  
		  if ( !empty( $num_posts[$mime_type] ) )
			$views[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), $num_posts[$mime_type] ) . '</a>';
	
		}

		$views['detached'] = '<a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf( __( 'Unattached <span class="count">(%s)</span>', 'detached files' ), $total_orphans ) . '</a>';
	
		}
		return $views;
	}

	function my_acf_input_admin_footer() {
	
		?>
		<script type="text/javascript">
		(function($) {
			
			acf.add_action('wysiwyg_tinymce_init', function( ed, id, mceInit, $field ){
				
				// ed (object) tinymce object returned by the init function
				// id (string) identifier for the tinymce instance
				// mceInit (object) args given to the tinymce function
				// $field (jQuery) field element 
				$('#tinymce').css('background', '#fff');
				
			});

			
		})(jQuery);	
		</script>
		<?php
				
		}
		
}
