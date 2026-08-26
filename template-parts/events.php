<?php
/**
 * Events search — CPT hta_event.
 *
 * @package HTA_Landing
 */

$terms  = get_terms(['taxonomy' => 'hta_event_cat', 'hide_empty' => false]);
$events = new WP_Query([
    'post_type'      => 'hta_event',
    'posts_per_page' => 50,
    'post_status'    => 'publish',
    'orderby'        => 'meta_value',
    'meta_key'       => '_hta_date',
    'order'          => 'ASC',
]);

$cities = [];
if ($events->have_posts()) {
    foreach ($events->posts as $event_post) {
        $loc = trim((string) hta_meta($event_post->ID, '_hta_location'));
        if ('' !== $loc) {
            $cities[$loc] = $loc;
        }
    }
    natcasesort($cities);
}
?>
<section id="events-search" class="py-20 px-4 bg-slate-900/40 hta-rule-t scroll-mt-20">
    <div class="max-w-6xl mx-auto">
        <div class="text-center max-w-2xl mx-auto mb-10" data-reveal>
            <h2 class="hta-section-title font-extrabold mb-3 text-white"><?php echo esc_html(hta_mod('hta_events_title', 'מנוע חיפוש אירועים')); ?></h2>
            <p class="text-slate-400 font-light"><?php echo esc_html(hta_mod('hta_events_subtitle', 'הקלידו מילת מפתח או בחרו קטגוריה כדי לסנן את האירועים בזמן אמת.')); ?></p>
        </div>

        <div class="hta-events-filters flex flex-col md:flex-row gap-4 justify-center max-w-5xl mx-auto mb-12">
            <input type="text" id="searchInput" placeholder="חפש לפי שם אירוע, נושא או מיקום (למשל: AI, סייבר, תל אביב)..." class="px-5 py-4 bg-slate-950 border border-slate-700 text-white flex-grow focus:outline-none focus:border-hta-1 placeholder:text-slate-500 shadow-inner">
            <select id="categoryFilter" class="hta-select px-5 py-4 bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-hta-1 shadow-inner">
                <option value="all">כל הקטגוריות</option>
                <?php if (! is_wp_error($terms)) : ?>
                    <?php foreach ($terms as $term) : ?>
                        <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <select id="cityFilter" class="hta-select px-5 py-4 bg-slate-950 border border-slate-700 text-white focus:outline-none focus:border-hta-1 shadow-inner">
                <option value="all">כל הערים</option>
                <?php foreach ($cities as $city) : ?>
                    <option value="<?php echo esc_attr($city); ?>"><?php echo esc_html($city); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php
        $mobile_cols  = hta_mod('hta_events_mobile_cols', '1');
        $desktop_cols = hta_mod('hta_events_desktop_cols', '4');
        $page_size    = hta_mod('hta_events_page_size', '4');
        $grid_classes = [
            'hta-events-grid',
            'grid',
            'grid-cols-1',
            'gap-6',
            '2' === $mobile_cols ? 'is-mobile-2' : '',
            '3' === $desktop_cols ? 'is-desktop-3' : 'is-desktop-4',
        ];
        ?>
        <div
            id="eventsGrid"
            class="<?php echo esc_attr(implode(' ', array_filter($grid_classes))); ?>"
            data-page-size="<?php echo esc_attr($page_size); ?>"
        >
            <?php if ($events->have_posts()) : ?>
                <?php while ($events->have_posts()) : $events->the_post(); ?>
                    <?php
                    $id       = get_the_ID();
                    $cat      = hta_meta($id, '_hta_category');
                    $location = hta_meta($id, '_hta_location');
                    $link     = hta_meta($id, '_hta_external_link');
                    $term     = $cat ? get_term_by('slug', $cat, 'hta_event_cat') : null;
                    $label    = ($term && ! is_wp_error($term)) ? $term->name : $cat;
                    $cat_bg   = ($term && ! is_wp_error($term)) ? hta_event_cat_bg($term) : '';
                    $cat_fg   = $cat_bg ? hta_contrast_text_color($cat_bg) : '';
                    ?>
                    <article class="event-card bg-slate-900 border border-slate-800 flex flex-col justify-between hover:border-hta-1/50" data-title="<?php echo esc_attr(hta_event_search_blob(get_post())); ?>" data-category="<?php echo esc_attr($cat ?: 'all'); ?>" data-city="<?php echo esc_attr($location); ?>">
                        <div class="hta-event-logo<?php echo has_post_thumbnail() ? '' : ' is-placeholder'; ?>">
                            <?php
                            if (has_post_thumbnail()) {
                                the_post_thumbnail('hta-event', [
                                    'alt' => esc_attr(get_the_title()),
                                ]);
                            } else {
                                ?>
                                <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-hta.png'); ?>" alt="" aria-hidden="true">
                                <?php
                            }
                            ?>
                        </div>
                        <div class="hta-event-body p-6 flex flex-col justify-between flex-grow">
                            <div>
                                <?php if ($label) : ?>
                                    <span
                                        class="hta-event-cat"
                                        data-cat="<?php echo esc_attr($cat); ?>"
                                        <?php if ($cat_bg) : ?>
                                            style="background-color: <?php echo esc_attr($cat_bg); ?>; color: <?php echo esc_attr($cat_fg); ?>;"
                                        <?php endif; ?>
                                    ><?php echo esc_html($label); ?></span>
                                <?php endif; ?>
                                <h3 class="text-xl font-bold mt-4 mb-2 text-white"><?php the_title(); ?></h3>
                                <p class="text-slate-400 text-sm font-light mb-4"><?php echo esc_html(wp_strip_all_tags(get_the_content())); ?></p>
                            </div>
                            <div class="hta-event-foot pt-4 border-t border-slate-800 flex justify-between items-center text-slate-500">
                                <span class="hta-event-city"><?php echo $location ? '📍 ' . esc_html($location) : ''; ?></span>
                                <?php if ($link) : ?>
                                    <a href="<?php echo hta_esc_link($link); ?>" class="hta-event-link" <?php echo (0 === strpos($link, 'http')) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>לפרטים והרשמה ←</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>

        <?php if ('all' !== $page_size) : ?>
            <div class="hta-events-more-wrap mt-10 text-center">
                <button type="button" id="eventsLoadMore" class="hta-btn px-8 py-3.5" hidden>
                    <?php esc_html_e('טען עוד', 'hta-landing'); ?>
                </button>
            </div>
        <?php endif; ?>

        <div id="noResults" class="<?php echo $events->have_posts() ? 'hidden' : ''; ?> text-center py-12 text-slate-500">
            לא נמצאו אירועים התואמים את החיפוש. נסה מילות מפתח אחרות.
        </div>
    </div>
</section>
