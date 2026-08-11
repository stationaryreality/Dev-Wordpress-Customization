<?php
/*
Template Name: Documents
*/

$doc_css_path = get_stylesheet_directory() . '/assets/css/documents.css';

if (file_exists($doc_css_path)) {
    wp_enqueue_style(
        'author-child-documents',
        get_stylesheet_directory_uri() . '/assets/css/documents.css',
        array(),
        filemtime($doc_css_path)
    );
}

$doc_items = new WP_Query(
    array(
        'post_type'      => 'document',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    )
);

get_header();
?>

<main id="main" class="site-main doc-archive">
    <div class="doc-wrap">

        <header class="doc-header">
            <?php if (have_posts()) : ?>
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <?php the_title('<h1 class="doc-page-title">', '</h1>'); ?>

                    <?php if (trim(get_the_content())) : ?>
                        <div class="doc-intro">
                            <?php the_content(); ?>
                        </div>
                    <?php else : ?>
                        <p class="doc-intro">
                            Platform documentation, architecture notes, and reference materials.
                        </p>
                    <?php endif; ?>
                    <?php
                endwhile;

                wp_reset_postdata();
                ?>
            <?php else : ?>
                <h1 class="doc-page-title">Documents</h1>

                <p class="doc-intro">
                    Platform documentation, architecture notes, and reference materials.
                </p>
            <?php endif; ?>
        </header>

        <?php if (!$doc_items->have_posts()) : ?>
            <div class="doc-empty">
                No documents found.
            </div>
        <?php else : ?>
            <div class="doc-grid">
                <?php
                foreach ($doc_items->posts as $doc_post) :
                    $doc_link  = get_permalink($doc_post->ID);
                    $doc_title = get_the_title($doc_post->ID);
                    $doc_date  = get_the_date('M j, Y', $doc_post->ID);

                    $doc_excerpt = get_the_excerpt($doc_post->ID);

                    if ($doc_excerpt) {
                        $doc_excerpt = wp_trim_words(
                            wp_strip_all_tags($doc_excerpt),
                            22,
                            '…'
                        );
                    }
                    ?>
                    <article class="doc-card">
                        <header class="doc-card-top">
                            <h2 class="doc-card-title">
                                <a href="<?php echo esc_url($doc_link); ?>">
                                    <?php echo esc_html($doc_title); ?>
                                </a>
                            </h2>
                        </header>

                        <div class="doc-card-body">
                            <span class="doc-card-date">
                                <?php echo esc_html($doc_date); ?>
                            </span>

                            <?php if ($doc_excerpt) : ?>
                                <p class="doc-card-excerpt">
                                    <?php echo esc_html($doc_excerpt); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php
                endforeach;
                ?>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </div>
</main>

<?php get_footer(); ?>