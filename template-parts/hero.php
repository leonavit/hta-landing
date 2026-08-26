<?php
/**
 * Hero.
 *
 * @package HTA_Landing
 */

$logo_svg = HTA_DIR . '/assets/images/logo-hta.svg';
$video    = hta_hero_video();
?>
<header class="hta-hero bg-slate-950 text-white pt-36 pb-24 px-4 text-center relative overflow-hidden" id="hero">
    <?php if ('youtube' === $video['type'] && $video['youtube']) : ?>
        <div class="hta-hero-video hta-hero-video-yt" aria-hidden="true">
            <iframe
                src="<?php echo esc_url('https://www.youtube-nocookie.com/embed/' . $video['youtube'] . '?autoplay=1&mute=1&controls=0&loop=1&playlist=' . $video['youtube'] . '&playsinline=1&rel=0&modestbranding=1'); ?>"
                title=""
                allow="autoplay; encrypted-media"
                tabindex="-1"
            ></iframe>
        </div>
    <?php elseif (! empty($video['src'])) : ?>
        <video class="hta-hero-video" autoplay muted loop playsinline preload="metadata" aria-hidden="true">
            <source src="<?php echo esc_url($video['src']); ?>" type="video/mp4">
        </video>
    <?php endif; ?>
    <div class="hta-hero-video-overlay pointer-events-none"></div>
    <div class="absolute inset-0 pointer-events-none hta-hero-glow"></div>
    <div class="max-w-4xl mx-auto relative z-10">
        <div class="hta-hero-logo-wrap is-hidden mx-auto mb-6" hidden>
            <?php
            if (is_readable($logo_svg)) {
                // Local theme SVG — each path is a logo dot for GSAP.
                echo file_get_contents($logo_svg); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
            ?>
        </div>
        <h1 class="hta-hero-title font-extrabold mt-6 tracking-tight leading-tight <?php echo esc_attr(hta_text_fill_class('hta_hero_title')); ?>">
            <?php echo esc_html(hta_mod('hta_hero_title', 'שבוע ההיי-טק הישראלי 2026')); ?>
        </h1>
        <p class="hta-hero-tagline mt-4 tracking-wide <?php echo esc_attr(hta_text_fill_class('hta_hero_tagline')); ?>">
            <?php echo esc_html(hta_mod('hta_hero_tagline', 'מחברים אנשים. טכנולוגיה. עתיד.')); ?>
        </p>
        <span class="hta-hero-kicker px-4 py-1.5 mt-4"><?php echo esc_html(hta_mod('hta_hero_kicker', '1-6.11.2026 | איגוד ההיי-טק הישראלי')); ?></span>
        <p class="hta-hero-subtitle text-slate-300 mt-4 max-w-2xl mx-auto">
            <?php echo esc_html(hta_mod('hta_hero_subtitle', 'שישה ימים . שישה עולמות תוכן . מאות אירועים. סיפור אחד של ההיי-טק הישראלי')); ?>
        </p>
        <div class="hta-hero-ctas mt-8 flex flex-row gap-3 sm:gap-4 justify-center items-stretch">
            <a href="#events-search" class="hta-btn flex-1 sm:flex-none px-4 sm:px-8 py-3.5 text-center"><?php echo esc_html(hta_mod('hta_hero_cta_primary', 'חיפוש אירועים בשבוע')); ?></a>
            <a href="#submit-event" class="hta-btn-secondary flex-1 sm:flex-none px-4 sm:px-8 py-3.5 text-center"><?php echo esc_html(hta_mod('hta_hero_cta_secondary', 'הגשת אירוע משלכם')); ?></a>
        </div>
    </div>
</header>
