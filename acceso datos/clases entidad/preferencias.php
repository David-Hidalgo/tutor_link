<?php
class preferencias{
    public $id_student;
    public $id_materia;
    
    public function __construct() 
    {
       $this->id_student = 'null';
       $this->id_materia = 'null'; 
        
    }
    public function tableCreate(){
        
        $table=   
            "INSERT INTO preferencias values" .
                "(null,:id_student,:id_materia)" ; 

       return  $table;
    }
     

}

