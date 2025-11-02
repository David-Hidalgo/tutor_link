<?php 
class sessions{ 
    public $id_horario;  
    public $id_tutor;
    public $id_materia;
    public $dia;
    public $inicio;
    public $final;

    public function __construct() 
    { 
      $this->id_horario = 'null';  
      $this->id_tutor = 'null';
      $this->id_materia = 'null';
      $this->dia = 'null';
      $this->inicio = 'null';
      $this->final = 'null';
        
    }
    public function tableCreate(){
        $table=   "INSERT INTO sessions values" .
                "(null,:id_tutor,:id_materia,:dia,:inicio,:final)"; 
       return  $table;
    }

}