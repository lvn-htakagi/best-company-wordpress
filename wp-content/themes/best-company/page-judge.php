<?php
get_header();
?>
<div class="contentsInner">
    <section id="whats">
        <p class="ttl">ベスト不動産カンパニーとは</p>
        <p>
            ベスト不動産カンパニーとは、厳正な審査のもとリビンマッチが認定する地域を代表する不動産会社です。<br>
            経営者の資質、地域における確固たる実績、挑戦と成長などの審査を経て、各都道府県において最大3社まで認定されます。
        </p>
    </section>

    <section id="aboutExam">
        <p class="ttl">審査基準について</p>
        <p class="ttl2">経営者の資質</p>
        <p> 経営者が高いビジョンを掲げ、ビジョン実現に向けて業務を推進してる</p>

        <p class="ttl2">当該地域における確固たる実績</p>
        <p> 当該地域において相当の業歴や確固たる売上実績、およびビジネス基盤がある</p>

        <p class="ttl2">挑戦と成長</p>
        <p>新しいチャレンジを行い、売上・利益・雇用を伸ばすことで地域に貢献している</p>
    </section>

    <section id="prerequisite">
        <p class="ttl">必須条件</p>
        <ul class="hissu">
            <li>宅地建物取引業免許を保有している</li>
            <li>当該都道府県で3年以上の営業実績がある</li>
            <li>前期の売上、営業利益、経常利益のいずれかが前々期を上回っている</li>
            <li>継続的に雇用を創出している</li>
        </ul>

        <p>以下に該当する事業者は審査を行いません</p><br>
        <ul class="otherThan">
            <li>民事再生法及び会社更生法による再生・更生手続中の事業者</li>
            <li>各種法令に違反しているもの</li>
            <li>行政機関からの行政指導を受け、改善がなされていないもの</li>
            <li>不当景品類及び不当表示防止法に違反しているもの</li>
            <li>公序良俗に反する事業を行っているもの</li>
        </ul>
    </section>
</div>

<div id="breadcrumbBottom" class="sp">
    <a href="{{config('app.lvnmatchUrl')}}/">不動産の比較サイト リビンマッチ</a>&nbsp;&gt;&nbsp;<a href="{{config('app.url')}}">ベスト不動産カンパニー</a>&nbsp;&gt;&nbsp;審査基準について
</div>

<?php get_footer(); ?>