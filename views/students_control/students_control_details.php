<?php


function students_control_details($data, $program_id){
    $users_array = [];

    foreach ($data as $user){
        $users_array[] = $user->student_id;
    }
    $users_array = array_unique($users_array);
    ?>

<?php if($users_array): ?>

    <table>
        <tbody>
        <tr>
            <th>ID</th>
            <th>Имя</th>
            <th>Логин</th>
            <th>Прогресс</th>
        </tr>
        <?php foreach ($users_array as $student_id):?>

            <?php
            $user_info = get_userdata($student_id);
            $user_id = $user_info->data->ID;
            $user_name = $user_info->data->display_name;
            $user_login = $user_info->data->user_login;
            $course_category = new MBLCategory(get_term($program_id), true, true);
            ?>

            <tr>
                <td><?php echo $user_id; ?></td>
                <td><?php echo $user_name; ?></td>
                <td><?php echo $user_login; ?></td>
                <td><?php echo $course_category->getProgress($user_id); ?>%</td>
            </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?php
}



