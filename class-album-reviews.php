<?php

class Album_Reviews {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '2.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'album-reviews';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {
		// all the actions go here

		add_action( 'init', array( $this, 'post_type_reviews' ), 0 );

		add_action( 'init', array( $this, 'review_taxonomies' ), 0 ); // taxonomy for genre

		add_action( 'save_post', array( $this, 'reviews_save_product_postdata' ), 1, 2); // save the custom fields
		//add_action( 'admin_head', array( $this, 'reviews_icon' ) );
		//add_action( 'admin_head', array( $this, 'reviews_header' ) );
		add_action( 'admin_menu', array( $this, 'custom_meta_boxes_reviews' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles') );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/* create the custom post type */
	public function post_type_reviews() {
	    $labels = array(
			'name' => _x('Album Reviews', 'post type general name', 'plague-reviews'),
			'singular_name' => _x('Review', 'post type singular name', 'plague-reviews'),
			'add_new' => __('Add New', 'plague-reviews'),
			'add_new_item' => __('Add New Album Review', 'plague-reviews'),
			'edit_item' => __('Edit Review', 'plague-reviews'),
			'edit' => __('Edit', 'plague-reviews'),
			'new_item' => __('New Album Review', 'plague-reviews'),
			'view_item' => __('View Album Review', 'plague-reviews'),
			'search_items' => __('Search Album Reviews', 'plague-reviews'),
			'not_found' =>  __('No reviews found', 'plague-reviews'),
			'not_found_in_trash' => __('No reviews found in Trash', 'plague-reviews'),
			'view' =>  __('View Album Review', 'plague-reviews'),
			'parent_item_colon' => ''
	  );
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array("slug" => "review/%artist%"),
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title','editor','thumbnail' ),
			'exclude_from_search' => false
	  );

	  register_post_type( 'album-review', $args );
	}

	public function review_taxonomies() {

		$genre_labels = array(
			'name' => __( 'Genres', 'plague-reviews' ),
			'singular_name' => __( 'Genre', 'plague-reviews' ),
			'search_items' => __( 'Search Genres', 'plague-reviews' ),
			'all_items' => __( 'All Genres', 'plague-reviews' ),
			'parent_item' => __( 'Parent Genre', 'plague-reviews' ),
			'parent_item_colon' => __( 'Parent Genre:', 'plague-reviews' ),
			'edit_item' => __( 'Edit Genre', 'plague-reviews' ),
			'update_item' => __( 'Update Genre', 'plague-reviews' ),
			'add_new_item' => __( 'Add New Genre', 'plague-reviews' ),
			'new_item_name' => __( 'New Genre Name', 'plague-reviews' ),
		);
		$label_uh_labels = array(
			'name' => __( 'Labels', 'plague-reviews' ),
			'singular_name' => __( 'Label', 'plague-reviews' ),
			'search_items' => __( 'Search Labels', 'plague-reviews' ),
			'all_items' => __( 'All Labels', 'plague-reviews' ),
			'parent_item' => __( 'Parent Label', 'plague-reviews' ),
			'parent_item_colon' => __( 'Parent Label:', 'plague-reviews' ),
			'edit_item' => __( 'Edit Label', 'plague-reviews' ),
			'update_item' => __( 'Update Label', 'plague-reviews' ),
			'add_new_item' => __( 'Add New Label', 'plague-reviews' ),
			'new_item_name' => __( 'New Label Name', 'plague-reviews' ),
		);
		$artist_labels = array(
			'name' => __( 'Artists', 'plague-reviews' ),
			'singular_name' => __( 'Artists', 'plague-reviews' ),
			'search_items' => __( 'Search Artists', 'plague-reviews' ),
			'all_items' => __( 'All Artists', 'plague-reviews' ),
			'edit_item' => __( 'Edit Artist', 'plague-reviews' ),
			'update_item' => __( 'Update Artist', 'plague-reviews' ),
			'add_new_item' => __( 'Add New Artist', 'plague-reviews' ),
			'new_item_name' => __( 'New Artist Name', 'plague-reviews' ),
		);
		register_taxonomy( 'genre', array('album-review','releases'), array( 'hierarchical' => true, 'labels' => $genre_labels, 'query_var' => 'genre', 'rewrite' => array( 'slug' => 'genre' ) ) ); // this is the genre taxonomy for album reviews
		register_taxonomy( 'label', 'album-review', array( 'hierarchical' => true, 'labels' => $label_uh_labels, 'query_var' => 'label', 'rewrite' => array( 'slug' => 'label' ) ) ); // this is the label taxonomy for album reviews
		register_taxonomy( 'artist', array('album-review', 'releases'), array( 'hierarchical' => true, 'labels' => $artist_labels, 'query_var' => 'artist', 'rewrite' => array( 'slug' => 'artist' ) ) ); // this is the artist taxonomy for album reviews
	}

	public function filter_post_type_link($link, $post) {
	    if ($post->post_type != 'album-review')
	        return $link;

	    if ($cats = get_the_terms($post->ID, 'artist'))
	        $link = str_replace('%artist%', array_pop($cats)->slug, $link);
	    return $link;
	}

	public function ratings() {
		$ratings = array(
			0 => array(
				'value' => 0,
				'label' => __( 'Zero stars', 'plague-reviews' ),
				'html' => ''
			),
			1 => array(
				'value' => 1,
				'label' => __( 'One star', 'plague-reviews' ),
				'html' => '<i class="icon-star"></i>'
			),
			2 => array(
				'value' => 2,
				'label' => __( 'Two stars', 'plague-reviews' ),
				'html' => '<i class="icon-star"></i><i class="icon-star"></i>'
			),
			3 => array(
				'value' => 3,
				'label' => __( 'Three stars', 'plague-reviews' ),
				'html' => '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i>'
			),
			4 => array(
				'value' => 4,
				'label' => __( 'Four stars', 'plague-reviews' ),
				'html' => '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i>'
			),
			5 => array(
				'value' => 5,
				'label' => __( 'Five stars', 'plague-reviews' ),
				'html' => '<i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i><i class="icon-star"></i>'
			)
		);
		return $ratings;
	}

	/* create custom meta boxes */

	public function custom_meta_boxes_reviews() {
	    add_meta_box("reviews-details", "Album Details", array( $this, "meta_cpt_reviews"), "album-review", "normal", "low");
	}

	public function meta_cpt_reviews() {
	    global $post;

		echo '<input type="hidden" name="reviews_noncename" id="reviews_noncename" value="' .
		wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

		echo '<p><label for="review_rating">' . __( 'Album Rating', 'plague-reviews' ) . '</label><br />';
		echo '<select name="review_rating">';
		$selected = get_post_meta( $post->ID, 'review_rating', true );
		foreach ( $this->ratings() as $rating ) {
			$value = $rating['value'];
			$label = $rating['label'];
			echo '<option value="' . $value . '" ' . selected( $value, $selected ) . '>' . esc_attr( $label ) . '</option>';
		}
		echo '</select>';

		echo '<p><label for="url_to_buy">' . __( 'URL to purchase album', 'plague-reviews' ) . '</label><br />';
		echo '<input class="widefat" type="text" name="url_to_buy" value="'.mysql_real_escape_string(get_post_meta($post->ID, 'url_to_buy', true)).'" /></p>';

		echo '<p><label for="tracklist">Track List</label><br />';
		echo wpautop(wp_kses_post(wp_editor( get_post_meta($post->ID, 'tracklist', true), 'tracklist', array('media_buttons' => false, 'teeny' => true, 'quicktags' => false, 'textarea_rows' => 5 ) ), array())).'</p>';

		$kses_allowed = array_merge(wp_kses_allowed_html( 'post' ), array(
			'iframe' => array(
				'src' => array(),
				'style' => array(),
				'width' => array(),
				'height' => array(),
				'scrolling' => array(),
				'frameborder' => array()
			)));
		echo '<p><label for="embed_code">Player Embed Code</label><br />';
		echo '<textarea class="embed_code widefat" rows="5" name="embed_code" />'.wp_kses(get_post_meta($post->ID, 'embed_code', true), $kses_allowed).'</textarea></p>';
	}

	/* When the post is saved, saves our product data */
	public function reviews_save_product_postdata($post_id, $post) {
		$nonce = isset( $_POST['reviews_noncename'] ) ? $_POST['reviews_noncename'] : 'all the pigs, all lined up';
		if ( !wp_verify_nonce( $nonce, plugin_basename(__FILE__) )) {
			return $post->ID;
		}

		/* confirm user is allowed to save page/post */
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post->ID ))
			return $post->ID;
		} else {
			if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;
		}

		/* ready our data for storage */
		$meta_keys = array('tracklist' => 'text', 'embed_code' => 'embed','url_to_buy' => 'text', 'review_rating' => 'numeric');

		/* Add values of $mydata as custom fields */
		foreach ($meta_keys as $meta_key => $type) {
			if( $post->post_type == 'revision' )
				return;
			if ( isset( $_POST[ $meta_key ] ) ) {
				if ( $type == 'text' ) {
					$value = wp_kses_post( $_POST[ $meta_key ] );
				}
				if ( $type == 'embed' ) {
					$kses_allowed = array_merge(wp_kses_allowed_html( 'post' ), array('iframe' => array(
						'src' => array(),
						'style' => array(),
						'width' => array(),
						'height' => array(),
						'scrolling' => array(),
						'frameborder' => array()
						)));
					$value = wp_kses( $_POST[ $meta_key ], $kses_allowed );
				}
				if ( $type == 'numeric' ) {
					if ( is_numeric( $_POST[ $meta_key ] ) ) {
						$value = wp_kses( $_POST[ $meta_key ], array() );
					}
				}

				update_post_meta( $post->ID, $meta_key, $value );
			} else {
				delete_post_meta( $post->ID, $meta_key );
			}
		}
	}

	public function admin_styles() {
		wp_enqueue_style( 'plague-fonts', plugins_url( 'css/plague-fonts.css', __FILE__ ), array(), $this->version );
		wp_enqueue_style( 'reviews-admin-css', plugins_url( 'css/reviews-admin.css', __FILE__ ), array(), $this->version );
	}
}