<?php
/**
 * Default page template.
 *
 * @package HTA_Landing
 */

get_header();
?>
<main class="pt-32 pb-20 px-4">
    <div class="max-w-3xl mx-auto">
        <?php while (have_posts()) : the_post(); ?>
            <h1 class="hta-section-title font-extrabold text-white mb-8"><?php the_title(); ?></h1>
            <div class="hta-page-content text-slate-300 leading-relaxed font-light">
                <?php the_content(); ?>
            </div>
        <?php endwhile; ?>
    </div>
</main>
<?php
get_footer();
