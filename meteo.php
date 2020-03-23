<?php
/*
Plugin Name: meteo plugin
Plugin URI: ###
Description: meteo plug in api openweathermaps
Version: 1.0
Author: Michele Malagnini
Author URI: http://www.michelemalagnini.com
License: GPL2
*/

class Meteo_Widget extends WP_Widget {

    // costruttore del plug in
	public function __construct() {
		parent::__construct(
	 		'wunderground_widget',
			'Meteo Widget',
			array( 'description' => 'A Widget for displaying the current weather using the openweather API' ) 
		);
	}
    
    
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$location = $instance['location'];
		
		wp_register_style('meteo-style', plugins_url('meteo/meteo.css', dirname(__FILE__)));
        wp_enqueue_style('meteo-style');


    	function curl($url) {
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            $data = curl_exec($ch);
            curl_close($ch);

            return $data;
        }
        
        //faccio partire la funzione curl inserendo dentro l api openweather location e la mia apikey generata precedentemente in tutto dentro la variabile urlContents
		$urlContents = curl("http://api.openweathermap.org/data/2.5/weather?q=".$location."&type=accurate&appid=APP_TOKEN downoad at open.weather.map");
		
		// debug
		/*echo $urlContents;*/
		
		//trasformo il json in array
		$weatherArray = json_decode($urlContents, true);
		
		// debug
		/*print_r($weatherArray);*/
		
		
		echo $before_widget;
		?>
		
		<!--creo la parte html che si genera automaticamente in base alla citta inserita in backend -->
		<div class='meteo-widget'>
			<div>	
			<h3 class='widget-title'><?php echo $title ?></h3>	
			<div class="location"><?php echo $location ?></div>
			<p class='widget-title'>
			<?php
			    // carico in una variabile frase informativa sul tempo in generale
			    $weather = "The weather in ".$location." is currently ".$weatherArray['weather'][0]['description'].".";
			    
			    //stampo a video variabile $weather
		        echo $weather;
		        
		    ?>
		    </p>
			<?php 
			// carico in una variabile la temperatura con calcolo per creare i grafi
		        $temp = $weatherArray['main']['temp'] - 273.15;
		        $arrotondo = round($temp);
		
			?>
			
			    
			<div class="temp"><?php echo $arrotondo ?>&deg;C <img src="http://openweathermap.org/img/w/<?php echo  $weatherArray['weather'][0]['icon'] ?>.png" /></div>
				<div class="wind">Wind: <?php echo $weatherArray['wind']['speed']*2.24 ?></div>
			</div>
		</div>
		<?php	
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['location'] = strip_tags( $new_instance['location'] );
		return $instance;
	}
    
    // backend pannello di controllo del widget
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = 'New title';
		}
		
		
		if ( isset( $instance[ 'location' ] ) ) {
			$location = $instance[ 'location' ];
		}
		else {
			$location = '';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Location:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'location' ); ?>" name="<?php echo $this->get_field_name( 'location' ); ?>" type="text" value="<?php echo esc_attr( $location ); ?>" />
		
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

}

function meteo_widgets_init(){
	register_widget( 'Meteo_Widget' );
}
add_action( 'widgets_init', 'meteo_widgets_init' )
