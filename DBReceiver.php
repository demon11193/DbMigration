<?php
namespace Vladlink\DbMigration;

/**
 *
 * @author demon112
 */
interface DBReceiver {
    
    /**
     * Проверка существования таблицы
     * 
     * @param string $table_name
     * @return bool
     */
    function hasTable(string $table_name):bool;
    /**
     * Проверка структуры
     * 
     * @param string $table_name
     * @param TableStrucure $table_structure
     * @return bool
     */
    function compareStructTable(string $table_name, TableStrucure $table_structure):bool;
    /**
     * Удаление таблицы
     */
    function removeTable(string $table_name);
    
    /**
     * Добавление таблицы
     * 
     * @param string $table_name
     * @param TableStrucure $table_structure
     */
    function addTable(string $table_name, TableStrucure $table_structure);
    
    /**
     * Добавление данных в таблицу
     * 
     * @param string $table_name
     * @param array $data
     */
    function addTableData(string $table_name, array $data);
    
    /**
     * Очистить таблицу
     * 
     * @param string $table_name назнание таблицы
     */
    function clearTable(string $table_name);
    
}
