<?php
/**
 * ショップ編集画面の保存処理
 *
 * ショップ編集画面に入力されたデータを保存する。
 *
 * 登録する項目
 * - 名前
 * - 代表者名
 * - 略歴
 * - 顔写真
 * - 画像
 * - 都道府県
 * - 年
 * - 編集部コメント
 * - 企業紹介
 * - カスタマーID
 * - フォトギャラリー
 * - 沿革
 * - インタビュー
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
 * 入力欄の共通登録処理は
 * inc/common/save-meta-field.php で定義。
 */

require_once get_theme_file_path('/inc/common/save-meta-field.php');

/**
 * IDを保存
 *
 * @param int $post_id 投稿ID
 */
function mytheme_save_post_name($post_id) {

    // nonce確認
    if (
        !isset($_POST['mytheme_post_name_nonce']) ||
        !wp_verify_nonce(
            $_POST['mytheme_post_name_nonce'],
            'mytheme_save_post_name'
        )
    ) {
        return;
    }

    // 自動保存は除外
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限確認
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 数字のみ受け付ける
    if (
        !isset($_POST['shop_url_id']) ||
        !preg_match('/^[0-9]+$/', $_POST['shop_url_id'])
    ) {
        return;
    }

    $post_name = sanitize_text_field($_POST['shop_url_id']);

    // URL末尾を更新
    if (get_post_field('post_name', $post_id) !== $post_name) {
        wp_update_post([
            'ID'        => $post_id,
            'post_name' => $post_name,
        ]);
    }
}
add_action('save_post_shop', 'mytheme_save_post_name');

/**
 * 会社名を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_name($post_id) {
    mytheme_save_text_meta_field(
    $post_id ,
    'mytheme_shop_name_nonce',
    'mytheme_save_shop_name',
    'shop_name',
    '_shop_name',
    'shop'
);
}
add_action('save_post_shop', 'mytheme_save_shop_name');

/**
 * 都道府県を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_prefecture($post_id) {
    mytheme_save_select_field(
        $post_id,
        'mytheme_shop_prefecture_nonce',
        'mytheme_save_shop_prefecture',
        'shop_prefecture',
        '_shop_prefecture',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_prefecture');


/**
 * 年を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_year($post_id) {
    mytheme_save_select_field(
        $post_id,
        'mytheme_shop_year_nonce',
        'mytheme_save_shop_year',
        'shop_year',
        '_shop_year',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_year');

/**
 * 代表者名を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_president($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_president_nonce',
        'mytheme_save_shop_president',
        'shop_president',
        '_shop_president',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_president');

/**
 * 略歴を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_career($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_career_nonce',
        'mytheme_save_shop_career',
        'shop_career',
        '_shop_career',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_career');

/**
 * 編集部コメントを保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_comment($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_comment_nonce',
        'mytheme_save_shop_comment',
        'shop_comment',
        '_shop_comment',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_comment');

/**
 * 企業紹介を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_introduction($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_introduction_nonce',
        'mytheme_save_shop_introduction',
        'shop_introduction',
        '_shop_introduction',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_introduction');

/**
 * カスタマーIDを保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_custome_id($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_custome_id_nonce',
        'mytheme_save_shop_custome_id',
        'shop_custome_id',
        '_shop_custome_id',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_custome_id');

/**
 * 所在地(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_address($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_address_nonce',
        'mytheme_save_shop_address',
        'shop_address',
        '_shop_address',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_address');

/**
 * 電話番号(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_tel($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_tel_nonce',
        'mytheme_save_shop_tel',
        'shop_tel',
        '_shop_tel',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_tel');

/**
 * FAX番号(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_fax($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_fax_nonce',
        'mytheme_save_shop_fax',
        'shop_fax',
        '_shop_fax',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_fax');

/**
 * URL(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_url($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_url_nonce',
        'mytheme_save_shop_url',
        'shop_url',
        '_shop_url',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_url');

/**
 * 資本金(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_capital($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_capital_nonce',
        'mytheme_save_shop_capital',
        'shop_capital',
        '_shop_capital',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_capital');

/**
 * 売上高(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_net_sales($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_net_sales_nonce',
        'mytheme_save_shop_net_sales',
        'shop_net_sales',
        '_shop_net_sales',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_net_sales');

/**
 * 事業内容(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_business_content($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_business_content_nonce',
        'mytheme_save_shop_business_content',
        'shop_business_content',
        '_shop_business_content',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_business_content');

/**
 * 許認可・免許番号(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_license_number($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_license_number_nonce',
        'mytheme_save_shop_license_number',
        'shop_license_number',
        '_shop_license_number',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_license_number');

/**
 * 従業員数(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_employee($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_employee_nonce',
        'mytheme_save_shop_employee',
        'shop_employee',
        '_shop_employee',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_employee');

/**
 * 加盟団体(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_organizations($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_organizations_nonce',
        'mytheme_save_shop_organizations',
        'shop_organizations',
        '_shop_organizations',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_organizations');

/**
 * 設立年月(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_establishment($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_establishment_nonce',
        'mytheme_save_shop_establishment',
        'shop_establishment',
        '_shop_establishment',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_establishment');

/**
 * 拠点(企業概要)を保存
 */
function mytheme_save_shop_base($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_base_nonce',
        'mytheme_save_shop_base',
        'shop_base',
        '_shop_base',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_base');

/**
 * 関係会社(企業概要)を保存
 * 
 * @param int $post_id 投稿ID
 */
function mytheme_save_shop_relationship($post_id) {
    mytheme_save_text_meta_field(
        $post_id,
        'mytheme_shop_relationship_nonce',
        'mytheme_save_shop_relationship',
        'shop_relationship',
        '_shop_relationship',
        'shop'
    );
}
add_action('save_post_shop', 'mytheme_save_shop_relationship');

/**
 * 沿革を保存
 *
 * @param int $post_id ショップの投稿ID
 */
function mytheme_save_shop_histories($post_id) {

    // nonceの確認
    if (
        !isset($_POST['mytheme_shop_histories_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_histories_nonce'])),
            'mytheme_save_shop_histories'
        )
    ) {
        return;
    }

    // 自動保存・リビジョンを除外
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    // ショップの編集権限を確認
    if (
        get_post_type($post_id) !== 'shop' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    // 沿革の入力データが存在するか確認
    if (!isset($_POST['shop_histories']) || !is_array($_POST['shop_histories'])) {
        return;
    }

    $histories = wp_unslash($_POST['shop_histories']);

    // ショップの公開状態を取得
    $shop_status = get_post_status($post_id);

    if (!in_array($shop_status, ['publish', 'draft', 'pending', 'private'], true)) {
        $shop_status = 'draft';
    }

    // 現在紐付いている沿革を取得
    $existing_histories = get_posts([
        'post_type'      => 'shop_history',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_key'       => '_shop_history_shop_id',
        'meta_value'     => $post_id,
        'fields'         => 'ids',
    ]);

    $saved_history_ids = [];
    $order = 0;

    // 沿革を保存
    foreach ($histories as $history) {

        if (!is_array($history)) {
            continue;
        }

        $history_id = isset($history['id'])
            ? absint($history['id'])
            : 0;

        $year = isset($history['year'])
            ? sanitize_text_field($history['year'])
            : '';

        $month = isset($history['month'])
            ? sanitize_textarea_field($history['month'])
            : '';

        $detail = isset($history['detail'])
            ? sanitize_textarea_field($history['detail'])
            : '';

        // 新規投稿で質問・回答が両方空なら作成しない
        if (!$history_id && $year === '' && $month === '') {
            continue;
        }

        // 既存沿革の場合
        if ($history_id) {

            // このショップに紐付いている投稿か確認
            if (
                get_post_type($history_id) !== 'shop_history' ||
                (int) get_post_meta($history_id, '_shop_history_shop_id', true) !== (int) $post_id
            ) {
                continue;
            }

            $saved_history_ids[] = $history_id;

            $result = wp_update_post([
                'ID'          => $history_id,
                'post_status' => $shop_status,
            ], true);

            if (is_wp_error($result)) {
                continue;
            }

        } else {

            // 新規沿革を作成
            $history_id = wp_insert_post([
                'post_type'   => 'shop_history',
                'post_status' => $shop_status,
                'post_author' => get_current_user_id(),
            ], true);

            if (is_wp_error($history_id)) {
                continue;
            }

            // ショップとの紐付けを保存
            update_post_meta(
                $history_id,
                '_shop_history_shop_id',
                $post_id
            );

            $saved_history_ids[] = $history_id;
        }

        // 年・月・詳細を保存
        update_post_meta(
            $history_id,
            '_shop_history_year',
            $year
        );

        update_post_meta(
            $history_id,
            '_shop_history_month',
            $month
        );
        update_post_meta(
            $history_id,
            '_shop_history_detail',
            $detail
        );

        // 表示順を保存
        update_post_meta(
            $history_id,
            '_shop_history_order',
            $order
        );

        $order++;
    }

    // 画面から削除された沿革をゴミ箱へ移動
    foreach ($existing_histories as $existing_id) {

        if (!in_array((int) $existing_id, $saved_history_ids, true)) {
            wp_trash_post($existing_id);
        }
    }
}
add_action('save_post_shop', 'mytheme_save_shop_histories');

/**
 * インタビューを保存
 *
 * @param int $post_id ショップの投稿ID
 */
function mytheme_save_shop_interviews($post_id) {

    // nonceの確認
    if (
        !isset($_POST['mytheme_shop_interviews_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['mytheme_shop_interviews_nonce'])),
            'mytheme_save_shop_interviews'
        )
    ) {
        return;
    }

    // 自動保存・リビジョンを除外
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    // ショップの編集権限を確認
    if (
        get_post_type($post_id) !== 'shop' ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    // インタビューの入力データが存在するか確認
    if (!isset($_POST['shop_interviews']) || !is_array($_POST['shop_interviews'])) {
        return;
    }

    $interviews = wp_unslash($_POST['shop_interviews']);

    // ショップの公開状態を取得
    $shop_status = get_post_status($post_id);

    if (!in_array($shop_status, ['publish', 'draft', 'pending', 'private'], true)) {
        $shop_status = 'draft';
    }

    // 現在紐付いているインタビューを取得
    $existing_interviews = get_posts([
        'post_type'      => 'shop_interview',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_key'       => '_shop_interview_shop_id',
        'meta_value'     => $post_id,
        'fields'         => 'ids',
    ]);

    $saved_interview_ids = [];
    $order = 0;

    // インタビューを保存
    foreach ($interviews as $interview) {

        if (!is_array($interview)) {
            continue;
        }

        $interview_id = isset($interview['id'])
            ? absint($interview['id'])
            : 0;

        $question = isset($interview['question'])
            ? sanitize_text_field($interview['question'])
            : '';

        $answer = isset($interview['answer'])
            ? sanitize_textarea_field($interview['answer'])
            : '';

        // 新規投稿で質問・回答が両方空なら作成しない
        if (!$interview_id && $question === '' && $answer === '') {
            continue;
        }

        // 既存インタビューの場合
        if ($interview_id) {

            // このショップに紐付いている投稿か確認
            if (
                get_post_type($interview_id) !== 'shop_interview' ||
                (int) get_post_meta($interview_id, '_shop_interview_shop_id', true) !== (int) $post_id
            ) {
                continue;
            }

            $saved_interview_ids[] = $interview_id;

            $result = wp_update_post([
                'ID'          => $interview_id,
                'post_status' => $shop_status,
            ], true);

            if (is_wp_error($result)) {
                continue;
            }

        } else {

            // 新規インタビューを作成
            $interview_id = wp_insert_post([
                'post_type'   => 'shop_interview',
                'post_status' => $shop_status,
                'post_author' => get_current_user_id(),
            ], true);

            if (is_wp_error($interview_id)) {
                continue;
            }

            // ショップとの紐付けを保存
            update_post_meta(
                $interview_id,
                '_shop_interview_shop_id',
                $post_id
            );

            $saved_interview_ids[] = $interview_id;
        }

        // 質問・回答を保存
        update_post_meta(
            $interview_id,
            '_shop_interview_question',
            $question
        );

        update_post_meta(
            $interview_id,
            '_shop_interview_answer',
            $answer
        );

        // 表示順を保存
        update_post_meta(
            $interview_id,
            '_shop_interview_order',
            $order
        );

        $order++;
    }

    // 画面から削除されたインタビューをゴミ箱へ移動
    foreach ($existing_interviews as $existing_id) {

        if (!in_array((int) $existing_id, $saved_interview_ids, true)) {
            wp_trash_post($existing_id);
        }
    }
}
add_action('save_post_shop', 'mytheme_save_shop_interviews');