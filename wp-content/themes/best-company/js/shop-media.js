jQuery(function ($) {
  let mediaFrame;

  $("#shop-image-select").on("click", function () {
    if (mediaFrame) {
      mediaFrame.open();
      return;
    }

    mediaFrame = wp.media({
      title: "ショップ画像を選択",
      button: {
        text: "この画像を使用",
      },
      library: {
        type: "image",
      },
      multiple: false,
    });

    mediaFrame.on("select", function () {
      const attachment = mediaFrame.state().get("selection").first().toJSON();

      $("#shop-image-id").val(attachment.id);
      $("#shop-image-removed").val("0");

      $("#shop-image-preview").html(
        $("<img>", {
          src: attachment.url,
          css: {
            maxWidth: "300px",
            height: "auto",
          },
        }),
      );
    });

    mediaFrame.open();
  });

  $("#shop-image-remove").on("click", function () {
    $("#shop-image-id").val("");
    $("#shop-image-removed").val("1");
    $("#shop-image-preview").empty();
  });
});
