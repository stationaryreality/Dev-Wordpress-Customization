<?php
/*
Template Name: Engineering Log
*/

$eng_css_path = get_stylesheet_directory() . '/assets/css/engineering-log.css';

if (file_exists($eng_css_path)) {
    wp_enqueue_style(
        'author-child-engineering-log',
        get_stylesheet_directory_uri() . '/assets/css/engineering-log.css',
        array(),
        filemtime($eng_css_path)
    );
}

if (!function_exists('eng_get_log_count_label')) {
    function eng_get_log_count_label($count) {
        $count = (int) $count;

        return 1 === $count ? '1 log' : sprintf('%d logs', $count);
    }
}

if (!function_exists('eng_render_log_card')) {
    function eng_render_log_card($log_post) {
        $link  = get_permalink($log_post->ID);
        $title = get_the_title($log_post->ID);
        $date  = get_the_date('M j, Y', $log_post->ID);

        $has_thumbnail = has_post_thumbnail($log_post->ID);

        $thumbnail_url = '';
        $thumbnail_alt = $title;

        if ($has_thumbnail) {
            $thumbnail_url = get_the_post_thumbnail_url($log_post->ID, 'medium_large');

            $thumbnail_id  = get_post_thumbnail_id($log_post->ID);
            $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            if (empty($thumbnail_alt)) {
                $thumbnail_alt = $title;
            }
        }

        $excerpt = get_the_excerpt($log_post->ID);

        if ($excerpt) {
            $excerpt = wp_trim_words(
                wp_strip_all_tags($excerpt),
                22,
                '…'
            );
        }

        $fallback_month = get_the_date('M', $log_post->ID);
        $fallback_day   = get_the_date('d', $log_post->ID);
        ?>
        <article class="eng-card">
            <a class="eng-media" href="<?php echo esc_url($link); ?>">
                <?php if ($has_thumbnail && $thumbnail_url) : ?>
                    <img
                        src="<?php echo esc_url($thumbnail_url); ?>"
                        alt="<?php echo esc_attr($thumbnail_alt); ?>"
                    >
                <?php else : ?>
                    <span class="eng-media-fallback" aria-hidden="true">
                        <span class="eng-media-month">
                            <?php echo esc_html($fallback_month); ?>
                        </span>

                        <span class="eng-media-day">
                            <?php echo esc_html($fallback_day); ?>
                        </span>
                    </span>
                <?php endif; ?>
            </a>

            <div class="eng-card-body">
                <span class="eng-card-date">
                    <?php echo esc_html($date); ?>
                </span>

                <h3 class="eng-card-title">
                    <a href="<?php echo esc_url($link); ?>">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>

                <?php if ($excerpt) : ?>
                    <p class="eng-card-excerpt">
                        <?php echo esc_html($excerpt); ?>
                    </p>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}

/*
|--------------------------------------------------------------------------
| Query engineering logs and group them by year
|--------------------------------------------------------------------------
*/

$eng_notes = new WP_Query(
    array(
        'post_type'      => 'note',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    )
);

$eng_groups = array();

if ($eng_notes->have_posts()) {
    foreach ($eng_notes->posts as $eng_post) {
        $eng_year = get_the_date('Y', $eng_post->ID);

        if (!isset($eng_groups[$eng_year])) {
            $eng_groups[$eng_year] = array();
        }

        $eng_groups[$eng_year][] = $eng_post;
    }
}

wp_reset_postdata();

get_header();
?>

<main id="main" class="site-main eng-logs">
    <div class="eng-wrap">

        <header class="eng-header">
            <?php if (have_posts()) : ?>
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <?php the_title('<h1 class="eng-page-title">', '</h1>'); ?>

                    <?php if (trim(get_the_content())) : ?>
                        <div class="eng-intro">
                            <?php the_content(); ?>
                        </div>
                    <?php else : ?>
                        <p class="eng-intro">
                            Engineering logs, technical notes, and build documentation.
                        </p>
                    <?php endif; ?>
                    <?php
                endwhile;

                wp_reset_postdata();
                ?>
            <?php else : ?>
                <h1 class="eng-page-title">Engineering Logs</h1>

                <p class="eng-intro">
                    Engineering logs, technical notes, and build documentation.
                </p>
            <?php endif; ?>
        </header>

        <?php if (empty($eng_groups)) : ?>
            <div class="eng-empty">
                No engineering logs found.
            </div>
        <?php else : ?>

            <?php if (count($eng_groups) > 1) : ?>
                <nav class="eng-year-nav" aria-label="Engineering log years">
                    <?php foreach (array_keys($eng_groups) as $eng_year) : ?>
                        <a href="#eng-year-<?php echo esc_attr($eng_year); ?>">
                            <?php echo esc_html($eng_year); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <?php foreach ($eng_groups as $eng_year => $eng_posts) : ?>
                <section class="eng-year-section" id="eng-year-<?php echo esc_attr($eng_year); ?>">
                    <header class="eng-year-header">
                        <h2 class="eng-year-title">
                            <?php echo esc_html($eng_year); ?>
                        </h2>

                        <span class="eng-year-count">
                            <?php echo esc_html(eng_get_log_count_label(count($eng_posts))); ?>
                        </span>
                    </header>

                    <div class="eng-grid">
                        <?php foreach ($eng_posts as $eng_post) : ?>
                            <?php eng_render_log_card($eng_post); ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>