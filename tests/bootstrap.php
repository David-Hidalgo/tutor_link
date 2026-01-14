<?php
declare(strict_types=1);

$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    @session_start();
}

if (!isset($_SESSION) || !is_array($_SESSION)) {
    $_SESSION = [];
}

if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1;
}

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
$root = realpath(__DIR__ . '/..') ?: (__DIR__ . '/..');

$uploadPath = $root . '/libs/class.upload.php';
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