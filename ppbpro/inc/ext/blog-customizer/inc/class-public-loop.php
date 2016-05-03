<?php

/**
 * pootle page builder blog customizer public loop class
 */
class pootle_page_builder_blog_customizer_Public_Loop{

	/**
	 * @var string $token Plugin token
	 */
	public $token;

	/**
	 * @var string $url Plugin root dir url
	 */
	public $url;

	/**
	 * @var string $path Plugin root dir path
	 */
	public $path;

	/**
	 * @var string $version Plugin version
	 */
	public $version;

	/**
	 * @var int $id Current post id
	 */
	public $id;


	/**
	 * Runs the loop on $query
	 * @param WP_Query $query
	 * @param array $set
	 * @since 1.0.0
	 */
	protected function loop( $query, $set ) {
		echo "<div id='{$this->loop_css_id}' class='ppb-custom-posts layout-{$set['layout']} {$set['layout']} {$set['text-position']} {$set['feat-img']}'>";

		$this->embed_styles( $set );

		$i = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			$i++;
			$attr = array(
				'id'        => "ppb-post-{$this->id}-{$this->loop_id}-{$i}",
				'class'     => array('ppb-post'),
			);

			echo '<article ' . pootlepb_stringify_attributes( $attr ) . '>';
			$this->show_post( $set );
			echo '</article>';
		}
		echo '</div>';
	}

	/**
	 * Outputs embed styles for loop
	 * @param array $set Settings
	 */
	protected function embed_styles( $set ) {
		$across = $set['across'];
		$half_across = ( $across + ( $across % 2 ) ) / 2;
		$quarter_across = 1;
		if ( 3 < $across ) {
			$quarter_across = ( $across - ( $across % 4 ) ) / 4;
		}
		$gutter = 2.5;
		if ( empty( $set['show-gutters'] ) && 'full-image' == $set['layout'] ) {
			$gutter = 0;
		}
		?>
		<style>
			<?php
			$this->styles_post_width( $across, $half_across, $quarter_across, $gutter );
			$this->styles_post_border( $set );
			$this->styles_rounded_corners( $set );
//			$this->styles_image_width( $set );
			?>
		</style>
	<?php
	}

	private function styles_post_width( $across, $half_across, $quarter_across, $gutter ) {
		?>
		@media screen and (max-width:639px) {
		#<?php echo $this->loop_css_id; ?> .ppb-post {
		width: <?php echo ( 100 - ( $gutter * $quarter_across ) ) / $quarter_across; ?>%;
		}
		#<?php echo $this->loop_css_id; ?> .ppb-post:nth-of-type(<?php echo $quarter_across; ?>n+1) {
		clear: both;
		}
		}

		@media screen and (min-width:640px) and (max-width:999px) {
		#<?php echo $this->loop_css_id; ?> .ppb-post {
		width: <?php echo ( 100 - ( $gutter * $half_across ) ) / $half_across; ?>%;
		}
		#<?php echo $this->loop_css_id; ?> .ppb-post:nth-of-type(<?php echo $half_across; ?>n+1) {
		clear:both;
		}
		}
		@media screen and (min-width:1000px) {
		#<?php echo $this->loop_css_id; ?> .ppb-post {
		width: <?php echo ( 100 - ( $gutter * $across ) ) / $across; ?>%;
		}
		#<?php echo $this->loop_css_id; ?> .ppb-post:nth-of-type(<?php echo $across; ?>n+1) {
		clear:both;
		}
		}
		#<?php echo $this->loop_css_id; ?> {
		margin: <?php echo $gutter/2; ?>% 0;
		}
		#<?php echo $this->loop_css_id; ?> .ppb-post {
		/*margin: <?php echo '0 ' . $gutter . '% ' . $gutter . '% 0;'; ?>;*/
		margin: <?php echo $gutter/2; ?>%;
		}
	<?php
	}

	/**
	 * Outputs styles for post border
	 * @param array $set Post custo settings
	 */
	private function styles_post_border( $set ) {
		if ( ! empty( $set['post-border-width'] ) ) {
			?>
			#<?php echo $this->loop_css_id; ?>.top-image:not(.circle) .ppb-blog-content {
			border-top: none;
			padding:10px;
			}

			#<?php echo $this->loop_css_id; ?>.circle.top-image .ppb-blog-content {
			border: none;
			}

			#<?php echo $this->loop_css_id; ?>.left-image .ppb-post,
			#<?php echo $this->loop_css_id; ?>.right-image .ppb-post,
			#<?php echo $this->loop_css_id; ?>.circle.top-image .ppb-post{
			padding: 10px;
			border: <?php echo $set['post-border-width'] . 'px solid ' . $set['post-border-color']; ?>;
			}

		<?php
		}
	}

	/**
	 * Outputs styles for rounded corners
	 * @param array $set Post custo settings
	 */
	private function styles_rounded_corners( $set ) {
		if ( ! empty( $set['rounded-corners'] ) ) {
			?>
			#<?php echo $this->loop_css_id; ?> .ppb-post{
			overflow: hidden;
			-moz-border-radius: 10px;
			-webkit-border-radius: 10px;
			border-radius: 10px;
			}
			#<?php echo $this->loop_css_id; ?>.top-image .ppb-blog-content {
			border-bottom-left-radius: 10px;
			border-bottom-right-radius: 10px;
			}
			#<?php echo $this->loop_css_id; ?>.top-image .feat-img {
			border-top-left-radius: 10px;
			border-top-right-radius: 10px;
			}
		<?php
		}
	}

	/**
	 * Outputs image width styles
	 * @param array $set Post custo settings
	 * @todo Remove in 1.1 or greater, Not used since 1.0.0
	 */
	private function styles_image_width( $set ) {
		if ( ! empty( $set['img-width'] ) ) {
			?>
			#<?php echo $this->loop_css_id; ?>.layout- header {
			margin-left: <?php echo $set['img-width'] + 3 ?>%;
			}
			#<?php echo $this->loop_css_id; ?>.layout-right-image header {
			margin-right: <?php echo $set['img-width'] + 3 ?>%;
			}
		<?php
		}
	}

	/**
	 * Outputs the post for custom post add on
	 * @param array $set
	 * @since 1.0.0
	 */
	protected function show_post( $set ) {

		$post_meta = array(
			'above-title' => '',
			'below-title' => '',
			'below-excerpt' => '',
		);

		//Featured image
		$img_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large' );
		if ( ! empty( $img_url[0] ) ) {
			echo "<div class='img-container'><a href='" . get_the_permalink() . "'><div class='feat-img' style='background-image:url({$img_url[0]});'></div></a></div>";
		}

		//Sets the post meta to different arrays
		$this->get_post_meta( $post_meta, $set );

		echo '<div class="ppb-blog-content">';

		//Gets the post header
		$this->post_header( $set, $post_meta );

		//Excerpt
		if ( ! empty( $set['show-excerpt'] ) ) {
			echo '<div class="excerpt"><p>' . substr( get_the_excerpt(), 0, 300 ) . '</p></div>';
		}

		//Below excerpt meta area
		echo $post_meta['below-excerpt'];

		echo '</div>';
	}

	/**
	 * Sets the post meta array
	 * @param array $pm Post meta
	 * @param array $set Settings
	 */
	protected function get_post_meta( &$pm, $set ) {
		//Date
		if ( ! empty( $set['show-date'] ) ) {
			$pm[ $set['show-date'] ] .= ' <span class="post-meta posted-on post-date">' .
			                            get_the_date( "F j, Y" ) . '</span>';
		}

		//Author
		if ( ! empty( $set['show-author'] ) ) {
			$pm[ $set['show-author'] ] .= ' <span class="post-meta by-line">By <span class="author">' .
			                              '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '">' . get_the_author() .'</a>' .
			                              '</span></span>';
		}

		//Categories
		if ( ! empty( $set['show-cats'] ) ) {
			$pm[ $set['show-cats'] ] .= ' <span class="post-meta categories">In: ' . get_the_category_list( ', ' ) . '</span>';
		}

		//Comments
		if ( ! empty( $set['show-comments'] ) ) {
			ob_start();
			echo ' <span class="post-meta post-comments comments">';
			comments_popup_link( 'Leave a reply', '1 Comment', '% Comments' );;
			echo '</span>';
			$pm[ $set['show-comments'] ] .= ob_get_contents();
			ob_end_clean();
		}
	}

	/**
	 * Outputs the post for custom post add on
	 * @param array $set
	 * @since 1.0.0
	 */
	protected function post_header( $set, $post_meta ) {

		//Above title meta area
		echo $post_meta['above-title'];

		//Title
		echo '<header><span>';
		echo '<h2 class="title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h2>';
		echo '</span></header>';

		//Below title meta area
		echo $post_meta['below-title'];

	}
}