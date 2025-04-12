<?php
/**
 * Class ContributorsTest
 *
 * @created 2025-04-12
 * @author atiqsu <atiqur.su@gmail.com>
 * @package Rtc_Wp_Starter
 */

/**
 * Sample test case.
 */
class ContributorsTest extends WP_UnitTestCase {


	/**
	 * Unit test setup.
	 */
	public function setUp(): void {
		parent::setUp();

		require_once plugin_dir_path( __FILE__ ) . '../class-metabox.php';
		require_once plugin_dir_path( __FILE__ ) . '../class-contributor.php';
		require_once plugin_dir_path( __FILE__ ) . './class-testablecontributor.php';
	}


	/**
	 * Test for checking if contributors are properly saving.
	 */
	public function test_metabox_contributors_saved() {
		$user1 = $this->factory()->user->create_and_get( array( 'role' => 'author' ) );
		$user2 = $this->factory()->user->create_and_get( array( 'role' => 'author' ) );

		$post_id = $this->factory()->post->create(
			array(
				'post_title'   => 'Multi Author Post',
				'post_content' => 'Content for multi author test',
				'post_status'  => 'publish',
			)
		);

		// simulate saving contributor metadata.
		update_post_meta( $post_id, '_rtc_contributors', array( $user1->ID, $user2->ID ) );

		$contributors = get_post_meta( $post_id, '_rtc_contributors', true );

		$this->assertIsArray( $contributors );
		$this->assertContains( $user1->ID, $contributors );
		$this->assertContains( $user2->ID, $contributors );
	}

	/**
	 * Testing if contributors are displaying or not.
	 */
	public function test_contributors_output_contains_usernames() {
		$user1   = $this->factory()->user->create_and_get( array( 'role' => 'author' ) );
		$post_id = $this->factory()->post->create(
			array(
				'post_author'  => $user1->ID,
				'post_status'  => 'publish',
				'post_content' => 'Content for multi author test',
			)
		);

		update_post_meta( $post_id, '_rtc_contributors', array( $user1->ID ) );

		$this->go_to( get_permalink( $post_id ) );

		global $wp_query;
		$wp_query->is_single   = true;
		$wp_query->is_singular = true;

		// Set up global post and post data.
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );

		$contributor = new TestableContributor();
		add_filter( 'the_content', array( $contributor, 'display_contributor_list' ) );

		// Now apply the filter.
		$content = apply_filters( 'the_content', $post->post_content );

		// Run assertions.
		$this->assertStringContainsString( 'Contributors', $content );
		$this->assertStringContainsString( $user1->display_name, $content );

		wp_reset_postdata();
	}

	/**
	 * Unit tests finished and tearing apart :P.
	 */
	public function tearDown(): void {
		global $post;
		$post = null;
		wp_reset_postdata();

		parent::tearDown();
	}
}
