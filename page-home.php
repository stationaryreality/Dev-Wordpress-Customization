<?php
/*
Template Name: Custom Home
*/

get_header();

// =====================================================
// DEFINE PAGE SLUGS FOR SECTIONS
// (Update these to match your actual page slugs)
// =====================================================

$domain_slugs = [
    'systems-administration-linux',
    'network-architecture-cdn',
    'frontend-cms-theme',
    'cybersecurity-hardening',
    'performance-optimization',
    'media-engineering',
    'information-architecture',
    'disaster-recovery',
    'communication-infrastructure',
    'ai-augmented-engineering',
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
| 1. HERO TEXT
|--------------------------------------------------------------------------
*/
?>
<div class="homepage-intro">
    <p class="homepage-intro-lead">Engineering the Knowledge Platform.</p>
    <p>Explore the architecture, development notes, documentation, and active work behind a custom WordPress knowledge platform.</p>
</div>

<?php
/*
|--------------------------------------------------------------------------
| 2. OVERVIEW + TIMELINE (Top Pair)
|--------------------------------------------------------------------------
*/
$overview_page = get_page_by_path('platform-overview');
$timeline_page = get_page_by_path('site-development-timeline'); // Adjust slug if needed
?>
<section class="homepage-section top-pair-section">
    <div class="top-pair-grid">
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
                        <?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
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