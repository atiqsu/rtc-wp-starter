<?php
/**
 * File for handling the author archives in author page .
 *
 * @created 2025-04-12
 * @author atiqsu <atiqur.su@gmail.com>
 * @package rtCamp
 */

namespace rtCamp;

use rtCamp\Metabox;

/**
 * Contributor class
 */
class Author_Archive {

	/**
	 * Initializes the actual actions
	 */
	public function init() {

		add_action( 'pre_get_posts', array( $this, 'modify_author_archive_query' ) );
	}

	/**
	 * Show all the posts for the author listed in contributors.
	 *
	 * @param string $query The query that fetches the posts for author.
	 */
	public function modify_author_archive_query( $query ) {

		if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {

			return;
		}

		$author_id = get_queried_object_id();

		$all_post_ids = $this->get_all_post_ids_from_pm( $author_id );

		$query->set( 'author', '' );
		$query->set( 'author_name', '' );

		$query->set( 'post__in', $all_post_ids );
	}

	/**
	 * Get all the posts for the author.
	 *
	 * @param int $author_id author id to fetch the posts for.
	 */
	public function get_all_post_ids_from_pm( $author_id ) {

		global $wpdb;

		$author_id = (int) $author_id;

		$cache_key        = 'cache_contributors_for_author_' . $author_id;
		$matched_post_ids = wp_cache_get( $cache_key, 'cached_contributors' );

		if ( false === $matched_post_ids ) {
			$like = '%' . $author_id . '%';

			$meta_rows = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					'SELECT post_id, meta_value FROM ' . $wpdb->postmeta . ' WHERE meta_key = %s AND meta_value LIKE %s',
					Metabox::CONTRIBUTORS_META_KEY,
					$like
				)
			);

			$matched_post_ids = array();

			foreach ( $meta_rows as $row ) {
				$contributors = maybe_unserialize( $row->meta_value );
				if ( is_array( $contributors ) && in_array( $author_id, $contributors ) ) { // phpcs:ignore
					$matched_post_ids[] = (int) $row->post_id;
				}
			}

			wp_cache_set( $cache_key, $matched_post_ids, 'cached_contributors', 60 );
		}

		$authored_post_ids = get_posts(
			array(
				'fields'         => 'ids',
				'author'         => $author_id,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		);

		$all_post_ids = array_unique( array_merge( $authored_post_ids, $matched_post_ids ) );

		if ( empty( $all_post_ids ) ) {
			$all_post_ids = array( 0 );
		}

		return $all_post_ids;
	}
}
