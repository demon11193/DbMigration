<?php
namespace Vladlink\DbMigration;

/**
 * Description of DBSource
 *
 * @author demon112
 */
interface DBSource {
    
    /**
     * Список таблиц для миграции
     * @return array
     */
    function tables():array;
    
    /**
     * Возвращает структуру таблицы
     * 
     * @param string $table_name назнание таблицы
     * @return TableStrucure Структура таблицы
     */
    function getTableStructure(string $table_name):TableStrucure;
    
    /**
     * Количество элементов в таблице
     * 
     * @param string $table_name назнание таблицы
     * @return int
     */
    function countRows(string $table_name):int;
    
    /**
     * Получить данные из таблице
     * 
     * @param string $table_name назнание таблицы
     * @param int $offset смещение
     * @param int $limit максимальное количество
     * @return array
     */
    function getRows(string $table_name, int $offset, int $limit):array;
    
}
