<?php

use WebberZone\Contextual_Related_Posts\Frontend\Media_Handler;

class Test_CRP_Media_Security extends WP_UnitTestCase {

	public function test_locked_content_and_attachment_images_are_not_exposed() {
		$id         = self::factory()->post->create(
			array(
				'post_status'   => 'publish',
				'post_password' => 'secret',
				'post_content'  => '<img src="https://example.org/private-body-image.jpg" />',
			)
		);
		$attachment = self::factory()->post->create(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_parent'    => $id,
				'post_mime_type' => 'image/jpeg',
			)
		);
		update_post_meta( $attachment, '_wp_attached_file', 'private-attached-image.jpg' );
		wp_set_current_user( 0 );
		foreach ( array( true, false ) as $scan ) {
			$html = Media_Handler::get_the_post_thumbnail(
				array(
					'post'        => $id,
					'scan_images' => $scan,
				)
			);
			$this->assertStringNotContainsString( 'private-body-image', $html );
			$this->assertStringNotContainsString( 'private-attached-image', $html );
		}
		wp_update_post(
			array(
				'ID'            => $id,
				'post_password' => '',
			)
		);
		$this->assertStringContainsString( 'private-body-image', Media_Handler::get_the_post_thumbnail( array( 'post' => $id ) ) );
	}

	public function test_array_dimensions_are_validated_before_size_selection() {
		$this->assertSame( Media_Handler::get_thumb_size( array( 150, 90 ) ), Media_Handler::get_thumb_size( array( '150" onerror="alert(1)', '90" onfocus="alert(1)' ) ) );
	}
	public function test_image_source_rejects_unsafe_protocols() {
		$this->assertStringNotContainsString( 'javascript:', Media_Handler::get_image_html( 'javascript:alert(1)' ) );
		$this->assertStringContainsString( 'https://example.org/safe.jpg', Media_Handler::get_image_html( 'https://example.org/safe.jpg' ) );
	}
}
