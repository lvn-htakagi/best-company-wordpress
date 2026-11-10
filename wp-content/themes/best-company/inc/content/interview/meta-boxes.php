<?php
/**
 * インタビュー編集画面の入力項目
 *
 * インタビュー編集画面に表示する各入力欄を登録する。
 *
 * 登録する項目
 * - ショップ（インタビューに紐付ける）
 * - 質問
 * - 回答
 *
 * 入力欄の共通登録処理は
 * inc/common/add-meta-box.php で定義。
 */

require_once get_theme_file_path('/inc/common/add-meta-box.php');

// インタビュー編集画面に「ショップ」を追加
mytheme_add_shop_interview_meta_box(
    'shop_interview_shop',
    'ショップ',
    'mytheme_render_interview_shop_field'
);

// インタビュー編集画面に「質問」を追加
mytheme_add_shop_interview_meta_box(
    'interview_question',
    '質問',
    'mytheme_render_interview_question_field'
);

// インタビュー編集画面に「回答」を追加
mytheme_add_shop_interview_meta_box(
    'interview_answer',
    '回答',
    'mytheme_render_interview_answer_field'
);