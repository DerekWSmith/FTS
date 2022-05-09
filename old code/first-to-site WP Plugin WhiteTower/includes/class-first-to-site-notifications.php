<?php

/**
 * The notification related action and filter.
 *
 * @link       https://whitetower.com.au/
 * @since      1.0.0
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to full fill notification feature.
 *
 * @since      1.0.0
 * @package    First_To_Site
 * @subpackage First_To_Site/includes
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site_Notification {


	public function __construct() {
		$this->notification_option_page();
	}

	public function daily_notification_loader() {
		$this->sla_expire_checker();
		$this->unassigned_project_list_checker();
		$this->document_waiting_approved_list_checker();
	}

	private function shortcode_replace($body, $project_id = 0, $parent_value = array()) {
		$url =  get_site_url();
		$body = str_replace('[url]', $url, $body);
		$body = str_replace('[date]', date('d/m/Y'), $body);

		// error_log('parent: '. print_r($parent_value, true));
		if( !empty($project_id) && $project_id != 0 ) {
			$project_title = get_the_title($project_id);
			$project_url = get_edit_post_link($project_id);
			$body = str_replace('[project-url]', $project_url, $body);
			$body = str_replace('[project-title]', $project_title, $body);
		}

		// For the upload file field
		if(!empty($parent_value)) {
			$file_name = $parent_value['upload']['filename'];
			$body = str_replace('[file-name]', $file_name, $body);

		}

		return $body;
	}

	/**
	 * Create notification acf option page. (use period)
	 *
	 * Notification option page for mapping notification with e-mail template.
	 *
	 * @since    1.0.0
	 */
	public static function notification_option_page() {

		if( function_exists('acf_add_options_page') ) {
	
			acf_add_options_page(array(
				'page_title' 	=> 'Notification Settings',
				'menu_title'	=> 'Notification Settings',
				'menu_slug' 	=> 'notification-settings',
				'capability'	=> 'manage_options',
				'redirect'		=> false,
				'icon_url' => 'dashicons-rss',
			));
			
		}
	}

	/**
	*   Send notification when a new project is created
	*/
	function new_project_create_notification($post_id, $post, $update) {
		$project = $post;
		$project_id = $post_id;

		if ($project->post_type == 'project' && $project->post_status == 'publish' && empty(get_post_meta( $project_id, 'check_if_create_notification_sent_once' ))) {
			# New Post
			# Get email template post
			$new_project_creation_email_template = get_field( 'new_project_creation_email_template', 'options' );
			if ( $new_project_creation_email_template ) {

				$email_template = $new_project_creation_email_template; // email template post object
				$email_template_id = $email_template->ID;
				# send email to all project admin
				$args = array(
					'role'    => 'project_admin'
				);
				$users = get_users( $args );
			
				foreach ( $users as $user ) {
					$project_admin_emails[] = $user->user_email;
				}

				$to = $project_admin_emails;

				# Replace shortcode in content
				$subject_heading = $this->shortcode_replace(get_field('template_subject', $email_template_id), $project_id);
				$message = $this->shortcode_replace(get_field('template_content', $email_template_id), $project_id);

				wp_mail($to, $subject_heading, $message, '');

				# And update the meta so it won't run again
				update_post_meta( $project_id, 'check_if_create_notification_sent_once', true );

			}
		}
	}
	
	/**
	*   Send notification when document status change to need attention
	*/
	function project_document_need_attention_notification($value, $post_id, $field) {
		$project = get_post($post_id) ;
		$project_id = $post_id;
		$field_name = $field['name'];
		
		$parent_field_id = $field['parent'];

		$old_value = get_field($field_name, $project_id);
		$old_value = $old_value['value'];
		// error_log('field: '. print_r($field,true));
		// error_log('old_value: '. print_r($old_value,true));
		// error_log('value: '. print_r($value,true));

		if ($project->post_type == 'project' && $project->post_status == 'publish') {
			if($old_value != $value && $value == 'needs_attention') {		
				// error_log('value: '. print_r($value,true));
				// error_log('field: '. print_r($field,true));
				if(!empty(filter_var($field_name, FILTER_SANITIZE_NUMBER_INT)) ) 
					$parent_repeat_field_id = (int) filter_var($field_name, FILTER_SANITIZE_NUMBER_INT);
				else
					$parent_repeat_field_id = 0;
				// error_log('parent_repeat_field_id: '. print_r($parent_repeat_field_id,true));

				$parent_field = acf_get_field($parent_field_id, $post_id);
				$parent_value = get_field($parent_field['key'], $post_id);
				// error_log('parent_value['.$parent_repeat_field_id.']: '. print_r($parent_value[$parent_repeat_field_id],true));
		
				# New Post
				# Get email template post
				$email_template = get_field( 'project_document_incomplete_email_template', 'options' );
				if ( $email_template ) {
	
					$email_template_id = $email_template->ID;
	
					# send email to project creator
					$author_id = get_post_field( 'post_author', $project_id );
					$author_info = get_userdata($author_id);
					$author_email = $author_info->user_email;
	
					$to = $author_email;
	
					# Replace shortcode in content
					$subject_heading = $this->shortcode_replace(get_field('template_subject', $email_template_id), $project_id, $parent_value[$parent_repeat_field_id]);
					$message = $this->shortcode_replace(get_field('template_content', $email_template_id), $project_id, $parent_value[$parent_repeat_field_id]);
	
					wp_mail($to, $subject_heading, $message, '');
				}
			}
			else if($old_value != $value && $value == 'pending') {		
				// error_log('value: '. print_r($value,true));
				// error_log('field: '. print_r($field,true));
				if(!empty(filter_var($field_name, FILTER_SANITIZE_NUMBER_INT)) ) 
					$parent_repeat_field_id = (int) filter_var($field_name, FILTER_SANITIZE_NUMBER_INT);
				else
					$parent_repeat_field_id = 0;
				// error_log('parent_repeat_field_id: '. print_r($parent_repeat_field_id,true));

				$parent_field = acf_get_field($parent_field_id, $post_id);
				$parent_value = get_field($parent_field['key'], $post_id);
				// error_log('parent_value['.$parent_repeat_field_id.']: '. print_r($parent_value[$parent_repeat_field_id],true));
		
				# New Post
				# Get email template post
				$email_template = get_field( 'project_document_pending_for_review_notification', 'options' );
				if ( $email_template ) {
	
					$email_template_id = $email_template->ID;
	
					# send email to project admin
					$author = get_field( 'project_manger', $project_id );
					$author_email = $author['user_email'];
	
					$to = $author_email;
	
					# Replace shortcode in content
					$subject_heading = $this->shortcode_replace(get_field('template_subject', $email_template_id), $project_id, $parent_value[$parent_repeat_field_id]);
					$message = $this->shortcode_replace(get_field('template_content', $email_template_id), $project_id, $parent_value[$parent_repeat_field_id]);
	
					wp_mail($to, $subject_heading, $message, '');
				}
			}
		}

		return $value;
	}

	private function unassigned_project_list_checker() {
		// Query projects that project_manger field is empty
		$args_projects = array(
			'post_type' => array('project'),
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'nopaging' => true,
			'order' => 'DESC',
			'meta_key' => 'project_manger',
			'meta_compare' => 'NOT EXISTS',
		);

		$projects = new WP_Query( $args_projects );
		$total_project_unassigned = 0;

		// loop through projects that project_manger field is empty
		if ( $projects->have_posts() ) {
			$html = '<div style="margin-bottom: 40px;">';
				$html .= '<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif;" border="1">';
					$html .= '<thead>';
						$html .= '<tr>';
							$html .= '<th class="td" scope="col" style="text-align: left;">Title</th>';
							$html .= '<th class="td" scope="col" style="text-align: left;">Author</th>';
						$html .= '</tr>';
					$html .= '</thead>';
					$html .= '<tbody>';
					while ( $projects->have_posts() ) {
						$projects->the_post();
						// create the talbe of project name, author name, edit url
						$html .= '<tr>';

							$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
							$html .= get_the_title();
							$html .= '</td>';
							$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
							$html .= get_the_author();
							$html .= '</td>';

						$html .= '</tr>';
						$total_project_unassigned++;
					}
					$html .= '</tbody>';
				$html .= '</table>';
			$html .= '</div>';
		} 

		if( $total_project_unassigned > 0 ) {
			// send the reminder to all project admin user
			// Get email template post
			$email_template = get_field( 'unassigned_projects_list_email_template', 'options' );
			if ( $email_template ) {
	
				$email_template_id = $email_template->ID;
				// send email to all project admin
				$args = array(
					'role'    => 'project_admin'
				);
				$users = get_users( $args );
			
				foreach ( $users as $user ) {
					$project_admin_emails[] = $user->user_email;
				}
	
				$to = $project_admin_emails;
	
				// Replace shortcode in content
				$subject_heading = $this->shortcode_replace(get_field('template_subject', $email_template_id));
				$message = $this->shortcode_replace(get_field('template_content', $email_template_id));
				$message = str_replace('[un-assigned-project-table]', $html, $message);
	
				wp_mail($to, $subject_heading, $message, '');
			}
		}

		wp_reset_postdata();

			
	}
	
	private function document_waiting_approved_list_checker() {
		
		$project_doc_list = array(
			'doc_upload_architectural_drawing',
			'doc_upload_discharge_info',
			'doc_upload_soil_report',
			'doc_upload_planning_permit',
			'doc_upload_town_planning_drawing',
			'doc_upload_psi',
			'doc_upload_other',
		);

		// loop through project admin user 
		$args = array(
			'role'    => 'project_admin'
		);
		$users = get_users( $args );

		foreach ( $users as $user ) {
			$html = '';
			$user_id = $user->ID;
			// loop through project (assigned with this project manger, and status is need attention)

			// Custom WP query projects
			$args_projects = array(
				'post_type' => array('project'),
				'post_status' => array('publish'),
				'posts_per_page' => -1,
				'nopaging' => true,
				'order' => 'DESC',
				'meta_key' => 'project_manger',
				'meta_compare' => '=',
				'meta_value' => $user_id,
			);

			$projects = new WP_Query( $args_projects );
			$total_pending_doc = 0;

			if ( $projects->have_posts() ) {
				$html .= '<div style="margin-bottom: 40px;">';
					$html .= '<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif;" border="1">';
						while ( $projects->have_posts() ) {
							$projects->the_post();
							$document_list = array();
							$project_id = get_the_ID();
							
							// check if all the documents upload repeat field => status sub fields equal to need attention
							foreach ($project_doc_list as $project_doc) {
								$document_list = $this->get_pending_doc_list($project_id, $project_doc, $document_list);
							}

							// create table of the documents need attention with document name
							if(count($document_list) > 0) {
								$html .= '<tbody>';
									$html .= '<tr>';
										$html .= '<th class="td" scope="col" style="text-align: left; font-weight: 700;">'.get_the_title().'</th>';
									$html .= '</tr>';
								foreach ( $document_list as $document ) {
									// create the table of project name, author name, edit url
									$html .= '<tr>';
										$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
										$html .= $document;
										$html .= '</td>';
									$html .= '</tr>';
								}
								$html .= '</tbody>';

								$total_pending_doc += count($document_list);
							}
						}
					$html .= '</table>';
				$html .= '</div>';
			}

			if ($total_pending_doc>0) {
				// send the reminder to this project admin user
				// Get email template post
				$email_template = get_field( 'projects_document_pending_list_email_template', 'options' );
				if ( $email_template ) {
	
					$email_template_id = $email_template->ID;
	
					$to = $user->user_email;
	
					// Replace shortcode in content
					$subject_heading = $this->shortcode_replace(get_field('template_subject', $email_template_id));
					$message = $this->shortcode_replace(get_field('template_content', $email_template_id));
					$message = str_replace('[project-pending-document-table]', $html, $message);
	
					wp_mail($to, $subject_heading, $message, '');
				}
			}
			wp_reset_postdata();
		}
	}

	private function get_pending_doc_list($project_id, $project_doc, $document_list) {

		if ( have_rows( $project_doc, $project_id ) ) {
			while ( have_rows( $project_doc, $project_id ) ) {

				the_row(); 
				
				$status = get_sub_field( 'status' );
				if ( $status['value'] == 'pending' ) {
					// collect project name
					$upload = get_sub_field( 'upload' );

					if ( $upload && is_array($upload) )
						$document_list[] = $upload['filename'];
					else {
						$document_list[] = basename ( get_attached_file( $upload ) );
					}
				}
			}
		}

		return $document_list;
	}
	
	private function sla_expire_checker() {
		
		// loop each project admin
		
		// loop through project admin user 
		$args = array(
			'role'    => 'project_admin'
		);
		$users = get_users( $args );

		foreach ( $users as $user ) {
			$html = '';
			$user_id = $user->ID;

			// Grab all Email create by them and status is late
			// Custom WP query email
			$args_email = array(
				'post_type' => array('email'),
				'post_status' => array('publish'),
				'posts_per_page' => -1,
				'order' => 'DESC',
				'orderby' => 'date',
				'author' => $user_id,
				'meta_query' => array (
					
					array(
						'key'     => 'sla-status',
						'value'   => 'late',
						'compare' => '=',
					),
				)
			);

			$email = new WP_Query( $args_email );
			// error_log(print_r($email,true));
			$total_sla_expired = 0;
			
			if ( $email->have_posts() ) {
				$html .= '<div style="margin-bottom: 40px;">';
					$html .= '<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif;" border="1">';
						$html .= '<thead>';
							$html .= '<tr>';
								$html .= '<th class="td" scope="col" style="text-align: left;"></th>';
								$html .= '<th class="td" scope="col" style="text-align: left;">Email Title</th>';
								$html .= '<th class="td" scope="col" style="text-align: left;">Project Title</th>';
								$html .= '<th class="td" scope="col" style="text-align: left;">SLA</th>';
								$html .= '<th class="td" scope="col" style="text-align: left;">Vendor</th>';
							$html .= '</tr>';
						$html .= '</thead>';
						$html .= '<tbody>';
							while ( $email->have_posts() ) {
								$email->the_post();

								$email_id = get_the_ID();
								$related_project = get_field('related_project');
								$sla = get_field('sla');
								$vendor = get_field('sla_vendor', $sla);

								// create the table message with
									
									// project
									// email title
									// vendor

								$html .= '<tr>';

									$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
									$html .= $total_sla_expired+1;
									$html .= '</td>';
									
									$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
									$html .= get_the_title();
									$html .= '</td>';

									$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
									$html .= $related_project->post_title;
									$html .= '</td>';

									$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
									$html .= get_the_title($sla);
									$html .= '</td>';

									$html .= '<td class="td" style="text-align:left; vertical-align: middle; font-family: Helvetica Neue, Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">';
									$html .= $vendor->post_title;
									$html .= '</td>';
								$html .= '</tr>';

								$total_sla_expired ++;
							}
						$html .= '</tbody>';
					$html .= '</table>';
				$html .= '</div>';
			} 

			if ($total_sla_expired > 0) {
				// send the reminder to this project admin user
				// Get email template post
				$email_template = get_field( 'project_sla_expire_email_template', 'options' );
				if ( $email_template ) {
	
					$email_template_id = $email_template->ID;
	
					$to = $user->user_email;
	
					// Replace shortcode in content
					$subject_heading = $this->shortcode_replace(get_field('template_subject', $email_template_id));
					$message = $this->shortcode_replace(get_field('template_content', $email_template_id));
					$message = str_replace('[email-sla-expired-table]', $html, $message);
	
					wp_mail($to, $subject_heading, $message, '');
				}
			}
			wp_reset_postdata();
		}
	}
}
