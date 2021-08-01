<?php // @codingStandardsIgnoreStart ?>
<?php

$template_args = [ 
  'archive' => false 
];

if (!empty($args)) {
  $template_args = array_merge( $template_args, $args );
}

get_template_part( 'dist/blog/blog' , $template_args['archive'] ? 'archive-item' : '');
