
jQuery(function($) {
    const $preview = $('#mytheme-shop-gallery-preview');
    const $input = $('#mytheme_shop_gallery_ids');
    let galleryFrame;

    function updateGalleryIds() {
        const ids = [];

        $preview.find('.mytheme-shop-gallery-item').each(function() {
            ids.push($(this).data('id'));
        });

        $input.val(ids.join(','));
    }

    $preview.sortable({
        items: '.mytheme-shop-gallery-item',
        update: updateGalleryIds
    });

    $('#mytheme-shop-gallery-add').on('click', function(e) {
        e.preventDefault();

        if (galleryFrame) {
            galleryFrame.open();
            return;
        }

        galleryFrame = wp.media({
            title: 'ギャラリー画像を選択',
            button: { text: '画像を追加' },
            library: { type: 'image' },
            multiple: true
        });

        galleryFrame.on('open', function() {
            const selection = galleryFrame.state().get('selection');

            $preview.find('.mytheme-shop-gallery-item').each(function() {
                const id = Number($(this).data('id'));
                const attachment = wp.media.attachment(id);

                attachment.fetch();
                selection.add(attachment);
            });
        });

        galleryFrame.on('select', function() {
            const selection = galleryFrame.state().get('selection');

            selection.each(function(attachment) {
                const data = attachment.toJSON();
                const id = Number(data.id);

                if ($preview.find('[data-id="' + id + '"]').length) {
                    return;
                }

                const thumbnail = data.sizes && data.sizes.thumbnail
                    ? data.sizes.thumbnail.url
                    : data.url;

                const $item = $('<div>', {
                    class: 'mytheme-shop-gallery-item'
                }).attr('data-id', id);

                $('<img>', {
                    src: thumbnail,
                    alt: ''
                }).appendTo($item);

                $('<button>', {
                    type: 'button',
                    class: 'button mytheme-shop-gallery-remove',
                    text: '削除'
                }).appendTo($item);

                $preview.append($item);
            });

            updateGalleryIds();
        });

        galleryFrame.open();
    });

    $preview.on('click', '.mytheme-shop-gallery-remove', function() {
        $(this).closest('.mytheme-shop-gallery-item').remove();
        updateGalleryIds();
    });
});
