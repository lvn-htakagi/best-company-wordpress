<?php
/**
 * 投稿の保存に関する共通処理
 *
 * 投稿を保存できるかの確認と、入力された内容の保存を行う。
 *
 * 共通処理
 * - 投稿の保存権限・状態の確認
 * - テキスト入力項目の保存
 */

/**
 * 投稿を保存できるか確認
 *
 * @param int    $post_id   投稿ID
 * @param string $post_type 投稿タイプ
 * @return bool
 */
function mytheme_can_save_post(int $post_id, string $post_type): bool {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return false;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return false;
    }

    if (get_post_type($post_id) !== $post_type) {
        return false;
    }

    return true;
}

/**
 * テキスト入力項目を保存する共通関数
 *
 * @param int    $post_id      投稿ID
 * @param string $nonce_name   nonceのフィールド名
 * @param string $nonce_action nonceのアクション名
 * @param string $field_name   入力フィールド名
 * @param string $meta_key     保存するメタキー
 * @param string $post_type    投稿タイプ
 * @return void
 */
function mytheme_save_text_meta_field(
    int $post_id,
    string $nonce_name,
    string $nonce_action,
    string $field_name,
    string $meta_key,
    string $post_type
): void {
    if (
        !isset($_POST[$nonce_name]) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST[$nonce_name])),
            $nonce_action
        )
    ) {
        return;
    }

    if (!mytheme_can_save_post($post_id, $post_type)) {
        return;
    }

    $value = sanitize_text_field(
        wp_unslash($_POST[$field_name] ?? '')
    );

    if ($value === '') {
        delete_post_meta($post_id, $meta_key);
        return;
    }

    update_post_meta($post_id, $meta_key, $value);
}

/**
 * セレクトボックスの選択内容を保存する共通関数
 *
 * @param int    $post_id      投稿ID
 * @param string $nonce_name   nonceのフィールド名
 * @param string $nonce_action nonceのアクション名
 * @param string $field_name   入力フィールド名
 * @param string $meta_key     保存するメタキー
 * @param string $post_type    保存元の投稿タイプ
 * @param string $value_type   選択値の種類（number / post）
 * @param string $related_type 紐付け先の投稿タイプ
 */
function mytheme_save_select_field(
    int $post_id,
    string $nonce_name,
    string $nonce_action,
    string $field_name,
    string $meta_key,
    string $post_type,
    string $value_type = 'number',
    string $related_type = ''
): void {
    // nonceの検証
    if (
        !isset($_POST[$nonce_name]) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST[$nonce_name])),
            $nonce_action
        )
    ) {
        return;
    }

    // 投稿を保存できるか確認
    if (!mytheme_can_save_post($post_id, $post_type)) {
        return;
    }

    if (!isset($_POST[$field_name])) {
        return;
    }

    $value = absint($_POST[$field_name]);

    // 未選択なら紐付けを削除
    if ($value === 0) {
        delete_post_meta($post_id, $meta_key);
        return;
    }

    // 紐付け先の投稿を確認
    if ($value_type === 'post') {
        if (
            get_post_type($value) !== $related_type ||
            !current_user_can('edit_post', $value)
        ) {
            return;
        }
    }

    // 選択内容を保存
    update_post_meta($post_id, $meta_key, $value);
}