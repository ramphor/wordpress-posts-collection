<?php
namespace Ramphor\Collection;

use Jankx\Template\Template;

class CollectionTemplate
{
    protected static $engine;

    protected static function getEngine()
    {
        if (is_null(static::$engine)) {
            $collectionTemplateDir = sprintf('%s/templates', constant('RAMPHOR_COLLECTION_ROOT_DIR'));
            static::$engine = Template::createEngine(
                'ramphor_collection',
                apply_filters('ramphor_collection_theme_directory_name', 'templates/collection'),
                $collectionTemplateDir,
                'wordpress'
            );
        }
        return static::$engine;
    }

    /**
     * @param   string|array $temlates
     * @param   array $data
     * @param   array  $context
     *
     * @return  string|null
     */
    public static function render()
    {
        $args = func_get_args();

        return call_user_func_array(array(
            static::getEngine(),
            'render'
        ), $args);
    }
}
