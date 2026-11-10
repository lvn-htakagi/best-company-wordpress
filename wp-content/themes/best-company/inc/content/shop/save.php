<?php
/**
 * ショップ編集画面の保存処理
 *
 * ショップ編集画面に入力されたデータを保存する。
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
 * 入力欄の共通登録処理は
 * inc/common/save-meta-field.php で定義。
 */

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

/**
 * 経歴を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_career($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_career_nonce',
        'mytheme_save_shop_career',
        'shop_career',
        '_shop_career',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_career');

/**
 * 編集部コメントを保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_comment($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_comment_nonce',
        'mytheme_save_shop_comment',
        'shop_comment',
        '_shop_comment',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_comment');

/**
 * 企業紹介を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_introduction($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_introduction_nonce',
        'mytheme_save_shop_introduction',
        'shop_introduction',
        '_shop_introduction',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_introduction');

/**
 * カスタムIDを保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_custome_id($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_custome_id_nonce',
        'mytheme_save_shop_custome_id',
        'shop_custome_id',
        '_shop_custome_id',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_custome_id');

/**
 * 会社名(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_company_name($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_company_name_nonce',
        'mytheme_save_shop_company_name',
        'shop_company_name',
        '_shop_company_name',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_company_name');

/**
 * 所在地(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_address($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_address_nonce',
        'mytheme_save_shop_address',
        'shop_address',
        '_shop_address',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_address');

/**
 * 電話番号(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_tel($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_tel_nonce',
        'mytheme_save_shop_tel',
        'shop_tel',
        '_shop_tel',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_tel');

/**
 * FAX番号(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_fax($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_fax_nonce',
        'mytheme_save_shop_fax',
        'shop_fax',
        '_shop_fax',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_fax');

/**
 * URL(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_url($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_url_nonce',
        'mytheme_save_shop_url',
        'shop_url',
        '_shop_url',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_url');

/**
 * 資本金(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_capital($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_capital_nonce',
        'mytheme_save_shop_capital',
        'shop_capital',
        '_shop_capital',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_capital');

/**
 * 売上高(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_net_sales($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_net_sales_nonce',
        'mytheme_save_shop_net_sales',
        'shop_net_sales',
        '_shop_net_sales',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_net_sales');

/**
 * 事業内容(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_business_content($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_business_content_nonce',
        'mytheme_save_shop_business_content',
        'shop_business_content',
        '_shop_business_content',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_business_content');

/**
 * 許認可・免許番号(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_license_number($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_license_number_nonce',
        'mytheme_save_shop_license_number',
        'shop_license_number',
        '_shop_license_number',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_license_number');

/**
 * 従業員数(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_employee($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_employee_nonce',
        'mytheme_save_shop_employee',
        'shop_employee',
        '_shop_employee',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_employee');

/**
 * 加盟団体(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_organizations($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_organizations_nonce',
        'mytheme_save_shop_organizations',
        'shop_organizations',
        '_shop_organizations',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_organizations');

/**
 * 設立年月(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_establishment($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_establishment_nonce',
        'mytheme_save_shop_establishment',
        'shop_establishment',
        '_shop_establishment',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_establishment');

/**
 * 拠点(企業概要)を保存
 */
function mytheme_save_shop_base($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_base_nonce',
        'mytheme_save_shop_base',
        'shop_base',
        '_shop_base',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_base');

/**
 * 関係会社(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_relationship($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_relationship_nonce',
        'mytheme_save_shop_relationship',
        'shop_relationship',
        '_shop_relationship',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_relationship');
