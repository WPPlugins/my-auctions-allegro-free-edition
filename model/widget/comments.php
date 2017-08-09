<?php
class Gjmaa_Comment_Allegro_Widget extends WP_Widget {

    public function __construct(){
        parent::__construct(false, $name = __('Allegro Comments Widget', 'gj_myauctions_allegro') );
    }

    // widget form creation
    function form($instance) {
        $modelAuctions = new GjmaaMyAuctionsAllegro();
        $fields = new GjmaaMyFieldsSettingsForm(array('my_auctions_allegro'=>$modelAuctions));

        if( $instance) {
            $title = esc_attr($instance['title']);
            $user = esc_attr($instance['user']);
            $count = esc_attr($instance['count']);
        } else {
            $title = '';
            $user = null;
            $count = 5;
        }

        echo '<p>'
            .'<label for="'.$this->get_field_id('user').'">'.__('User','gj_myauctions_allegro').':</label>'
            .'<input class="widefat" id="'.$this->get_field_id('user').'" name="'.$this->get_field_name('user').'" type="text" value="'.$user.'" />'
            .'</p>'
            .'<p>'
            .'<label for="'.$this->get_field_id('title').'">'.__('Title','gj_myauctions_allegro').':</label>'
            .'<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />'
            .'</p>'
            .'<p>'
            .'<label for="'.$this->get_field_id('count').'">'.__('Count of comments', 'gj_myauctions_allegro').':</label>'
            .$fields->generateNumberInput($this->get_field_name('count'),false,$count,'widefat',1,25)
            .'</p>';

    }

    // widget update
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        // Fields
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['user'] = strip_tags($new_instance['user']);
        $instance['count'] = strip_tags($new_instance['count']);

        $instance['count'] = $instance['count'] < 10 ? $instance['count'] : 10;

        return $instance;
    }

    // widget display
    function widget($args, $instance) {
        $gjSettingsModel = new GjmaaSettings();
        $gjSettings = $gjSettingsModel->getSettings();

        $feedbackModel = new GjmaaFeedbackItem();

        $api_allegro = new GjmaaAllegroWebApi($gjSettings['allegro_site'],(isset($gjSettings['allegro_api']) ? $gjSettings['allegro_api'] : null),$gjSettings['allegro_username'],$gjSettings['allegro_password']);
        extract( $args );
        $user_id = $api_allegro->getUserID($instance['user']);
        $count_of_auctions = $instance['count'] <= 10 ? $instance['count'] : 10;
        $style = 'allegro-widget-style';
        if( ( ! wp_style_is( $style, 'queue' ) ) && ( ! wp_style_is( $style, 'done' ) ) )
            wp_enqueue_style( $style, GJMAA_URL . 'css/allegro-widget.css' );

        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if ($title){
            echo $before_title . $title . $after_title;
        }
        echo $feedbackModel->addCommentSection($api_allegro,$user_id,$count_of_auctions);
        echo $after_widget;
//        die('asdadsda');
    }
}

add_action('widgets_init', create_function('', 'return register_widget("Gjmaa_Comment_Allegro_Widget");'));