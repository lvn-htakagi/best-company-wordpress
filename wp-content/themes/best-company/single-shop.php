
<?php
get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();

        $shop_id = get_the_ID();

        // 既存のカスタムフィールド
        $shop_name         = get_post_meta($shop_id, '_shop_name', true);
        $image_id          = (int) get_post_meta($shop_id, '_shop_image', true);
        $introduction      = get_post_meta($shop_id, '_shop_introduction', true);
        $comment           = get_post_meta($shop_id, '_shop_comment', true);

        // 会社概要（入力欄・保存処理は後から接続）
        $company_name      = get_post_meta($shop_id, '_shop_company_name', true);
        $address           = get_post_meta($shop_id, '_shop_address', true);
        $tel               = get_post_meta($shop_id, '_shop_tel', true);
        $fax               = get_post_meta($shop_id, '_shop_fax', true);
        $url               = get_post_meta($shop_id, '_shop_url', true);
        $capital           = get_post_meta($shop_id, '_shop_capital', true);
        $net_sales         = get_post_meta($shop_id, '_shop_net_sales', true);
        $business_content  = get_post_meta($shop_id, '_shop_business_content', true);
        $interview         = get_post_meta($shop_id, '_shop_interview', true);

        // お店の歴史（配列データを想定）
        $histories = get_post_meta($shop_id, '_shop_histories', true);
        if (!is_array($histories)) {
            $histories = get_posts([
                'post_type'      => 'shop_history',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'meta_query'     => [
                    [
                        'key'     => '_shop_history_shop_id',
                        'value'   => $shop_id,
                        'compare' => '=',
                        'type'    => 'NUMERIC',
                    ],
                ],
            ]);
        }

        // 年別一覧へのリンク用
        $shop_year = get_post_meta($shop_id, '_shop_year', true);
        $back_url = home_url('/shops/');

        if ($shop_year !== '') {
            $back_url = home_url('/shops/' . rawurlencode((string) $shop_year) . '/');
        }
        ?>

        <main class="shop-detail">
            <a class="shop-back-link" href="<?php echo esc_url($back_url); ?>">
                ← <?php echo $shop_year !== '' ? esc_html($shop_year) . '年の一覧に戻る' : 'ショップ一覧に戻る'; ?>
            </a>

            <article class="shop-detail__container">

                <div class="shop-detail__hero">
                    <?php
                    if ($image_id) {
                        echo wp_get_attachment_image(
                            $image_id,
                            'large',
                            false,
                            [
                                'class' => 'shop-detail__image',
                                'alt'   => $shop_name ?: get_the_title(),
                            ]
                        );
                    } else {
                        ?>
                        <div class="shop-detail__no-image">
                            画像はありません
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <h1 class="shop-detail__title">
                    <?php echo esc_html($shop_name ?: get_the_title()); ?>
                </h1>

                <?php if ($introduction !== '') : ?>
                    <section class="shop-detail__section">
                        <h2 class="shop-detail__heading">企業紹介</h2>
                        <div class="shop-detail__text-box">
                            <?php echo nl2br(esc_html($introduction)); ?>
                        </div>
                    </section>
                <?php endif; ?>
                <section class="shop-detail__section">
                    <h2 class="shop-detail__heading">お店の歴史</h2>

                    <?php if (!empty($histories)) : ?>

                        <?php
                        // 年・月の古い順に並べる
                        usort($histories, static function ($a, $b) {
                            $a_year = (int) get_post_meta(
                                $a->ID,
                                '_shop_history_year',
                                true
                            );
                            $b_year = (int) get_post_meta(
                                $b->ID,
                                '_shop_history_year',
                                true
                            );

                            $year_diff = $a_year <=> $b_year;

                            if ($year_diff !== 0) {
                                return $year_diff;
                            }

                            $a_month = (int) get_post_meta(
                                $a->ID,
                                '_shop_history_month',
                                true
                            );
                            $b_month = (int) get_post_meta(
                                $b->ID,
                                '_shop_history_month',
                                true
                            );

                            return $a_month <=> $b_month;
                        });
                        ?>

                        <ul class="shop-timeline">
                            <?php foreach ($histories as $history) : ?>
                                <?php
                                $history_year = get_post_meta(
                                    $history->ID,
                                    '_shop_history_year',
                                    true
                                );

                                $history_month = get_post_meta(
                                    $history->ID,
                                    '_shop_history_month',
                                    true
                                );

                                $history_detail = get_post_meta(
                                    $history->ID,
                                    '_shop_history_detail',
                                    true
                                );
                                ?>

                                <li class="shop-timeline__item">
                                    <span class="shop-timeline__date">
                                        <?php
                                        if ($history_year !== '') {
                                            echo esc_html($history_year) . '年';
                                        }

                                        if ($history_month !== '') {
                                            echo esc_html($history_month) . '月';
                                        }
                                        ?>
                                    </span>

                                    <p class="shop-timeline__event"><?php
                                        echo nl2br(esc_html($history_detail));
                                    ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                    <?php else : ?>

                        <p class="shop-detail__empty">
                            沿革情報はまだ登録されていません。
                        </p>

                    <?php endif; ?>
                </section>
                <section class="shop-detail__section">
                    <h2 class="shop-detail__heading">会社概要</h2>

                    <table class="shop-company-table">
                        <tbody>
                            <tr>
                                <th scope="row">会社名</th>
                                <td><?php echo esc_html($company_name ?: '—'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">所在地</th>
                                <td><?php echo esc_html($address ?: '—'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">電話番号</th>
                                <td><?php echo esc_html($tel ?: '—'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">FAX番号</th>
                                <td><?php echo esc_html($fax ?: '—'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">URL</th>
                                <td>
                                    <?php if ($url && wp_http_validate_url($url)) : ?>
                                        <a href="<?php echo esc_url($url); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer">
                                            <?php echo esc_html($url); ?>
                                        </a>
                                    <?php else : ?>
                                        <?php echo esc_html($url ?: '—'); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">資本金</th>
                                <td><?php echo esc_html($capital ?: '—'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">売上高</th>
                                <td><?php echo esc_html($net_sales ?: '—'); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">事業内容</th>
                                <td><?php echo nl2br(esc_html($business_content ?: '—')); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <?php if ($interview !== '') : ?>
                    <section class="shop-detail__section">
                        <h2 class="shop-detail__heading">インタビュー</h2>
                        <div class="shop-detail__text-box"><?php echo nl2br(esc_html($interview)); ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($comment !== '') : ?>
                    <section class="shop-detail__section">
                        <h2 class="shop-detail__heading">編集部コメント</h2>
                        <div class="shop-detail__text-box">
                            <?php echo nl2br(esc_html(trim($comment))); ?>
                        </div>
                    </section>
                <?php endif; ?>

            </article>
        </main>

        <?php
    endwhile;
endif;

get_footer();
