<?php
/**
 * Plugin Name: Stylish Popular Posts
 * Plugin URI: https://github.com/dolatabadi/WordPress-Plugins/tree/master/stylish-popular-posts
 * Description: This plugin creates a widget which shows top popular posts based on the number of comments. You can specify the number of posts to display.
 * Version: 1.1
 * Author: Dolatabadi
 * Author URI: https://github.com/dolatabadi
 * License: GNU General Public License v2
*/
 
/**
 * Loading language files.
 */
function stylish_popular_posts_init() {
  load_plugin_textdomain( 'stylish-popular-posts', false, basename( dirname( __FILE__ ) ) . '/languages');
}
add_action('init', 'stylish_popular_posts_init');

/**
 * Loading custom style for the widget.
 */
function stylish_popular_posts_style() {
		wp_register_style('stylish_popular_posts_style', plugins_url('/css/style.css',__FILE__ ));
		wp_enqueue_style('stylish_popular_posts_style');
}
add_action( 'init','stylish_popular_posts_style');

/**
 * Set custom thumbnail sizes
*/
function stylish_popular_posts_thumbnail() {
	add_theme_support( 'post-thumbnails' );
    add_image_size( 'popular_posts_img', 600, 360, true );
}
add_action( 'init', 'stylish_popular_posts_thumbnail' );

class stylish_popular_posts extends WP_Widget {

	/**
	 * Setup the widget
	 */
	public function __construct() {
		parent::__construct(
			'stylish_popular_posts',
			__('Stylish popular posts', 'stylish-popular-posts'),
			array( 'description' => __( 'Displays most popular posts based on the number of comments', 'stylish-popular-posts' ), )
		);
	}

	/**
	 * Display the widget
	 */
	function widget( $args, $instance ) {
		extract( $args );
		
		/* Variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$number = $instance['number'];
		$checkbox = $instance['checkbox'];
		$top_popular_posts = new WP_Query('showposts=' . $number . '&orderby=comment_count&order=DESC');
		if ($top_popular_posts->have_posts()) :
		
		echo $before_widget;

		/* Display the widget title. */
		if ( $title )
			echo $before_title . $title . $after_title;
		?>
			<?php
				$num = 0;
				sprintf('%02d',$num);
			?>
			<?php  while ($top_popular_posts->have_posts()) : $top_popular_posts->the_post(); ?>
				<?php $num++; $current_num = sprintf('%02d',$num); ?>
				<div class="stylish-popular-widget">
					<a href="<?php echo get_permalink() ?>" rel="bookmark"><?php the_post_thumbnail('popular_posts_img'); ?></a>
					<div class="meta-text">
						<h3><a href="<?php echo get_permalink() ?>"><?php the_title(); ?></a></h3>
						<span class="date"><?php _e('Posted on ', 'stylish-popular-posts'); ?><?php the_time( get_option('date_format') ); ?></span>
					</div>
					<?php if( $checkbox == '1' ) {
						echo '<span class="popular-number">'.$current_num.'</span>' ;
					}?>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_query(); ?>
			<?php endif; ?>
		<?php
		echo $after_widget;
	}
	/**
	 * Update the widget settings
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = strip_tags( $new_instance['number'] );
		$instance['checkbox'] = strip_tags($new_instance['checkbox']);
		return $instance;
	}
	
	function form( $instance ) {
		/* Set up default widget settings. */
		// Check values
		if( $instance) {
			$title = esc_attr($instance['title']);
			$number = esc_attr($instance['number']);
			$checkbox = esc_attr($instance['checkbox']);
		} else {
			$title = 'Popular Posts';
			$number = '3';
			$checkbox = '1';
		}?>
		<!-- widget title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'stylish-popular-posts'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>"  />
		</p>
		<!-- number of posts to show -->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Number of posts to show:', 'stylish-popular-posts'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $number; ?>" size="3" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('checkbox'); ?>"><?php _e('Display post number', 'stylish-popular-posts'); ?></label>
			<input id="<?php echo $this->get_field_id('checkbox'); ?>" name="<?php echo $this->get_field_name('checkbox'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?> />
		</p>
	<?php
	}
}
// widget registration
add_action('widgets_init', create_function('', 'return register_widget("stylish_popular_posts");'));
?>