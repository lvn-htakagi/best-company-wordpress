<?php
require_once get_theme_file_path('/inc/save-field.php');

define('LVN_MATCH_URL', 'https://www.lvnmatch.com');
define('BEST_COMPANY_URL', LVN_MATCH_URL . '/best/');

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

/**
 * ショップ編集画面に「名前」を追加
 */
function mytheme_add_shop_name_field() {
    add_meta_box(
        'shop_name',
        '名前',
        'mytheme_render_shop_name_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_name_field');

/**
 * 名前の入力欄を表示
 */
function mytheme_render_shop_name_field($post) {
    $name = get_post_meta($post->ID, '_shop_name', true);

    wp_nonce_field('mytheme_save_shop_name', 'mytheme_shop_name_nonce');
    ?>
    <label for="shop_name">名前</label>
    <input
        type="text"
        id="shop_name"
        name="shop_name"
        style="width: 100%;"
        value="<?php echo esc_attr($name); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「詳細」を追加
 */
function mytheme_add_shop_detail_field() {
    add_meta_box(
        'shop_detail',
        '詳細',
        'mytheme_render_shop_detail_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_detail_field');

/**
 * 詳細の入力欄を表示
 */
function mytheme_render_shop_detail_field($post) {
    $comment = get_post_meta($post->ID, '_shop_detail', true);

    wp_nonce_field('mytheme_save_shop_detail', 'mytheme_shop_detail_nonce');
    ?>
    <label for="shop_detail">詳細</label>
    <textarea
        id="shop_detail"
        name="shop_detail"
        style="width: 100%;"
    ><?php echo esc_textarea($comment); ?></textarea>
    <?php
}

/**
 * 詳細を保存
 */
function mytheme_save_shop_detail($post_id) {
    if (
        !isset($_POST['mytheme_shop_detail_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_detail_nonce'])),
            'mytheme_save_shop_detail'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $comment = sanitize_text_field(wp_unslash($_POST['shop_detail']));

    if ($comment === '') {
        delete_post_meta($post_id, '_shop_detail');
        return;
    }

    update_post_meta($post_id, '_shop_detail', $comment);
}
add_action('save_post_shop', 'mytheme_save_shop_detail');

/**
 * ショップ編集画面に「画像」を追加
 */
function mytheme_add_shop_image_field() {
    add_meta_box(
        'shop_image',
        '画像',
        'mytheme_render_shop_image_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_image_field');

/**
 * 画像の入力欄を表示
 */
function mytheme_render_shop_image_field($post) {
    $image_id = get_post_meta($post->ID, '_shop_image', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

    wp_nonce_field(
        'mytheme_save_shop_image',
        'mytheme_shop_image_nonce'
    );

    echo '<div id="shop-image-preview">';

    if ($image_url) {
        echo '<img src="' . esc_url($image_url) . '" style="max-width: 300px; height: auto;">';
    }

    echo '</div>';

    echo '<input type="hidden" id="shop-image-id" name="shop_image" value="' . esc_attr($image_id) . '">';
    echo '<input type="hidden" id="shop-image-removed" name="shop_image_removed" value="0">';

    echo '<button type="button" class="button" id="shop-image-select">画像を選択</button> ';
    echo '<button type="button" class="button" id="shop-image-remove">画像を削除</button>';
}

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
        'mytheme-shop-media',
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
 * ショップ編集画面に「都道府県」を追加
 */
function mytheme_add_shop_prefecture_field() {
    add_meta_box(
        'shop_prefecture',
        '都道府県',
        'mytheme_render_shop_prefecture_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_prefecture_field');

/**
 * 都道府県の選択肢
 */
function mytheme_get_prefectures() {
    return [
        1 => '北海道',
        2 => '青森県',
        3 => '岩手県',
        4 => '宮城県',
        5 => '秋田県',
        6 => '山形県',
        7 => '福島県',
    ];
}

/**
 * 都道府県の入力欄を表示
 */
function mytheme_render_shop_prefecture_field($post) {
    $prefecture = get_post_meta($post->ID, '_shop_prefecture', true);

    wp_nonce_field(
        'mytheme_save_shop_prefecture',
        'mytheme_shop_prefecture_nonce'
    );

    echo '<select id="shop_prefecture" name="shop_prefecture">';
    echo '<option value="">選択してください</option>';

    foreach (mytheme_get_prefectures() as $key => $name) {
        echo '<option value="' . esc_attr($key) . '"'
            . selected($prefecture, $key, false) . '>';
        echo esc_html($name);
        echo '</option>';
    }

    echo '</select>';
}

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
 * ショップ編集画面に「年」を追加
 */
function mytheme_add_shop_year_field() {
    add_meta_box(
        'shop_year',
        '年',
        'mytheme_render_shop_year_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_year_field');

/**
 * 年の入力欄を表示
 */
function mytheme_render_shop_year_field($post) {
    $year = get_post_meta($post->ID, '_shop_year', true);

    wp_nonce_field('mytheme_save_shop_year', 'mytheme_shop_year_nonce');
    ?>
    <label for="shop_year">年（例：2026）</label>
    <input
        type="number"
        id="shop_year"
        name="shop_year"
        value="<?php echo esc_attr($year); ?>"
        min="1900"
        max="2100"
        step="1"
        style="width: 100%;"
    >
    <?php
}

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
 * ショップ編集画面に「編集部コメント」を追加
 */
function mytheme_add_shop_comment_field() {
    add_meta_box(
        'shop_comment',
        '編集部コメント',
        'mytheme_render_shop_comment_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_comment_field');

/**
 * 編集部コメントの入力欄を表示
 */
function mytheme_render_shop_comment_field($post) {
    $comment = get_post_meta($post->ID, '_shop_comment', true);

    wp_nonce_field('mytheme_save_shop_comment', 'mytheme_shop_comment_nonce');
    ?>
    <label for="shop_comment">編集部コメント</label>
    <textarea
        id="shop_comment"
        name="shop_comment"
        style="width: 100%;"
    ><?php echo esc_textarea($comment); ?></textarea>
    <?php
}

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
 * ショップ編集画面に「企業紹介」を追加
 */
function mytheme_add_shop_introduction_field() {
    add_meta_box(
        'shop_introduction',
        '企業紹介',
        'mytheme_render_shop_introduction_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_introduction_field');

/**
 * 企業紹介の入力欄を表示
 */
function mytheme_render_shop_introduction_field($post) {
    $introduction = get_post_meta($post->ID, '_shop_introduction', true);

    wp_nonce_field('mytheme_save_shop_introduction', 'mytheme_shop_introduction_nonce');
    ?>
    <label for="shop_introduction">企業紹介</label>
    <textarea
        id="shop_introduction"
        name="shop_introduction"
        style="width: 100%;"
    ><?php echo esc_textarea($introduction); ?></textarea>
    <?php
}

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
 * ショップ編集画面に「インタビュー」を追加
 */
function mytheme_add_shop_interview_field() {
    add_meta_box(
        'shop_interview',
        'インタビュー',
        'mytheme_render_shop_interview_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_interview_field');

/**
 * インタビューの入力欄を表示
 */
function mytheme_render_shop_interview_field($post) {
    $interview = get_post_meta($post->ID, '_shop_interview', true);

    wp_nonce_field('mytheme_save_shop_interview', 'mytheme_shop_interview_nonce');
    ?>
    <label for="shop_interview">インタビュー</label>
    <textarea
        id="shop_interview"
        name="shop_interview"
        style="width: 100%;"
    ><?php echo esc_textarea($interview); ?></textarea>
    <?php
}

/**
 * インタビューを保存
 */
function mytheme_save_shop_interview($post_id) {
    if (
        !isset($_POST['mytheme_shop_interview_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_interview_nonce'])),
            'mytheme_save_shop_interview'
        )
    ) {
        return;
    }

    if (!mytheme_can_save_shop($post_id)) {
        return;
    }

    $interview = sanitize_text_field(wp_unslash($_POST['shop_interview']));

    if ($interview === '') {
        delete_post_meta($post_id, '_shop_interview');
        return;
    }

    update_post_meta($post_id, '_shop_interview', $interview);
}
add_action('save_post_shop', 'mytheme_save_shop_interview');

/**
 * ショップ編集画面に「会社名(企業概要)」を追加
 */
function mytheme_add_shop_company_name_field() {
    add_meta_box(
        'shop_company_name',
        '会社名',
        'mytheme_render_shop_company_name_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_company_name_field');

/**
 * 会社名(企業概要)の入力欄を表示
 */
function mytheme_render_shop_company_name_field($post) {
    $company_name = get_post_meta($post->ID, '_shop_company_name', true);

    wp_nonce_field('mytheme_save_shop_company_name', 'mytheme_shop_company_name_nonce');
    ?>
    <label for="shop_company_name">会社名(企業概要)</label>
    <input
        type="text"
        id="shop_company_name"
        name="shop_company_name"
        style="width: 100%;"
        value="<?php echo esc_attr($company_name); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「所在地(企業概要)」を追加
 */
function mytheme_add_shop_address_field() {
    add_meta_box(
        'shop_address',
        '所在地',
        'mytheme_render_shop_address_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_address_field');

/**
 * 所在地(企業概要)の入力欄を表示
 */
function mytheme_render_shop_address_field($post) {
    $address = get_post_meta($post->ID, '_shop_address', true);

    wp_nonce_field('mytheme_save_shop_address', 'mytheme_shop_address_nonce');
    ?>
    <label for="shop_address">所在地(企業概要)</label>
    <input
        type="text"
        id="shop_address"
        name="shop_address"
        style="width: 100%;"
        value="<?php echo esc_attr($address); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「電話番号(企業概要)」を追加
 */
function mytheme_add_shop_tel_field() {
    add_meta_box(
        'shop_tel',
        '電話番号',
        'mytheme_render_shop_tel_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_tel_field');

/**
 * 電話番号(企業概要)の入力欄を表示
 */
function mytheme_render_shop_tel_field($post) {
    $tel = get_post_meta($post->ID, '_shop_tel', true);

    wp_nonce_field('mytheme_save_shop_tel', 'mytheme_shop_tel_nonce');
    ?>
    <label for="shop_tel">電話番号(企業概要)</label>
    <input
        type="text"
        id="shop_tel"
        name="shop_tel"
        style="width: 100%;"
        value="<?php echo esc_attr($tel); ?>"
    />
    <?php
}

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

    if(!preg_match("/^0[0-9]{9}$/i",$_POST['shop_tel'])) {
        return;
    }

    $tel = sanitize_text_field(wp_unslash($_POST['shop_tel']));

    if ($tel === '') {
        delete_post_meta($post_id, '_shop_tel');
        return;
    }

    update_post_meta($post_id, '_shop_tel', $tel);
}
add_action('save_post_shop', 'mytheme_save_shop_tel');

/**
 * ショップ編集画面に「FAX番号(企業概要)」を追加
 */
function mytheme_add_shop_fax_field() {
    add_meta_box(
        'shop_fax',
        'FAX番号',
        'mytheme_render_shop_fax_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_fax_field');

/**
 * 会社名(企業概要)の入力欄を表示
 */
function mytheme_render_shop_fax_field($post) {
    $fax = get_post_meta($post->ID, '_shop_fax', true);

    wp_nonce_field('mytheme_save_shop_fax', 'mytheme_shop_fax_nonce');
    ?>
    <label for="shop_fax">FAX番号(企業概要)</label>
    <input
        type="text"
        id="shop_fax"
        name="shop_fax"
        style="width: 100%;"
        value="<?php echo esc_attr($fax); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「URL(企業概要)」を追加
 */
function mytheme_add_shop_url_field() {
    add_meta_box(
        'shop_url',
        'URL',
        'mytheme_render_shop_url_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_url_field');

/**
 * URL(企業概要)の入力欄を表示
 */
function mytheme_render_shop_url_field($post) {
    $url = get_post_meta($post->ID, '_shop_url', true);

    wp_nonce_field('mytheme_save_shop_url', 'mytheme_shop_url_nonce');
    ?>
    <label for="shop_url">URL(企業概要)</label>
    <input
        type="text"
        id="shop_url"
        name="shop_url"
        style="width: 100%;"
        value="<?php echo esc_attr($url); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「資本金(企業概要)」を追加
 */
function mytheme_add_shop_capital_field() {
    add_meta_box(
        'shop_capital',
        '資本金',
        'mytheme_render_shop_capital_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_capital_field');

/**
 * 資本金(企業概要)の入力欄を表示
 */
function mytheme_render_shop_capital_field($post) {
    $capital = get_post_meta($post->ID, '_shop_capital', true);

    wp_nonce_field('mytheme_save_shop_capital', 'mytheme_shop_capital_nonce');
    ?>
    <label for="shop_capital">資本金(企業概要)</label>
    <input
        type="text"
        id="shop_capital"
        name="shop_capital"
        style="width: 100%;"
        value="<?php echo esc_attr($capital); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「売上高(企業概要)」を追加
 */
function mytheme_add_shop_net_sales_field() {
    add_meta_box(
        'shop_net_sales',
        '売上高',
        'mytheme_render_shop_net_sales_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_net_sales_field');

/**
 * 売上高(企業概要)の入力欄を表示
 */
function mytheme_render_shop_net_sales_field($post) {
    $net_sales = get_post_meta($post->ID, '_shop_net_sales', true);

    wp_nonce_field('mytheme_save_shop_net_sales', 'mytheme_shop_net_sales_nonce');
    ?>
    <label for="shop_net_sales">売上高(企業概要)</label>
    <input
        type="text"
        id="shop_net_sales"
        name="shop_net_sales"
        style="width: 100%;"
        value="<?php echo esc_attr($net_sales); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「事業内容(企業概要)」を追加
 */
function mytheme_add_shop_business_content_field() {
    add_meta_box(
        'shop_business_content',
        '事業内容',
        'mytheme_render_shop_business_content_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_business_content_field');

/**
 * 事業内容(企業概要)の入力欄を表示
 */
function mytheme_render_shop_business_content_field($post) {
    $business_content = get_post_meta($post->ID, '_shop_business_content', true);

    wp_nonce_field('mytheme_save_shop_business_content', 'mytheme_shop_business_content_nonce');
    ?>
    <label for="shop_business_content">事業内容(企業概要)</label>
    <textarea
        id="shop_business_content"
        name="shop_business_content"
        style="width: 100%;"
    ><?php echo esc_textarea($business_content); ?></textarea>
    <?php
}

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
 * ショップ編集画面に「許認可・免許番号(企業概要)」を追加
 */
function mytheme_add_shop_license_number_field() {
    add_meta_box(
        'shop_license_number',
        '許認可・免許番号',
        'mytheme_render_shop_license_number_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_license_number_field');

/**
 * 許認可・免許番号(企業概要)の入力欄を表示
 */
function mytheme_render_shop_license_number_field($post) {
    $license_number = get_post_meta($post->ID, '_shop_license_number', true);

    wp_nonce_field('mytheme_save_shop_license_number', 'mytheme_shop_license_number_nonce');
    ?>
    <label for="shop_license_number">許認可・免許番号(企業概要)</label>
    <textarea
        id="shop_license_number"
        name="shop_license_number"
        style="width: 100%;"
    ><?php echo esc_textarea($license_number); ?></textarea>
    <?php
}

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
 * ショップ編集画面に「加盟団体(企業概要)」を追加
 */
function mytheme_add_shop_organizations_field() {
    add_meta_box(
        'shop_organizations',
        '加盟団体',
        'mytheme_render_shop_organizations_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_organizations_field');

/**
 * 事業内容(企業概要)の入力欄を表示
 */
function mytheme_render_shop_organizations_field($post) {
    $organizations = get_post_meta($post->ID, '_shop_organizations', true);

    wp_nonce_field('mytheme_save_shop_organizations', 'mytheme_shop_organizations_nonce');
    ?>
    <label for="shop_organizations">加盟団体(企業概要)</label>
    <textarea
        id="shop_organizations"
        name="shop_organizations"
        style="width: 100%;"
    ><?php echo esc_textarea($organizations); ?></textarea>
    <?php
}

/**
 * ショップ編集画面に「従業員数(企業概要)」を追加
 */
function mytheme_add_shop_employee_field() {
    add_meta_box(
        'shop_employee',
        '従業員数',
        'mytheme_render_shop_employee_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_employee_field');

/**
 * 従業員数(企業概要)の入力欄を表示
 */
function mytheme_render_shop_employee_field($post) {
    $employee = get_post_meta($post->ID, '_shop_employee', true);

    wp_nonce_field('mytheme_save_shop_employee', 'mytheme_shop_employee_nonce');
    ?>
    <label for="shop_employee">従業員数(企業概要)</label>
    <input
        type="text"
        id="shop_employee"
        name="shop_employee"
        style="width: 100%;"
        value="<?php echo esc_attr($employee); ?>"
    />
    <?php
}
add_action('save_post_shop', 'mytheme_save_shop_organizations');
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
 * ショップ編集画面に「設立年月(企業概要)」を追加
 */
function mytheme_add_shop_establishment_field() {
    add_meta_box(
        'shop_establishment',
        '設立年月',
        'mytheme_render_shop_establishment_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_establishment_field');

/**
 * 設立年月(企業概要)の入力欄を表示
 */
function mytheme_render_shop_establishment_field($post) {
    $establishment = get_post_meta($post->ID, '_shop_establishment', true);

    wp_nonce_field('mytheme_save_shop_establishment', 'mytheme_shop_establishment_nonce');
    ?>
    <label for="shop_establishment">設立年月(企業概要)</label>
    <input
        type="text"
        id="shop_establishment"
        name="shop_establishment"
        style="width: 100%;"
        value="<?php echo esc_attr($establishment); ?>"
    />
    <?php
}

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
 * ショップ編集画面に「拠点(企業概要)」を追加
 */
function mytheme_add_shop_base_field() {
    add_meta_box(
        'shop_base',
        '拠点',
        'mytheme_render_shop_base_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_base_field');

/**
 * 拠点(企業概要)の入力欄を表示
 */
function mytheme_render_shop_base_field($post) {
    $base = get_post_meta($post->ID, '_shop_base', true);

    wp_nonce_field('mytheme_save_shop_base', 'mytheme_shop_base_nonce');
    ?>
    <label for="shop_base">拠点(企業概要)</label>
    <textarea
        id="shop_base"
        name="shop_base"
        style="width: 100%;"
    ><?php echo esc_textarea($base); ?></textarea>
    <?php
}

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
 * ショップ編集画面に「関係会社(企業概要)」を追加
 */
function mytheme_add_shop_relationship_field() {
    add_meta_box(
        'shop_relationship',
        '関係会社',
        'mytheme_render_shop_relationship_field',
        'shop',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_shop_relationship_field');

/**
 * 関係会社(企業概要)の入力欄を表示
 */
function mytheme_render_shop_relationship_field($post) {
    $relationship = get_post_meta($post->ID, '_shop_relationship', true);

    wp_nonce_field('mytheme_save_shop_relationship', 'mytheme_shop_relationship_nonce');
    ?>
    <label for="shop_relationship">関係会社(企業概要)</label>
    <textarea
        id="shop_relationship"
        name="shop_relationship"
        style="width: 100%;"
    ><?php echo esc_textarea($relationship); ?></textarea>
    <?php
}

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
 * 沿革編集画面に「ショップ」を追加
 */
function mytheme_add_history_shop_field() {
    add_meta_box(
        'history_shop',
        'ショップ',
        'mytheme_render_history_shop_field',
        'shop_history',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_history_shop_field');

/**
 * ショップ選択欄を表示
 */
function mytheme_render_history_shop_field($post) {
    $selected_shop_id = get_post_meta(
        $post->ID,
        '_shop_history_shop_id',
        true
    );

    wp_nonce_field(
        'mytheme_save_history_shop',
        'mytheme_history_shop_nonce'
    );

    // 登録済みのショップを取得
    $shops = get_posts([
        'post_type'      => 'shop',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ]);

    echo '<select name="history_shop_id" id="history_shop_id">';
    echo '<option value="">ショップを選択してください</option>';

    foreach ($shops as $shop) {
        $shop_name = get_post_meta(
            $shop->ID,
            '_shop_name',
            true
        );

        // ショップ名が未入力の場合の表示
        if ($shop_name === '') {
            $shop_name = 'ショップID: ' . $shop->ID;
        }

        echo '<option value="' . esc_attr($shop->ID) . '"'
            . selected(
                (string) $selected_shop_id,
                (string) $shop->ID,
                false
            )
            . '>';
        echo esc_html($shop_name);
        echo '</option>';
    }

    echo '</select>';
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
function mytheme_add_history_year_field() {
    add_meta_box(
        'history_year',
        '年',
        'mytheme_render_history_year_field',
        'shop_history',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_history_year_field');

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
function mytheme_add_history_month_field() {
    add_meta_box(
        'history_month',
        '月',
        'mytheme_render_history_month_field',
        'shop_history',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_history_month_field');

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
function mytheme_add_history_detail_field() {
    add_meta_box(
        'history_detail',
        '沿革',
        'mytheme_render_history_detail_field',
        'shop_history',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'mytheme_add_history_detail_field');

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
    if (get_query_var('shop_year') !== '') {
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
}
add_action('wp_enqueue_scripts', 'mytheme_enqueue_fadein_script');

function mytheme_enqueue_shop_archive_styles() {
    if (get_query_var('shop_year') !== '') {
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
