<?php
class GIAO_CRAWL {

	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
	public static function init_hooks(){
		self::$initiated = true;
		// self::crawl_data_post();
		add_filter( 'cron_schedules', array('GIAO_CRAWL', 'cron_interval_crawl') );
		add_action( 'bl_cron_hook', array('GIAO_CRAWL','crawl_data_post') );
		if ( ! wp_next_scheduled( 'bl_cron_hook' ) ) {
		    wp_schedule_event( time(), 'five_seconds', 'bl_cron_hook' );
		}
	}
	public static function plugin_activation(){
		if ( version_compare( $GLOBALS['wp_version'], GIAO_CRAWL__MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'giao' );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
		}
	}
	public static function plugin_deactivation(){
		
	}
	public static function cron_interval_crawl( $schedules ) { 
	    $schedules['five_seconds'] = array(
	        'interval' => 10,
	        'display'  => esc_html__( 'Every Five Seconds' ), );
	    return $schedules;
	}
	public static function crawl_data_post(){
		if ( ! function_exists( 'post_exists' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/post.php' );
		}
		require GIAO_CRAWL__PLUGIN_DIR . "library/simple_html_dom.php";
		$args = array( 'post_type' => 'giao_crawl');
		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) : $loop->the_post();
			$data = get_post_meta( get_the_id(), '_crawl_data_option', true );
			$data = unserialize($data);

			if($data != ""){
				if(isset($data['domain'])){
					$html = file_get_html($data['domain']);
					$body = $html->find('body');
					foreach($body as $article) {
					    $url = $article->find($data['url']);

					    foreach ($url as $value) {
					    	$url_post = $value->href;

					    	// $items[] = $url_post;

			    			$html2 = file_get_html($url_post);
			    			$body2 = $html2->find('body');
			    			foreach($body2 as $article2) {
			    				$item2['title'] = $article2->find($data['title'], 0)->plaintext;
			    				$content = $article2->find($data['content'], 0);
			    				$item2['category'] = $data['category'];
			    				if($data['save_img'] == 1){
			    					$item2['image'] = $content->find('img', 0)->getAttribute('data-src');
			    				} else {
			    					$item2['image'] = $data['image'];
			    				}
			    				$item2['content'] = self::custom_content($content, $data['attr']);
			    				self::insert_post($item2);

			    				echo "<pre>";
			    				print_r($item2);
			    				echo "</pre>";die;
			    			}
					    }

					}
					// $html2->clear();
				}
			}
		endwhile;

	}

		public static function insert_post($data){
			// $img_id = self::save_image($data['image']);
	    	$my_post = array(
	    	  'post_title'    => $data['title'],
	    	  'post_content'  => $data['content'],
	    	  'post_type' => 'post',
	    	  'post_status'   => 'publish',
	    	  'post_author'   => 1,
	    	  'post_category' => array( $data['category'] )
	    	);
		    if (!post_exists($data['title'])) {
		   		$post_id = wp_insert_post( $my_post );

				include_once( ABSPATH . 'wp-admin/includes/image.php' );
		   		$imageurl = $data['image'];
		   		$imagetype = end(explode('/', getimagesize($imageurl)['mime']));
		   		$uniq_name = date('dmY').''.(int) microtime(true); 
		   		$filename = $uniq_name.'.'.$imagetype;

		   		$uploaddir = wp_upload_dir();
		   		$uploadfile = $uploaddir['path'] . '/' . $filename;
		   		$contents= file_get_contents($imageurl);
		   		$savefile = fopen($uploadfile, 'w');
		   		fwrite($savefile, $contents);
		   		fclose($savefile);

		   		$wp_filetype = wp_check_filetype(basename($filename), null );
		   		$attachment = array(
		   		    'post_mime_type' => $wp_filetype['type'],
		   		    'post_title' => $filename,
		   		    'post_content' => '',
		   		    'post_status' => 'inherit'
		   		);

		   		$attach_id = wp_insert_attachment( $attachment, $uploadfile );
		   		$imagenew = get_post( $attach_id );
		   		$fullsizepath = get_attached_file( $imagenew->ID );
		   		$attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
		   		wp_update_attachment_metadata( $attach_id, $attach_data ); 
		   		set_post_thumbnail($post_id, $attach_id);
		   	}
		}
		public static function custom_content($content, $attr = ""){
			if ($content && $attr != "") {
				foreach ($content->find('img') as $img) {
					$content = str_replace($img->src, $img->getAttribute('data-src'), $content);
				}
			}
			$content = preg_replace('#<a.*?>([^>]*)</a>#i', '$1', $content); 
			return $content;

		}

}
?>