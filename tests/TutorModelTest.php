<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TutorModelTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!isset($_SESSION['id'])) {
            $_SESSION['id'] = 1;
        }
        Database::$lastInstance = null;
    }

    public function testGetPerfilReturnsFetchAll(): void
    {
        if (!class_exists('tutorModel')) {
            $this->markTestSkipped('tutorModel not loaded.');
        }

        $model = new tutorModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $expected = [
            ['id' => 1, 'title' => 'a'],
            ['id' => 2, 'title' => 'b'],
        ];
        $db->setQueryResult('select * from posts', new DummyStatement(false, $expected));

        $rows = $model->getPerfil();
        $this->assertSame($expected, $rows);
    }

    public function testGetEscuelaReturnsFetchAll(): void
    {
        if (!class_exists('tutorModel')) {
            $this->markTestSkipped('tutorModel not loaded.');
        }

        $model = new tutorModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $expected = [
            ['id' => 1, 'escuela' => 'A'],
            ['id' => 2, 'escuela' => 'B'],
        ];
        $db->setQueryResult('SELECT * FROM escuela WHERE 1', new DummyStatement(false, $expected));

        $rows = $model->getEscuela();
        $this->assertSame($expected, $rows);
    }

    public function testGetSubjectAppendsOptionString(): void
    {
        if (!class_exists('tutorModel')) {
            $this->markTestSkipped('tutorModel not loaded.');
        }

        $model = new tutorModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $option = "AND carrera_materia.idcarrera = 10";
        $sql = "SELECT DISTINCT  subject.id, subject.materia_des FROM\n         `subject` INNER JOIN carrera_materia on carrera_materia.idmateria = subject.id WHERE 1  $option";

        $expected = [
            ['id' => 1, 'materia_des' => 'Mate'],
        ];
        $db->setQueryResult($sql, new DummyStatement(false, $expected));

        $rows = $model->getsubject($option);
        $this->assertSame($expected, $rows);
    }

    public function testEditPerfilUsesPrepareAndParams(): void
    {
        if (!class_exists('tutorModel')) {
            $this->markTestSkipped('tutorModel not loaded.');
        }

        $model = new tutorModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $result = $model->editPerfil(5, 'N', 'A', '555', 'img.jpg');

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];
        $this->assertStringContainsString('UPDATE users SET name=:name', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([
            ':name' => 'N',
            ':apellido' => 'A',
            ':telefono' => '555',
            ':foto' => 'img.jpg',
        ], $stmt->lastParams);

        $this->assertSame(0, $result);
    }
}
