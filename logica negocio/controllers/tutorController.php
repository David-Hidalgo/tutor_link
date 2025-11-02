<?php
require_once ROOT .'acceso datos'.DS. 'clases entidad' . DS . 'sessions.php';
require_once ROOT .'acceso datos'.DS. 'clases entidad' . DS . 'subject.php';
class tutorController extends Controller
{
    private $_perfil;
    private $_register;
    protected $_view;
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['approved'])) {
            $this->redirect();
        }
        $this->_perfil =  $this->loadModel('tutor');
        $this->_register = $this->loadModel('user');
        include_once  ROOT . 'libs' . DS .  "class.upload.php";
    }

    public function index()
    {
        if ($_SESSION['level'] != 2) {
            $this->redirect();
        }
        $sbj = new subject;
        $object = $sbj->tableRead();
        $this->_view->title = 'Mi Perfil';
        $user = $this->_perfil->getUserName();
        $escuela = $this->_perfil->getEscuela();
        $subjecttutor = $this->_perfil->getsubjectTutor($object);
        $sessions = $this->_perfil->getsessions();
        $this->_view->requests=$this->_perfil->getRequests();
       // print_r($user) ;echo 'toy';exit;
        $this->_view->perfil = $user;
        $this->_view->escuela = $escuela;
        $this->_view->subjecttutor = $subjecttutor;
        $this->_view->sessions = $sessions;
        extract($_POST);
        if(isset($reqcan)){
           $id = $this->getInt('reqid');
           $cancel = $this->cancelRequest($id);
           $this->_view->requests=$this->_perfil->getRequests();
           $this->_view->_message =  $cancel;
           $this->_view->show('viewtutor', 'tutor');
        }
        if(isset($edithora)){ 
            $response = $this->handleSessionEdition($_POST);
            if ($response['t'] == 0) {
                $this->_view->modalmessage2 = $response;
            }
            if ($response['t'] == 1) {
                $this->_view->_message = $response;
            } 
            $user = $this->_perfil->getUserName();
            $escuela = $this->_perfil->getEscuela();
            $subjecttutor = $this->_perfil->getsubjectTutor($object);
            $sessions = $this->_perfil->getsessions();
            $modalEdit = 'edit'.$idhora;
            $this->_view->modnum=$modalEdit;
            $this->_view->perfil = $user;
            $this->_view->escuela = $escuela;
            $this->_view->subjecttutor = $subjecttutor;
            $this->_view->sessions = $sessions;
            $this->_view->show('viewtutor', 'tutor');
            exit;
        }
        if (isset($addhro)) {
            $response = $this->handleSessionCreation($_POST);
            if ($response['t'] == 0) {
                $this->_view->modalmessage = $response;
            }
            if ($response['t'] == 1) {
                $this->_view->_message = $response;
            }

            $user = $this->_perfil->getUserName();
            $escuela = $this->_perfil->getEscuela();
            $subjecttutor = $this->_perfil->getsubjectTutor($object);
            $sessions = $this->_perfil->getsessions();

            $this->_view->perfil = $user;
            $this->_view->escuela = $escuela;
            $this->_view->subjecttutor = $subjecttutor;
            $this->_view->sessions = $sessions;
            $this->_view->show('viewtutor', 'tutor');
            exit;
        }
        if (isset($abt)) { 
            $about = $this->getSql('about');
            $id = $this->getInt('idtut');
            $modification = $this->setAbout($about,$id);
            $this->_view->_message = $modification;
            $this->_view->perfil = $this->_perfil->getUserName();
            $this->_view->show('viewtutor', 'tutor');
        }
        if (isset($elm)) {
            $pic = $this->getSql('pic');
            $path_pic = ROOT . 'public' . DS . 'img' . DS . 'profiles' . DS;

            if (file_exists($path_pic . $pic)) {
                unlink($path_pic . $pic);
            }
            $this->deleteMyPerfil();
        }
        if (isset($med)) {
            $modification = $this->validateSessionData();
            $this->_view->_message = $modification;
            $this->_view->perfil = $this->_perfil->getUserName();
            $this->_view->show('viewtutor', 'tutor');
        }

        $this->_view->show('viewtutor', 'tutor');
    }

    public function getsubject()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)
            echo 'mosca toy';
            exit;
        }
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;
        $rawPostData = file_get_contents('php://input');
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $id = $requestData['id_carrera'];
        if (intval($id) > 0) {
            $option = " AND carrera_materia.idcarrera = $id ";
        } elseif ($id == "nones") {
            $option = " ";
        }

        $subject = $this->_perfil->getsubject($option);
        echo json_encode($subject);
    }
    public function validateSessionData()
    {
        $user = $this->_perfil->getUserName();
        extract($_POST);

        if (!$this->getSql('name')) {
            $message = array('msj' => 'Debe escribir el nombre', 'error' => 1);
            return $message;
        }

        if (!$this->getSql('apell')) {
            $message = array('msj' => 'Debe escribir el apellido', 'error' => 2);
            return $message;
        }

        if ($this->validatePhoneN($this->getSql('tlf')) == 0) {
            $message = array('msj' => 'Debe escribir un telefono valido', 'error' => 6);
            return $message;
        }

        if ($_FILES['pic']['size'] > 0) {
            $path_pic = ROOT . 'public' . DS . 'img' . DS . 'profiles' . DS;
            $prefix = 'prf_';
            $upload = new upload($_FILES['pic'], 'es_Es');
            $upload->allowed = array('image/*');
            $img = $upload->file_new_name_body = $prefix . uniqid();
            $upload->image_resize = true;
            $upload->image_x = 220;
            $upload->image_ratio_y = true;
            $upload->image_convert = 'jpg';
            $upload->jpeg_quality = 100;
            $upload->process($path_pic);
        } else {
            $img = $user['foto'];
        }
        $namex = $name;
        $apellido = $apell;
        $telefono = $tlf;


        $perfil = $this->_perfil->editPerfil($user['id'], $namex,    $apellido, $telefono,   $img);
        if ($perfil == 1) {
            $message = array('msj' => 'Su registro fue actualizado', 'error' => '43');
            return $message;
        }
    }
    public function setAbout($about,$id){
        $update = $this->_perfil->setAboutMe($about,$id);
        if ($update == 1) {
            $message = array('msj' => 'Su registro fue actualizado', 'error' => '98');
            return $message;
        }
        
    }
    public function cancelRequest($id){
        $update = $this->_perfil->cancelRequestParticular($id);
        if ($update == 1) {
            $message = array('msj' => 'la solicitud fue cancelada', 'error' => '73');
            return $message;
        }  
    }

    public function handleSessionCreation()
    {
        $insert = new sessions;
        $object = $insert->tableCreate();
        extract($_POST);
        $id_materia = $materia;
        $dias = $dia;
        $inicio = $horai;
        $final = $horaf;
        //validacion de hora inicial final, no repetir dia y hora

        if ($this->getSql('materia') == 'nones') {
            $modalmessage = array('t' => 0, 'msjx' => 'Debe seleccionar materia', 'err' => 1);
            return $modalmessage;
        }
        if ($this->getSql('dia') == 0) {
            $modalmessage = array('t' => 0, 'msjx' => 'Debe seleccionar el dia', 'err' => 2);
            return $modalmessage;
        }
        if ($this->getSql('horai') == 0) {
            $modalmessage = array('t' => 0, 'msjx' => 'Debe seleccionar hora inicial', 'err' => 3);
            return $modalmessage;
        }
        if ($this->getSql('horaf') == 0) {
            $modalmessage = array('t' => 0, 'msjx' => 'Debe seleccionar hora final', 'err' => 4);
            return $modalmessage;
        }
        if ($this->getSql('horai') > $this->getSql('horaf')) {
            $modalmessage = array('t' => 0, 'msjx' => 'Hora inicial debe ser antes de la hora final', 'err' => 3);
            return $modalmessage;
        }
        if ($this->getSql('horai') == $this->getSql('horaf')) {
            $modalmessage = array('t' => 0, 't' => 0, 'msjx' => 'Hora inicial y Hora final no pueden ser las mismas', 'err' => 3);
            return $modalmessage;
        }

        $checkduplicity = $this->_perfil->revisaDuplicidad($dia, $inicio, $final);
        if ($checkduplicity > 0) {
            $modalmessage = array('t' => 0, 'msjx' => 'Este horario no se puede registrar porque coincide con otro', 'err' => 7332);
            return $modalmessage;
        }

        $registro = $this->_perfil->registerSession($id_materia, $dias,  $inicio, $final, $object);

        $message = array('t' => 1, 'msj' => 'El horario quedo registrado', 'error' => '73');
        return $message;
    }
    public function handleSessionEdition()
    { 
        extract($_POST); 
        
        $diaS = $dia;
        $inicio = $horai;
        $final = $horaf;
        $id_horario = $idhora;
        //validacion de hora inicial final, no repetir dia y hora 
        if ($this->getSql('horai') > $this->getSql('horaf')) {
            $modalmessage = array('t' => 0, 'msjx' => 'Hora inicial debe ser antes de la hora final', 'err' => 3);
            return $modalmessage;
        }
        if ($this->getSql('horai') == $this->getSql('horaf')) {
            $modalmessage = array('t' => 0, 't' => 0, 'msjx' => 'Hora inicial y Hora final no pueden ser las mismas', 'err' => 3);
            return $modalmessage;
        } 
        $checkduplicity = $this->_perfil->revisaDuplicidad($diaS, $inicio, $final);
        if ($checkduplicity > 0) {
            $modalmessage = array('t' => 0, 'msjx' => 'Este horario no se puede registrar porque coincide con otro', 'err' => 8332);
            return $modalmessage;
        }

        $registro = $this->_perfil->editSession($id_horario, $diaS,  $inicio, $final);

        $message = array('t' => 1, 'msj' => 'El horario ha sido modificado', 'error' => '73');
        return $message;
    }
    public function deleteMyPerfil()
    {
        $this->_perfil->deleteMiperfil();
        Session::destroy();
        $this->redirect();
    }
}
