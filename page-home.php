<?php get_header(); ?>


  <section class="first-block">

    <?php

    // this is the 'noweapon makes' text
    while (have_posts()) : the_post();
      the_content();
    endwhile;

    ?>

    <?php

    // this is for important announcements, marked as sticky
    $sticky = get_option( 'sticky_posts' );
    $args = array(
      'post__in' => array( $sticky[0] )
    );

    $p = get_posts( $args );
    foreach ( $p as $post ) : setup_postdata($post);

      get_template_part( 'listing', $post->post_type );

    endforeach;

    ?>

    <h1 class="supersize">records</h1>

    <div>

      <?php

        $args = array(
          'post_type' => array( 'product' /*, 'event'*/ ),
          'post_parent' => 0,
          'numberposts' => 10,
        );

        $p = get_posts( $args );
        foreach ( $p as $post ) : setup_postdata($post);

          get_template_part( 'listing', $post->post_type );

        endforeach;

      ?>
	
  	</div>

  </section>


  <section class="second-block">

    <h1 class="supersize">thoughts</h1>

    <div>

      <?php

      $sticky = get_option( 'sticky_posts' );
      $p = get_posts( array(
        'numberposts' => 2,
      	'post__not_in' => $sticky,
      ));
      foreach ( $p as $post ) : setup_postdata($post);

        // strangely there is a global var inside the 'listing' template part that 
        // counts and display the first item large and the others small.
        get_template_part( 'listing', $post->post_type );

      endforeach;

      ?>

      <div class="more clear">
        <a href="<?php bloginfo( 'url' ) ?>#more">&rarr;&nbsp;more</a>
      </div>

    </div>
	
  </section>



<?php get_footer();
