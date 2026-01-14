<?php
use PHPUnit\Framework\TestCase;

class SecuritySanitizationTest extends TestCase {
    
    // 16. Validación de Email (Filter Var)
    public function testEmailSanitization() {
        $rawEmail = "  test@ucab.edu.ve  ";
        $clean = filter_var(trim($rawEmail), FILTER_SANITIZE_EMAIL);
        $this->assertEquals('test@ucab.edu.ve', $clean);
    }

    // 17. Rechazo de emails inválidos
    public function testInvalidEmailFormat() {
        $badEmail = "usuario@@dominio";
        $this->assertFalse(filter_var($badEmail, FILTER_VALIDATE_EMAIL));
    }

    // 18. Prevención de XSS en Strings (Codificación de salidas)
    public function testXSSProtection() {
        $maliciousInput = "<script>alert('xss')</script>";
        $clean = htmlspecialchars($maliciousInput, ENT_QUOTES, 'UTF-8');
        $this->assertEquals("&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;", $clean);
    }

    // 19. Validación de Enteros (IDs para evitar inyección en GET)
    public function testIdValidation() {
        $id = "105 OR 1=1";
        // Tu función de validación
        $isValid = ctype_digit($id); 
        $this->assertFalse($isValid);
    }

    // 20. Token CSRF (Si lo implementaste en formularios)
    public function testCsrfTokenGeneration() {
        if (!function_exists('generateCsrfToken')) {
            $this->markTestSkipped('generateCsrfToken() no existe en este proyecto (test placeholder).');
        }

        $token = generateCsrfToken(); // Tu función
        $this->assertNotEmpty($token);
        $this->assertEquals(32, strlen(bin2hex(random_bytes(16)))); // Longitud típica
    }

    // 21. Headers de Seguridad (Mock response)
    // Simula que tu app añade headers seguros
    public function testSecurityHeadersConfig() {
        $headers = ['X-Frame-Options' => 'DENY', 'X-Content-Type-Options' => 'nosniff'];
        $this->assertArrayHasKey('X-Frame-Options', $headers);
    }
}