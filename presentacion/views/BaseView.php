<?php

class View
{
    private $_controller;
    private $_js;


    public function __construct(Request $require) {
        
        $this->_controller = $require->getController(); 
        $this->_js = array();
    }
    public function show($view, $item=false) 
        {
        $menu= array(
                array(
                    'id' => 'home',
                    'title' => 'Home',
                    'link' => BASE_URL
                    ),
                array(    
                    'id' => 'company',
                    'title' => 'Company',
                    'link' => BASE_URL . 'company'                    
                ),
            /*array(    
                    'id' => 'post',
                    'title' => 'Prueba Mierda DB',
                    'link' => BASE_URL . 'post'                    
                ),*/
            array(    
                    'id' => 'products',
                    'title' => 'Products',
                    'link' => BASE_URL . 'products'                    
                ),
            array(    
                    'id' => 'contact',
                    'title' => 'Contact',
                    'link' => BASE_URL . 'contact'                    
                ),
            array(    
                    'id' => 'catalog',
                    'title' => 'Catalog',
                    'link' => BASE_URL . 'catalog'                    
                )
            
        );
        
        if(Session::get('approved')){
            $menu[] = array(    
                    'id' => 'login',
                    'title' => 'Close Session',
                    'link' => BASE_URL . 'login/close'                    
                );
        }
        else{$menu[] = array(    
                    'id' => 'login',
                    'title' => 'Start Session',
                    'link' => BASE_URL . 'login'                    
                );
            $menu[] = array(    
                    'id' => 'register',
                    'title' => 'Register',
                    'link' => BASE_URL . 'register'                    
                );    
            
        }
        
        $js= array();
        if (count($this->_js)) 
            {
            $js= $this->_js;
            
            }      
        
        $_layoutParams= array (
            'path_css' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/css/',
            'path_img' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/img/',
            'path_js' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/js/',
            'menu' => $menu,
            'js'=> $js    
        );
       // $pathView = ROOT . 'views' . DS . $this->_controller . DS . $view . '.phtml';
       $pathView = ROOT .'presentacion'.DS. 'views' . DS . $view . '.phtml';
        
        if (is_readable($pathView))
            {
            //include_once ROOT . 'views' . DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'header.php';
            include_once ROOT .'presentacion'.DS. 'views' . DS . 'header.php';
            include_once $pathView;
           // include_once ROOT . 'views' . DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'footer.php';
            include_once ROOT .'presentacion'.DS. 'views' . DS . 'footer.php';
            }
            else
                {
                throw new Exception('Error in View');                
            }
        }
        
        public function setJs(array $js)
            {
            if (is_array($js) && count($js)){
                for($i=0 ; $i < count($js); $i++){
                    $this->_js[] = BASE_URL . 'views/' . $this->_controller . '/js/' . $js[$i] . '.js';                                         
                }
            }else{
                throw new Exception('Error loading Js');
            
            }
            
            
            
            }
            
}
