$dbh = new PDO(
    'mysql:host=localhost;dbname=qmsdb',
    'root',
    'SECRET_PASSWORD'
);
$db = pg_connect('CONNECTED_STRING');

$tables = [
    'products',
    'devices',
    'casts',
    'counters',
];

$source = new MySQLSource($tables, $dbh);
$receiver = new PGSQLReceiver($db, 'qmsdb');

$migrator = new SQLMigrator($source, $receiver);
$migrator->migrate();