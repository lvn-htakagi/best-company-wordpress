<?php
function mytheme_can_save_shop($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return false;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return false;
    }

    if (get_post_type($post_id) !== 'shop') {
        return false;
    }

    return true;
}