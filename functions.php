<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


wp_enqueue_script( 'transist', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.transit/0.9.12/jquery.transit.min.js' , array('jquery'));
wp_enqueue_script( 'script', plugins_url( '/courses_dashboard_2/js/script.js' ), array('jquery'));
wp_enqueue_script( 'courses_dashboard_ajax', plugins_url( '/courses_dashboard_2/ajax/ajax.js' ), array('jquery'));
wp_enqueue_script( 'treeview', plugins_url( '/courses_dashboard_2/libs/treeview/jquery.treeview.js' ), array('jquery'));



//wp_enqueue_script( 'courses_dashboard_js', plugins_url( '/courses_dashboard_2/js/script.js' ), array('jquery'));
wp_enqueue_style('courses_dashboard_css', plugins_url('/courses_dashboard_2/css/style.css'));
wp_enqueue_style('treeview_css', plugins_url('/courses_dashboard_2/libs/treeview/jquery.treeview.css'));



require_once __DIR__ . '/classes/WP_Term_Image.php';
add_action( 'admin_init', '\\Kama\\WP_Term_Image::init' );

use Controllers\AccessController;

require_once __DIR__ . '/controllers/ProgramController.php';
require_once __DIR__ . '/controllers/StudentController.php';
require_once __DIR__ . '/controllers/KeyController.php';
require_once __DIR__ . '/controllers/AccessController.php';
require_once __DIR__ . '/controllers/ProfileController.php';
require_once __DIR__ . '/models/Program.php';
require_once __DIR__ . '/models/Key.php';
require_once __DIR__ . '/models/Student.php';
require_once __DIR__ . '/models/Course.php';
require_once __DIR__ . '/models/Director.php';
require_once __DIR__ . '/ajax/ajax.php';

//require_once __DIR__ . '/views/program/programs_list.php';
//require_once __DIR__ . '/views/students_control/students_control_list.php';
//Access keys
//------------------------------------------------------------



//function cd__access_get_user_courses(){
//    $model = new AccessController();
//    return $model->actionGetUserCourses();
//}



//function cd__generate_key(){
//    $alphabet = '1234567890';
//    $pass = array(); //remember to declare $pass as an array
//    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
//    for ($i = 0; $i < 36; $i++) {
//        $n = rand(0, $alphaLength);
//        $pass[] = $alphabet[$n];
//    }
//    return $key = implode($pass);
//}
//
//function cd__insert_access_key($key){
//    global $wpdb;
//
//    $table_name = $wpdb->prefix . "courses_dashboard__access_keys";
//    $wpdb->insert( $table_name, [ 'access_key' =>  $key ]);
//    return $wpdb->insert_id;
//}
//function cd__create_access_key(){
//    //$key = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 99999))), 0, 30), 7));
//    do{
//        $key = cd__generate_key();
//    } while(!empty(cd__get_from_table('courses_dashboard__access_keys', 'access_key', $key)));
//
//    return cd__insert_access_key($key);
//}
//function cd__connect_user_with_key($user_id, $key_id){
//    global $wpdb;
//    $table_name = $wpdb->prefix . "courses_dashboard__users_keys";
//    $wpdb->insert( $table_name, [ 'user_id' =>  $user_id, 'key_id' => $key_id ]);
//}
//function cd__connect_key_with_course($course_id, $key_id){
//    global $wpdb;
//    $table_name = $wpdb->prefix . "courses_dashboard__courses_keys";
//    $wpdb->insert( $table_name, [ 'course_id' =>  $course_id, 'key_id' => $key_id ]);
//}
//function cd__connect_key_with_student($student_id, $key_id){
//    global $wpdb;
//    $table_name = $wpdb->prefix . "courses_dashboard__students_keys";
//    $wpdb->insert( $table_name, [ 'student_id' =>  $student_id, 'key_id' => $key_id ]);
//}
//function cd__create_and_attach_key($user_id, $course_id){
//    $key_id = cd__create_access_key();
//    cd__connect_user_with_key($user_id, $key_id);
//    cd__connect_key_with_course($course_id, $key_id);
//}

//--------------------------------------------------------------


//Courses
//------------------------------------------------------------

function cd__get_user_keys($user_id){

    global $wpdb;
    $table = $wpdb->prefix . "courses_dashboard__users_keys";
    $data = $wpdb->get_results(
        "
	SELECT * 
	FROM $table WHERE user_id = $user_id
	"
    );
    return $data;
}
function cd__get_from_table($tab_name, $key, $value){
    global $wpdb;
    $table = $wpdb->prefix . $tab_name;
    if(is_array($value)){
        $comma_separated = "('" . implode("','", $value) . "')";  // ('1','2','3')
        $data = $wpdb->get_results(
            "
            SELECT * 
            FROM $table WHERE $key in $comma_separated
            "
        );
    }else{
        $data = $wpdb->get_results(
            "
            SELECT * 
            FROM $table WHERE $key = $value
            "
        );
    }

    return $data;
}
function cd__get_user_courses($user_id){
    global $wpdb;
    $table = $wpdb->prefix . "courses_dashboard__courses_keys";

    $users_keys = cd__get_user_keys($user_id);

    $users_keys_ids = [];

    foreach ($users_keys as $key){
        $users_keys_ids[] = $key->key_id;
    }
    $courses = [];
    foreach ($users_keys_ids as $key_id){
        $courses[] = $wpdb->get_results(
            "
            SELECT * 
            FROM $table WHERE key_id = $key_id
            "
        )[0];
    }

    return $courses;
}

//--------------------------------------------------------------

function cd__show_selected_table($table){
    global $wpdb;
    $data = $wpdb->get_results(
        "
	SELECT * 
	FROM $table
	"
    );
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
function cd__delete_row($table_name, $col_name, $col_value){
    global $wpdb;
    $wpdb->delete( $wpdb->prefix . $table_name, [ $col_name => $col_value ] );
}


// View
//--------------------------------------------------------------

add_shortcode( 'render_courses_list', 'render_courses_list__shortcode' );

function render_courses_list__shortcode( $atts ){
    if( !is_user_logged_in() ) return;

    ?>
    <?php

    $atts = shortcode_atts( [
        'courses_ids'      => '',
        'access_keys_page' => false,
        'students_page'    => false,
        'courses_page'     => false,
        'statistic_page'     => false
    ], $atts );
    $user_id = get_current_user_id();

    if($atts['courses_ids'] === ''){
        $courses_ids = [];
        foreach (cd__get_user_courses($user_id) as $item){
            $courses_ids[] = $item->course_id;
        }
        if(!empty($courses_ids)){
            $atts['courses_ids'] = $courses_ids;
        }
    }

    $terms = get_terms( [
        'taxonomy' => 'wpm-category',
        'include'  => $atts['courses_ids']
    ] );

    ?>


    <div id="lk-content" ></div>
    <div class="cd__dashboard_body">

        <?php if($atts['courses_page']): ?>
        <div class="cd__access_key_form">
            <input class="cd__access_key_form_input" type="text" placeholder="0000-0000-0000...">
            <button class="cd__access_key_form_submit">Введите ваш код доступа</button>
            <div class="cd__access_key_form_result"></div>
        </div>


        <?php $user_info = get_userdata(get_current_user_id());?>
        <?php if($user_info->caps["customer_company"]):?>
            <div class="cd__access_key_form">
                <input class="cd__course_id" type="text" placeholder="число">
                <button class="cd__send_add_course_to_head_submit">Введите ID курса</button>
            </div>

        <?php endif; ?>

        <?php endif; ?>
        <?php

        if(empty($courses_ids)){
            echo '<blockquote>У вас пока нет курсов</blockquote>';
            return;
        }
        ?>


        <div class="cd__dashboard_tab_head">
            <?php if($atts['access_keys_page']): ?>
                <h3>Коды доступа</h3>
            <?php elseif($atts['students_page']): ?>
                <h3>Список студентов</h3>
            <?php elseif($atts['courses_page']): ?>
                <h3>Доступные вам курсы:</h3>
            <?php endif; ?>
        </div>


        <?php if($atts['statistic_page']): ?>
            <div class="cd__show_statistic_trigger" data-course_id="<?php echo $term_id; ?>">
                <a href ="" >
                    <img src="<?php echo $image_url;?>" alt="" />
                    <div>
                        <?php echo $term->name;?>
                        <div>
                            <button>Смотреть статистику</button>
                        </div>
                    </div>
                </a>
                <?php
                global $wpdb;

                $where = '';
                $loginTable = MBLStats::getTable();
                $usersTable = $wpdb->prefix . "users";
                $limit = "";
                $order = "ORDER BY unique_logins DESC, nb_logins DESC, u.user_login ASC";
                $userWhere = '';

                $nbSubSelect = "SELECT COUNT(lt.id) FROM {$loginTable} lt WHERE lt.user_id = u.ID{$where}";
                $uniqueNbSubSelect = "SELECT COUNT(DISTINCT(lt.ip)) FROM {$loginTable} lt WHERE lt.user_id = u.ID{$where}";

                $users = $wpdb->get_results("SELECT u.*, ({$nbSubSelect}) as nb_logins, ({$uniqueNbSubSelect}) as unique_logins FROM {$usersTable} u WHERE ({$nbSubSelect})>0{$userWhere} {$order} {$limit}", OBJECT);

                $items = array();
                $adminsNb = 0;
                foreach ($users AS $user) {
                    if(wpm_is_admin(get_user_by('ID', $user->ID))) {
                        $adminsNb++;
                        continue;
                    }
                    if ($user->nb_logins) {
                        $user->logins = $wpdb->get_results("SELECT * FROM {$loginTable} lt WHERE user_id={$user->ID}{$where}", OBJECT);
                    } else {
                        $user->logins = array();
                    }

                    $items[] = $user;
                }
                ?>
                <?php if (!empty($items)) : ?>
                    <table class="wp-list-table widefat fixed pages">
                        <thead>
                        <tr>
                            <th class="column-primary column-user"><?php _e('Пользователь', 'mbl_admin'); ?></th>
                            <th class="column-total"><?php _e('Всего заходов', 'mbl_admin'); ?></th>
                            <th class="column-unique"><?php _e('Уникальных', 'mbl_admin'); ?></th>
<!--                            <th class="column-active">--><?php //_e('Активность', 'mbl_admin'); ?><!--</th>-->
<!--                            <th class="column-exclude">--><?php //_e('Исключения', 'mbl_admin'); ?><!--</th>-->
                            <th class="column-actions"></th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th class="column-primary column-user"><?php _e('Пользователь', 'mbl_admin'); ?></th>
                            <th class="column-total"><?php _e('Всего заходов', 'mbl_admin'); ?></th>
                            <th class="column-unique"><?php _e('Уникальных', 'mbl_admin'); ?></th>
<!--                            <th class="column-active">--><?php //_e('Активность', 'mbl_admin'); ?><!--</th>-->
<!--                            <th class="column-exclude">--><?php //_e('Исключения', 'mbl_admin'); ?><!--</th>-->
                            <th class="column-actions"></th>
                        </tr>
                        </tfoot>
                        <?php
                        $i = 0;
                        foreach ($items as $item) {
                            $alternative = (++$i % 2) ? 'alternate ' : '';

                            $user_profile_url = admin_url('/user-edit.php?user_id=' . $item->ID);
                            $user_name = $item->display_name . ($item->user_login != $item->display_name ? ' (' . $item->user_login . ')' : '');
                            $user = $user_name;
                            ?>
                            <tr class="status-publish hentry iedit <?php echo $alternative ?>">
                                <td data-colname="<?php _e('Пользователь', 'mbl_admin'); ?>"
                                    class="column-primary column-title">
                                    <?php echo $user; ?>
                                </td>
                                <td data-colname="<?php _e('Всего заходов', 'mbl_admin'); ?>"
                                    class="column-total">
                                    <?php echo $item->nb_logins; ?>
                                </td>
                                <td data-colname="<?php _e('Уникальных', 'mbl_admin'); ?>" class="column-unique">
                                    <?php echo $item->unique_logins; ?>
                                </td>
<!--                                <td data-colname="--><?php //_e('Активность', 'mbl_admin'); ?><!--" class="column-active">-->
<!--                                    --><?php //echo wpm_show_user_id_column_content('', 'wpm_status', $item->ID, urlencode($current_url)); ?>
<!--                                </td>-->
<!--                                <td data-colname="--><?php //_e('Исключения', 'mbl_admin'); ?><!--" class="column-exclude">-->
<!--                                    <a href="#" data-exclude="--><?php //echo $item->ID; ?><!--">--><?php //echo wpm_is_excluded_from_block($item->ID) ? __('Убрать', 'mbl_admin') : __('Добавить', 'mbl_admin'); ?><!--</a>-->
<!--                                </td>-->
                                <td class="column-actions">
                                    <?php if (!empty($item->logins)) : ?>
                                        <a href="#"
                                           data-toggle-row="<?php echo $item->ID ?>"><?php _e('Подробнее', 'mbl_admin'); ?></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr id="details-row-<?php echo $item->ID ?>">
                                <td colspan="6" class="details-row">
                                    <div style="display: none">
                                        <?php if (!empty($item->logins)) : ?>
                                            <table class="wp-list-table fixed wpm-logins">
                                                <thead>
                                                <tr>
                                                    <th width="5%"><?php _e('№', 'mbl_admin'); ?></th>
                                                    <th><?php _e('Дата и время', 'mbl_admin'); ?></th>
                                                    <th><?php _e('IP', 'mbl_admin'); ?></th>
                                                    <th><?php _e('Страна', 'mbl_admin'); ?></th>
                                                    <th><?php _e('Браузер', 'mbl_admin'); ?></th>
                                                    <th><?php _e('ОС', 'mbl_admin'); ?></th>
                                                    <th><?php _e('Устройство', 'mbl_admin'); ?></th>
                                                </tr>
                                                </thead>
                                                <?php foreach ($item->logins as $k => $login) : ?>
                                                    <tr>
                                                        <td class="check-column"><?php echo $k + 1; ?></td>
                                                        <td class="check-column"><?php echo date_i18n('d.m.Y H:i:s', strtotime($login->logged_in_at) + (get_option('gmt_offset') * HOUR_IN_SECONDS)); ?></td>
                                                        <td class="check-column"><a target=«_blank»
                                                                                    href="http://ipgeobase.ru/?address=<?php echo $login->ip; ?>"><?php echo $login->ip; ?></a>
                                                        </td>
                                                        <td class="check-column"><?php echo implode(' : ', array_filter(array($login->country_name, $login->country_code))); ?></td>
                                                        <td class="check-column"><?php echo $login->browser; ?></td>
                                                        <td class="check-column"><?php echo $login->os; ?></td>
                                                        <td class="check-column">
                                                            <?php echo implode(' : ', array_filter(array(wpm_stats_get_device_type($login->device), $login->brandname, $login->model))); ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }; ?>
                    </table>
                <?php else : ?>
                    <p><?php _e('Нет записей', 'mbl_admin') ?></p>
                <?php endif; ?>
            </div>
        <script>
            jQuery(document).on('click', '[data-toggle-row]', function (e) {
                var item = jQuery(this),
                    id = item.data('toggle-row'),
                    row = jQuery('#details-row-' + id + ' div:first');
                row.slideToggle();
                return false;
            });
        </script>
        <?php endif;?>


        <div class="cd__courses_list">
            <?php
            foreach ($terms as $term ){
                $term_id = $term->term_id;
                $term_link = get_term_link($term_id, 'wpm-category');
                $image_id = get_term_meta( $term_id, '_thumbnail_id', 1 );
                $image_url = wp_get_attachment_image_url( $image_id, 'full' );

                ?>
                <?php if($atts['access_keys_page']): ?>
                    <div class="cd__show_access_keys_trigger" data-course_id="<?php echo $term_id; ?>">
                        <a href ="" >
                            <img src="<?php echo $image_url;?>" alt="" />
                            <div>
                                <?php echo $term->name;?>
                                <div>
                                    <button>Перейти к кодам доступа</button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php elseif($atts['students_page']): ?>
                    <div class="cd__show_students_trigger" data-course_id="<?php echo $term_id; ?>">
                        <a href ="" >
                            <img src="<?php echo $image_url;?>" alt="" />
                            <div>
                                <?php echo $term->name;?>
                                <div>
                                    <button>Перейти к студентам курса</button>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php else: ?>
                    <div>
                        <a href ="<?php echo $term_link;?>" >
                            <img src="<?php echo $image_url;?>" alt="" />
                            <div>
                                <?php echo $term->name;?>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>
                <?php
            }?>
        </div>
        <div class="cd__dashboard_tab_body">
            <div class="cd__dashboard_tab_body_result"></div>
            <button class="cd__dashboard_tab_body_back_trigger">Назад</button>
        </div>
    </div>
    <?php

    return;
}

//--------------------------------------------------------------




//--------------------------------------------------------------
//Ajax functions
//--------------------------------------------------------------


//ТЕХНИЧЕСКАЯ ФУНКЦИЯ ДЛЯ ТЕСТИРОВАНИЯ (добавка ключей руководителю)
//--------------------------------------------------------------------
add_action('wp_ajax_cd__add_access_keys_to_user', 'cd__add_access_keys_to_user');
add_action('wp_ajax_nopriv_cd__add_access_keys_to_user', 'cd__add_access_keys_to_user');
function cd__add_access_keys_to_user(){
    $course_id = $_POST['course_id'];
    $count = $_POST['count'];
    $user_id = get_current_user_id();
    for($i = 1; $i <= (int)$count; $i++){
        cd__create_and_attach_key($user_id, $course_id);
    }
    wp_die();
}
//--------------------------------------------------------------------


//ТЕХНИЧЕСКАЯ ФУНКЦИЯ ДЛЯ ТЕСТИРОВАНИЯ (добавка курса руководителю)
//--------------------------------------------------------------------
add_action('wp_ajax_cd__add_course_to_head', 'cd__add_course_to_head');
add_action('wp_ajax_nopriv_cd__add_course_to_head', 'cd__add_course_to_head');
function cd__add_course_to_head(){
    $course_id = $_POST['course_id'];
    $user_id = get_current_user_id();
    cd__create_and_attach_key($user_id, $course_id);
}
//--------------------------------------------------------------------


//Render access keys tab content
//--------------------------------------------------------------------
add_action('wp_ajax_cd__get_access_keys', 'cd__get_access_keys');
add_action('wp_ajax_nopriv_cd__get_access_keys', 'cd__get_access_keys');
function cd__get_access_keys(){
    $course_id = $_POST['course_id'];
    $user_keys_ids = [];
    $user_id = get_current_user_id();
    $user_keys_all = cd__get_from_table('courses_dashboard__users_keys', 'user_id', $user_id);
    $course_keys_all = cd__get_from_table('courses_dashboard__courses_keys', 'course_id', $course_id);
    $course_keys_all_ids = [];

    foreach ($course_keys_all as $course_key){
        $course_keys_all_ids[] = $course_key->key_id;
    }
    foreach ($user_keys_all as $user_key) {
        $has_key = array_search($user_key->key_id, $course_keys_all_ids);

        if($has_key !== false){
            $user_keys_ids[] = $user_key->key_id;
        }
    }
    $user_keys = cd__get_from_table('courses_dashboard__access_keys', 'id', $user_keys_ids);

    ?>
    <table>
        <tbody>
            <tr>
                <th>Ключ</th>
                <th>Активирован</th>
            </tr>
            <?php
            foreach ($user_keys as $user_key) {
                $user_key->student_id = cd__get_from_table('courses_dashboard__students_keys', 'key_id', $user_key->id)[0];
                ?>
                    <tr>
                        <td><?php echo $user_key->access_key; ?></td>
                        <td><?php echo $user_key->student_id ? 'Да' : 'Нет'; ?></td>
                    </tr>
                <?php
            }?>
        </tbody>
    </table>
    <button data-course_id="<?php echo $course_id; ?>" class="cd__add_5_keys_trigger">Добавить 2 ключа этого курса</button>
    <?php
//    echo '<pre>';
//    print_r($user_keys);
//    echo '</pre>';
    wp_die();
}
//--------------------------------------------------------------------



//Render students tab content
//--------------------------------------------------------------------
add_action('wp_ajax_cd__render_students_tab', 'cd__render_students_tab');
add_action('wp_ajax_nopriv_cd__render_students_tab', 'cd__render_students_tab');
function cd__render_students_tab(){
    $course_id = $_POST['course_id'];
    $user_keys_ids = [];
    $user_id = get_current_user_id();
    $user_keys_all = cd__get_from_table('courses_dashboard__users_keys', 'user_id', $user_id);
    $course_keys_all = cd__get_from_table('courses_dashboard__courses_keys', 'course_id', $course_id);
    $course_keys_all_ids = [];
    $course_category = new MBLCategory(get_term($course_id), true, true);


    foreach ($course_keys_all as $course_key){
        $course_keys_all_ids[] = $course_key->key_id;
    }
    foreach ($user_keys_all as $user_key) {
        $has_key = array_search($user_key->key_id, $course_keys_all_ids);

        if($has_key !== false){
            $user_keys_ids[] = $user_key->key_id;
        }
    }
    $students_keys = cd__get_from_table('courses_dashboard__students_keys', 'key_id', $user_keys_ids);

    ?>
    <table>
        <tbody>
        <tr>
            <th>Логин</th>
            <th>Полное имя</th>
            <th>Email</th>
            <th>Зарегистрирован</th>
            <th>Прогресс</th>
            <th>ID</th>
        </tr>
        <?php

        foreach ($students_keys as $student) {
            $student_id = $student->student_id;
            $user_info = get_userdata($student_id);
            ?>

            <?php if($student_id != $user_id): ?>
            <tr>
                <td>
                    <a href="/account/?user=<?php echo $student_id; ?>">
                        <strong><?php echo $user_info->user_login; ?></strong>
                    </a>
                </td>
                <td><?php echo $user_info->display_name; ?></td>
                <td><?php echo $user_info->user_email; ?></td>
                <td><?php
                    $date = new DateTime($user_info->user_registered);
                    echo $date->format('d.m.Yг в H:i');
                    ?>
                </td>
                <td>
                    <?php echo $course_category->getProgress($student_id); ?>%
                </td>
                <td><?php echo $student_id; ?></td>
            </tr>
            <?php endif;?>


            <?php
        }?>
        </tbody>
    </table>

    <?php
//    echo '<pre>';
//    print_r($user_keys);
//    echo '</pre>';
    wp_die();
}
//--------------------------------------------------------------------


// Регистрация кода доступа в ЛК
//--------------------------------------------------------------------
add_action('wp_ajax_cd__access_key_form_add_key', 'cd__access_key_form_add_key');
add_action('wp_ajax_nopriv_cd__access_key_form_add_key', 'cd__access_key_form_add_key');
function cd__access_key_form_add_key(){
    $key = $_POST['key'];
    $user_id = get_current_user_id();
    $current_key = cd__get_from_table('courses_dashboard__access_keys', 'access_key', $key)[0]->id;

    $same_key_in_table = cd__get_from_table('courses_dashboard__students_keys', 'key_id', $current_key);

    if(!$current_key){
        echo 'Неверный ключ';
    }else if(empty($same_key_in_table)){
        cd__connect_key_with_student($user_id, $current_key);
        cd__connect_user_with_key($user_id, $current_key);
        echo 'Ключ активирован!';
    }else{
        echo 'Ошибка! Ключ уже зарегистрирован!';
    }

    wp_die();
}
//--------------------------------------------------------------------







//connect_key_with_course(random_int(1, 10000), random_int(1, 10000));
//cd__create_and_attach_key(1000, 10000);
//cd__show_selected_table( "wp_courses_dashboard__access_keys");
//create_access_key();
