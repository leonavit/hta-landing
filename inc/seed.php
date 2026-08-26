<?php
/**
 * One-time demo content so the landing is populated after activation.
 *
 * @package HTA_Landing
 */

if (! defined('ABSPATH')) {
    exit;
}

function hta_seed_demo_content(): void
{
    if (get_option('hta_demo_seeded')) {
        return;
    }

    $categories = [
        'ai'    => 'בינה מלאכותית (AI)',
        'cyber' => 'סייבר וטכנולוגיה',
        'vc'    => 'השקעות והון סיכון',
    ];

    foreach ($categories as $slug => $name) {
        if (! term_exists($slug, 'hta_event_cat')) {
            wp_insert_term($name, 'hta_event_cat', ['slug' => $slug]);
        }
    }

    $events = [
        [
            'title'    => 'פסגת ה-AI השנתית',
            'content'  => 'כנס ענק בהשתתפות בכירים בתעשיית הבינה המלאכותית העולמית.',
            'category' => 'ai',
            'location' => 'תל אביב',
            'date'     => '2026-11-02',
        ],
        [
            'title'    => 'עתיד אבטחת הסייבר',
            'content'  => 'דיונים על האתגרים הביטחוניים והטכנולוגיים החדשים ביותר בעולם הסייבר.',
            'category' => 'cyber',
            'location' => 'הרצליה',
            'date'     => '2026-11-01',
        ],
        [
            'title'    => 'זירת המשקיעים וה-VC',
            'content'  => 'איך מגייסים הון וקרנות בתקופה הנוכחית? טיפים ממנהלי קרנות מובילים.',
            'category' => 'vc',
            'location' => 'ירושלים',
            'date'     => '2026-11-04',
        ],
    ];

    foreach ($events as $event) {
        $id = wp_insert_post([
            'post_type'    => 'hta_event',
            'post_status'  => 'publish',
            'post_title'   => $event['title'],
            'post_content' => $event['content'],
        ]);
        if ($id && ! is_wp_error($id)) {
            update_post_meta($id, '_hta_date', $event['date']);
            update_post_meta($id, '_hta_category', $event['category']);
            update_post_meta($id, '_hta_location', $event['location']);
            update_post_meta($id, '_hta_external_link', '#');
            wp_set_object_terms($id, [$event['category']], 'hta_event_cat');
        }
    }

    $ambassadors = [
        ['ישראל ישראלי', 'מנכ"ל טכנולוגיות'],
        ['מיכל כהן', 'שותפה בקרן VC'],
        ['דוד לוי', 'מומחה סייבר ואבטחה'],
        ['שרה אברהם', 'יזמת ומייסדת סטארטאפ'],
    ];
    foreach ($ambassadors as $item) {
        $id = wp_insert_post([
            'post_type'   => 'hta_ambassador',
            'post_status' => 'publish',
            'post_title'  => $item[0],
        ]);
        if ($id && ! is_wp_error($id)) {
            update_post_meta($id, '_hta_role', $item[1]);
        }
    }

    hta_seed_partner_logos(true);

    $media = [
        [
            'title'   => 'כותרת הכתבה המרכזית בתקשורת הכלכלית',
            'excerpt' => 'סקירה מקיפה על חשיבות מהלך האיחוד של איגוד ההייטק לחיזוק התעשייה.',
            'type'    => 'article',
            'link'    => '#',
        ],
        [
            'title'   => 'ראיון מצולם',
            'excerpt' => 'סרטון יוטיוב',
            'type'    => 'video',
            'link'    => '#',
        ],
        [
            'title'   => 'ראיון מיוחד עם ראשי האיגוד',
            'excerpt' => '"שבוע ההייטק יהפוך למוקד עלייה לרגל למשקיעים וחברות מכל העולם".',
            'type'    => 'news',
            'link'    => '#',
        ],
    ];
    foreach ($media as $item) {
        $id = wp_insert_post([
            'post_type'    => 'hta_media',
            'post_status'  => 'publish',
            'post_title'   => $item['title'],
            'post_excerpt' => $item['excerpt'],
        ]);
        if ($id && ! is_wp_error($id)) {
            update_post_meta($id, '_hta_media_type', $item['type']);
            update_post_meta($id, '_hta_media_link', $item['link']);
        }
    }

    update_option('hta_demo_seeded', 1);
    hta_seed_legal_pages(true);
}
add_action('after_switch_theme', 'hta_seed_demo_content', 20);

/**
 * Legal / policy pages for the footer.
 *
 * @return array<string, array{title: string, content: string}>
 */
function hta_legal_pages(): array
{
    return [
        'takkanon' => [
            'title'   => 'תקנון',
            'content' => <<<'HTML'
<h2>1. כללי</h2>
<p>ברוכים הבאים לאתר שבוע ההיי-טק הישראלי (להלן: "האתר"), המופעל על ידי איגוד ההיי-טק הישראלי. השימוש באתר, בתכניו ובשירותים המוצעים בו כפוף לתקנון זה. גלישה באתר או שימוש בו מהווים הסכמה לתנאים המפורטים להלן.</p>

<h2>2. השירותים באתר</h2>
<p>האתר מספק מידע על שבוע ההיי-טק הישראלי, אירועים, שותפים, מדיה ואפשרות להגשת אירועים. האיגוד שומר לעצמו את הזכות לעדכן, לשנות, להשהות או להפסיק כל חלק מהשירותים, ללא הודעה מוקדמת.</p>

<h2>3. הגשת אירועים</h2>
<p>משתמשים המגישים אירועים דרך הטופס באתר מתחייבים כי המידע שנמסר מדויק, מלא ואינו מפר זכויות צד שלישי. האיגוד רשאי לסרב לפרסם אירוע, לערוך את פרטיו או להסירו, לפי שיקול דעתו הבלעדי ומבלי שתהיה למגיש טענה בגין כך.</p>

<h2>4. קניין רוחני</h2>
<p>כל התכנים באתר — לרבות טקסטים, עיצוב, לוגואים, תמונות, סרטונים וסימני מסחר — הינם רכוש האיגוד או צדדים שלישיים שהעניקו רישיון שימוש, ואין להעתיק, להפיץ, לשכפל או לעשות בהם שימוש מסחרי ללא אישור מראש ובכתב.</p>

<h2>5. הגבלת אחריות</h2>
<p>המידע באתר מוצג כפי שהוא (AS IS). האיגוד לא יישא באחריות לנזק ישיר או עקיף הנובע משימוש באתר, מהסתמכות על מידע שפורסם בו, או מקישורים לאתרים חיצוניים.</p>

<h2>6. שינויים בתקנון</h2>
<p>האיגוד רשאי לעדכן תקנון זה מעת לעת. נוסח מעודכן יפורסם בעמוד זה, ותאריך העדכון האחרון יצוין בתחתית העמוד. המשך שימוש באתר לאחר עדכון התקנון מהווה הסכמה לנוסח המעודכן.</p>

<p><em>עודכן לאחרונה: אוגוסט 2026</em></p>
HTML,
        ],
        'mediniut-pratiut' => [
            'title'   => 'מדיניות פרטיות',
            'content' => <<<'HTML'
<h2>1. מבוא</h2>
<p>איגוד ההיי-טק הישראלי (להלן: "האיגוד") מכבד את פרטיות המשתמשים באתר שבוע ההיי-טק הישראלי. מדיניות זו מתארת אילו נתונים נאספים, כיצד הם משמשים ומהן זכויותיכם.</p>

<h2>2. איזה מידע נאסף</h2>
<ul>
<li>פרטים שמוסרים בטופס הגשת אירוע: שם אירוע, חברה מארחת, תאריך, מיקום ותיאור.</li>
<li>נתוני שימוש טכניים: כתובת IP, סוג דפדפן, דפים שנצפו וזמני גישה (באמצעות כלי אנליטיקה).</li>
<li>פרטי קשר שנמסרו מרצון במסגרת פנייה לאיגוד.</li>
</ul>

<h2>3. מטרות השימוש במידע</h2>
<ul>
<li>ניהול, סקירה ופרסום אירועים שהוגשו.</li>
<li>תפעול, שיפור ואבטחת האתר.</li>
<li>יצירת קשר עם מגישי אירועים, במידת הצורך.</li>
<li>עמידה בדרישות דין.</li>
</ul>

<h2>4. שיתוף מידע</h2>
<p>האיגוד לא ימכור את המידע האישי שלכם. מידע עשוי להימסר לספקי שירות הפועלים מטעמו (אחסון, אבטחה, דיוור) ובהתאם להוראות רשות מוסמכת, ככל שיידרש על פי דין.</p>

<h2>5. אבטחת מידע</h2>
<p>האיגוד מיישם אמצעי אבטחה סבירים להגנה על המידע, לרבות הצפנה, בקרות גישה וגיבויים. עם זאת, אין אפשרות להבטיח אבטחה מוחלטת במערכות מקוונות.</p>

<h2>6. זכויותיכם</h2>
<p>בכפוף לדין, ניתן לפנות לאיגוד בבקשה לעיון, תיקון או מחיקה של מידע אישי, ובבקשה להגביל עיבוד מידע. פניות יטופלו בהתאם לחוק הגנת הפרטיות, התשמ"א–1981.</p>

<h2>7. עוגיות (Cookies)</h2>
<p>האתר עשוי להשתמש בעוגיות לצורך תפעול, שמירת העדפות ומדידת שימוש. ניתן לנהל העדפות עוגיות דרך הגדרות הדפדפן.</p>

<h2>8. יצירת קשר</h2>
<p>לשאלות בנושא פרטיות ניתן לפנות לאיגוד ההיי-טק הישראלי דרך פרטי הקשר המופיעים באתר.</p>

<p><em>עודכן לאחרונה: אוגוסט 2026</em></p>
HTML,
        ],
        'negishut' => [
            'title'   => 'הצהרת נגישות',
            'content' => <<<'HTML'
<h2>1. מחויבות לנגישות</h2>
<p>איגוד ההיי-טק הישראלי פועל להנגיש את אתר שבוע ההיי-טק הישראלי לכלל האוכלוסייה, לרבות אנשים עם מוגבלות, בהתאם לעקרונות תקן ישראלי 5568 המבוסס על WCAG 2.0 ברמת AA, ככל הניתן.</p>

<h2>2. התאמות שבוצעו באתר</h2>
<ul>
<li>מבנה סמנטי וכותרות היררכיות לניווט נוח.</li>
<li>ניגודיות צבעים מספקת בין טקסט לרקע.</li>
<li>תמיכה בניווט מקלדת בחלקים מרכזיים של האתר.</li>
<li>טקסטים חלופיים (alt) לתמונות משמעותיות.</li>
<li>התאמה לצפייה במכשירים ניידים.</li>
</ul>

<h2>3. מגבלות ידועות</h2>
<p>למרות מאמצינו, ייתכן שחלקים מסוימים באתר — לרבות תכנים של צד שלישי, סרטונים או מסמכים שהועלו על ידי גורמים חיצוניים — טרם הונגשו במלואם. אנו ממשיכים לעבוד על שיפור הנגישות באופן שוטף.</p>

<h2>4. דרכי פנייה לקבלת סיוע</h2>
<p>נתקלתם בבעיית נגישות? נשמח לסייע. ניתן לפנות אלינו ולציין:</p>
<ul>
<li>תיאור הבעיה.</li>
<li>כתובת העמוד בו נתקלתם בקושי.</li>
<li>סוג הדפדפן והמכשיר.</li>
</ul>
<p>אנו נטפל בפנייה ונחזור אליכם בהקדם האפשרי.</p>

<h2>5. רכז/ת נגישות</h2>
<p>לפניות בנושא נגישות ניתן ליצור קשר עם רכז/ת הנגישות של האיגוד דרך ערוצי הקשר הרשמיים המופיעים באתר.</p>

<h2>6. תאריך עדכון</h2>
<p>הצהרה זו עודכנה לאחרונה באוגוסט 2026, ותעודכן בהתאם לשינויים שיבוצעו באתר.</p>
HTML,
        ],
    ];
}

function hta_seed_legal_pages(bool $force = false): void
{
    if (! $force && get_option('hta_legal_pages_seeded')) {
        return;
    }

    foreach (hta_legal_pages() as $slug => $page) {
        $existing = get_page_by_path($slug);
        if ($existing instanceof WP_Post) {
            continue;
        }

        wp_insert_post([
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $page['title'],
            'post_name'    => $slug,
            'post_content' => $page['content'],
        ]);
    }

    update_option('hta_legal_pages_seeded', 1);
}

function hta_seed_if_needed(): void
{
    if ('hta-landing' === get_template()) {
        hta_seed_demo_content();
        hta_seed_partner_logos();
        hta_seed_legal_pages();
    }
}
add_action('init', 'hta_seed_if_needed', 30);

/**
 * Partner logos shipped with the theme. Title = filename without extension.
 *
 * @return string[]
 */
function hta_partner_logo_files(): array
{
    return [
        'microsoft.webp',
        'taasiyeda.webp',
        'codevalue.webp',
        'ibm.webp',
        'kyndryl.webp',
        'kaltura.webp',
        'at.webp',
        'aeronautics.webp',
        'musictech.webp',
        '19tech.webp',
        'manhigoot.webp',
        'rad.webp',
        'marvell.webp',
        'sanmina.webp',
        'sick.webp',
        'altshuler-shaham.webp',
        'shavot.webp',
        'peres.webp',
        'predixaAI.webp',
        'marketeam.webp',
    ];
}

function hta_seed_partner_logos(bool $force = false): void
{
    if (! $force && get_option('hta_partners_logos_seeded')) {
        return;
    }

    $dir = HTA_DIR . '/assets/images/partners';
    if (! is_dir($dir)) {
        return;
    }

    $placeholders = get_posts([
        'post_type'      => 'hta_partner',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        's'              => 'LOGO',
    ]);
    foreach ($placeholders as $placeholder) {
        if (preg_match('/^LOGO \d+$/', $placeholder->post_title)) {
            wp_delete_post($placeholder->ID, true);
        }
    }

    $order = 0;
    foreach (hta_partner_logo_files() as $filename) {
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $path  = $dir . '/' . $filename;
        if (! is_readable($path)) {
            continue;
        }

        $existing = get_posts([
            'post_type'      => 'hta_partner',
            'post_status'    => 'any',
            'title'          => $title,
            'posts_per_page' => 1,
        ]);

        $post_id = $existing ? (int) $existing[0]->ID : 0;
        if (! $post_id) {
            $post_id = wp_insert_post([
                'post_type'   => 'hta_partner',
                'post_status' => 'publish',
                'post_title'  => $title,
                'menu_order'  => $order,
            ]);
        } else {
            wp_update_post([
                'ID'         => $post_id,
                'menu_order' => $order,
            ]);
        }

        $order++;
        if ($post_id && ! is_wp_error($post_id) && ! has_post_thumbnail($post_id)) {
            hta_attach_theme_image((int) $post_id, $path, $title);
        }
    }

    update_option('hta_partners_logos_seeded', 1);
}

function hta_attach_theme_image(int $post_id, string $source, string $alt): void
{
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $tmp = wp_tempnam(basename($source));
    if (! $tmp || ! copy($source, $tmp)) {
        return;
    }

    $attachment_id = media_handle_sideload(
        [
            'name'     => basename($source),
            'tmp_name' => $tmp,
        ],
        $post_id,
        $alt
    );

    if (is_wp_error($attachment_id)) {
        @unlink($tmp);
        return;
    }

    set_post_thumbnail($post_id, $attachment_id);
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
}
