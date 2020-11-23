<?php
namespace Ramphor\Collection;

class Collection
{
    public $id;
    public $name;
    protected $active = true;

    public function __construct($id, $args = array())
    {
        $this->set_collection_id($id);

        if (!is_null($args)) {
            $this->parseArgs($args);
        }
    }

    public function set_collection_id($id)
    {
        $this->id = $id;
    }

    public function parseArgs($args)
    {
        $args = wp_parse_args($args, array(
            'name' => $this->id,
            'public' => true,
        ));
        $this->name = $args['name'];
        $this->active = $args['public'];
    }
}
