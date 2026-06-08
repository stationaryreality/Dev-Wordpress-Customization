<?php
/*
Template Name: Task Dashboard
*/

get_header();

$projects = get_terms([
    'taxonomy'   => 'project',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
]);

?>

<main class="task-dashboard">

    <h1 class="page-title">
        Development Tasks
    </h1>

    <p class="page-description">
        Active, completed, and archived development tasks organized by project.
    </p>

    <?php

    /*
    |--------------------------------------------------------------------------
    | ACTIVE TASKS
    |--------------------------------------------------------------------------
    */

    ?>

    <section class="task-section">

        <h2 class="page-section-title">
            Active Tasks
        </h2>

        <?php

        foreach ($projects as $project) :

            $tasks = new WP_Query([
                'post_type'      => 'task',
                'posts_per_page' => -1,
                'orderby'        => 'modified',
                'order'          => 'DESC',

                'tax_query' => [

                    'relation' => 'AND',

                    [
                        'taxonomy' => 'project',
                        'field'    => 'term_id',
                        'terms'    => $project->term_id,
                    ],

                    [
                        'taxonomy' => 'status',
                        'field'    => 'slug',
                        'terms'    => 'active',
                    ]

                ]

            ]);

            if (!$tasks->have_posts()) {
                continue;
            }

            ?>

            <div class="task-project-group">

                <h3>
                    <?php echo esc_html($project->name); ?>
                </h3>

                <ul class="homepage-list">

                    <?php while ($tasks->have_posts()) :
                        $tasks->the_post(); ?>

                        <li>

                            <a href="<?php the_permalink(); ?>">

                                □ <?php the_title(); ?>

                            </a>

                        </li>

                    <?php endwhile; ?>

                </ul>

            </div>

            <?php

            wp_reset_postdata();

        endforeach;

        ?>

    </section>

    <hr class="homepage-divider">

    <?php

    /*
    |--------------------------------------------------------------------------
    | COMPLETED TASKS
    |--------------------------------------------------------------------------
    */

    ?>

    <section class="task-section">

        <h2 class="page-section-title">
            Completed Tasks
        </h2>

        <?php

        foreach ($projects as $project) :

            $tasks = new WP_Query([
                'post_type'      => 'task',
                'posts_per_page' => -1,
                'orderby'        => 'modified',
                'order'          => 'DESC',

                'tax_query' => [

                    'relation' => 'AND',

                    [
                        'taxonomy' => 'project',
                        'field'    => 'term_id',
                        'terms'    => $project->term_id,
                    ],

                    [
                        'taxonomy' => 'status',
                        'field'    => 'slug',
                        'terms'    => 'completed',
                    ]

                ]

            ]);

            if (!$tasks->have_posts()) {
                continue;
            }

            ?>

            <div class="task-project-group">

                <h3>
                    <?php echo esc_html($project->name); ?>
                </h3>

                <ul class="homepage-list">

                    <?php while ($tasks->have_posts()) :
                        $tasks->the_post(); ?>

                        <li>

                            <a href="<?php the_permalink(); ?>">

                                ✓ <?php the_title(); ?>

                            </a>

                        </li>

                    <?php endwhile; ?>

                </ul>

            </div>

            <?php

            wp_reset_postdata();

        endforeach;

        ?>

    </section>

    <hr class="homepage-divider">

    <?php

    /*
    |--------------------------------------------------------------------------
    | UNCATEGORIZED
    |--------------------------------------------------------------------------
    */

    $uncategorized = new WP_Query([

        'post_type'      => 'task',
        'posts_per_page' => -1,
        'orderby'        => 'modified',
        'order'          => 'DESC',

        'tax_query' => [

            [
                'taxonomy' => 'project',
                'operator' => 'NOT EXISTS',
            ]

        ]

    ]);

    if ($uncategorized->have_posts()) :
    ?>

        <section class="task-section">

            <h2 class="page-section-title">
                Uncategorized Tasks
            </h2>

            <ul class="homepage-list">

                <?php while ($uncategorized->have_posts()) :
                    $uncategorized->the_post(); ?>

                    <li>

                        <a href="<?php the_permalink(); ?>">

                            • <?php the_title(); ?>

                        </a>

                    </li>

                <?php endwhile; ?>

            </ul>

        </section>

    <?php
    endif;

    wp_reset_postdata();
    ?>

</main>

<?php get_footer(); ?>