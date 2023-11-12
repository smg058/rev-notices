<?php
/**
 * TestNoticeVisibleForCorrectRole Testcase
 *
 * @package REV_Notices
 */

/**
 * Tests to verify the notices are visible to correct users.
 */
class TestNoticeVisibleForCorrectRole extends WP_UnitTestCase {

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->editor_user_id = self::factory()->user->create(
			array(
				'role' => 'editor',
			)
		);

		$this->subscriber_user_id = self::factory()->user->create(
			array(
				'role' => 'subscriber',
			)
		);

		$this->administrator_user_id = self::factory()->user->create(
			array(
				'role' => 'administrator',
			)
		);
	}

	/**
	 * Test to verify the notice is visible to user's with given user level/capability.
	 */
	public function test_user_can_see_notice() {
		// Singleton is not used here as that causes the notices registered in one test to be visible in the next.
		$rev_notices = new REV_Notices();

		REV_Notices::add_notice(
			array(
				'id'         => 'notice-with-capability',
				'type'       => 'info',
				'class'      => 'rev-sites-5-star',
				'capability' => 'edit_posts',
				'show_if'    => true,
				'message'    => 'Notice With Capability',
			)
		);

		// Notice should be visible to the `editor` user.
		wp_set_current_user( $this->editor_user_id );
		$this->assertContains( 'Notice With Capability', get_echo( array( $rev_notices, 'show_notices' ) ) );

		// Notice should not be visible to the `subscriber` user.
		wp_set_current_user( $this->subscriber_user_id );
		$this->assertNotContains( 'Notice With Capability', get_echo( array( $rev_notices, 'show_notices' ) ) );

		// Notice should be visible to the `administrator`.
		wp_set_current_user( $this->administrator_user_id );
		$this->assertContains( 'Notice With Capability', get_echo( array( $rev_notices, 'show_notices' ) ) );
	}

	/**
	 * Test that if capability is not passed, the notice is visible only to the user's with `manage_options` cap.
	 */
	public function test_user_can_see_notice_without_capability() {
		// Singleton is not used here as that causes the notices registered in one test to be visible in the next.
		$rev_notices = new REV_Notices();

		REV_Notices::add_notice(
			array(
				'id'      => 'notice-without-explicite-capability',
				'type'    => 'info',
				'class'   => 'rev-sites-5-star',
				'show_if' => true,
				'message' => 'Notice Without Explicite Capibility',
			)
		);

		// Notice should not be visible to the `editor` user.
		wp_set_current_user( $this->editor_user_id );
		$this->assertNotContains( 'Notice Without Explicite Capibility', get_echo( array( $rev_notices, 'show_notices' ) ) );

		// Notice should not be visible to the `subscriber` user.
		wp_set_current_user( $this->subscriber_user_id );
		$this->assertNotContains( 'Notice Without Explicite Capibility', get_echo( array( $rev_notices, 'show_notices' ) ) );

		// Notice should be visible to the `administrator`.
		wp_set_current_user( $this->administrator_user_id );
		$this->assertContains( 'Notice Without Explicite Capibility', get_echo( array( $rev_notices, 'show_notices' ) ) );
	}
}
