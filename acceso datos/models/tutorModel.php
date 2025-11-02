<?php

class tutorModel extends Model
{  
    private $_id;
    public function __construct() {
        parent::__construct();
        $this->_id= $_SESSION['id'];
    }
    
    public function getPerfil()
    {
        $post = $this->_db->query("select * from posts");
        return $post->fetchall();
    } 
    public function getUserName()
    {
      
       $user = $this->_db->query("SELECT users.id, users.name, users.apellido, users.cedula,users.telefono, users.email,escuela.id
        as 'id_escuela',escuela.escuela,users.role, users.date,users.foto ,users.about FROM `users` LEFT JOIN escuela on
         escuela.id = users.carrera where users.id=$this->_id");
        return $user->fetch();
    }
    public function getEscuela()
    {
        $escuela = $this->_db->query(
            "SELECT * FROM escuela WHERE 1"
        );
        return $escuela->fetchall();
    }
    public function getsubject($option=false){
         
        $string = "SELECT DISTINCT  subject.id, subject.materia_des FROM
         `subject` INNER JOIN carrera_materia on carrera_materia.idmateria = subject.id WHERE 1  $option";
          $subject = $this->_db->query($string);       
        return $subject->fetchall(); 
    }    
   
public function getsubjectTutor($object){
        
        $subject = $this->_db->query($object);       
        return $subject->fetchall(); 
    }  
    public function getsessions(){  
         $string2 = "SELECT sessions.id_horario, sessions.id_tutor, sessions.id_materia,sessions.dia,sessions.inicio,sessions.final, 
         TIME_FORMAT(SEC_TO_TIME(sessions.inicio * 3600), '%l %p') as 'h_inicio', 
         TIME_FORMAT(SEC_TO_TIME(sessions.final * 3600), '%l %p') as 'h_final' ,
          sessions.inicio,sessions.final,  subject.materia_des,COALESCE(b.inscritos,0) as 'inscritos' 
          FROM (SELECT COUNT(enrollment.id)  as 'inscritos', enrollment.id_horaio as 'cursos' 
          FROM enrollment GROUP BY enrollment.id_horaio)b right JOIN `sessions` on b.cursos = sessions.id_horario
           INNER JOIN subject on subject.id = sessions.id_materia WHERE sessions.id_tutor = $this->_id ORDER BY sessions.dia";
        $sessions = $this->_db->query($string2);       
      return $sessions->fetchall(); 

    }
     
 
    public function revisaDuplicidad($dia,$inicio,$final){
        $string1="SELECT COUNT(sessions.id_horario) as 'cta_1' FROM sessions WHERE sessions.dia = '$dia' AND sessions.inicio = $inicio AND sessions.id_tutor = $this->_id;";
        $string2="SELECT COUNT(sessions.id_horario) as 'cta_2'   FROM sessions WHERE sessions.dia = '$dia' AND  $inicio < sessions.inicio AND $final BETWEEN sessions.inicio AND sessions.final   
                    AND sessions.id_tutor =  $this->_id"; 
        $string3="SELECT COUNT(sessions.id_horario) as 'cta_3'  FROM sessions WHERE sessions.dia = '$dia' AND  $inicio < sessions.inicio AND $final >= sessions.final   
                AND sessions.id_tutor =  $this->_id";
         $sessions = $this->_db->query($string1);     
         $sessions2 = $this->_db->query($string2);     
         $sessions3 = $this->_db->query($string3);       
         $result1 = $sessions->fetch();
         $result2 = $sessions2->fetch();
         $result3 = $sessions3->fetch(); 
         if($result1['cta_1']>0){return 1;}elseif($result2['cta_2']>0){return 1;}elseif($result3['cta_3']>0){return 1;}  
         return 0;
           
    }
    public function editPerfil($id, $name,	$apellido, $telefono,	 $img  ) 
    {
       $id = (int) $id;
       
        $update = $this->_db->prepare("UPDATE users SET name=:name, apellido=:apellido,   telefono=:telefono, foto=:foto  WHERE id= $id");
             $update->execute(
                     array(
                    
                    ':name' => $name,
                    ':apellido' => $apellido, 
                    ':telefono' => $telefono,  
                    ':foto'=>$img  
                ));
                $no = $update->rowCount();        
        return $no ;        
       
    }
    public function getRequests(){
        $string="SELECT CONCAT(users.name,' ', users.apellido) as 'nombre' , subject.materia_des, 
        particulares.memo,particulares.status,particulares.id_particulares FROM `particulares` 
        INNER JOIN users ON  users.id = particulares.id_estudiante INNER JOIN subject ON 
        subject.id = particulares.id_materia WHERE particulares.id_tutor = $this->_id and particulares.status != 1";
         $requests = $this->_db->query($string);       
         return $requests->fetchall(); 

    }
    public function editSession($id_horario, $dia,  $inicio, $final){
        $update = $this->_db->prepare("UPDATE sessions SET dia =:dia, inicio=:inicio,  final=:final  WHERE id_horario= $id_horario");
             $update->execute(
                     array(
                    
                    ':dia' => $dia,  
                    ':inicio' => $inicio, 
                    ':final' => $final 
                ));
                $no = $update->rowCount();        
        return $no ;     

    }
    public function cancelRequestParticular($id){
        $update = $this->_db->prepare("UPDATE particulares SET status =:status WHERE id_particulares= $id");
        $update->execute(
                array( 
               ':status' => 1 
           ));
           $no = $update->rowCount();        
             return $no ; 
    }
    public function setAboutMe($about,$id){
        $update = $this->_db->prepare("UPDATE users SET about=:about WHERE id= $id");
        $update->execute(
                array( 
               ':about' => $about 
           ));
           $no = $update->rowCount();        
   return $no ;        

    }
    public function registerSession( $id_materia, $dia,  $inicio, $final,$object)
    {   

        $registrar = $this->_db->prepare(
            $object
        )
            ->execute(array(
                ':id_tutor' => $this->_id,
                ':id_materia' => $id_materia,
                ':dia'  => $dia,
                ':inicio' => $inicio,
                ':final' => $final 
            ));
           
        return $registrar; 
    } 
     
    public function deleteMiPerfil()
    {
      $string="DELETE FROM users WHERE id = $this->_id";
      $string1="DELETE FROM sessions WHERE id_tutor = $this->_id";
      $string2="DELETE FROM enrollment WHERE id_estudiante = $this->_id";
     
       $this->_db->query($string);
       $this->_db->query($string1);
       $this->_db->query($string2);

    }    
    
}
