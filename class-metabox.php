<?php
/**
 * File for handling the plugin metabox.
 *
 * @created 2025-04-09
 * @author atiqsu <atiqur.su@gmail.com>
 * @package rtCamp
 */

namespace rtCamp;

/**
 * Metabox class
 */
class Metabox {

	const CONTRIBUTORS_META_KEY     = '_rtc_contributors';
	const CONTRIBUTORS_NONCE        = 'rtc_contributors_nonce';
	const CONTRIBUTORS_NONCE_ACTION = 'rtc_contributors_nonce_action';

	/**
	 * Initialize the metabox.
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_post', array( $this, 'save_contributor_data' ), 10, 2 );
	}

	/**
	 * Add the metabox.
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'rtc_contributors_meta_box',
			__( 'Contributors', 'rtc-wp-assignment' ),
			array( $this, 'render_metabox' ),
			'post'
		);
	}

	/**
	 * Renders the content of the contributors metabox.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox( $post ) {

		$saved_contributors = get_post_meta( $post->ID, self::CONTRIBUTORS_META_KEY, true );
		$saved_contributors = is_array( $saved_contributors ) ? $saved_contributors : array();

		wp_nonce_field( self::CONTRIBUTORS_NONCE_ACTION, self::CONTRIBUTORS_NONCE );

		$users = get_users(
			array(
				'role__in' => array( 'administrator', 'editor', 'author', 'contributor' ),
				'orderby'  => 'display_name',
				'order'    => 'ASC',
			)
		);

		echo '<div class="rtc-contributors-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #ccd0d4; padding: 6px;">';

		if ( ! empty( $users ) ) {

			echo '<ul>';

			foreach ( $users as $user ) {
				$user_id = $user->ID;
				$checked = in_array( $user_id, $saved_contributors, true ) ? 'checked' : '';

				echo '<li>';
				echo '<label for="rtc_contributor_' . esc_attr( $user_id ) . '">';
				echo '<input type="checkbox" name="rtc_contributors[]" id="rtc_contributor_' . esc_attr( $user_id ) . '" value="' . esc_attr( $user_id ) . '" ' . $checked . '> '; // phpcs:ignore
				echo esc_html( $user->display_name );
				echo '</label>';
				echo '</li>';
			}

			echo '</ul>';

		} else {
			echo '<p>' . esc_html__( 'No users found with authoring capabilities.', 'rtc-wp-assignment' ) . '</p>';
		}

		echo '</div>';
		echo '<p class="description">' . esc_html__( 'Select users who contributed to this post.', 'rtc-wp-assignment' ) . '</p>';
	}

	/**
	 * Save the contributor data.
	 *
	 * @param int     $post_id The post ID.
	 * @param WP_Post $post The post object.
	 */
	public function save_contributor_data( $post_id, $post ) {

		if ( ! isset( $_POST[ self::CONTRIBUTORS_NONCE ] ) || ! wp_verify_nonce( $_POST[ self::CONTRIBUTORS_NONCE ], self::CONTRIBUTORS_NONCE_ACTION ) ) { // phpcs:ignore
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$submitted_ids = isset( $_POST['rtc_contributors'] ) ? (array) $_POST['rtc_contributors'] : array(); // phpcs:ignore
		$sanitized_ids = array_map( 'intval', $submitted_ids );
		$sanitized_ids = array_filter( $sanitized_ids );

		if ( ! empty( $sanitized_ids ) ) {
			update_post_meta( $post_id, self::CONTRIBUTORS_META_KEY, $sanitized_ids );

			foreach ( $sanitized_ids as $idd ) {
				$cache_key = 'cache_contributors_for_author_' . $idd;
				wp_cache_delete( $cache_key, 'cached_contributors' );
			}
		} else {
			delete_post_meta( $post_id, self::CONTRIBUTORS_META_KEY );
		}
	}
}
