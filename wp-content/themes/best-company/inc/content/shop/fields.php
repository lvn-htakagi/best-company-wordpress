<?php
/**
 * ショップ編集画面の入力欄
 *
 * ショップ編集画面に表示する各入力欄の内容を定義する。
 *
 * 登録する項目
 * - 名前
 * - 代表者名
 * - 経歴
 * - 顔写真
 * - 画像
 * - 都道府県
 * - 年
 * - 編集部コメント
 * - 企業紹介
 * - カスタムID
 * - フォトギャラリー
 *
 * 会社概要
 *   - 会社名
 *   - 所在地
 *   - 電話番号
 *   - FAX番号
 *   - URL
 *   - 資本金
 *   - 売上高
 *   - 事業内容
 *   - 許認可・免許番号
 *   - 加盟団体
 *   - 従業員数
 *   - 設立年月
 *   - 拠点
 *   - 関係会社
 *
 * 入力欄の共通表示処理は
 * inc/common/fields.php で定義。
 */

require_once get_theme_file_path('/inc/common/add-input.php');

/**
 * 名前入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */

function mytheme_render_shop_name_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_name',
        'mytheme_shop_name_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_name',
        '名前'
    );
}

/**
 * 代表者名入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_president_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_president',
        'mytheme_shop_president_nonce'
    );
    
    mytheme_render_shop_text_field(
        $post,
        'shop_president',
        '代表者名'
    );
}

