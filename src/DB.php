<?php
namespace Ramphor\Collection;

class DB
{
    public static function setup()
    {
        $db = new static();
        $db->create_global_collections_table();
        $db->create_user_collections_table();
    }

    protected function create_table($table_name, $table_fields)
    {
        if (!is_array($table_fields)) {
            return;
        }
        global $wpdb;
        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS %s%s',
            $wpdb->prefix,
            $table_name
        );

        $sql .= '(' . PHP_EOL;
        foreach ($table_fields as $table_field => $option) {
            $sql .= sprintf(
                "\t%s %s," . PHP_EOL,
                $table_field,
                is_array($option) ? implode(' ', $option) : $option
            );
        }
        $sql = rtrim($sql, ',' . PHP_EOL) . PHP_EOL . ')';
        $sql .= sprintf(' ENGINE=InnoDB DEFAULT CHARSET=%s;', $wpdb->get_charset_collate());

        return $wpdb->query($sql);
    }

    public function create_global_collections_table()
    {
        $this->create_table('ramphor_collection_global', array(
            'ID' => array('BIGINT', 'UNSIGNED', 'AUTO_INCREMENT'),
            'collection' => array('VARCHAR(60) NOT NULL'),
            'post_id' => array('BIGINT', 'UNSIGNED', 'NOT NULL'),
            'user_id' => array('BIGINT', 'UNSIGNED', 'NOT NULL'),
            'added_at' => array('TIMESTAMP', 'NULL'),
            'deleted_at' => array('TIMESTAMP', 'NULL'),
            'PRIMARY KEY' => '(ID)'
        ));
    }

    public function create_user_collections_table()
    {
        $this->create_table('ramphor_collection_user', array(
            'ID' => array('BIGINT', 'UNSIGNED', 'AUTO_INCREMENT'),
            'collection_name' => array('VARCHAR(60) NOT NULL'),
            'user_id' => array('BIGINT', 'UNSIGNED', 'NOT NULL'),
            'added_at' => array('TIMESTAMP', 'NULL'),
            'deleted_at' => array('TIMESTAMP', 'NULL'),
            'PRIMARY KEY' => '(ID)'
        ));
        $this->create_table('ramphor_collection_user_subscribe', array(
            'ID' => array('BIGINT', 'UNSIGNED', 'AUTO_INCREMENT'),
            'user_collection_id' => array('BIGINT', 'UNSIGNED'),
            'post_id' => array('BIGINT', 'UNSIGNED', 'NOT NULL'),
            'added_at' => array('TIMESTAMP', 'NULL'),
            'deleted_at' => array('TIMESTAMP', 'NULL'),
            'PRIMARY KEY' => '(ID)'
        ));
    }
}
