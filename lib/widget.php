<?php



/**
 * Categories widget class
 *
 * @since 2.8.0
 */
class JAM_Widget_Taxonomy extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'classname' => 'jam_widget_taxonomy', 'description' => 'Display a list of taxonomy with/without image' );
		parent::__construct('taxonomy', 'Jam Taxonomy List', $widget_ops);
	}

	/**
	 * @staticvar bool $first_dropdown
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		static $first_dropdown = true;

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'Taxonomy' : $instance['title'], $instance, $this->id_base );
		$cat_include = ( $instance['cat_include'] ) ? $instance['cat_include'] : '';
		$cat_order = ( $instance['cat_order'] ) ? $instance['cat_order'] : '';
		$jam_taxonomy = ( $instance['jam_taxonomy'] ) ? $instance['jam_taxonomy'] : 'category';

		$img = ! empty( $instance['display_image'] ) ? '1' : '0';
		$cat_excerpt = ! empty( $instance['cat_excerpt'] ) ? '1' : '0';

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array(
			'orderby'      => $cat_order,
			'include' => $cat_include,
			'hide_empty' => 0,
			'show_option_none' => 'No taxonomy',
			'taxonomy' => $jam_taxonomy,
			'use_desc_for_title' => '',
			'title_li' => ''
		);

?>
		<ul class="jam_taxonomy_list">
<?php

		/**
		 * Filter the arguments for the Categories widget.
		 *
		 * @since 2.8.0
		 *
		 * @param array $cat_args An array of Categories widget options.
		 */
			$data = get_categories( $cat_args );
			foreach ( $data as $k ) {
				$imgr = get_taxonomy_image($k->term_id);
				$imgl = '';
				$showexcerpt = '';
				$excerpt = $k->description;
				if ( $imgr && $img )
					$imgl = '<div class="cat_thumb"><img src="'. $imgr .'" alt="'. $k->name .'" /></div>';
				if ( $excerpt && $cat_excerpt )
					$showexcerpt = '<div class="cat_excerpt">'. $excerpt . '</div>';
				echo '<li>'. $imgl .'<div class="cat_link"><a href="'.get_term_link( $k ).'">'. $k->name .'</a></div>'. $showexcerpt .'</li>';
			}
?>
		</ul>
<?php

		echo $args['after_widget'];
	}

	/**
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['cat_include'] = strip_tags($new_instance['cat_include']);
		$instance['display_image'] = !empty($new_instance['display_image']) ? 1 : 0;
		$instance['cat_excerpt'] = !empty($new_instance['cat_excerpt']) ? 1 : 0;
		$instance['cat_order'] = !empty($new_instance['cat_order']) ? $new_instance['cat_order'] : 0;
		$instance['jam_taxonomy'] = !empty($new_instance['jam_taxonomy']) ? $new_instance['jam_taxonomy'] : 'category';
		return $instance;
	}

	/**
	 * @param array $instance
	 */
	public function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
		$display_image = isset( $instance['display_image'] ) ? (bool) $instance['display_image'] : false;
		$cat_excerpt = isset( $instance['cat_excerpt'] ) ? (bool) $instance['cat_excerpt'] : false;
		$cat_include = isset( $instance['cat_include'] ) ? $instance['cat_include'] : '';
		$cat_order = isset( $instance['cat_order'] ) ? $instance['cat_order'] : '0';
		$jam_taxonomy = isset( $instance['jam_taxonomy'] ) ? $instance['jam_taxonomy'] : 'category';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		
		<p>
			<label for="<?php echo $this->get_field_id('jam_taxonomy'); ?>">Taxonomy:</label>
			<select id="<?php echo $this->get_field_id('jam_taxonomy'); ?>" name="<?php echo $this->get_field_name('jam_taxonomy'); ?>">
				<?php
				$args = array(
				  'public'   => true,
				  'rewrite' => true,
				  'show_ui' => true
				);
				$taxonomies = get_taxonomies($args, 'objects');
				foreach ( $taxonomies as $taxonomy ) {
					$checked = '';
					if ( $taxonomy->name == $jam_taxonomy )
						$checked = ' selected=""';
					echo '
						<option '. $checked .' value="'.$taxonomy->name.'">'. $taxonomy->labels->name .'</option>
					';
				}
				?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cat_include'); ?>">Include (Taxonomy ID):</label>
			<input class="widefat" placeholder="Default: ALL" id="<?php echo $this->get_field_id('cat_include'); ?>" name="<?php echo $this->get_field_name('cat_include'); ?>" type="text" value="<?php echo $cat_include; ?>" />
			<br>
			<span style="font-size:10px;color: #666">Include taxonomy that you love to display, separate by comma (,) </span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('cat_order'); ?>">Order:</label>
			<select id="<?php echo $this->get_field_id('cat_order'); ?>" name="<?php echo $this->get_field_name('cat_order'); ?>">
				<option value="0" <?php selected( 0, $cat_order ) ?>>Default</option>
				<option value="ID" <?php selected( 'ID', $cat_order ) ?>>By ID</option>
				<option value="count" <?php selected( 'count', $cat_order ) ?>>By numberPost</option>
				<option value="name" <?php selected( 'name', $cat_order ) ?>>By Name</option>
			</select>
		</p>
		
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('cat_excerpt'); ?>" name="<?php echo $this->get_field_name('cat_excerpt'); ?>"<?php checked( $cat_excerpt ); ?> />
		<label for="<?php echo $this->get_field_id('cat_excerpt'); ?>">Show description ?</label><br />
		
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('display_image'); ?>" name="<?php echo $this->get_field_name('display_image'); ?>"<?php checked( $display_image ); ?> />
		<label for="<?php echo $this->get_field_id('display_image'); ?>">Show image ?</label></p>
<?php
	}

}


// Register and load the widget
function jamviet_load_widget_category_image() {
	#unregister_widget('WP_Widget_Categories');
	register_widget( 'JAM_Widget_Taxonomy' );
}
add_action( 'widgets_init', 'jamviet_load_widget_category_image' );
