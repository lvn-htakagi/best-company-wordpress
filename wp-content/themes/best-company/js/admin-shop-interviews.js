
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#mytheme-shop-interviews');
    
    if (!container) {
        return;
    }

    const list = container.querySelector('.mytheme-interview-list');
    const addButton = container.querySelector('#mytheme-shop-interview-add');
    const heading = container.querySelector('.mytheme-interviews-heading');

    // 件数と番号を更新
    function updateInterviewCount() {
        const items = list.querySelectorAll('.mytheme-interview-item');

        heading.textContent = `インタビュー（${items.length}件）`;

        items.forEach((item, index) => {
            item.querySelector('strong').textContent = `インタビュー ${index + 1}`;

            item.querySelector('input[type="hidden"]').name = `shop_interviews[${index}][id]`;
            item.querySelector('input[type="text"]').name = `shop_interviews[${index}][question]`;
            item.querySelector('textarea').name = `shop_interviews[${index}][answer]`;
        });
    }

    // インタビューを追加
    addButton.addEventListener('click', () => {
        const original = list.querySelector('.mytheme-interview-item');
        const newItem = original.cloneNode(true);

        // 入力内容をリセット
        newItem.querySelectorAll('input, textarea').forEach((field) => {
            field.value = '';
        });

        list.appendChild(newItem);

        updateInterviewCount();
    });

    // インタビューの折りたたみ
    list.addEventListener('click', (event) => {
        const toggleButton = event.target.closest('.mytheme-interview-toggle');

        if (!toggleButton) {
            return;
        }

        const item = toggleButton.closest('.mytheme-interview-item');
        const content = item.querySelector('.mytheme-interview-content');

        const isCollapsed = content.hidden;

        content.hidden = !isCollapsed;
        toggleButton.textContent = isCollapsed ? '折りたたむ' : '開く';
    });

    // インタビューの削除
    list.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('.mytheme-interview-delete');
        const items = list.querySelectorAll('.mytheme-interview-item');

        if (!deleteButton) {
            return;
        }

        const item = deleteButton.closest('.mytheme-interview-item');
        if (items.length > 1) {
            item.remove();
            updateInterviewCount();
        }
    });

    // ドラッグ＆ドロップで並び替え
    jQuery(list).sortable({
        handle: '.mytheme-interview-header',
        update: function () {
            updateInterviewCount();
        }
    });

    // 初期表示の件数を更新
    updateInterviewCount();
});
