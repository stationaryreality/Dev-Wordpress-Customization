<?php
/**
 * Template Name: Projects Preview (All Treatments)
 * Template Post Type: page
 *
 * Temporary preview page for comparing project card treatments.
 * After choosing a final design, this file should be reduced to only the chosen treatment.
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

if (!function_exists('dev_projects_preview_palette')) {
    function dev_projects_preview_palette() {
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

if (!function_exists('dev_projects_preview_color')) {
    function dev_projects_preview_color($index, $item = null) {
        $color = '';

        if (function_exists('get_project_color')) {
            $color = get_project_color($index);

            if ((!is_string($color) || '' === trim($color)) && !empty($item->term_id)) {
                $color = get_project_color($item->term_id);
            }
        }

        if (!is_string($color) || '' === trim($color)) {
            $palette = dev_projects_preview_palette();
            $color   = $palette[$index % count($palette)];
        }

        $color = trim($color);

        if (!preg_match('/^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color)) {
            $palette = dev_projects_preview_palette();
            $color   = $palette[$index % count($palette)];
        }

        return $color;
    }
}

if (!function_exists('dev_projects_preview_get_sorted_terms')) {
    function dev_projects_preview_get_sorted_terms() {
        $terms = get_terms(
            array(
                'taxonomy'   => 'project',
                'hide_empty' => false,
            )
        );

        if (is_wp_error($terms) || empty($terms)) {
            return array();
        }

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

if (!function_exists('dev_projects_preview_prepare_items')) {
    function dev_projects_preview_prepare_items($terms, $limit = 8) {
        if (!empty($terms)) {
            $items          = array();
            $selected_terms = $limit > 0 ? array_slice($terms, 0, $limit) : $terms;

            foreach ($selected_terms as $term) {
                $link = get_term_link($term);

                if (is_wp_error($link)) {
                    $link = '#';
                }

                $items[] = (object) array(
                    'term_id'     => $term->term_id,
                    'name'        => $term->name,
                    'count'       => (int) $term->count,
                    'description' => $term->description,
                    'link'        => $link,
                );
            }

            return $items;
        }

        /*
         * Sample fallback so you can see the card styles even if no project terms exist.
         * This fallback can be removed in the final version.
         */
        return array(
            (object) array(
                'term_id'     => 0,
                'name'        => 'Site Tools',
                'count'       => 24,
                'description' => 'Example fallback item used only when no project terms are found.',
                'link'        => '#',
            ),
            (object) array(
                'term_id'     => 0,
                'name'        => 'Engineering Logs',
                'count'       => 19,
                'description' => 'Example fallback item used only when no project terms are found.',
                'link'        => '#',
            ),
            (object) array(
                'term_id'     => 0,
                'name'        => 'Documents',
                'count'       => 16,
                'description' => 'Example fallback item used only when no project terms are found.',
                'link'        => '#',
            ),
            (object) array(
                'term_id'     => 0,
                'name'        => 'Timeline System',
                'count'       => 14,
                'description' => 'Example fallback item used only when no project terms are found.',
                'link'        => '#',
            ),
            (object) array(
                'term_id'     => 0,
                'name'        => 'WordPress Customization',
                'count'       => 11,
                'description' => 'Example fallback item used only when no project terms are found.',
                'link'        => '#',
            ),
            (object) array(
                'term_id'     => 0,
                'name'        => 'Import Pipeline',
                'count'       => 8,
                'description' => 'Example fallback item used only when no project terms are found.',
                'link'        => '#',
            ),
        );
    }
}

if (!function_exists('dev_projects_preview_card')) {
    function dev_projects_preview_card($item, $index, $variant) {
        $color       = dev_projects_preview_color($index, $item);
        $link        = !empty($item->link) ? $item->link : '#';
        $count       = isset($item->count) ? (int) $item->count : 0;
        $count_label = 1 === $count ? '1 record' : sprintf('%d records', $count);
        $excerpt     = '';

        if (!empty($item->description)) {
            $excerpt = wp_trim_words(wp_strip_all_tags($item->description), 18, '…');
        }

        $card_class = 'project-card project-card--' . $variant;
        ?>
        <article class="<?php echo esc_attr($card_class); ?>" style="--project-color: <?php echo esc_attr($color); ?>;">
            <?php if ('grid' === $variant) : ?>
                <div class="project-swatches" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            <?php endif; ?>

            <header class="project-card-head">
                <?php if ('swatch' === $variant || 'hybrid' === $variant) : ?>
                    <span class="project-swatch" aria-hidden="true"></span>
                <?php endif; ?>

                <h3 class="project-card-title">
                    <a href="<?php echo esc_url($link); ?>">
                        <?php echo esc_html($item->name); ?>
                    </a>
                </h3>

                <span class="project-card-count"><?php echo esc_html($count_label); ?></span>
            </header>

            <?php if ($excerpt) : ?>
                <div class="project-card-body">
                    <p><?php echo esc_html($excerpt); ?></p>
                </div>
            <?php endif; ?>
        </article>
        <?php
    }
}

if (!function_exists('dev_projects_preview_section')) {
    function dev_projects_preview_section($key, $title, $description, $items) {
        ?>
        <section class="projects-preview-section" id="option-<?php echo esc_attr($key); ?>">
            <header>
                <h2><?php echo esc_html($title); ?></h2>
                <p><?php echo esc_html($description); ?></p>
            </header>

            <div class="projects-grid">
                <?php foreach ($items as $index => $item) : ?>
                    <?php dev_projects_preview_card($item, $index, $key); ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php
    }
}

$dev_projects_terms = dev_projects_preview_get_sorted_terms();
$dev_projects_items = dev_projects_preview_prepare_items($dev_projects_terms, 8);

get_header();
?>

<main id="main" class="site-main projects-preview">
    <div class="projects-preview-wrap">
        <header class="projects-preview-header">
            <h1>Projects Archive — Card Treatment Preview</h1>
            <p>
                This is a temporary comparison page. It shows four project-card treatments using the
                same project data. Pick the one that feels right, then we’ll remove the others and
                make the chosen version the final Projects archive design.
            </p>

            <ul class="projects-preview-nav">
                <li><a href="#option-swatch">A. Compact swatch</a></li>
                <li><a href="#option-rail">B. Left rail + tick</a></li>
                <li><a href="#option-grid">C. Swatch grid</a></li>
                <li><a href="#option-hybrid">D. Recommended hybrid</a></li>
            </ul>
        </header>

        <?php if (empty($dev_projects_terms)) : ?>
            <div class="projects-preview-notice">
                No project terms were found, so sample cards are being shown for preview purposes.
            </div>
        <?php endif; ?>

        <?php
        dev_projects_preview_section(
            'swatch',
            'Option A — Compact swatch beside title',
            'A small color swatch identifies the project without dominating the card. Cleanest and most documentation-like.',
            $dev_projects_items
        );

        dev_projects_preview_section(
            'rail',
            'Option B — Left accent rail with small tick',
            'A thin left border and small top tick keep the color more visible while still avoiding a full header bar.',
            $dev_projects_items
        );

        dev_projects_preview_section(
            'grid',
            'Option C — Small swatch grid',
            'A small row of swatch tiles gives a more designed, paint-chip feel. This one is the most distinctive.',
            $dev_projects_items
        );

        dev_projects_preview_section(
            'hybrid',
            'Option D — Recommended hybrid',
            'Compact swatch plus a subtle colored left edge and tinted count badge. Best balance of clean and identifiable.',
            $dev_projects_items
        );
        ?>
    </div>
</main>

<?php
get_footer();