<?php

class userModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isEmailAvaliable($email)
    {
        $id = $this->_db->query("SELECT email FROM users WHERE email = '$email'");
        if ($id->fetch()) {
            return true;
        }
        return false;
    }

    public function registerUser($name,    $apellido,    $cedula, $telefono,    $email,    $carrera,   $role, $pass, $object)
    {
        $register = $this->_db->prepare(
            "INSERT INTO users values" .
                "(null,:name,:apellido,	:cedula,:telefono,:email,:carrera, :role,now(),:pass, null,null )"

        )
            ->execute(array(
                ':name' => $name,
                ':apellido' => $apellido,
                ':cedula' => $cedula,
                ':telefono' => $telefono,
                ':email' => $email,
                ':carrera' => $carrera,
                ':role' => $role,
                ':pass' => Hash::getHash('sha1', $pass, HASH_KEY)
            ));

        if ($role == 2) {
            $this->registerTutor($name,    $apellido, $cedula,   $email, $telefono, $object);
        }
        if ($role == 1) {
            $this->registerStudent($name,    $apellido,    $cedula,   $email,    $carrera, $object);
        }
        return $register;
    }
    public function registerStudent($name,    $apellido,    $cedula,   $email,    $carrera, $object)
    {
        $register2 = $this->_db->prepare(
            $object
        )
            ->execute(array(
                ':name' => $name,
                ':apellido' => $apellido,
                ':cedula' => $cedula,
                ':carrera' => $carrera,
                ':email' => $email,
            ));
    }
    public function registerTutor($name,    $apellido, $cedula,   $email, $telefono, $object)
    {
        $register2 = $this->_db->prepare(
            $object
        )
            ->execute(array(
                ':name' => $name,
                ':apellido' => $apellido,
                ':cedula' => $cedula,
                ':telefono' => $telefono,
                ':email' => $email,
            ));
    }
    public function getUserCI($ci)
    {
        $user = $this->_db->query(
            "SELECT count(id) as 'count' FROM users WHERE cedula = '$ci'"
        );
        $user->fetch();

        return   $user;
    }
    public function getUser($id, $code)
    {
        $user = $this->_db->query(
            "SELECT * FROM users WHERE id = $id AND code = '$code'"
        );
        return $user->fetch();
    }
    public function activateUser($id, $code)
    {
        $this->_db->query(
            "UPDATE users SET status = 1 WHERE id = $id and code = '$code'"
        );
    }
     
    public function getCedula($ci)
    {
        $cedula = $this->_db->query(
            "SELECT count(id) as count FROM users WHERE cedula = '$ci'"
        );
        $count = $cedula->fetch();
        return $count['count'];
    }
    public function getEscuela()
    {
        $escuela = $this->_db->query(
            "SELECT * FROM escuela WHERE 1"
        );
        return $escuela->fetchall();
    }
    public function authenticateUser($user, $pass)
    {
        $data = $this->_db->query(
            "SELECT * FROM users " .
                "WHERE email = '$user'" .
                "AND pass = '" . Hash::getHash('sha1', $pass, HASH_KEY) . "'"
        );
        return $data->fetch();
    }
}
