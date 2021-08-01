<?php // @codingStandardsIgnoreStart ?>
<?php
  use Wb4WpTheme\Managers\Customize\Customize_Settings;

  $layout    = Customize_Settings::get_setting( 'wb4wp_single_post_section_layout_setting' );
  $author_id = $post->post_author;
?>

<?php $show_author = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_author_setting' ); ?>
<?php $show_author_avatar = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_author_image_setting' ); ?>
<?php $show_pub_date = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_publication_date_setting' ); ?>
<?php $show_social_sharing = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_social_sharing_setting' ); ?>

<?php if ( $layout == 'single-post-2' ) : ?>   
	<hr class="wp-block-separator is-style-wide">
  <?php endif ?>

<div class="wp4wp-header-meta">

  <div class="wb4wp-author">
	<?php
	  $default_wp_image       = 'http://0.gravatar.com/avatar/c205780ea90b76f9475601894813262f?s=52&amp;d=mm&amp;r=g';
	  $avatar                 = the_author_meta( 'avatar', $author_id );
	  $author_profile_picture = ! empty( $avatar ) ? $avatar : $default_wp_image;
	?>
	
	<?php if ( $show_author_avatar ) : ?>
	  <img 
		class="wb4wp-avatar avatar is-round" 
		src="<?php echo $author_profile_picture; ?> "
		width="140"
		height="140"
		alt="profile picture of <?php echo the_author_meta( 'display_name', $author_id ); ?>"
	  >
	<?php endif ?>
	
	<div class="wb4wp-author-detail">
	  <?php if ( $show_author ) : ?>
		<p class="wb4wp-author-name has-small-font-size">
			<?php echo the_author_meta( 'display_name', $author_id ); ?>
		</p>
	  <?php endif ?>

	  <?php if ( $show_pub_date ) : ?>
		<p class="wb4wp-author-post-date">
			<?php echo get_the_date( 'j M, Y', $post ); ?>
		</p>
	  <?php endif ?>
	</div>

  </div>

  <?php if ( $show_social_sharing ) : ?>
	<div class="wb4wp-social">
		<?php
		$social_channels = array(
			'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=%s',
			'twitter'  => 'https://twitter.com/intent/tweet?text=%s',
			'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=%s',
		);
		global $wp;
		$page_url = home_url( add_query_arg( array(), $wp->request ) );
		?>
		<?php
		foreach ( $social_channels as $account => $url ) :
			$share_url = sprintf( $url, $page_url );
			if ( ! empty( $share_url ) ) :
				?>
		<a href="<?php echo $share_url; ?>" class="wb4wp-social-icon fa fa-<?php echo $account; ?>" aria-label="Go to our <?php echo $account; ?> page" target="_blank">
		</a>
		<?php endif; ?>
	  <?php endforeach; ?>
	</div>
  <?php endif ?>
</div>
