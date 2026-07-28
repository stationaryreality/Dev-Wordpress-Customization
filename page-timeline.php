<?php

/*
Template Name: Engineering Timeline
*/

get_header();

// Enqueue timeline styles
wp_enqueue_style(
    'timeline-styles',
    get_template_directory_uri() . '/assets/css/timeline.css',
    [],
    '1.0'
);

?>

<div class="timeline-page">

    <header class="timeline-header">
        <h1>Engineering Development Timeline</h1>
        <p>
            This timeline captures the evolution of the engineering platform,
            from early experiments through major architectural milestones.
        </p>
    </header>

    <?php

    // ✅ Changed: Sorting by record_number instead of create_time
    $records = new WP_Query([
        'post_type' => 'record',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => 'record_number',
        'orderby' => 'meta_value',
        'order' => 'ASC'
    ]);

    if ($records->have_posts()) :

    ?>

    <div class="timeline-container">
        <table class="engineering-timeline">
            <thead>
                <tr>
                    <th class="col-id">Record ID</th>
                    <th class="col-date">Date</th>
                    <th class="col-record">Record</th>
                    <th class="col-project">Project</th>
                    <th class="col-sqlite">ID</th>
                    <th class="col-status">Status</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($records->have_posts()) : $records->the_post();

                    $record_date = get_field('create_time');
                    $importance = get_field('importance');
                    $review_status = get_field('review_status');
                    
                    // Get taxonomy terms
                    $projects = get_the_terms(get_the_ID(), 'project');
                ?>

                <tr class="timeline-row importance-<?php echo esc_attr($importance); ?>">
                    
                    <td class="col-id">
                        <?php echo esc_html(get_field('record_number')); ?>
                    </td>

                    <td class="col-date">
                        <time datetime="<?php echo esc_attr($record_date); ?>">
                            <?php echo esc_html(date('Y-m-d', strtotime($record_date))); ?>
                        </time>
                    </td>

                    <td class="col-record">
                        <a href="<?php the_permalink(); ?>" class="record-link">
                            <?php the_title(); ?>
                        </a>
                    </td>

                    <td class="col-project">
                        <?php
                        if ($projects && !is_wp_error($projects)) {
                            $project_links = [];

                            foreach ($projects as $project) {
                                $project_links[] = sprintf(
                                    '<a href="%s" class="project-tag">%s</a>',
                                    esc_url(get_term_link($project)),
                                    esc_html($project->name)
                                );
                            }

                            echo implode(' ', $project_links);

                        } else {
                            echo '<span class="no-project">—</span>';
                        }
                        ?>
                    </td>

                    <td class="col-sqlite">
                            <?php echo esc_html(get_field('sqlite_id')); ?>
                    </td>

                    <td class="col-status">
                        <span class="status-badge status-<?php echo esc_attr($review_status); ?>">
                            <?php 
                            $status_labels = [
                                'imported' => 'Imported',       // ✅ Added for your restored status
                                'needs_review' => 'Needs Review',
                                'reviewed' => 'Reviewed'
                            ];

                            echo esc_html($status_labels[$review_status] ?? $review_status);
                            ?>
                        </span>
                    </td>

                </tr>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php 
    endif;
    wp_reset_postdata();
    ?>

</div>

<?php get_footer(); ?>