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
    | 1 & 2. PLATFORM OVERVIEW (Full Article Link)
    |--------------------------------------------------------------------------
    */
    $overview_page = get_page_by_path('platform-overview');
    if ($overview_page) :
    ?>
    <section class="homepage-section">
        <div class="tag-posts-grid" style="grid-template-columns: 1fr;">
            <div class="tag-post-item" style="max-width: 800px; margin: 0 auto;">
                <a href="<?php echo get_permalink($overview_page->ID); ?>" class="tag-post-thumbnail">
                    <?php if (has_post_thumbnail($overview_page->ID)) : ?>
                        <?php echo get_the_post_thumbnail($overview_page->ID, 'large'); ?>
                    <?php endif; ?>
                </a>
                <a href="<?php echo get_permalink($overview_page->ID); ?>" class="tag-post-title">
                    Platform Overview & Architecture
                </a>
                <p class="tag-post-excerpt">
                    The vision, structure, and technical foundation of the dual-site ecosystem.
                </p>
            </div>
        </div>
    </section>
    <?php endif; ?>


    <?php
/*
|--------------------------------------------------------------------------
| 3. ENGINEERING PULSE (Activity Feed)
|--------------------------------------------------------------------------
*/
?>
<section class="homepage-section engineering-pulse-section">
    <h2 class="page-section-title">Engineering Pulse</h2>
    <p class="section-subtitle">Active development, recent milestones, and ongoing engineering tasks.</p>
    
    <div class="activity-feed">
        
        <?php
        // 1. Recent Site Updates (3 most recent)
        $updates_query = new WP_Query([
            'post_type'      => 'update',
            'posts_per_page' => 3,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        
        if ($updates_query->have_posts()) :
            while ($updates_query->have_posts()) : $updates_query->the_post();
        ?>
            <div class="activity-item">
                <span class="activity-date"><?php echo get_the_date('M j'); ?></span>
                <div class="activity-content">
                    <span class="activity-status update">Update</span>
                    <h3 class="activity-title"><?php the_title(); ?></h3>
                    <p class="activity-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                    <a href="<?php echo get_permalink(get_page_by_path('site-updates')->ID); ?>" class="activity-link">View All Updates →</a>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        
        // 2. Most Recent Completed Task
        $completed_task = new WP_Query([
            'post_type'      => 'task',
            'posts_per_page' => 1,
            'meta_key'       => 'task_status',
            'meta_value'     => 'complete',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        
        if ($completed_task->have_posts()) :
            while ($completed_task->have_posts()) : $completed_task->the_post();
        ?>
            <div class="activity-item">
                <span class="activity-date"><?php echo get_the_date('M j'); ?></span>
                <div class="activity-content">
                    <span class="activity-status completed">Completed</span>
                    <h3 class="activity-title"><?php the_title(); ?></h3>
                    <a href="<?php echo get_permalink(get_page_by_path('active-and-complete-tasks')->ID); ?>" class="activity-link">View All Tasks →</a>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        
        // 3. Most Recent Active Task
        $active_task = new WP_Query([
            'post_type'      => 'task',
            'posts_per_page' => 1,
            'meta_key'       => 'task_status',
            'meta_value'     => 'active',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        
        if ($active_task->have_posts()) :
            while ($active_task->have_posts()) : $active_task->the_post();
        ?>
            <div class="activity-item">
                <span class="activity-date"><?php echo get_the_date('M j'); ?></span>
                <div class="activity-content">
                    <span class="activity-status active">Active</span>
                    <h3 class="activity-title"><?php the_title(); ?></h3>
                    <a href="<?php echo get_permalink(get_page_by_path('active-and-complete-tasks')->ID); ?>" class="activity-link">View All Tasks →</a>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        
        // 4. Recent Engineering Logs (3 most recent)
        $logs_query = new WP_Query([
            'post_type'      => 'note',
            'posts_per_page' => 3,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        
        if ($logs_query->have_posts()) :
            while ($logs_query->have_posts()) : $logs_query->the_post();
        ?>
            <div class="activity-item">
                <span class="activity-date"><?php echo get_the_date('M j'); ?></span>
                <div class="activity-content">
                    <span class="activity-status log">Log</span>
                    <h3 class="activity-title"><?php the_title(); ?></h3>
                    <p class="activity-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                    <a href="<?php echo get_permalink(get_page_by_path('engineering-logs')->ID); ?>" class="activity-link">View All Logs →</a>
                </div>
            </div>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
        
    </div>
    
    <div class="activity-footer">
        <a href="<?php echo get_permalink(get_page_by_path('master-timeline')->ID); ?>" class="btn-link">View Full Timeline →</a>
    </div>
</section>


    <?php
    /*
    |--------------------------------------------------------------------------
    | 3. TECHNICAL DOMAINS (Formerly "The Engine")
    |--------------------------------------------------------------------------
    */
    
    // Exclude Resources and Home page
    $resource_slugs = [
        'main-site', 'site-updates', 'active-and-complete-tasks', 
        'Engineering Logs', 'site-tools', 'wordpress-customization-github', 'platform-overview',
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

// Remove Platform Overview from Resources section
$overview_page = get_page_by_path('platform-overview');
if ($overview_page) {
    $resource_ids = array_diff($resource_ids, [$overview_page->ID]);
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