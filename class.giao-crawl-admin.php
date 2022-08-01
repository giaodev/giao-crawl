<?php
class GIAO_CRAWL_ADMIN {

	private static $initiated = false;
	private static $notices   = array();

	public function __construct() {
	}

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
	public static function init_hooks(){
		self::$initiated = true;
		self::admin_init();
		self::giao_crawl_post();
		// add_action('admin_menu', array('GIAO_CRAWL_ADMIN', 'load_menu'));
		add_action('admin_enqueue_scripts', array('GIAO_CRAWL_ADMIN', 'load_style'));
		add_action('admin_enqueue_scripts', array('GIAO_CRAWL_ADMIN', 'giao_crawl_post'));
		add_action( 'load-post.php', array( 'GIAO_CRAWL_ADMIN', 'init_metabox' ) );
		add_action( 'load-post-new.php', array( 'GIAO_CRAWL_ADMIN', 'init_metabox' ) );

	}

	// Register Custom Post Type
	public static function giao_crawl_post() {

		$labels = array(
			'name'                  => _x( 'Post Types', 'Post Type General Name', 'giao' ),
			'singular_name'         => _x( 'Giao Crawl', 'Post Type Singular Name', 'giao' ),
			'menu_name'             => __( 'Giao Crawl', 'giao' ),
			'name_admin_bar'        => __( 'Giao Crawl', 'giao' ),
			'archives'              => __( 'Item Archives', 'giao' ),
			'attributes'            => __( 'Item Attributes', 'giao' ),
			'parent_item_colon'     => __( 'Parent Item:', 'giao' ),
			'all_items'             => __( 'All Items', 'giao' ),
			'add_new_item'          => __( 'Add New Item', 'giao' ),
			'add_new'               => __( 'Add New', 'giao' ),
			'new_item'              => __( 'New Item', 'giao' ),
			'edit_item'             => __( 'Edit Item', 'giao' ),
			'update_item'           => __( 'Update Item', 'giao' ),
			'view_item'             => __( 'View Item', 'giao' ),
			'view_items'            => __( 'View Items', 'giao' ),
			'search_items'          => __( 'Search Item', 'giao' ),
			'not_found'             => __( 'Not found', 'giao' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'giao' ),
			'featured_image'        => __( 'Featured Image', 'giao' ),
			'set_featured_image'    => __( 'Set featured image', 'giao' ),
			'remove_featured_image' => __( 'Remove featured image', 'giao' ),
			'use_featured_image'    => __( 'Use as featured image', 'giao' ),
			'insert_into_item'      => __( 'Insert into item', 'giao' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'giao' ),
			'items_list'            => __( 'Items list', 'giao' ),
			'items_list_navigation' => __( 'Items list navigation', 'giao' ),
			'filter_items_list'     => __( 'Filter items list', 'giao' ),
		);
		$args = array(
			'label'                 => __( 'Giao Crawl', 'giao' ),
			'description'           => __( 'Giao Crawl', 'giao' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'giao_crawl', $args );

	}

	public static function load_style(){
		global $my_admin_page;
		$screen = get_current_screen();
		if (is_admin() && ($screen->id == 'toplevel_page_giao-crawl') ) {
			wp_enqueue_style( 'bs-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', false,'1.1','all');
			wp_enqueue_script( 'bs-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js', array ( 'jquery' ), 1.1, true);
		}
	}

	public static function admin_init() {

		load_plugin_textdomain( 'giao' );

	}

	public static function admin_head() {
		if ( !current_user_can( 'manage_options' ) )
			return;
	}

	public static function init_metabox() {
	    add_action( 'add_meta_boxes', array( 'GIAO_CRAWL_ADMIN', 'add_meta_box'  )        );
	    add_action( 'save_post',      array( 'GIAO_CRAWL_ADMIN', 'save_metabox' ), 10, 2 );
	}

	public static function add_meta_box( $post_type ) {
	    // Limit meta box to certain post types.
	    $post_types = array( 'giao_crawl');
	
	    if ( in_array( $post_type, $post_types ) ) {
	        add_meta_box(
	            'some_meta_box_name',
	            __( 'Cấu hình Crawl dữ liệu tại đây', 'textdomain' ),
	            array( 'GIAO_CRAWL_ADMIN', 'render_meta_box_content' ),
	            $post_type,
	            'advanced',
	            'high'
	        );
	    }
	}

	public static function save_metabox( $post_id ) {
	    /*
	     * We need to verify this came from the our screen and with proper authorization,
	     * because save_post can be triggered at other times.
	     */
	
	    // Check if our nonce is set.
	    if ( ! isset( $_POST['myplugin_inner_custom_box_nonce'] ) ) {
	        return $post_id;
	    }
	
	    $nonce = $_POST['myplugin_inner_custom_box_nonce'];
	
	    // Verify that the nonce is valid.
	    if ( ! wp_verify_nonce( $nonce, 'myplugin_inner_custom_box' ) ) {
	        return $post_id;
	    }
	
	    /*
	     * If this is an autosave, our form has not been submitted,
	     * so we don't want to do anything.
	     */
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
	        return $post_id;
	    }
	
	    // Check the user's permissions.
	    if ( 'giao_crawl' == $_POST['post_type'] ) {
	        if ( ! current_user_can( 'edit_page', $post_id ) ) {
	            return $post_id;
	        }
	    } else {
	        if ( ! current_user_can( 'edit_post', $post_id ) ) {
	            return $post_id;
	        }
	    }
	
	    /* OK, it's safe for us to save the data now. */
	
	    // Sanitize the user input.
	    $data = $_POST['option'];
	
	    // Update the meta field.
	    update_post_meta( $post_id, '_crawl_data_option', serialize($data) );

	    // echo "<pre>";
	    // print_r(serialize($data));
	    // echo "</pre>";die;
	}
	
	
	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public static function render_meta_box_content( $post ) {

	    // Add an nonce field so we can check for it later.
	    wp_nonce_field( 'myplugin_inner_custom_box', 'myplugin_inner_custom_box_nonce' );
	
	    // Use get_post_meta to retrieve an existing value from the database.
	    $data = get_post_meta( $post->ID, '_crawl_data_option', true );
		$data = unserialize($data);
		// echo "<pre>";
		// print_r($data);
		// echo "</pre>";
		$cate = "";
		$domain = "";
		$image = "";
		$title = "";
		$url = "";
		$content = "";
		$save_img = "";

		if (isset($data['domain'])) {
			$domain = $data['domain'];
		}
		if (isset($data['image'])) {
			$image = $data['image'];
		}
		if (isset($data['title'])) {
			$title = $data['title'];
		}
		if (isset($data['url'])) {
			$url = $data['url'];
		}
		if (isset($data['category'])) {
			$cate = $data['category'];
		}
		if (isset($data['content'])) {
			$content = $data['content'];
		}
		if (isset($data['attr'])) {
			$attr = $data['attr'];
		}
		if (isset($data['save_img'])) {
			$save_img = $data['save_img'];
		}

		require GIAO_CRAWL__PLUGIN_DIR . "views/giao-crawl-option.php";
	}
}
?>