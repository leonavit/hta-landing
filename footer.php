<?php
/**
 * Footer.
 *
 * @package HTA_Landing
 */

$social_links = hta_footer_social_links();
?>
<footer class="bg-slate-950 text-slate-300 py-10 px-4 hta-rule-t">
    <div class="hta-footer-inner max-w-6xl mx-auto text-lg font-medium">
        <p class="hta-footer-copy"><?php echo esc_html(hta_mod('hta_footer_copy', '© 2026 איגוד ההיי-טק הישראלי. כל הזכויות שמורות.')); ?></p>
        <div class="hta-footer-assoc">
            <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-manufacturers.png'); ?>" alt="Manufacturers' Association of Israel">
            <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-hitech.png'); ?>" alt="Israeli High-Tech Association">
        </div>
        <div class="hta-footer-nav">
            <nav class="hta-footer-links" aria-label="<?php esc_attr_e('קישורי פוטר', 'hta-landing'); ?>">
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'hta-footer-menu',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ]);
                } else {
                    ?>
                    <a href="<?php echo esc_url(hta_page_url('takkanon')); ?>" class="hover:text-hta-1 transition-all">תקנון</a>
                    <a href="<?php echo esc_url(hta_page_url('mediniut-pratiut')); ?>" class="hover:text-hta-1 transition-all">מדיניות פרטיות</a>
                    <a href="<?php echo esc_url(hta_page_url('negishut')); ?>" class="hover:text-hta-1 transition-all">הצהרת נגישות</a>
                    <?php
                }
                ?>
            </nav>
            <?php if ($social_links) : ?>
                <nav class="hta-footer-social" aria-label="<?php esc_attr_e('רשתות חברתיות', 'hta-landing'); ?>">
                    <?php foreach ($social_links as $item) : ?>
                        <a
                            class="hta-social-link"
                            href="<?php echo esc_url($item['url']); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="<?php echo esc_attr($item['label']); ?>"
                        >
                            <?php echo hta_social_icon_svg($item['slug']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- hard-coded SVG ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</footer>
<?php if (is_front_page()) : ?>
<nav class="hta-section-rail" id="htaSectionRail" aria-label="<?php esc_attr_e('ניווט סקשנים', 'hta-landing'); ?>">
    <?php
    $hta_section_rail = [
        'hero'           => __('ראש העמוד', 'hta-landing'),
        'about'          => __('אודות', 'hta-landing'),
        'why'            => __('למה זה חשוב', 'hta-landing'),
        'topics'         => __('נושאי השבוע', 'hta-landing'),
        'events-search'  => __('חיפוש אירועים', 'hta-landing'),
        'ambassadors'    => __('שגרירים', 'hta-landing'),
        'partners'       => __('שותפים', 'hta-landing'),
        'media'          => __('מן התקשורת', 'hta-landing'),
        'submit-event'   => __('הגשת אירוע', 'hta-landing'),
    ];
    foreach ($hta_section_rail as $section_id => $section_label) :
        ?>
        <a
            href="#<?php echo esc_attr($section_id); ?>"
            class="hta-section-rail-dot"
            data-section="<?php echo esc_attr($section_id); ?>"
            aria-label="<?php echo esc_attr($section_label); ?>"
        ></a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
<button type="button" class="hta-back-top" id="htaBackTop" aria-label="חזרה לראש העמוד">
    <span class="hta-particle-field hta-particle-field--btn" data-particle-count="10" aria-hidden="true"></span>
    <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="square" stroke-linejoin="miter" d="M12 19V5M5 12l7-7 7 7"></path>
    </svg>
</button>
<?php wp_footer(); ?>
</body>
</html>
