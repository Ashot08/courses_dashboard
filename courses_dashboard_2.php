<?php
/*
 * Plugin Name: courses_dashboard_2
 * Author URI:  https://kwork.ru/user/ashot08
 * Author:      Ashot08
*/
if (!defined('ABSPATH')) { exit; }
include_once('functions.php');


/*-------------------Подключение шаблона для страницы "аккаунт"--------------------*/


add_filter( 'theme_templates', 'add_my_template_to_list', 10, 4 );
add_filter( 'template_include', 'my_plugin_template_include' );

// Добавляем в список свои шаблоны для страниц
function add_my_template_to_list( $templates, $wp_theme, $post, $post_type ) {
    if ( 'page' === $post_type ) {
        // Дополняем массив шаблонов своими собственными
        $templates += my_plugin_templates();
    }

    return $templates;
}

// Формируем массив с шаблонами
function my_plugin_templates() {
    $base_path = basename( __DIR__ );

    return [
        $base_path . '/templates/account.php' => 'Шаблон страницы Аккаунт',
        //$base_path . '/templates/page-tpl-2.php' => 'Шаблон из плагина №2',
    ];
}

// Подключает шаблон страницы из плагина
function my_plugin_template_include( $template ) {
    // Если это не страница - возвращаем что есть

    if ( ! is_page('account') ) {
        return $template;
    }

    // Получаем сохранённый шаблон
    $path_slug = get_post_meta( get_the_ID(), '_wp_page_template', true );

    // Если шаблон не плагина - возвращаем что есть
    if ( ! in_array( $path_slug, array_keys( my_plugin_templates() ) ) ) {
        return $template;
    }

    // Создаем полный путь к файлу
    $path_file = wp_normalize_path( WP_PLUGIN_DIR . '/' . $path_slug );

    // Проверяем, есть ли физически файл шаблона и, если да - отдаем движку
    if ( file_exists( $path_file ) ) {
        return $path_file;
    }

    return $template;
}

/*----------------------------------------------------------*/


function courses_dashboard_activate() {
    create_directors_courses_table();
    create_directors_programs_table();
    create_directors_keys_table();
    create_students_keys_table();
    create_programs_keys_table();
    create_programs_courses_table();
    create_students_courses_table();
    create_students_programs_table();
    create_program_table();
    create_key_table();
}


/*------------------- Создание таблиц в БД --------------------*/

function create_directors_courses_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__directors_courses";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              director_id bigint(11) NOT NULL,
              course_id bigint(11) NOT NULL,
              PRIMARY KEY (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

function create_directors_programs_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__directors_programs";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              director_id bigint(11) NOT NULL,
              program_id bigint(11) NOT NULL,
              PRIMARY KEY (id),
              UNIQUE KEY program_id (program_id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

function create_directors_keys_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__directors_keys";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              director_id bigint(11) NOT NULL,
              key_id bigint(11) NOT NULL,
              PRIMARY KEY (id),
              UNIQUE KEY key_id (key_id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}

function create_students_keys_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__students_keys";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              student_id bigint(11) NOT NULL,
              key_id bigint(11) NOT NULL,
              PRIMARY KEY (id),
              UNIQUE KEY key_id (key_id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_programs_keys_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__programs_keys";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              program_id bigint(11) NOT NULL,
              key_id bigint(11) NOT NULL,
              PRIMARY KEY (id),
              UNIQUE KEY key_id (key_id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_programs_courses_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__programs_courses";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              program_id bigint(11) NOT NULL,
              course_id bigint(11) NOT NULL,
              PRIMARY KEY (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_students_courses_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__students_courses";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              student_id bigint(11) NOT NULL,
              course_id bigint(11) NOT NULL,
              PRIMARY KEY (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_students_programs_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__students_programs";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              student_id bigint(11) NOT NULL,
              program_id bigint(11) NOT NULL,
              PRIMARY KEY (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_program_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__program";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              title TINYTEXT NOT NULL,
              image TINYTEXT NOT NULL,
              description TEXT NOT NULL,
              PRIMARY KEY (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}
function create_key_table(){
    global $wpdb;

    $table_name = $wpdb->prefix . "c_dash__key";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "CREATE TABLE " . $table_name . " (
              id bigint(11) NOT NULL AUTO_INCREMENT,
              start_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
              access_key TINYTEXT NOT NULL,
              PRIMARY KEY (id)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }
}


function drop_all_tables(){
    drop_table('c_dash__directors_courses');
    drop_table('c_dash__directors_keys');
    drop_table('c_dash__directors_programs');
    drop_table('c_dash__key');
    drop_table('c_dash__program');
    drop_table('c_dash__programs_courses');
    drop_table('c_dash__programs_keys');
    drop_table('c_dash__students_courses');
    drop_table('c_dash__students_keys');
    drop_table('c_dash__students_programs');
}


function drop_table($table_name){
        global $wpdb;
        $table_name = $wpdb->prefix . $table_name;
        $sql = "DROP TABLE IF EXISTS $table_name";
        $result = $wpdb->query($sql);
}

/*-------------------------------------------------------------------------*/




register_activation_hook( __FILE__, 'courses_dashboard_activate' );
