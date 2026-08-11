<?php
/**
 * Project Taxonomy
 *
 * Shows the development history and related content
 * for a single engineering project.
 */

get_header();

$project = get_queried_object();

?>

<div class="project-page">

    <header class="project-header">

        <h1><?php echo esc_html($project->name); ?></h1>

        <?php if (!empty($project->description)) : ?>

            <div class="project-description">

                <?php echo wpautop($project->description); ?>

            </div>

        <?php endif; ?>

    </header>

    <section class="project-timeline">

        <h2>Timeline</h2>

        <?php

        $timeline = new WP_Query([
            'post_type'      => 'record',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => 'create_time',
            'order'          => 'ASC',
            'tax_query' => [
                [
                    'taxonomy' => 'project',
                    'field'    => 'term_id',
                    'terms'    => $project->term_id
                ]
            ]
        ]);

        ?>

        <?php if ($timeline->have_posts()) : ?>

            <table class="project-timeline-table">

                <thead>

                    <tr>

                        <th>Date</th>

                        <th>Record</th>

                    </tr>

                </thead>

                <tbody>

                <?php while ($timeline->have_posts()) : $timeline->the_post(); ?>

                    <tr>

                        <td>

                            <?php echo esc_html(get_field('create_time')); ?>

                        </td>

                        <td>

                            <a href="<?php the_permalink(); ?>">

                                <?php the_title(); ?>

                            </a>

                        </td>

                    </tr>

                <?php endwhile; ?>

                </tbody>

            </table>

        <?php else : ?>

            <p>No timeline records yet.</p>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </section>

    <?php

    $content_types = [
        'article'  => 'Articles',
        'update'   => 'Updates',
        'document' => 'Documentation'
    ];

    foreach ($content_types as $type => $label) :

    ?>

    <section class="project-section">

        <h2><?php echo esc_html($label); ?></h2>

        <?php

        $query = new WP_Query([
            'post_type'      => $type,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query' => [
                [
                    'taxonomy' => 'project',
                    'field'    => 'term_id',
                    'terms'    => $project->term_id
                ]
            ]
        ]);

        ?>

        <?php if ($query->have_posts()) : ?>

            <ul>

            <?php while ($query->have_posts()) : $query->the_post(); ?>

                <li>

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </li>

            <?php endwhile; ?>

            </ul>

        <?php else : ?>

            <p>No <?php echo strtolower($label); ?> yet.</p>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </section>

    <?php endforeach; ?>

    <?php

    /*
    |--------------------------------------------------------------------------
    | ACTIVE TASKS
    |--------------------------------------------------------------------------
    */

    $active_tasks = new WP_Query([
        'post_type'      => 'task',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',

        'tax_query' => [

            'relation' => 'AND',

            [
                'taxonomy' => 'project',
                'field'    => 'term_id',
                'terms'    => $project->term_id
            ],

            [
                'taxonomy' => 'task_status',
                'field'    => 'slug',
                'terms'    => 'active'
            ]

        ]

    ]);

    ?>

    <section class="project-section">

        <h2>Active Tasks</h2>

        <?php if ($active_tasks->have_posts()) : ?>

            <ul>

            <?php while ($active_tasks->have_posts()) : $active_tasks->the_post(); ?>

                <li>

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </li>

            <?php endwhile; ?>

            </ul>

        <?php else : ?>

            <p>No active tasks.</p>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </section>

    <?php

    /*
    |--------------------------------------------------------------------------
    | COMPLETED TASKS
    |--------------------------------------------------------------------------
    */

    $completed_tasks = new WP_Query([
        'post_type'      => 'task',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',

        'tax_query' => [

            'relation' => 'AND',

            [
                'taxonomy' => 'project',
                'field'    => 'term_id',
                'terms'    => $project->term_id
            ],

            [
                'taxonomy' => 'task_status',
                'field'    => 'slug',
                'terms'    => 'completed'
            ]

        ]

    ]);

    ?>

    <section class="project-section">

        <h2>Completed Tasks</h2>

        <?php if ($completed_tasks->have_posts()) : ?>

            <ul>

            <?php while ($completed_tasks->have_posts()) : $completed_tasks->the_post(); ?>

                <li>

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </li>

            <?php endwhile; ?>

            </ul>

        <?php else : ?>

            <p>No completed tasks.</p>

        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </section>

</div>

<?php get_footer(); ?>