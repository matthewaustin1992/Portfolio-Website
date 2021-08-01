<?php // @codingStandardsIgnoreStart ?>
<?php
  use Wb4WpTheme\Managers\Customize\Customize_Settings;

  // Defaults
  $template_args = [
    'show_price' => false,
    'hover_for_cart_details' => false
  ];

  if(!empty($args)) {
    $template_args = array_merge( $template_args, $args );
  }
?>

<?php //if (!empty(Customize_Settings::get_setting('wb4wp_header_section_cart-button'))): ?>
<?php if (true): //TODO: replace this with setting ?>
  <?php if ( class_exists( 'WooCommerce' ) ) :?>
    <a href="<?= esc_url( wc_get_cart_url() ); ?>" class="wb4wp-navbar-button wb4wp-navbar-button-cart">
      <i class="wb4wp-icon fa fa-shopping-cart wb4wp-navbar-button-cart-icon"></i>
      <span class="count wb4wp-wc-cart-item-count"><?= esc_html( WC()->cart->get_cart_contents_count());?></span>
      
      <?php if( $template_args['show_price'] ): ?>
        <span class="price">
            <?= wp_strip_all_tags( WC()->cart->get_cart_total() );?>
        </span>
      <?php endif;?>
    </a>
    <?php if( $template_args['hover_for_cart_details'] ): ?>
      <div class="wb4wp-wc-mini-cart widget_shopping_cart_content">
        <?php woocommerce_mini_cart(); ?>
      </div>
    <?php endif;?>
    
  <?php endif;?>
<?php endif; ?>
