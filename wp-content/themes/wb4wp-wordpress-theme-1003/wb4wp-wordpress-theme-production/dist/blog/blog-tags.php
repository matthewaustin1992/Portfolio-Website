<?php // @codingStandardsIgnoreStart ?>
<?php
  use Wb4WpTheme\Managers\Customize\Customize_Settings;
?>

<?php $show_tags = Customize_Settings::get_setting( 'wb4wp_single_post_section_show_tags_setting' ); ?>

<?php if ( !$show_tags ) : ?>
	<?php $page_id = get_queried_object_id(); ?>
	<?php $tags = get_the_tags($page_id); ?>
	
	<?php if ( $tags ) : ?>
		<div class="tags">
			<?php foreach ( $tags as $tag ) { ?>
			<a href="<?php echo get_tag_link( $tag->term_id ); ?> " rel="tag"><?php echo $tag->name; ?></a>
			<?php } ?>
		</div>
	<?php endif ?>
<?php endif ?>