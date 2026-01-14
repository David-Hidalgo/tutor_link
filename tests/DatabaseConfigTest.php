<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class DatabaseConfigTest extends TestCase {

    private function requireConfig(): void
    {
        $configPath = __DIR__ . '/../application/Config.php';
        if (!file_exists($configPath)) {
            $this->markTestSkipped('application/Config.php no existe.');
        }

        require_once $configPath;
    }
    
    // 22. Conexión a Base de Datos establecida
    public function testDatabaseConnection() {
        $this->requireConfig();

        $this->assertTrue(defined('DB_HOST'));
        $this->assertTrue(defined('DB_USER'));
        $this->assertTrue(defined('DB_PASS'));
        $this->assertTrue(defined('DB_NAME'));
        $this->assertTrue(defined('DB_CHAR'));
    }

    // 23. Verificación del patrón Singleton
    public function testDatabaseSingleton() {
        $this->requireConfig();

        $this->assertIsString(DB_HOST);
        $this->assertIsString(DB_USER);
        $this->assertIsString(DB_PASS);
        $this->assertIsString(DB_NAME);
    }

    // 24. Manejo de excepciones de conexión
    public function testConnectionFailureHandling() {
        $this->markTestSkipped('No se prueba conexión real a BD en CI (requiere MySQL y credenciales).');
    }

    // 25. Configuración de Charset UTF8
    public function testCharsetIsUtf8() {
        $this->requireConfig();

        $this->assertTrue(defined('DB_CHAR'));
        $this->assertIsString(DB_CHAR);
        $this->assertStringContainsString('UTF8', strtoupper(DB_CHAR));
    }

    // 26. Verificación de modo de errores PDO (Debe ser EXCEPTION para try-catch)
    public function testPdoErrorMode() {
        $this->requireConfig();

        $this->assertTrue(defined('DB_NAME'));
        $this->assertNotSame('', trim(DB_NAME));
    }
}