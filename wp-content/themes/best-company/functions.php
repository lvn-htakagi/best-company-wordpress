<?php
require_once get_theme_file_path('/inc/save-field.php');
require_once get_theme_file_path('/inc/content/shop/meta-boxes.php');
require_once get_theme_file_path('/inc/content/shop/fields.php');

define('LVN_MATCH_URL', 'https://dev-best-wp.lvnmatch.com');
define('BEST_COMPANY_URL', LVN_MATCH_URL . '/shops/');

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

function mytheme_register_shop_post_type() {
    register_post_type('shop', [
        'labels' => [
            'name'          => 'ショップ',
            'singular_name' => 'ショップ',
            'add_new_item'  => 'ショップを追加',
            'edit_item'     => 'ショップを編集',
        ],
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-store',
        'supports'     => [],
        'rewrite'      => [
            'slug' => 'shops',
        ],
    ]);
}
add_action('init', 'mytheme_register_shop_post_type');

/**
 * 沿革のカスタム投稿タイプを登録
 */
function mytheme_register_shop_history_post_type() {
    register_post_type('shop_history', [
        'labels' => [
            'name'          => '沿革',
            'singular_name' => '沿革',
            'add_new_item'  => '沿革を追加',
            'edit_item'     => '沿革を編集',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-backup',
        'supports'     => [],
    ]);
}
add_action('init', 'mytheme_register_shop_history_post_type');

/**
 * インタビューのカスタム投稿タイプを登録
 */
function mytheme_register_shop_interview_post_type() {
    register_post_type('shop_interview', [
        'labels' => [
            'name'          => 'インタビュー',
            'singular_name' => 'インタビュー',
            'add_new_item'  => 'インタビューを追加',
            'edit_item'     => 'インタビューを編集',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_rest' => false,
        'menu_icon'    => 'dashicons-format-status',
        'supports'     => [],
    ]);
}
add_action('init', 'mytheme_register_shop_interview_post_type');

function mytheme_remove_shop_editor_support() {
    remove_post_type_support('shop', 'title');
    remove_post_type_support('shop', 'editor');
    remove_post_type_support('shop', 'thumbnail');
}
add_action('init', 'mytheme_remove_shop_editor_support', 20);

function mytheme_remove_shop_history_editor_support() {
    remove_post_type_support('shop_history', 'title');
    remove_post_type_support('shop_history', 'editor');
    remove_post_type_support('shop_history', 'thumbnail');
}
add_action('init', 'mytheme_remove_shop_history_editor_support', 20);

function mytheme_remove_shop_interview_editor_support() {
    remove_post_type_support('shop_interview', 'title');
    remove_post_type_support('shop_interview', 'editor');
    remove_post_type_support('shop_interview', 'thumbnail');
}
add_action('init', 'mytheme_remove_shop_interview_editor_support', 20);

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
 * 名前を保存
 */
function mytheme_save_shop_name($post_id) {
    if (
        !isset($_POST['mytheme_shop_name_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_name_nonce'])),
            'mytheme_save_shop_name'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $name = sanitize_text_field(wp_unslash($_POST['shop_name']));

    if ($name === '') {
        delete_post_meta($post_id, '_shop_name');
        return;
    }

    update_post_meta($post_id, '_shop_name', $name);
}
add_action('save_post_shop', 'mytheme_save_shop_name');

/**
 * 代表者名を保存
 */
function mytheme_save_shop_president($post_id) {
    if (
        !isset($_POST['mytheme_shop_president_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_president_nonce'])),
            'mytheme_save_shop_president'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $president = sanitize_text_field(wp_unslash($_POST['shop_president']));

    if ($president === '') {
        delete_post_meta($post_id, '_shop_president');
        return;
    }

    update_post_meta($post_id, '_shop_president', $president);
}
add_action('save_post_shop', 'mytheme_save_shop_president');

/**
 * 経歴を保存
 */
function mytheme_save_shop_career($post_id) {
    if (
        !isset($_POST['mytheme_shop_career_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_career_nonce'])),
            'mytheme_save_shop_career'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $comment = sanitize_text_field(wp_unslash($_POST['shop_career']));

    if ($comment === '') {
        delete_post_meta($post_id, '_shop_career');
        return;
    }

    update_post_meta($post_id, '_shop_career', $comment);
}
add_action('save_post_shop', 'mytheme_save_shop_career');

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

    if (!mytheme_can_save_shop($post_id)) {
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

    if (!mytheme_can_save_shop($post_id)) {
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
 * 都道府県を保存
 */
function mytheme_save_shop_prefecture($post_id) {
    if (
        !isset($_POST['mytheme_shop_prefecture_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['mytheme_shop_prefecture_nonce'])
            ),
            'mytheme_save_shop_prefecture'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    if (!isset($_POST['shop_prefecture']) || absint($_POST['shop_prefecture']) === 0) {
        return;
    }

    $prefecture = absint($_POST['shop_prefecture']);

    if (
        $prefecture !== 0 &&
        !array_key_exists($prefecture, mytheme_get_prefectures())
    ) {
        return;
    }

    if ($prefecture === 0) {
        delete_post_meta($post_id, '_shop_prefecture');
    } else {
        update_post_meta($post_id, '_shop_prefecture', $prefecture);
    }
}
add_action('save_post_shop', 'mytheme_save_shop_prefecture');

/**
 * 年を保存
 */
function mytheme_save_shop_year($post_id) {
    if (
        !isset($_POST['mytheme_shop_year_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_year_nonce'])),
            'mytheme_save_shop_year'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    if (!isset($_POST['shop_year'])) {
        return;
    }

    $year = sanitize_text_field(wp_unslash($_POST['shop_year']));

    if ($year === '') {
        delete_post_meta($post_id, '_shop_year');
        return;
    }

    if (!preg_match('/^\d{4}$/', $year)) {
        return;
    }

    update_post_meta($post_id, '_shop_year', $year);
}
add_action('save_post_shop', 'mytheme_save_shop_year');

/**
 * 編集部コメントを保存
 */
function mytheme_save_shop_comment($post_id) {
    if (
        !isset($_POST['mytheme_shop_comment_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_comment_nonce'])),
            'mytheme_save_shop_comment'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $comment = sanitize_text_field(wp_unslash($_POST['shop_comment']));

    if ($comment === '') {
        delete_post_meta($post_id, '_shop_comment');
        return;
    }

    update_post_meta($post_id, '_shop_comment', $comment);
}
add_action('save_post_shop', 'mytheme_save_shop_comment');

/**
 * 企業紹介を保存
 */
function mytheme_save_shop_introduction($post_id) {
    if (
        !isset($_POST['mytheme_shop_introduction_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_introduction_nonce'])),
            'mytheme_save_shop_introduction'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $introduction = sanitize_text_field(wp_unslash($_POST['shop_introduction']));

    if ($introduction === '') {
        delete_post_meta($post_id, '_shop_introduction');
        return;
    }

    update_post_meta($post_id, '_shop_introduction', $introduction);
}
add_action('save_post_shop', 'mytheme_save_shop_introduction');

/**
 * カスタムIDを保存
 */
function mytheme_save_shop_custome_id($post_id) {
    if (
        !isset($_POST['mytheme_shop_custome_id_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_custome_id_nonce'])),
            'mytheme_save_shop_custome_id'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $custome_id = sanitize_text_field(wp_unslash($_POST['shop_custome_id']));

    if ($custome_id === '') {
        delete_post_meta($post_id, '_shop_custome_id');
        return;
    }

    update_post_meta($post_id, '_shop_custome_id', $custome_id);
}
add_action('save_post_shop', 'mytheme_save_shop_custome_id');

/**
 * 会社名(企業概要)を保存
 */
function mytheme_save_shop_company_name($post_id) {
    if (
        !isset($_POST['mytheme_shop_company_name_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_company_name_nonce'])),
            'mytheme_save_shop_company_name'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $company_name = sanitize_text_field(wp_unslash($_POST['shop_company_name']));

    if ($company_name === '') {
        delete_post_meta($post_id, '_shop_company_name');
        return;
    }

    update_post_meta($post_id, '_shop_company_name', $company_name);
}
add_action('save_post_shop', 'mytheme_save_shop_company_name');

/**
 * 所在地(企業概要)を保存
 */
function mytheme_save_shop_address($post_id) {
    if (
        !isset($_POST['mytheme_shop_address_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_address_nonce'])),
            'mytheme_save_shop_address'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $address = sanitize_text_field(wp_unslash($_POST['shop_address']));

    if ($address === '') {
        delete_post_meta($post_id, '_shop_address');
        return;
    }

    update_post_meta($post_id, '_shop_address', $address);
}
add_action('save_post_shop', 'mytheme_save_shop_address');

/**
 * 電話番号(企業概要)を保存
 */
function mytheme_save_shop_tel($post_id) {
    if (
        !isset($_POST['mytheme_shop_tel_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_tel_nonce'])),
            'mytheme_save_shop_tel'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    if (!isset($_POST['shop_tel'])) {
        return;
    }

    $tel = sanitize_text_field(wp_unslash($_POST['shop_tel']));

    if ($tel === '') {
        delete_post_meta($post_id, '_shop_tel');
        return;
    }

    // ハイフン区切り可、数字部分が0始まりの10〜11桁
    if (!preg_match('/^0\d{1,4}-?\d{1,4}-?\d{3,4}$/', $tel) || !preg_match('/^0\d{9,10}$/', str_replace('-', '', $tel))) {
        return;
    }

    update_post_meta($post_id, '_shop_tel', $tel);
}
add_action('save_post_shop', 'mytheme_save_shop_tel');

/**
 * FAX番号(企業概要)を保存
 */
function mytheme_save_shop_fax($post_id) {
    if (
        !isset($_POST['mytheme_shop_fax_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_fax_nonce'])),
            'mytheme_save_shop_fax'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $fax = sanitize_text_field(wp_unslash($_POST['shop_fax']));

    if ($fax === '') {
        delete_post_meta($post_id, '_shop_fax');
        return;
    }

    update_post_meta($post_id, '_shop_fax', $fax);
}
add_action('save_post_shop', 'mytheme_save_shop_fax');

/**
 * URL(企業概要)を保存
 */
function mytheme_save_shop_url($post_id) {
    if (
        !isset($_POST['mytheme_shop_url_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_url_nonce'])),
            'mytheme_save_shop_url'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $url = sanitize_text_field(wp_unslash($_POST['shop_url']));

    if ($url === '') {
        delete_post_meta($post_id, '_shop_url');
        return;
    }

    update_post_meta($post_id, '_shop_url', $url);
}
add_action('save_post_shop', 'mytheme_save_shop_url');

/**
 * 資本金(企業概要)を保存
 */
function mytheme_save_shop_capital($post_id) {
    if (
        !isset($_POST['mytheme_shop_capital_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_capital_nonce'])),
            'mytheme_save_shop_capital'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $capital = sanitize_text_field(wp_unslash($_POST['shop_capital']));

    if ($capital === '') {
        delete_post_meta($post_id, '_shop_capital');
        return;
    }

    update_post_meta($post_id, '_shop_capital', $capital);
}
add_action('save_post_shop', 'mytheme_save_shop_capital');

/**
 * 売上高(企業概要)を保存
 */
function mytheme_save_shop_net_sales($post_id) {
    if (
        !isset($_POST['mytheme_shop_net_sales_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_net_sales_nonce'])),
            'mytheme_save_shop_net_sales'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $net_sales = sanitize_text_field(wp_unslash($_POST['shop_net_sales']));

    if ($net_sales === '') {
        delete_post_meta($post_id, '_shop_net_sales');
        return;
    }

    update_post_meta($post_id, '_shop_net_sales', $net_sales);
}
add_action('save_post_shop', 'mytheme_save_shop_net_sales');

/**
 * 事業内容(企業概要)を保存
 */
function mytheme_save_shop_business_content($post_id) {
    if (
        !isset($_POST['mytheme_shop_business_content_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_business_content_nonce'])),
            'mytheme_save_shop_business_content'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $business_content = sanitize_text_field(wp_unslash($_POST['shop_business_content']));

    if ($business_content === '') {
        delete_post_meta($post_id, '_shop_business_content');
        return;
    }

    update_post_meta($post_id, '_shop_business_content', $business_content);
}
add_action('save_post_shop', 'mytheme_save_shop_business_content');

/**
 * 許認可・免許番号(企業概要)を保存
 */
function mytheme_save_shop_license_number($post_id) {
    if (
        !isset($_POST['mytheme_shop_license_number_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_license_number_nonce'])),
            'mytheme_save_shop_license_number'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $license_number = sanitize_text_field(wp_unslash($_POST['shop_license_number']));

    if ($license_number === '') {
        delete_post_meta($post_id, '_shop_license_number');
        return;
    }

    update_post_meta($post_id, '_shop_license_number', $license_number);
}
add_action('save_post_shop', 'mytheme_save_shop_license_number');

/**
 * 従業員数(企業概要)の入力欄を表示
 */
function mytheme_render_shop_employee_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_employee',
        'mytheme_shop_employee_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_employee',
        '従業員数(企業概要)'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_employee');
/**
 * 従業員数(企業概要)を保存
 */
function mytheme_save_shop_employee($post_id) {
    if (
        !isset($_POST['mytheme_shop_employee_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_employee_nonce'])),
            'mytheme_save_shop_employee'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $employee = sanitize_text_field(wp_unslash($_POST['shop_employee']));

    if ($employee === '') {
        delete_post_meta($post_id, '_shop_employee');
        return;
    }

    update_post_meta($post_id, '_shop_employee', $employee);
}
add_action('save_post_shop', 'mytheme_save_shop_employee');

/**
 * 加盟団体(企業概要)を保存
 */
function mytheme_save_shop_organizations($post_id) {
    if (
        !isset($_POST['mytheme_shop_organizations_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_organizations_nonce'])),
            'mytheme_save_shop_organizations'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $organizations = sanitize_text_field(wp_unslash($_POST['shop_organizations']));

    if ($organizations === '') {
        delete_post_meta($post_id, '_shop_organizations');
        return;
    }

    update_post_meta($post_id, '_shop_organizations', $organizations);
}
add_action('save_post_shop', 'mytheme_save_shop_organizations');

/**
 * 設立年月(企業概要)を保存
 */
function mytheme_save_shop_establishment($post_id) {
    if (
        !isset($_POST['mytheme_shop_establishment_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_establishment_nonce'])),
            'mytheme_save_shop_establishment'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $establishment = sanitize_text_field(wp_unslash($_POST['shop_establishment']));

    if ($establishment === '') {
        delete_post_meta($post_id, '_shop_establishment');
        return;
    }

    update_post_meta($post_id, '_shop_establishment', $establishment);
}
add_action('save_post_shop', 'mytheme_save_shop_establishment');

/**
 * 拠点(企業概要)を保存
 */
function mytheme_save_shop_base($post_id) {
    if (
        !isset($_POST['mytheme_shop_base_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_base_nonce'])),
            'mytheme_save_shop_base'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $base = sanitize_text_field(wp_unslash($_POST['shop_base']));

    if ($base === '') {
        delete_post_meta($post_id, '_shop_base');
        return;
    }

    update_post_meta($post_id, '_shop_base', $base);
}
add_action('save_post_shop', 'mytheme_save_shop_base');

/**
 * 関係会社(企業概要)を保存
 */
function mytheme_save_shop_relationship($post_id) {
    if (
        !isset($_POST['mytheme_shop_relationship_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_relationship_nonce'])),
            'mytheme_save_shop_relationship'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $relationship = sanitize_text_field(wp_unslash($_POST['shop_relationship']));

    if ($relationship === '') {
        delete_post_meta($post_id, '_shop_relationship');
        return;
    }

    update_post_meta($post_id, '_shop_relationship', $relationship);
}
add_action('save_post_shop', 'mytheme_save_shop_relationship');

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
 * 沿革編集画面に「ショップ」を追加
 */
mytheme_add_shop_history_meta_box(
    'shop_history_shop',
    'ショップ',
    'mytheme_render_history_shop_field'
);


/**
 * ショップ選択欄を表示
 */
function mytheme_render_history_shop_field($post) {
    wp_nonce_field(
        'mytheme_save_history_shop',
        'mytheme_history_shop_nonce'
    );

    mytheme_render_shop_field($post, 'history');
}

/**
 * 沿革に紐付けるショップIDを保存
 */
function mytheme_save_history_shop($post_id) {
    // nonceの検証
    if (
        !isset($_POST['mytheme_history_shop_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['mytheme_history_shop_nonce'])
            ),
            'mytheme_save_history_shop'
        )
    ) {
        return;
    }

    // 自動保存を除外
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 沿革の編集権限を確認
    if (
        get_post_type($post_id) !== 'shop_history' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    if (!isset($_POST['history_shop_id'])) {
        return;
    }

    $shop_id = absint($_POST['history_shop_id']);

    // 選択解除なら紐付けを削除
    if ($shop_id === 0) {
        delete_post_meta($post_id, '_shop_history_shop_id');
        return;
    }

    // 実在するショップか確認
    if (
        get_post_type($shop_id) !== 'shop' ||
        !current_user_can('edit_post', $shop_id)
    ) {
        return;
    }

    update_post_meta(
        $post_id,
        '_shop_history_shop_id',
        $shop_id
    );
}
add_action('save_post_shop_history', 'mytheme_save_history_shop');

/**
 * 沿革編集画面に「年」を追加
 */
mytheme_add_shop_history_meta_box(
    'history_year',
    '年',
    'mytheme_render_history_year_field'
);

/**
 * 年の選択肢
 */
function mytheme_get_history_years() {
    $year = [];
    for($i=1900;$i<=2036;$i++){
        $year[$i] = $i . "年";
    }
    return $year;
}

/**
 * 年の入力欄を表示
 */
function mytheme_render_history_year_field($post) {
    $history_year = get_post_meta($post->ID, '_shop_history_year', true);

    wp_nonce_field(
        'mytheme_save_history_year',
        'mytheme_history_year_nonce'
    );

    echo '<select id="history_year" name="history_year">';
    echo '<option value="">選択してください</option>';

    foreach (mytheme_get_history_years() as $key => $name) {
        echo '<option value="' . esc_attr($key) . '"'
            . selected((string) $history_year, (string) $key, false) . '>';
        echo esc_html($name);
        echo '</option>';
    }

    echo '</select>';
}

/**
 * 年を保存
 */
function mytheme_save_history_year($post_id) {
    if (
        !isset($_POST['mytheme_history_year_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['mytheme_history_year_nonce'])
            ),
            'mytheme_save_history_year'
        )
    ) {
        return;
    }

    if (
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
    ) {
        return;
    }

    if (
        get_post_type($post_id) !== 'shop_history' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    if (!isset($_POST['history_year'])) {
        return;
    }

    $history_year = absint($_POST['history_year']);

    if (
        $history_year !== 0 &&
        !array_key_exists($history_year, mytheme_get_history_years())
    ) {
        return;
    }

    if ($history_year === 0) {
        delete_post_meta($post_id, '_shop_history_year');
    } else {
        update_post_meta($post_id, '_shop_history_year', $history_year);
    }
}
add_action('save_post_shop_history', 'mytheme_save_history_year');

/**
 * 沿革編集画面に「月」を追加
 */
mytheme_add_shop_history_meta_box(
    'history_month',
    '月',
    'mytheme_render_history_month_field'
);

/**
 * 月の選択肢
 */
function mytheme_get_history_months() {
    return [
        1 => '1月',
        2 => '2月',
        3 => '3月',
        4 => '4月',
        5 => '5月',
        6 => '6月',
        7 => '7月',
        8 => '8月',
        9 => '9月',
        10 => '10月',
        11 => '11月',
        12 => '12月',
    ];
}

/**
 * 月の入力欄を表示
 */
function mytheme_render_history_month_field($post) {
    $history_month = get_post_meta($post->ID, '_shop_history_month', true);

    wp_nonce_field(
        'mytheme_save_history_month',
        'mytheme_history_month_nonce'
    );

    echo '<select id="history_month" name="history_month">';
    echo '<option value="">選択してください</option>';

    foreach (mytheme_get_history_months() as $key => $name) {
        echo '<option value="' . esc_attr($key) . '"'
            . selected((string) $history_month, (string) $key, false) . '>';
        echo esc_html($name);
        echo '</option>';
    }

    echo '</select>';
}

/**
 * 月を保存
 */
function mytheme_save_history_month($post_id) {
    if (
        !isset($_POST['mytheme_history_month_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['mytheme_history_month_nonce'])
            ),
            'mytheme_save_history_month'
        )
    ) {
        return;
    }

    if (
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE
    ) {
        return;
    }

    if (
        get_post_type($post_id) !== 'shop_history' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    if (!isset($_POST['history_month'])) {
        return;
    }

    $history_month = absint($_POST['history_month']);

    if (
        $history_month !== 0 &&
        !array_key_exists($history_month, mytheme_get_history_months())
    ) {
        return;
    }

    if ($history_month === 0) {
        delete_post_meta($post_id, '_shop_history_month');
    } else {
        update_post_meta($post_id, '_shop_history_month', $history_month);
    }
}
add_action('save_post_shop_history', 'mytheme_save_history_month');

/**
 * 沿革編集画面に「沿革」を追加
 */
mytheme_add_shop_history_meta_box(
    'history_detail',
    '沿革',
    'mytheme_render_history_detail_field'
);

/**
 * 沿革の入力欄を表示
 */
function mytheme_render_history_detail_field($post) {
    $detail = get_post_meta($post->ID, '_shop_history_detail', true);

    wp_nonce_field('mytheme_save_history_detail', 'mytheme_history_detail_nonce');
    ?>
    <input
        type="text"
        id="history_detail"
        name="history_detail"
        style="width: 100%;"
        value="<?php echo esc_attr($detail); ?>"
    />
    <?php
}

/**
 * 沿革を保存
 */
function mytheme_save_history_detail($post_id) {
    if (
        !isset($_POST['mytheme_history_detail_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_history_detail_nonce'])),
            'mytheme_save_history_detail'
        )
    ) {
        return;
    }

    $detail = sanitize_textarea_field(wp_unslash($_POST['history_detail']));

    if ($detail === '') {
        delete_post_meta($post_id, '_shop_history_detail');
        return;
    }

    update_post_meta($post_id, '_shop_history_detail', $detail);
}
add_action('save_post_shop_history', 'mytheme_save_history_detail');

/**
 * インタビュー編集画面に「ショップ」を追加
 */
mytheme_add_shop_interview_meta_box(
    'shop_interview_shop',
    'ショップ',
    'mytheme_render_interview_shop_field'
);


/**
 * ショップ選択欄を表示
 */
function mytheme_render_interview_shop_field($post) {
    mytheme_render_shop_nonce('mytheme_save_interview_shop', 'mytheme_interview_shop_nonce');

    mytheme_render_shop_field($post, 'interview');
}

/**
 * 沿革に紐付けるショップIDを保存
 */
function mytheme_save_interview_shop($post_id) {
    // nonceの検証
    if (
        !isset($_POST['mytheme_interview_shop_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['mytheme_interview_shop_nonce'])
            ),
            'mytheme_save_interview_shop'
        )
    ) {
        return;
    }

    // 自動保存を除外
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 沿革の編集権限を確認
    if (
        get_post_type($post_id) !== 'shop_interview' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    if (!isset($_POST['interview_shop_id'])) {
        return;
    }

    $shop_id = absint($_POST['interview_shop_id']);

    // 選択解除なら紐付けを削除
    if ($shop_id === 0) {
        delete_post_meta($post_id, '_shop_interview_shop_id');
        return;
    }

    // 実在するショップか確認
    if (
        get_post_type($shop_id) !== 'shop' ||
        !current_user_can('edit_post', $shop_id)
    ) {
        return;
    }

    update_post_meta(
        $post_id,
        '_shop_interview_shop_id',
        $shop_id
    );
}
add_action('save_post_shop_interview', 'mytheme_save_interview_shop');

/**
 * インタビュー編集画面に「質問」を追加
 */
mytheme_add_shop_interview_meta_box(
    'interview_question',
    '質問',
    'mytheme_render_interview_question_field'
);

/**
 * インタビューの質問の入力欄を表示
 */
function mytheme_render_interview_question_field($post) {
    $question = get_post_meta($post->ID, '_shop_interview_question', true);

    wp_nonce_field('mytheme_save_interview_question', 'mytheme_interview_question_nonce');
    ?>
    <input
        type="text"
        id="interview_question"
        name="interview_question"
        style="width: 100%;"
        value="<?php echo esc_attr($question); ?>"
    />
    <?php
}

/**
 * インタビューの質問を保存
 */
function mytheme_save_interview_question($post_id) {
    if (
        !isset($_POST['mytheme_interview_question_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_interview_question_nonce'])),
            'mytheme_save_interview_question'
        )
    ) {
        return;
    }

    $question = sanitize_textarea_field(wp_unslash($_POST['interview_question']));

    if ($question === '') {
        delete_post_meta($post_id, '_shop_interview_question');
        return;
    }

    update_post_meta($post_id, '_shop_interview_question', $question);
}
add_action('save_post_shop_interview', 'mytheme_save_interview_question');

/**
 * インタビュー編集画面に「回答」を追加
 */
mytheme_add_shop_interview_meta_box(
    'interview_answer',
    '回答',
    'mytheme_render_interview_answer_field'
);

/**
 * インタビューの回答の入力欄を表示
 */
function mytheme_render_interview_answer_field($post) {
    $answer = get_post_meta($post->ID, '_shop_interview_answer', true);

    wp_nonce_field('mytheme_save_interview_answer', 'mytheme_interview_answer_nonce');
    ?>
    <label for="interview_answer">回答</label>
    <textarea
        id="interview_answer"
        name="interview_answer"
        style="width: 100%;"
    ><?php echo esc_textarea($answer); ?></textarea>
    <?php
}

/**
 * インタビューの回答を保存
 */
function mytheme_save_interview_answer($post_id) {
    if (
        !isset($_POST['mytheme_interview_answer_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_interview_answer_nonce'])),
            'mytheme_save_interview_answer'
        )
    ) {
        return;
    }

    $answer = sanitize_textarea_field(wp_unslash($_POST['interview_answer']));

    if ($answer === '') {
        delete_post_meta($post_id, '_shop_interview_answer');
        return;
    }

    update_post_meta($post_id, '_shop_interview_answer', $answer);
}
add_action('save_post_shop_interview', 'mytheme_save_interview_answer');

/**
 * ショップ一覧の列を設定
 */
function mytheme_shop_admin_columns($columns) {
    $new_columns = [];

    // ショップ名を先頭にする
    $new_columns['shop_name'] = 'ショップ名';

    // 既存の列を追加（タイトル列は除外）
    foreach ($columns as $key => $label) {
        if ($key !== 'title') {
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
    if ($column !== 'shop_name') {
        return;
    }

    $shop_name = get_post_meta($post_id, '_shop_name', true);

    echo esc_html($shop_name ?: '（ショップ名未入力）');
}
add_action('manage_shop_posts_custom_column', 'mytheme_shop_admin_column_content', 10, 2);

/**
 * ショップ名の並び替えを有効にする
 */
function mytheme_shop_sortable_columns($columns) {
    $columns['shop_name'] = 'shop_name';
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
}
add_action('pre_get_posts', 'mytheme_shop_orderby');

/**
 * 沿革一覧の列を設定
 */
function mytheme_history_admin_columns($columns) {
    $new_columns = [];

    // 沿革を先頭にする
    $new_columns['history_detail'] = '沿革';

    // 既存の列を追加（タイトル列は除外）
    foreach ($columns as $key => $label) {
        if ($key !== 'title') {
            $new_columns[$key] = $label;
        }
    }

    return $new_columns;
}
add_filter('manage_shop_history_posts_columns', 'mytheme_history_admin_columns');

/**
 * 沿革一覧に沿革を表示
 */
function mytheme_history_admin_column_content($column, $post_id) {
    if ($column !== 'history_detail') {
        return;
    }

    $history_detail = get_post_meta($post_id, '_shop_history_detail', true);

    echo esc_html($history_detail ?: '（沿革未入力）');
}
add_action('manage_shop_history_posts_custom_column', 'mytheme_history_admin_column_content', 10, 2);

/**
 * 沿革の並び替えを有効にする
 */
function mytheme_shop_history_sortable_columns($columns) {
    $columns['history_detail'] = 'history_detail';
    return $columns;
}
add_filter('manage_edit-shop_history_sortable_columns', 'mytheme_shop_history_sortable_columns');

/**
 * 沿革で並び替える
 */
function mytheme_shop_history_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') !== 'shop_history') {
        return;
    }

    if ($query->get('orderby') === 'history_detail') {
        $query->set('meta_key', '_shop_history_detail');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'mytheme_shop_history_orderby');

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

function mytheme_add_google_tag_manager() {
    ?>
    <!-- Google Tag Manager -->
    <script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;
    f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WTRGTJP');
    </script>
    <!-- End Google Tag Manager -->
    <?php
}
add_action('wp_head', 'mytheme_add_google_tag_manager', 1);
