<?php
// =====================================================
// 1. COMPLETELY REMOVE YOAST BREADCRUMBS ON HOMEPAGE
// =====================================================
add_filter('wpseo_breadcrumb_output', function($output) {
    if (is_front_page() || is_home()) {
        return ''; // Returns nothing, reclaiming vertical space
    }
    return $output;
});

// =====================================================
// 2. MAP CPT ARCHIVES & FIX TAXONOMIES IN BREADCRUMBS
// =====================================================
add_filter('wpseo_breadcrumb_links', function($links) {
    if (empty($links)) return $links;

    // Map CPT archive URLs to your custom pages
    $archive_mappings = [
        'update'   => 'site-updates',
        'task'     => 'active-and-complete-tasks',
        'note'     => 'engineering-logs',
        'document' => 'documents',
    ];

    // Loop through and replace archive links
    foreach ($links as $key => $link) {
        if ($key === count($links) - 1) continue; // Skip current page

        foreach ($archive_mappings as $cpt => $page_slug) {
            $archive_url = get_post_type_archive_link($cpt);
            if ($archive_url && rtrim($link['url'], '/') === rtrim($archive_url, '/')) {
                $page = get_page_by_path($page_slug);
                if ($page) {
                    $links[$key]['text'] = get_the_title($page);
                    $links[$key]['url']  = get_permalink($page);
                    $links[$key]['id']   = $page->ID; // Required for Yoast schema
                }
            }
        }
    }

    // Fix Project Taxonomy Breadcrumb Hierarchy (Home > Projects > Project Name)
    if (is_tax('project')) {
        $projects_page = get_page_by_path('projects');
        if ($projects_page) {
            $term = get_queried_object();
            
            // Rebuild the links array to ensure Projects page is in the middle
            $links = [
                [
                    'url'  => home_url('/'),
                    'text' => 'Home',
                    'id'   => get_option('page_on_front')
                ],
                [
                    'url'  => get_permalink($projects_page),
                    'text' => get_the_title($projects_page),
                    'id'   => $projects_page->ID
                ],
                [
                    'url'  => '',
                    'text' => $term->name
                ]
            ];
        }
    }

    return $links;
});