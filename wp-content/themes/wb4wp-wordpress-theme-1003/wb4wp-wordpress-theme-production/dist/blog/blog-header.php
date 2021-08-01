<?php // @codingStandardsIgnoreStart ?>
<div class="wb4wp-blog-container">
    <?php $blog_template_part_path = 'dist/blog/blog'; ?>
    <?php get_template_part($blog_template_part_path, 'header-meta'); ?>

    <header class="entry-header">
        <h1 class="entry-title"><?php single_post_title(); ?></h1>
    </header>
</div>