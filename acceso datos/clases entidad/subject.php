<?php 
class subject{ 
    public $id;
    public $materia_des; 

    public function __construct() 
    { 
      $this->id  = 'null';   
      $this->materia_des = 'null'; 
        
    }
    public function tableRead(){
        $iduser = $_SESSION['id'];
        $string = "SELECT subject.materia_des,sessions.id_materia,sessions.id_tutor  FROM `sessions` INNER JOIN subject on 
        subject.id = sessions.id_materia WHERE sessions.id_tutor = $iduser group by sessions.id_materia ";

       return  $string;
    }

}