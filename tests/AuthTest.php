<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Autenticación real del proyecto: userModel::authenticateUser().
 */
final class AuthTest extends TestCase
{
    protected function setUp(): void
    {
        Database::$lastInstance = null;
    }

    public function testAuthenticateUserReturnsRowWhenFound(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel no está cargado.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $email = 'a@b.com';
        $pass = 'secret';
        $hash = Hash::getHash('sha1', $pass, HASH_KEY);

        $sql = "SELECT * FROM users " .
            "WHERE email = '$email'" .
            "AND pass = '$hash'";

        $expectedRow = ['id' => 1, 'email' => $email];
        $db->setQueryResult($sql, new DummyStatement($expectedRow));

        $row = $model->authenticateUser($email, $pass);
        $this->assertSame($expectedRow, $row);

        $this->assertNotEmpty($db->queries);
        $this->assertSame($sql, end($db->queries));
    }

    public function testAuthenticateUserReturnsFalseWhenNotFound(): void
    {
        if (!class_exists('userModel')) {
            $this->markTestSkipped('userModel no está cargado.');
        }

        $model = new userModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $email = 'missing@b.com';
        $pass = 'secret';
        $hash = Hash::getHash('sha1', $pass, HASH_KEY);

        $sql = "SELECT * FROM users " .
            "WHERE email = '$email'" .
            "AND pass = '$hash'";

        $db->setQueryResult($sql, new DummyStatement(false));

        $row = $model->authenticateUser($email, $pass);
        $this->assertFalse($row);
    }
}