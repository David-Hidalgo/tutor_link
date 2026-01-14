<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Tests de lógica de negocio usando métodos reales existentes en tutorModel.
 */
final class BusinessLogicTest extends TestCase
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

    public function testEditSessionUsesPrepareAndExecute(): void
    {
        if (!class_exists('tutorModel')) {
            $this->markTestSkipped('tutorModel no está cargado.');
        }

        $model = new tutorModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $result = $model->editSession(10, 'Lunes', 9, 11);

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];
        $this->assertSame('UPDATE sessions SET dia =:dia, inicio=:inicio,  final=:final  WHERE id_horario= 10', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([':dia' => 'Lunes', ':inicio' => 9, ':final' => 11], $stmt->lastParams);

        $this->assertSame(0, $result);
    }

    public function testCancelRequestParticularSetsStatusToOne(): void
    {
        if (!class_exists('tutorModel')) {
            $this->markTestSkipped('tutorModel no está cargado.');
        }

        $model = new tutorModel();
        $db = Database::$lastInstance;
        $this->assertInstanceOf(Database::class, $db);

        $result = $model->cancelRequestParticular(7);

        $this->assertNotEmpty($db->preparedStatements);
        $stmt = $db->preparedStatements[0];
        $this->assertSame('UPDATE particulares SET status =:status WHERE id_particulares= 7', $stmt->sql);
        $this->assertTrue($stmt->executed);
        $this->assertSame([':status' => 1], $stmt->lastParams);

        $this->assertSame(0, $result);
    }
}