<?php

use Controllers\ProgramController;
use Controllers\StudentController;
use Controllers\KeyController;
use Controllers\ProfileController;

add_action('wp_ajax_cd__get_director_programs_list', 'cd__get_director_programs_list');
add_action('wp_ajax_nopriv_cd__get_director_programs_list', 'cd__get_director_programs_list');
function cd__get_director_programs_list(){
    $director_id = $_POST['id'];
    $controller = new ProgramController();
    $controller->actionViewDirectorPrograms($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_profile', 'cd__get_profile');
add_action('wp_ajax_nopriv_cd__get_profile', 'cd__get_profile');
function cd__get_profile(){
    $controller = new ProfileController();
    $controller->actionViewProfile();
    wp_die();
}

add_action('wp_ajax_cd__get_students_control_details', 'cd__get_students_control_details');
add_action('wp_ajax_nopriv_cd__get_students_control_details', 'cd__get_students_control_details');
function cd__get_students_control_details(){
    $program_id = $_POST['id'];
    $controller = new StudentController();
    $controller->actionViewStudentsControlDetails($program_id);
    wp_die();
}


add_action('wp_ajax_cd__get_students_control_programs_list', 'cd__get_students_control_programs_list');
add_action('wp_ajax_nopriv_cd__get_students_control_programs_list', 'cd__get_students_control_programs_list');
function cd__get_students_control_programs_list(){
    $director_id = $_POST['id'];
    $controller = new StudentController();
    $controller->actionViewDirectorPrograms($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_keys_programs_list', 'cd__get_keys_programs_list');
add_action('wp_ajax_nopriv_cd__get_keys_programs_list', 'cd__get_keys_programs_list');
function cd__get_keys_programs_list(){
    $director_id = get_current_user_id();
    $controller = new KeyController();
    $controller->actionViewDirectorPrograms($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_key_programs_details', 'cd__get_key_programs_details');
add_action('wp_ajax_nopriv_cd__get_key_programs_details', 'cd__get_key_programs_details');
function cd__get_key_programs_details(){
    $program_id = $_POST['id'];
    $controller = new KeyController();
    $controller->actionViewProgramKeys($program_id);
    wp_die();
}

add_action('wp_ajax_cd__create_and_attach_key', 'cd__create_and_attach_key');
add_action('wp_ajax_nopriv_cd__create_and_attach_key', 'cd__create_and_attach_key');
function cd__create_and_attach_key(){
    $director_id = get_current_user_id();
    $program_id = $_POST['id'];
    $controller = new KeyController();
    $controller->actionCreateAndAttachKey($director_id, $program_id);
    wp_die();
}




add_action('wp_ajax_cd__get_create_program_view', 'cd__get_create_program_view');
add_action('wp_ajax_nopriv_cd__get_create_program_view', 'cd__get_create_program_view');
function cd__get_create_program_view(){
    //$director_id = $_POST['id'];
    $director_id = get_current_user_id();
    $controller = new ProgramController();
    $controller->actionViewCreateProgram($director_id);
    wp_die();
}

add_action('wp_ajax_cd__get_program_details', 'cd__get_program_details');
add_action('wp_ajax_nopriv_cd__get_program_details', 'cd__get_program_details');
function cd__get_program_details(){
    $program_id = $_POST['id'];
    $controller = new ProgramController();
    $controller->actionViewProgramDetails($program_id);
    wp_die();
}

add_action('wp_ajax_cd__create_new_program', 'cd__create_new_program');
add_action('wp_ajax_nopriv_cd__create_new_program', 'cd__create_new_program');
function cd__create_new_program(){
    $director_id = get_current_user_id();
    $name = $_POST['name'];
    $description = $_POST['description'];
    $courses_lvl_1 = $_POST['courses_lvl_1'] ?? [];
    $courses_lvl_2 = $_POST['courses_lvl_2'] ?? [];
    $alwaysChecked = $_POST['alwaysChecked'] ?? [];

    $courses = array_merge($courses_lvl_1, $courses_lvl_2, $alwaysChecked);

    $controller = new ProgramController();
    $controller->actionCreateProgram($director_id, $name, $description, $courses );
    wp_die();
}


add_action('wp_ajax_cd__create_new_separate_programs', 'cd__create_new_separate_programs');
add_action('wp_ajax_nopriv_cd__create_new_separate_programs', 'cd__create_new_separate_programs');
function cd__create_new_separate_programs(){
    $director_id = get_current_user_id();
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $description = $_POST['description'];
    $courses_lvl_1 = $_POST['courses_lvl_1'] ?? [];
    $courses_lvl_2 = $_POST['courses_lvl_2'] ?? [];

    if(!$name){
        echo 'errorName';
        wp_die();
    }else{
        $name .= " ($surname)";
    }

    if(empty($courses_lvl_2)){
        wp_die();
    }

    $controller = new ProgramController();

    $controller->actionCreateProgram($director_id, $name, $description, $courses_lvl_2 );


    wp_die();
}


add_action('wp_ajax_cd__add_course_to_director', 'cd__add_course_to_director');
add_action('wp_ajax_nopriv_cd__add_course_to_director', 'cd__add_course_to_director');
function cd__add_course_to_director(){
    $course_id = $_POST['id'];
    $director_id = get_current_user_id();

    $controller = new ProgramController();
    $controller->actionAddCourseToDirector($director_id, $course_id);
    wp_die();
}

add_action('wp_ajax_cd__get_chapters_list', 'cd__get_chapters_list');
add_action('wp_ajax_nopriv_cd__get_chapters_list', 'cd__get_chapters_list');
function cd__get_chapters_list(){
    $course_id = $_POST['id'];
    $controller = new ProgramController();
    $controller->actionViewChaptersList($course_id);
    wp_die();
}

add_action('wp_ajax_cd__connect_student_with_program', 'cd__connect_student_with_program');
add_action('wp_ajax_nopriv_cd__connect_student_with_program', 'cd__connect_student_with_program');
function cd__connect_student_with_program(){
    $key = $_POST['key'];
    $controller = new StudentController();
    $controller->actionConnectStudentWithProgram($key);
    wp_die();
}