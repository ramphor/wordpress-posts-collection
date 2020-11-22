<?php
namespace Ramphor\Collection;

use Jankx\Template\Template;

class CollectionTemplate {
    protected static $loader;

    protected static function getLoader() {
        if (is_null(static::$loader)) {
            $collectionTemplateDir = sprintf('%s/templates', constant('RAMPHOR_COLLECTION_ROOT_DIR'));
            static::$loader = Template::getLoader(
                $collectionTemplateDir,
                apply_filters('ramphor_collection_theme_directory_name', 'templates/collection'),
                'wordpress'
            );
        }
        return static::$loader;
    }

    /**
     * @param   string|array $temlates
     * @param   array $data
     * @param   array  $context
     *
     * @return  string|null
     */
    public static function render() {
        $args = func_get_args();

        return call_user_func_array(array(
            static::getLoader(),
            'render'
        ), $args);
    }
}
