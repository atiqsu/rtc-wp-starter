<?php
/**
 * File for handling the contributor list display in post.
 *
 * @created 2025-04-09
 * @author atiqsu <atiqur.su@gmail.com>
 * @package rtCamp
 */

namespace rtCamp;

use rtCamp\Metabox;

/**
 * Contributor class
 */
class Contributor {

	/**
	 * Initialize the contributor class
	 */
	public function init() {
		add_filter( 'the_content', array( $this, 'display_contributor_list' ) );
	}

	/**
	 * Add the contributor list to the post content
	 *
	 * @param string $content The post content.
	 * @return string The post content with the contributor list
	 */
	public function display_contributor_list( $content ) {

		if ( ! is_single() || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$post_id         = get_the_ID();
		$contributor_ids = self::get_contributors( $post_id );

		if ( empty( $contributor_ids ) ) {
			return $content;
		}

		$htm  = '<div class="rtc-contributors-box">';
		$htm .= '<h3>' . esc_html__( 'Contributors', 'rtc-wp-assignment' ) . '</h3>';
		$htm .= '<ul>';

		foreach ( $contributor_ids as $user_id ) {
			$user_data = get_userdata( $user_id );

			if ( $user_data ) {
				$author_link  = get_author_posts_url( $user_id );
				$avatar       = get_avatar( $user_id, 48 ); // 48px size avatar
				$display_name = esc_html( $user_data->display_name );

				$htm .= '<li>';
				$htm .= '<div class="contributor-avatar">' . $avatar . '</div>';
				$htm .= '<div class="contributor-info">';
				$htm .= '<a href="' . esc_url( $author_link ) . '" class="contributor-name">' . $display_name . '</a>';
				$htm .= '</div>';
				$htm .= '</li>';
			}
		}

		$htm .= '</ul>';
		$htm .= '</div>';

		$content .= $htm;

		return $content;
	}

	/**
	 * Get the contributors for a post
	 *
	 * @param int $post_id The post ID.
	 * @return array The contributor IDs.
	 */
	public static function get_contributors( $post_id ) {

		$contributors = get_post_meta( $post_id, Metabox::CONTRIBUTORS_META_KEY, true );

		return is_array( $contributors ) ? array_map( 'intval', $contributors ) : array();
	}
}
