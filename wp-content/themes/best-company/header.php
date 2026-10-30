
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo esc_url(is_singular() ? get_permalink() : home_url(add_query_arg([], $GLOBALS['wp']->request ?? ''))); ?>">
    <meta property="og:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
    <meta property="og:description" content="<?php echo esc_attr(get_bloginfo('description')); ?>">

    <?php if (is_singular() && has_post_thumbnail()) : ?>
        <meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>">
    <?php endif; ?>

    <meta name="robots" content="index,follow">

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe
        src="https://www.googletagmanager.com/ns.html?id=GTM-WTRGTJP"
        height="0"
        width="0"
        style="display:none;visibility:hidden"
    ></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<header class="pc">
    <div id="breadcrumb">
        <a href="https://www.lvnmatch.com/" rel="nofollow">不動産の比較サイト リビンマッチ</a>
        &nbsp;&gt;&nbsp;ベスト不動産カンパニー
    </div>

    <div id="headMothers">
        <img
            src="<?php echo esc_url(get_theme_file_uri('/assets/images/tosyoLogo.png')); ?>"
            height="48"
            alt="リビン・テクノロジーズ株式会社（東証グロース上場）が運営するサービスです。証券コード：4445"
        >
        リビン・テクノロジーズ株式会社（東証グロース上場）が運営するサービスです。
        <br>
        証券コード：4445
    </div>
</header>

<div id="headContents">
    <h1 id="siteLogo">
        <a href="<?php echo esc_url(BEST_COMPANY_URL); ?>"><img src="<?php echo esc_url(get_theme_file_uri('/assets/images/logoHeader.png')); ?>" alt="" /></a>
        <?php if (get_query_var('shop_year') !== '') : ?>
            <span class="year-number"><?php echo get_query_var('shop_year'); ?></span>
        <?php endif; ?>
    </h1>
    <?php if (is_post_type_archive('shop') || get_query_var('shop_year') !== '') : ?>
        <?php if (get_query_var('shop_year') == date('Y') || get_query_var('shop_year') == '') : ?>
            <div class="navigation">
                <ul>
                    <li><a class="mincho" href="#" class="current">ベスト不動産<br class="sp">カンパニー</a></li>
                    <li><a class="mincho" href="#">審査基準に<br class="sp">ついて</a></li>
                </ul>
            </div>
            <div id="mainV">
                <img class="pc" src="<?php echo esc_url(get_theme_file_uri('/assets/images/best/mainV_' . date('Y') . '_pc.jpg')); ?>" alt="ベスト不動産カンパニーとは" />
                <img class="sp" src="<?php echo esc_url(get_theme_file_uri('/assets/images/best/mainV_' . date('Y') . '_sp.jpg')); ?>" alt="ベスト不動産カンパニー認定マーク" />
            </div>
            <div id="about" class="sp">
                <p class="mincho">ベスト不動産カンパニーとは</p>
                <p class="mincho">
                    ベスト不動産カンパニーとは、厳正な審査のもとリビンマッチが認定する地域を代表する不動産会社です。<br>
                    経営者の資質、地域における確固たる実績、挑戦と成長などの審査を経て、各都道府県において最大3社まで認定されます。
                </p>
            </div>
        <?php elseif (is_singular('shop') || get_query_var('shop_year') < date('Y')) : ?>
            <div id="mainV_past">
                <img class="pc" src="<?php echo esc_url(get_theme_file_uri('/assets/images/best/mainV_pastpage_pc.jpg')); ?>" alt="ベスト不動産カンパニー一覧">
                <img class="sp" src="<?php echo esc_url(get_theme_file_uri('/assets/images/best/mainV_pastpage_sp.jpg')); ?>" alt="ベスト不動産カンパニー一覧">
                <p id="pastTtl" class="mincho"><?php echo get_query_var('shop_year'); ?>年ベスト不動産カンパニー一覧</p>
            </div>
        <?php endif; ?>
    <?php elseif (is_singular('shop') || is_page('judge')) : ?>
        <div class="navigation">
            <ul>
                <li><a class="mincho" href="#" class="current">ベスト不動産<br class="sp">カンパニー</a></li>
                <li><a class="mincho" href="#">審査基準に<br class="sp">ついて</a></li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<div id="contents" class="clearfix">