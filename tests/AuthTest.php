<?php
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase {
    private $authController;

    protected function setUp(): void {
        $this->authController = new AuthController();
    }

    // 9. Login Exitoso
    public function testLoginSuccess() {
        $response = $this->authController->handleLogin('test@ucab.edu.ve', '123456');
        $this->assertEquals('success', $response['status']);
    }

    // 10. Login Fallido - Password Incorrecto
    public function testLoginWrongPassword() {
        $response = $this->authController->handleLogin('test@ucab.edu.ve', 'badpass');
        $this->assertEquals('error', $response['status']);
    }

    // 11. Login Fallido - Usuario no existe
    public function testLoginUserNotFound() {
        $response = $this->authController->handleLogin('ghost@ucab.edu.ve', '123456');
        $this->assertEquals('error', $response['status']);
    }

    // 12. Validación de entradas vacías
    public function testLoginEmptyFields() {
        $response = $this->authController->handleLogin('', '');
        $this->assertArrayHasKey('error_msg', $response);
    }

    // 13. Simulación de ataque SQL Injection (Debe fallar o ser sanitizado)
    // Esto verifica la corrección de la "Prioridad 0" de tu informe.
    public function testLoginSqlInjectionAttempt() {
        $payload = "' OR '1'='1";
        $response = $this->authController->handleLogin($payload, 'anything');
        // No debe retornar éxito ni error de sintaxis SQL
        $this->assertNotEquals('success', $response['status']);
        $this->assertStringNotContainsString('SQLSTATE', json_encode($response));
    }

    // 14. Redirección post-login
    public function testRedirectBasedOnRole() {
        // Mockear usuario estudiante
        $url = $this->authController->getRedirectUrl('student');
        $this->assertEquals('/dashboard/student', $url);
    }

    // 15. Bloqueo de cuenta (Opcional, si tienes política de intentos)
    public function testAccountLockoutAfterFailures() {
        for ($i = 0; $i < 5; $i++) {
            $this->authController->handleLogin('test@ucab.edu.ve', 'wrong');
        }
        $response = $this->authController->handleLogin('test@ucab.edu.ve', '123456');
        $this->assertEquals('locked', $response['status']); // Ajustar según tu lógica
    }
}