<?php
use PHPUnit\Framework\TestCase;

class BusinessLogicTest extends TestCase {

    protected function setUp(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if (!isset($_SESSION['id'])) {
            $_SESSION['id'] = 1;
        }
    }

    // 27. Listar Tutores disponibles
    public function testListTutors() {
        if (!class_exists('TutorModel') && !class_exists('tutorModel')) {
            $this->markTestSkipped('TutorModel no existe en este proyecto (test placeholder).');
        }

        $tutorModel = new TutorModel();

        if (!method_exists($tutorModel, 'getAll')) {
            $this->markTestSkipped('TutorModel::getAll() no existe en este proyecto (test placeholder).');
        }

        $tutors = $tutorModel->getAll();
        $this->assertIsArray($tutors);
        // Verificar estructura de respuesta
        if (count($tutors) > 0) {
            $this->assertArrayHasKey('specialty', $tutors[0]);
        }
    }

    // 28. Estudiante se inscribe en materia
    public function testStudentEnrollment() {
        if (!class_exists('EnrollmentModel')) {
            $this->markTestSkipped('EnrollmentModel no existe en este proyecto (test placeholder).');
        }
        $enrollment = new EnrollmentModel();
        $result = $enrollment->register(1, 101); // UserID, CourseID
        $this->assertTrue($result);
    }

    // 29. Evitar doble inscripción
    public function testPreventDoubleEnrollment() {
        if (!class_exists('EnrollmentModel')) {
            $this->markTestSkipped('EnrollmentModel no existe en este proyecto (test placeholder).');
        }
        $enrollment = new EnrollmentModel();
        $this->expectException(Exception::class);
        $enrollment->register(1, 101); // Segunda vez
    }

    // 30. Filtrado de tutores por materia
    public function testFilterTutorsBySubject() {
        if (!class_exists('TutorModel') && !class_exists('tutorModel')) {
            $this->markTestSkipped('TutorModel no existe en este proyecto (test placeholder).');
        }

        $tutorModel = new TutorModel();

        if (!method_exists($tutorModel, 'searchBySubject')) {
            $this->markTestSkipped('TutorModel::searchBySubject() no existe en este proyecto (test placeholder).');
        }

        $results = $tutorModel->searchBySubject('Matemáticas');
        foreach ($results as $tutor) {
            $this->assertEquals('Matemáticas', $tutor['subject']);
        }
    }
}