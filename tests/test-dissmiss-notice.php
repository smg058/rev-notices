<?php
/**
 * DissMiss Notice Testcase
 *
 * @package REV_Notices
 */

/**
 * Test to verify the notice can be dismissed by the users.
 */
class TestDismissNotice extends WP_Ajax_UnitTestCase {

	private $editor_user_id;

	/**
	 * Test that the notice is correctly dismissed by the user..
	 */
	public function test_user_can_dismiss_notice() {
		$this->editor_user_id = self::factory()->user->create(
			array(
				'role' => 'editor',
			)
		);

		REV_Notices::add_notice(
			array(
				'id'         => 'rev-sites-5-start-notice',
				'type'       => 'info',
				'class'      => 'rev-sites-5-star',
				'capability' => 'edit_posts',
				'show_if'    => true,
				'message'    => 'Sample Notice',
			)
		);

		wp_set_current_user( $this->editor_user_id );

		$_POST = array(
			'nonce'     => wp_create_nonce( 'rev-notices' ),
			'notice_id' => 'rev-sites-5-start-notice',
		);

		try {
			$this->_handleAjax( 'rev-notice-dismiss' );
		} catch ( WPAjaxDieContinueException $e ) {
			unset( $e );
		}

		$response = json_decode( $this->_last_response, true );

		$this->assertContains( $response, array( 'success' => true ) );

		$user_meta_status = get_user_meta( $this->editor_user_id, 'rev-sites-5-start-notice', true );
		$this->assertSame( $user_meta_status, 'notice-dismissed' );
	}
}
