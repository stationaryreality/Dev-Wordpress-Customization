<?php
/*
Template Name: Custom Home
*/

get_header();

// =====================================================
// DEFINE PAGE SLUGS FOR SECTIONS
// =====================================================

$domain_slugs = [
    'virtual-private-server',
    'linux-server-configuration',
    'dns-content-delivery',
    'wordpress-content-management',
    'security-hardening',
    'performance-optimization',
    'media-handling-image-strategy',
    'content-structure-taxonomy',
    'backup-strategies-recovery',
    'contact-systems-email-forms',
    'development-tools-workflows',
    'using-ai-in-site-development',
];

$resource_slugs = [
    'site-updates',
    'active-and-complete-tasks',
    'engineering-logs',
    'site-tools',
    'main-site',
    'wordpress-customization-github',
];
?>

<main class="homepage-posts">

<?php
/*
|--------------------------------------------------------------------------
| 2. MAIN SITE + OVERVIEW + TIMELINE (3-Column Grid)
|--------------------------------------------------------------------------
*/
$main_site_page = get_page_by_path('main-site');
$overview_page = get_page_by_path('platform-overview');
$timeline_page = get_page_by_path('timeline');
?>
<section class="homepage-section top-pair-section">
    
    <!-- ADDED SECTION TITLE -->
    <h2 class="page-section-title">Behind the Build</h2>
    
    <div class="top-pair-grid top-pair-grid-three">
        
        <?php if ($main_site_page) : ?>
        <div class="top-pair-card top-pair-card-logo">
            <a href="<?php echo get_permalink($main_site_page->ID); ?>" class="tag-post-thumbnail">
                <?php if (has_post_thumbnail($main_site_page->ID)) echo get_the_post_thumbnail($main_site_page->ID, 'medium'); ?>
            </a>
            <a href="<?php echo get_permalink($main_site_page->ID); ?>" class="tag-post-title">Main Site</a>
            
            <span class="tag-post-url">www.sitename.com</span>
            
            <p class="tag-post-excerpt">The knowledge platform this dev site was built to support.</p>
        </div>
        <?php endif; ?>
        
        <?php if ($overview_page) : ?>
        <div class="top-pair-card">
            <a href="<?php echo get_permalink($overview_page->ID); ?>" class="tag-post-thumbnail">
                <?php if (has_post_thumbnail($overview_page->ID)) echo get_the_post_thumbnail($overview_page->ID, 'large'); ?>
            </a>
            <a href="<?php echo get_permalink($overview_page->ID); ?>" class="tag-post-title">Platform Overview & Architecture</a>
            <p class="tag-post-excerpt">The vision, structure, and technical foundation of the dual-site ecosystem.</p>
        </div>
        <?php endif; ?>
        
        <?php if ($timeline_page) : ?>
        <div class="top-pair-card">
            <a href="<?php echo get_permalink($timeline_page->ID); ?>" class="tag-post-thumbnail">
                <?php if (has_post_thumbnail($timeline_page->ID)) echo get_the_post_thumbnail($timeline_page->ID, 'large'); ?>
            </a>
            <a href="<?php echo get_permalink($timeline_page->ID); ?>" class="tag-post-title">Site Development Timeline</a>
            <p class="tag-post-excerpt">1.5 years of engineering history, architectural decisions, and platform evolution.</p>
        </div>
        <?php endif; ?>
        
    </div>
</section>


<?php
/*
|--------------------------------------------------------------------------
| 3. CORE NAVIGATION (6-Item Compact Grid)
|--------------------------------------------------------------------------
*/
$core_slugs = [
    'site-tools',
    'site-updates',
    'active-and-complete-tasks',
    'engineering-logs',
    'documents',
    'wordpress-customization-github'
];

$core_ids = [];
foreach ($core_slugs as $slug) {
    $page = get_page_by_path($slug);
    if ($page) $core_ids[] = $page->ID;
}

if (!empty($core_ids)) :
$core_query = new WP_Query([
    'post_type'      => 'page',
    'posts_per_page' => 6,
    'post__in'       => $core_ids,
    'orderby'        => 'post__in'
]);

if ($core_query->have_posts()) :
?>
<section class="homepage-section core-nav-section">
    <!-- Title is OUTSIDE the grid/box -->
    <h2 class="page-section-title">Platform Directory</h2>
    
    <!-- The background box goes on this grid container -->
    <div class="core-nav-grid">
        <?php while ($core_query->have_posts()) : $core_query->the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="core-nav-item">
                <div class="core-nav-thumbnail">
                    <?php if (has_post_thumbnail()) the_post_thumbnail('thumbnail'); ?>
                </div>
                <span class="core-nav-title"><?php the_title(); ?></span>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php
endif;
endif;
?>


<?php
/*
|--------------------------------------------------------------------------
| 3. ENGINEERING PULSE (2-Column Compact)
|--------------------------------------------------------------------------
*/
?>
<section class="homepage-section engineering-pulse-section">
    <h2 class="page-section-title">Engineering Pulse</h2>
    <div class="pulse-grid">
        <!-- LEFT: Tasks -->
        <div class="pulse-column">
            <h3 class="pulse-column-title">Tasks</h3>
            <?php
            $active_tasks = new WP_Query(['post_type' => 'task', 'posts_per_page' => 5, 'orderby' => 'date', 'order' => 'DESC', 'tax_query' => [['taxonomy' => 'task_status', 'field' => 'slug', 'terms' => 'active']]]);
            if ($active_tasks->have_posts()) : while ($active_tasks->have_posts()) : $active_tasks->the_post(); ?>
                <div class="pulse-item">
                    <span class="pulse-status active">Active</span>
                    <a href="<?php the_permalink(); ?>" class="pulse-title"><?php the_title(); ?></a>
                    <span class="pulse-date"><?php echo get_the_date('M j'); ?></span>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>

            <?php
            $completed_tasks = new WP_Query(['post_type' => 'task', 'posts_per_page' => 6, 'orderby' => 'date', 'order' => 'DESC', 'tax_query' => [['taxonomy' => 'task_status', 'field' => 'slug', 'terms' => 'completed']]]);
            if ($completed_tasks->have_posts()) : while ($completed_tasks->have_posts()) : $completed_tasks->the_post(); ?>
                <div class="pulse-item">
                    <span class="pulse-status completed">Done</span>
                    <a href="<?php the_permalink(); ?>" class="pulse-title"><?php the_title(); ?></a>
                    <span class="pulse-date"><?php echo get_the_date('M j'); ?></span>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
            <a href="/active-and-complete-tasks/" class="pulse-link">View All Tasks →</a>
        </div>

        <!-- RIGHT: Updates + Logs -->
        <div class="pulse-column">
            <h3 class="pulse-column-title">Updates</h3>
            <?php
            $updates_query = new WP_Query(['post_type' => 'update', 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC']);
            if ($updates_query->have_posts()) : while ($updates_query->have_posts()) : $updates_query->the_post(); ?>
                <div class="pulse-item">
                    <span class="pulse-status update">Update</span>
                    <a href="<?php the_permalink(); ?>" class="pulse-title"><?php the_title(); ?></a>
                    <span class="pulse-date"><?php echo get_the_date('M j'); ?></span>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
            <a href="/site-updates/" class="pulse-link">View All Updates →</a>

            <h3 class="pulse-column-title" style="margin-top: 1.5rem;">Engineering Logs</h3>
            <?php
            $logs_query = new WP_Query(['post_type' => 'note', 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC']);
            if ($logs_query->have_posts()) : while ($logs_query->have_posts()) : $logs_query->the_post(); ?>
                <div class="pulse-item">
                    <span class="pulse-status log">Log</span>
                    <a href="<?php the_permalink(); ?>" class="pulse-title"><?php the_title(); ?></a>
                    <span class="pulse-date"><?php echo get_the_date('M j'); ?></span>
                </div>
            <?php endwhile; wp_reset_postdata(); endif; ?>
            <a href="/engineering-logs/" class="pulse-link">View All Logs →</a>
        </div>
    </div>
    <div class="pulse-footer">
        <a href="/site-development-timeline/" class="btn-link">View Full Timeline →</a>
    </div>
</section>

<?php
/*
|--------------------------------------------------------------------------
| 4. TECHNICAL DOMAINS (Horizontal List)
|--------------------------------------------------------------------------
*/
// NOTE: Ensure $domain_slugs is defined earlier in your file, e.g., $domain_slugs = ['slug-1', 'slug-2'];
if (isset($domain_slugs) && !empty($domain_slugs)) :
    $domain_ids = [];
    foreach ($domain_slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page) $domain_ids[] = $page->ID;
    }

    if (!empty($domain_ids)) :
    $domains_query = new WP_Query(['post_type' => 'page', 'posts_per_page' => -1, 'post__in' => $domain_ids, 'orderby' => 'post__in']);
    if ($domains_query->have_posts()) :
    ?>
    <section class="homepage-section domain-list-section">
        <h2 class="page-section-title">Technical Domains</h2>
        <div class="domain-list-grid">
            <?php while ($domains_query->have_posts()) : $domains_query->the_post(); ?>
                <div class="domain-list-item">
                    <a href="<?php the_permalink(); ?>" class="domain-list-thumbnail">
                        <?php if (has_post_thumbnail()) the_post_thumbnail('thumbnail'); ?>
                    </a>
                    <div class="domain-list-content">
                        <a href="<?php the_permalink(); ?>" class="domain-list-title"><?php the_title(); ?></a>
                        <p class="domain-list-excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php
    endif;
    endif;
endif;
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

</main>

<?php get_footer(); ?>