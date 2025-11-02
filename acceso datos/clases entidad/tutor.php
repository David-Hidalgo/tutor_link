<?php
 require_once ROOT. 'acceso datos'.DS.'clases entidad'.DS.'user.php';
class tutor extends user{ 
    public $telefono;  
    public function __construct() 
    {
        $this->telefono = 'null';
        
    }
    public function tableCreate(){
        $table=  $this->tableTutor() ; 
       return  $table;
    }

}
