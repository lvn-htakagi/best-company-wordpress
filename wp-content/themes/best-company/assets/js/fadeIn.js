jQuery(function ($) {
  const $window = jQuery(window);

  $window.on("scroll", scrollFadeIn);
  scrollFadeIn();

  function scrollFadeIn() {
    jQuery(".fadein").each(function () {
      const targetElement = jQuery(this).offset().top;
      const scroll = $window.scrollTop();
      const windowHeight = $window.height();

      if (scroll > targetElement - windowHeight + 100) {
        jQuery(this).css("opacity", "1").css("transform", "translateY(0)");
      }
    });
  }
});
