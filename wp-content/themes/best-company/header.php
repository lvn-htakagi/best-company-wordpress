
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
    <?php
    // ページ固有のヘッダーコンテンツが必要な場合に使用
    ?>
</div>

<div id="contents" class="clearfix">