<?php

class Request
{
    private $_controller;
    private $_method;
    private $_arguments;
    
    public function __construct() {
        if(isset($_GET['url'])){
            $url= filter_input(INPUT_GET,'url', FILTER_SANITIZE_URL);
            $url=  explode('/', $url);
            $url= array_filter($url);
        
            $this->_controller= strtolower(array_shift($url));
            $this->_method= strtolower(array_shift($url));
            $this->_arguments=$url;        
        }    
        
        if(!$this->_controller){
            
            $this->_controller=DEFAULT_CONTROLLER;
           }
        if(!$this->_method){
            $this->_method='index';            
        }   
        if(!isset($this->_arguments)){
            $this->_arguments=array() ;            
        }
    }
    public function  getController(){
        return $this->_controller;
    }
    public function getMethod() {
     return $this->_method;   
    }
    public function getArguments() {
        return $this->_arguments;   
    }        
    
}
        
  
/*
class Request
{
    private $_controlador;
    private $_metodo;
    private $_argumentos;
    
    public function __construct() {
        if(isset($_GET['url'])){
            $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $url = array_filter($url);
            
            $this->_controlador = strtolower(array_shift($url));
            $this->_metodo = strtolower(array_shift($url));
            $this->_argumentos = $url;
        }       
        
        if(!$this->_controlador){
            $this->_controlador = DEFAULT_CONTROLLER;
        }
        
        if(!$this->_metodo){
            $this->_metodo = 'index';
        }
        
        if(!isset($this->_argumentos)){
            $this->_argumentos = array();
        }
    }
    
    public function getControlador()
    {
        return $this->_controlador;
    }
    
    public function getMetodo()
    {
        return $this->_metodo;
    }
    
    public function getArgs()
    {
        return $this->_argumentos;
    }
}

   */ 
    

