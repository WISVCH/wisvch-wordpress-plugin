<?php
namespace WISVCH\CPT\HonoraryMember;


/**
 * Register post type.
 *
 * @package CPT\Board;
 */
class Registration {

	public $post_type = 'honorary_member';

	public function init() {
		// Add the team post type
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Initiate registration of post type.
	 */
	public function register() {
		$this->register_post_type();
	}

	/**
	 * Register the custom post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	protected function register_post_type() {
		$labels = array(
			'name'               => __( 'Honorary Members', 'wisvch-plugin' ),
			'singular_name'      => __( 'Honorary Member', 'wisvch-plugin' ),
			'add_new'            => __( 'Add Honorary Member', 'wisvch-plugin' ),
			'add_new_item'       => __( 'Add New Honorary Member', 'wisvch-plugin' ),
			'edit_item'          => __( 'Edit Honorary Member', 'wisvch-plugin' ),
			'new_item'           => __( 'New Honorary Member', 'wisvch-plugin' ),
			'view_item'          => __( 'View Honorary Member', 'wisvch-plugin' ),
			'search_items'       => __( 'Search Honorary Members', 'wisvch-plugin' ),
			'not_found'          => __( 'No Honorary Members found', 'wisvch-plugin' ),
			'not_found_in_trash' => __( 'No Honorary Members in the trash', 'wisvch-plugin' ),
		);

		$supports = array(
			'title',
			'editor',
			'thumbnail',
			'custom-fields',
			'revisions',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'public'          => true,
			'has_archive'     => true,
			'capability_type' => 'page',
			'rewrite'         => array(
				'slug'            => 'association/honorary-members',
				'with_front'      => false,
				'feeds'           => null,
				'pages'           => false
			),
			'menu_position'   => 40,
			'menu_icon'       => 'dashicons-welcome-learn-more',
		);

		$args = apply_filters( 'honorary_member_post_type_args', $args );

		register_post_type( $this->post_type, $args );
	}

}