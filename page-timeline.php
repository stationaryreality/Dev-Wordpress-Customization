<?php
/*
Template Name: Engineering Timeline
*/

get_header();

// Enqueue timeline styles
wp_enqueue_style(
    'timeline-styles',
    get_stylesheet_directory_uri() . '/assets/css/timeline.css',
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
        
        <!-- Conversation Weight Legend -->
        <div class="timeline-legend">
            <div class="legend-item">
                <span class="legend-dot" style="background: #fff5f5;"></span>
                <span>Milestone (251+ msgs)</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #fffbf0;"></span>
                <span>Major (101–250 msgs)</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #f8fbff;"></span>
                <span>Normal (26–100 msgs)</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ffffff;"></span>
                <span>Minor (0–25 msgs)</span>
            </div>
        </div>
    </header>

    <?php
    $records = new WP_Query([
        'post_type'      => 'record',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_key'       => 'record_number',
        'orderby'        => 'meta_value',
        'order'          => 'ASC'
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
                    <th class="col-sqlite">SQLite ID</th>
                    <th class="col-status">Status</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($records->have_posts()) : $records->the_post();

                    $record_date = get_field('create_time');
                    $review_status = get_field('review_status');
                    $message_count = intval(get_field('message_count'));
                    $acf_importance = get_field('importance');
                    
                    // Auto-calculate importance based on message count
                    if ($message_count >= 251) {
                        $calculated_importance = 'milestone';
                    } elseif ($message_count >= 101) {
                        $calculated_importance = 'major';
                    } elseif ($message_count >= 26) {
                        $calculated_importance = 'normal';
                    } else {
                        $calculated_importance = 'minor';
                    }
                    
                    // Use ACF value if it exists and is valid, otherwise use calculated
                    $final_importance = in_array($acf_importance, ['minor', 'normal', 'major', 'milestone']) ? $acf_importance : $calculated_importance;
                    
                    $projects = get_the_terms(get_the_ID(), 'project');
                ?>

                <tr class="timeline-row importance-<?php echo esc_attr($final_importance); ?>">
                    
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
                            foreach ($projects as $project) {
                                // Get the dynamic color for this project (assumes get_project_color() is in functions.php)
                                $project_color = function_exists('get_project_color') ? get_project_color($project->slug) : '#3498db';
                                
                                // Create a very light background version of the color (hex + 25 for ~15% opacity)
                                $light_bg = $project_color . '25'; 
                                
                                printf(
                                    '<a href="%s" class="project-tag" style="background: %s; color: %s; border-color: %s;">%s</a>',
                                    esc_url(get_term_link($project)),
                                    esc_attr($light_bg),
                                    esc_attr($project_color),
                                    esc_attr($project_color),
                                    esc_html($project->name)
                                );
                            }
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
                                'imported'     => 'Imported',
                                'needs_review' => 'Needs Review',
                                'reviewed'     => 'Reviewed'
                            ];
                            echo esc_html($status_labels[$review_status] ?? ucfirst($review_status));
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