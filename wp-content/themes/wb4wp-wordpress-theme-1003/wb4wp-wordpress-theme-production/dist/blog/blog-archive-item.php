<?php // @codingStandardsIgnoreStart ?>
<a href="<?php the_permalink(); ?>" class="wb4wp-archive-item-blog-link">
  <figure class="wb4wp-archive-item-image">
    <div class="wb4wp-archive-item-image-container">
      <?php $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'post-thumbnail' ); ?>
      <?php if($url): ?>
        <img src="<?php echo $url ?>" />
      <?php endif; ?>
    </div>
  </figure>
  

  <h3 class="wb4wp-archive-item-title">
    <?php the_title(); ?>
  </h3>

  <p class="wb4wp-archive-item-post-date">
    <?= get_the_date('j M, Y',$post) ?>
  </p>
</a>