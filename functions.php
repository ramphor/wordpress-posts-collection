<?php
use Ramphor\Collection\GlobalCollection;
use Ramphor\Collection\CollectionTemplate;


function ramphor_collection_asset_url($path = '') {
    $abspath = constant('ABSPATH');
    $collectionRoot = constant('RAMPHOR_COLLECTION_ROOT_DIR');
    if (PHP_OS === 'WINNT') {
        $abspath = str_replace('\\', '/', $abspath);
        $collectionRoot = str_replace('\\', '/', $collectionRoot);
    }
    $assetDirectoryUrl = str_replace($abspath, site_url('/'), $collectionRoot);
    return sprintf('%s/assets/%s', $assetDirectoryUrl, $path);
}

function register_post_collection($collection, $args = array()) {
    global $global_collection;
    if (!is_a($global_collection, GlobalCollection::class)) {
        return;
    }
    $global_collection->add_collection($collection, $args);
}

function check_post_in_collection($post_id, $collection, $user_id = null, $skip_delete_flag = false) {
    global $wpdb;
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }
    $sql = $wpdb->prepare(
        "SELECT ID FROM {$wpdb->prefix}ramphor_collection_global g WHERE g.post_id=%d AND collection=%s AND g.user_id=%d",
        $post_id,
        $collection,
        $user_id
    );
    if (!$skip_delete_flag) {
        $sql .= ' AND g.deleted_at IS NULL';
    }
    return (int) $wpdb->get_var($sql);
}

function add_post_to_collection($post_id, $collection, $user_id = null) {
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }
    global $wpdb;
    $collection_added_id = check_post_in_collection($post_id, $collection, $user_id, true);
    if ($collection_added_id > 0) {
        $sql = $wpdb->prepare(
            "UPDATE {$wpdb->prefix}ramphor_collection_global SET post_id=%d, collection=%s, user_id=%d, deleted_at=NULL WHERE ID=%d",
            $post_id,
            $collection,
            $user_id,
            $collection_added_id
        );
    } else {
        $sql = $wpdb->prepare(
            "INSERT INTO {$wpdb->prefix}ramphor_collection_global(post_id, collection, user_id, added_at) VALUES(%d, %s, %d, NOW())",
            $post_id,
            $collection,
            $user_id
        );
    }
    return $wpdb->query($sql);
}

function remove_post_to_collection($post_id, $collection, $user_id = null) {
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }
    $collection_added_id = check_post_in_collection($post_id, $collection, $user_id, true);
    if ($collection_added_id <= 0) {
        return true;
    }

    global $wpdb;
    if (apply_filters("ramphor_collection_{$collection}_soft_delete", true)) {
        $sql = $wpdb->prepare(
            "UPDATE {$wpdb->prefix}ramphor_collection_global SET deleted_at=NOW() WHERE ID=%d",
            $collection_added_id
        );
    } else {
        $sql = $wpdb->prepare(
            "DELETE FROM {$wpdb->prefix}ramphor_collection_global WHERE ID=%d",
            $collection_added_id
        );
    }
    return $wpdb->query($sql);
}

function show_post_in_collection_status($post_id, $collection, $user_id = null) {
    if (is_null($user_id)) {
        $user_id = get_current_user_id();
    }

    echo '<div class="the-collection-status">';
        if (empty($user_id) || check_post_in_collection($post_id, $collection, $user_id, false) <= 0) {
            echo '<div
                class="collection-action"
                data-collection-action="add"
                data-collection="' . $collection . '"
                data-post-id="' . $post_id . '"
                data-nonce="' . wp_create_nonce(sprintf('#%s-%d', $collection, $post_id)) . '"
            >';
            CollectionTemplate::render(array(
                str_replace('_', '-', $collection) . '/not-exists',
                'not-exists',
            ));

        } else {
            echo '<div
                class="collection-action"
                data-collection-action="remove"
                data-collection="' . $collection . '"
                data-post-id="' . $post_id . '"
                data-nonce="' . wp_create_nonce(sprintf('#%s-%d', $collection, $post_id)) . '"
            >';
            CollectionTemplate::render(array(
                str_replace('_', '-', $collection) . '/exists',
                'exists',
            ));
        }
        echo '</div>';
    echo '</div>';
}
