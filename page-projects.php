<?php
/**
 * Template Name: All Projects
 * Template Post Type: page
 *
 * Final Projects archive page.
 * Uses Option D card treatment: compact swatch, subtle left accent, tinted count badge.
 */

$dev_projects_css_path = get_stylesheet_directory() . '/assets/css/projects-archive.css';

if (file_exists($dev_projects_css_path)) {
    wp_enqueue_style(
        'author-child-projects-archive',
        get_stylesheet_directory_uri() . '/assets/css/projects-archive.css',
        array(),
        filemtime($dev_projects_css_path)
    );
}

if (!function_exists('dev_projects_archive_palette')) {
    function dev_projects_archive_palette() {
        return array(
            '#1565c0',
            '#2e7d32',
            '#e65100',
            '#6a1b9a',
            '#00695c',
            '#c62828',
            '#4e342e',
            '#283593',
            '#00838f',
            '#ad1457',
        );
    }
}

if (!function_exists('dev_projects_archive_is_valid_color')) {
    function dev_projects_archive_is_valid_color($color) {
        if (!is_string($color)) {
            return false;
        }

        $color = trim($color);

        if ('' === $color) {
            return false;
        }

        return (bool) preg_match(
            '/^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i',
            $color
        );
    }
}

if (!function_exists('dev_projects_archive_get_color')) {
    function dev_projects_archive_get_color($index, $term = null) {
        /*
         * Primary method:
         * Use the same position-based color mapper used by the homepage project grid.
         */
        if (function_exists('get_project_color')) {
            $color = get_project_color($index);

            if (dev_projects_archive_is_valid_color($color)) {
                return trim($color);
            }

            /*
             * Fallback only if the main call does not return a usable color.
             * This can help if the existing function expects a term object or term ID.
             */
            if ($term instanceof WP_Term) {
                $color = get_project_color($term->term_id);

                if (dev_projects_archive_is_valid_color($color)) {
                    return trim($color);
                }

                $color = get_project_color($term);

                if (dev_projects_archive_is_valid_color($color)) {
                    return trim($color);
                }
            }
        }

        /*
         * Hard fallback if get_project_color() is unavailable or returns invalid data.
         */
        $palette = dev_projects_archive_palette();

        return $palette[$index % count($palette)];
    }
}

if (!function_exists('dev_projects_archive_get_sorted_terms')) {
    function dev_projects_archive_get_sorted_terms() {
        $terms = get_terms(
            array(
                'taxonomy'   => 'project',
                'hide_empty' => false,
            )
        );

        if (is_wp_error($terms) || empty($terms)) {
            return array();
        }

        /*
         * Match the homepage sorting:
         * record count descending, then project name ascending.
         */
        usort(
            $terms,
            function ($a, $b) {
                if ((int) $a->count === (int) $b->count) {
                    return strcasecmp($a->name, $b->name);
                }

                return (int) $b->count <=> (int) $a->count;
            }
        );

        return $terms;
    }
}

$dev_projects_terms = dev_projects_archive_get_sorted_terms();

get_header();
?>

<main id="main" class="site-main projects-archive">
    <div class="projects-archive-wrap">

        <?php if (have_posts()) : ?>
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <header class="projects-archive-header">
                    <?php the_title('<h1 class="projects-archive-title">', '</h1>'); ?>

                    <?php if (trim(get_the_content())) : ?>
                        <div class="projects-archive-intro">
                            <?php the_content(); ?>
                        </div>
                    <?php endif; ?>
                </header>
                <?php
            endwhile;

            wp_reset_postdata();
            ?>
        <?php else : ?>
            <header class="projects-archive-header">
                <h1 class="projects-archive-title">All Projects</h1>
            </header>
        <?php endif; ?>

        <?php if (empty($dev_projects_terms)) : ?>
            <div class="projects-archive-empty">
                No projects found.
            </div>
        <?php else : ?>
            <div class="projects-grid">
                <?php
                foreach ($dev_projects_terms as $index => $term) {
                    $color = dev_projects_archive_get_color($index, $term);

                    $link = get_term_link($term);

                    if (is_wp_error($link)) {
                        $link = '#';
                    }

                    $count       = (int) $term->count;
                    $count_label = 1 === $count ? '1 record' : sprintf('%d records', $count);

                    $excerpt = '';

                    if (!empty($term->description)) {
                        $excerpt = wp_trim_words(
                            wp_strip_all_tags($term->description),
                            18,
                            '…'
                        );
                    }
                    ?>
                    <article class="project-card" style="--project-color: <?php echo esc_attr($color); ?>;">
                        <header class="project-card-head">
                            <span class="project-swatch" aria-hidden="true"></span>

                            <h2 class="project-card-title">
                                <a href="<?php echo esc_url($link); ?>">
                                    <?php echo esc_html($term->name); ?>
                                </a>
                            </h2>

                            <span class="project-card-count">
                                <?php echo esc_html($count_label); ?>
                            </span>
                        </header>

                        <?php if ($excerpt) : ?>
                            <div class="project-card-body">
                                <p><?php echo esc_html($excerpt); ?></p>
                            </div>
                        <?php endif; ?>
                    </article>
                    <?php
                }
                ?>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php
get_footer();