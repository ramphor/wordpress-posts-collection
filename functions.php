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

function check_post_in_collection($post_id, $collection, $user_id = null) {
}

function show_post_in_collection_status($post_id, $collection, $user_id = null) {
    echo '<div class="the-collection-status">';
        if (empty($user_id) || check_post_in_collection($post_id, $collection, $user_id)) {
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
