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
add_action('save_post', 'mytheme_save_shop_capital');

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
add_action('save_post', 'mytheme_save_shop_net_sales');

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
add_action('save_post', 'mytheme_save_shop_business_content');

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
add_action('save_post', 'mytheme_save_shop_license_number');

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
add_action('save_post', 'mytheme_save_shop_employee');

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
add_action('save_post', 'mytheme_save_shop_organizations');

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
add_action('save_post', 'mytheme_save_shop_establishment');

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
add_action('save_post', 'mytheme_save_shop_base');

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
add_action('save_post', 'mytheme_save_shop_relationship');