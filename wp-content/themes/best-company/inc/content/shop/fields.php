<?php
/**
 * ショップ編集画面の入力欄
 *
 * ショップ編集画面に表示する各入力欄の内容を定義する。
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
 * 入力欄の共通表示処理は
 * inc/common/fields.php で定義。
 */

require_once get_theme_file_path('/inc/common/add-input.php');

/**
 * 名前入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */

function mytheme_render_shop_name_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_name',
        'mytheme_shop_name_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_name',
        '名前'
    );
}

/**
 * 代表者名入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_president_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_president',
        'mytheme_shop_president_nonce'
    );
    
    mytheme_render_shop_text_field(
        $post,
        'shop_president',
        '代表者名'
    );
}

/**
 * 経歴入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_career_field($post) {
    $comment = get_post_meta($post->ID, '_shop_career', true);

    wp_nonce_field('mytheme_save_shop_career', 'mytheme_shop_career_nonce');
    ?>
    <label for="shop_career">経歴</label>
    <textarea
        id="shop_career"
        name="shop_career"
        style="width: 100%;"
    ><?php echo esc_textarea($comment); ?></textarea>
    <?php
}

/**
 * 顔写真の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_face_field($post) {
    $image_id = get_post_meta($post->ID, '_shop_face', true);
    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

    wp_nonce_field(
        'mytheme_save_shop_face',
        'mytheme_shop_face_nonce'
    );

    echo '<div id="shop-face-image-preview">';

    if ($image_url) {
        echo '<img src="' . esc_url($image_url) . '" style="max-width: 300px; height: auto;">';
    }

    echo '</div>';

    echo '<input type="hidden" id="shop-face-id" name="shop_face" value="' . esc_attr($image_id) . '">';
    echo '<input type="hidden" id="shop-face-removed" name="shop_face_removed" value="0">';

    echo '<button type="button" class="button" id="shop-face-select">画像を選択</button> ';
    echo '<button type="button" class="button" id="shop-face-remove">画像を削除</button>';
}

/**
 * 画像の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
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
 * 
 * @param WP_Post $post 投稿オブジェクト
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
 * 年の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
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
 * 編集部コメントの入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
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
 * 企業紹介の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
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
 * カスタムIDの入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_custome_id_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_custome_id',
        'mytheme_shop_custome_id_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_custome_id',
        'カスタムID'
    );
}

/**
 * ギャラリー入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_gallery_field($post) {
    $shop_gallery_ids = get_post_meta(
        $post->ID,
        '_shop_gallery_ids',
        true
    );

    if (!is_array($shop_gallery_ids)) {
        $shop_gallery_ids = [];
    }

    wp_nonce_field(
        'mytheme_save_shop_gallery',
        'mytheme_shop_gallery_nonce'
    );
    ?>

    <p>画像を追加して、ドラッグで順番を変更できます。</p>
    <p>
        画像は複数選択できます。<br>
        Mac：Commandキーを押しながら画像をクリック<br>
        Windows：Ctrlキーを押しながら画像をクリック<br>
        ※ 選択後、「画像を追加」ボタンをクリックしてください。
    </p>

    <input
        type="hidden"
        id="mytheme_shop_gallery_ids"
        name="mytheme_shop_gallery_ids"
        value="<?php echo esc_attr(implode(',', array_map('absint', $shop_gallery_ids))); ?>"
    >

    <div id="mytheme-shop-gallery-preview">
        <?php foreach ($shop_gallery_ids as $image_id) : ?>
            <?php
            $image_id = absint($image_id);

            if (!$image_id || !wp_attachment_is_image($image_id)) {
                continue;
            }
            ?>
            <div class="mytheme-shop-gallery-item"
                data-id="<?php echo esc_attr($image_id); ?>">
                <?php
                echo wp_get_attachment_image(
                    $image_id,
                    'thumbnail'
                );
                ?>
                <button type="button" class="button mytheme-shop-gallery-remove">
                    削除
                </button>
            </div>
        <?php endforeach; ?>
    </div>

    <p>
        <button type="button" class="button button-primary"
                id="mytheme-shop-gallery-add">
            画像を追加
        </button>
    </p>

    <style>
        #mytheme-shop-gallery-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .mytheme-shop-gallery-item {
            width: 120px;
            padding: 6px;
            border: 1px solid #ccc;
            background: #fff;
            cursor: move;
        }

        .mytheme-shop-gallery-item img {
            display: block;
            width: 100%;
            height: 100px;
            object-fit: cover;
            margin-bottom: 6px;
        }
    </style>

    <?php
}

/**
 * 会社名の入力欄を表示
 */
function mytheme_render_shop_company_name_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_company_name',
        'mytheme_shop_company_name_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_company_name',
        '会社名'
    );
}

/**
 * 所在地(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_address_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_address',
        'mytheme_shop_address_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_address',
        '所在地(企業概要)'
    );
}

/**
 * 電話番号(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_tel_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_tel',
        'mytheme_shop_tel_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_tel',
        '電話番号(企業概要)'
    );
}

/**
 * FAX番号(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_fax_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_fax',
        'mytheme_shop_fax_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_fax',
        'FAX番号(企業概要)'
    );
}

/**
 * URL(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_url_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_url',
        'mytheme_shop_url_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_url',
        'URL(企業概要)'
    );
}

/**
 * 資本金(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_capital_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_capital',
        'mytheme_shop_capital_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_capital',
        '資本金(企業概要)'
    );
}

/**
 * 売上高(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_net_sales_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_net_sales',
        'mytheme_shop_net_sales_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_net_sales',
        '売上高(企業概要)'
    );
}

/**
 * 事業内容(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_business_content_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_business_content',
        'mytheme_shop_business_content_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_business_content',
        '事業内容(企業概要)'
    );
}

/**
 * 許認可・免許番号(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_license_number_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_license_number',
        'mytheme_shop_license_number_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_license_number',
        '許認可・免許番号(企業概要)'
    );
}

/**
 * 加盟団体(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_organizations_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_organizations',
        'mytheme_shop_organizations_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_organizations',
        '加盟団体(企業概要)'
    );
}

/**
 * 設立年月(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_establishment_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_establishment',
        'mytheme_shop_establishment_nonce'
    );

    mytheme_render_shop_text_field(
        $post,
        'shop_establishment',
        '設立年月(企業概要)'
    );
}

/**
 * 拠点(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_base_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_base',
        'mytheme_shop_base_nonce'
    );

    mytheme_render_shop_textarea_field(
        $post,
        'shop_base',
        '拠点(企業概要)'
    );
}

/**
 * 関係会社(企業概要)の入力欄を表示
 * 
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_relationship_field($post) {
    mytheme_render_shop_nonce(
        'mytheme_save_shop_relationship',
        'mytheme_shop_relationship_nonce'
    );

    mytheme_render_shop_textarea_field(
        $post,
        'shop_relationship',
        '関係会社(企業概要)'
    );
}