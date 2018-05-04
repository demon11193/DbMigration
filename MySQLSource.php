<?php
namespace Vladlink\DbMigration;

/**
 * Description of PGSQLSource
 *
 * @author demon112
 */
class MySQLSource implements DBSource {
    
    private $tables;
    private $dbh;
    
    public function __construct(array $tables, \PDO $dbh) {
        $this->tables = $tables;
        $this->dbh = $dbh;
    }
    
    public function tables(): array {
        return $this->tables;
    }

    public function countRows(string $table_name): int {
        return $this->dbh->query("SELECT COUNT(*) as count FROM " . $table_name)->fetchColumn();
    }

    public function getRows(string $table_name, int $offset, int $limit):array {
        return $this->dbh->query("SELECT * FROM " . $table_name . ' LIMIT '.$limit.' OFFSET '.$offset)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTableStructure(string $table_name): TableStrucure {
        $res = $this->dbh->query("SHOW COLUMNS FROM $table_name")->fetchAll(\PDO::FETCH_ASSOC);
        return new MySQLTableStructure($res);
    }

}
