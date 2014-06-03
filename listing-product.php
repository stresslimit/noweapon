
  <article <?php post_class( 'listing record' ) ?>>

    <div class="images">
      <a href="<?php the_permalink() ?>"><?php the_post_thumbnail() ?></a>
      <div class="cat_no"><?php echo $post->nw_catalog_no ?></div>
    </div>

    <h3><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h3>
    <?php /*<h3><?php echo nw_get_attribute( $post->ID, 'catalog_no' ) ?></h3> */ ?>

  </article>
