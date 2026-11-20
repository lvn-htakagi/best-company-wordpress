
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#mytheme-shop-histories');
    
    if (!container) {
        return;
    }

    const list = container.querySelector('.mytheme-history-list');
    const addButton = container.querySelector('#mytheme-shop-history-add');
    const heading = container.querySelector('.mytheme-histories-heading');

    // 件数と番号を更新
    function updatehistoryCount() {
        const items = list.querySelectorAll('.mytheme-history-item');

        heading.textContent = `沿革（${items.length}件）`;

        items.forEach((item, index) => {
            item.querySelector('strong').textContent = `沿革 ${index + 1}`;

            item.querySelector('input[type="hidden"]').name = `shop_histories[${index}][id]`;
            item.querySelector('input[type="text"]').name = `shop_histories[${index}][question]`;
            item.querySelector('textarea').name = `shop_histories[${index}][answer]`;
        });
    }

    // 沿革を追加
    addButton.addEventListener('click', () => {
        const original = list.querySelector('.mytheme-history-item');
        const newItem = original.cloneNode(true);

        // 入力内容をリセット
        newItem.querySelectorAll('input, textarea').forEach((field) => {
            field.value = '';
        });

        list.appendChild(newItem);

        updatehistoryCount();
    });

    // 沿革の折りたたみ
    list.addEventListener('click', (event) => {
        const toggleButton = event.target.closest('.mytheme-history-toggle');

        if (!toggleButton) {
            return;
        }

        const item = toggleButton.closest('.mytheme-history-item');
        const content = item.querySelector('.mytheme-history-content');

        const isCollapsed = content.hidden;

        content.hidden = !isCollapsed;
        toggleButton.textContent = isCollapsed ? '折りたたむ' : '開く';
    });

    // 沿革の削除
    list.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('.mytheme-history-delete');
        const items = list.querySelectorAll('.mytheme-history-item');

        if (!deleteButton) {
            return;
        }

        const item = deleteButton.closest('.mytheme-history-item');
        if (items.length > 1) {
            item.remove();
            updatehistoryCount();
        }
    });

    // ドラッグ＆ドロップで並び替え
    jQuery(list).sortable({
        handle: '.mytheme-history-header',
        update: function () {
            updatehistoryCount();
        }
    });

    // 初期表示の件数を更新
    updatehistoryCount();
});
