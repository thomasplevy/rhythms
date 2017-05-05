<?php
/**
 * Handle admin settings screens
 * @since    1.0.0
 * @version  1.0.0
 */

// prevent direct access
if ( ! defined( 'ABSPATH' ) ) { exit; }

class Rhythms_Admin_Settings {

	const PAGE_SLUG = 'rhythms-settings';
	const PREFIX = 'rhythms_';

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'register_page' ) );
		add_action( 'admin_init', array( $this, 'save' ) );

	}

	public function save() {

		if ( ! isset( $_POST ) ) {
			return;
		}

		if ( ! isset( $_POST['_rhythms_nonce'] ) || ! wp_verify_nonce( $_POST['_rhythms_nonce'], 'save_rhythms_settings' ) ) {
			return;
		}

		foreach ( $this->get_settings() as $id => $setting ) {

			if ( isset( $_POST[ $this->get_option_name( $id ) ] ) ) {
				$this->set_option( $id, $_POST[ $this->get_option_name( $id ) ] );
			} elseif ( 'checkbox' === $setting['type'] ) {
				$this->set_option( $id, 'no' );
			}

		}

	}


	public function get_settings() {
		return apply_filters( 'rhythms_settings', array(
			'sometimes_y' => array(
				'description' => __( 'Check to have Rhythms Optimizer automatically remove "Y". It\'s the worst, we agree with you.', 'rhythms' ),
				'default' => 'no',
				'label' => __( 'And sometimes "Y"?', 'rhythms' ),
				'type' => 'checkbox',
			)
		) );
	}

	private function get_option_name( $option ) {
		return self::PREFIX . $option;
	}

	private function get_option( $option, $default = '' ) {
		return get_option( $this->get_option_name( $option ), $default );
	}

	private function set_option( $option, $value ) {
		$value = sanitize_text_field( $value );
		return update_option( $this->get_option_name( $option ), $value );
	}

	private function get_checkbox_html( $id, $settings ) {
		ob_start();
		?>
		<fieldset>

			<legend class="screen-reader-text">
				<span>Membership</span>
			</legend>

			<label for="<?php echo $this->get_option_name( $id ); ?>">
				<input <?php checked( 'yes', $this->get_option( $id, $settings['default'] ) ); ?> id="<?php echo $this->get_option_name( $id ); ?>" name="<?php echo $this->get_option_name( $id ); ?>" type="checkbox" value="yes">
				<?php echo $settings['description']; ?>
			</label>

		</fieldset>
		<?php
		return ob_get_clean();
	}

	public function register_page() {

		add_menu_page( __( 'Rhythms', 'rhythms' ), __( 'Rhythms', 'rhythms' ), 'manage_options', self::PAGE_SLUG, array( $this, 'output_page' ), 'dashicons-format-audio' );

	}

	public function output_field( $id, $settings ) {
		switch ( $settings['type'] ) {
			case 'checkbox':
				$element = $this->get_checkbox_html( $id, $settings );
			break;
		}
		?>
		<tr>
			<th scope="row">
				<label for="<?php echo $this->get_option_name( $id ); ?>"><?php echo $settings['label']; ?></label>
			</th>
			<td><?php echo $element; ?></td>
		</tr>
		<?php
	}

	public function output_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Rhythms Settings', 'rhythms' ); ?></h1>

			<form action="<?php echo esc_url( admin_url( add_query_arg( 'page', self::PAGE_SLUG, 'admin.php' ) ) ); ?>" method="POST">

				<table class="form-table">

					<?php foreach ( $this->get_settings() as $id => $setting ) : ?>
						<?php $this->output_field( $id, $setting ); ?>
					<?php endforeach; ?>

				</table>

				<?php wp_nonce_field( 'save_rhythms_settings', '_rhythms_nonce' ); ?>
				<button class="button button-primary" type="submit"><?php _e( 'Save', 'rhythms' ); ?></button>

			</form>

		</div>
		<?php
	}

}

return new Rhythms_Admin_Settings();
