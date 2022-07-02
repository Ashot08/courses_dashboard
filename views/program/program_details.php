<?php
function program_details($data){
    ?>
<?php if($data): ?>

<!--    <div class="cd__programs_list">-->
        <?php
        $terms_array = [];
        $terms_ids = [];
        $term_by_parents = [];

        ?>

        <?php foreach ($data as $course): ?>
            <?php $term = get_term( $course->course_id, 'wpm-category' );?>
            <?php
            if($course->course_id){
                $terms_array[$course->course_id] = $term;

                $terms_ids[] = $course->course_id;
            }
            ?>
        <?php endforeach; ?>


    <?php
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
        'parent'         => '',
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
    $categories = get_terms('wpm-category', $args);
    $categoryHierarchy = array();
    sort_terms_hierarchically($categories, $categoryHierarchy);

    function removeMissedTerms(&$termsArray, $terms_ids){
        foreach ($termsArray as $term){
            if($term->children){
                removeMissedTerms($term->children, $terms_ids);
            }else{
                if(!in_array($term->term_id, $terms_ids)){
                    unset($termsArray[$term->term_id]);
                }
            }
            if(empty($term->children) && !in_array($term->term_id, $terms_ids)){
                unset($termsArray[$term->term_id]);
            }
        }
    }


    removeMissedTerms($categoryHierarchy, $terms_ids);


    ?>

<!--        --><?php //foreach ($terms_array as $term): ?>
<!--            --><?php //if($term): ?>
<!--                --><?php
//                $image_id = get_term_meta( $term->term_id, '_thumbnail_id', 1 );
//                $image_url = wp_get_attachment_image_url( $image_id, 'full' );
//                $empty_image = '/wp-content/plugins/courses_dashboard_2/views/program/images/course.jpg';
//                ?>
<!--                <div class="cd__programs_item">-->
<!--                    <img src="--><?php //echo $image_url ?:  $empty_image; ?><!--" alt="">-->
<!--                    <div class="cd__programs_item_title">--><?//= $term->name ?><!--</div>-->
<!--                    <a href="--><?//= get_term_link($term->term_id); ?><!--"><button>Перейти</button></a>-->
<!--                </div>-->
<!---->
<!--            --><?php //endif; ?>
<!--        --><?php //endforeach; ?>


<!---->
<!--    </div>-->
    <div class="cd__program_hierarchy_list">
        <?php
        renderProgramDetails($categoryHierarchy, $terms_ids);
        ?>
    </div>
        <script>
            jQuery('.cd__program_hierarchy_list > ul').treeview({
                collapsed: false,
                animated: 'medium',
                unique: false
            });


        </script>

<?php endif; ?>
    <?php

}
function renderProgramDetails($terms, $terms_ids){
    if($terms):?>

    <ul>
        <?php foreach ($terms as $term): ?>
            <?php
                $is_open = in_array($term->term_id, $terms_ids);
                $has_children = $term->children;
            ?>
            <li class="<?php echo $is_open ? 'cd__list_item_open' : 'cd__list_item_not_open'; ?>
                       <?php echo $has_children ? 'cd__list_has_children' : 'cd__list_has_not_children';?>
                    ">

                <?php if($is_open): ?>

                    <div class="cd__program_hierarchy_list_item_wrapper">
                        <a href="<?= get_term_link($term->term_id); ?>">
                            <span><?= $term->name ?></span>
                        </a>
                    </div>

                <?php else: ?>

                    <div class="cd__program_hierarchy_list_item_wrapper">
                        <span><?= $term->name ?></span>
                        <svg style="width:14px;height:14px" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M12,17C10.89,17 10,16.1 10,15C10,13.89 10.89,13 12,13A2,2 0 0,1 14,15A2,2 0 0,1 12,17M18,20V10H6V20H18M18,8A2,2 0 0,1 20,10V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V10C4,8.89 4.89,8 6,8H7V6A5,5 0 0,1 12,1A5,5 0 0,1 17,6V8H18M12,3A3,3 0 0,0 9,6V8H15V6A3,3 0 0,0 12,3Z" />
                        </svg>
                    </div>

                <?php endif; ?>
                <?php if($has_children): ?>
                    <?php renderProgramDetails($term->children, $terms_ids) ?>
                <?php endif; ?>
            </li>
        <?php endforeach;?>
    </ul>

    <?php endif;
}
function sort_terms_hierarchically(Array &$cats, Array &$into, $parentId = 0)
{
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parentId) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $topCat) {
        $topCat->children = array();
        sort_terms_hierarchically($cats, $topCat->children, $topCat->term_id);
    }
}


