<?php
/**
 * ショップ編集画面の入力項目
 *
 * ショップ編集画面に表示する各入力欄を登録する。
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
 * inc/common/add-meta-box.php で定義。
 */

require_once get_theme_file_path('/inc/common/add-meta-box.php');

// 名前
mytheme_add_shop_meta_box(
    'shop_name',
    '名前',
    'mytheme_render_shop_name_field'
);

// 代表者名
mytheme_add_shop_meta_box(
    'shop_president',
    '代表者名',
    'mytheme_render_shop_president_field'
);

// 経歴
mytheme_add_shop_meta_box(
    'shop_career',
    '経歴',
    'mytheme_render_shop_career_field'
);

// 顔写真
mytheme_add_shop_meta_box(
    'shop_face',
    '顔写真',
    'mytheme_render_shop_face_field'
);

// 画像
mytheme_add_shop_meta_box(
    'shop_image',
    '画像',
    'mytheme_render_shop_image_field'
);

// 都道府県
mytheme_add_shop_meta_box(
    'shop_prefecture',
    '都道府県',
    'mytheme_render_shop_prefecture_field'
);

// 年
mytheme_add_shop_meta_box(
    'shop_year',
    '年',
    'mytheme_render_shop_year_field'
);

// 編集部コメント
mytheme_add_shop_meta_box(
    'shop_comment',
    '編集部コメント',
    'mytheme_render_shop_comment_field'
);

// 企業紹介
mytheme_add_shop_meta_box(
    'shop_introduction',
    '企業紹介',
    'mytheme_render_shop_introduction_field'
);

// カスタムID
mytheme_add_shop_meta_box(
    'shop_custome_id',
    'カスタムID',
    'mytheme_render_shop_custome_id_field'
);

// フォトギャラリー
mytheme_add_shop_meta_box(
    'shop_gallery',
    'フォトギャラリー',
    'mytheme_render_shop_gallery_field'
);

// 会社名
mytheme_add_shop_meta_box(
    'shop_company_name',
    '会社名',
    'mytheme_render_shop_company_name_field'
);

// 所在地
mytheme_add_shop_meta_box(
    'shop_address',
    '所在地',
    'mytheme_render_shop_address_field'
);

// 電話番号
mytheme_add_shop_meta_box(
    'shop_tel',
    '電話番号',
    'mytheme_render_shop_tel_field'
);

// FAX番号
mytheme_add_shop_meta_box(
    'shop_fax',
    'FAX番号',
    'mytheme_render_shop_fax_field'
);

// URL
mytheme_add_shop_meta_box(
    'shop_url',
    'URL',
    'mytheme_render_shop_url_field'
);

// 資本金
mytheme_add_shop_meta_box(
    'shop_capital',
    '資本金',
    'mytheme_render_shop_capital_field'
);

// 売上高
mytheme_add_shop_meta_box(
    'shop_net_sales',
    '売上高',
    'mytheme_render_shop_net_sales_field'
);

// 事業内容
mytheme_add_shop_meta_box(
    'shop_business_content',
    '事業内容',
    'mytheme_render_shop_business_content_field'
);

// 許認可・免許番号
mytheme_add_shop_meta_box(
    'shop_license_number',
    '許認可・免許番号',
    'mytheme_render_shop_license_number_field'
);

// 加盟団体
mytheme_add_shop_meta_box(
    'shop_organizations',
    '加盟団体',
    'mytheme_render_shop_organizations_field'
);

//　従業員数
mytheme_add_shop_meta_box(
    'shop_employee',
    '従業員数',
    'mytheme_render_shop_employee_field'
);

// 設立年月
mytheme_add_shop_meta_box(
    'shop_establishment',
    '設立年月',
    'mytheme_render_shop_establishment_field'
);

// 拠点
mytheme_add_shop_meta_box(
    'shop_base',
    '拠点',
    'mytheme_render_shop_base_field'
);

// 関係会社
mytheme_add_shop_meta_box(
    'shop_relationship',
    '関係会社',
    'mytheme_render_shop_relationship_field'
);