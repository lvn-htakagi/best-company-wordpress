<?php
/**
 * 沿革編集画面の入力欄
 *
 * 沿革編集画面に表示する各入力欄の内容を定義する。
 *
 * 登録する項目
 * - ショップ（沿革に紐付ける）
 * - 年
 * - 月
 * - 沿革
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
function mytheme_render_history_shop_field($post) {
    wp_nonce_field(
        'mytheme_save_history_shop',
        'mytheme_history_shop_nonce'
    );

    mytheme_render_shop_selector_field($post, 'history');
}

/**
 * 年の選択肢
 */
function mytheme_get_history_years() {
    $year = [];
    for($i = 1900; $i <= 2036; $i++){
        $year[$i] = $i . "年";
    }
    return $year;
}

/**
 * 年の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_history_year_field($post) {
    wp_nonce_field(
        'mytheme_save_history_year',
        'mytheme_history_year_nonce'
    );

    mytheme_render_selector_field($post, 'shop_history_year', mytheme_get_history_years());
}

/**
 * 月の選択肢
 */
function mytheme_get_history_months() {
    $month = [];
    for($i = 1; $i <= 12; $i++){
        $month[$i] = $i . "月";
    }
    return $month;
}

/**
 * 月の入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_history_month_field($post) {
    wp_nonce_field(
        'mytheme_save_history_month',
        'mytheme_history_month_nonce'
    );

    mytheme_render_selector_field($post, 'shop_history_month', mytheme_get_history_months());
}

/**
 * 沿革の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_history_detail_field($post) {
    wp_nonce_field(
        'mytheme_save_history_detail',
        'mytheme_history_detail_nonce'
    );

    mytheme_render_text_field(
        $post,
        'shop_history_detail',
        '沿革'
    );
}