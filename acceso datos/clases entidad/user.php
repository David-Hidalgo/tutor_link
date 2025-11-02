<?php
abstract class user{
    protected $id ;
    protected $name;
    protected $lastName;
    protected $ci;
    protected $role;
    protected $email; 
    protected $telefono;
    protected $carrera;
    public function __construct() 
    {
       $this->id = 'null';
       $this->name = 'null';
       $this->lastName = 'null';
       $this->ci = 'null';
       $this->role = 'null';
       $this->email = 'null'; 
       $this->telefono = 'null';
       $this->carrera = 'null';
        
    }
    public function tableUser(){
        $table=   
            "INSERT INTO users values" .
                "(null,:name,:apellido,	:cedula,:telefono,:email,:carrera, :role,now(),:pass, null)" ; 

       return  $table;
    }
    public function tableTutor(){
        $table=   
            "INSERT INTO tutor values" .
                "(null,:name,:apellido,	:cedula,:email,:telefono,null)" ; 

       return  $table;
    }
    public function tableStudent(){
        $table=   
            "INSERT INTO student values" .
                "(null,:name,:apellido,	:cedula,:email, :carrera)" ; 

       return  $table;
    }

}

