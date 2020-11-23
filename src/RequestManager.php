<?php
namespace Ramphor\Collection;

class RequestManager
{
    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    public function init()
    {
        add_action('wp_ajax_add_post_to_collection', array($this, 'add_post_to_collection'));
        add_action('wp_ajax_nopriv_add_post_to_collection', array($this, 'user_not_logged_in'));

        add_action('wp_ajax_remove_post_to_collection', array($this, 'remove_post_to_collection'));
        add_action('wp_ajax_nopriv_remove_post_to_collection', array($this, 'user_not_logged_in'));
    }

    public function user_not_logged_in()
    {
        wp_send_json_error(array(
            'error' => 'not_logged_in',
            'message' => __('User is not logged in', 'ramphor_collection')
        ));
    }

    public function add_post_to_collection()
    {
    }

    public function remove_post_to_collection()
    {
    }
}
