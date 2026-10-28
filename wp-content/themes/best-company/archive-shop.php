
<?php
get_header();

$year = (int) get_query_var('shop_year');

if (!$year) {
    $year = (int) date('Y');
}

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

<section id="logoList_top<?php echo esc_attr($year !== (int) date('Y') ? '_past' : ''); ?>">
    <div class="contentsInner">

        <p class="sectionTitle mincho">
            ベスト不動産カンパニー一覧
        </p>

        <?php foreach ($grouped_shops as $prefecture => $shop_ids) : ?>
            <div class="prefectureDiv">

                <p class="prefectureName mincho">
                    <?php echo esc_html($prefecture); ?>
                </p>

                <ul class="fadein">
                    <?php foreach ($shop_ids as $shop) : ?>
                        <?php
                        $shop_name = $shop['name'];

                        $image_id = $shop['image_id'];

                        $image_url = $image_id
                            ? wp_get_attachment_image_url($image_id, 'full')
                            : '';
                        ?>

                        <li>
                            <a href="<?php echo esc_url($shop['url']); ?>">
                                <?php if ($image_url) : ?>
                                    <img
                                        src="<?php echo esc_url($image_url); ?>"
                                        alt="<?php echo esc_attr($shop_name); ?>"
                                    >
                                <?php endif; ?>

                                <p class="corpName">
                                    <?php echo esc_html($shop_name); ?>
                                </p>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <?php if (count($shop_ids) === 2) : ?>
                        <li></li>
                    <?php endif; ?>
                </ul>

            </div>
        <?php endforeach; ?>

        <div id="pastpageList">
            <p>過去のベスト不動産カンパニー一覧</p>

            <ul>
                <?php for ($i = 2020; $i < (int) date('Y'); $i++) : ?>
                    <li>
                        <a href="<?php echo esc_url(home_url('/shops/' . $i . '/')); ?>">
                            <?php echo esc_html($i); ?>年
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </div>

    </div>

    <div id="footerTown">
        <img
            src="<?php echo esc_url(get_theme_file_uri('/assets/images/footerTown.png')); ?>"
            alt=""
        >
    </div>
</section>

<div id="breadcrumbBottom" class="sp">
    <a href="https://www.lvnmatch.com/" rel="nofollow">
        不動産の比較サイト リビンマッチ
    </a>
    &nbsp;&gt;&nbsp;ベスト不動産カンパニー
</div>

<?php get_footer(); ?>
