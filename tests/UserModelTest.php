<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

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

        $this->assertNotEmpty($db->preparedStatements);
        $usersStmt = $db->preparedStatements[0];
        $this->assertStringContainsString('INSERT INTO users', $usersStmt->sql);
        $this->assertSame(Hash::getHash('sha1', 'secret', HASH_KEY), $usersStmt->lastParams[':pass']);

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
