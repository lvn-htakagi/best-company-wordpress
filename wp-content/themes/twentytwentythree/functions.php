<?php
require_once get_theme_file_path('/inc/save-field.php');

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

function mytheme_remove_shop_editor_support() {
    remove_post_type_support('shop', 'title');
    remove_post_type_support('shop', 'editor');
    remove_post_type_support('shop', 'thumbnail');
}
add_action('init', 'mytheme_remove_shop_editor_support', 20);

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
add_action('save_post', 'mytheme_save_shop_year');

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
add_action('save_post', 'mytheme_save_shop_comment');

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
add_action('save_post', 'mytheme_save_shop_introduction');

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
add_action('save_post', 'mytheme_save_shop_interview');

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
add_action('save_post', 'mytheme_save_shop_company_name');

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
add_action('save_post', 'mytheme_save_shop_address');

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
add_action('save_post', 'mytheme_save_shop_tel');

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
add_action('save_post', 'mytheme_save_shop_fax');

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
add_action('save_post', 'mytheme_save_shop_url');



