<?php
function chapters_list($data){
    ?>
    <h3>Выберите нужные разделы</h3>

<div class="cd__chapters" data-course_id="<?= $data; ?>">
    <?php



    function renderTerms($parent, $root_course_id, $level = 1){
        $args = [
            'taxonomy'      => [ 'wpm-category' ], // название таксономии с WP 4.5
            'orderby'       => 'id',
            'order'         => 'ASC',
            'hide_empty'    => true,
            'object_ids'    => null,
            'include'       => array(),
            'exclude'       => array(),
            'exclude_tree'  => array(),
            'number'        => '',
            'fields'        => 'all',
            'count'         => false,
            'slug'          => '',
            'parent'         => $parent,
            'hierarchical'  => true,
            'child_of'      => 0,
            'get'           => '', // ставим all чтобы получить все термины
            'name__like'    => '',
            'pad_counts'    => false,
            'offset'        => '',
            'search'        => '',
            'cache_domain'  => 'core',
            'name'          => '',    // str/arr поле name для получения термина по нему. C 4.2.
            'childless'     => false, // true не получит (пропустит) термины у которых есть дочерние термины. C 4.2.
            'update_term_meta_cache' => true, // подгружать метаданные в кэш
            'meta_query'    => '',
        ];

        $terms = get_terms( $args );
        ?>
        <?php if($terms): ?>
            <ul class="cd__chapters_list_items cd__chapters_list_level_<?= $level++; ?>">
                <?php foreach ($terms as $term): ?>
                    <li data-chapter_id="<?= $term->term_id; ?>" class="cd__chapters_list_item">
                        <label class="cd__chapters_list_item_label">
                            <input class="cd__chapters_list_item_input" type="checkbox"
                                   data-chapter_id="<?= $term->term_id; ?>"
                                   data-parent_id="<?= $parent; ?>"
                                   data-root_course_id="<?= $root_course_id ?>">
                            <span><?= $term->name; ?></span>
                        </label>
                        <?php
                            $term_id = $term->term_id;
                            if($term_id){
                                renderTerms($term_id, $root_course_id, $level);
                            }
                        ?>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>


    <?php
    }
    ?>


    <?php renderTerms($data, $data); ?>

    <div>
        <button data-course_id="<?= $data; ?>" data-action="submit_chosen_chapters" class="cd__modal_toggler">Выбрать</button>
    </div>
</div>

    <?php

}