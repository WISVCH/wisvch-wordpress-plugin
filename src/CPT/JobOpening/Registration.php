<?php
namespace WISVCH\CPT\JobOpening;


/**
 * Register post types and taxonomies.
 *
 * @package WISVCH\JobOpening;
 */
class Registration {

	public $post_type = 'job_opening';

	public $taxonomies = array( 'job-type', 'job-study' );

	public function init() {
		// Add the team post type and taxonomies
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Initiate registrations of post type and taxonomies.
	 *
	 * @uses Team_Post_Type_Registrations::register_post_type()
	 * @uses Team_Post_Type_Registrations::register_taxonomy_category()
	 */
	public function register() {
		$this->register_post_type();
		$this->register_taxonomy_category();
		$this->register_taxonomy_study();
	}

	/**
	 * Register the custom post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	protected function register_post_type() {
		$labels = array(
			'name'               => __( 'Job Openings', 'team-post-type' ),
			'singular_name'      => __( 'Job Opening', 'team-post-type' ),
			'add_new'            => __( 'Add Job Opening', 'team-post-type' ),
			'add_new_item'       => __( 'Add Job Opening', 'team-post-type' ),
			'edit_item'          => __( 'Edit Job Opening', 'team-post-type' ),
			'new_item'           => __( 'New Job Opening', 'team-post-type' ),
			'view_item'          => __( 'View Job Opening', 'team-post-type' ),
			'search_items'       => __( 'Search Job Opening', 'team-post-type' ),
			'not_found'          => __( 'No job openings found', 'team-post-type' ),
			'not_found_in_trash' => __( 'No job openings in the trash', 'team-post-type' ),
		);

		$supports = array(
			'title',
			'editor',
			'excerpt',
			'revisions',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'has_archive'     => true,
			'capability_type' => 'post',
			'rewrite'         => array(
				'slug'            => 'career/job-openings',
				'with_front'      => false,
				'feeds'           => null,
				'pages'           => false
			),
			'menu_position'   => 40,
			'menu_icon'       => 'dashicons-megaphone',
		);

		$args = apply_filters( 'job_opening_post_type_args', $args );

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Register Job Opening Categories.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	protected function register_taxonomy_category() {
		$labels = array(
			'name'                       => __( 'Job Opening Types', 'team-post-type' ),
			'singular_name'              => __( 'Job Opening Type', 'team-post-type' ),
			'menu_name'                  => __( 'Types', 'team-post-type' ),
			'edit_item'                  => __( 'Edit Job Opening Type', 'team-post-type' ),
			'update_item'                => __( 'Update Job Opening Type', 'team-post-type' ),
			'add_new_item'               => __( 'Add New Job Opening Type', 'team-post-type' ),
			'new_item_name'              => __( 'New Job Opening Type Name', 'team-post-type' ),
			'parent_item'                => __( 'Parent Job Opening Type', 'team-post-type' ),
			'parent_item_colon'          => __( 'Parent Job Opening Type:', 'team-post-type' ),
			'all_items'                  => __( 'All Types', 'team-post-type' ),
			'search_items'               => __( 'Search Job Opening Types', 'team-post-type' ),
			'popular_items'              => __( 'Popular Job Opening Types', 'team-post-type' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'team-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove job opening types', 'team-post-type' ),
			'choose_from_most_used'      => __( 'Choose from the most used types', 'team-post-type' ),
			'not_found'                  => __( 'No job opening types found.', 'team-post-type' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => '/career/job-type' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		$args = apply_filters( 'job_opening_post_type_category_args', $args );

		register_taxonomy( $this->taxonomies[0], $this->post_type, $args );

		// Also register for companies
		register_taxonomy_for_object_type( $this->taxonomies[0], "company");
	}

	/**
	 * Register Job Opening Study.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	protected function register_taxonomy_study() {
		$labels = array(
			'name'                       => __( 'Job Opening Studies', 'team-post-type' ),
			'singular_name'              => __( 'Job Opening Study', 'team-post-type' ),
			'menu_name'                  => __( 'Studies', 'team-post-type' ),
			'edit_item'                  => __( 'Edit Job Opening Study', 'team-post-type' ),
			'update_item'                => __( 'Update Job Opening Study', 'team-post-type' ),
			'add_new_item'               => __( 'Add New Job Opening Study', 'team-post-type' ),
			'new_item_name'              => __( 'New Job Opening Study Name', 'team-post-type' ),
			'parent_item'                => __( 'Parent Job Opening Study', 'team-post-type' ),
			'parent_item_colon'          => __( 'Parent Job Opening Study:', 'team-post-type' ),
			'all_items'                  => __( 'All Studies', 'team-post-type' ),
			'search_items'               => __( 'Search Job Opening Studies', 'team-post-type' ),
			'popular_items'              => __( 'Popular Job Opening Studies', 'team-post-type' ),
			'separate_items_with_commas' => __( 'Separate studies with commas', 'team-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove job opening studies', 'team-post-type' ),
			'choose_from_most_used'      => __( 'Choose from the most used studies', 'team-post-type' ),
			'not_found'                  => __( 'No job opening studies found.', 'team-post-type' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'career/study', 'with_front' => false ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		$args = apply_filters( 'job_opening_post_type_study_args', $args );

		register_taxonomy( $this->taxonomies[1], $this->post_type, $args );

		// Also register for companies
		register_taxonomy_for_object_type( $this->taxonomies[1], "company");
	}
}