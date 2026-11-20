<?php
/**
 * インタビューの入力欄を表示
 *
 * @param WP_Post $post 投稿オブジェクト
 */
function mytheme_render_shop_interviews_field($post) {
    wp_nonce_field(
        'mytheme_save_shop_interviews',
        'mytheme_shop_interviews_nonce'
    );

    $interviews = get_posts([
        'post_type'      => 'shop_interview',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_query'     => [
            'shop_id_clause' => [
                'key'     => '_shop_interview_shop_id',
                'value'   => $post->ID,
                'compare' => '=',
            ],
            'order_clause' => [
                'key'     => '_shop_interview_order',
                'compare' => 'EXISTS',
                'type'    => 'NUMERIC',
            ],
        ],
        'orderby' => [
            'order_clause' => 'ASC',
        ],
    ]);
    ?>
    <div id="mytheme-shop-interviews">

    <p class="mytheme-interviews-heading">
        インタビュー（<?php echo esc_html(count($interviews)); ?>件）
    </p>

    <div class="mytheme-interview-list">
        <?php if ($interviews) : ?>
            <?php foreach ($interviews as $index => $interview) : ?>
                <?php
                $question = get_post_meta($interview->ID, '_shop_interview_question', true);
                $answer = get_post_meta($interview->ID, '_shop_interview_answer', true);
                ?>
                <div class="mytheme-interview-item">
                    <div class="mytheme-interview-header">
                        <strong>インタビュー <?php echo esc_html($index + 1); ?></strong>
                        <div>
                            <button type="button" class="button mytheme-interview-toggle">
                                折りたたむ
                            </button>
                            <button type="button" class="button mytheme-interview-delete">
                                削除
                            </button>
                        </div>
                    </div>
                    <div class="mytheme-interview-content">
                        <input
                            type="hidden"
                            name="shop_interviews[<?php echo esc_attr($index); ?>][id]"
                            value="<?php echo esc_attr($interview->ID); ?>"
                        >
                        <p>
                            <label>質問</label>
                            <input
                                type="text"
                                name="shop_interviews[<?php echo esc_attr($index); ?>][question]"
                                class="widefat"
                                value="<?php echo esc_attr($question); ?>"
                                placeholder="質問を入力してください"
                            >
                        </p>
                        <p>
                            <label>回答</label>
                            <textarea
                                name="shop_interviews[<?php echo esc_attr($index); ?>][answer]"
                                class="widefat"
                                rows="5"
                                placeholder="回答を入力してください"
                            ><?php echo esc_textarea($answer); ?></textarea>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="mytheme-interview-item">
                <div class="mytheme-interview-header">
                    <strong>インタビュー 1</strong>
                    <div>
                        <button type="button" class="button mytheme-interview-toggle">
                            折りたたむ
                        </button>
                        <button type="button" class="button mytheme-interview-delete">
                            削除
                        </button>
                    </div>
                </div>
                <div class="mytheme-interview-content">
                    <input type="hidden" name="shop_interviews[0][id]" value="">

                    <p>
                        <label>質問</label>
                        <input
                            type="text"
                            name="shop_interviews[0][question]"
                            class="widefat"
                            placeholder="質問を入力してください"
                        >
                    </p>

                    <p>
                        <label>回答</label>
                        <textarea
                            name="shop_interviews[0][answer]"
                            class="widefat"
                            rows="5"
                            placeholder="回答を入力してください"
                        ></textarea>
                    </p>

                </div>

            </div>

        <?php endif; ?>

        </div>

        <p>
            <button type="button" class="button button-primary" id="mytheme-shop-interview-add">
                ＋ インタビューを追加
            </button>
        </p>

    </div>
    <?php
}
