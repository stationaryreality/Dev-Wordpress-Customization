<?php

/*
Template Name: Engineering Timeline
*/

get_header();

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

$records = new WP_Query([
    'post_type' => 'record',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_key' => 'create_time',
    'orderby' => 'meta_value',
    'order' => 'ASC'
]);


if ($records->have_posts()) :

?>

<table class="engineering-timeline">

<thead>

<tr>
<th>Date</th>
<th>Record</th>
<th>Category</th>
<th>Status</th>
</tr>

</thead>


<tbody>


<?php while ($records->have_posts()) : $records->the_post();


$record_date = get_field('create_time');

$category = get_field('primary_project');

$status = get_field('review_status');

?>


<tr>


<td>

<?php echo esc_html(
    date(
        'Y-m-d',
        strtotime($record_date)
    )
); ?>

</td>


<td>

<a href="<?php the_permalink(); ?>">

<?php the_title(); ?>

</a>

</td>


<td>

<?php

if ($category) {

echo get_term($category)->name;

}

?>

</td>


<td>

<?php

echo esc_html(
    ucfirst($status)
);

?>

</td>


</tr>


<?php endwhile; ?>


</tbody>


</table>


<?php endif;


wp_reset_postdata();


?>


</div>


<?php get_footer(); ?>