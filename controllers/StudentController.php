<?php
namespace Controllers;



use Models\Program;
use Models\Student;
use Models\Key;
require_once __DIR__ . '/../views/students_control/students_control_list.php';
require_once __DIR__ . '/../views/students_control/students_control_details.php';

class StudentController{
    public function actionViewDirectorPrograms($director_id){
        $model = new Program();
        $model = $model->getProgramsContentByDirectorId($director_id);
        return students_control_list($model);
    }

    public function actionViewStudentsControlDetails($program_id){
        $model = new Student();
        $model = $model->getStudentsByProgramId($program_id);

        if(empty($model)){
            echo 'not_found';
        }else{
            return students_control_details($model, $program_id);
        }

    }

    public function actionConnectStudentWithProgram($access_key){
        $student_id = get_current_user_id();
        $model = new Program();
        $keyModel = new Key();
        $studentModel = new Student();
        $key = $keyModel->getKeyByAccessKey($access_key);
        $is_key_exist_before = false;
        $is_connection_completed = false;

        if(is_array($key) && !empty($key)){
            $is_key_exist_before = $studentModel->getStudentByKeyId($key[0]->id);
        }else{
            echo 'key_error';
            return;
        }
        if($is_key_exist_before){
            echo 'exist_before';
            return;
        }else{
            $is_connection_completed = $studentModel->connectStudentWithKey($student_id, $key[0]->id);
        }
        if($is_connection_completed){
            $program_id = $model->getProgramByKeyId($key[0]->id);
            $studentModel->connectStudentWithProgram($student_id, $program_id[0]->program_id);
            echo 'success';
        }else{
            echo 'error';
        }

    }


}