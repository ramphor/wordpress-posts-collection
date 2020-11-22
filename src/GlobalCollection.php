<?php
namespace Ramphor\Collection;

use Ramphor\Collection\Collection;

class GlobalCollection {
    protected static $globalCollections = array();

    public function add_collection($collection, $args = array()) {
        $collection = new Collection($collection, $args);
        if (!$collection->id) {
            return false;
        }
        static::$globalCollections[$collection->id] = $collection;
    }
}
