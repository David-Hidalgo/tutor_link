<?php
use PHPUnit\Framework\TestCase;

class DatabaseConfigTest extends TestCase {
    
    // 22. Conexión a Base de Datos establecida
    public function testDatabaseConnection() {
        $db = Database::getInstance();
        $this->assertInstanceOf(PDO::class, $db->getConnection());
    }

    // 23. Verificación del patrón Singleton
    public function testDatabaseSingleton() {
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();
        $this->assertSame($db1, $db2);
    }

    // 24. Manejo de excepciones de conexión
    public function testConnectionFailureHandling() {
        // Esto requeriría cambiar config temporalmente o mockear
        $this->expectException(PDOException::class);
        new Database('wrong_host', 'user', 'pass'); // Simulado
    }

    // 25. Configuración de Charset UTF8
    public function testCharsetIsUtf8() {
        $pdo = Database::getInstance()->getConnection();
        $result = $pdo->query("SELECT @@character_set_database")->fetchColumn();
        $this->assertStringContainsString('utf8', $result);
    }

    // 26. Verificación de modo de errores PDO (Debe ser EXCEPTION para try-catch)
    public function testPdoErrorMode() {
        $pdo = Database::getInstance()->getConnection();
        $mode = $pdo->getAttribute(PDO::ATTR_ERRMODE);
        $this->assertEquals(PDO::ERRMODE_EXCEPTION, $mode);
    }
}