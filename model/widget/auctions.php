<?php

class Gjmaa_Allegro_Widget extends WP_Widget {

	// constructor
	function __construct() {
		parent::__construct(false, $name = __('Allegro Widget', 'gj_myauctions_allegro') );
	}

	// widget form creation
	function form($instance) {
		$modelAuctions = new GjmaaMyAuctionsAllegro();
        $gjSettingsModel = new GjmaaSettings();
        $gjSettings = $gjSettingsModel->getSettings();
		$importFields = new GjmaaMyFieldsImportForm(array('my_auctions_allegro'=>$modelAuctions,'gj_settings'=>$gjSettingsModel));
		$fields = new GjmaaMyFieldsSettingsForm(array('my_auctions_allegro'=>$modelAuctions));
		
		if( $instance) {
			 $title = esc_attr($instance['title']);
			 $settings_of_auctions = esc_attr($instance['settings_of_auctions']);
			 $count = esc_attr($instance['count']);
			 $show_price = esc_attr($instance['show_price']);
			 $show_time = esc_attr($instance['show_time']);
		} else {
			 $title = '';
			 $settings_of_auctions = null;
			 $count = 5;
			 $show_price = 0;
			 $show_time = 0;
		}
		
		echo '<p>'
			.'<label for="'.$this->get_field_id('settings_of_auctions').'">'.__('Settings of auctions', 'gj_myauctions_allegro').':</label>'
			.$importFields->getImportSelect($settings_of_auctions,$this->get_field_id('settings_of_auctions'),$this->get_field_name('settings_of_auctions'),'widefat')
			.'</p>'
			.'<p>'
			.'<label for="'.$this->get_field_id('title').'">'.__('Title').':</label>'
			.'<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />'
			.'</p>'
			.'<p>'
			.'<label for="'.$this->get_field_id('count').'">'.__('Count of auctions', 'gj_myauctions_allegro').':</label>'
			.$fields->generateNumberInput($this->get_field_name('count'),false,$count,'widefat',1,10)
			.'</p>'
			.'<p>'
			.'<label for="'.$this->get_field_id('show_price').'">'.__('Show price', 'gj_myauctions_allegro').':</label>'
			.$fields->generateSelect($this->get_field_name('show_price'),$modelAuctions->booleanFields(),false,$show_price,'widefat')
			.'</p>'
			.'<p>'
			.'<label for="'.$this->get_field_id('show_time').'">'.__('Show time', 'gj_myauctions_allegro').':</label>'
			.$fields->generateSelect($this->get_field_name('show_time'),$modelAuctions->booleanFields(),false,$show_time,'widefat')
			.'</p>'
            .'<p>'
            .'<label for="'.$this->get_field_id('show_details').'">'.__('Show details', 'gj_myauctions_allegro').':</label>'
            .$fields->generateSelect($this->get_field_name('show_details'),$modelAuctions->booleanFields(),false,$show_time,'widefat')
            .'</p>';
			
	}

	// widget update
	function update($new_instance, $old_instance) {
        $gjSettingsModel = new GjmaaSettings();
        $gjSettings = $gjSettingsModel->getSettings();
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['settings_of_auctions'] = strip_tags($new_instance['settings_of_auctions']);
		$instance['count'] = $new_instance['count'];
		$instance['show_price'] = strip_tags($new_instance['show_price']);
		$instance['show_time'] = strip_tags($new_instance['show_time']);
		return $instance;
	}

	// widget display
	function widget($args, $instance) {
        $gjSettingsModel = new GjmaaSettings();
        $gjSettings = $gjSettingsModel->getSettings();
        $api_allegro = new GjmaaAllegroWebApi($gjSettings['allegro_site'],(isset($gjSettings['allegro_api']) ? $gjSettings['allegro_api'] : null),$gjSettings['allegro_username'],$gjSettings['allegro_password']);
		extract( $args );
		extract($instance);
        $style = 'allegro-widget-style';
        if( ( ! wp_style_is( $style, 'queue' ) ) && ( ! wp_style_is( $style, 'done' ) ) )
            wp_enqueue_style( $style, GJMAA_URL . 'css/allegro-widget.css' );
		$title = apply_filters('widget_title', $instance['title']);
		$modelAuctions = new GjmaaMyAuctionsAllegro();
		echo $before_widget;
		if ($title){
			echo $before_title . $title . $after_title;
		}
		$attributes = array(
			'id' => $settings_of_auctions,
			'count' => $count,
			'show_price' => $show_price,
			'show_time' => $show_time
		);
		echo $modelAuctions->showAuctionsFromSettings($attributes,'widget',$api_allegro);
		echo $after_widget;
	}
	
	
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("Gjmaa_Allegro_Widget");'));