<?php
/**
 * ショップ用テキスト入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param string $field_name フィールド名
 * @param string $label ラベル
 */
function mytheme_render_shop_text_field($post, $field_name, $label) {
    $meta_key = '_' . $field_name;
    $value = get_post_meta($post->ID, $meta_key, true);
    ?>

    <label for="<?php echo esc_attr($field_name); ?>">
        <?php echo esc_html($label); ?>
    </label>
    <input
        type="text"
        id="<?php echo esc_attr($field_name); ?>"
        name="<?php echo esc_attr($field_name); ?>"
        style="width: 100%;"
        value="<?php echo esc_attr($value); ?>"
    >

    <?php
}

/**
 * ショップ用テキストエリア入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param string $field_name フィールド名
 * @param string $label ラベル
 */
function mytheme_render_shop_textarea_field($post, $field_name, $label) {
    $meta_key = '_' . $field_name;
    $value = get_post_meta($post->ID, $meta_key, true);
    ?>

    <label for="<?php echo esc_attr($field_name); ?>">
        <?php echo esc_html($label); ?>
    </label>
    <textarea
        id="<?php echo esc_attr($field_name); ?>"
        name="<?php echo esc_attr($field_name); ?>"
        style="width: 100%;"
    ><?php echo esc_textarea($value); ?></textarea>

    <?php
}

/**
 * ショップ用nonceを表示
 * 
 * @param string $action アクション名
 * @param string $name ノンスフィールド名
 */
function mytheme_render_shop_nonce($action, $name) {
    wp_nonce_field($action, $name);
}