<?php
/*
Template Name: Custom Home
*/

get_header();
?>

<main class="homepage-posts">

    <?php
    /*
    |--------------------------------------------------------------------------
    | 1. OVERVIEW / START HERE
    |--------------------------------------------------------------------------
    */
    ?>
    <div class="homepage-intro">
        <p class="homepage-intro-lead">
            Engineering the Knowledge Platform.
        </p>
        <p>
            Explore the architecture, development notes, documentation, and active work behind a custom WordPress knowledge platform.
        </p>
    </div>

    <?php
    /*
    |--------------------------------------------------------------------------
    | 2. TECHNICAL DOMAINS (Formerly "The Engine")
    | Note: Keeping the catch-all query for Phase 1. Will be refined in Phase 2.
    |--------------------------------------------------------------------------
    */
    
    // Exclude Resources and Home page
    $resource_slugs = [
        'main-site', 'site-updates', 'active-and-complete-tasks', 
        'Engineering Logs', 'site-tools', 'wordpress-customization-github',
    ];
    $exclude_ids = [];
    foreach ($resource_slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page) $exclude_ids[] = $page->ID;
    }
    $home_page = get_page_by_path('home');
    if ($home_page) $exclude_ids[] = $home_page->ID;

    $domains_query = new WP_Query([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__not_in'   => $exclude_ids,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    if ($domains_query->have_posts()) :
    ?>
    <section class="homepage-section">
        <h2 class="page-section-title">Technical Domains</h2>
        <div class="tag-posts-grid">
            <?php while ($domains_query->have_posts()) : $domains_query->the_post(); ?>
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
    | 3. LEARNING SERIES (Formerly "Demystifying Code")
    |--------------------------------------------------------------------------
    */
    $learning_query = new WP_Query([
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

    if ($learning_query->have_posts()) :
    ?>
    <section class="homepage-section">
        <h2 class="page-section-title">Learning Series</h2>
        <div class="tag-posts-grid">
            <?php while ($learning_query->have_posts()) : $learning_query->the_post(); ?>
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
    | 4. ARTICLES & GUIDES (Formerly "Guides & Unrelated")
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
        <h2 class="page-section-title">Articles & Guides</h2>
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

    <?php
    /*
    |--------------------------------------------------------------------------
    | 5. RESOURCES (Formerly "Site Resources", moved to bottom)
    |--------------------------------------------------------------------------
    */
    $resource_ids = [];
    foreach ($resource_slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page) $resource_ids[] = $page->ID;
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
        <h2 class="page-section-title">Resources</h2>
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

</main>

<?php get_footer(); ?>