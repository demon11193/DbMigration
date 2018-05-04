<?php
namespace Vladlink\DbMigration;

/**
 * Description of MySQLTableStructure
 *
 * @author demon112
 */
class MySQLTableStructure implements TableStrucure {
    
    /**
     * Массив соотвествия типов
     */
    const TYPE_MATCHING = [
        'bigint' => 'int',
        'tinyint' => 'int',
        'int' => 'int',
        'varchar' => 'varchar',
        'char' => 'varchar',
        'timestamp' => 'datetime'
    ];
    
    private $columns;
    
    public function __construct(array $columns) {
        $this->columns = $columns;
    }
    
    public function getColumnType($column_name): string {
        $type = $this->getColumnByName($column_name)['Type'];
        $pos = strrpos($type, "(");
        if ($pos !== false) {
            $type = substr($type, 0, $pos);
        }
        if (!isset(self::TYPE_MATCHING[$type])) {
            throw new \Exception('Тип "'.$type.'" не поддерживается MySQLTableStructure');
        }
        return self::TYPE_MATCHING[$type];
    }

    public function getColumnTypeLength($column_name): int {
        $type = $this->getColumnByName($column_name)['Type'];
        $pos = strrpos($type, "(");
        if ($pos !== false) {
            $len = substr($type, $pos+1, strlen($type)-$pos-2);
            return $len;
        }
        return 0;
    }

    public function getColumns(): array {
        $res = [];
        foreach ($this->columns as $col) {
            $res[] = $col['Field'];
        }
        return $res;
    }
    
    public function isColumnNull($column_name): bool {
        return $this->getColumnByName($column_name)['Null'] != 'NO';
    }
    
    public function getPkColumn(): string {
        foreach ($this->columns as $column) {
            if ($column['Key'] == 'PRI') {
                return $column['Field'];
            }
        }
        throw new \Exception('Колонка с UK не найдена.');
    }
    
    public function isUniqueColumn($column_name): bool {
        return $this->getColumnByName($column_name)['Key'] == 'UNI';
        
    }
    
    private function getColumnByName($column_name) {
        foreach ($this->columns as $column) {
            if ($column['Field'] == $column_name) {
                return $column;
            }
        }
        throw new \Exception('Колонка не найдена.');
    }

}
