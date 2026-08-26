<?php
/**
 * 404.
 *
 * @package HTA_Landing
 */

get_header();
?>
<main class="pt-36 pb-24 px-4 text-center">
    <h1 class="hta-section-title font-extrabold text-white mb-4">העמוד לא נמצא</h1>
    <p class="text-slate-400 mb-8">ייתכן שהכתובת השתנתה.</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block bg-hta-1 text-slate-950 font-bold px-8 py-3.5">חזרה לדף הבית</a>
</main>
<?php
get_footer();
