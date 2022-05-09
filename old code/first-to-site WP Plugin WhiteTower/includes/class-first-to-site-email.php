<?php

/**
 * The email-specific functionality of the plugin.
 *
 * @link       https://whitetower.com.au/
 * @since      1.0.0
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/email
 */

/**
 * The email-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the email-specific stylesheet and JavaScript.
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/email
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site_Email {


	private function email_body_replace($body, $email_id) {
		$url =  get_site_url();
		$body = str_replace('[url]', $url, $body);

		return $body;
	}

	public function email_send_after_post_save( $post_id ) {
		
		// Get newly saved values.
		$post_type = get_post_type( $post_id );
		
		if($post_type != "email") return;

		if(metadata_exists('email', $post_id, 'email_submit') && get_post_meta($post_id, 'email_submit') == 1) return;
		

		if ( $subject_heading = get_field( 'subject_heading', $post_id ) ) {
			// Update the email post title to the subject heading
			$post_update = array(
				'ID'         => $post_id,
				'post_title' => $subject_heading
			);
		
			wp_update_post( $post_update );	
		}

		$to  = get_field( 'cc', $post_id );
		if($bcc = get_field( 'bcc', $post_id )) $to .= ', ' . $bcc;
		$template = get_field( 'template', $post_id );
		$message = $this->email_body_replace(get_field('template_content', $template->ID), $post_id);
		
		// $attachment = get_field( 'attachment', $post_id  );
		// $attachments = ( $attachment ) ? array($attachment['url']): array();
		$attachments = array(); // list of attachments urls
		
		if ( have_rows( 'attatchment' ) ) {
			while ( have_rows( 'attatchment' ) ) {
				the_row(); 
				$attatchment_project_id = get_sub_field( 'attatchment_project' );
				$doc_type = get_sub_field( 'attatchment_doc_type' );
				$document_id = get_sub_field( 'document_id' );
				// error_log('attachment_project_id: '. print_r($attatchment_project_id, true));
				// error_log('doc_type: '. print_r($doc_type, true));
				// error_log('document_id: '. print_r($document_id, true));
				if ( $attatchment_project_id ) {
					$attachment = get_field( $doc_type .'_'.$document_id.'_upload', $attatchment_project_id);
					// error_log('attachment: '. print_r($attachment, true));

					if ($attachment) {
						$attachments[] = get_attached_file( $attachment['id'] );
					}
				}
			}
		}
			
		// error_log('attachments: '. print_r($attachments, true));

		wp_mail($to, $subject_heading, $message, '', $attachments);

		if(!metadata_exists('email', $post_id, 'email_submit')) {
			add_post_meta(
				$post_id,
				'email_submit',
				true
			);
		}
		
		if(!metadata_exists('email', $post_id, 'email_submit_date')) {
			add_post_meta( $post_id, 'email_submit_date', time() );
		}
		else {
			update_post_meta( $post_id, 'email_submit_date', time() );
		}
	}

	// Display project summary template
	private function load_email_buttons_template () {
		global $pagenow;
		global $post;
		
		ob_start();

		?>
		<div id="save-action" class="text-right w-full">
			<a href="/wp-admin/edit.php?post_type=email" id="cancel" class="inline-flex px-16 py-3 font-semibold hover:opacity-75">Cancel</a>

			<?php if( in_array( $pagenow, array( 'post-new.php' ) ) && !metadata_exists('email', $post->ID, 'email_submit')): ?>
				<button type="submit" name="publish" id="publish" value="Publish" class="bg-black bg-opacity-80 duration-200 font-semibold hover:bg-opacity-75 inline-flex px-16 py-3 rounded-lg text-white transition-colors">Submit</button>
			<?php else:?>
				<?php if ( $submit_date = (get_post_meta($post->ID, 'email_submit_date'))) : ?>
					<span class="inline-flex py-3 font-semibold">Submit at <?php echo date("d-m-Y H:i:s", $submit_date[0]); ?></span>
				<?php endif; ?>
			<?php endif; ?>

		</div>
		<?php

		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
		wp_reset_postdata();
		
	}

	// Allow for summary template in messages
	public function acf_load_field_message_for_email_buttons ( $field ) {
			$field['message'] = $this->load_email_buttons_template();
		return $field;
	}
}
