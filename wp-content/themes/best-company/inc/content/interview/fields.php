<?php
/**
 * インタビュー編集画面の入力欄
 *
 * インタビュー編集画面に表示する各入力欄の内容を定義する。
 *
 * 登録する項目
 * - ショップ（インタビューに紐付ける）
 * - 質問
 * - 回答
 * 
 * 入力欄の共通表示処理は
 * inc/common/fields.php で定義。
 */

require_once get_theme_file_path('/inc/common/add-input.php');

/**
 * ショップ選択欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_interview_shop_field($post) {
    wp_nonce_field('mytheme_save_interview_shop', 'mytheme_interview_shop_nonce');

    mytheme_render_shop_selector_field($post, 'interview');
}

/**
 * インタビューの質問の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_interview_question_field($post) {
    wp_nonce_field('mytheme_save_interview_question', 'mytheme_interview_question_nonce');

    mytheme_render_text_field($post, 'shop_interview_question' , '質問');
}

/**
 * インタビューの回答の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_interview_answer_field($post) {
    wp_nonce_field('mytheme_save_interview_answer', 'mytheme_interview_answer_nonce');

    mytheme_render_textarea_field($post, 'shop_interview_answer', '回答');
}

