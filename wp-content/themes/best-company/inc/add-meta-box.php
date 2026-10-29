<?php
/**
 * ショップの項目の欄を生成する関数
 *
 * @param string $id メタボックスのID
 * @param string $title メタボックスのタイトル
 * @param callable $callback コールバック関数
 * 
 */
function mytheme_add_shop_meta_box($id, $title, $callback) {
    add_action('add_meta_boxes', function () use ($id, $title, $callback) {
        add_meta_box(
            $id,
            $title,
            $callback,
            'shop',
            'normal',
            'default'
        );
    });
}

/**
 * 沿革の項目の欄を生成する関数
 *
 * @param string $id メタボックスのID
 * @param string $title メタボックスのタイトル
 * @param callable $callback コールバック関数
 * 
 */
function mytheme_add_shop_history_meta_box($id, $title, $callback) {
    add_action('add_meta_boxes', function () use ($id, $title, $callback) {
        add_meta_box(
            $id,
            $title,
            $callback,
            'shop_history',
            'normal',
            'default'
        );
    });
}