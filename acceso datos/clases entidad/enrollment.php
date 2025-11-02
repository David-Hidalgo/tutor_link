<?php 
 class enrollment{ 
     public $sessionId;
     public $studentId;  
     public function __construct() 
     {
         $this->sessionId = 'null';
         $this->studentId = 'null';
         
     }
     public function tableCreate(){
         $table=   "INSERT INTO enrollment values ('null',:id_estudiante,:id_horario)";
        return  $table;
     }
 

}