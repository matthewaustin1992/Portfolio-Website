<?php

namespace Wb4Wp\Models;

/**
 * Class Attachment
 * @package Wb4Wp\Models
 */
class Attachment {

	public $id;
	public $date;
	public $name;
	public $modified;
	public $url;
	public $thumbnail;

	public function __construct( $wp_attachment ) {
		$this->id        = $wp_attachment->ID;
		$this->name      = $wp_attachment->post_title;
		$this->date      = $wp_attachment->post_date;
		$this->modified  = $wp_attachment->post_modified;
		$this->url       = wp_get_attachment_url( $wp_attachment->ID );
		$this->thumbnail = wp_get_attachment_image_url( $wp_attachment->ID, 'medium_large' );
	}
}
