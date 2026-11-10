<?php
/**
 * 沿革編集画面の保存処理
 *
 * 沿革編集画面に入力されたデータを保存する。
 *
 * 登録する項目
 * - ショップ（沿革に紐付ける）
 * - 年
 * - 月
 * - 沿革
 *
 * 入力欄の共通表示処理は
 * inc/common/save-meta-field.php
 */

require_once get_theme_file_path('/inc/common/save-meta-field.php');

/**
 * ショップ（沿革に紐付ける）を保存する処理
 * 
 * @param int $post_id 保存対象の投稿ID
 * @return void
 */
function mytheme_save_history_shop($post_id) {
    mytheme_save_select_field(
        $post_id,
        'mytheme_history_shop_nonce',
        'mytheme_save_history_shop',
        'history_shop_id',
        '_shop_history_shop_id',
        'shop_history',
        'post',
        'shop'
    );
}
add_action('save_post_shop_history', 'mytheme_save_history_shop');