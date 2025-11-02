<?php 
class particulares{  
    public $id_tutor;
    public $id_materia;
    
    public function __construct() 
    { 
     
      $this->id_tutor = 'null';
      $this->id_materia = 'null';
              
    }
    public function tableCreate(){
      $object = "INSERT INTO particulares values" .
      "(null,:id_estudiante,:id_tutor,:id_materia,:memo,:status)"; 
       return  $object;
    }

}