<?php 
// Settings Page: Notification
class notification_Settings_Page {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wph_create_settings' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_sections' ) );
		add_action( 'admin_init', array( $this, 'wph_setup_fields' ) );
	}

	public function wph_create_settings() {
		$page_title = 'Notification Setting';
		$menu_title = 'Notification';
		$capability = 'manage_options';
		$slug = 'notification';
		$callback = array($this, 'wph_settings_content');
		$icon = 'dashicons-rss';
		$position = 65;
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
	}

	public function wph_settings_content() { ?>
		<div class="wrap">
			<h1>Notification Setting</h1>
			<?php settings_errors(); ?>
			<form method="POST" action="options.php">
				<?php
					settings_fields( 'notification' );
					do_settings_sections( 'notification' );
					submit_button();
				?>
			</form>
		</div> <?php
	}

	public function wph_setup_sections() {
		add_settings_section( 'notification_section', '', array(), 'notification' );
	}

	public function wph_setup_fields() {
		$template_array = array();
		$args_templates = array(
			'post_type' => array('email_template'),
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'order' => 'DESC',
			'orderby' => 'title',
		);

		$templates = new WP_Query( $args_templates );

		if ( $templates->have_posts() ) {
			while ( $templates->have_posts() ) {
				$templates->the_post();
				$template_array[get_the_ID()] = get_the_title(  );
			}
		} else {

		}

		wp_reset_postdata();

		$fields = array(
			array(
				'label' => 'Project creation email template',
				'id' => 'projectcreation_select',
				'type' => 'select',
				'section' => 'notification_section',
				'options' => $template_array
			),
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'wph_field_callback' ), 'notification', $field['section'], $field );
			register_setting( 'notification', $field['id'] );
		}
	}

	public function wph_field_callback( $field ) {
		$value = get_option( $field['id'] );
		$placeholder = '';
		if ( isset($field['placeholder']) ) {
			$placeholder = $field['placeholder'];
		}
		switch ( $field['type'] ) {
				case 'select':
				case 'multiselect':
					if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
						$attr = '';
						$options = '';
						foreach( $field['options'] as $key => $label ) {
							$options.= sprintf('<option value="%s" %s>%s</option>',
								$key,
								selected($value, $key, false),
								$label
							);
						}
						if( $field['type'] === 'multiselect' ){
							$attr = ' multiple="multiple" ';
						}
						printf( '<select name="%1$s" id="%1$s" %2$s>%3$s</select>',
							$field['id'],
							$attr,
							$options
						);
					}
					break;
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$placeholder,
					$value
				);
		}
		if( isset($field['desc']) ) {
			if( $desc = $field['desc'] ) {
				printf( '<p class="description">%s </p>', $desc );
			}
		}
	}
}
