<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    private $userModel;

    protected function setUp(): void {
        // Asumiendo que tienes una clase UserModel
        $this->userModel = new UserModel(); 
    }

    // 1. Prueba de creación de usuario exitosa
    public function testCreateUserSuccess() {
        $result = $this->userModel->create('Estudiante', 'test@ucab.edu.ve', '123456');
        $this->assertTrue($result, "El usuario debería crearse correctamente.");
    }

    // 2. Validación de contraseña hasheada (Control AC-02 del informe)
    public function testPasswordIsHashed() {
        $user = $this->userModel->findByEmail('test@ucab.edu.ve');
        $this->assertNotEquals('123456', $user['password'], "La contraseña no debe guardarse en texto plano.");
        $this->assertTrue(password_verify('123456', $user['password']), "El hash debe coincidir con Argon2/Bcrypt.");
    }

    // 3. Prueba de duplicidad de email
    public function testCreateUserDuplicateEmail() {
        $this->expectException(Exception::class); // O el manejo de error que tengas
        $this->userModel->create('Otro', 'test@ucab.edu.ve', 'pass');
    }

    // 4. Búsqueda de usuario existente
    public function testFindUserByEmailFound() {
        $user = $this->userModel->findByEmail('test@ucab.edu.ve');
        $this->assertIsArray($user);
        $this->assertEquals('test@ucab.edu.ve', $user['email']);
    }

    // 5. Búsqueda de usuario inexistente
    public function testFindUserByEmailNotFound() {
        $user = $this->userModel->findByEmail('noexiste@ucab.edu.ve');
        $this->assertFalse($user);
    }

    // 6. Actualización de perfil
    public function testUpdateUserProfile() {
        $update = $this->userModel->update('test@ucab.edu.ve', ['name' => 'Nuevo Nombre']);
        $this->assertTrue($update);
    }

    // 7. Validación de roles (RBAC - Control A.5.15)
    public function testUserRoleAssignment() {
        $user = $this->userModel->findByEmail('test@ucab.edu.ve');
        // Asumiendo que guardas el rol en la BD
        $this->assertContains($user['role'], ['admin', 'student', 'tutor']);
    }

    // 8. Eliminación de usuario (Limpieza)
    public function testDeleteUser() {
        $delete = $this->userModel->delete('test@ucab.edu.ve');
        $this->assertTrue($delete);
    }
}<?php
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase {
    private $userModel;

    protected function setUp(): void {
        // Asumiendo que tienes una clase UserModel
        $this->userModel = new UserModel(); 
    }

    // 1. Prueba de creación de usuario exitosa
    public function testCreateUserSuccess() {
        $result = $this->userModel->create('Estudiante', 'test@ucab.edu.ve', '123456');
        $this->assertTrue($result, "El usuario debería crearse correctamente.");
    }

    // 2. Validación de contraseña hasheada (Control AC-02 del informe)
    public function testPasswordIsHashed() {
        $user = $this->userModel->findByEmail('test@ucab.edu.ve');
        $this->assertNotEquals('123456', $user['password'], "La contraseña no debe guardarse en texto plano.");
        $this->assertTrue(password_verify('123456', $user['password']), "El hash debe coincidir con Argon2/Bcrypt.");
    }

    // 3. Prueba de duplicidad de email
    public function testCreateUserDuplicateEmail() {
        $this->expectException(Exception::class); // O el manejo de error que tengas
        $this->userModel->create('Otro', 'test@ucab.edu.ve', 'pass');
    }

    // 4. Búsqueda de usuario existente
    public function testFindUserByEmailFound() {
        $user = $this->userModel->findByEmail('test@ucab.edu.ve');
        $this->assertIsArray($user);
        $this->assertEquals('test@ucab.edu.ve', $user['email']);
    }

    // 5. Búsqueda de usuario inexistente
    public function testFindUserByEmailNotFound() {
        $user = $this->userModel->findByEmail('noexiste@ucab.edu.ve');
        $this->assertFalse($user);
    }

    // 6. Actualización de perfil
    public function testUpdateUserProfile() {
        $update = $this->userModel->update('test@ucab.edu.ve', ['name' => 'Nuevo Nombre']);
        $this->assertTrue($update);
    }

    // 7. Validación de roles (RBAC - Control A.5.15)
    public function testUserRoleAssignment() {
        $user = $this->userModel->findByEmail('test@ucab.edu.ve');
        // Asumiendo que guardas el rol en la BD
        $this->assertContains($user['role'], ['admin', 'student', 'tutor']);
    }

    // 8. Eliminación de usuario (Limpieza)
    public function testDeleteUser() {
        $delete = $this->userModel->delete('test@ucab.edu.ve');
        $this->assertTrue($delete);
    }
}