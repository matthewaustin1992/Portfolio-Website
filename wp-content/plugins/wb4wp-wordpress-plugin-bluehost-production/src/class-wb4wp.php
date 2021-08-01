<?php

namespace Wb4Wp;

use Wb4Wp\Helpers\Environment_Helper;
use Wb4Wp\Managers\Admin_Manager;
use Wb4Wp\Managers\Api_Manager;
use Wb4Wp\Managers\Blog_Manager;
use Wb4Wp\Managers\Contacts_Manager;
use Wb4Wp\Managers\Description_Manager;
use Wb4Wp\Managers\Instance_Manager;
use Wb4Wp\Managers\Migration_Manager;
use Wb4Wp\Managers\Provider_Bluehost_Manager;
use Wb4Wp\Managers\Provider_Wb4wp_Manager;
use Wb4Wp\Managers\Provision_Manager;
use Wb4Wp\Managers\Stats_Manager;
use Wb4Wp\Managers\Template_Manager;
use Wb4Wp\Managers\Theme_Manager;
use Wb4Wp\Managers\Update_Manager;
use Wb4Wp\Managers\Woo_Commerce_Manager;
use Wb4Wp\Managers\WordPress_Manager;
use Wb4Wp\Helpers\WP_Post_Revisioning_Helper;

/**
 * Class Wb4Wp
 *
 * @package Wb4Wp
 */
final class Wb4wp {

	private static $instance;

	private $admin_manager;
	private $api_manager;
	private $description_manager;
	private $template_manager;
	private $bluehost_manager;
	private $wb4wp_manager;
	private $word_press_manager;
	private $environment_helper;
	private $instance_manager;
	private $stats_manager;
	private $provision_manager;
	private $update_manager;
	private $migration_manager;
	private $blog_manager;
	private $contacts_manager;
	private $woo_commerce_manager;
	private $wp_post_revisioning_helper;

	public function __construct() {
		if ( current_user_can( 'administrator' ) ) {
			$this->admin_manager = new Admin_Manager();
			$this->admin_manager->add_hooks();
		}

		$this->api_manager          		= new Api_Manager();
		$this->description_manager  		= new Description_Manager();
		$this->template_manager     		= new Template_Manager();
		$this->bluehost_manager     		= new Provider_Bluehost_Manager();
		$this->wb4wp_manager        		= new Provider_Wb4wp_Manager();
		$this->word_press_manager   		= new WordPress_Manager();
		$this->environment_helper   		= new Environment_Helper();
		$this->instance_manager     		= new Instance_Manager();
		$this->provision_manager    		= new Provision_Manager();
		$this->stats_manager        		= new Stats_Manager();
		$this->update_manager       		= new Update_Manager();
		$this->migration_manager    		= new Migration_Manager();
		$this->blog_manager         		= new Blog_Manager();
		$this->contacts_manager     		= new Contacts_Manager();
		$this->woo_commerce_manager 		= new Woo_Commerce_Manager();
		$this->wp_post_revisioning_helper 	= new WP_Post_Revisioning_Helper();
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Wb4wp();
		}

		return self::$instance;
	}

	public function add_hooks() {
		if ( null !== $this->admin_manager ) {
			$this->admin_manager->add_hooks();
		}

		$this->word_press_manager->add_hooks();
		$this->api_manager->add_hooks();
		$this->template_manager->add_hooks();
		$this->description_manager->add_hooks();
		$this->update_manager->add_hooks();

		Theme_Manager::add_hooks();
	}

	public function get_provision_manager() {
		return $this->provision_manager;
	}

	public function get_stats_manager() {
		return $this->stats_manager;
	}

	public function get_instance_manager() {
		return $this->instance_manager;
	}

	public function get_api_manager() {
		return $this->api_manager;
	}

	public function get_wb4wp_manager() {
		return $this->wb4wp_manager;
	}

	public function get_admin_manager() {
		return $this->admin_manager;
	}

	public function get_description_manager() {
		return $this->description_manager;
	}

	public function get_template_manager() {
		return $this->template_manager;
	}

	public function get_bluehost_manager() {
		return $this->bluehost_manager;
	}

	public function get_word_press_manager() {
		return $this->word_press_manager;
	}

	public function get_update_manager() {
		return $this->update_manager;
	}

	public function get_blog_manager() {
		return $this->blog_manager;
	}

	public function get_contacts_manager() {
		return $this->contacts_manager;
	}

	public function get_woo_commerce_manager() {
		return $this->woo_commerce_manager;
	}
}
