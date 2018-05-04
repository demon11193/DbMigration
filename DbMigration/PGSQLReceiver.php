<?php
namespace MyTools\DbMigration;

/**
 * Description of PGSQLReceiver
 *
 * @author demon112
 */
class PGSQLReceiver implements DBReceiver {
    
    const TYPE_MATCHING = [
        'varchar' => 'VARCHAR',
        'int' => 'INT',
        'date' => 'DATE',
        'datetime' => 'TIMESTAMP',
        'text' => 'TEXT'
    ];
    
    private $db, $schema;
    
    /**
     * 
     * @param resource $db подключение pg_connect
     * @param string $schema Схема
     */
    function __construct($db, string $schema) {
        $this->db = $db;
        $this->schema = $schema;
    }
    
    public function addTable(string $table_name, TableStrucure $table_structure) {
        
        $sql_fields = "";
        
        $columns = $table_structure->getColumns();
        $pk = $table_structure->getPkColumn();
        
        foreach ($columns as $col) {
            if ($sql_fields) {
                $sql_fields .= ', ';
            }
            $sql_fields .= "$col";
            
            $type = $table_structure->getColumnType($col);
            $is_pk = $pk == $col;
            
            if (!isset(self::TYPE_MATCHING[$type])) {
                throw new \Exception('Неподдерживаемый тип');
            }
            
            $sql_fields .= " " . self::TYPE_MATCHING[$type];
            
            if (self::TYPE_MATCHING[$type] == 'VARCHAR') {
                $length = $table_structure->getColumnTypeLength($col);
                if ($length) {
                    $sql_fields .= "($length)";
                }
            }
            
            if ($is_pk) {
                $sql_fields .= " PRIMARY KEY";
            }
            if (!$table_structure->isColumnNull($col)) {
                $sql_fields .= " NOT NULL";
            }
            
        }
        
        $sql = "CREATE TABLE {$this->schema}.$table_name ($sql_fields); ";
        
        foreach ($columns as $col) {
            if ($table_structure->isUniqueColumn($col)) {
                $sql .= " CREATE UNIQUE INDEX {$table_name}_{$col}_uindex ON {$this->schema}.$table_name ({$col});";
            }
        }
        
        $res = pg_query($this->db, $sql);
        if (!$res) {
            throw new \Exception('Ошибка при выполнении запроса: ' . $sql);
        }
        
    }

    public function addTableData(string $table_name, array $data) {
        if (empty($data)) {
            return;
        }
        $fields = "";
        $values = "";
        
        foreach (current($data) as $key=>$row) {
            if ($fields) {
                $fields .= ',';
            }
            $fields .= strtolower("\"$key\"");
        }
        
        foreach ($data as $row) {
            $ins = "";
            foreach ($row as $value) {
                if ($ins) {
                    $ins .= ',';
                }
                $ins .= $this->valueForSql($value);
            }
            if ($values) {
                $values .= ",";
            }
            $values .= "($ins)";
        }
        
        $sql = "INSERT INTO {$this->schema}.$table_name ($fields) VALUES $values;";
        $res = pg_query($this->db, $sql);
        if (!$res) {
            throw new \Exception('Ошибка при выполнении запроса: ' . $sql);
        }
    }

    public function clearTable(string $table_name) {
        $sql = "TRUNCATE {$this->schema}.$table_name RESTART IDENTITY;";
        $res = pg_query($this->db, $sql);
        if (!$res) {
            throw new \Exception('Ошибка при выполнении запроса: ' . $sql);
        }
    }
    
    public function removeTable(string $table_name) {
        $sql = "DROP TABLE {$this->schema}.$table_name;";
        $res = pg_query($this->db, $sql);
        if (!$res) {
            throw new \Exception('Ошибка при выполнении запроса: ' . $sql);
        }
    }

    public function compareStructTable(string $table_name, TableStrucure $table_structure): bool {
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_schema = '{$this->schema}' AND table_name = '$table_name'";
        $res = pg_query($this->db, $sql);
        
        $result = pg_fetch_all_columns($res);
        foreach ($table_structure->getColumns() as $col) {
            if (!in_array($col, $result)) {
                return false;
            }
        }
        // Тут, по хорошему, нужны ещё проверочки, но пока хватит
        return true;
    }

    public function hasTable(string $table_name): bool {
        $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = '{$this->schema}' AND table_name = '$table_name'";
        $res = pg_query($this->db, $sql);
        if (!$res) {
            throw new \Exception('Ошибка при выполнении запроса: ' . $sql);
        }
        $d = pg_fetch_row($res, 0);
        return !!$d;
    }
    
    private function valueForSql($value) {
        if (is_bool($value)) {
            return $value?'TRUE':'FALSE';
        }
        if (is_null($value)) {
            return "NULL";
        }
        
        return "'".pg_escape_string($value)."'";
    }

}
