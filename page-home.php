<?php
/* Template Name: Custom Home */
get_header(); ?>

<main class="homepage-posts">

  <?php
  // === PAGES GRID (The Engine)
  $excluded_ids = array(get_the_ID());
  $page_args = array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'post__not_in' => $excluded_ids,
    'orderby' => 'menu_order',
    'order' => 'ASC'
  );

  $pages_query = new WP_Query($page_args);

  if ($pages_query->have_posts()) :
    echo '<h2 class="page-section-title">The Engine</h2>';
    echo '<div class="tag-posts-grid">';
    while ($pages_query->have_posts()) : $pages_query->the_post(); ?>
      <div class="tag-post-item">
        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
        <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
      </div>
    <?php endwhile;
    echo '</div>';
    wp_reset_postdata();
  endif;
  ?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <?php
  
// === DEMYSTIFYING CODE SERIES
$demystifying_args = array(
  'post_type' => 'chapter',
  'posts_per_page' => -1,
  'orderby' => 'menu_order',
  'order' => 'ASC',
  'category_name' => 'demystifying'
);

$demystifying_query = new WP_Query($demystifying_args);

if ($demystifying_query->have_posts()) :
  echo '<h2 class="page-section-title">Demystifying Code</h2>';
  echo '<div class="tag-posts-grid">';
  while ($demystifying_query->have_posts()) : $demystifying_query->the_post(); ?>
    <div class="tag-post-item">
      <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
        <?php if (has_post_thumbnail()) : ?>
          <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
      </a>
      <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
      <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
    </div>
  <?php endwhile;
  echo '</div>';
  wp_reset_postdata();
endif;
  ?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <?php
// === GUIDES & UNRELATED (formerly "Unrelated")
$unrelated_args = array(
  'post_type' => 'chapter',
  'posts_per_page' => -1,
  'orderby' => 'menu_order',
  'order' => 'ASC',
  'category_name' => 'guidesunrelated'
);

$unrelated_query = new WP_Query($unrelated_args);

if ($unrelated_query->have_posts()) :
  echo '<h2 class="page-section-title">Guides & Unrelated</h2>';
  echo '<div class="tag-posts-grid">';
  while ($unrelated_query->have_posts()) : $unrelated_query->the_post(); ?>
    <div class="tag-post-item">
      <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
        <?php if (has_post_thumbnail()) : ?>
          <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
      </a>
      <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
      <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
    </div>
  <?php endwhile;
  echo '</div>';
  wp_reset_postdata();
endif;
  ?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <?php

  // === POSTS (Current Tasks) - MIDDLE
  $post_args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC'
  );

  $post_query = new WP_Query($post_args);

if ($post_query->have_posts()) :
  echo '<h2 id="tasks" class="page-section-title">Current Tasks</h2>'; // Added id="tasks"
  echo '<div class="tag-posts-grid">';
    while ($post_query->have_posts()) : $post_query->the_post(); ?>
      <div class="tag-post-item">
        <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
          <?php if (has_post_thumbnail()) : ?>
            <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
          <?php endif; ?>
        </a>
        <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
        <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
      </div>
    <?php endwhile;
    echo '</div>';
    wp_reset_postdata();
  else :
    echo '<p>No tasks found.</p>';
  endif;
  ?>

  <hr style="margin: 4em auto; max-width: 80%; border: 0; border-top: 1px solid #ccc;">

  <?php

// === COMPLETED TASKS - moved to very bottom
$chapter_args = array(
  'post_type' => 'chapter',
  'posts_per_page' => -1,
  'orderby' => 'menu_order',
  'order' => 'ASC',
  'category_name' => 'completed'
);

$chapter_query = new WP_Query($chapter_args);

if ($chapter_query->have_posts()) :
  echo '<h2 class="page-section-title">Completed Tasks</h2>';
  echo '<div class="tag-posts-grid">';
  while ($chapter_query->have_posts()) : $chapter_query->the_post(); ?>
    <div class="tag-post-item">
      <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
        <?php if (has_post_thumbnail()) : ?>
          <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
        <?php endif; ?>
      </a>
      <a href="<?php the_permalink(); ?>" class="tag-post-title"><?php the_title(); ?></a>
      <p class="tag-post-excerpt"><?php the_excerpt(); ?></p>
    </div>
  <?php endwhile;
  echo '</div>';
  wp_reset_postdata();
endif;

?>


</main>

<?php get_footer(); ?>
