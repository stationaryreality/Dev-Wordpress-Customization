<?php
/**
 * Record CPT Template
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<article <?php post_class('record-entry'); ?>>

<header class="record-header">

    <h1 class="record-title">
        <?php the_title(); ?>
    </h1>

    <?php if ($record_number = get_field('record_number')) : ?>

        <div class="record-id">
            Record ID:
            <?php echo esc_html($record_number); ?>
        </div>

    <?php endif; ?>


</header>


<div class="record-meta">

<?php if ($date = get_field('create_time')) : ?>

<p>
<strong>Date:</strong>
<?php echo esc_html(date('Y-m-d', strtotime($date))); ?>
</p>

<?php endif; ?>


<?php

$project = get_field('primary_project');

if ($project) :

?>

<p>
<strong>Category:</strong>

<?php

if (is_object($project)) {

    echo esc_html($project->name);

} else {

    echo esc_html(get_term($project)->name);

}

?>

</p>

<?php endif; ?>


</div>



<div class="record-summary">

<?php

$summary = get_field('summary');

if ($summary) {

    echo wpautop(
        esc_html($summary)
    );

} else {

    the_excerpt();

}

?>

</div>



<hr>


<section class="record-source">

<section class="record-source">

<h3>
Source Information
</h3>


<ul>

<li>
SQLite ID:
<?php echo esc_html(get_field('sqlite_id')); ?>
</li>


<li>
Original Title:
<?php echo esc_html(get_field('original_title')); ?>
</li>


<li>
Messages:
<?php echo esc_html(get_field('message_count')); ?>
</li>


<li>
Imported:
<?php echo esc_html(get_field('import_date')); ?>
</li>


</ul>


</section>


</article>