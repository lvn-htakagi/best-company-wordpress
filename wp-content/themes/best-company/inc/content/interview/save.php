<?php
/**
 * インタビュー編集画面の保存処理
 *
 * インタビュー編集画面に入力されたデータを保存する。
 *
 * 登録する項目
 * - ショップ（インタビューに紐付ける）
 * - 質問
 * - 回答
 *
 * 入力欄の共通表示処理は
 * inc/common/save-meta-field.php
 */

require_once get_theme_file_path('/inc/common/save-meta-field.php');

/**
 * ショップ（インタビューに紐付ける）を保存する処理
 * 
 * @param int $post_id 保存対象の投稿ID
 * @return void
 */
function mytheme_save_interview_shop($post_id) {
    mytheme_save_select_field(
        $post_id,
        'mytheme_interview_shop_nonce',
        'mytheme_save_interview_shop',
        'interview_shop_id',
        '_shop_interview_shop_id',
        'shop_history',
        'post',
        'shop'
    );
}
add_action('save_post_shop_history', 'mytheme_save_interview_shop');


