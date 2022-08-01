<?php

  $categories = get_categories( array(
      'orderby' => 'name',
      'order'   => 'ASC',
      'hide_empty'      => false
  ) );
  ?>
    <p><label for="domain_field">
        <?php _e( 'Domain', 'giao' ); ?>
    </label></p>
    <p>
      <select name="option[category]">
        <?php
        foreach( $categories as $category ) {
        ?>
        <option value="<?php echo $category->term_id ?>" <?php echo ($cate == $category->term_id) ? "selected" : "" ?>><?php echo $category->name . " (" . $category->term_id . ")" ?></option>
        <?php
        } 
        ?>
      </select>

      <input type="text" id="domain_field" name="option[domain]" value="<?php echo esc_attr( $domain ); ?>" size="25" />
    </p>
    <p><label for="url_post_field">
        <?php _e( 'URL Post', 'giao' ); ?>
    </label></p>
    <p><input type="text" id="url_post_field" name="option[url]" value="<?php echo esc_attr( $url ); ?>" size="25" /></p>

    <p><label for="img_field">
        <?php _e( 'Image', 'giao' ); ?>
    </label></p>
    <p><input type="text" id="img_field" name="option[image]" value="<?php echo esc_attr( $image ); ?>" size="25" /></p>

    <p><label for="domain_field">
        <?php _e( 'Title', 'giao' ); ?>
    </label></p>
    <p><input type="text" id="title_field" name="option[title]" value="<?php echo esc_attr( $title ); ?>" size="25" /></p>

    <p><label for="content_field">
        <?php _e( 'Content', 'giao' ); ?>
    </label></p>
    <p><input type="text" id="content_field" name="option[content]" value="<?php echo esc_attr( $content ); ?>" size="25" /></p>

    <p><label for="save_img">
        <?php _e( 'Tải hình ảnh đầu tiên trong nội dung làm ảnh đại diện', 'giao' ); ?>
    </label><input type="checkbox" id="save_img_field" name="option[save_img]" value="1" size="25" <?php echo ($save_img == 1) ? "checked" : ""; ?> /></p>
    <p><label for="attribute_field">
        <?php _e( 'Nhập thuộc tính hình ảnh cần lấy ( ví dụ src, data-src, srcset )', 'giao' ); ?>
    </label></p>
    <p><input type="text" id="attribute_field" name="option[attr]" value="<?php echo esc_attr( $attr ); ?>" size="25" placeholder="src" /></p>

    <p><label for="attribute_field">
        <?php _e( 'Ký tự cần thay đổi trong bài viết, nhập mỗi keyword 1 dòng', 'giao' ); ?>
    </label></p>
    <p><textarea placeholder="Muadonghocu.vn"></textarea></p>