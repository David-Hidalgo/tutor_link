<?php

abstract class Controller
    {
    protected $_view;
    public function __construct() 
    {
        $this->_view= new View(new Request);    
    }
    abstract public function index();
    
    protected function loadModel($model)
           { 
            $model = $model . 'Model';
            $pathModel = ROOT . 'acceso datos'.DS.'models' . DS . $model . '.php';
            
            if (is_readable($pathModel)){
                require_once $pathModel;
                $model = new $model;
                return $model;
            }  else {
                throw new Exception('Model Error');
            }
        
        }
        
        protected function getLibrary($library)
        {
            $pathLibrary= ROOT . 'libs' . DS . $library . '.php';
            if(is_readable($pathLibrary)){
                require_once $pathLibrary;                
            }else
                {
                throw new Exception ('Error on Library');
                }
            
        } 
        
        protected function getText($key)
        {
        
           if(isset($_POST[$key]) && !empty($_POST[$key])) 
           {
               $_POST[$key] = htmlspecialchars($_POST[$key], ENT_QUOTES);
               return $_POST[$key];
              
           }        
                  return '';  
            
        }
        protected function getInt($key)
        {
            if(isset($_POST[$key]) && !empty($_POST[$key])) 
           {
               $_POST[$key] = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
               return $_POST[$key];
              
           }        
                  return 0;              
        }
        
        protected function redirect($path = false)
             {
            if($path)
                {
                header('location: ' . BASE_URL . $path);
                } 
                else {
                header('location: ' . BASE_URL );
                }
                exit;
            
             }
        protected function validatePhoneN($phone) {
            
            $pattern = "/^\+?[\d\s\-\(\)]{7,15}$/";
            return preg_match($pattern, $phone);
        }
             protected function filerInt($int)
            {
                 $int = (int) $int;
                 
                 if(is_int($int)){
                     return $int;
                 }  else {
                     return 0;
                 }
            }
            
            protected function getPostParam($key)
            {
                if(isset($_POST[$key])){
                    return  $_POST[$key];    
                }
            }
         
            protected function  getSql($key){
                if(isset($_POST[$key]) && !empty($_POST[$key])){
                    $_POST[$key]= strip_tags($_POST[$key]);
                     
                    return trim($_POST[$key]);
                }
                
            }
            protected function getAlphaNum($key){
                if(isset($_POST[$key]) && !empty($_POST[$key])){
                    $_POST[$key] = (string) preg_replace('/[^A-Z0-9_]/i', '', $_POST[$key]);
                    return trim($_POST[$key]);
                }
            }
            
            public function validateEmail($email) {
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    return false;
                }
                return true;
            }
                     
    }
 

