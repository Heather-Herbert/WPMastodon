<?php
/**
    Plugin Name: WPMastodon
    Description: A Mastodon plugin
    Author: Heather Herbert
 */

class My_Widget_Avatar extends WP_Widget
{
    function My_Widget_Avatar()
    {
        $widget_ops = array( 'classname' => 'widget_avatar', 'description' => __("My Avatar Widget") );
        $this->WP_Widget('my_avatar', __('My Avatar'), $widget_ops);
    }

    function widget( $args, $instance )
    {
        extract($args);
        $text = apply_filters('widget_text', $instance['text'], $instance);
        echo $before_widget;
        ?>
        <div class="textwidget">
            <div class="avatar">
                <div class="avatartext">
                    <p><?php echo $text; ?></p>
                </div>
            </div>
            <div class="avatarimage"></div>
        </div>
        <?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;
        if (current_user_can('unfiltered_html') ) {
            $instance['text'] =  $new_instance['text'];
        } else {
            $instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text']))); // wp_filter_post_kses() expects slashed
        }
        return $instance;
    }

    function form( $instance )
    {
        $instance = wp_parse_args((array) $instance, array( 'text' => '' ));
        $text = format_to_edit($instance['text']);
        ?>

        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        <?php
    }
}


function lbi_widgets_init()
{
    register_widget('My_Widget_Avatar');
}
add_action('widgets_init', 'lbi_widgets_init');
