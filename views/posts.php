<?php

add_action( 'wp_ajax_virtuoso_portfolio_display_posts', 'virtuoso_portfolio_display_posts' );
add_action( 'wp_ajax_nopriv_virtuoso_portfolio_display_posts', 'virtuoso_portfolio_display_posts' );

function virtuoso_portfolio_display_posts() {

//  $offset = (count($_POST) > 0) ? (int) $_POST['offset'] : 0; // skip previous pagination
  $taxonomy = ($_POST['taxonomy'] !== null) ? $_POST['taxonomy'] : ''; // for single term pagination
  $numberOfPosts = (count($_POST) > 0) ? (int) $_POST['numberOfPosts'] : 6; // default number of posts or grab from ajax

  // VIRTUOSO PORTFOLIO OPTIONS
  $pluralCPTName = get_option('virtuoso_portfolio_cpt_name_plural');
  $cptSlug = strtolower($pluralCPTName);
  $taxonomyNamePlural = get_option('virtuoso_portfolio_taxonomy_name_plural');
  $taxonomySlug = strtolower($taxonomyNamePlural);

  if ($taxonomy !== '') {

    // single term
    $taxonomies = array($taxonomy);

  } else {

    $styles = get_terms( array(
        'taxonomy' => $taxonomySlug,
        'hide_empty' => false,  ) );

    $taxonomies = array();

    foreach ($styles as $style) {
      $taxonomies[] = $style->slug;
    }

  }

  // WP_Query arguments
  $args = array(
    'post_type'       => array( $cptSlug ),
    'post_status'     => array( 'publish' ),
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'posts_per_page'  => -1, // show all posts
//    'numberposts'     => $numberOfPosts,
//    'offset'          => $offset,
    'tax_query' => array(
        array(
            'taxonomy' => $taxonomySlug,
            'field' => 'slug',
            'terms' => $taxonomies
        )
    )
  );

  // The Query
  $loop = new WP_Query( $args );

  if ( $loop->have_posts() ) :?>
       <?php
      // loop through posts
      while ( $loop->have_posts() ): $loop->the_post();

        $image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array(400,400));

        if ( $image_attributes ) {

          $taxonomies = get_the_terms( get_the_ID(), $taxonomySlug );

          ?>
          <li data-taxonomy-slug="<?php echo $taxonomies[0]->slug?>">
              <div class="item_wrap">
                <a href="<?php the_permalink(); ?>" class="portfolio_image_link">
                  <img class="single_slider_item" src="<?php echo $image_attributes[0]; ?>"/>
                  <div class="portfolio_header_wrap">
                    <a class="portfolio_name" href="<?php the_permalink(); ?>"><span><?php the_title(); ?></span></a>
                    <span class="portfolio_category"><?php echo get_field('category'); ?></span>
                  </div>
                </a>
              </div>
          </li>
          <?php

        }

      endwhile; ?>
  <?php

  endif;

  wp_reset_query();
  wp_reset_postdata();

//  if (count($loop->posts) === $numberOfPosts) {
    ?>
<!--    </div> .gallery_wrap -->
<!--    <div class="show_more">-->
<!--      <a>Show more <i class="ti-reload icon"></i></a>-->
<!--      <a href="#/" data-offset="0" data-number-of-posts="--><?php ////echo $numberOfPosts?><!--" data-taxonomy-slug="--><?php ////echo $taxonomy?><!--">Show more <i class="ti-reload icon"></i></a>-->
<!--    </div>-->
    <?php
//  }

//  if ($offset > 0) {
//    exit; // avoid trailing 0 from json
//  }

}
