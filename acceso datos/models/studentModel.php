<?php

class studentModel extends Model
{
    private $_id;
    public function __construct()
    {
        parent::__construct();
        $this->_id = $_SESSION['id'];
    }


    public function getUserName()
    {

        $user = $this->_db->query("SELECT users.id, users.name, users.apellido, users.cedula,users.telefono, users.email,escuela.id as 'id_escuela',escuela.escuela,users.role,  users.date,users.foto  FROM `users`
    LEFT JOIN escuela on escuela.id = users.carrera where users.id=$this->_id");
        return $user->fetch();
    }
    public function getEscuela()
    {
        $escuela = $this->_db->query(
            "SELECT * FROM escuela WHERE 1"
        );
        return $escuela->fetchall();
    }
   
    public function getSubjectAvailability($option = false)
    {
        if (!$option) {
            $option = "  GROUP BY subject.materia_des; ";
        }
        $string = "SELECT carrera_materia.id,carrera_materia.idcarrera,carrera_materia.idmateria , COALESCE(a.id_materia,0) as 'id_materia' , subject.materia_des
         FROM(SELECT sessions.id_materia FROM `sessions` WHERE 1 GROUP BY sessions.id_materia)a RIGHT JOIN carrera_materia on 
         carrera_materia.idmateria = a.id_materia INNER JOIN subject on carrera_materia.idmateria = subject.id $option";
        $subject = $this->_db->query($string);
        return $subject->fetchall();
    }
    public function getSubjectPreference()
    { 
        $string = "SELECT subject.id, subject.materia_des , a.id_student FROM 
        (SELECT preferencias.id_materia, preferencias.id_student FROM preferencias WHERE preferencias.id_student = $this->_id)a 
        RIGHT JOIN subject ON subject.id = a.id_materia WHERE a.id_student is null ORDER BY materia_des;";
        $subject = $this->_db->query($string);
        return $subject->fetchall();
    }

    public function getTutorAvailability($id)
    {
        $string = "SELECT sessions.id_horario, sessions.id_tutor,sessions.id_materia,users.id, CONCAT(users.name,' ',users.apellido) as 'tutor' 
    FROM sessions INNER JOIN users on users.id = sessions.id_tutor WHERE sessions.id_materia = '$id' GROUP BY sessions.id_tutor";
        $tutores = $this->_db->query($string);
        return $tutores->fetchall();
        //  return array('string' => $string);
    }
    public function getTutorProfile($id)
    {
        $string = "SELECT users.id, CONCAT(users.name,' ',users.apellido) as 'name', users.date, 
    users.foto , users.about FROM users   WHERE users.id = $id ";
        $tutor = $this->_db->query($string);
        return $tutor->fetch();
    }
    public function getsubjectTutor()
    {

        $string = "SELECT subject.materia_des,sessions.id_materia,sessions.id_tutor  FROM `sessions` INNER JOIN subject on subject.id = sessions.id_materia WHERE sessions.id_tutor = $this->_id group by sessions.id_materia ";
        $subject = $this->_db->query($string);
        return $subject->fetchall();
    }
    public function getMyPreferences(){
        $string = "SELECT preferencias.id_preferencia, subject.materia_des FROM preferencias INNER JOIN subject 
        ON subject.id = preferencias.id_materia WHERE preferencias.id_student = $this->_id";
        $subject = $this->_db->query($string);
        return $subject->fetchall();

    }
    public function getSessionsAvailability()
    {
        $string2 = "SELECT sessions.id_horario, sessions.id_tutor, sessions.id_materia,sessions.dia, 
         TIME_FORMAT(SEC_TO_TIME(sessions.inicio * 3600), '%l %p') as 'h_inicio', 
         TIME_FORMAT(SEC_TO_TIME(sessions.final * 3600), '%l %p') as 'h_final' ,
          sessions.inicio,sessions.final,  subject.materia_des,COALESCE(b.inscritos,0) as 'inscritos' 
          FROM (SELECT COUNT(enrollment.id)  as 'inscritos', enrollment.id_horaio as 'cursos' 
          FROM enrollment GROUP BY enrollment.id_horaio)b right JOIN `sessions` on b.cursos = sessions.id_horario
           INNER JOIN subject on subject.id = sessions.id_materia WHERE sessions.id_tutor = $this->_id ORDER BY sessions.dia";
        $sessions = $this->_db->query($string2);
        return $sessions->fetchall();
    }
    public function getMyRequests(){
        $string="SELECT CONCAT(users.name,' ', users.apellido) as 'nombre' , subject.materia_des, particulares.memo,particulares.status,particulares.id_particulares FROM `particulares` 
        INNER JOIN users ON users.id = particulares.id_tutor INNER JOIN subject ON subject.id = particulares.id_materia WHERE 
        particulares.id_estudiante = $this->_id and particulares.status != 1";
        $request= $this->_db->query($string);
        return $request->fetchAll();
    }
    public function getsessionsEst($id)
    {
        $string = "SELECT sessions.id_horario, sessions.id_tutor, sessions.id_materia,sessions.dia,
         TIME_FORMAT(SEC_TO_TIME(sessions.inicio * 3600), '%l %p') as 'h_inicio', 
         TIME_FORMAT(SEC_TO_TIME(sessions.final * 3600), '%l %p') as 'h_final' , 
         sessions.inicio,sessions.final,10-COUNT(enrollment.id_estudiante) as 'cupo', subject.materia_des FROM `sessions` 
         INNER JOIN subject on subject.id = sessions.id_materia left join enrollment on enrollment.id_horaio = sessions.id_horario WHERE sessions.id_tutor = $id
         GROUP BY sessions.id_horario ORDER BY sessions.dia";
        $string2 = "SELECT b.id_horario, b.id_tutor, b.id_materia, b.dia,
       b.h_inicio, b.h_final, b.inicio, b.final, b.cupo, b.materia_des, 
       CASE WHEN ins.id_estudiante IS NOT NULL THEN 'Inscrito' ELSE 'No Inscrito' END AS estado_inscripcion
FROM (
    SELECT sessions.id_horario, sessions.id_tutor, sessions.id_materia, sessions.dia,
           TIME_FORMAT(SEC_TO_TIME(sessions.inicio * 3600), '%l %p') AS 'h_inicio',
           TIME_FORMAT(SEC_TO_TIME(sessions.final * 3600), '%l %p') AS 'h_final',
           sessions.inicio, sessions.final, enrollment.id_horaio,
           10 - COUNT(enrollment.id_estudiante) AS 'cupo', 
           subject.materia_des
    FROM sessions
    INNER JOIN subject ON subject.id = sessions.id_materia
    LEFT JOIN enrollment on enrollment.id_horaio = sessions.id_horario
    WHERE sessions.id_tutor = $id
    GROUP BY sessions.id_horario
    ORDER BY sessions.dia
) b
LEFT JOIN enrollment ins ON ins.id_horaio = b.id_horario AND ins.id_estudiante = $this->_id;
ORDER BY b.dia;";
        $sessions = $this->_db->query($string2);
        return $sessions->fetchall();
    }
    public function getEnrollmentsByStudent()
    {
        $string = "SELECT enrollment.id, sessions.id_horario, sessions.id_tutor,sessions.id_materia,sessions.dia, 
    TIME_FORMAT(SEC_TO_TIME(sessions.inicio * 3600), '%l %p') AS 'h_inicio',
     TIME_FORMAT(SEC_TO_TIME(sessions.final * 3600), '%l %p') AS 'h_final',subject.materia_des, 
     CONCAT(users.name,' ',users.apellido) as 'tutor', users.telefono, users.email,users.foto FROM `sessions` 
     INNER JOIN enrollment on enrollment.id_horaio = sessions.id_horario INNER JOIN subject on subject.id = sessions.id_materia
      INNER JOIN users on users.id = sessions.id_tutor WHERE enrollment.id_estudiante =$this->_id;";
        $inscripcion = $this->_db->query($string);
        return $inscripcion->fetchall();
    }

    public function editPerfil($id, $name,    $apellido, $carrera, $img)
    {
        $id = (int) $id;
        $update = $this->_db->prepare("UPDATE users SET name=:name, apellido=:apellido, carrera=:carrera, foto=:foto  WHERE id= $id");
        $update->execute(
            array(

                ':name' => $name,
                ':apellido' => $apellido,
                ':carrera' => $carrera,
                ':foto' => $img
            )
        );
        $no = $update->rowCount();
        return $no;
    }
    public function editPreferenceSubject($newpreference,$preferenceid){  
        $update = $this->_db->prepare("UPDATE preferencias SET id_materia=:id_materia  WHERE id_preferencia= $preferenceid");
        $update->execute(
            array( 
                ':id_materia' => $newpreference                 
            )
        );
        $no = $update->rowCount();
        return $no; 
    }
    public function editRequestParticulares($idrq,$memo){
        $update = $this->_db->prepare("UPDATE particulares SET memo=:memo  WHERE id_particulares= $idrq");
        $update->execute(
            array( 
                ':memo' => $memo                 
            )
        );
        $no = $update->rowCount(); 
        return $no;
  
    }

    public function registerEnrollment($id, $object)
    {

        $registrar = $this->_db->prepare(
            $object
        )
            ->execute(array(
                'id_estudiante' => $this->_id,
                ':id_horario' => $id
            ));

        return $registrar;
    }
    public function registerRequest($idt,$idm,$object=false){
        $registrar = $this->_db->prepare(
        /* "INSERT INTO particulares values" .
                "(null,:id_estudiante,:id_tutor,:id_materia,:memo)"//*/ $object
        )
            ->execute(array(
                'id_estudiante' => $this->_id,
                ':id_tutor' => $idt,
                ':id_materia' => $idm,
                ':memo'=>"Hola, quisiera agendar una clase particular",
                ':status'=>0
            ));

        return $registrar;

    }
    public function addPreferenceSubject($preference,$object){ 
        $preference = $this->_db->prepare(
            $object
        )
            ->execute(array(
                'id_student' => $this->_id,
                ':id_materia' => $preference
            ));

        return $preference; 
    }
    public function deleteMiPerfil()
    {
        $string = "DELETE FROM users WHERE id = $this->_id";
        $string1 = "DELETE FROM sessions WHERE id_tutor = $this->_id";
        $string2 = "DELETE FROM enrollment WHERE id_estudiante = $this->_id";

        $this->_db->query($string);
        $this->_db->query($string1);
        $this->_db->query($string2);
    }
    public function deleteEnrrollment($enrollNumber){ 
        $registrar = $this->_db->prepare(
            "DELETE FROM enrollment WHERE id = $enrollNumber"
        )
            ->execute();

        return $registrar;
        
    }
}
