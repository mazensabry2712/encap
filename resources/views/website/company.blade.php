@extends('website.layout')

@php
    $locale = request()->route('locale', app()->getLocale());

    $copyByLocale = [
        'ar' => [
            'home' => 'الرئيسية',
            'eyebrow' => 'إحدى شركات مجموعة ENCAP',
            'title' => 'شركة حدائق الشمال',
            'subtitle' => 'إحدى شركات الشركة المصرية الوطنية للإنتاج الزراعي ENCAP',
            'location_label' => 'مقر الأنشطة الزراعية',
            'location' => 'منطقة الجوف، المملكة العربية السعودية',
            'industry_label' => 'مجال العمل',
            'industry' => 'الإنتاج الزراعي وتطوير المشروعات الزراعية',
            'parent_label' => 'الشركة الأم',
            'parent_name' => 'الشركة المصرية الوطنية للإنتاج الزراعي',
            'about_kicker' => 'نبذة عن الشركة',
            'about_title' => 'إمكانات منطقة الجوف، برؤية زراعية متطورة',
            'paragraphs' => [
                'تُعد شركة حدائق الشمال إحدى الشركات التابعة للشركة المصرية الوطنية للإنتاج الزراعي ENCAP، وتتخذ من منطقة الجوف بالمملكة العربية السعودية مقرًا لأنشطتها الزراعية.',
                'تعمل الشركة في مجال الإنتاج الزراعي وزراعة وإنتاج الأعلاف الخضراء، والبطاطس، والقمح، والبصل، والبطيخ، إلى جانب تجهيز الأراضي الزراعية وتنفيذ شبكات الري وتطوير البنية التحتية للمشروعات الزراعية.',
                'وتسعى حدائق الشمال إلى تطوير عملياتها الزراعية، وتعزيز كفاءة الإنتاج، والاستفادة من الإمكانات الزراعية لمنطقة الجوف، من خلال الاهتمام بجودة الممارسات الزراعية وكفاءة إدارة الموارد، بما يدعم استدامة الإنتاج ويسهم في تنمية القطاع الزراعي.',
            ],
            'specialties_kicker' => 'مجالات العمل',
            'specialties_title' => 'تخصصات حدائق الشمال',
            'specialties_intro' => 'أنشطة زراعية تشمل إنتاج المحاصيل وتجهيز الأراضي وتطوير البنية التحتية للمشروعات.',
            'crops_title' => 'الإنتاج الزراعي والمحاصيل',
            'crops_intro' => 'التركيز على زراعة وإنتاج مجموعة من المحاصيل والأعلاف.',
            'crops' => ['الأعلاف الخضراء', 'البطاطس', 'القمح', 'البصل', 'البطيخ'],
            'operations_title' => 'تجهيز وتطوير المشروعات الزراعية',
            'operations_intro' => 'أعمال تدعم جاهزية الأراضي وكفاءة أنظمة الري ومرافق المشروع.',
            'operations' => ['تجهيز الأراضي الزراعية', 'تنفيذ شبكات الري', 'تطوير البنية التحتية للمشروعات الزراعية'],
            'gallery_kicker' => 'الأنشطة الزراعية',
            'gallery_title' => 'من الأرض يبدأ الإنتاج',
            'gallery_intro' => 'صور توضيحية لمجالات العمل الزراعي والمحاصيل.',
            'image_one_title' => 'الزراعة وتجهيز الأراضي',
            'image_one_alt' => 'حقل زراعي أخضر ضمن صورة توضيحية للأنشطة الزراعية',
            'image_two_title' => 'المحاصيل الزراعية',
            'image_two_alt' => 'محصول البطاطس ضمن صورة توضيحية للمحاصيل الزراعية',
            'image_note' => '',
            'logo_alt' => 'شعار ENCAP، الشركة الأم لحدائق الشمال',
            'map_label' => 'منطقة الجوف، السعودية',
        ],
        'en' => [
            'home' => 'Home',
            'eyebrow' => 'An ENCAP Group Company',
            'title' => 'Hadaeq Al-Shamal Company',
            'subtitle' => 'A subsidiary of The Egyptian National Company for Agricultural Production (ENCAP)',
            'location_label' => 'Agricultural operations',
            'location' => 'Al Jouf region, Kingdom of Saudi Arabia',
            'industry_label' => 'Sector',
            'industry' => 'Agricultural production and farm-project development',
            'parent_label' => 'Parent company',
            'parent_name' => 'The Egyptian National Company for Agricultural Production',
            'about_kicker' => 'Company overview',
            'about_title' => 'Developing agriculture in the Al Jouf region',
            'paragraphs' => [
                'Hadaeq Al-Shamal Company is a subsidiary of The Egyptian National Company for Agricultural Production (ENCAP), with its agricultural operations based in the Al Jouf region of the Kingdom of Saudi Arabia.',
                'The company works in agricultural production and the cultivation and production of green fodder, potatoes, wheat, onions and watermelon. Its activities also include preparing agricultural land, implementing irrigation networks, and developing infrastructure for agricultural projects.',
                'Hadaeq Al-Shamal aims to improve its agricultural operations and production efficiency while making good use of Al Jouf’s agricultural potential. Its approach emphasizes sound agricultural practices and efficient resource management to support sustainable production and contribute to the development of the agricultural sector.',
            ],
            'specialties_kicker' => 'What we do',
            'specialties_title' => 'Hadaeq Al-Shamal specializations',
            'specialties_intro' => 'Agricultural activities spanning crop production, land preparation and farm-project infrastructure.',
            'crops_title' => 'Agricultural production and crops',
            'crops_intro' => 'Growing and producing a focused range of crops and fodder.',
            'crops' => ['Green fodder', 'Potatoes', 'Wheat', 'Onions', 'Watermelon'],
            'operations_title' => 'Farm preparation and project development',
            'operations_intro' => 'Work that supports land readiness, efficient irrigation and project facilities.',
            'operations' => ['Agricultural land preparation', 'Irrigation network implementation', 'Agricultural project infrastructure development'],
            'gallery_kicker' => 'Agricultural activities',
            'gallery_title' => 'Production begins in the field',
            'gallery_intro' => 'Illustrative imagery related to agricultural work and crops.',
            'image_one_title' => 'Farming and land preparation',
            'image_one_alt' => 'Green agricultural field used as illustrative imagery for farming activities',
            'image_two_title' => 'Agricultural crops',
            'image_two_alt' => 'Potatoes used as illustrative imagery for agricultural crops',
            'image_note' => 'These are illustrative images, not verified photographs of Hadaeq Al-Shamal farms.',
            'logo_alt' => 'ENCAP parent-company logo',
            'map_label' => 'Al Jouf, Saudi Arabia',
        ],
        'fr' => [
            'home' => 'Accueil',
            'eyebrow' => 'Une société du groupe ENCAP',
            'title' => 'Société Hadaeq Al-Shamal',
            'subtitle' => 'Filiale de The Egyptian National Company for Agricultural Production (ENCAP)',
            'location_label' => 'Activités agricoles',
            'location' => 'Région d’Al Jouf, Royaume d’Arabie saoudite',
            'industry_label' => 'Secteur',
            'industry' => 'Production agricole et développement de projets agricoles',
            'parent_label' => 'Société mère',
            'parent_name' => 'The Egyptian National Company for Agricultural Production',
            'about_kicker' => 'Présentation',
            'about_title' => 'Développer l’agriculture dans la région d’Al Jouf',
            'paragraphs' => [
                'Hadaeq Al-Shamal est une filiale de The Egyptian National Company for Agricultural Production (ENCAP). Ses activités agricoles sont basées dans la région d’Al Jouf, au Royaume d’Arabie saoudite.',
                'La société intervient dans la production agricole et la culture de fourrage vert, de pommes de terre, de blé, d’oignons et de pastèques. Ses activités comprennent également la préparation des terres agricoles, la réalisation de réseaux d’irrigation et le développement des infrastructures des projets agricoles.',
                'Hadaeq Al-Shamal cherche à faire évoluer ses opérations agricoles et à améliorer l’efficacité de sa production en valorisant le potentiel agricole d’Al Jouf. L’attention portée aux bonnes pratiques agricoles et à la gestion efficace des ressources vise à soutenir une production durable et le développement du secteur agricole.',
            ],
            'specialties_kicker' => 'Activités',
            'specialties_title' => 'Domaines d’activité de Hadaeq Al-Shamal',
            'specialties_intro' => 'Des activités couvrant la production végétale, la préparation des terres et les infrastructures agricoles.',
            'crops_title' => 'Production agricole et cultures',
            'crops_intro' => 'Culture et production d’une gamme ciblée de cultures et de fourrage.',
            'crops' => ['Fourrage vert', 'Pommes de terre', 'Blé', 'Oignons', 'Pastèques'],
            'operations_title' => 'Préparation des terres et développement',
            'operations_intro' => 'Des travaux favorisant la préparation des terres, l’irrigation et les équipements des projets.',
            'operations' => ['Préparation des terres agricoles', 'Mise en œuvre de réseaux d’irrigation', 'Développement des infrastructures agricoles'],
            'gallery_kicker' => 'Activités agricoles',
            'gallery_title' => 'La production commence dans les champs',
            'gallery_intro' => 'Images illustratives des activités agricoles et des cultures.',
            'image_one_title' => 'Agriculture et préparation des terres',
            'image_one_alt' => 'Champ agricole vert, image illustrative des activités agricoles',
            'image_two_title' => 'Cultures agricoles',
            'image_two_alt' => 'Pommes de terre, image illustrative des cultures agricoles',
            'image_note' => 'Ces images sont illustratives et ne sont pas des photographies vérifiées des fermes de Hadaeq Al-Shamal.',
            'logo_alt' => 'Logo de la société mère ENCAP',
            'map_label' => 'Al Jouf, Arabie saoudite',
        ],
        'ru' => [
            'home' => 'Главная',
            'eyebrow' => 'Компания группы ENCAP',
            'title' => 'Компания Hadaeq Al-Shamal',
            'subtitle' => 'Дочерняя компания Egyptian National Company for Agricultural Production (ENCAP)',
            'location_label' => 'Сельскохозяйственная деятельность',
            'location' => 'Регион Эль-Джауф, Королевство Саудовская Аравия',
            'industry_label' => 'Сфера деятельности',
            'industry' => 'Сельскохозяйственное производство и развитие агропроектов',
            'parent_label' => 'Материнская компания',
            'parent_name' => 'Egyptian National Company for Agricultural Production',
            'about_kicker' => 'О компании',
            'about_title' => 'Развитие сельского хозяйства в регионе Эль-Джауф',
            'paragraphs' => [
                'Hadaeq Al-Shamal — дочерняя компания Egyptian National Company for Agricultural Production (ENCAP). Её сельскохозяйственная деятельность сосредоточена в регионе Эль-Джауф Королевства Саудовская Аравия.',
                'Компания занимается сельскохозяйственным производством и выращиванием зелёных кормов, картофеля, пшеницы, лука и арбузов. В её работу также входят подготовка сельскохозяйственных земель, создание ирригационных сетей и развитие инфраструктуры аграрных проектов.',
                'Hadaeq Al-Shamal стремится совершенствовать сельскохозяйственные процессы и повышать эффективность производства, используя потенциал региона Эль-Джауф. Внимание к качеству агротехнических практик и эффективному управлению ресурсами способствует устойчивому производству и развитию сельскохозяйственного сектора.',
            ],
            'specialties_kicker' => 'Направления работы',
            'specialties_title' => 'Специализация Hadaeq Al-Shamal',
            'specialties_intro' => 'Сельскохозяйственная деятельность включает выращивание культур, подготовку земель и инфраструктуру агропроектов.',
            'crops_title' => 'Сельскохозяйственное производство и культуры',
            'crops_intro' => 'Выращивание и производство основных культур и кормов.',
            'crops' => ['Зелёные корма', 'Картофель', 'Пшеница', 'Лук', 'Арбузы'],
            'operations_title' => 'Подготовка земель и развитие проектов',
            'operations_intro' => 'Работы по подготовке земель, организации орошения и оснащению проектов.',
            'operations' => ['Подготовка сельскохозяйственных земель', 'Создание ирригационных сетей', 'Развитие инфраструктуры агропроектов'],
            'gallery_kicker' => 'Сельскохозяйственная деятельность',
            'gallery_title' => 'Производство начинается в поле',
            'gallery_intro' => 'Иллюстративные изображения сельскохозяйственных работ и культур.',
            'image_one_title' => 'Земледелие и подготовка земель',
            'image_one_alt' => 'Зелёное сельскохозяйственное поле, иллюстрирующее аграрную деятельность',
            'image_two_title' => 'Сельскохозяйственные культуры',
            'image_two_alt' => 'Картофель, иллюстрирующий сельскохозяйственные культуры',
            'image_note' => 'Это иллюстративные изображения, а не подтверждённые фотографии хозяйств Hadaeq Al-Shamal.',
            'logo_alt' => 'Логотип материнской компании ENCAP',
            'map_label' => 'Эль-Джауф, Саудовская Аравия',
        ],
        'tr' => [
            'home' => 'Ana sayfa',
            'eyebrow' => 'ENCAP Grubu şirketi',
            'title' => 'Hadaeq Al-Shamal Şirketi',
            'subtitle' => 'Mısır Ulusal Tarımsal Üretim Şirketi ENCAP’ın bağlı şirketi',
            'location_label' => 'Tarım faaliyetleri',
            'location' => 'Al Jouf bölgesi, Suudi Arabistan Krallığı',
            'industry_label' => 'Faaliyet alanı',
            'industry' => 'Tarımsal üretim ve tarım projelerinin geliştirilmesi',
            'parent_label' => 'Ana şirket',
            'parent_name' => 'Mısır Ulusal Tarımsal Üretim Şirketi',
            'about_kicker' => 'Şirket hakkında',
            'about_title' => 'Al Jouf bölgesinde tarımın geliştirilmesi',
            'paragraphs' => [
                'Hadaeq Al-Shamal, Mısır Ulusal Tarımsal Üretim Şirketi ENCAP’ın bağlı şirketidir. Tarımsal faaliyetlerini Suudi Arabistan Krallığı’nın Al Jouf bölgesinde yürütür.',
                'Şirket; yeşil yem, patates, buğday, soğan ve karpuz yetiştiriciliği ve üretimi dâhil tarımsal üretim alanında çalışır. Faaliyetleri ayrıca tarım arazilerinin hazırlanmasını, sulama şebekelerinin kurulmasını ve tarımsal projelerin altyapısının geliştirilmesini kapsar.',
                'Hadaeq Al-Shamal, Al Jouf bölgesinin tarımsal potansiyelinden yararlanarak faaliyetlerini geliştirmeyi ve üretim verimliliğini artırmayı amaçlar. İyi tarım uygulamalarına ve kaynakların verimli yönetimine verilen önem, sürdürülebilir üretimi ve tarım sektörünün gelişimini destekler.',
            ],
            'specialties_kicker' => 'Faaliyet alanları',
            'specialties_title' => 'Hadaeq Al-Shamal uzmanlık alanları',
            'specialties_intro' => 'Bitkisel üretim, arazi hazırlığı ve tarımsal proje altyapısını kapsayan faaliyetler.',
            'crops_title' => 'Tarımsal üretim ve ürünler',
            'crops_intro' => 'Seçilmiş ürünlerin ve yem bitkilerinin yetiştirilmesi ve üretimi.',
            'crops' => ['Yeşil yem', 'Patates', 'Buğday', 'Soğan', 'Karpuz'],
            'operations_title' => 'Arazi hazırlığı ve proje geliştirme',
            'operations_intro' => 'Arazilerin hazırlanmasını, sulama verimliliğini ve proje tesislerini destekleyen çalışmalar.',
            'operations' => ['Tarım arazilerinin hazırlanması', 'Sulama şebekelerinin kurulması', 'Tarımsal proje altyapısının geliştirilmesi'],
            'gallery_kicker' => 'Tarımsal faaliyetler',
            'gallery_title' => 'Üretim tarlada başlar',
            'gallery_intro' => 'Tarım çalışmaları ve ürünleriyle ilgili temsilî görseller.',
            'image_one_title' => 'Tarım ve arazi hazırlığı',
            'image_one_alt' => 'Tarımsal faaliyetleri temsil eden yeşil tarla görseli',
            'image_two_title' => 'Tarımsal ürünler',
            'image_two_alt' => 'Tarımsal ürünleri temsil eden patates görseli',
            'image_note' => 'Bu görseller temsilîdir; Hadaeq Al-Shamal çiftliklerinden doğrulanmış fotoğraflar değildir.',
            'logo_alt' => 'Ana şirket ENCAP logosu',
            'map_label' => 'Al Jouf, Suudi Arabistan',
        ],
    ];

    $copy = $copyByLocale[$locale] ?? $copyByLocale['en'];

    $farmImage = \App\Support\EncapImage::url(
        'farm-cultivation-2026.jpg',
        'https://images.unsplash.com/photo-1719757168062-3ceb06988532?fit=crop&fm=jpg&w=1400&q=85'
    );
    $cropImage = \App\Support\EncapImage::url(
        'potato-sorting-2026.jpg',
        'https://images.pexels.com/photos/2889093/pexels-photo-2889093.jpeg?auto=compress&cs=tinysrgb&w=1400'
    );
@endphp

@push('head')
<style>
    .hs-company{--hs-ink:#17271f;--hs-green:#244b37;--hs-deep:#102b20;--hs-leaf:#b6c88e;--hs-lime:#e4edcf;--hs-cream:#f5f3e9;--hs-paper:#fbfaf5;--hs-muted:#68736b;--hs-line:rgba(29,56,40,.16);color:var(--hs-ink);background:var(--hs-paper)}
    .hs-company *{box-sizing:border-box}
    .hs-company__hero{position:relative;isolation:isolate;overflow:hidden;padding:25px 0 clamp(52px,7vw,96px);background:linear-gradient(140deg,#f4f2e7 0%,#fbfaf5 58%,#e8eddd 100%)}
    .hs-company__hero:before{content:"";position:absolute;z-index:-1;width:560px;height:560px;max-width:70vw;max-height:70vw;top:-300px;inset-inline-end:-100px;border:1px solid #244b3722;border-radius:50%;box-shadow:0 0 0 45px #244b3708,0 0 0 90px #244b3705}
    .hs-company__breadcrumbs{display:flex;flex-wrap:wrap;gap:9px;align-items:center;margin:0 0 32px;color:#68736b;font-size:12px}
    .hs-company__breadcrumbs a{color:var(--hs-green);text-decoration:none}
    .hs-company__breadcrumbs a:hover{text-decoration:underline}
    .hs-company__hero-grid{display:grid;grid-template-columns:minmax(0,1.2fr) minmax(230px,.65fr);align-items:center;gap:clamp(28px,6vw,90px)}
    .hs-company__eyebrow,.hs-company__kicker{display:flex;align-items:center;gap:10px;color:var(--hs-green);font-size:11px;font-weight:800;letter-spacing:.08em;line-height:1.8}
    .hs-company__eyebrow:before,.hs-company__kicker:before{content:"";width:25px;height:2px;flex:0 0 auto;background:var(--hs-leaf)}
    .hs-company__title{margin:16px 0 12px;font-family:Georgia,"Times New Roman",serif;font-size:clamp(42px,6.1vw,78px);font-weight:500;line-height:1.08;letter-spacing:-.045em;color:var(--hs-deep);text-wrap:balance}
    .hs-company__subtitle{max-width:780px;margin:0;color:#4b5c50;font-size:clamp(16px,1.8vw,21px);font-weight:650;line-height:1.8}
    .hs-company__facts{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px}
    .hs-company__fact{display:flex;align-items:flex-start;gap:10px;min-width:min(100%,240px);padding:13px 15px;border:1px solid var(--hs-line);background:#ffffff80}
    .hs-company__fact-mark{display:grid;place-items:center;flex:0 0 31px;width:31px;height:31px;border-radius:50%;background:var(--hs-lime);color:var(--hs-green);font-size:15px}
    .hs-company__fact strong,.hs-company__fact span{display:block}
    .hs-company__fact strong{font-size:11px;color:#778176;margin-bottom:3px}
    .hs-company__fact span{font-size:12px;font-weight:700;line-height:1.65}
    .hs-company__brand-card{padding:clamp(22px,3vw,34px);border:1px solid #ffffff;background:#ffffffc9;box-shadow:0 24px 65px #15312012;text-align:center;backdrop-filter:blur(9px)}
    .hs-company__brand-logo{display:grid;place-items:center;min-height:105px;padding:12px 4px 22px;border-bottom:1px solid var(--hs-line)}
    .hs-company__brand-logo img{display:block;max-width:min(100%,240px);width:100%;height:82px;object-fit:contain}
    .hs-company__brand-tag{display:inline-block;margin:22px 0 8px;color:var(--hs-green);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .hs-company__brand-name{margin:0;color:var(--hs-deep);font-family:Georgia,"Times New Roman",serif;font-size:clamp(22px,2.2vw,31px);font-weight:500;line-height:1.2}
    .hs-company__brand-parent{margin:12px 0 0;color:var(--hs-muted);font-size:11px;line-height:1.8}
    .hs-company__section{padding-block:clamp(58px,8vw,100px)}
    .hs-company__overview-grid{display:grid;grid-template-columns:minmax(220px,.76fr) minmax(0,1.24fr);gap:clamp(30px,7vw,100px);align-items:start}
    .hs-company__section-title{margin:12px 0 0;font-family:Georgia,"Times New Roman",serif;font-size:clamp(31px,4.2vw,51px);font-weight:500;line-height:1.2;letter-spacing:-.035em;color:var(--hs-deep);text-wrap:balance}
    .hs-company__body-copy p{margin:0 0 17px;color:#59685d;font-size:14px;line-height:2}
    .hs-company__body-copy p:last-child{margin-bottom:0}
    .hs-company__specialties{background:#eff0e6}
    .hs-company__section-heading{display:flex;justify-content:space-between;align-items:end;gap:28px;margin-bottom:34px}
    .hs-company__section-intro{max-width:430px;margin:0;color:var(--hs-muted);font-size:13px;line-height:1.9}
    .hs-company__specialty-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
    .hs-company__specialty-card{padding:clamp(22px,3vw,34px);border:1px solid #244b3718;background:#fbfaf5;min-width:0}
    .hs-company__specialty-top{display:flex;align-items:center;gap:13px;margin-bottom:10px}
    .hs-company__specialty-icon{display:grid;place-items:center;flex:0 0 43px;width:43px;height:43px;border-radius:50%;background:var(--hs-lime);color:var(--hs-green);font-size:20px}
    .hs-company__specialty-card h3{margin:0;font-size:19px;font-weight:750;line-height:1.45;color:var(--hs-deep)}
    .hs-company__specialty-card>p{margin:0 0 20px;color:var(--hs-muted);font-size:12px;line-height:1.85}
    .hs-company__chips{display:flex;flex-wrap:wrap;gap:8px;padding:0;margin:0;list-style:none}
    .hs-company__chips li{padding:8px 11px;border:1px solid #244b3727;background:white;color:#33483a;font-size:12px;font-weight:650;line-height:1.4}
    .hs-company__operations{display:grid;gap:10px;margin:0;padding:0;list-style:none}
    .hs-company__operations li{display:flex;align-items:flex-start;gap:10px;color:#33483a;font-size:13px;line-height:1.75}
    .hs-company__operations li:before{content:"✓";display:grid;place-items:center;flex:0 0 22px;width:22px;height:22px;margin-top:1px;border-radius:50%;background:var(--hs-lime);color:var(--hs-green);font-size:12px;font-weight:800}
    .hs-company__gallery{background:var(--hs-paper)}
    .hs-company__gallery-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
    .hs-company__photo{position:relative;overflow:hidden;min-width:0;margin:0;background:#dce3d4}
    .hs-company__photo img{display:block;width:100%;height:clamp(230px,32vw,420px);object-fit:cover;transition:transform .7s cubic-bezier(.2,.7,.2,1),filter .6s;filter:saturate(.88) contrast(1.02)}
    .hs-company__photo:hover img{transform:scale(1.035);filter:saturate(1.02)}
    .hs-company__photo figcaption{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:17px 18px;background:#eeefe4;border-bottom:1px solid #cfd5c7;color:var(--hs-deep);font-size:14px;font-weight:750}
    .hs-company__photo-index{color:#7d8976;font-size:10px;font-weight:800;letter-spacing:.12em}
    .hs-company__image-note{margin:16px 0 0;color:#7a8378;font-size:11px;line-height:1.8}
    @media (max-width:820px){.hs-company__hero-grid,.hs-company__overview-grid{grid-template-columns:minmax(0,1fr)}.hs-company__brand-card{max-width:440px}.hs-company__brand-logo{min-height:85px}.hs-company__section-heading{display:block}.hs-company__section-intro{margin-top:16px}.hs-company__specialty-grid{gap:14px}}
    @media (max-width:600px){.hs-company__hero{padding-top:18px}.hs-company__breadcrumbs{margin-bottom:24px}.hs-company__facts{display:grid;grid-template-columns:minmax(0,1fr)}.hs-company__fact{min-width:0}.hs-company__specialty-grid,.hs-company__gallery-grid{grid-template-columns:minmax(0,1fr)}.hs-company__photo img{height:270px}.hs-company__section{padding-block:54px}.hs-company__specialty-card{padding:22px}.hs-company__title{font-size:clamp(39px,11vw,58px)}}
    @media (prefers-reduced-motion:reduce){.hs-company__photo img{transition:none}.hs-company__photo:hover img{transform:none}}
</style>
@endpush

@section('content')
<div class="hs-company">
    <section class="hs-company__hero" aria-labelledby="hs-company-title">
        <div class="container">
            <nav class="hs-company__breadcrumbs" aria-label="{{ $locale === 'ar' ? 'مسار التنقل' : 'Breadcrumbs' }}">
                <a href="{{ route('home', ['locale' => $locale]) }}">{{ $copy['home'] }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $copy['title'] }}</span>
            </nav>

            <div class="hs-company__hero-grid">
                <div>
                    <div class="hs-company__eyebrow">{{ $copy['eyebrow'] }}</div>
                    <h1 id="hs-company-title" class="hs-company__title">{{ $copy['title'] }}</h1>
                    <p class="hs-company__subtitle">{{ $copy['subtitle'] }}</p>

                    <div class="hs-company__facts">
                        <div class="hs-company__fact">
                            <span class="hs-company__fact-mark" aria-hidden="true">⌖</span>
                            <div>
                                <strong>{{ $copy['location_label'] }}</strong>
                                <span>{{ $copy['location'] }}</span>
                            </div>
                        </div>
                        <div class="hs-company__fact">
                            <span class="hs-company__fact-mark" aria-hidden="true">✳</span>
                            <div>
                                <strong>{{ $copy['industry_label'] }}</strong>
                                <span>{{ $copy['industry'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="hs-company__brand-card" aria-label="{{ $copy['parent_label'] }}">
                    <div class="hs-company__brand-logo">
                        <img src="{{ \App\Support\EncapImage::url('logo.png') }}" alt="{{ $copy['logo_alt'] }}" width="300" height="90" decoding="async">
                    </div>
                    <span class="hs-company__brand-tag">{{ $copy['parent_label'] }}</span>
                    <h2 class="hs-company__brand-name">{{ $copy['title'] }}</h2>
                    <p class="hs-company__brand-parent">{{ $copy['parent_name'] }}</p>
                </aside>
            </div>
        </div>
    </section>

    <section class="hs-company__section" aria-labelledby="hs-company-about-title">
        <div class="container hs-company__overview-grid">
            <header>
                <div class="hs-company__kicker">{{ $copy['about_kicker'] }}</div>
                <h2 id="hs-company-about-title" class="hs-company__section-title">{{ $copy['about_title'] }}</h2>
            </header>
            <div class="hs-company__body-copy">
                @foreach($copy['paragraphs'] as $paragraph)
                    <p>{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </section>

    <section class="hs-company__section hs-company__specialties" aria-labelledby="hs-company-specialties-title">
        <div class="container">
            <div class="hs-company__section-heading">
                <div>
                    <div class="hs-company__kicker">{{ $copy['specialties_kicker'] }}</div>
                    <h2 id="hs-company-specialties-title" class="hs-company__section-title">{{ $copy['specialties_title'] }}</h2>
                </div>
                <p class="hs-company__section-intro">{{ $copy['specialties_intro'] }}</p>
            </div>

            <div class="hs-company__specialty-grid">
                <article class="hs-company__specialty-card">
                    <div class="hs-company__specialty-top">
                        <span class="hs-company__specialty-icon" aria-hidden="true">❧</span>
                        <h3>{{ $copy['crops_title'] }}</h3>
                    </div>
                    <p>{{ $copy['crops_intro'] }}</p>
                    <ul class="hs-company__chips">
                        @foreach($copy['crops'] as $crop)
                            <li>{{ $crop }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="hs-company__specialty-card">
                    <div class="hs-company__specialty-top">
                        <span class="hs-company__specialty-icon" aria-hidden="true">⌘</span>
                        <h3>{{ $copy['operations_title'] }}</h3>
                    </div>
                    <p>{{ $copy['operations_intro'] }}</p>
                    <ul class="hs-company__operations">
                        @foreach($copy['operations'] as $operation)
                            <li>{{ $operation }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="hs-company__section hs-company__gallery" aria-labelledby="hs-company-gallery-title">
        <div class="container">
            <div class="hs-company__section-heading">
                <div>
                    <div class="hs-company__kicker">{{ $copy['gallery_kicker'] }}</div>
                    <h2 id="hs-company-gallery-title" class="hs-company__section-title">{{ $copy['gallery_title'] }}</h2>
                </div>
                <p class="hs-company__section-intro">{{ $copy['gallery_intro'] }}</p>
            </div>

            <div class="hs-company__gallery-grid">
                <figure class="hs-company__photo">
                    <img src="{{ $farmImage }}" alt="{{ $copy['image_one_alt'] }}" width="1200" height="850" loading="lazy" decoding="async">
                    <figcaption>
                        <span>{{ $copy['image_one_title'] }}</span>
                        <span class="hs-company__photo-index">01 / 02</span>
                    </figcaption>
                </figure>
                <figure class="hs-company__photo">
                    <img src="{{ $cropImage }}" alt="{{ $copy['image_two_alt'] }}" width="1200" height="850" loading="lazy" decoding="async">
                    <figcaption>
                        <span>{{ $copy['image_two_title'] }}</span>
                        <span class="hs-company__photo-index">02 / 02</span>
                    </figcaption>
                </figure>
            </div>
            @if(filled($copy['image_note']))<p class="hs-company__image-note">{{ $copy['image_note'] }}</p>@endif
        </div>
    </section>
</div>
@endsection
