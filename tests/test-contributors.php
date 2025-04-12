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


    public function setUp(): void {
        parent::setUp();
    }


	/**
	 * Test for checking if contributors are properly saving.
	 */
	public function test_metabox_contributors_saved() {
		$user1 = $this->factory->user->create_and_get( array( 'role' => 'author' ) );
		$user2 = $this->factory->user->create_and_get( array( 'role' => 'author' ) );

		$post_id = $this->factory->post->create(
			array(
				'post_title'  => 'Multi Author Post',
				'post_content'  => 'Content for multi author test',
				'post_status' => 'publish',
			)
		);

		// Simulate saving contributor metadata.
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
		$user1   = $this->factory->user->create_and_get( array( 'role' => 'author' ) );
		$post_id = $this->factory->post->create(
			array(
				'post_author' => $user1->ID,
				'post_status' => 'publish',
				'post_content'  => 'Content for multi author test',
			)
		);

		update_post_meta( $post_id, '_rtc_contributors', array( $user1->ID ) );


        global $post;
        $post_obj = get_post( $post_id );
        $post = $post_obj; 
        
        //Set up post data to simulate being "in the loop".
        setup_postdata($post);


        $this->go_to( get_permalink( $post_id ) );

        $content = apply_filters( 'the_content', $post->post_content );

        $this->assertStringContainsString( 'Contributors', $content );
        $this->assertStringContainsString( $user1->display_name, $content ); 

        wp_reset_postdata();

	}

    public function tearDown(): void {
        global $post;
        $post = null;
        wp_reset_postdata();
        
        parent::tearDown();
    }
}
