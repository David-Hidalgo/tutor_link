<?php
ini_set('display_errors', 1);
define('DS',DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('APP_PATH', ROOT . 'application' . DS);
define('CONTROLLER_PATH', ROOT.'logica negocio' .DS. 'controllers' . DS);
define('MODEL_PATH', ROOT .'acceso datos'.DS. 'models' . DS);
define('VIEW_PATH', ROOT .'presentacion'.DS. 'views' . DS);

try{
require_once APP_PATH . 'Config.php';
require_once APP_PATH . 'Request.php';
require_once APP_PATH . 'Bootstrap.php';
require_once CONTROLLER_PATH . 'BaseController.php';
require_once MODEL_PATH . 'BaseModel.php';
require_once VIEW_PATH . 'BaseView.php';
require_once APP_PATH . 'Database.php';
require_once APP_PATH . 'Session.php';
require_once APP_PATH . 'Hash.php';

  
Session::init();

    Bootstrap::run(new Request);
}
catch(Exception $e){
    echo $e->getMessage();
}
        

 
    
