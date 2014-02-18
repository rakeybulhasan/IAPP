<?php
/**
 * The main template file.
 * @package WordPress
 */

$options = get_option('ecb_theme_options');
$catIds = $options['component_category_template'];
$taCatId = $options['ta_component_category_template'];
if (ICL_LANGUAGE_CODE != 'en') {
    $catIds = get_translated_category_ids($catIds);
}
if (ICL_LANGUAGE_CODE != 'en') {
    $taCatId = get_translated_category_ids($taCatId);
}

$categoryId = get_query_var('cat');

$ancestors = get_ancestors($categoryId, 'category');
$child_component = array_intersect($catIds, $ancestors);

if (in_array($categoryId, $catIds)) {
    if (in_array($categoryId, $taCatId)) {
        get_template_part('child-components-category');
    } else {

        get_template_part('components-category');
    }

} elseif ($child_component) {

    get_template_part('child-components-category');

} else {

    get_template_part('default-category');
}
