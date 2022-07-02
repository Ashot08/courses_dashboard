<?php
namespace Controllers;

use Models\Course;
use Models\Program;
use Models\Director;
use Models\Key;

require_once __DIR__ . '/../views/program/programs_list.php';
require_once __DIR__ . '/../views/program/create_program.php';
require_once __DIR__ . '/../views/program/program_details.php';
require_once __DIR__ . '/../views/program/chapters_list.php';

class ProgramController{
    public function actionViewDirectorPrograms($director_id){
        $model = new Program();
        $keyModel = new Key();

        $user = get_current_user_id();
        $user_info = get_userdata(get_current_user_id());
        $is_company = false;
        if(is_user_logged_in() && isset($user_info->caps["customer_company"])){
            $is_company = true;
        }
        if(!$user)return false;
        if($is_company){
            $model = $model->getProgramsContentByDirectorId($user);
        }else{
            $keys =  $keyModel->getKeysByStudentId($user);
            $programs = [];
            if(is_array($keys) && !empty($keys)){
                foreach ($keys as $key){
                    $program = $model->getProgramByKeyId($key->key_id);
                    if($program){
                        $programs[] = $model->getProgram($program[0]->program_id);
                    }
                }
            }
            $model = $programs;
        }
        return programs_list($model);
    }
    public function actionViewCreateProgram($director_id){
        $model = new Course();
        $model = $model->getCoursesByDirectorId($director_id);
        return create_program($model);
    }
    public function actionViewChaptersList($course_id){
        return chapters_list($course_id);
    }
    public function actionViewProgramDetails($program_id){
        $model = new Course();
        $model = $model->getCoursesByProgramId($program_id);
        if($model){
            return program_details($model);
        }else{
            echo '';
        }

    }
    public function actionCreateProgram($director_id, $title, $description, $coursesIds){
        if(!$title){
            echo 'errorName';
        }elseif (!$coursesIds){
            echo 'errorCoursesIds';
        }else{
            $model = new Program($title, $description);
            $programId = $model->createProgram();
            $course = new Course();
            foreach ($coursesIds as $courseId){
                $course->connectCourseWithProgram($courseId, $programId);
            }
            $director = new Director();
            $director->connectDirectorWithProgram($director_id, $programId);
            echo 'success';
        }
    }
    public function actionAddCourseToDirector($director_id, $course_id){
        $model = new Director();
        $model->connectDirectorWithCourse($director_id, $course_id);
        echo 'Вы (id = ' . $director_id . ') зачислены на курс с ID = ' . $course_id;
    }
}