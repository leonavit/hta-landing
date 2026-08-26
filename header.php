<?php
/**
 * Header — RTL, native wp_head.
 *
 * @package HTA_Landing
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="scroll-smooth">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-slate-950 text-slate-100 antialiased'); ?>>
<?php wp_body_open(); ?>

<nav class="hta-site-nav fixed top-0 left-0 right-0 z-50 bg-transparent">
    <div class="hta-site-nav-inner max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 items-center">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="hta-nav-brand flex items-center">
            <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-hta.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="hta-nav-logo">
        </a>

        <div class="hidden lg:flex items-center gap-1 font-medium text-white hta-nav">
            <a href="#about" class="nav-link" data-target="about">אודות</a>
            <a href="#why" class="nav-link" data-target="why">למה זה חשוב</a>
            <a href="#topics" class="nav-link" data-target="topics">נושאי השבוע</a>
            <a href="#events-search" class="nav-link" data-target="events-search">חיפוש אירועים</a>
            <a href="#ambassadors" class="nav-link" data-target="ambassadors">שגרירים</a>
            <a href="#partners" class="nav-link" data-target="partners">שותפים</a>
            <a href="#media" class="nav-link" data-target="media">מן התקשורת</a>
        </div>

        <a href="#submit-event" class="hta-btn hta-nav-cta nav-link shrink-0 px-5 py-2.5 text-base whitespace-nowrap" data-target="submit-event">הגשת אירוע</a>
        <button id="menuBtn" class="hta-nav-toggle hta-hamburger-btn lg:hidden focus:outline-none" type="button" aria-label="פתח תפריט" aria-expanded="false" aria-controls="mobileMenu">
            <?php hta_render_hamburger_icon('nav'); ?>
        </button>
    </div>
</nav>

<div id="mobileMenu" class="hta-mobile-menu fixed inset-0 z-50 flex flex-col justify-center items-center opacity-0 pointer-events-none lg:hidden">
    <button id="closeBtn" class="hta-mobile-close hta-hamburger-btn" type="button" aria-label="סגור תפריט">
        <?php hta_render_hamburger_icon('close'); ?>
    </button>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="hta-mobile-logo-link">
        <img src="<?php echo esc_url(HTA_URI . '/assets/images/logo-hta.png'); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="hta-mobile-logo">
    </a>
    <div class="hta-mobile-nav font-semibold text-white text-center">
        <a href="#about" class="mobile-link">אודות</a>
        <a href="#why" class="mobile-link">למה זה חשוב</a>
        <a href="#topics" class="mobile-link">נושאי השבוע</a>
        <a href="#events-search" class="mobile-link">חיפוש אירועים</a>
        <a href="#ambassadors" class="mobile-link">שגרירים</a>
        <a href="#partners" class="mobile-link">שותפים</a>
        <a href="#media" class="mobile-link">מן התקשורת</a>
        <a href="#submit-event" class="mobile-link hta-mobile-submit nav-link" data-target="submit-event">הגשת אירוע</a>
    </div>
</div>
