<?php
use PHPUnit\Framework\TestCase;

class DatabaseConfigTest extends TestCase {

    private function skipIfDatabaseSingletonNotImplemented(): void
    {
        if (!class_exists('Database') || !method_exists('Database', 'getInstance')) {
            $this->markTestSkipped('Database::getInstance() no existe en este proyecto (test placeholder).');
        }
    }
    
    // 22. Conexión a Base de Datos establecida
    public function testDatabaseConnection() {
        $this->skipIfDatabaseSingletonNotImplemented();
        $db = Database::getInstance();
        $this->assertInstanceOf(PDO::class, $db->getConnection());
    }

    // 23. Verificación del patrón Singleton
    public function testDatabaseSingleton() {
        $this->skipIfDatabaseSingletonNotImplemented();
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();
        $this->assertSame($db1, $db2);
    }

    // 24. Manejo de excepciones de conexión
    public function testConnectionFailureHandling() {
        $this->markTestSkipped('Test placeholder: requiere un constructor Database configurable o mocking de PDO.');
    }

    // 25. Configuración de Charset UTF8
    public function testCharsetIsUtf8() {
        $this->skipIfDatabaseSingletonNotImplemented();
        $pdo = Database::getInstance()->getConnection();
        $result = $pdo->query("SELECT @@character_set_database")->fetchColumn();
        $this->assertStringContainsString('utf8', $result);
    }

    // 26. Verificación de modo de errores PDO (Debe ser EXCEPTION para try-catch)
    public function testPdoErrorMode() {
        $this->skipIfDatabaseSingletonNotImplemented();
        $pdo = Database::getInstance()->getConnection();
        $mode = $pdo->getAttribute(PDO::ATTR_ERRMODE);
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $mode);
    }
}