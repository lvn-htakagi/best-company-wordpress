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
