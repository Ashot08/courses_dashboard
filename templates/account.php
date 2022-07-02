<?php
/**
 * The template for displaying Account Page
 */


get_header();

?>

<div class="cd__account_wrapper">
    <div class="cd__account">

        <?php
        $user_info = get_userdata(get_current_user_id());
        $user_name = $user_info->data->display_name;
        $user_login = $user_info->data->user_login;
        ?>

        <div class="cd__lk_header">
            <div class="cd__lk_header_title">
                Добро пожаловать, <?php echo $user_name ?: $user_login; ?>!
            </div>
        </div>

        <?php

        if(is_user_logged_in() && isset($user_info->caps["customer_company"])):?>

            <div class="cd__tabs" data-user_id="<?= get_current_user_id(); ?>">
                <ul class="cd__tabs_nav">
                    <li data-tab="programs" class="cd__tabs_nav_item">Учебные Программы</li>
                    <li data-tab="students_control" class="cd__tabs_nav_item">Контроль студентов</li>
                    <li data-tab="keys" class="cd__tabs_nav_item">Коды доступа</li>
                    <li data-tab="statistic" class="cd__tabs_nav_item">Статистика посещений</li>
<!--                    <li data-tab="courses" class="cd__tabs_nav_item">Курсы</li>-->
                    <li data-tab="profile" class="cd__tabs_nav_item">Профиль</li>
                </ul>
                <div class="cd__steps_warnings"></div>
                <div class="cd__tabs_content"></div>
            </div>

        <?php elseif (is_user_logged_in()): ?>

            <div class="cd__tabs" data-user_id="<?= get_current_user_id(); ?>">
                <ul class="cd__tabs_nav">
                    <li data-tab="programs" class="cd__tabs_nav_item">Учебные Программы</li>
                    <li data-tab="profile" class="cd__tabs_nav_item">Профиль</li>
                </ul>
                <div class="cd__steps_warnings"></div>
                <div class="cd__tabs_content"></div>
            </div>

        <?php else: ?>

            <?php echo do_shortcode('[woocommerce_my_account]'); ?>

        <?php endif; ?>
    </div>
</div>

<div class="cd__modal_wrapper">
    <div class="cd__modal">
        <div class="cd__modal_back cd__modal_toggler"></div>
        <div class="cd__modal_content">
            <div class="cd__modal_close cd__modal_toggler">
                <svg style="width:36px;height:36px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V5H19V19M17,8.4L13.4,12L17,15.6L15.6,17L12,13.4L8.4,17L7,15.6L10.6,12L7,8.4L8.4,7L12,10.6L15.6,7L17,8.4Z"></path>
                </svg>
            </div>
            <div class="cd__modal_result">

            </div>
        </div>
    </div>
</div>


<?php

get_footer();
