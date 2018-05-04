<?php

namespace MyTools\DbMigration;

/**
 * Description of MySqlMigrator
 *
 * @author demon112
 */
class SQLMigrator {
    
    /**
     * Количество данных за итерацию
     */
    const LIMIT_DATA_ITERATION = 1000;
    
    private $source;
    private $receiver;
    
    function __construct(DBSource $source, DBReceiver $receiver) {
        $this->source = $source;
        $this->receiver = $receiver;
    }
    
    /**
     * Миграция всех таблиц
     */
    function migrate() {
        $tables = $this->source->tables();
        foreach ($tables as $table_name) {
            $this->migrateTable($table_name);
        }
    }
    
    /**
     * Миграция таблицы
     * 
     * @param type $table_name
     */
    private function migrateTable($table_name) {
        $table_structure = $this->source->getTableStructure($table_name);
        
        if ($this->receiver->hasTable($table_name)) {
            if (!$this->receiver->compareStructTable($table_name, $table_structure)) {
                $this->receiver->removeTable($table_name);
                $this->receiver->addTable($table_name, $table_structure);
            }
        }
        else {
            $this->receiver->addTable($table_name, $table_structure);
        }
        
        $count = $this->source->countRows($table_name);
        
        if ($count > 0) {
            $this->receiver->clearTable($table_name);
            
            for ($i = 0; $i <= $count; $i += self::LIMIT_DATA_ITERATION) {
                $data = $this->source->getRows($table_name, $i, self::LIMIT_DATA_ITERATION);
                $this->receiver->addTableData($table_name, $data);
            }
        }
        else {
            $this->receiver->clearTable($table_name);
        }
    }
    
}
