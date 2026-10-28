<?php
get_header();

if (have_posts()) :
    while (have_posts()) :
        the_post();

        $shop_id = get_the_ID();

        // 基本情報
        $shop_name    = get_post_meta($shop_id, '_shop_name', true);
        $company_name = get_post_meta($shop_id, '_shop_company_name', true) ?: $shop_name;
        $shop_year    = get_post_meta($shop_id, '_shop_year', true);
        $comment      = get_post_meta($shop_id, '_shop_comment', true);
        $introduction = get_post_meta($shop_id, '_shop_introduction', true);

        // 会社概要
        $company_fields = [
            'address'    => get_post_meta($shop_id, '_shop_address', true),
            'phone'      => get_post_meta($shop_id, '_shop_tel', true),
            'fax'        => get_post_meta($shop_id, '_shop_fax', true),
            'url'        => get_post_meta($shop_id, '_shop_url', true),
            'capital'    => get_post_meta($shop_id, '_shop_capital', true),
            'sales'      => get_post_meta($shop_id, '_shop_net_sales', true),
            'business'   => get_post_meta($shop_id, '_shop_business_content', true),
            'license'    => get_post_meta($shop_id, '_shop_license', true),
            'employee'   => get_post_meta($shop_id, '_shop_employee', true),
            'membership' => get_post_meta($shop_id, '_shop_membership', true),
            'establish'  => get_post_meta($shop_id, '_shop_establish', true),
            'branch'     => get_post_meta($shop_id, '_shop_branch', true),
            'associate'  => get_post_meta($shop_id, '_shop_associate', true),
        ];

        // 代表者情報
        $president       = get_post_meta($shop_id, '_shop_president', true);
        $president_image = (int) get_post_meta($shop_id, '_shop_president_image', true);
        $career           = get_post_meta($shop_id, '_shop_career', true);

        // 企業紹介
        $catch_copy = get_post_meta($shop_id, '_shop_catch_copy', true);

        // インタビュー
        $interview = get_post_meta($shop_id, '_shop_interview', true);

        // 沿革：既存の shop_history CPT を利用
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
            'meta_key'       => '_shop_history_year',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
        ]);

        usort($histories, static function ($a, $b) {
            $a_year  = (int) get_post_meta($a->ID, '_shop_history_year', true);
            $b_year  = (int) get_post_meta($b->ID, '_shop_history_year', true);
            $a_month = (int) get_post_meta($a->ID, '_shop_history_month', true);
            $b_month = (int) get_post_meta($b->ID, '_shop_history_month', true);

            return [$a_year, $a_month] <=> [$b_year, $b_month];
        });

        // ギャラリー：画像IDとキャプションの配列を想定
        $images = get_post_meta($shop_id, '_shop_images', true);
        if (!is_array($images)) {
            $images = [];
        }

        // エリア名：都道府県フィールドから取得
        $prefecture_names = [
            1  => '北海道',
            2  => '青森県',
            3  => '岩手県',
            4  => '宮城県',
            5  => '秋田県',
            6  => '山形県',
            7  => '福島県',
        ];
        $area_id = get_post_meta($shop_id, '_shop_prefecture', true);
        $area_name = $prefecture_names[$area_id] ?? '';

        // 画像
        $logo_id = (int) get_post_meta($shop_id, '_shop_logo', true);

        // URL
        $base_title = 'ベスト不動産カンパニー' . date('Y');
        $title = $company_name . 'の企業情報 | ' . $base_title;

        $back_url = $shop_year !== ''
            ? home_url('/shops/' . rawurlencode((string) $shop_year) . '/')
            : get_post_type_archive_link('shop');

        $canonical = get_permalink($shop_id);

        // テーマCSS
        $detail_css_path = get_theme_file_path('/assets/css/detail.css');
        if (file_exists($detail_css_path)) {
            wp_enqueue_style(
                'mytheme-shop-detail',
                get_theme_file_uri('/assets/css/detail.css'),
                [],
                filemtime($detail_css_path)
            );
        }
        ?>
            <div class="contentsInner">
                <section>
                    <div id="corpInfo">
                        <h2>
                            <img
                                src="<?php echo esc_url(get_theme_file_uri('/assets/images/laurel.png')); ?>"
                                alt="<?php echo esc_attr($base_title); ?>"
                            >
                            <span><?php echo esc_html($area_name); ?></span>
                        </h2>

                        <div id="corpLogo">
                            <?php
                            if ($logo_id) {
                                echo wp_get_attachment_image(
                                    $logo_id,
                                    'medium',
                                    false,
                                    ['alt' => $company_name]
                                );
                            } else {
                                $image_id = (int) get_post_meta($shop_id, '_shop_image', true);

                                if ($image_id) {
                                    echo wp_get_attachment_image(
                                        $image_id,
                                        'medium',
                                        false,
                                        ['alt' => $company_name]
                                    );
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div id="presidentDiv">
                        <div id="profileSection">
                            <?php if ($president_image) : ?>
                                <div id="presidentPh">
                                    <?php
                                    echo wp_get_attachment_image(
                                        $president_image,
                                        'large',
                                        false,
                                        ['alt' => $president]
                                    );
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div id="profile">
                                <?php if ($president) : ?>
                                    <p id="presidentName"><?php echo esc_html($president); ?></p>
                                <?php endif; ?>

                                <?php if ($career) : ?>
                                    <div id="biography">
                                        <p>【略歴】</p>
                                        <pre><?php echo esc_html($career); ?></pre>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div id="corpIntroduction">
                            <p>企業紹介</p>
                            <pre><?php echo esc_html($introduction); ?></pre>
                        </div>
                        <div id="interview">
                            <p>インタビュー</p>
                            <pre><?php echo esc_html($interview); ?></pre>
                        </div>
                    </div>
                </section>

                <!-- 企業概要 -->
                <section id="overview">
                    <p>企業概要</p>
                    <dl>
                        <dt>会社名</dt>
                        <dd><?php echo esc_html($company_name); ?></dd>

                        <dt>所在地</dt>
                        <dd><?php echo esc_html($company_fields['address']); ?></dd>

                        <dt>電話番号</dt>
                        <dd><?php echo esc_html($company_fields['phone']); ?></dd>

                        <dt>FAX番号</dt>
                        <dd><?php echo esc_html($company_fields['fax']); ?></dd>

                        <dt>URL</dt>
                        <dd>
                            <?php if ($company_fields['url'] && wp_http_validate_url($company_fields['url'])) : ?>
                                <a href="<?php echo esc_url($company_fields['url']); ?>"
                                   target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html($company_fields['url']); ?>
                                </a>
                            <?php else : ?>
                                <?php echo esc_html($company_fields['url']); ?>
                            <?php endif; ?>
                        </dd>

                        <dt>資本金</dt>
                        <dd><?php echo esc_html($company_fields['capital']); ?></dd>

                        <dt>売上高</dt>
                        <dd><pre><?php echo esc_html($company_fields['sales']); ?></pre></dd>

                        <dt>事業内容</dt>
                        <dd><pre><?php echo esc_html($company_fields['business']); ?></pre></dd>

                        <dt>許認可・免許番号</dt>
                        <dd><pre><?php echo esc_html($company_fields['license']); ?></pre></dd>

                        <dt>従業員数</dt>
                        <dd><?php echo esc_html($company_fields['employee']); ?></dd>

                        <dt>加盟団体</dt>
                        <dd><pre><?php echo esc_html($company_fields['membership']); ?></pre></dd>

                        <dt>設立年月</dt>
                        <dd><?php echo esc_html($company_fields['establish']); ?></dd>

                        <dt>拠点</dt>
                        <dd><pre><?php echo esc_html($company_fields['branch']); ?></pre></dd>

                        <dt>関係会社</dt>
                        <dd><pre><?php echo esc_html($company_fields['associate']); ?></pre></dd>
                    </dl>
                </section>

                <!-- 沿革 -->
                <section id="history">
                    <p>沿革</p>
                    <dl>
                        <?php foreach ($histories as $history) : ?>
                            <?php
                            $history_year = get_post_meta($history->ID, '_shop_history_year', true);
                            $history_month = get_post_meta($history->ID, '_shop_history_month', true);
                            $history_detail = get_post_meta($history->ID, '_shop_history_detail', true);
                            ?>
                            <dt>
                                <?php
                                echo esc_html($history_year);
                                if ($history_month !== '') {
                                    echo esc_html($history_month) . '月';
                                }
                                ?>
                            </dt>
                            <dd><pre><?php echo esc_html($history_detail); ?></pre></dd>
                        <?php endforeach; ?>
                    </dl>
                </section>

                <!-- フォトギャラリー -->
                <?php if ($images) : ?>
                    <section id="gallery">
                        <p>フォトギャラリー</p>
                        <ul class="fadein">
                            <?php foreach ($images as $image) : ?>
                                <?php
                                $gallery_image_id = (int) ($image['image_id'] ?? $image['id'] ?? 0);
                                $caption = $image['caption'] ?? '';
                                ?>
                                <?php if ($gallery_image_id) : ?>
                                    <li>
                                        <?php
                                        echo wp_get_attachment_image(
                                            $gallery_image_id,
                                            'large',
                                            false,
                                            ['alt' => $caption]
                                        );
                                        ?>
                                        <span class="photoDdesc"><?php echo esc_html($caption); ?></span>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <!-- 編集部コメント -->
                <?php if ($comment) : ?>
                    <section id="editorialComment">
                        <p id="editorTitle">
                            <img
                                src="<?php echo esc_url(get_theme_file_uri('/assets/images/pen.svg')); ?>"
                                alt="<?php echo esc_attr($base_title); ?> 編集部コメント"
                            >
                            編集部コメント
                        </p>
                        <pre><?php echo esc_html($comment); ?></pre>
                    </section>
                <?php endif; ?>

                <!-- お問い合わせ -->
                <section id="inquiryBtn">
                    <?php
                    $inquiry_url = add_query_arg(
                        ['detail_urls[]' => $canonical],
                        'https://www.lvnmatch.com/shop/form'
                    );
                    ?>
                    <a href="<?php echo esc_url($inquiry_url); ?>">
                        <img
                            src="<?php echo esc_url(get_theme_file_uri('/assets/images/inquiryBtn.png')); ?>"
                            width="404"
                            height="70"
                            alt="この会社に問い合わせる"
                        >
                    </a>
                </section>
            </div>

            <div id="breadcrumbBottom" class="sp">
                <a href="<?php echo esc_url(home_url('/')); ?>">ベスト不動産カンパニー</a>
                &nbsp;&gt;&nbsp;
                <?php echo esc_html($company_name); ?>
            </div>

        <?php
    endwhile;
endif;

get_footer();