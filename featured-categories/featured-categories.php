<?php
/*
Plugin Name: Featured Categories
Plugin URI: http://lifeunconstrained.com/#freebies
Description: A widget to display posts and thumbnails in a featured category. Thumbnails will display 100px square. Author name links to author page (if in use) or list of posts. Posts are sorted in descending order by date. This is a fork of James Lao's Category Posts plugin (http://wordpress.org/extend/plugins/category-posts/), with less options.
Version: 1.0
Author: Danielle Nelson
Author URI: http://lifeunconstrained.com
License: GPL3
*/

class FeaturedCatPosts extends WP_Widget {

function FeaturedCatPosts() {
	parent::WP_Widget(false, $name='Featured Category Posts');
}

// Create the widget
function widget($args, $instance) {
	global $post;
	$post_old = $post;

	extract( $args );

	// Post title
	if( !$instance["title"] ) {
		$category_info = get_category($instance["cat"]);
		$instance["title"] = $category_info->name;
	}

	// Create the query
	$cat_posts = new WP_Query(
		"showposts=" . $instance["num"] . 
		"&cat=" . $instance["cat"] .
		"&orderby=" . 'date' .
		"&order=" . 'DESC'
	);

	// Excerpt
	$new_excerpt_length = create_function('$length', "return " . $instance["excerpt_length"] . ";");
	if ( $instance["excerpt_length"] > 0 )
		add_filter('excerpt_length', $new_excerpt_length);

	// Start the widget output
	echo $before_widget;
	
	// Widget title
	echo $before_title;
	echo '<a href="' . get_category_link($instance["cat"]) . '">' . $instance["title"] . '</a>';
	echo $after_title;

	// Start the list output
	echo "<ul>\n";
	
	while ( $cat_posts->have_posts() ) {
		$cat_posts->the_post();
	?>
		<li class="cat-post-item">
			<div class="thumbcol">
				<?php
					if (
						function_exists('the_post_thumbnail') &&
						has_post_thumbnail()
					) :
				?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
					<?php the_post_thumbnail( array('100,100') ); ?>
					</a>
				<?php endif; ?>
			</div>

			<div class="contentcol">
				<a class="post-title" style="display: block; clear: both; "href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a>

				<?php the_excerpt(); ?> 
			
				<?php if ( $instance['comment_num'] ) : ?>
				<p class="comment-num">(<?php comments_number(); ?>)</p>
				<?php endif; ?>

				<p class="post-date">By <?php the_author_posts_link(); ?> on <?php the_time("F j, Y"); ?> at <?php the_time("g:i a"); ?></p>
			</div>
		</li>
	<?php
	}
	
	echo "</ul>\n";
	
	// End the widget
	echo $after_widget;

	// End the excerpt filter
	remove_filter('excerpt_length', $new_excerpt_length);
	
	// Restore the post object
	$post = $post_old; 
}

// Widget form options
function form($instance) {
?>
		<p>
			<label for="<?php echo $this->get_field_id("title"); ?>">
				<?php _e( 'Title' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
			</label>
		</p>
		
		<p>
			<label>
				<?php _e( 'Category' ); ?>:
				<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("cat"), 'selected' => $instance["cat"] ) ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("num"); ?>">
				<?php _e('Number of posts to show'); ?>:
				<input style="text-align: center;" id="<?php echo $this->get_field_id("num"); ?>" name="<?php echo $this->get_field_name("num"); ?>" type="text" value="<?php echo absint($instance["num"]); ?>" size='3' />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id("excerpt_length"); ?>">
				<?php _e( 'Excerpt length (in words):' ); ?>
			</label>
			<input style="text-align: center;" type="text" id="<?php echo $this->get_field_id("excerpt_length"); ?>" name="<?php echo $this->get_field_name("excerpt_length"); ?>" value="<?php echo $instance["excerpt_length"]; ?>" size="3" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("comment_num"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("comment_num"); ?>" name="<?php echo $this->get_field_name("comment_num"); ?>"<?php checked( (bool) $instance["comment_num"], true ); ?> />
				<?php _e( 'Show number of comments' ); ?>
			</label>
		</p>

<?php

}

}

add_action( 'widgets_init', create_function('', 'return register_widget("FeaturedCatPosts");') );


/*  Copyright 2011 Danielle Nelson  (email : dani@lifeunconstrained.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
