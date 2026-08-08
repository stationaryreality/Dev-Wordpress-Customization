<?php
/*
Template Name: Task Dashboard
*/

$tda_css_path = get_stylesheet_directory() . '/assets/css/task-dashboard.css';

if (file_exists($tda_css_path)) {
    wp_enqueue_style(
        'author-child-task-dashboard',
        get_stylesheet_directory_uri() . '/assets/css/task-dashboard.css',
        array(),
        filemtime($tda_css_path)
    );
}

if (!function_exists('tda_palette')) {
    function tda_palette() {
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

if (!function_exists('tda_is_valid_color')) {
    function tda_is_valid_color($color) {
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

if (!function_exists('tda_get_project_position_map')) {
    function tda_get_project_position_map() {
        static $map = null;

        if (null !== $map) {
            return $map;
        }

        $map   = array();
        $terms = get_terms(
            array(
                'taxonomy'   => 'project',
                'hide_empty' => false,
            )
        );

        if (is_wp_error($terms) || empty($terms)) {
            return $map;
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

        foreach ($terms as $index => $term) {
            $map[$term->term_id] = $index;
        }

        return $map;
    }
}

if (!function_exists('tda_get_project_color')) {
    function tda_get_project_color($term_id) {
        $term_id = (int) $term_id;
        $map     = tda_get_project_position_map();
        $index   = isset($map[$term_id]) ? $map[$term_id] : null;

        /*
         * Primary method:
         * Use the same position-based color mapper used by the homepage grid.
         */
        if (null !== $index && function_exists('get_project_color')) {
            $color = get_project_color($index);

            if (tda_is_valid_color($color)) {
                return trim($color);
            }
        }

        /*
         * Fallback if no position is available or the mapper fails.
         */
        if (function_exists('get_project_color')) {
            $color = get_project_color($term_id);

            if (tda_is_valid_color($color)) {
                return trim($color);
            }
        }

        $palette = tda_palette();

        return $palette[absint($term_id) % count($palette)];
    }
}

if (!function_exists('tda_get_project_task_groups')) {
    function tda_get_project_task_groups($status_slug) {
        $projects = get_terms(
            array(
                'taxonomy'   => 'project',
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );

        if (is_wp_error($projects) || empty($projects)) {
            return array();
        }

        $groups = array();

        foreach ($projects as $project) {
            $tasks = new WP_Query(
                array(
                    'post_type'      => 'task',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'modified',
                    'order'          => 'DESC',
                    'no_found_rows'  => true,

                    'tax_query' => array(
                        'relation' => 'AND',

                        array(
                            'taxonomy' => 'project',
                            'field'    => 'term_id',
                            'terms'    => $project->term_id,
                        ),

                        array(
                            'taxonomy' => 'task_status',
                            'field'    => 'slug',
                            'terms'    => $status_slug,
                        ),
                    ),
                )
            );

            if ($tasks->have_posts()) {
                $groups[] = array(
                    'term'  => $project,
                    'posts' => $tasks->posts,
                );
            }

            wp_reset_postdata();
        }

        return $groups;
    }
}

if (!function_exists('tda_get_task_count_label')) {
    function tda_get_task_count_label($count) {
        $count = (int) $count;

        return 1 === $count ? '1 task' : sprintf('%d tasks', $count);
    }
}

if (!function_exists('tda_balance_weighted_groups')) {
    function tda_balance_weighted_groups($groups, $base_weight = 4) {
        $items = array();

        foreach ($groups as $group) {
            $task_count   = count($group['posts']);
            $title_length = function_exists('mb_strlen')
                ? mb_strlen($group['term']->name)
                : strlen($group['term']->name);

            /*
             * Weight is intentionally simple:
             * card overhead + task count + small allowance for long project names.
             */
            $weight = $base_weight + $task_count + floor($title_length / 30);

            $items[] = array(
                'term'   => $group['term'],
                'posts'  => $group['posts'],
                'weight' => (int) $weight,
            );
        }

        /*
         * Sort heaviest first for better greedy balancing.
         */
        usort(
            $items,
            function ($a, $b) {
                if ($a['weight'] === $b['weight']) {
                    return strcasecmp($a['term']->name, $b['term']->name);
                }

                return $b['weight'] <=> $a['weight'];
            }
        );

        $columns = array(
            array(),
            array(),
        );

        $weights = array(
            0,
            0,
        );

        /*
         * Assign each item to whichever column is currently shorter.
         */
        foreach ($items as $item) {
            $target = ($weights[0] <= $weights[1]) ? 0 : 1;

            $columns[$target][] = $item;
            $weights[$target]  += $item['weight'];
        }

        /*
         * Sort each column so lighter/smaller cards appear nearer the top.
         */
        foreach ($columns as $index => $column) {
            usort(
                $column,
                function ($a, $b) {
                    if ($a['weight'] === $b['weight']) {
                        return strcasecmp($a['term']->name, $b['term']->name);
                    }

                    return $a['weight'] <=> $b['weight'];
                }
            );

            $columns[$index] = $column;
        }

        return $columns;
    }
}

if (!function_exists('tda_render_project_card')) {
    function tda_render_project_card($item, $status_key, $icon) {
        $color       = tda_get_project_color($item['term']->term_id);
        $task_count  = count($item['posts']);
        $count_label = tda_get_task_count_label($task_count);
        ?>
        <article class="tda-project-card" style="--tda-project-color: <?php echo esc_attr($color); ?>;">
            <header class="tda-project-card-head">
                <span class="tda-project-swatch" aria-hidden="true"></span>

                <h3 class="tda-project-title">
                    <?php echo esc_html($item['term']->name); ?>
                </h3>

                <span class="tda-project-count">
                    <?php echo esc_html($count_label); ?>
                </span>
            </header>

            <ul class="tda-task-list">
                <?php foreach ($item['posts'] as $task) : ?>
                    <li class="tda-task-item tda-task-item--<?php echo esc_attr($status_key); ?>">
                        <span class="tda-task-icon" aria-hidden="true">
                            <?php echo esc_html($icon); ?>
                        </span>

                        <a class="tda-task-link" href="<?php echo esc_url(get_permalink($task->ID)); ?>">
                            <?php echo esc_html(get_the_title($task->ID)); ?>
                        </a>

                        <span class="tda-task-date">
                            <?php echo esc_html(get_the_modified_date('M j, Y', $task->ID)); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </article>
        <?php
    }
}

if (!function_exists('tda_render_card_section')) {
    function tda_render_card_section($key, $label, $description, $groups, $icon) {
        $total_tasks = 0;

        foreach ($groups as $group) {
            $total_tasks += count($group['posts']);
        }

        ?>
        <section class="tda-section tda-section--<?php echo esc_attr($key); ?>">
            <header class="tda-section-header">
                <span class="tda-status-dot" aria-hidden="true"></span>

                <h2 class="tda-section-title">
                    <?php echo esc_html($label); ?>
                </h2>

                <span class="tda-section-count">
                    <?php echo esc_html(tda_get_task_count_label($total_tasks)); ?>
                </span>
            </header>

            <?php if ($description) : ?>
                <p class="tda-section-description">
                    <?php echo esc_html($description); ?>
                </p>
            <?php endif; ?>

            <?php if (empty($groups)) : ?>
                <div class="tda-empty">
                    No <?php echo esc_html(strtolower($label)); ?> found.
                </div>
            <?php else : ?>
                <?php
                $columns   = tda_balance_weighted_groups($groups, 4);
                $has_two   = !empty($columns[1]);
                $css_class = $has_two ? 'tda-columns--two' : 'tda-columns--single';
                ?>
                <div class="tda-columns <?php echo esc_attr($css_class); ?>">
                    <?php foreach ($columns as $column_index => $column_items) : ?>
                        <?php if (empty($column_items)) : ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <div class="tda-column <?php echo $has_two ? '' : 'tda-column--single'; ?>">
                            <?php
                            foreach ($column_items as $item) {
                                tda_render_project_card($item, $key, $icon);
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}

if (!function_exists('tda_render_archive_group')) {
    function tda_render_archive_group($item) {
        $color       = tda_get_project_color($item['term']->term_id);
        $task_count  = count($item['posts']);
        $count_label = tda_get_task_count_label($task_count);
        ?>
        <div class="tda-archive-group" style="--tda-project-color: <?php echo esc_attr($color); ?>;">
            <header class="tda-archive-group-head">
                <span class="tda-archive-dot" aria-hidden="true"></span>

                <h3 class="tda-archive-title">
                    <?php echo esc_html($item['term']->name); ?>
                </h3>

                <span class="tda-archive-count">
                    <?php echo esc_html($count_label); ?>
                </span>
            </header>

            <ul class="tda-archive-list">
                <?php foreach ($item['posts'] as $task) : ?>
                    <li>
                        <a href="<?php echo esc_url(get_permalink($task->ID)); ?>">
                            <?php echo esc_html(get_the_title($task->ID)); ?>
                        </a>

                        <span class="tda-archive-date">
                            <?php echo esc_html(get_the_modified_date('M j, Y', $task->ID)); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}

if (!function_exists('tda_render_archived_section')) {
    function tda_render_archived_section($groups) {
        $total_tasks = 0;

        foreach ($groups as $group) {
            $total_tasks += count($group['posts']);
        }

        ?>
        <section class="tda-section tda-section--archived">
            <header class="tda-section-header">
                <span class="tda-status-dot" aria-hidden="true"></span>

                <h2 class="tda-section-title">
                    Archived Tasks
                </h2>

                <span class="tda-section-count">
                    <?php echo esc_html(tda_get_task_count_label($total_tasks)); ?>
                </span>
            </header>

            <p class="tda-section-description">
                Retired, abandoned, replaced, or otherwise archived tasks.
            </p>

            <?php if (empty($groups)) : ?>
                <div class="tda-empty">
                    No archived tasks found.
                </div>
            <?php else : ?>
                <?php
                $columns   = tda_balance_weighted_groups($groups, 2);
                $has_two   = !empty($columns[1]);
                $css_class = $has_two ? 'tda-archive-columns--two' : 'tda-archive-columns--single';
                ?>
                <div class="tda-archive-columns <?php echo esc_attr($css_class); ?>">
                    <?php foreach ($columns as $column_index => $column_items) : ?>
                        <?php if (empty($column_items)) : ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <div class="tda-archive-column <?php echo $has_two ? '' : 'tda-archive-column--single'; ?>">
                            <?php
                            foreach ($column_items as $item) {
                                tda_render_archive_group($item);
                            }
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
    }
}

$tda_active_groups    = tda_get_project_task_groups('active');
$tda_completed_groups = tda_get_project_task_groups('completed');
$tda_archived_groups  = tda_get_project_task_groups('archived');

$tda_uncategorized = new WP_Query(
    array(
        'post_type'      => 'task',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'no_found_rows'  => true,

        'tax_query' => array(
            array(
                'taxonomy' => 'project',
                'operator' => 'NOT EXISTS',
            ),
        ),
    )
);

$tda_uncategorized_count = count($tda_uncategorized->posts);

get_header();
?>

<main id="main" class="site-main tda-dashboard">
    <div class="tda-wrap">

        <header class="tda-header">
            <?php if (have_posts()) : ?>
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <?php the_title('<h1 class="tda-page-title">', '</h1>'); ?>

                    <?php if (trim(get_the_content())) : ?>
                        <div class="tda-intro">
                            <?php the_content(); ?>
                        </div>
                    <?php else : ?>
                        <p class="tda-intro">
                            Active, completed, and archived development tasks organized by project.
                        </p>
                    <?php endif; ?>
                    <?php
                endwhile;

                wp_reset_postdata();
                ?>
            <?php else : ?>
                <h1 class="tda-page-title">Development Tasks</h1>

                <p class="tda-intro">
                    Active, completed, and archived development tasks organized by project.
                </p>
            <?php endif; ?>
        </header>

        <?php
        tda_render_card_section(
            'active',
            'Active Tasks',
            'Tasks currently in progress.',
            $tda_active_groups,
            '□'
        );

        tda_render_card_section(
            'completed',
            'Completed Tasks',
            'Finished tasks kept for documentation and reference.',
            $tda_completed_groups,
            '✓'
        );

        tda_render_archived_section($tda_archived_groups);
        ?>

        <?php if ($tda_uncategorized->have_posts()) : ?>
            <section class="tda-section tda-section--uncategorized">
                <header class="tda-section-header">
                    <span class="tda-status-dot" aria-hidden="true"></span>

                    <h2 class="tda-section-title">
                        Uncategorized Tasks
                    </h2>

                    <span class="tda-section-count">
                        <?php echo esc_html(tda_get_task_count_label($tda_uncategorized_count)); ?>
                    </span>
                </header>

                <p class="tda-section-description">
                    Tasks that are not currently assigned to a project.
                </p>

                <div class="tda-columns tda-columns--single">
                    <div class="tda-column tda-column--single">
                        <article class="tda-project-card" style="--tda-project-color: #94a3b8;">
                            <header class="tda-project-card-head">
                                <span class="tda-project-swatch" aria-hidden="true"></span>

                                <h3 class="tda-project-title">
                                    No Project Assigned
                                </h3>

                                <span class="tda-project-count">
                                    <?php echo esc_html(tda_get_task_count_label($tda_uncategorized_count)); ?>
                                </span>
                            </header>

                            <ul class="tda-task-list">
                                <?php
                                while ($tda_uncategorized->have_posts()) :
                                    $tda_uncategorized->the_post();
                                    ?>
                                    <li class="tda-task-item tda-task-item--uncategorized">
                                        <span class="tda-task-icon" aria-hidden="true">•</span>

                                        <a class="tda-task-link" href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a>

                                        <span class="tda-task-date">
                                            <?php echo esc_html(get_the_modified_date('M j, Y')); ?>
                                        </span>
                                    </li>
                                    <?php
                                endwhile;
                                ?>
                            </ul>
                        </article>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </div>
</main>

<?php get_footer(); ?>