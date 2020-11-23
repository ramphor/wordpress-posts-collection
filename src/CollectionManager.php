<?php
namespace Ramphor\Collection;

use Ramphor\Collection\RequestManager;
use Ramphor\Collection\Types\GlobalCollection;

class CollectionManager
{
    const VERSION = '0.0.1.34';

    protected static $instance;

    public $request;

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct()
    {
        $this->bootstrap();
        $this->includes();
        $this->init();
    }


    public function bootstrap()
    {
        define('RAMPHOR_COLLECTION_ROOT_DIR', dirname(__DIR__));
    }

    public function includes()
    {
        require_once RAMPHOR_COLLECTION_ROOT_DIR . '/functions.php';
    }

    public function init()
    {
        global $global_collection;
        $global_collection = new GlobalCollection();


        $this->request = new RequestManager();

        add_action('wp_enqueue_scripts', array($this, 'registerScripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'), 100);
    }

    public function registerScripts()
    {
        wp_register_script(
            'ramphor-collection',
            ramphor_collection_asset_url('js/collection.js'),
            array(),
            static::VERSION,
            true
        );

        $global_variables = array(
            'user_not_loggedin_callback' => '',
            'add_post_to_collection' => admin_url('admin-ajax.php?action=add_post_to_collection'),
            'remove_post_to_collection' => admin_url('admin-ajax.php?action=remove_post_to_collection'),
        );
        wp_localize_script(
            'ramphor-collection',
            'ramphor_collections',
            apply_filters('ramphor_collection_global_variables', $global_variables)
        );
    }

    public function enqueueScripts()
    {
        global $wp_scripts;
        $is_deps = apply_filters('ramphor_collection_script_is_dep', null);

        if (is_null($is_deps)) {
            foreach ($wp_scripts->registered as $script) {
                if ($is_deps) {
                    break;
                }
                $is_deps = in_array('ramphor-collection', $script->deps);
            }
        }

        if (!$is_deps) {
            wp_enqueue_script('ramphor-collection');
        }
    }
}
