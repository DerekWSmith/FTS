<?php
/**
 * Register Custom Post Types for this plugin
 *
 * @since      1.0.0
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site_cpt {

	/**
     * Register Custom Post Type - Project
	 *
	 * @since    1.0.0
	 */
	public function project() {

        $labels = array(
            'name'                  => _x( 'Projects', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Project', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Projects', 'text_domain' ),
            'name_admin_bar'        => __( 'Project', 'text_domain' ),
            'archives'              => __( 'Project Archives', 'text_domain' ),
            'attributes'            => __( 'Project Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Project:', 'text_domain' ),
            'all_items'             => __( 'All Projects', 'text_domain' ),
            'add_new_item'          => __( 'Add New Project', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Project', 'text_domain' ),
            'edit_item'             => __( 'Edit Project', 'text_domain' ),
            'update_item'           => __( 'Update Project', 'text_domain' ),
            'view_item'             => __( 'View Project', 'text_domain' ),
            'view_items'            => __( 'View Projects', 'text_domain' ),
            'search_items'          => __( 'Search Project', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into project', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this project', 'text_domain' ),
            'items_list'            => __( 'Projects list', 'text_domain' ),
            'items_list_navigation' => __( 'Projects list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter projects list', 'text_domain' ),
        );

        $capabilities = array(
            'edit_post'             => 'edit_project',
            'read_post'             => 'read_project',
            'delete_post'           => 'delete_project',
            'edit_posts'            => 'edit_project',
            'edit_others_posts'     => 'edit_others_project',
            'publish_posts'         => 'publish_project',
            'read_private_posts'    => 'read_private_project',
        );

        $args = array(
            'label'                 => __( 'Project', 'text_domain' ),
            'description'           => __( 'Projects created by First to site User', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-open-folder',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'     => 'project',
            'capabilities'       => $capabilities,
        );

        register_post_type( 'project', $args );
    
    }

	/**
     * Register Custom Post Type - SLA
	 *
	 * @since    1.0.0
	 */
    function service_level_agreement() {

        $labels = array(
            'name'                  => _x( 'SLA', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'SLA', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'SLA', 'text_domain' ),
            'name_admin_bar'        => __( 'SLA', 'text_domain' ),
            'archives'              => __( 'SLA Archives', 'text_domain' ),
            'attributes'            => __( 'SLA Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent SLA:', 'text_domain' ),
            'all_items'             => __( 'All SLA', 'text_domain' ),
            'add_new_item'          => __( 'Add New SLA', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New SLA', 'text_domain' ),
            'edit_item'             => __( 'Edit SLA', 'text_domain' ),
            'update_item'           => __( 'Update SLA', 'text_domain' ),
            'view_item'             => __( 'View SLA', 'text_domain' ),
            'view_items'            => __( 'View SLA', 'text_domain' ),
            'search_items'          => __( 'Search SLA', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into SLA', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this SLA', 'text_domain' ),
            'items_list'            => __( 'SLA list', 'text_domain' ),
            'items_list_navigation' => __( 'SLA list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter SLA list', 'text_domain' ),
        );
        $capabilities = array(
            'edit_post'             => 'edit_service_level_agreem',
            'read_post'             => 'read_service_level_agreem',
            'delete_post'           => 'delete_service_level_agreem',
            'edit_posts'            => 'edit_service_level_agreem',
            'edit_others_posts'     => 'edit_others_service_level_agreem',
            'publish_posts'         => 'publish_service_level_agreem',
            'read_private_posts'    => 'read_private_service_level_agreem',
        );

        $args = array(
            'label'                 => __( 'SLA', 'text_domain' ),
            'description'           => __( 'Service level agreement', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-media-default',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'     => 'service_level_agreem',
            'capabilities'       => $capabilities,

        );
        register_post_type( 'Service_level_agreem', $args );

    }

	/**
     * Register Custom Post Type - Vendor
	 *
	 * @since    1.0.0
	 */
    function vendor() {

        $labels = array(
            'name'                  => _x( 'Vendors', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'Vendor', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'Vendors', 'text_domain' ),
            'name_admin_bar'        => __( 'Vendor', 'text_domain' ),
            'archives'              => __( 'Vendor Archives', 'text_domain' ),
            'attributes'            => __( 'Vendor Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Vendor:', 'text_domain' ),
            'all_items'             => __( 'All Vendors', 'text_domain' ),
            'add_new_item'          => __( 'Add New Vendor', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Vendor', 'text_domain' ),
            'edit_item'             => __( 'Edit Vendor', 'text_domain' ),
            'update_item'           => __( 'Update Vendor', 'text_domain' ),
            'view_item'             => __( 'View Vendor', 'text_domain' ),
            'view_items'            => __( 'View Vendors', 'text_domain' ),
            'search_items'          => __( 'Search Vendor', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into vendor', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this vendor', 'text_domain' ),
            'items_list'            => __( 'Vendors list', 'text_domain' ),
            'items_list_navigation' => __( 'Vendors list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter vendors list', 'text_domain' ),
        );

        $capabilities = array(
            'edit_post'             => 'edit_vendor',
            'read_post'             => 'read_vendor',
            'delete_post'           => 'delete_vendor',
            'edit_posts'            => 'edit_vendor',
            'edit_others_posts'     => 'edit_others_vendor',
            'publish_posts'         => 'publish_vendor',
            'read_private_posts'    => 'read_private_vendor',
        );

        $args = array(
            'label'                 => __( 'Vendor', 'text_domain' ),
            'description'           => __( 'Vendor', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-store',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'     => 'vendor',
            'capabilities'       => $capabilities,
        );
        register_post_type( 'vendor', $args );

    }

    /**
     * Register Custom Post Type - Email
	 *
	 * @since    1.0.0
	 */
    function email() {

        $labels = array(
            'name'                  => _x( 'E-mails', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'E-mail', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'E-mails', 'text_domain' ),
            'name_admin_bar'        => __( 'E-mail', 'text_domain' ),
            'archives'              => __( 'E-mail Archives', 'text_domain' ),
            'attributes'            => __( 'E-mail Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent E-mail:', 'text_domain' ),
            'all_items'             => __( 'All E-mails', 'text_domain' ),
            'add_new_item'          => __( 'Add New E-mail', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New E-mail', 'text_domain' ),
            'edit_item'             => __( 'Edit E-mail', 'text_domain' ),
            'update_item'           => __( 'Update E-mail', 'text_domain' ),
            'view_item'             => __( 'View E-mail', 'text_domain' ),
            'view_items'            => __( 'View E-mails', 'text_domain' ),
            'search_items'          => __( 'Search E-mail', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into E-mail', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this E-mail', 'text_domain' ),
            'items_list'            => __( 'E-mails list', 'text_domain' ),
            'items_list_navigation' => __( 'E-mails list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter E-mails list', 'text_domain' ),
        );

        $capabilities = array(
            'edit_post'             => 'edit_email',
            'read_post'             => 'read_email',
            'delete_post'           => 'delete_email',
            'edit_posts'            => 'edit_email',
            'edit_others_posts'     => 'edit_others_email',
            'publish_posts'         => 'publish_email',
            'read_private_posts'    => 'read_private_email',
        );

        $args = array(
            'label'                 => __( 'E-mail', 'text_domain' ),
            'description'           => __( 'E-mail submissions', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-email',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'     => 'email',
            'capabilities'       => $capabilities,
        );
        register_post_type( 'email', $args );

    }

    /**
     * Register Custom Post Type - Email Template
	 *
	 * @since    1.0.0
	 */
    function email_template() {

        $labels = array(
            'name'                  => _x( 'E-mail Templates', 'Post Type General Name', 'text_domain' ),
            'singular_name'         => _x( 'E-mail Template', 'Post Type Singular Name', 'text_domain' ),
            'menu_name'             => __( 'E-mail Templates', 'text_domain' ),
            'name_admin_bar'        => __( 'E-mail Template', 'text_domain' ),
            'archives'              => __( 'Template Archives', 'text_domain' ),
            'attributes'            => __( 'Template Attributes', 'text_domain' ),
            'parent_item_colon'     => __( 'Parent Template:', 'text_domain' ),
            'all_items'             => __( 'All Templates', 'text_domain' ),
            'add_new_item'          => __( 'Add New Template', 'text_domain' ),
            'add_new'               => __( 'Add New', 'text_domain' ),
            'new_item'              => __( 'New Template', 'text_domain' ),
            'edit_item'             => __( 'Edit Template', 'text_domain' ),
            'update_item'           => __( 'Update Template', 'text_domain' ),
            'view_item'             => __( 'View Template', 'text_domain' ),
            'view_items'            => __( 'View Templates', 'text_domain' ),
            'search_items'          => __( 'Search Template', 'text_domain' ),
            'not_found'             => __( 'Not found', 'text_domain' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
            'featured_image'        => __( 'Featured Image', 'text_domain' ),
            'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
            'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
            'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
            'insert_into_item'      => __( 'Insert into template', 'text_domain' ),
            'uploaded_to_this_item' => __( 'Uploaded to this template', 'text_domain' ),
            'items_list'            => __( 'Templates list', 'text_domain' ),
            'items_list_navigation' => __( 'Templates list navigation', 'text_domain' ),
            'filter_items_list'     => __( 'Filter templates list', 'text_domain' ),
        );

        $capabilities = array(
            'edit_post'             => 'edit_email_template',
            'read_post'             => 'read_email_template',
            'delete_post'           => 'delete_email_template',
            'edit_posts'            => 'edit_email_template',
            'edit_others_posts'     => 'edit_others_email_template',
            'publish_posts'         => 'publish_email_template',
            'read_private_posts'    => 'read_private_email_template',
        );

        $args = array(
            'label'                 => __( 'E-mail Template', 'text_domain' ),
            'description'           => __( 'Pre-set message for E-mails', 'text_domain' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor' ),
            'taxonomies'            => array( '' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-email-alt2',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'     => 'email_template',
            'capabilities'       => $capabilities,
        );
        register_post_type( 'email_template', $args );

    }

	/**
     * Register Custom Taxonomy - project_status
	 *
	 * @since    1.0.0
	 */
    function project_status() {

        $labels = array(
            'name'                       => _x( 'Project Statuses', 'Taxonomy General Name', 'text_domain' ),
            'singular_name'              => _x( 'Project Status', 'Taxonomy Singular Name', 'text_domain' ),
            'menu_name'                  => __( 'Project Status', 'text_domain' ),
            'all_items'                  => __( 'All status', 'text_domain' ),
            'parent_item'                => __( 'Parent Status', 'text_domain' ),
            'parent_item_colon'          => __( 'Parent Status:', 'text_domain' ),
            'new_item_name'              => __( 'New Item Status', 'text_domain' ),
            'add_new_item'               => __( 'Add New Status', 'text_domain' ),
            'edit_item'                  => __( 'Edit Status', 'text_domain' ),
            'update_item'                => __( 'Update Status', 'text_domain' ),
            'view_item'                  => __( 'View Status', 'text_domain' ),
            'separate_items_with_commas' => __( 'Separate status with commas', 'text_domain' ),
            'add_or_remove_items'        => __( 'Add or remove status', 'text_domain' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
            'popular_items'              => __( 'Popular Status', 'text_domain' ),
            'search_items'               => __( 'Search status', 'text_domain' ),
            'not_found'                  => __( 'Not Found', 'text_domain' ),
            'no_terms'                   => __( 'No status', 'text_domain' ),
            'items_list'                 => __( 'Status list', 'text_domain' ),
            'items_list_navigation'      => __( 'Status list navigation', 'text_domain' ),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'capability_type'     => 'project_status',
            'capabilities'       => array(
                'manage_terms' => 'manage_project_status',
                'edit_terms' => 'edit_project_status',
                'delete_terms' => 'delete_project_status',
                'assign_terms' => 'assign_project_status',
            ),
        );
        register_taxonomy( 'project_status', array( 'project' ), $args );

    }

    /**
     * Register Custom Taxonomy - document_status
	 *
	 * @since    1.0.0
	 */
    function document_status() {

        $labels = array(
            'name'                       => _x( 'Document status', 'Taxonomy General Name', 'text_domain' ),
            'singular_name'              => _x( 'Document status', 'Taxonomy Singular Name', 'text_domain' ),
            'menu_name'                  => __( 'Document status', 'text_domain' ),
            'all_items'                  => __( 'All status', 'text_domain' ),
            'parent_item'                => __( 'Parent status', 'text_domain' ),
            'parent_item_colon'          => __( 'Parent status:', 'text_domain' ),
            'new_item_name'              => __( 'New Status Name', 'text_domain' ),
            'add_new_item'               => __( 'Add New Status', 'text_domain' ),
            'edit_item'                  => __( 'Edit Status', 'text_domain' ),
            'update_item'                => __( 'Update Status', 'text_domain' ),
            'view_item'                  => __( 'View Status', 'text_domain' ),
            'separate_items_with_commas' => __( 'Separate status with commas', 'text_domain' ),
            'add_or_remove_items'        => __( 'Add or remove status', 'text_domain' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
            'popular_items'              => __( 'Popular Status', 'text_domain' ),
            'search_items'               => __( 'Search Status', 'text_domain' ),
            'not_found'                  => __( 'Not Found', 'text_domain' ),
            'no_terms'                   => __( 'No status', 'text_domain' ),
            'items_list'                 => __( 'Status list', 'text_domain' ),
            'items_list_navigation'      => __( 'Status list navigation', 'text_domain' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'document_status', array( 'attachment' ), $args );

    }
}

?>