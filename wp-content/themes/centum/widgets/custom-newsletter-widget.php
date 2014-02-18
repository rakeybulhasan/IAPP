<?php
add_action( 'widgets_init', 'custom_newsletter_widget' );
function custom_newsletter_widget() { return register_widget('custom_newsletter_widgets'); }


class custom_newsletter_widgets extends WP_Widget {

    /** constructor */
    function custom_newsletter_widgets() {
          $widget_ops = array('classname' => 'widget_text','description' => __( 'Custom Newsletter Widget') );
        parent::WP_Widget( 'custom_newsletter_widgets', $name = 'Custom Newsletter Widget', $widget_ops );
    }
    function widget($args, $instance) {
        extract($args);

        ?>
            <script language="javascript" type="text/javascript" src="<?php echo emailnews_plugin_url('widget/widget.js'); ?>"></script>
            <link rel="stylesheet" media="screen" type="text/css" href="<?php echo emailnews_plugin_url('widget/widget.css'); ?>" />
            <div>
                <?php
                extract($args);
                if ($instance['icl_language'] != 'multilingual' && $instance['icl_language'] != ICL_LANGUAGE_CODE) {
                    return '';
                } else if ($instance['icl_language'] == 'multilingual' && function_exists('icl_t')) {
                    // Get translations
                    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                    remove_filter('widget_text', 'icl_sw_filters_widget_text');
                    $text = apply_filters('widget_text', icl_t('Widgets', 'widget body - ' . $this->id, $instance['text']), $instance);
                    add_filter('widget_text', 'icl_sw_filters_widget_text');
                } else {
                    remove_filter('widget_title', 'icl_sw_filters_widget_title');
                    remove_filter('widget_text', 'icl_sw_filters_widget_text');
                    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
                    $text = apply_filters('widget_text', $instance['text'], $instance);
                    add_filter('widget_title', 'icl_sw_filters_widget_title');
                    add_filter('widget_text', 'icl_sw_filters_widget_text');
                }
                echo $before_widget;
                if (!empty($title)) {
                    echo $before_title . $title . $after_title;
                }
                ?>
                <div class="eemail_caption">
                    <div class="textwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
                </div>
                <div class="eemail_msg">
                    <span id="eemail_msg"></span>
                </div>
                <div class="eemail_textbox">
                    <input class="eemail_textbox_class" name="eemail_txt_email" id="eemail_txt_email" onkeypress="if(event.keyCode==13) eemail_submit_ajax('<?php echo emailnews_plugin_url('widget'); ?>')" onblur="if(this.value=='') this.value='<?php _e('Enter Email','text_domain');?>';" onfocus="if(this.value=='<?php _e('Enter Email','text_domain');?>') this.value='';" placeholder="<?php _e('Enter Email','text_domain');?>" value="" maxlength="150" type="text">
                </div>
                <div class="eemail_button">
                    <input class="eemail_textbox_button" name="eemail_txt_Button" id="eemail_txt_Button" onClick="return eemail_submit_ajax('<?php echo emailnews_plugin_url('widget'); ?>')" value="<?php _e('Submit','text_domain'); ?>" type="button">
                </div>
            </div>

  <?php
        echo $after_widget;
    }



    function update($new_instance, $old_instance)
    {
        global $wpdb;
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        if (current_user_can('unfiltered_html'))
            $instance['text'] = $new_instance['text'];
        else
            $instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text']))); // wp_filter_post_kses() expects slashed
        $instance['filter'] = isset($new_instance['filter']);

        if ($new_instance['icl_language'] == 'multilingual') {
            $string = $wpdb->get_row($wpdb->prepare("SELECT id, value, status FROM {$wpdb->prefix}icl_strings WHERE context=%s AND name=%s", 'Widgets', 'widget body - ' . $this->id));
            if ($string) {
                icl_st_update_string_actions('Widgets', 'widget body - ' . $this->id, $old_instance['text'], $instance['text']);
            } else {
                icl_register_string('Widgets', 'widget body - ' . $this->id, $instance['text']);
            }
        }
        $instance['icl_language'] = $new_instance['icl_language'];
        return $instance;
    }


    function form($instance) {
        $instance = wp_parse_args((array) $instance, array(
            'title' => '',
            'text' => '',
            'icl_language' => 'multilingual',
            'icl_converted_from' => -1));
        $title = strip_tags($instance['title']);
        $text = esc_textarea($instance['text']);
        $language = $instance['icl_language'];

        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

        <p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
        <?php
        icl_widget_text_language_selectbox($language, $this->get_field_name('icl_language'));
    }

}

?>