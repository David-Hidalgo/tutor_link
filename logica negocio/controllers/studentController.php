<?php
require_once ROOT .'acceso datos'.DS. 'clases entidad' . DS . 'preferencias.php';
require_once ROOT .'acceso datos'.DS. 'clases entidad' . DS . 'enrollment.php';
require_once ROOT . 'acceso datos'.DS.'clases entidad' . DS . 'particulares.php';
class studentController extends Controller
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
        $this->_perfil =  $this->loadModel('student');
        $this->_register = $this->loadModel('user');
        include_once  ROOT . 'libs' . DS .  "class.upload.php";
    }

    public function index()
    {
        $this->_view->title = 'Mi Perfil';
        $user = $this->_perfil->getUserName();
        $escuela = $this->_perfil->getEscuela();
        $subject = $this->_perfil->getSubjectAvailability();
        $subjecttutor = $this->_perfil->getsubjectTutor();
        $sessions = $this->_perfil->getSessionsAvailability();
        $enrollment = $this->_perfil->getEnrollmentsByStudent();
        $this->_view->preference = $this->_perfil->getSubjectPreference();
        $this->_view->myPreference = $this->_perfil->getMyPreferences();
        $this->_view->requests = $this->_perfil->getMyRequests();
        $this->_view->perfil = $user;
        $this->_view->escuela = $escuela;
        $this->_view->subject = $subject;
        $this->_view->subjecttutor = $subjecttutor;
        $this->_view->sessions = $sessions;
        $this->_view->enrollment = $enrollment;
        extract($_POST);
        if(isset($reqmod)){
            $memo = $this->getSql('about');
            $idrq= $this->getInt('reqid');
            
            $edit = $this->editRequest($idrq,$memo);
            $this->_view->_message = $edit; 
            $this->_view->requests = $this->_perfil->getMyRequests();
            $this->_view->show('viewstudent', 'student');  
        }
        if(isset($edpref)){ 
            $newpreference = $this->getInt('editprefer');
            $preferenceid= $this->getInt('pref_id');
            $edit = $this->editPreference($newpreference,$preferenceid);
            $this->_view->_message = $edit;
            $this->_view->perfil = $this->_perfil->getUserName();
            $this->_view->preference = $this->_perfil->getSubjectPreference();
            $this->_view->myPreference = $this->_perfil->getMyPreferences();
            $this->_view->show('viewstudent', 'student');
         }
        if(isset($adpref)){
           $preference = $this->getInt('prefer');
           $add = $this->addPreference($preference);
           $this->_view->_message = $add;
           $this->_view->perfil = $this->_perfil->getUserName();
           $this->_view->preference = $this->_perfil->getSubjectPreference();
           $this->_view->myPreference = $this->_perfil->getMyPreferences();
           $this->_view->show('viewstudent', 'student');
        }
        if(isset($elmenr)){
            $enr=$this->getInt('enroll');
            $delete = $this->cancelEnrrollment($enr);
            $this->_view->_message = $delete;
            //$this->_view->sessions = $sessions;
            $this->_view->enrollment = $this->_perfil->getEnrollmentsByStudent();
            $this->_view->show('viewstudent', 'student');
             
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
            ///
            $modification = $this->validateEnrollmentData();
            $this->_view->_message = $modification;
            $this->_view->perfil = $this->_perfil->getUserName();
            $this->_view->show('viewstudent', 'student');
        }
        $this->_view->show('viewstudent', 'student');
    }
    public function validateEnrollmentData()
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

        if ($escu < 1 || $escu > 3) {
            $message = array('msj' => 'Debe seleccionar la carrera', 'error' => 8);
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
        $escuela = $escu;


        $perfil = $this->_perfil->editPerfil($user['id'], $namex,    $apellido, $escuela,   $img);
        if ($perfil == 1) {
            $message = array('msj' => 'Su registro fue actualizado', 'error' => '43');
            return $message;
        }
    }
    public function addPreference($preference){
        $insert= new preferencias;
        $object = $insert->tableCreate();
        $preference = $this->_perfil->addPreferenceSubject($preference,$object); 
            $message = array('msj' => 'Su preferencia fue registrada', 'error' => '18');
            return $message;  
    }

    public function editPreference($newpreference,$preferenceid){
            $this->_perfil->editPreferenceSubject($newpreference,$preferenceid); 
            $message = array('msj' => 'Su preferencia fue modificada', 'error' => '18');
            return $message;  
    }
    public function editRequest($idrq,$memo){
        $this->_perfil->editRequestParticulares($idrq,$memo); 

            $message = array('msj' => 'Su Solicitud fue modificada', 'error' => '33');
            return $message;
    }
    public function handleSubjectFilter()
    {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)

        }
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;
        $rawPostData = file_get_contents('php://input');
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $id = $requestData['id_carrera'];
        if (intval($id) > 0) {
            $option = "  WHERE carrera_materia.idcarrera = $id "; //" AND carrera_materia.idcarrera = $id "; 
        } elseif ($id == "nones") {
            $option = "  GROUP BY subject.materia_des; ";
        }

        $subject = $this->_perfil->getSubjectAvailability($option);
        echo json_encode($subject);
    } 
    public function handleTutorFilter()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)

        }
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;
        $rawPostData = file_get_contents('php://input');
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $id = $requestData['id_materia'];

        $tutores = $this->_perfil->getTutorAvailability($id);
        echo json_encode($tutores);
    }
    public function getsessionsEst()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)

        }
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;
        $rawPostData = file_get_contents('php://input');
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $id = $requestData['id_tutor'];

        $horario = $this->_perfil->getsessionsEst($id);
        echo json_encode($horario);
    }
    public function handleTutorProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)

        }
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;
        $rawPostData = file_get_contents('php://input');
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $id = $requestData['id_tutor'];

        $perfilTutor = $this->_perfil->getTutorProfile($id);
        echo json_encode($perfilTutor);
    }
    public function getenrollment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)

        }
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;

        $perfilTutor = $this->_perfil->getEnrollmentsByStudent();
        echo json_encode($perfilTutor);
    }
    public function handleEnrollment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Redirect to an error page (replace 'error-page' with the actual URL)
            $this->redirect();
        }
        $insert = new enrollment;
        $object = $insert->tableCreate();
        //echo 'toy'; echo json_encode($this->_client->getEdos(1));exit;
        $rawPostData = file_get_contents('php://input');
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $id = $requestData['id_horario'];


        $perfilTutor = $this->_perfil->registerEnrollment($id, $object);

        echo json_encode($perfilTutor);
    }
    public function requestPrivateClass(){
        header('Content-Type: application/json'); // Set the content type to JSON
    
        // Enable error reporting (for debugging purposes)
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
       // $particular = new particulares;
        // Get the raw POST data
        $rawPostData = file_get_contents('php://input');
         $insert = new particulares;
         $obj = $insert->tableCreate();
       
       $object = "INSERT INTO particulares values" .
                "(null,:id_estudiante,:id_tutor,:id_materia,:memo)"; // $insert->tableCreate();
        // Parse the JSON data into an associative array
        $requestData = json_decode($rawPostData, true);
        $idt = $requestData['id_tutor'];
        $idm = $requestData['id_materia'];
        $request = $this->_perfil->registerRequest($idt,$idm,$obj);

    
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['error' => 'Invalid JSON data']);
            exit();
        }
    
        // Return the received data as a JSON response
        echo json_encode($requestData);
        exit();
    }
    

    public function deleteMyPerfil()
    {
        $this->_perfil->deleteMiperfil();
        Session::destroy();
        $this->redirect();
    }
    public function cancelEnrrollment($enr){
        $delete=$this->_perfil->deleteEnrrollment($enr);
        $message = array('msj' => 'Se Elimino la inscripcion correctamente', 'error' => 33);
        return $message;
        
    }
}
