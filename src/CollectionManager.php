<?php
namespace Ramphor\Collection;

class CollectionManager {
    protected static $instance;

    public static function getInstance() {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct() {
        $this->bootstrap();
        $this->includes();
        $this->init();
    }


    public function bootstrap() {
        define('RAMPHOR_COLLECTION_ROOT_DIR', dirname(__DIR__));
    }

    public function includes() {
        require_once RAMPHOR_COLLECTION_ROOT_DIR . '/functions.php';
    }

    public function init() {
        global $global_collection;
        $global_collection = new GlobalCollection();
    }
}
