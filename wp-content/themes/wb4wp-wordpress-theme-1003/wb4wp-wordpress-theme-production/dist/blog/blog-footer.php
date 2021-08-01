<?php // @codingStandardsIgnoreStart ?>
<?php if ( get_edit_post_link() ) : ?>
  <footer class="entry-footer">
    <?php
      edit_post_link(
        sprintf(
          wp_kses(
            /* translators: %s: Name of current post. Only visible to screen readers */
            __( 'Edit <span class="screen-reader-text">%s</span>', 'wb4wp_theme' ),
            array(
              'span' => array(
                'class' => array(),
              ),
            )
          ),
          wp_kses_post( get_the_title() )
        ),
        '<span class="edit-link">',
        '</span>'
      );
    ?>
  </footer><!-- .entry-footer -->
<?php endif; ?>