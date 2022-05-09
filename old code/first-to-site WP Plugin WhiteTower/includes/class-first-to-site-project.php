<?php

/**
 * The project-specific functionality of the plugin.
 *
 * @link       https://whitetower.com.au/
 * @since      1.0.0
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/project
 */

/**
 * The project-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the project-specific stylesheet and JavaScript.
 *
 * @package    First_To_Site
 * @subpackage First_To_Site/project
 * @author     Whitetower Digital <services@whitetower.com.au>
 */
class First_To_Site_Project {

	public function __construct() {

	}

	// Avoid project summary show in the edit page
	public function load_project_summary_field($field) {
		global $pagenow;
		if( in_array( $pagenow, array( 'post-new.php' ) )){
			$keys = array_keys($field);
			$values = array_fill(0, count($keys), null);
			$new_field = array_combine($keys, $values);
			$new_field['key'] = "field_619f120ac4ab3";
			return $new_field;
		}
		return $field;
	}

	// Display project summary template
	private function load_project_summary_template () {
		global $pagenow;

		// Don't show the project summary in new page
		if( !in_array( $pagenow, array( 'post-new.php' ) )){
			ob_start();
			
			include_once plugin_dir_path(__DIR__).'assets/parts/project-summary.php';
	
			$output_string = ob_get_contents();
			ob_end_clean();
			return $output_string;
			wp_reset_postdata();
		}
	}

	// Allow for summary template in messages
	public function acf_load_field_message_for_project_summary ( $field ) {
		$field['message'] = $this->load_project_summary_template();
		return $field;
	}

	// Avoid Email log tab show in the edit page
	public function load_email_log_field($field) {
		global $pagenow;
		
		if( in_array( $pagenow, array( 'post-new.php' ) )){
			$keys = array_keys($field);
			$values = array_fill(0, count($keys), null);
			$new_field = array_combine($keys, $values);
			$new_field['key'] = "field_61f72d1012a56";
			return $new_field;
		}
		return $field;
	}

	public function hide_email_log_field_for_customer( $field ) {

		global $current_user;
		$roles = ( array ) $current_user->roles; // obtaining the role 
		if( is_admin() && in_array('customer', $roles)) {
			return false;
		}
	  
		return $field;
	  }
	  
	// Display Email log tab's template
	private function load_email_log_template () {
		global $pagenow, $current_user;
		$roles = ( array ) $current_user->roles; // obtaining the role 

		// Don't show the project summary in new page
		if( !in_array( $pagenow, array( 'post-new.php' ) ) && !in_array('customer', $roles) ){
			ob_start();
			
			include_once plugin_dir_path(__DIR__).'assets/parts/project-email-log.php';
	
			$output_string = ob_get_contents();
			ob_end_clean();
			wp_reset_postdata();
			// error_log(print_r('load email template: '. get_the_ID(), true));

			return $output_string;
		}

	}

	// Allow for email log template in messages
	public function acf_load_field_message_for_email_log ( $field ) {
		$field['message'] = $this->load_email_log_template();

		return $field;
	}

	// Check required plugin is installed
	public function fts_plugin_has_parents() {
		if ( 
			is_admin() && current_user_can( 'activate_plugins' ) && 
			!is_plugin_active( 'advanced-custom-fields-pro/acf.php') || 
			!is_plugin_active( 'members/members.php' ) 
		) 
		{
			add_action( 'admin_notices', 'required_plugin_notice' );

			deactivate_plugins( plugin_basename( __FILE__) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
	
	function wpse_set_attachment_category( $file )
	{
		// error_log(print_r($file, true));
		$attachment = $this->wp_get_attachment_by_post_name($file->name);
		if ( $attachment ) {
			wp_set_object_terms($attachment->ID, 'needs-attention', 'document_status', true);
		}
		return $file;
	}

	private function wp_get_attachment_by_post_name( $post_name ) {
		$args           = array(
			'posts_per_page' => 1,
			'post_type'      => 'attachment',
			'name'           => trim( $post_name ),
		);

		$get_attachment = new WP_Query( $args );

		if ( ! $get_attachment || ! isset( $get_attachment->posts, $get_attachment->posts[0] ) ) {
			return false;
		}

		return $get_attachment->posts[0];
	}
	
	// message
		
	/**
	 * Exclude feedback comments from queries and RSS.

	* @param  array $clauses A compacted array of comment query clauses.
	* @return array
	*/
	public function exclude_project_messages( $clauses ) {
		global $pagenow;

		if($pagenow != 'post.php') {
			$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'project_message' ";
		}
		return $clauses;

	}

	/**
	 * Exclude feedback comments from queries and RSS.
	 *
	 * @param  string $where The WHERE clause of the query.
	 * @return string
	 */
	public function exclude_project_messages_from_feed_where( $where ) {
		return $where . ( $where ? ' AND ' : '' ) . " comment_type != 'project_message' ";
	}

	// Allow for summary template in messages
	public function project_message_display ( $field ) {
			$field['message'] = $this->project_message_display_callback();
		return $field;
	}
	/**
	 * Feedback post notice meta box call back function
	 *
	 * @param [object] $post
	 */
	function project_message_display_callback(  ) {
		
		global $post;
		$args = array(
			'post_id' => $post->ID,
		);
		// remove_filter( 'comments_clauses', 'exclude_project_comments');

		$notes = $this->get_project_messages( $args );
		ob_start();

		?>
		<ul class="order_notes">
			<?php
			if ( $notes ) {
				foreach ( $notes as $note ) {
					$css_class   = array( 'message' );
					$css_class[] = 'system' === $note->added_by ? 'system-note' : '';
					?>
					<li class="<?php echo esc_attr( implode( ' ', $css_class ) ); ?>" data-rel="<?php echo absint( $note->comment_ID ); ?>">
						<div class="note_content">
							<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); // @codingStandardsIgnoreLine ?>
						</div>
						<p class="meta">
							<abbr class="exact-date" title="<?php echo esc_attr( $note->comment_date ); ?>">
								<?php
								echo esc_html( sprintf( __( '%1$s', 'first-to-site' ), $note->comment_date ) );
								?>
							</abbr>
							<?php
							if ( 'system' !== $note->comment_author ) :
								echo esc_html( sprintf( ' ' . __( 'by %s', 'first-to-site' ), $note->comment_author ) );
							endif;
							?>
							<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'first-to-site' ); ?></a>
						</p>
					</li>
					<?php
				}
			} else {
				?>
				<li class="no-items"><?php esc_html_e( 'There are no message yet.', 'first-to-site' ); ?></li>
				<?php
			}
			?>
		</ul>
		<div class="add_note">
			<p>
				<textarea type="text" name="order_note" id="add_order_note" class="input-text" cols="20" rows="5"></textarea>
				<button type="button" class="add_note bg-black bg-opacity-80 duration-200 font-semibold hover:bg-opacity-75 inline-flex px-8 py-3 rounded-lg text-white transition-colors"><?php esc_html_e( 'Submit', 'first-to-site' ); ?></button>
				<button type="button" class="clear_note bg-black bg-opacity-0 duration-200 font-semibold hover:bg-opacity-5 inline-flex px-8 py-3 rounded-lg text-black transition-colors"><?php esc_html_e( 'clear', 'first-to-site' ); ?></button>
			</p>
		</div>
		<?php
		
		$output_string = ob_get_contents();
		ob_end_clean();
		return $output_string;
		wp_reset_postdata();

	}

	/**
	 * Get feedback notes function using post id
	 * 
	 * @param [array] $args
	 * @return [object array] $notes
	 */
	function get_project_messages( $args ) {

		// Define orderby.
		$orderby_mapping = array(
			'date_created'     => 'comment_date',
			'date_created_gmt' => 'comment_date_gmt',
			'id'               => 'comment_ID',
		);

		$args['orderby'] = ! empty( $args['orderby'] ) && in_array( $args['orderby'], array( 'date_created', 'date_created_gmt', 'id' ), true ) ? $orderby_mapping[ $args['orderby'] ] : 'comment_ID';

		// Set correct comment type.
		$args['type'] = 'project_message';

		// Always approved.
		$args['status'] = 'approve';

		// Does not support 'count' or 'fields'.
		unset( $args['count'], $args['fields'] );

		$notes = get_comments( $args );

		return $notes;
	}

	/**
	 * Get feedback note function based on note id/comment id
	 *
	 * @param [string] $data
	 * @return object $data
	 */
	function get_project_message( $data ) {
		if ( is_numeric( $data ) ) {
			$data = get_comment( $data );
		}

		if ( ! is_a( $data, 'WP_Comment' ) ) {
			return null;
		}

		return (object) apply_filters(
			'project_get_order_note',
			array(
				'id'            => (int) $data->comment_ID,
				'date_created'  => $data->comment_date,
				'content'       => $data->comment_content,
				'customer_note' => (bool) get_comment_meta( $data->comment_ID, 'is_customer_note', true ),
				'added_by'      => __( 'Project', 'first-to-site' ) === $data->comment_author ? 'system' : $data->comment_author,
			),
			$data
		);
	}

	/**
	 * Add note to feedback
	 *
	 * @param [string] $post_id
	 * @param [string] $note
	 * @param boolean $added_by_user
	 * @return [int] $comment_id
	 */
	function add_project_message( $post_id, $note, $added_by_user = false ) {
		if ( ! $post_id ) {
		return 0;
		}

		if ( is_user_logged_in() && current_user_can( 'edit_post', $post_id ) && $added_by_user ) {
		$user                 = get_user_by( 'id', get_current_user_id() );
		$comment_author       = $user->display_name;
		$comment_author_email = $user->user_email;
		} else {
		$comment_author        = __( 'Project', 'first-to-site' );
		$comment_author_email  = strtolower( __( 'Project', 'first-to-site' ) ) . '@';
		$comment_author_email .= isset( $_SERVER['HTTP_HOST'] ) ? str_replace( 'www.', '', sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : 'noreply.com'; // WPCS: input var ok.
		$comment_author_email  = sanitize_email( $comment_author_email );
		}
		$commentdata = apply_filters(
			'new_project_message_data',
		array(
			'comment_post_ID'      => $post_id,
			'comment_author'       => $comment_author,
			'comment_author_email' => $comment_author_email,
			'comment_author_url'   => '',
			'comment_content'      => $note,
			'comment_agent'        => 'Project',
			'comment_type'         => 'project_message',
			'comment_parent'       => 0,
			'comment_approved'     => 1,
		),
		array(
			'order_id'         => $post_id,
		)
		);

		$comment_id = wp_insert_comment( $commentdata );

		return $comment_id;
	}

	/**
	 * Delete note based on note id
	 *
	 * @param [string] $note_id
	 */
	function delete_project_message( $note_id ) {
		return wp_delete_comment( $note_id, true );
	}

	/**
	 * Ajax custom delete note callback function
	 */
	function delete_project_message_callback() {
		check_ajax_referer( 'delete-project-message', 'security' );
		if ( ! current_user_can( 'edit_posts' ) || ! isset( $_POST['note_id'] ) ) {
			wp_die( -1 );
		}

		$note_id = (int) $_POST['note_id'];

		if ( $note_id > 0 ) {
			$this->delete_project_message( $note_id );
		}
		wp_die();
	}

	/**
	 * AJAX custom add feedback callback function
	 *
	 * @return void
	 */
	function add_project_message_callback() {
		check_ajax_referer( 'add-project-message', 'security' );

		if ( ! current_user_can( 'edit_posts' ) || ! isset( $_POST['post_id'], $_POST['note']) ) {
			wp_die( -1 );
		}

		$post_id   = absint( $_POST['post_id'] );
		$note      = wp_kses_post( trim( wp_unslash( $_POST['note'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( $post_id > 0 ) {
			
			$comment_id = $this->add_project_message( $post_id, $note, true );
			$note       = $this->get_project_message( $comment_id );

			$note_classes   = array( 'message' );
			$css_class[] = 'project' === $note->added_by ? 'system-note' : '';
			?>
			<li rel="<?php echo absint( $note->id ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
				<div class="note_content">
					<?php echo wp_kses_post( wpautop( wptexturize( make_clickable( $note->content ) ) ) ); ?>
				</div>
				<p class="meta">
					<abbr class="exact-date" title="<?php echo esc_attr( $note->date_created); ?>">
						<?php
						/* translators: $1: Date created, $2 Time created */
						printf( esc_html__( '%1$s', 'first-to-site' ), esc_html( $note->date_created ) );
						?>
					</abbr>
					<?php
					if ( 'system' !== $note->added_by ) :
						/* translators: %s: note author */
						printf( ' ' . esc_html__( 'by %s', 'first-to-site' ), esc_html( $note->added_by ) );
					endif;
					?>
					<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'first-to-site' ); ?></a>
				</p>
			</li>
			<?php
		}
		wp_die();
	}

	public function status_edit_exclude( $field ) {
		
		global $current_user;
		$roles = ( array ) $current_user->roles; // obtaining the role 
		if( is_admin() && in_array('customer', $roles)) {
			$field['readonly'] = 1;
			$field['disabled'] = 1;
		}

		return $field;
	}

	public function update_status_after_file_reupload ($value, $post_id, $field, $original) {
		$project = get_post($post_id) ;
		$project_id = $post_id;
		$field_name = $field['name'];
		
		$parent_field_id = $field['parent'];

		// Check old value and only update status to pending when it is attention-need before
		$old_value = get_field($field_name, $project_id);

		if( $old_value['id'] != $original ) {

			$counter = 1;
			$status_field_name = preg_replace_callback("/upload/", function ($m) use (&$counter) {

				#-- replacement for 2nd occurence of "upload"
				if ($counter++ == 2) {
					return "status";
				}

				#-- else leave current match
				return $m[0];

			}, $field_name);

			$status_field_value = get_field($status_field_name, $project_id);

			if ( $status_field_value['value'] == 'needs_attention' ) {

				update_field($status_field_name, 'pending', $project_id);
			}
		}


		return $value;
	}
}
