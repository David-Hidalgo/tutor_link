<?php
 
class indexController extends Controller
{
    protected $_view;
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function index() 
    {          $post = $this->loadModel('index');
        
                $this->_view->title='TutorLink';
                $this->_view->setJs(array('jquery.nivo.slider.pack','nivo'));
                $this->_view->show('viewindex','home') ; 
        
    }
    
}


 

