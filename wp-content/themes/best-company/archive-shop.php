
<?php
wp_enqueue_style(
    'shop-archive',
    get_theme_file_uri('/assets/css/shop-archive.css'),
    [],
    '1.0.0'
);

get_header();

$year = get_query_var('shop_year');

// 公開済みショップをすべて取得（年・地域の条件なし）
$shops_query = new WP_Query([
    'post_type'      => 'shop',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => '_shop_year',
            'value'   => $year,
            'compare' => '=',
        ],
    ],
]);

$prefecture_names = [
    1  => '北海道',
    2  => '青森県',
    3  => '岩手県',
    4  => '宮城県',
    5  => '秋田県',
    6  => '山形県',
    7  => '福島県',
];

$grouped_shops = [];

while ($shops_query->have_posts()) {
    $shops_query->the_post();

    $shop_id = get_the_ID();

    $prefecture = get_post_meta(
        $shop_id,
        '_shop_prefecture',
        true
    );

    $prefecture = trim((string) $prefecture);

    if (is_numeric($prefecture)) {
        $prefecture = $prefecture_names[(int) $prefecture]
            ?? '都道府県未設定';
    } elseif ($prefecture === '') {
        $prefecture = '都道府県未設定';
    }

    $grouped_shops[$prefecture][] = [
        'name' => get_post_meta($shop_id, '_shop_name', true),
        'detail' => get_post_meta($shop_id, '_shop_comment', true),
        'image_id' => (int) get_post_meta($shop_id, '_shop_image', true),
        'url' => get_permalink($shop_id),
    ];
}

wp_reset_postdata();

// 北海道・東北を先に表示
$prefecture_order = [
    '北海道',
    '青森県',
    '岩手県',
    '宮城県',
    '秋田県',
    '山形県',
    '福島県',
];

uksort($grouped_shops, function ($a, $b) use ($prefecture_order) {
    $a_order = array_search($a, $prefecture_order, true);
    $b_order = array_search($b, $prefecture_order, true);

    $a_order = $a_order === false ? PHP_INT_MAX : $a_order;
    $b_order = $b_order === false ? PHP_INT_MAX : $b_order;

    return $a_order <=> $b_order ?: strcmp($a, $b);
});
?>

<main class="shop-archive">
    <header class="shop-archive__header">
        <h1>ショップ一覧</h1>
        <p>登録されているショップをご紹介します。</p>
    </header>

    <div class="shop-archive__content">
        <?php if (empty($grouped_shops)) : ?>
            <p class="shop-archive__empty">
                公開済みのショップはまだありません。
            </p>
        <?php else : ?>
            <?php foreach ($grouped_shops as $prefecture => $shops) : ?>
                <section class="pref-section">
                    <h2><?php echo esc_html($prefecture); ?></h2>

                    <ul class="shop-grid">
                        <?php foreach ($shops as $shop) : ?>
                            <li class="shop-card">
                                <a href="<?php echo esc_url($shop['url']); ?>">
                                    <?php if ($shop['image_id']) : ?>
                                        <?php
                                        echo wp_get_attachment_image(
                                            $shop['image_id'],
                                            'medium',
                                            false,
                                            ['class' => 'shop-card__image']
                                        );
                                        ?>
                                    <?php endif; ?>

                                    <h3>
                                        <?php
                                        echo esc_html(
                                            $shop['name'] ?: 'ショップ名未設定'
                                        );
                                        ?>
                                    </h3>

                                    <p>
                                        <?php
                                        echo esc_html(
                                            $shop['detail'] ?: '詳細情報はありません。'
                                        );
                                        ?>
                                    </p>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
