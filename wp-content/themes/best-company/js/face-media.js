jQuery(function ($) {
  let mediaFrame;

  $("#shop-face-select").on("click", function () {
    if (mediaFrame) {
      mediaFrame.open();
      return;
    }

    mediaFrame = wp.media({
      title: "顔写真画像を選択",
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

      $("#shop-face-id").val(attachment.id);
      $("#shop-face-removed").val("0");

      $("#shop-face-image-preview").html(
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

  $("#shop-face-remove").on("click", function () {
    $("#shop-face-id").val("");
    $("#shop-face-removed").val("1");
    $("#shop-face-image-preview").empty();
  });
});
