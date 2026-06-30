<?php
/*
Template Name: Custom Home
*/

get_header();
?>

<main class="homepage-posts">

<div class="homepage-intro">
    <p class="homepage-intro-lead">
        This site documents the design, engineering, and ongoing evolution of Stationary Reality.
    </p>

    <p>
        Explore the architecture, development notes, documentation, and active work behind a custom WordPress knowledge platform.
    </p>
</div>

<?php
/*
|--------------------------------------------------------------------------
| SITE RESOURCES
|--------------------------------------------------------------------------
*/

$resource_slugs = [
    'main-site',
    'site-updates',
    'active-and-complete-tasks',
    'site-tools',
    'wordpress-customization-github',

];

$resource_ids = [];

foreach ($resource_slugs as $slug) {
    $page = get_page_by_path($slug);
    if ($page) {
        $resource_ids[] = $page->ID;
    }
}

if (!empty($resource_ids)) :

$resources_query = new WP_Query([
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'post__in'       => $resource_ids,
    'orderby'        => 'post__in',
]);

if ($resources_query->have_posts()) :
?>

<section class="homepage-section">

    <h2 class="page-section-title">Site Resources</h2>

    <div class="tag-posts-grid">

        <?php while ($resources_query->have_posts()) : $resources_query->the_post(); ?>

            <div class="tag-post-item">

                <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </a>

                <a href="<?php the_permalink(); ?>" class="tag-post-title">
                    <?php the_title(); ?>
                </a>

            </div>

        <?php endwhile; ?>

    </div>

</section>

<?php
endif;
wp_reset_postdata();
endif;
?>


<?php
/*
|--------------------------------------------------------------------------
| THE ENGINE
|--------------------------------------------------------------------------
*/

$exclude_ids = $resource_ids;

$home_page = get_page_by_path('home');
if ($home_page) {
    $exclude_ids[] = $home_page->ID;
}

$engine_query = new WP_Query([
    'post_type'      => 'page',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'post__not_in'   => $exclude_ids,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);

if ($engine_query->have_posts()) :
?>

<section class="homepage-section">

    <h2 class="page-section-title">The Engine</h2>

    <div class="tag-posts-grid">

        <?php while ($engine_query->have_posts()) : $engine_query->the_post(); ?>

            <div class="tag-post-item">

                <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </a>

                <a href="<?php the_permalink(); ?>" class="tag-post-title">
                    <?php the_title(); ?>
                </a>

            </div>

        <?php endwhile; ?>

    </div>

</section>

<?php
endif;

wp_reset_postdata();
?>

<?php
/*
|--------------------------------------------------------------------------
| DEMYSTIFYING CODE
|--------------------------------------------------------------------------
*/

$demystifying_query = new WP_Query([
    'post_type' => 'article',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'tax_query' => [[
        'taxonomy' => 'series',
        'field'    => 'slug',
        'terms'    => 'demystifying-code'
    ]]
]);

if ($demystifying_query->have_posts()) :
?>

<section class="homepage-section">

    <h2 class="page-section-title">Demystifying Code</h2>

    <div class="tag-posts-grid">

        <?php while ($demystifying_query->have_posts()) : $demystifying_query->the_post(); ?>

            <div class="tag-post-item">

                <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </a>

                <a href="<?php the_permalink(); ?>" class="tag-post-title">
                    <?php the_title(); ?>
                </a>

            </div>

        <?php endwhile; ?>

    </div>

</section>

<?php
endif;
wp_reset_postdata();
?>

<?php
/*
|--------------------------------------------------------------------------
| GUIDES & UNRELATED
|--------------------------------------------------------------------------
*/

$guides_query = new WP_Query([
    'post_type' => 'article',
    'posts_per_page' => -1,
    'orderby' => 'menu_order',
    'order' => 'ASC',
    'tax_query' => [[
        'taxonomy' => 'series',
        'field'    => 'slug',
        'terms'    => 'guides-unrelated'
    ]]
]);

if ($guides_query->have_posts()) :
?>

<section class="homepage-section">

    <h2 class="page-section-title">Guides & Unrelated</h2>

    <div class="tag-posts-grid">

        <?php while ($guides_query->have_posts()) : $guides_query->the_post(); ?>

            <div class="tag-post-item">

                <a href="<?php the_permalink(); ?>" class="tag-post-thumbnail">
                    <?php if (has_post_thumbnail()) : ?>
                        <img src="<?php the_post_thumbnail_url('medium'); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </a>

                <a href="<?php the_permalink(); ?>" class="tag-post-title">
                    <?php the_title(); ?>
                </a>

            </div>

        <?php endwhile; ?>

    </div>

</section>

<?php
endif;

wp_reset_postdata();
?>

</main>

<?php get_footer(); ?>