<?php
/**
 * 沿革の入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_histories_field($post) {
    wp_nonce_field(
        'mytheme_save_shop_histories',
        'mytheme_shop_histories_nonce'
    );

    $histories = get_posts([
        'post_type'      => 'shop_history',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_query'     => [
            'shop_id_clause' => [
                'key'     => '_shop_history_shop_id',
                'value'   => $post->ID,
                'compare' => '=',
            ],
            'order_clause' => [
                'key'     => '_shop_history_order',
                'compare' => 'EXISTS',
                'type'    => 'NUMERIC',
            ],
        ],
        'orderby' => [
            'order_clause' => 'ASC',
        ],
    ]);
    ?>
    <div id="mytheme-shop-histories">

    <p class="mytheme-histories-heading">
        沿革（<?php echo esc_html(count($histories)); ?>件）
    </p>

    <div class="mytheme-history-list">
        <?php if ($histories) : ?>
            <?php foreach ($histories as $index => $history) : ?>
                <?php
                $year = get_post_meta($history->ID, '_shop_history_year', true);
                $month = get_post_meta($history->ID, '_shop_history_month', true);
                $detail = get_post_meta($history->ID, '_shop_history_detail', true);
                ?>
                <div class="mytheme-history-item">
                    <div class="mytheme-history-header">
                        <strong>沿革 <?php echo esc_html($index + 1); ?></strong>
                        <div>
                            <button type="button" class="button mytheme-history-toggle">
                                折りたたむ
                            </button>
                            <button type="button" class="button mytheme-history-delete">
                                削除
                            </button>
                        </div>
                    </div>
                    <div class="mytheme-history-content">
                        <input
                            type="hidden"
                            name="shop_histories[<?php echo esc_attr($index); ?>][id]"
                            value="<?php echo esc_attr($history->ID); ?>"
                        >
                        <p>
                            <label>年</label>
                            <select
                                name="shop_histories[<?php echo esc_attr($index); ?>][year]"
                                class="widefat"
                            >
                                <option value="">年を選択してください</option>
                                <?php for ($y = 2036; $y >= 1900; $y--) : ?>
                                    <option value="<?php echo esc_attr($y); ?>" <?php selected($year, $y); ?>>
                                        <?php echo esc_html($y); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </p>
                        <p>
                            <label>月</label>
                            <select
                                name="shop_histories[<?php echo esc_attr($index); ?>][month]"
                                class="widefat"
                            >
                                <option value="">月を選択してください</option>
                                <?php for ($m = 1; $m <= 12; $m++) : ?>
                                    <option value="<?php echo esc_attr($m); ?>" <?php selected($month, $m); ?>>
                                        <?php echo esc_html($m); ?>月
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </p>
                        <p>
                            <label>沿革</label>
                            <textarea
                                name="shop_histories[<?php echo esc_attr($index); ?>][detail]"
                                class="widefat"
                                rows="5"
                                placeholder="沿革を入力してください"
                            ><?php echo esc_textarea($detail); ?></textarea>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="mytheme-history-item">
                <div class="mytheme-history-header">
                    <strong>沿革 1</strong>
                    <div>
                        <button type="button" class="button mytheme-history-toggle">
                            折りたたむ
                        </button>
                        <button type="button" class="button mytheme-history-delete">
                            削除
                        </button>
                    </div>
                </div>
                <div class="mytheme-history-content">
                    <input type="hidden" name="shop_histories[0][id]" value="">

                    <p>
                        <label>年</label>
                        <select
                            name="shop_histories[0][year]"
                            class="widefat"
                        >
                            <option value="">年を選択してください</option>
                            <?php for ($y = 2036; $y >= 1900; $y--) : ?>
                                <option value="<?php echo esc_attr($y); ?>">
                                    <?php echo esc_html($y); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                        <select
                            name="shop_histories[0][month]"
                            class="widefat"
                        >
                            <option value="">月を選択してください</option>
                            <?php for ($m = 1; $m <= 12; $m++) : ?>
                                <option value="<?php echo esc_attr($m); ?>">
                                    <?php echo esc_html($m); ?>月
                                </option>
                            <?php endfor; ?>
                        </select>
                    </p>

                    <p>
                        <label>沿革</label>
                        <textarea
                            name="shop_histories[0][detail]"
                            class="widefat"
                            rows="5"
                            placeholder="沿革を入力してください"
                        ></textarea>
                    </p>

                </div>

            </div>

        <?php endif; ?>

        </div>

        <p>
            <button type="button" class="button button-primary" id="mytheme-shop-history-add">
                ＋ 沿革を追加
            </button>
        </p>

    </div>
    <?php
}