<?php

class userController extends Controller{
    
    private $_register;
    protected $_view;
    public function __construct() {
        parent::__construct(); 
        $this->_register = $this->loadModel('user');
        require_once ROOT.'acceso datos'.DS. 'clases entidad'.DS.'tutor.php';
        require_once ROOT. 'acceso datos'.DS.'clases entidad'.DS.'student.php';
    }
    public function index(){
        if(!Session::get('approved')){ 
            $this->redirect();   
           }  
    }
    public function handleRegistration() {
        if(Session::get('approved')){ 
            $this->redirect();   
           }
        $escuela= $this->_register->getEscuela();
        
        $this->_view->title = 'Register';
       $this->_view->_regok=0;
        
        extract($_POST); 
            
        if(isset($sendrg) ){
            $this->_view->data = $_POST;
           $this->validateRequiredFields($_POST);  
           $this->validateUcabEmail($_POST);
           $this->validateEmailFormat($_POST);
           $this->validatePhoneNumber($_POST);
           $this->validatePasswordsMatch($_POST); 

            $namex=$name;
            $apellido=$apell;
            $cedula=$cedu;
            $telefono=$tlf;
            $emailx=$email;
            $carrera=$escu; 
            $role=$customRadio; 
            $passx=$pass;
            
            $insert = new tutor;
            if($role==2){ 
            $insert2= $insert->tableTutor();}
            elseif($role==1){ 
            $insert2= $insert->tableStudent();}
            
            
             
          $registro= $this->_register->registerUser($namex,	$apellido,	$cedula,	$telefono,	$emailx,$carrera, 	$role, $passx,$insert2 );
          if($registro==1){
            $this->_view->_regok=1;
            $this->_view->show('viewregistro', 'register');
            exit;
          }else{  echo 'tpy165';exit;

          }
        } 
        $this->_view->data1 = $escuela;
        $this->_view->show('viewregistro', 'register');
                
    } 
    public function validateRequiredFields(){
        extract($_POST);
        if(!isset($customRadio) ){
            $this->_view->_message = array('msj'=>'Debe seleccionar una opcion','error'=>0);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;}
        if(!$this->getSql('name')){
            $this->_view->_message = array('msj'=>'Debe escribir el nombre','error'=>1);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        }  
        if(!$this->getSql('apell')){
            $this->_view->_message = array('msj'=>'Debe escribir el apellido','error'=>2);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        }  
        if(!$this->getSql('cedu')){
            $this->_view->_message = array('msj'=>'Debe escribir la Cédula','error'=>3);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        }
        $chars = array("-", ".", ",", " ");
        $ci = str_replace($chars, "", $this->getSql('cedu'));
        $v1 = ucwords(substr($ci, 0, 1));
        $v2 = substr($ci, 1);
        if ($v1 == 'V' or $v1 == 'E') {
            $v3 = 1;
        } else {
            $v3 = 0;
        }
      
        if (strlen($this->_register->getCedula($ci) > 0)) {
            $this->_view->_message = array('msj'=>'Error en la Cédula. La Cédula ya existe','error'=>3);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;                
        }

        if (strlen($ci) > 10) {
            $this->_view->_message = array('msj'=> "Error en la Cédula. Demasiados caracteres",'error'=>3);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;  
        }
        if ($v3 == 0) {
            $this->_view->_message = array('msj'=> "Error en la Cédula.  El inicio de la CI debe ser una letra permitida",'error'=>3);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;  
        }
        if (is_numeric($v2) != 1) {
            $this->_view->_message = array('msj'=> "Error en la Cédula. Introdujo caracteres no pemitidos",'error'=>3);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        }
        if($customRadio == 1){ 
            if( $escu<1 || $escu>3){
                $this->_view->_message = array('msj'=>'Debe seleccionar la carrera','error'=>8);
                $this->_view->data1 = $this->_register->getEscuela();
                $this->_view->show('viewregistro', 'register');
                exit;   
            }
    }
}
    public function validateUcabEmail(){
        extract($_POST);
        if($customRadio == 1){  
        $validateUcab=  substr($this->getSql('email'),-16);

                if(!$this->getSql('email') || $validateUcab <> '@est.ucab.edu.ve'){
                    $this->_view->_message = array('msj'=>'Debe escribir un email de la UCAB valido','error'=>4);
                    $this->_view->data1 = $this->_register->getEscuela();
                    $this->_view->show('viewregistro', 'register');
                    exit;
                }     
                if($this->_register->isEmailAvaliable($this->getSql('email'))){
                    $this->_view->_message = array('msj'=>'Este email ya existe en la base de datos','error'=>4);
                    $this->_view->data1 = $this->_register->getEscuela();
                    $this->_view->show('viewregistro', 'register');
                    exit;
                } 
        }
        return;      
            }
    
    public function validateEmailFormat(){
        extract($_POST);
        if($customRadio == 2){
            if(!$this->getSql('email')){
                $this->_view->_message = array('msj'=>'Debe xxxxescribir un email valido','error'=>4);
                $this->_view->data1 = $this->_register->getEscuela();
                $this->_view->show('viewregistro', 'register');
                exit;
            }
            if($this->_register->isEmailAvaliable($this->getSql('email'))){
                $this->_view->_message = array('msj'=>'Este email ya existe en la base de datos','error'=>4);
                $this->_view->data1 = $this->_register->getEscuela();
                $this->_view->show('viewregistro', 'register');
                exit;
            }  
        } 
        return;
    }
   public function  validatePhoneNumber(){
     extract($_POST);
    if($customRadio == 2){
        if($this->validatePhoneN($this->getSql('tlf'))==0){
            $this->_view->_message = array('msj'=>'Debe escribir un telefono valido','error'=>6);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        } 
    }
    return;
   }
 
    public function validatePasswordsMatch(){
        extract($_POST);
        if (preg_match('/\s/', $pass) > 0) {
            $this->_view->_message = array('msj'=>'El Password no puede tener espacios','error'=>7);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        }
        if (strlen($pass) < 6) {
            $this->_view->_message = array('msj'=>'El password debe tener al menos 6 caracteres','error'=>7);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
        }
        
        if($this->getSql('pass2') <> $this->getSql('pass')){
            $this->_view->_message = array('msj'=>'Los passwords no coinciden','error'=>7);
            $this->_view->data1 = $this->_register->getEscuela();
            $this->_view->show('viewregistro', 'register');
            exit;
          
        } 
    } 

    public function handleLogin(){ 
        $this->_view->title='Inicio Sesion'; 
        extract($_POST); 
        if(isset($sendx) ){ 
            $this->_view->data = $_POST;
                   $row = $this->_register->authenticateUser(
                    $this->getSql('email'),
                    $this->getSql('pass')
                    );
            if(!$row){
                $this->_view->_message = array('msj'=>'El usuario o el password no existen','error'=>'');
                $this->_view->show('viewlogin','user');
                exit;
            }
               
            Session::set('approved', true);
            Session::set('level',$row['role']);
            Session::set('name',$row['name']);
            Session::set('id',$row['id']);
            Session::set('time',time());
            
            $this->redirect();
            }   
        $this->_view->show('viewlogin','user');
    }    
        

   
    public function close(){
        Session::destroy();
        $this->redirect();
    }
    
}
