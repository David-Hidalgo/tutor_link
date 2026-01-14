<?php
use PHPUnit\Framework\TestCase;

class BusinessLogicTest extends TestCase {

    // 27. Listar Tutores disponibles
    public function testListTutors() {
        $tutorModel = new TutorModel();
        $tutors = $tutorModel->getAll();
        $this->assertIsArray($tutors);
        // Verificar estructura de respuesta
        if (count($tutors) > 0) {
            $this->assertArrayHasKey('specialty', $tutors[0]);
        }
    }

    // 28. Estudiante se inscribe en materia
    public function testStudentEnrollment() {
        $enrollment = new EnrollmentModel();
        $result = $enrollment->register(1, 101); // UserID, CourseID
        $this->assertTrue($result);
    }

    // 29. Evitar doble inscripción
    public function testPreventDoubleEnrollment() {
        $enrollment = new EnrollmentModel();
        $this->expectException(Exception::class);
        $enrollment->register(1, 101); // Segunda vez
    }

    // 30. Filtrado de tutores por materia
    public function testFilterTutorsBySubject() {
        $tutorModel = new TutorModel();
        $results = $tutorModel->searchBySubject('Matemáticas');
        foreach ($results as $tutor) {
            $this->assertEquals('Matemáticas', $tutor['subject']);
        }
    }
}