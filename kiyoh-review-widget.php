<?php

/*
Plugin Name: Kiyoh Review Widget
Plugin URI: https://www.wordpressassist.nl/
Description: Vertoon de score van de website of webshop in Kiyoh en gebruik hiervoor de Schema.org opmaak.
Author: Auke Jongbloed
Version: 1.4.0
Author URI: https://www.wordpressassist.nl/

simplexml: https://www.w3schools.com/Php/php_xml_simplexml_read.asp
tutorial: https://premium.wpmudev.org/blog/how-to-build-wordpress-widgets-like-a-pro/
example xml: https://kiyoh.nl/xml/recent_company_reviews.xml?connectorcode=6352f85e7fd3d270d6e&company_id=6036
structured data: https://developers.google.com/search/docs/data-types/products
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
	
add_action( 'widgets_init', function(){
     register_widget( 'WA_Kiyoh_review_widget' );
});	


/**
 * Adds WA_Kiyoh_review_widget widget.
 */
class WA_Kiyoh_review_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		
		parent::__construct(
			'WA_Kiyoh_review_widget', // Base ID
			__('Kiyoh Review Widget', 'wakrw'), // Name
			array( 'description' => __( 'Vertoon de score van de website of webshop in Kiyoh en gebruik hiervoor de Schema.org opmaak.', 'wakrw' ), ) // Args
		);
		
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_review_style' ) );
		
	}
	
	/**
	 * Include CSS file for MyPlugin.
	 */
	function enqueue_review_style() {
	    wp_register_style( 'review-style',  plugin_dir_url( __FILE__ ) . 'assets/review-style.css' );
	    wp_enqueue_style( 'review-style' );
	}
	
	
	
	/**
	 * fetch a random review message.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 * @return string $review
	 */
	public function review_rand_quote( $xml  ) {
		
		$reviews 		= $xml->review_list->review;
		$num_reviews 	= count($reviews);
		
		$company_url 	= $xml->company->url;
		
		$maxnum 	= $num_reviews - 1;	
		$i			= 0;
		$rand_num 	= rand($i, $maxnum);
		$review 	= $xml->review_list->review[$rand_num]->positive;
		$klant 	= $xml->review_list->review[$rand_num]->customer->name;
		$plaats = $xml->review_list->review[$rand_num]->customer->place;
		
		while ($review == "" && $i < 10){
			
			$i++;
			
			$rand_num 	= rand(0, $num_reviews);
			$review = $xml->review_list->review[$rand_num]->positive;
			$klant 	= $xml->review_list->review[$rand_num]->customer->name;
			$plaats = $xml->review_list->review[$rand_num]->customer->place;
			
		}
		
		?>
		<div class="wa-quote">
			<div class="wa-name"><?php if($klant != "") echo $klant . __(' uit ') . $plaats . __(' zegt: ');?></div>
			<div><q><?php echo $review;?></q></div>
			
		</div>
		<?php

	}
	
	/**
	 * Front-end display of widget for on pages.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function page_widget_box( $args, $instance ) {
		
		
		$xml = $this->receiveData( $instance );
		
		$brand 				= 'HMDH';
		$rating_percentage 	= $xml->company->total_score*10;
        $maxrating 			= 10;
		$company_name 		= $xml->company->name;
		$company_url 		= $xml->company->url;
		$rating 			= $xml->company->total_score;
		$total_reviews 		= $xml->company->total_reviews;
		$upload_dir 		= wp_upload_dir();
		
     	echo $args['before_widget'];
     	
     	if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		?>
		
		<div class="wa-pa-kiyoh-shop-snippets">
				<div class="wa-pa-rating-box">
					<div class="wa-pa-rating" style="width:<?php echo $rating_percentage;?>%"></div>
                </div>
				<?php echo $this->review_rand_quote( $xml );?>
				<div class="wa-pa-logo-box">
                    <div class="wa-pa-kiyoh-logo"><a href="<?php echo $company_url;?>" target="_blank"><img src="https://kiyoh.nl/img/logo/124x67.png" style="border:0px" /></a></div>
                </div>
                
                <div class="wa-pa-kiyoh-schema" itemscope="itemscope" itemtype="https://schema.org/WebPage">
                    <div itemprop="aggregateRating" itemscope="itemscope" itemtype="https://schema.org/AggregateRating">
                        <meta itemprop="bestRating" content="<?php echo $maxrating;?>">
                        <p><span itemprop="ratingCount"><?php echo $total_reviews;?> <?php echo __('klanten waarderen ons gemiddeld met een')?></span> <span class="wa-pa-rating-size" itemprop="ratingValue"><?php echo $rating;?></span><?php echo "/" .$maxrating?><br />
                        </p>
                    </div>
                </div>
                <div class="wa-pa-more"><a href="<?php echo $company_url;?>" target="_blank" class="button wa-pa-kiyoh-link"><?php echo __('lees meer ...');?></a></div>
            </div>
		
		<?php
		
		echo $args['after_widget'];
	}
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function large_widget_box( $args, $instance ) {
		
		
		$xml = $this->receiveData( $instance );
		
		$brand 				= 'HMDH';
		$rating_percentage 	= $xml->company->total_score*10;
        $maxrating 			= 10;
		$company_name 		= $xml->company->name;
		$company_url 		= $xml->company->url;
		$rating 			= $xml->company->total_score;
		$total_reviews 		= $xml->company->total_reviews;
		$upload_dir 		= wp_upload_dir();
		
     	echo $args['before_widget'];
     	
     	if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		?>
		
		<div class="wa-kiyoh-shop-snippets">
				<div class="wa-rating-box">
					<div class="wa-rating" style="width:<?php echo $rating_percentage;?>%"></div>
                </div>
				<?php echo $this->review_rand_quote( $xml );?>
				<div class="wa-logo-box">
                    <div class="wa-kiyoh-logo"><a href="<?php echo $company_url;?>" target="_blank"><img src="https://kiyoh.nl/img/logo/124x67.png" style="border:0px" /></a></div>
                </div>
                
                <div class="wa-kiyoh-schema" itemscope="itemscope" itemtype="https://schema.org/WebPage">
                    <div itemprop="aggregateRating" itemscope="itemscope" itemtype="https://schema.org/AggregateRating">
                        <meta itemprop="bestRating" content="<?php echo $maxrating;?>">
                        <p><span itemprop="ratingCount"><?php echo $total_reviews;?> <?php echo __('klanten waarderen ons gemiddeld met een')?></span> <span class="wa-rating-size" itemprop="ratingValue"><?php echo $rating;?></span><?php echo "/" .$maxrating?><br />
                        </p>
                    </div>
                </div>
                <div class="wa-more"><a href="<?php echo $company_url;?>" target="_blank" class="wa-kiyoh-link"><?php echo __('lees meer ...');?></a></div>
            </div>
            <style>
                

            </style>
		
		<?php
		
		echo $args['after_widget'];
	}

	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function small_widget_box( $args, $instance ) {
		
		
		$xml = $this->receiveData( $instance );
		
		$brand 				= 'HMDH';
		$rating_percentage 	= $xml->company->total_score*10;
        $maxrating 			= 10;
		$company_name 		= $xml->company->name;
		$company_url 		= $xml->company->url;
		$rating 			= $xml->company->total_score;
		$total_reviews 		= $xml->company->total_reviews;
		$upload_dir 		= wp_upload_dir();
		
     	echo $args['before_widget'];
     	
     	if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		?>
		
		<div class="wa-sm-kiyoh-shop-snippets">
				<div class="-smwa-kiyoh-schema" itemscope="itemscope" itemtype="https://schema.org/WebPage">
                    <div itemprop="aggregateRating" itemscope="itemscope" itemtype="https://schema.org/AggregateRating">
                        <meta itemprop="bestRating" content="<?php echo $maxrating;?>">
                        <span itemprop="ratingCount"> <?php echo __('Kiyoh')?></span> <span itemprop="ratingValue"><?php echo $rating;?></span><?php echo "/" .$maxrating?>                    </div>
                    <div class="wa-sm-rating-box">
						<div class="wa-sm-rating" style="width:<?php echo $rating_percentage;?>%"></div>
                	</div>
                </div>
                <div class="wa-sm-more"><?php echo __('klik')?> <a href="<?php echo $company_url;?>" target="_blank" class="wa-sm-kiyoh-link"><?php echo __('hier')?></a> <?php echo __('om de reviews te bekijken')?></div>
            </div>
            <style>
                

            </style>
		
		<?php
		
		echo $args['after_widget'];
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		
		$title 			= isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : __( 'New title', 'wakrw' );
		$connectorcode 	= isset( $instance[ 'connectorcode' ] ) ? $instance[ 'connectorcode' ] : __( '0000000000000', 'wakrw' );
		$company_id 	= isset( $instance[ 'company_id' ] ) ? $instance[ 'company_id' ] : __( '0000', 'wakrw' );
		$widget_size 	= isset( $instance[ 'widget_size' ] ) ? $instance[ 'widget_size' ] : __( 'large', 'wakrw' );

		
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'connectorcode' ); ?>"><?php _e( 'Connectorcode:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'connectorcode' ); ?>" name="<?php echo $this->get_field_name( 'connectorcode' ); ?>" type="text" value="<?php echo esc_attr( $connectorcode ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'company_id' ); ?>"><?php _e( 'Company id:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'company_id' ); ?>" name="<?php echo $this->get_field_name( 'company_id' ); ?>" type="text" value="<?php echo esc_attr( $company_id ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_size' ); ?>"><?php _e( 'Widget size:' ); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'widget_size' ); ?>" name="<?php echo $this->get_field_name( 'widget_size' ); ?>" >
				<option value="large" <?php if(esc_attr( $widget_size ) == "large") echo"selected"; ?>>large</option>
				<option value="small" <?php if(esc_attr( $widget_size ) == "small") echo"selected"; ?>>small</option>
				<option value="page" <?php if(esc_attr( $widget_size ) == "page") echo"selected"; ?>>page</option>
			</select>
		</p>
		<?php 
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		
		$instance = array();
		$instance['title'] 			= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['connectorcode'] 	= ( ! empty( $new_instance['connectorcode'] ) ) ? strip_tags( $new_instance['connectorcode'] ) : '';
		$instance['company_id'] 	= ( ! empty( $new_instance['company_id'] ) ) ? strip_tags( $new_instance['company_id'] ) : '';
		$instance['widget_size'] 	= ( ! empty( $new_instance['widget_size'] ) ) ? strip_tags( $new_instance['widget_size'] ) : '';


		return $instance;
		
	}
	
	/**
	 * Retrieve the values in the kiyoh xml file.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return xml object.
	 */
    public function receiveData( $instance )
    {
        
        $kiyoh_server 		= 'kiyoh.nl';
		$kiyoh_connector 	= $instance['connectorcode'];
		$company_id 		= $instance['company_id'];
		
		$xml_file = 'https://www.'.$kiyoh_server.'/xml/recent_company_reviews.xml?connectorcode=' . $kiyoh_connector . '&company_id=' . $company_id;
        
		
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $xml_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $output = curl_exec($ch);
        
        $data = simplexml_load_string($output);
        
        if(curl_errno($ch)){
		    
		    $error = curl_error($ch);
		    curl_close($ch);
		    return $error;
		    
		}else{
			
			curl_close($ch);
			return $data;
			
		}
        
    }
    
    /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		switch ( $instance['widget_size'] ) {
		    case "small":
		        	echo $this->small_widget_box( $args, $instance );
		        break;
		    case "large":
		        	echo $this->large_widget_box( $args, $instance );
		        break;
		    case "page":
		        	echo $this->page_widget_box( $args, $instance );
		        break;
			default:
				echo $this->large_widget_box( $args, $instance );
		}
		
	}
	
} // class WA_Kiyoh_review_widget