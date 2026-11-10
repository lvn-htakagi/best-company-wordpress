<?php
/**
 * 沿革編集画面の入力項目
 *
 * 沿革編集画面に表示する各入力欄を登録する。
 *
 * 登録する項目
 * - ショップ（沿革に紐付ける）
 * - 年
 * - 月
 * - 沿革
 *
 * 入力欄の共通登録処理は
 * inc/common/add-meta-box.php で定義。
 */

require_once get_theme_file_path('/inc/common/add-meta-box.php');

// 沿革編集画面に「ショップ」を追加
mytheme_add_shop_history_meta_box(
    'shop_history_shop',
    'ショップ',
    'mytheme_render_history_shop_field'
);

// 沿革編集画面に「年」を追加
mytheme_add_shop_history_meta_box(
    'shop_history_year',
    '年',
    'mytheme_render_history_year_field'
);

// 沿革編集画面に「月」を追加
mytheme_add_shop_history_meta_box(
    'shop_history_month',
    '月',
    'mytheme_render_history_month_field'
);

// 沿革編集画面に「沿革」を追加
mytheme_add_shop_history_meta_box(
    'shop_history_detail',
    '沿革',
    'mytheme_render_history_detail_field'
);