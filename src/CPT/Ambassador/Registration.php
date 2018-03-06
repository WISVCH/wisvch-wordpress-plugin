<?php
namespace WISVCH\CPT\Ambassador;


/**
 * Register post type.
 *
 * @package CPT\Board;
 */
class Registration {

	public $post_type = 'ambassador';

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
			'name'               => __( 'Ambassadors', 'wisvch-plugin' ),
			'singular_name'      => __( 'Ambassador', 'wisvch-plugin' ),
			'add_new'            => __( 'Add Ambassador', 'wisvch-plugin' ),
			'add_new_item'       => __( 'Add New Ambassador', 'wisvch-plugin' ),
			'edit_item'          => __( 'Edit Ambassador', 'wisvch-plugin' ),
			'new_item'           => __( 'New Ambassador', 'wisvch-plugin' ),
			'view_item'          => __( 'View Ambassador', 'wisvch-plugin' ),
			'search_items'       => __( 'Search Ambassadors', 'wisvch-plugin' ),
			'not_found'          => __( 'No Ambassadors found', 'wisvch-plugin' ),
			'not_found_in_trash' => __( 'No Ambassadors in the trash', 'wisvch-plugin' ),
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
				'slug'            => 'association/ambassadors',
				'with_front'      => false,
				'feeds'           => null,
				'pages'           => false
			),
			'menu_position'   => 40,
			'menu_icon'       => 'dashicons-megaphone',
		);

		$args = apply_filters( 'ambassador_post_type_args', $args );

		register_post_type( $this->post_type, $args );
	}

}