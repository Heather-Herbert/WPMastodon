<?php
/**
 * Plugin Name: WPMastodon
 * Plugin URI: https://heatherherbert.uk
 * Description: Mastodon tool set.
 * Author: Heather Herbert
 * Version: 0.0.1
 * Author URI: https://heatherherbert.uk
 * License: GPL2+
 * Text Domain: mastodon
 * Requires at least: 6.0
 * Requires PHP: 5.6
 *
 * @category Toolset
 * @package  Wpmastodon
 * @author   Heather Herbert <heather.herbert.1975@gmail.com>
 * @license  GPL2+ https://opensource.org/licenses/gpl-2.0.php
 * @link     https://heatherherbert.uk
 */
class WPMastodonWidget extends WP_Widget
{

    /**
     * Constructor
     */
    function WPMastodonWidget()
    {
        $widget_ops = array( 'classname' => 'widget_avatar', 'description' => __("Mastodon Widget") );
        $this->WP_Widget('my_avatar', __('My Avatar'), $widget_ops);
    }

    /**
     * @param  $args
     * @param  $instance
     * @return void
     */
    function widget($args, $instance )
    {
        extract($args);
        $url = apply_filters('widget_text', $instance['url'], $instance);
        $error = '';
        $id = '';
        $instance = '';

        preg_match("/[0-9]{18}$/", $url, $result);
        if (count($result) != 1) {
            $error = 'Sorry, I can\'t locate this toot';
        } else {
            $id = $result[0];
        }

        $result = parse_url($url);
        if ($result['host'] == '') {
            $error = 'Sorry, I can\'t locate this toot';
        } else {
            $instance = $result['host'];
        }

        echo $before_widget;
        if ($error == '') {
            $url = 'https://' . $instance . '/api/v1/statuses/' . $id;
            $JSON = file_get_contents($url);
            $TootArray = json_decode($JSON, true);
            $MastodonUser = $TootArray['account'];
        ?>
            <div class="wrapper">
                <a class="profile-bg main-wrapper d-block">
                    <img src="<?php echo $MastodonUser['header'] ?>">
                </a>
                <div>
                    <a href="<?php echo $MastodonUser['url'] ?>" id="profile-link">
                        <img src="<?php echo $MastodonUser['avatar']?>" id="profile-img">
                    </a>
                    <div id="profile-marg">
                        <div id="profile-name">
                            <a href="<?php echo $MastodonUser['url'] ?>"><?php echo $MastodonUser['display_name'] ?></a>
                        </div>
                        <small>
                            <a href="<?php echo $MastodonUser['url'] ?>">@<?php echo $instance; ?>@<?php echo $MastodonUser['acct'] ?></a>
                        </small>
                    </div>
                    <div id="profile-state">
                        <ul id="profile-Arrange">
                            <li id="profile-details">
                                <span class="d-block" id="profile-label">Toots</span>
                                <span id="profile-number">
                            <?php echo $MastodonUser['statuses_count'] ?>
                        </span>
                            </li>
                            <li id="profile-details">
                                <span class="d-block" id="profile-label">Following</span>
                                <span id="profile-number">
                            <?php echo $MastodonUser['following_count'] ?>
                        </span>
                            </li>
                            <li id="profile-details">
                                <span class="d-block" id="profile-label">Followers</span>
                                <span id="profile-number">
                            <?php echo $MastodonUser['followers_count'] ?>
                        </span>
                            </li>
                        </ul>
                    </div>
                    <div class="toot">
                        <?php echo $TootArray['content']; ?>
                        <div id="profile-state">
                            <ul id="profile-Arrange">
                                <li id="profile-details">
                                    <span class="d-block" id="profile-label">Replys</span>
                                    <span id="profile-number">
                            <?php echo $TootArray['replies_count'] ?>
                        </span>
                                </li>
                                <li id="profile-details">
                                    <span class="d-block" id="profile-label">Reblogs</span>
                                    <span id="profile-number">
                            <?php echo $TootArray['reblogs_count'] ?>
                        </span>
                                </li>
                                <li id="profile-details">
                                    <span class="d-block" id="profile-label">favourites</span>
                                    <span id="profile-number">
                            <?php echo $TootArray['favourites_count'] ?>
                        </span>
                                </li>
                            </ul>
                            <div class="link2toot"><span><a href="<?php echo $TootArray['url'] ?>">View this toot on Mastodon</a></span></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        else
        {   // Display the error message.
        ?>
        <div class="textwidget">
            <div class="Mastodon">
                <div class="MastodonErrorText">
                    <p><?php echo $error; ?></p>
                </div>
            </div>
        </div>
        <?php
        }
        echo $after_widget;


    }

    /**
     * @param  $new_instance
     * @param  $old_instance
     * @return mixed
     */
    function update($new_instance, $old_instance )
    {
        $instance = $old_instance;
        if (current_user_can('unfiltered_html') ) {
            $instance['url'] =  $new_instance['url'];
        } else {
            $instance['url'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['url'])));
        }
        return $instance;
    }

    /**
     * @param  $instance
     * @return void
     */
    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array( 'url' => '' ));
        $url = format_to_edit($instance['url']);
        ?>

        <textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>"><?php echo $url; ?></textarea>
        <?php
    }
}


/**
 * @return void
 */
function MPMastondon_Widgets_init()
{
    register_widget('WPMastodonWidget');
}
add_action('widgets_init', 'MPMastondon_Widgets_init');
