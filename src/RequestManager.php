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

    protected function parse_request_input()
    {
        $pre = apply_filters('ramphor_collection_parse_request_input', null, $_POST);
        if (!is_null($pre)) {
            return $pre;
        }
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function verify_request($collection, $post_id, $nonce)
    {
        $strOfHash = sprintf('#%s-%d', $collection, $post_id);
        return wp_verify_nonce($nonce, $strOfHash);
    }

    public function add_post_to_collection()
    {
        $request = $this->parse_request_input();
        if (!is_array($request)) {
            wp_send_json_error(array(
                'error' => 'parse_data_error',
                'message' => __('Parse data occur error', 'ramphor_collection'),
            ));
        }
        $request = wp_parse_args($request, array(
            'collection' => '',
            'post_id' => 0,
            'nonce' => ''
        ));

        $collection = $request['collection'];
        $post_id = $request['post_id'];
        $nonce = $request['nonce'];

        if (!$this->verify_request($collection, $post_id, $nonce)) {
            wp_send_json_error(array(
                'error' => 'bad_request',
                'message' => __('Bad request', 'ramphor_collection'),
            ));
        }
        if (!add_post_to_collection($post_id, $collection)) {
            wp_send_json_error(array(
                'error' => 'db_error',
                'message' => __('Database query occur error', 'ramphor_collection'),
            ));
        }

        wp_send_json_success(array(
            'new_html' => CollectionTemplate::render(array(
                str_replace('_', '-', $collection) . '/exists',
                'exists',
            ), array(), null, false)
        ));
    }

    public function remove_post_to_collection()
    {
        $request = $this->parse_request_input();
        if (!is_array($request)) {
            wp_send_json_error(array(
                'error' => 'parse_data_error',
                'message' => __('Parse data occur error', 'ramphor_collection'),
            ));
        }
        $request = wp_parse_args($request, array(
            'collection' => '',
            'post_id' => 0,
            'nonce' => ''
        ));

        $collection = $request['collection'];
        $post_id = $request['post_id'];
        $nonce = $request['nonce'];

        if (!$this->verify_request($request['collection'], $request['post_id'], $request['nonce'])) {
            wp_send_json_error(array(
                'error' => 'bad_request',
                'message' => __('Bad request', 'ramphor_collection'),
            ));
        }

        if (!remove_post_to_collection($post_id, $collection)) {
            wp_send_json_error(array(
                'error' => 'db_error',
                'message' => __('Database query occur error', 'ramphor_collection'),
            ));
        }

        wp_send_json_success(array(
            'new_html' => CollectionTemplate::render(array(
                str_replace('_', '-', $collection) . '/not-exists',
                'not-exists',
            ), array(), null, false)
        ));
    }
}
