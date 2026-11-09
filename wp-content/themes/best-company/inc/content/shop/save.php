<?php

require_once get_theme_file_path('/inc/common/save-meta-field.php');

/**
 * 名前を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_name($post_id) {
    mytheme_save_text_meta_field(
    $post_id ,
    'mytheme_shop_name_nonce',
    'mytheme_save_shop_name',
    'shop_name',
    '_shop_name',
    'shop'
);
}
add_action('save_post_shop', 'mytheme_save_shop_name');

/**
 * 代表者名を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_president($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_president_nonce',
        'mytheme_save_shop_president',
        'shop_president',
        '_shop_president',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_president');