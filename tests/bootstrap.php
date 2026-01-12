<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * ------------------------------------------------------------
 * Test doubles (stubs) to avoid real DB / hashing dependencies
 * ------------------------------------------------------------
 */

if (!defined('HASH_KEY')) {
    define('HASH_KEY', 'test_hash_key');
}

if (!class_exists('Hash')) {
    class Hash
    {
        public static function getHash(string $algo, string $pass, string $key): string
        {
            // Determinístico para tests
            return $algo . ':' . $key . ':' . $pass;
        }
    }
}

if (!class_exists('DummyStatement')) {
    class DummyStatement
    {
        public mixed $fetchResult = false;
        public array $fetchAllResult = [];
        public int $rowCountResult = 0;

        public function __construct(mixed $fetchResult = false, array $fetchAllResult = [], int $rowCountResult = 0)
        {
            $this->fetchResult = $fetchResult;
            $this->fetchAllResult = $fetchAllResult;
            $this->rowCountResult = $rowCountResult;
        }

        public function fetch(): mixed
        {
            return $this->fetchResult;
        }

        public function fetchall(): array
        {
            // El código usa "fetchall()" en algunos modelos
            return $this->fetchAllResult;
        }

        public function fetchAll(): array
        {
            return $this->fetchAllResult;
        }

        public function rowCount(): int
        {
            return $this->rowCountResult;
        }
    }
}

if (!class_exists('DummyPreparedStatement')) {
    class DummyPreparedStatement extends DummyStatement
    {
        public string $sql;
        public bool $executed = false;
        public array $lastParams = [];

        public function __construct(string $sql)
        {
            parent::__construct();
            $this->sql = $sql;
        }

        public function execute(array $params = []): bool
        {
            $this->executed = true;
            $this->lastParams = $params;
            return true;
        }
    }
}

if (!class_exists('Database')) {
    class Database
    {
        public static ?Database $lastInstance = null;

        /** @var list<string> */
        public array $queries = [];

        /** @var list<DummyPreparedStatement> */
        public array $preparedStatements = [];

        /** @var array<string, DummyStatement> */
        private array $queryResultsBySql = [];

        public function __construct()
        {
            self::$lastInstance = $this;
        }

        public function setQueryResult(string $sql, DummyStatement $stmt): void
        {
            $this->queryResultsBySql[$sql] = $stmt;
        }

        public function query(string $sql): DummyStatement
        {
            $this->queries[] = $sql;
            return $this->queryResultsBySql[$sql] ?? new DummyStatement();
        }

        public function prepare(string $sql): DummyPreparedStatement
        {
            $stmt = new DummyPreparedStatement($sql);
            $this->preparedStatements[] = $stmt;
            return $stmt;
        }
    }
}

/**
 * -----------------------
 * Load code under test
 * -----------------------
 */
$root = realpath(__DIR__ . '/../../') ?: (__DIR__ . '/../../');

$uploadPath = __DIR__ . '/class.upload.php';
if (file_exists($uploadPath)) {
    require_once $uploadPath;
}

$baseModelPath = $root . '/acceso datos/models/BaseModel.php';
$indexModelPath = $root . '/acceso datos/models/indexModel.php';
$userModelPath  = $root . '/acceso datos/models/userModel.php';

if (file_exists($baseModelPath)) {
    require_once $baseModelPath;
}
if (file_exists($indexModelPath)) {
    require_once $indexModelPath;
}
if (file_exists($userModelPath)) {
    require_once $userModelPath;
}

/**
 * Optional models (may vary in repo / may not be parsable if incomplete)
 */
$tutorModelPath   = $root . '/acceso datos/models/tutorModel.php';
$studentModelPath = $root . '/acceso datos/models/studentModel.php';
if (file_exists($tutorModelPath)) {
    require_once $tutorModelPath;
}
if (file_exists($studentModelPath)) {
    require_once $studentModelPath;
}

/**
 * ---------------
 * Upload tests
 * ---------------
 */
final class UploadClassTest extends TestCase
{
    private string $tmpDir;

    public static function setUpBeforeClass(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    protected function setUp(): void
    {
        $this->tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'tutor_link_tests_' . bin2hex(random_bytes(6));
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0777, true);
        }
    }

    protected function tearDown(): void
    {
        $this->rrmdir($this->tmpDir);
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    public function testLocalFileConstructorAndProcessReturnsContent(): void
    {
        if (!class_exists('upload')) {
            $this->markTestSkipped('class upload not loaded (libs/upload/class.upload.php missing).');
        }

        $content = "hola\nmundo\n";
        $file = $this->tmpDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, $content);

        $u = new upload($file);

        $this->assertTrue($u->uploaded);
        $this->assertTrue($u->no_upload_check);
        $this->assertSame('test.txt', $u->file_src_name);
        $this->assertSame(strlen($content), (int)$u->file_src_size);

        $returned = $u->process(null);
        $this->assertSame($content, $returned);
        $this->assertTrue($u->processed);
    }

    public function testProcessWritesFileToDestinationDirectory(): void
    {
        if (!class_exists('upload')) {
            $this->markTestSkipped('class upload not loaded (libs/upload/class.upload.php missing).');
        }

        $content = "archivo destino\n";
        $file = $this->tmpDir . DIRECTORY_SEPARATOR . 'source.txt';
        file_put_contents($file, $content);

        $dest = $this->tmpDir . DIRECTORY_SEPARATOR . 'out';
        $u = new upload($file);

        $u->file_new_name_body = 'copied_' . bin2hex(random_bytes(4));
        $u->process($dest);

        $this->assertTrue($u->processed, $u->error ?? 'process() failed');
        $expectedPath = $dest . DIRECTORY_SEPARATOR . $u->file_dst_name;

        // Nota: process() llama init() al final, pero deja file_dst_name asignado
        $this->assertNotSame('', $u->file_dst_name);
        $this->assertFileExists($expectedPath);
        $this->assertSame($content, file_get_contents($expectedPath));
    }
}

/**
 * ----------------
 * indexModel tests
 * ----------------
 */
final class IndexModelTest extends TestCase
{
    protected function setUp(): void
    {
        Database::$lastInstance = null;
    }

    public function testGetPostsReturnsFetchAll(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        // Crear modelo (internamente crea new Database() => Database::$lastInstance)
        $model = new indexModel();

        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $expected = [
            ['id' => 1, 'title' => 'a'],
            ['id' => 2, 'title' => 'b'],
        ];
        $db->setQueryResult('select * from posts', new DummyStatement(false, $expected));

        $rows = $model->getPosts();
        $this->assertSame($expected, $rows);
    }

    public function testGetPostCastsIdToIntInQuery(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = 'select * from posts where id=5';
        $expectedRow = ['id' => 5, 'title' => 'hola'];
        $db->setQueryResult($sql, new DummyStatement($expectedRow));

        $row = $model->getPost('5 OR 1=1');
        $this->assertSame($expectedRow, $row);

        $this->assertNotEmpty($db->queries);
        $this->assertSame($sql, end($db->queries));
    }

    public function testInsertPostUsesPrepareAndExecute(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $model->insertPost('t', 'b');

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];

        $this->assertSame('insert into posts VALUES (null, :title, :body)', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([':title' => 't', ':body' => 'b'], $stmt->lastParams);
    }

    public function testEditPostCastsIdAndUsesPlaceholders(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $model->editPost('7abc', 'new', 'body');

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];

        $this->assertSame('UPDATE posts SET title= :title, body= :body WHERE id= :id', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([':id' => 7, ':title' => 'new', ':body' => 'body'], $stmt->lastParams);
    }

    public function testDeletePostCastsIdInQuery(): void
    {
        if (!class_exists('indexModel')) {
            $this->markTestSkipped('indexModel not loaded.');
        }

        $model = new indexModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $model->deletePost('9xyz');

        $this->assertNotEmpty($db->queries);
        $this->assertSame('DELETE FROM posts WHERE id = 9', end($db->queries));
    }
}

/**
 * ----------------
 * userModel tests
 * ----------------
 */
final class UserModelTest extends TestCase
{
    protected function setUp(): void
    {
        Database::$lastInstance = null;
    }

    public function testIsEmailAvaliableReturnsTrueWhenEmailExists(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel not loaded.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = "SELECT email FROM users WHERE email = 'a@b.com'";
        $db->setQueryResult($sql, new DummyStatement(['email' => 'a@b.com']));

        $this->assertTrue($model->isEmailAvaliable('a@b.com'));
    }

    public function testIsEmailAvaliableReturnsFalseWhenEmailDoesNotExist(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel not loaded.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = "SELECT email FROM users WHERE email = 'missing@b.com'";
        $db->setQueryResult($sql, new DummyStatement(false));

        $this->assertFalse($model->isEmailAvaliable('missing@b.com'));
    }

    public function testRegisterUserRole1TriggersRegisterStudentAndUsesHash(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel not loaded.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $studentInsertSql = 'INSERT INTO students(name,apellido,cedula,carrera,email) VALUES (:name,:apellido,:cedula,:carrera,:email)';

        $ok = $model->registerUser(
            'N', 'A', '123', '555', 'n@a.com', '10',
            1, 'secret', $studentInsertSql
        );

        $this->assertTrue($ok);

        // 1) INSERT principal a users
        $this->assertNotEmpty($db->preparedStatements);
        $usersStmt = $db->preparedStatements[0];
        $this->assertStringContainsString('INSERT INTO users', $usersStmt->sql);
        $this->assertSame(Hash::getHash('sha1', 'secret', HASH_KEY), $usersStmt->lastParams[':pass']);

        // 2) INSERT a students (role==1)
        $this->assertGreaterThanOrEqual(2, count($db->preparedStatements));
        $studentStmt = $db->preparedStatements[1];
        $this->assertSame($studentInsertSql, $studentStmt->sql);
        $this->assertTrue($studentStmt->executed);
        $this->assertSame([
            ':name' => 'N',
            ':apellido' => 'A',
            ':cedula' => '123',
            ':carrera' => '10',
            ':email' => 'n@a.com',
        ], $studentStmt->lastParams);
    }

    public function testRegisterUserRole2TriggersRegisterTutor(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel not loaded.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $tutorInsertSql = 'INSERT INTO tutors(name,apellido,cedula,telefono,email) VALUES (:name,:apellido,:cedula,:telefono,:email)';

        $ok = $model->registerUser(
            'N', 'A', '123', '555', 'n@a.com', '10',
            2, 'secret', $tutorInsertSql
        );

        $this->assertTrue($ok);

        $this->assertNotEmpty($db->preparedStatements);
        $this->assertGreaterThanOrEqual(2, count($db->preparedStatements));

        $tutorStmt = $db->preparedStatements[1];
        $this->assertSame($tutorInsertSql, $tutorStmt->sql);
        $this->assertTrue($tutorStmt->executed);
        $this->assertSame([
            ':name' => 'N',
            ':apellido' => 'A',
            ':cedula' => '123',
            ':telefono' => '555',
            ':email' => 'n@a.com',
        ], $tutorStmt->lastParams);
    }

    public function testGetCedulaReturnsCount(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel not loaded.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = "SELECT count(id) as count FROM users WHERE cedula = 'V-1'";
        $db->setQueryResult($sql, new DummyStatement(['count' => 3]));

        $count = $model->getCedula('V-1');
        $this->assertSame(3, $count);
    }
}