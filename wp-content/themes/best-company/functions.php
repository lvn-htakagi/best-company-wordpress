<?php
require_once get_theme_file_path('/inc/save-field.php');
// ショップ関連のファイルを読み込む
require_once get_theme_file_path('/inc/content/shop/meta-boxes.php');
require_once get_theme_file_path('/inc/content/shop/fields.php');
require_once get_theme_file_path('/inc/content/shop/save.php');
require_once get_theme_file_path('/inc/content/shop/histories.php');
require_once get_theme_file_path('/inc/content/shop/interviews.php');

define('LVN_MATCH_URL', 'https://dev-best-wp.lvnmatch.com');
define('BEST_COMPANY_URL', LVN_MATCH_URL . '/shops/');

global $wpdb;

function mytheme_remove_comments_menu() {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'mytheme_remove_comments_menu');

function mytheme_remove_posts_menu() {
    remove_menu_page('edit.php');
}
add_action('admin_menu', 'mytheme_remove_posts_menu');

function mytheme_remove_pages_menu() {
    remove_menu_page('edit.php?post_type=page');
}
add_action('admin_menu', 'mytheme_remove_pages_menu');

function mytheme_remove_themes_menu() {
    remove_menu_page('themes.php');
}
add_action('admin_menu', 'mytheme_remove_themes_menu');

function mytheme_remove_shop_date_column($columns) {
    unset($columns['date']);

    $checkbox = $columns['cb'] ?? null;
    unset($columns['cb']);

    if ($checkbox !== null) {
        $columns = ['cb' => $checkbox] + $columns;
    }

    return $columns;
}
add_filter('manage_shop_posts_columns', 'mytheme_remove_shop_date_column');

function mytheme_register_shop_post_type() {
    register_post_type('shop', [
        'labels' => [
            'name'          => 'ショップ',
            'singular_name' => 'ショップ',
            'add_new_item'  => 'ショップを追加',
            'edit_item'     => 'ショップを編集',
        ],
        'public'          => true,
        'show_in_rest'    => true,
        'has_archive'     => true,
        'menu_icon'       => 'dashicons-store',
        'supports'        => [],
        'rewrite'         => [
            'slug' => 'shops',
        ],
        'capability_type' => ['shop', 'shops'],
        'map_meta_cap'    => true,
    ]);
}
add_action('init', 'mytheme_register_shop_post_type');

function mytheme_remove_shop_editor_support() {
    remove_post_type_support('shop', 'title');
    remove_post_type_support('shop', 'editor');
    remove_post_type_support('shop', 'thumbnail');
}
add_action('init', 'mytheme_remove_shop_editor_support', 20);

/**
 * ショップの権限を設定
 */
function mytheme_add_shop_capabilities() {

    // 管理者
    $admin_role = get_role('administrator');

    if ($admin_role) {
        $admin_capabilities = [
            'edit_shops',
            'edit_others_shops',
            'publish_shops',
            'read_private_shops',
            'delete_shops',
            'delete_private_shops',
            'delete_published_shops',
            'delete_others_shops',
            'edit_private_shops',
            'edit_published_shops',
            'create_shops',
        ];

        foreach ($admin_capabilities as $capability) {
            $admin_role->add_cap($capability);
        }
    }

    // 編集者（一般）
    $editor_role = get_role('editor');

    if ($editor_role) {
        $editor_capabilities = [
            'edit_shops',
            'create_shops',
        ];

        foreach ($editor_capabilities as $capability) {
            $editor_role->add_cap($capability);
        }
    }
}
add_action('init', 'mytheme_add_shop_capabilities');


function mytheme_restrict_shop_status($data, $postarr) {

    if ($data['post_type'] !== 'shop') {
        return $data;
    }

    // 管理者は制限しない
    if (current_user_can('manage_options')) {
        return $data;
    }

    // 既存ショップの場合は現在の公開状態を維持
    if (!empty($postarr['ID'])) {
        $post = get_post($postarr['ID']);

        if ($post) {
            $data['post_status'] = $post->post_status;
        }
    } else {
        // 新規登録は下書き
        $data['post_status'] = 'draft';
    }

    return $data;
}
add_filter('wp_insert_post_data', 'mytheme_restrict_shop_status', 10, 2);

/**
 * ショップ一覧に複製リンクを追加
 * 
 * @param array $actions 投稿のアクションリンク
 * @param WP_Post $post 投稿オブジェクト
 * @return array 修正後のアクションリンク
 */
function mytheme_add_shop_duplicate_link($actions, $post) {
    if ($post->post_type !== 'shop') {
        return $actions;
    }

    if (!current_user_can('edit_post', $post->ID)) {
        return $actions;
    }

    $url = wp_nonce_url(
        admin_url('admin.php?action=mytheme_duplicate_shop&post=' . $post->ID),
        'mytheme_duplicate_shop_' . $post->ID
    );

    $actions['duplicate'] = '<a href="' . esc_url($url) . '">複製</a>';

    return $actions;
}
add_filter('post_row_actions', 'mytheme_add_shop_duplicate_link', 10, 2);

/**
 * ショップを複製
 */
function mytheme_duplicate_shop() {
    if (!current_user_can('edit_posts')) {
        wp_die('この操作を行う権限がありません。');
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

    if (!$post_id) {
        wp_die('複製元のショップが指定されていません。');
    }

    check_admin_referer('mytheme_duplicate_shop_' . $post_id);

    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'shop') {
        wp_die('複製元のショップが見つかりません。');
    }

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('このショップを複製する権限がありません。');
    }

    $suffix = 2;

    do {
        $new_slug = (string) $suffix;
        $existing_post = get_page_by_path($new_slug, OBJECT, 'shop');
        $suffix++;
    } while ($existing_post);

    // 投稿データを複製
    $new_post_id = wp_insert_post([
        'post_type'    => 'shop',
        'post_title'   => $post->post_title,
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_status'  => 'draft',
        'menu_order'   => $post->menu_order,
        'post_author'  => get_current_user_id(),
        'post_name'    => $new_slug,
    ], true);

    if (is_wp_error($new_post_id)) {
        wp_die('ショップの複製に失敗しました。');
    }

    // カスタムフィールドを複製
    $meta_data = get_post_meta($post_id);

    foreach ($meta_data as $meta_key => $meta_values) {
        foreach ($meta_values as $meta_value) {
            add_post_meta($new_post_id, $meta_key, maybe_unserialize($meta_value));
        }
    }

    // アイキャッチ画像を引き継ぐ
    $thumbnail_id = get_post_thumbnail_id($post_id);

    if ($thumbnail_id) {
        set_post_thumbnail($new_post_id, $thumbnail_id);
    }

    // 複製したショップの編集画面へ移動
    wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    exit;
}
add_action('admin_action_mytheme_duplicate_shop', 'mytheme_duplicate_shop');

/**
 * ショップ編集画面でメディアライブラリを読み込む
 */
function mytheme_enqueue_face_media($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || $screen->post_type !== 'shop') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'mytheme-shop-media',
        get_theme_file_uri('/js/face-media.js'),
        ['jquery'],
        filemtime(get_theme_file_path('/js/face-media.js')),
        true
    );
}
add_action('admin_enqueue_scripts', 'mytheme_enqueue_face_media');

/**
 * 顔写真を保存
 */
function mytheme_save_shop_face($post_id) {
    if (
        !isset($_POST['mytheme_shop_face_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_face_nonce'])),
            'mytheme_save_shop_face'
        )
    ) {
        return;
    }

    if (!old_mytheme_can_save_shop($post_id)) {
        return;
    }

    if (
        isset($_POST['shop_face_removed']) &&
        is_string($_POST['shop_face_removed']) &&
        sanitize_text_field(wp_unslash($_POST['shop_face_removed'])) === '1'
    ) {
        delete_post_meta($post_id, '_shop_face');
        return;
    }

    if (!isset($_POST['shop_face'])) {
        return;
    }

    $image_id = absint($_POST['shop_face']);

    if ($image_id && !wp_attachment_is_image($image_id)) {
        return;
    }

    if (!$image_id) {
        return;
    }

    update_post_meta($post_id, '_shop_face', $image_id);
}
add_action('save_post_shop', 'mytheme_save_shop_face');

/**
 * ショップ編集画面でメディアライブラリを読み込む
 */
function mytheme_enqueue_shop_media($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || $screen->post_type !== 'shop') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'mytheme-shop-image-media',
        get_theme_file_uri('/js/shop-media.js'),
        ['jquery'],
        filemtime(get_theme_file_path('/js/shop-media.js')),
        true
    );
}
add_action('admin_enqueue_scripts', 'mytheme_enqueue_shop_media');

/**
 * ショップ画像を保存
 */
function mytheme_save_shop_image($post_id) {
    if (
        !isset($_POST['mytheme_shop_image_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_image_nonce'])),
            'mytheme_save_shop_image'
        )
    ) {
        return;
    }

    if (!old_mytheme_can_save_shop($post_id)) {
        return;
    }

    if (
        isset($_POST['shop_image_removed']) &&
        is_string($_POST['shop_image_removed']) &&
        sanitize_text_field(wp_unslash($_POST['shop_image_removed'])) === '1'
    ) {
        delete_post_meta($post_id, '_shop_image');
        return;
    }

    if (!isset($_POST['shop_image'])) {
        return;
    }

    $image_id = absint($_POST['shop_image']);

    if ($image_id && !wp_attachment_is_image($image_id)) {
        return;
    }

    if (!$image_id) {
        return;
    }

    update_post_meta($post_id, '_shop_image', $image_id);
}
add_action('save_post_shop', 'mytheme_save_shop_image');

/**
 * ショップ編集画面でメディアライブラリを読み込む
 */
function mytheme_shop_gallery_admin_scripts($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || $screen->post_type !== 'shop') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');

    $script_path = get_theme_file_path('/js/shop-gallery.js');

    wp_enqueue_script(
        'mytheme-shop-gallery',
        get_theme_file_uri('/js/shop-gallery.js'),
        ['jquery', 'jquery-ui-sortable', 'media-editor', 'media-views'],
        filemtime($script_path),
        true
    );
}
add_action('admin_enqueue_scripts', 'mytheme_shop_gallery_admin_scripts');

/**
 * ギャラリー画像を保存
 */
function mytheme_save_shop_gallery($post_id) {
    if (
        !isset($_POST['mytheme_shop_gallery_nonce'])
        || !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['mytheme_shop_gallery_nonce'])
            ),
            'mytheme_save_shop_gallery'
        )
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['mytheme_shop_gallery_ids'])) {
        return;
    }

    $raw_ids = sanitize_text_field(
        wp_unslash($_POST['mytheme_shop_gallery_ids'])
    );

    $shop_gallery_ids = array_filter(
        array_map('absint', explode(',', $raw_ids))
    );

    $shop_gallery_ids = array_values(
        array_unique(
            array_filter(
                $shop_gallery_ids,
                'wp_attachment_is_image'
            )
        )
    );

    if ($shop_gallery_ids) {
        update_post_meta(
            $post_id,
            '_shop_gallery_ids',
            $shop_gallery_ids
        );
    } else {
        delete_post_meta($post_id, '_shop_gallery_ids');
    }
}
add_action('save_post_shop', 'mytheme_save_shop_gallery');

/**
 * ショップ一覧の列を設定
 */
function mytheme_shop_admin_columns($columns) {
    $new_columns = [];

    // ショップ名を先頭にする
    $new_columns['cb'] = $columns['cb'];
    $new_columns['shop_name'] = 'ショップ名';
    $new_columns['shop_customer_id'] = 'カスタマーID';
    $new_columns['shop_prefecture'] = '都道府県';
    $new_columns['shop_year'] = '年';
    $new_columns['shop_start_date'] = '掲載開始日';
    $new_columns['shop_status'] = '掲載状況';

    // 既存の列を追加（タイトル列は除外）
    foreach ($columns as $key => $label) {
        if (!in_array($key, ['cb', 'title', 'date'], true)) {
            $new_columns[$key] = $label;
        }
    }

    return $new_columns;
}
add_filter('manage_shop_posts_columns', 'mytheme_shop_admin_columns');

/**
 * ショップ一覧にショップ名を表示
 */
function mytheme_shop_admin_column_content($column, $post_id) {
    if ($column === 'shop_name') {
        $shop_name = get_post_meta($post_id, '_shop_name', true);
        echo esc_html($shop_name ?: '（ショップ名未入力）');
    }
    if ($column === 'shop_customer_id') {
        $shop_customer_id = get_post_meta($post_id, '_shop_custome_id', true);
        echo esc_html($shop_customer_id ?: '（カスタマーID未入力）');
    }
    if ($column === 'shop_prefecture') {
        $shop_prefecture = mytheme_get_prefectures()[get_post_meta($post_id, '_shop_prefecture', true)] ?? '';
        echo esc_html($shop_prefecture ?: '（都道府県未入力）');
    }
    if ($column === 'shop_year') {
        $shop_year = get_post_meta($post_id, '_shop_year', true);
        echo esc_html($shop_year ?: '（年未入力）');
    }
    if ($column === 'shop_start_date') {
        $post_date = get_the_date('Y/m/d', $post_id);
        echo esc_html($post_date);
    }
    if ($column === 'shop_status') {
        $status = get_post_status($post_id);

        $status_labels = [
            'publish' => '公開',
            'private' => '非公開',
            'draft'   => '下書き',
            'pending' => '承認待ち',
        ];
        echo esc_html($status_labels[$status] ?? '（掲載状況未入力）');
    }
}
add_action('manage_shop_posts_custom_column', 'mytheme_shop_admin_column_content', 10, 2);

/**
 * ショップ名の並び替えを有効にする
 */
function mytheme_shop_sortable_columns($columns) {
    $columns['shop_name'] = 'shop_name';
    $columns['shop_customer_id'] = 'shop_customer_id';
    $columns['shop_prefecture'] = 'shop_prefecture';
    $columns['shop_year'] = 'shop_year';
    $columns['shop_start_date'] = 'shop_start_date';
    return $columns;
}
add_filter('manage_edit-shop_sortable_columns', 'mytheme_shop_sortable_columns');

/**
 * ショップ名で並び替える
 */
function mytheme_shop_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'shop') {
        return;
    }

    if ($query->get('orderby') === 'shop_name') {
        $query->set('meta_key', '_shop_name');
        $query->set('orderby', 'meta_value');
    }

    if ($query->get('orderby') === 'shop_customer_id') {
        $query->set('meta_key', '_shop_custome_id');
        $query->set('orderby', 'meta_value');
    }

    if ($query->get('orderby') === 'shop_prefecture') {
        global $wpdb;
        $prefectures = mytheme_get_prefectures();

        // 都道府県IDを県名に変換するCASE式を作成
        $case_sql = 'CASE';

        foreach ($prefectures as $id => $name) {
            $case_sql .= $wpdb->prepare(
                ' WHEN meta_value = %s THEN %s',
                (string) $id,
                $name
            );
        }

        $case_sql .= ' ELSE "" END';

        $query->set('meta_key', '_shop_prefecture');
        $query->set('orderby', 'meta_value');
    }

    if ($query->get('orderby') === 'shop_year') {
        $query->set('meta_key', '_shop_year');
        $query->set('orderby', 'meta_value');
    }

    if ($query->get('orderby') === 'shop_start_date') {
        $query->set('orderby', 'date');
    }
}
add_action('pre_get_posts', 'mytheme_shop_orderby');

/**
 * 年別ショップ一覧のURL
 * /shops/2026 → ショップ一覧（2026年）
 */
function mytheme_shop_year_rewrite_rules() {
    add_rewrite_rule(
        '^shops/([0-9]{4})/?$',
        'index.php?post_type=shop&shop_year=$matches[1]',
        'top'
    );
}
add_action('init', 'mytheme_shop_year_rewrite_rules');

function mytheme_shop_year_query_vars($vars) {
    $vars[] = 'shop_year';
    return $vars;
}
add_filter('query_vars', 'mytheme_shop_year_query_vars');

function mytheme_enqueue_shop_single_styles() {
    if (is_singular('shop')) {
        $css_path = get_theme_file_path('/assets/css/detail.css');

        wp_enqueue_style(
            'mytheme-shop-single',
            get_theme_file_uri('/assets/css/detail.css'),
            [],
            file_exists($css_path) ? filemtime($css_path) : null
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_shop_single_styles');

function mytheme_enqueue_lvn_styles() {
    $css_path = get_theme_file_path('/assets/css/common.css');

    if (file_exists($css_path)) {
        wp_enqueue_style(
            'mytheme-lvn-common',
            get_theme_file_uri('/assets/css/common.css'),
            [],
            filemtime($css_path)
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_lvn_styles');

function mytheme_enqueue_fadein_script() {
    $js_path = get_theme_file_path('/assets/js/fadeIn.js');

    if (file_exists($js_path)) {
        wp_enqueue_script(
            'mytheme-fadein',
            get_theme_file_uri('/assets/js/fadeIn.js'),
            ['jquery'],
            filemtime($js_path),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_fadein_script');

function mytheme_enqueue_shop_archive_styles() {
    if (get_query_var('shop_year') !== '' || is_post_type_archive('shop')) {
        $css_path = get_theme_file_path('/assets/css/top.css');

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'mytheme-top',
                get_theme_file_uri('/assets/css/top.css'),
                [],
                filemtime($css_path)
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_shop_archive_styles');

function mytheme_enqueue_judge_styles() {
    if (is_page('judge')) {
        $css_path = get_theme_file_path('/assets/css/exam.css');

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'mytheme-judge',
                get_theme_file_uri('/assets/css/exam.css'),
                [],
                filemtime($css_path)
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_judge_styles');

function mytheme_add_shop_judge_rewrite_rule() {
    add_rewrite_rule(
        '^shops/([0-9]{4})/judge/?$',
        'index.php?pagename=judge&shop_year=$matches[1]',
        'top'
    );
}
add_action('init', 'mytheme_add_shop_judge_rewrite_rule');

/**
 * ショップ編集画面用JavaScriptを読み込む
 */
function mytheme_enqueue_shop_admin_scripts($hook_suffix) {
    wp_enqueue_script('jquery-ui-sortable');
    // 投稿編集画面・新規追加画面以外は読み込まない
    if (!in_array($hook_suffix, ['post.php', 'post-new.php'], true)) {
        return;
    }

    $screen = get_current_screen();

    // ショップの編集画面以外は読み込まない
    if (!$screen || $screen->post_type !== 'shop') {
        return;
    }

    wp_enqueue_script(
        'mytheme-admin-shop-interviews',
        get_theme_file_uri('/js/admin-shop-interviews.js'),
        [],
        wp_get_theme()->get('Version'),
        true
    );

    wp_enqueue_script(
        'mytheme-admin-shop-histories',
        get_theme_file_uri('/js/admin-shop-histories.js'),
        [],
        wp_get_theme()->get('Version'),
        true
    );

    wp_enqueue_style(
        'mytheme-admin-shop-fields',
        get_theme_file_uri('/css/admin-shop-fields.css'),
        [],
        wp_get_theme()->get('Version')
    );
}
add_action('admin_enqueue_scripts', 'mytheme_enqueue_shop_admin_scripts');