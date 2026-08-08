<?php
/*
Template Name: All Projects
*/

get_header();

// Get all project terms with their record counts
$projects = get_terms([
    'taxonomy' => 'project',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
]);

// Count records for each project
$projects_with_counts = [];
if (!empty($projects) && !is_wp_error($projects)) {
    foreach ($projects as $project) {
        $record_count_query = new WP_Query([
            'post_type' => 'record',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'tax_query' => [[
                'taxonomy' => 'project',
                'field' => 'term_id',
                'terms' => $project->term_id
            ]]
        ]);
        $projects_with_counts[] = [
            'term' => $project,
            'count' => $record_count_query->found_posts
        ];
        wp_reset_postdata();
    }
    
    // Sort by count (desc), then by name (asc)
    usort($projects_with_counts, function($a, $b) {
        if ($a['count'] === $b['count']) {
            return strcmp($a['term']->name, $b['term']->name);
        }
        return $b['count'] - $a['count'];
    });
}
?>

<div class="projects-archive-page">
    
    <header class="projects-header">
        <h1>All Projects</h1>
        <p>A complete index of engineering projects, sorted by activity.</p>
    </header>

    <?php if (!empty($projects_with_counts)) : ?>
    <div class="projects-grid-full">
        <?php 
        $color_palette = [
            '#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6',
            '#1abc9c', '#e67e22', '#34495e', '#16a085', '#c0392b',
        ];
        
        foreach ($projects_with_counts as $index => $project_data) : 
            $project = $project_data['term'];
            $count = $project_data['count'];
            $color = $color_palette[$index % count($color_palette)];
        ?>
<a href="<?php echo esc_url(get_term_link($project)); ?>" class="project-card">
    <div class="project-card-header" style="background: <?php echo $color; ?>;">
        <h3 class="project-card-title"><?php echo esc_html($project->name); ?></h3>
    </div>
    <div class="project-card-content">
        <?php if (!empty($project->description)) : ?>
            <p class="project-card-description"><?php echo esc_html($project->description); ?></p>
        <?php endif; ?>
        <span class="project-card-count"><?php echo $count; ?> <?php echo $count === 1 ? 'record' : 'records'; ?></span>
    </div>
</a>
        <?php endforeach; ?>
    </div>
    <?php else : ?>
        <p>No projects found.</p>
    <?php endif; ?>

</div>

<?php get_footer(); ?>