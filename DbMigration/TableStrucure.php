<?php
namespace MyTools\DbMigration;

/**
 * Структура таблицы
 * 
 * @author demon112
 */
interface TableStrucure {
    
    const TYPES = ['varchar','int','date','datetime','text'];
    
    /**
     * Получить массив колонок с названиями
     */
    function getColumns():array;
    /**
     * Тип колонки: поддерживаемы типы: TYPES ['varchar','int','date','datetime','text']
     * 
     * @param string $column_name
     */
    function getColumnType($column_name):string;
    
    /**
     * Размер типа
     * 
     * @param int $column_name
     * @return int Размер. 0 - безразмерный
     */
    function getColumnTypeLength($column_name):int;
    
    /**
     * Колонка nullable?
     * 
     * @param bool $column_name
     */
    function isColumnNull($column_name):bool;
    
    /**
     * Возращает колонку первичного ключа
     */
    function getPkColumn():string;
    
    /**
     * Является ли колонка уникальной
     * 
     * @param type $column_name
     */
    function isUniqueColumn($column_name):bool;
    
}
