<?php
namespace Models;
class Student {

    public function getStudentsByProgramId(int $program_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__students_programs";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT student_id 
        FROM $table WHERE program_id = %d
        ", $program_id
        ));
        return $data;
    }

    public function connectStudentWithProgram(int $student_id, int $program_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__students_programs";
        $wpdb->insert( $table_name, [ 'student_id' => $student_id, 'program_id' =>  $program_id ]);
        return $wpdb->insert_id;
    }

    public function connectStudentWithKey(int $student_id, int $key_id){
        global $wpdb;
        $table_name = $wpdb->prefix . "c_dash__students_keys";
        $wpdb->insert( $table_name, [ 'student_id' => $student_id, 'key_id' =>  $key_id ]);
        return $wpdb->insert_id;
    }

    public function getStudentByKeyId(int $key_id){
        global $wpdb;
        $table = $wpdb->prefix . "c_dash__students_keys";
        $data = $wpdb->get_results($wpdb->prepare(
            "
        SELECT student_id 
        FROM $table WHERE key_id = %d
        ", $key_id
        ));
        return $data;
    }

}
