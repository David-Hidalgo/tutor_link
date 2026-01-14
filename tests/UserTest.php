<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Tests de usuario basados en userModel real.
 */
final class UserTest extends TestCase
{
    private userModel $userModel;

    protected function setUp(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel no está cargado.');
        }

        Database::$lastInstance = null;
        $this->userModel = new userModel();
    }

    public function testGetUserCIReturnsStatementAndRunsQuery(): void
    {
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = "SELECT count(id) as 'count' FROM users WHERE cedula = 'V-1'";
        $db->setQueryResult($sql, new DummyStatement(['count' => 1]));

        $stmt = $this->userModel->getUserCI('V-1');
        $this->assertInstanceOf(DummyStatement::class, $stmt);

        $this->assertNotEmpty($db->queries);
        $this->assertSame($sql, end($db->queries));
    }

    public function testGetUserReturnsFetchedRow(): void
    {
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $sql = "SELECT * FROM users WHERE id = 5 AND code = 'abc'";
        $expected = ['id' => 5, 'code' => 'abc'];
        $db->setQueryResult($sql, new DummyStatement($expected));

        $row = $this->userModel->getUser(5, 'abc');
        $this->assertSame($expected, $row);
    }

    public function testActivateUserRunsUpdateQuery(): void
    {
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $this->userModel->activateUser(5, 'abc');

        $this->assertNotEmpty($db->queries);
        $this->assertSame("UPDATE users SET status = 1 WHERE id = 5 and code = 'abc'", end($db->queries));
    }
}
