<?php
/**
 * Text widget class
 *
 * @since 2.8.0
 */
function Text_Comment()
{
    register_widget('WP_Widget_Text_Comment');

    //do_action('widgets_init');
}

add_action('widgets_init', 'Text_Comment', 1);

class WP_Widget_Text_Comment extends WP_Widget
{

    function __construct()
    {
        $widget_ops = array('classname' => 'text-commentcc', 'description' => __('Text comments for high light'));
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('WP_Widget_Text_Comment', __('Text for comments'), $widget_ops, $control_ops);
    }

    function widget($args, $instance)
    {
        extract($args);
        if ($instance['icl_language'] != 'multilingual' && $instance['icl_language'] != ICL_LANGUAGE_CODE) {
            return '';
        } else if ($instance['icl_language'] == 'multilingual' && function_exists('icl_t')) {
            // Get translations
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            remove_filter('widget_text', 'icl_sw_filters_widget_text');
            $author = apply_filters('widget_text', icl_t('Widgets', 'widget author - ' . $this->id, $instance['author']), $instance);
            $text = apply_filters('widget_text', icl_t('Widgets', 'widget body - ' . $this->id, $instance['text']), $instance);
            add_filter('widget_text', 'icl_sw_filters_widget_text');
        } else {
            remove_filter('widget_title', 'icl_sw_filters_widget_title');
            remove_filter('widget_text', 'icl_sw_filters_widget_text');
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            $author = apply_filters('widget_text', $instance['author'], $instance);
            $text = apply_filters('widget_text', $instance['text'], $instance);
            add_filter('widget_title', 'icl_sw_filters_widget_title');
            add_filter('widget_text', 'icl_sw_filters_widget_text');
        }
        extract($args);
        echo $before_widget . '<blockquote class="example-obtuse">';
        if (!empty($title)) {
            echo $before_title . $title . $after_title;
        } ?>
        <div
            class="textwidget"><?php echo !empty($instance['filter']) ? wpautop($text) : '<p>' . $text . '</p>'; ?></div>
        <?php
        echo '</blockquote><p>'.$author.'</p>' . $after_widget;
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        global $wpdb;
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['author'] = strip_tags($new_instance['author']);
        if (current_user_can('unfiltered_html'))
            $instance['text'] = $new_instance['text'];
        else
            $instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text']))); // wp_filter_post_kses() expects slashed
        $instance['filter'] = isset($new_instance['filter']);
      if (current_user_can('unfiltered_html'))
            $instance['author'] = $new_instance['author'];
        else
            $instance['author'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['author']))); // wp_filter_post_kses() expects slashed
        $instance['filter'] = isset($new_instance['filter']);

        if ($new_instance['icl_language'] == 'multilingual') {
            $string = $wpdb->get_row($wpdb->prepare("SELECT id, value, status FROM {$wpdb->prefix}icl_strings WHERE context=%s AND name=%s", 'Widgets', 'widget body - ' . $this->id));
            if ($string) {
                icl_st_update_string_actions('Widgets', 'widget body - ' . $this->id, $old_instance['text'], $instance['text']);
            } else {
                icl_register_string('Widgets', 'widget body - ' . $this->id, $instance['text']);
            }
        }
        if ($new_instance['icl_language'] == 'multilingual') {
            $string = $wpdb->get_row($wpdb->prepare("SELECT id, value, status FROM {$wpdb->prefix}icl_strings WHERE context=%s AND name=%s", 'Widgets', 'widget author - ' . $this->id));
            if ($string) {
                icl_st_update_string_actions('Widgets', 'widget author - ' . $this->id, $old_instance['author'], $instance['author']);
            } else {
                icl_register_string('Widgets', 'widget author - ' . $this->id, $instance['author']);
            }
        }
        $instance['icl_language'] = $new_instance['icl_language'];
        return $instance;
    }

    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array(
            'title' => '',
            'text' => '',
            'author' => '',
            'icl_language' => 'multilingual',
            'icl_converted_from' => -1));
        $title = strip_tags($instance['title']);
        $text = esc_textarea($instance['text']);
        $author = strip_tags($instance['author']);
        $language = $instance['icl_language'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>
        <p>
            <label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Author:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('author'); ?>"
                   name="<?php echo $this->get_field_name('author'); ?>" type="text"
                   value="<?php echo esc_attr($author); ?>"/></p>

        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>"
                  name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

        <p><input id="<?php echo $this->get_field_id('filter'); ?>"
                  name="<?php echo $this->get_field_name('filter'); ?>"
                  type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label
                for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label>
        </p>
    <?php
        icl_widget_text_language_selectbox($language, $this->get_field_name('icl_language'));
    }
}