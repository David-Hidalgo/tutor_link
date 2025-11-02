<?php
 require_once ROOT. 'acceso datos'.DS.'clases entidad'.DS.'user.php';
 class student extends user{ 
     public $telefono;  
     public function __construct() 
     {
         $this->telefono = 'null';
         
     }
     public function tableCreate(){
         $table=  $this->tableStudent() ; 
        return  $table;
     }
 

}
