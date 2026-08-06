<?php

// Enqueue parent and child theme styles
function ct_author_child_enqueue_styles() {
    $parent_style = 'ct-author-style';

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('ct-author-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style)
    );
}
add_action('wp_enqueue_scripts', 'ct_author_child_enqueue_styles');

// Enable excerpts for Pages
add_post_type_support('page', 'excerpt');

// Redirect default Posts admin screen to show only published posts
add_action('load-edit.php', function () {
    $screen = get_current_screen();
    if ($screen->post_type == 'post' && !isset($_GET['post_status']) && !isset($_GET['all_posts'])) {
        wp_redirect(admin_url('edit.php?post_status=publish&post_type=post'));
        exit;
    }
});

// [Search blocks by anchor across CPTS, future linking tool
function get_blocks_by_anchor($target_anchors = []) {
    $matching_blocks = [];
    $args = [
        'post_type' => ['post'],
        'posts_per_page' => -1,
        'post_status' => 'publish',
    ];
    $query = new WP_Query($args);
    while ($query->have_posts()) {
        $query->the_post();
        $blocks = parse_blocks(get_the_content());
        foreach ($blocks as $block) {
            if (!empty($block['attrs']['anchor']) && in_array($block['attrs']['anchor'], $target_anchors)) {
                $matching_blocks[] = $block;
            }
        }
    }
    wp_reset_postdata();
    return $matching_blocks;
}


// Disable Author Archive pages (redirect to 404)
add_action('template_redirect', function () {
    if (is_author()) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
});

// Remove Google Fonts from Parent Theme
function child_theme_remove_google_fonts() {
    wp_dequeue_style('ct-author-google-fonts'); // Update handle if needed
}
add_action('wp_enqueue_scripts', 'child_theme_remove_google_fonts', 20);

// Load custom local fonts
function child_theme_enqueue_custom_fonts() {
    wp_enqueue_style('custom-fonts', get_stylesheet_directory_uri() . '/fonts/fonts.css');
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_custom_fonts');


// =====================================================
// Disable Comments Site-Wide
// =====================================================
add_action('init', function() {
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);
add_action('admin_menu', function() {
    remove_menu_page('edit-comments.php');
});
add_action('init', function() {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

// =====================================================
// Disable RSS Feeds
// =====================================================
add_action('do_feed', 'disable_feeds', 1);
add_action('do_feed_rdf', 'disable_feeds', 1);
add_action('do_feed_rss', 'disable_feeds', 1);
add_action('do_feed_rss2', 'disable_feeds', 1);
add_action('do_feed_atom', 'disable_feeds', 1);
function disable_feeds() {
    wp_die(__('No feed available, please visit the homepage.'));
}

// =====================================================
// UNIFIED SIDEBAR NAVIGATION SHORTCODE
// =====================================================

add_action('init', function () {
    // Register the single unified shortcode
    add_shortcode('dev_unified_nav', 'generate_unified_dev_nav');
});

if (!function_exists('generate_unified_dev_nav')) {
    function generate_unified_dev_nav() {
        ob_start(); // Start output buffering to build clean HTML
        ?>
        <nav class="dev-sidebar-nav">
            
            <?php 
            // ---------------------------------------------------------
            // 1. BEHIND THE BUILD
            // ---------------------------------------------------------
            $behind_build_slugs = ['main-site', 'platform-overview', 'timeline', 'the-architect'];
            $behind_build_ids = [];
            foreach ($behind_build_slugs as $slug) {
                $page = get_page_by_path($slug);
                if ($page) $behind_build_ids[] = $page->ID;
            }

            if (!empty($behind_build_ids)) :
                $bb_query = new WP_Query(['post_type' => 'page', 'post__in' => $behind_build_ids, 'orderby' => 'post__in', 'posts_per_page' => -1]);
                if ($bb_query->have_posts()) :
            ?>
            <div class="nav-block">
                <h3 class="nav-block-title">Behind the Build</h3>
                <ul class="nav-list">
                    <?php while ($bb_query->have_posts()) : $bb_query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php 
                endif;
            endif; 
            ?>

            <?php 
            // ---------------------------------------------------------
            // 2. PLATFORM DIRECTORY & RESOURCES
            // ---------------------------------------------------------
            $resource_slugs = ['site-updates', 'active-and-complete-tasks', 'engineering-logs', 'documents', 'site-tools', 'wordpress-customization-github'];
            $resource_ids = [];
            foreach ($resource_slugs as $slug) {
                $page = get_page_by_path($slug);
                if ($page) $resource_ids[] = $page->ID;
            }

            if (!empty($resource_ids)) :
                $res_query = new WP_Query(['post_type' => 'page', 'post__in' => $resource_ids, 'orderby' => 'post__in', 'posts_per_page' => -1]);
                if ($res_query->have_posts()) :
            ?>
            <div class="nav-block">
                <h3 class="nav-block-title">Platform Directory</h3>
                <ul class="nav-list">
                    <?php while ($res_query->have_posts()) : $res_query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php 
                endif;
            endif; 
            ?>

            <?php 
            // ---------------------------------------------------------
            // 3. TECHNICAL DOMAINS
            // ---------------------------------------------------------
            $domain_slugs = [
                'virtual-private-server', 'linux-server-configuration', 'dns-content-delivery',
                'wordpress-content-management', 'security-hardening', 'performance-optimization',
                'media-handling-image-strategy', 'content-structure-taxonomy', 'backup-strategies-recovery',
                'contact-systems-email-forms', 'development-tools-workflows', 'using-ai-in-site-development',
            ];
            $domain_ids = [];
            foreach ($domain_slugs as $slug) {
                $page = get_page_by_path($slug);
                if ($page) $domain_ids[] = $page->ID;
            }

            if (!empty($domain_ids)) :
                $dom_query = new WP_Query(['post_type' => 'page', 'post__in' => $domain_ids, 'orderby' => 'post__in', 'posts_per_page' => -1]);
                if ($dom_query->have_posts()) :
            ?>
            <div class="nav-block">
                <h3 class="nav-block-title">Technical Domains</h3>
                <ul class="nav-list">
                    <?php while ($dom_query->have_posts()) : $dom_query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php 
                endif;
            endif; 
            ?>

            <?php 
            // ---------------------------------------------------------
            // 4. LEARNING SERIES: DEMYSTIFYING CODE
            // ---------------------------------------------------------
            $demo_query = new WP_Query([
                'post_type' => 'article',
                'posts_per_page' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'tax_query' => [['taxonomy' => 'series', 'field' => 'slug', 'terms' => 'demystifying-code']]
            ]);

            if ($demo_query->have_posts()) :
            ?>
            <div class="nav-block">
                <h3 class="nav-block-title">Demystifying Code</h3>
                <ul class="nav-list">
                    <?php while ($demo_query->have_posts()) : $demo_query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php 
            // ---------------------------------------------------------
            // 5. GUIDES (Temporary - Remove when migrating to new site)
            // ---------------------------------------------------------
            $guides_query = new WP_Query([
                'post_type' => 'article',
                'posts_per_page' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'tax_query' => [['taxonomy' => 'series', 'field' => 'slug', 'terms' => 'guides-unrelated']]
            ]);

            if ($guides_query->have_posts()) :
            ?>
            <div class="nav-block">
                <h3 class="nav-block-title">Guides & Unrelated</h3>
                <ul class="nav-list">
                    <?php while ($guides_query->have_posts()) : $guides_query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile; wp_reset_postdata(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php 
            // ---------------------------------------------------------
            // 6. PROJECTS LINK
            // ---------------------------------------------------------
            ?>
            <div class="nav-block">
                <h3 class="nav-block-title">Projects</h3>
                <ul class="nav-list">
                    <li><a href="/projects/">View All Projects →</a></li>
                </ul>
            </div>

        </nav>
        <?php
        return ob_get_clean(); // Return the buffered HTML
    }
}

/**
 * Global Excerpt Cleaner
 * Strips rogue HTML, theme-injected wrappers, and "Continue reading" links globally.
 */
function global_clean_excerpt($excerpt) {
    if (empty($excerpt)) {
        return $excerpt;
    }
    
    // 1. Strip ALL HTML tags (removes <div class="more-link-wrapper">, <a>, <span>, etc.)
    $clean = wp_strip_all_tags($excerpt);
    
    // 2. Remove "Continue reading", "Read more", or "[...]" and any trailing text
    $clean = preg_replace('/\s*(\[\.\.\.\]|Continue reading|Read more).*$/i', '', $clean);
    
    // 3. Clean up stray ellipsis, periods, or trailing spaces
    return trim(rtrim($clean, ' .…'));
}
add_filter('get_the_excerpt', 'global_clean_excerpt', 999);
add_filter('the_excerpt', 'global_clean_excerpt', 999);

/**
 * Kill the default WordPress excerpt more string entirely
 */
function custom_excerpt_more($more) {
    return '';
}
add_filter('excerpt_more', 'custom_excerpt_more', 999);


// CSS files loader for Dev Site Child Theme
function dev_site_enqueue_styles() {
    $css_files = [
        'global',
        'task-dashboard',
        'hero',
        'top-pair',
        'core-nav',
        'engineering-pulse',
        'domain-list',
        'resource-grid',
        'timeline',
        'timeline-widget'
    ];

    foreach ($css_files as $file) {
        wp_enqueue_style(
            'dev-' . $file,
            get_stylesheet_directory_uri() . "/assets/css/{$file}.css",
            [],
            filemtime(get_stylesheet_directory() . "/assets/css/{$file}.css") // Auto cache-bust on save
        );
    }
}
add_action('wp_enqueue_scripts', 'dev_site_enqueue_styles');


//2026-08-06
/**
 * Get color for a project based on its slug
 * Uses a curated palette of 10 colors that cycle through projects
 */
function get_project_color($project_slug) {
    $color_palette = [
        '#3498db', // Blue
        '#e74c3c', // Red
        '#2ecc71', // Green
        '#f39c12', // Orange
        '#9b59b6', // Purple
        '#1abc9c', // Teal
        '#e67e22', // Dark Orange
        '#34495e', // Dark Blue
        '#16a085', // Dark Teal
        '#c0392b', // Dark Red
    ];
    
    // Convert slug to a consistent index
    $hash = crc32($project_slug);
    $index = abs($hash) % count($color_palette);
    
    return $color_palette[$index];
}

// 2026-7-27


require_once get_stylesheet_directory() . '/inc/helpers/record-breadcrumbs.php';

require_once get_stylesheet_directory() . '/inc/helpers/record-redirects.php';