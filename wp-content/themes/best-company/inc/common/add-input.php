<?php
/**
 * ショップ用テキスト入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param string $field_name フィールド名
 * @param string $label ラベル
 */
function mytheme_render_text_field($post, $field_name, $label) {
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
function mytheme_render_textarea_field($post, $field_name, $label) {
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
 * ショップの選択肢を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 * @param string $field_name フィールド名
 */
function mytheme_render_shop_selector_field($post, $field_name) {
    $selected_shop_id = get_post_meta(
        $post->ID,
        '_shop_' . $field_name . '_shop_id',
        true
    );

    // 登録済みのショップを取得
    $shops = get_posts([
        'post_type'      => 'shop',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ]);

    echo '<select name="' . esc_attr($field_name) . '_shop_id" id="' . esc_attr($field_name) . '_shop_id">';
    echo '<option value="">ショップを選択してください</option>';

    foreach ($shops as $shop) {
        $shop_name = get_post_meta(
            $shop->ID,
            '_shop_name',
            true
        );

        // ショップ名が未入力の場合の表示
        if ($shop_name === '') {
            $shop_name = 'ショップID: ' . $shop->ID;
        }

        echo '<option value="' . esc_attr($shop->ID) . '"'
            . selected(
                (string) $selected_shop_id,
                (string) $shop->ID,
                false
            )
            . '>';
        echo esc_html($shop_name);
        echo '</option>';
    }

    echo '</select>';
}