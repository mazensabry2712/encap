<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\SeoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebsiteController extends Controller
{
    private function useLocale(string $locale): void
    {
        App::setLocale($locale);
    }

    public function home(string $locale): View
    {
        $this->useLocale($locale);
        $categories = Category::query()->where('is_published', true)
            ->orderBy('sort_order')->orderBy('name_en')->limit(10)->get();

        $settings = SeoSetting::current();
        $defaultTitle = $locale === 'ar' ? $settings->site_title_ar : $settings->site_title_en;
        $defaultDescription = $locale === 'ar'
            ? $settings->site_description_ar
            : $settings->site_description_en;
        $pageSeo = $settings->metadataFor('home', $locale, $defaultTitle, $defaultDescription);

        return view('website.home', compact('categories', 'locale', 'pageSeo'));
    }

    public function products(string $locale): View
    {
        $this->useLocale($locale);
        $categories = Category::query()->where('is_published', true)
            ->orderBy('sort_order')->orderBy('name_en')->paginate(12);

        $settings = SeoSetting::current();
        $pageSeo = $settings->metadataFor(
            'products',
            $locale,
            __('Our Products | ENCAP'),
            __('Explore ENCAP agricultural product categories for international wholesale and export enquiries.')
        );

        return view('website.products.index', compact('categories', 'locale', 'pageSeo'));
    }

    public function category(string $locale, Category $category): View
    {
        $this->useLocale($locale);
        abort_unless($category->is_published, 404);

        $settings = SeoSetting::current();
        $name = $locale === 'ar' && $category->name_ar ? $category->name_ar : $category->name_en;
        $shortDescription = $locale === 'ar' && $category->short_description_ar
            ? $category->short_description_ar
            : $category->short_description_en;
        $seoTitle = ($locale === 'ar' ? $category->seo_title_ar : $category->seo_title_en)
            ?: $name . ' | ENCAP';
        $seoDescription = ($locale === 'ar' ? $category->seo_description_ar : $category->seo_description_en)
            ?: ($shortDescription ?: ($locale === 'ar' ? $settings->site_description_ar : $settings->site_description_en));
        $pageSeo = ['title' => $seoTitle, 'description' => $seoDescription];

        return view('website.products.show', compact('category', 'locale', 'pageSeo'));
    }

    public function contentPage(string $locale, string $page): View
    {
        $this->useLocale($locale);

        $content = [
            'about' => [
                'en' => [
                    'eyebrow' => 'About ENCAP',
                    'title' => 'Agriculture with a clear route to market',
                    'intro' => 'ENCAP connects Egyptian agricultural production with the needs of wholesale buyers and international markets.',
                    'body' => 'Our approach combines production from our own farms with sourcing from trusted partner farms. Product availability, specifications and fulfilment details are confirmed for each enquiry.',
                    'hero_badge' => 'A connected supply journey',
                    'hero_caption' => 'Illustrative stock photography. The pictured location is not represented as an ENCAP-owned farm.',
                    'hero_landscape_alt' => 'Agricultural fields in soft morning light, used as illustrative imagery.',
                    'hero_produce_alt' => 'Fresh produce arranged for a wholesale market, used as illustrative imagery.',
                    'approach_image_alt' => 'A grower working among agricultural crops, used as illustrative imagery.',
                    'journey_heading' => 'One connected journey, from origin to market',
                    'journey_intro' => 'Each enquiry starts with the product and destination. We coordinate the relevant steps, then confirm availability and scope before commitment.',
                    'journey' => [
                        ['icon' => 'sprout', 'eyebrow' => '01 / ORIGIN', 'title' => 'Production', 'description' => 'Start with the crop, its growing season and the supply available from company farms.'],
                        ['icon' => 'network', 'eyebrow' => '02 / SOURCING', 'title' => 'Partner sourcing', 'description' => 'Where appropriate, review trusted partner-farm options against the requested product and volume.'],
                        ['icon' => 'package', 'eyebrow' => '03 / PREPARATION', 'title' => 'Preparation', 'description' => 'Align sorting, grading and packing requirements to the crop and the buyer’s specification.'],
                        ['icon' => 'globe', 'eyebrow' => '04 / DESTINATION', 'title' => 'Export coordination', 'description' => 'Clarify destination requirements and coordinate relevant next steps with the parties involved.'],
                    ],
                    'approach_heading' => 'Clarity before commitment. Coordination at every step.',
                    'approach_intro' => 'Details vary by crop, season and destination, so we confirm the scope for each enquiry rather than promise a one-size-fits-all service.',
                    'approach_points' => [
                        ['title' => 'Start with the requirement', 'description' => 'Product, grade, estimated quantity, packaging and destination help define the right conversation.'],
                        ['title' => 'Confirm what is available', 'description' => 'Seasonal availability and service scope are checked for each enquiry.'],
                        ['title' => 'Agree on the details', 'description' => 'Specifications and next steps are discussed before supply arrangements are confirmed.'],
                    ],
                ],
                'ar' => [
                    'eyebrow' => 'عن ENCAP',
                    'title' => 'إنتاج زراعي بطريق واضح إلى الأسواق',
                    'intro' => 'تربط ENCAP الإنتاج الزراعي المصري باحتياجات تجار الجملة والأسواق الدولية.',
                    'body' => 'يجمع نهجنا بين الإنتاج في مزارعنا والتوريد من المزارع الشريكة الموثوقة. ويتم تأكيد توافر المنتجات ومواصفاتها وتفاصيل التوريد لكل طلب.',
                    'hero_badge' => 'رحلة توريد مترابطة',
                    'hero_caption' => 'صور توضيحية من مصادر خارجية، ولا تمثل المزرعة الظاهرة باعتبارها مزرعة مملوكة لشركة ENCAP.',
                    'hero_landscape_alt' => 'حقول زراعية في ضوء الصباح، ضمن صور توضيحية.',
                    'hero_produce_alt' => 'منتجات زراعية طازجة مرتبة لسوق الجملة، ضمن صور توضيحية.',
                    'approach_image_alt' => 'مزارع يعمل بين المحاصيل الزراعية، ضمن صور توضيحية.',
                    'journey_heading' => 'رحلة واحدة مترابطة من المصدر إلى السوق',
                    'journey_intro' => 'يبدأ كل طلب بالمنتج والوجهة المطلوبة. ننسق الخطوات المناسبة، ثم نؤكد التوافر ونطاق الخدمة قبل الالتزام.',
                    'journey' => [
                        ['icon' => 'sprout', 'eyebrow' => '01 / المصدر', 'title' => 'الإنتاج الزراعي', 'description' => 'نبدأ بنوع المحصول وموسم زراعته والكميات المتاحة من مزارع الشركة.'],
                        ['icon' => 'network', 'eyebrow' => '02 / التوريد', 'title' => 'التوريد من الشركاء', 'description' => 'عند الحاجة، نراجع خيارات التوريد من المزارع الشريكة وفق المنتج والكمية المطلوبة.'],
                        ['icon' => 'package', 'eyebrow' => '03 / التجهيز', 'title' => 'التجهيز والتعبئة', 'description' => 'ننسق متطلبات الفرز والتصنيف والتعبئة وفق طبيعة المحصول ومواصفات المشتري.'],
                        ['icon' => 'globe', 'eyebrow' => '04 / الوجهة', 'title' => 'تنسيق التصدير', 'description' => 'نوضح متطلبات الوجهة وننسق الخطوات التالية مع الأطراف المعنية.'],
                    ],
                    'approach_heading' => 'وضوح قبل الالتزام، وتنسيق في كل خطوة.',
                    'approach_intro' => 'تختلف التفاصيل حسب المحصول والموسم والوجهة؛ لذلك نؤكد نطاق الخدمة لكل طلب بدلًا من تقديم وعود عامة لا تناسب كل الحالات.',
                    'approach_points' => [
                        ['title' => 'نبدأ باحتياجات المشتري', 'description' => 'يساعد تحديد المنتج والدرجة والكمية التقديرية والتعبئة والوجهة على توضيح المطلوب.'],
                        ['title' => 'نتحقق من المتاح', 'description' => 'يتم التحقق من التوافر الموسمي ونطاق الخدمة لكل طلب.'],
                        ['title' => 'نتفق على التفاصيل', 'description' => 'تتم مناقشة المواصفات والخطوات التالية قبل تأكيد ترتيبات التوريد.'],
                    ],
                ],
            ],
            'farms' => [
                'en' => [
                    'eyebrow' => 'Agriculture & Farms',
                    'title' => 'Built from the field up',
                    'intro' => 'Agricultural sourcing begins with understanding the crop, growing season, required volume and route to market.',
                    'body' => 'ENCAP connects production from company farms with sourcing from partner farms. Crop, grade, quantity and supply windows are confirmed for every enquiry; available products vary with the season and harvest.',
                    'services_heading' => 'Agriculture from origin to supply',
                    'services_intro' => 'We align crop availability and buyer requirements before moving to preparation.',
                    'services' => [
                        ['title' => 'Crop production & farm operations', 'description' => 'Season-aware production information for crops available through company farms or trusted grower partners.'],
                        ['title' => 'Harvest planning', 'description' => 'Coordinate estimated volumes, timing and post-harvest handling around the crop’s actual harvest window.'],
                        ['title' => 'Partner farm sourcing', 'description' => 'Review suitable grower sources when additional volumes or a particular product specification is needed.'],
                    ],
                    'workflow_heading' => 'How farm supply comes together',
                    'workflow' => [
                        ['title' => 'Define the crop and market', 'description' => 'Identify product, grade, target quantity and destination.'],
                        ['title' => 'Check season and volume', 'description' => 'Confirm the harvest window and available supply before making a commitment.'],
                        ['title' => 'Agree the supply details', 'description' => 'Align specifications, handling and the next step with the buyer.'],
                    ],
                ],
                'ar' => [
                    'eyebrow' => 'الزراعة والمزارع',
                    'title' => 'من الحقل تبدأ رحلة التوريد',
                    'intro' => 'يبدأ التوريد الزراعي بفهم المحصول وموسمه والكمية المطلوبة وطريقه إلى السوق.',
                    'body' => 'تربط ENCAP الإنتاج من مزارع الشركة بالتوريد من المزارع الشريكة. ويتم تأكيد المحصول والدرجة والكمية وفترة التوريد لكل استفسار، إذ تختلف المنتجات المتاحة حسب الموسم والحصاد.',
                    'services_heading' => 'من مصدر المحصول إلى التوريد',
                    'services_intro' => 'نوازن بين المحاصيل المتاحة ومتطلبات المشتري قبل الانتقال إلى التجهيز.',
                    'services' => [
                        ['title' => 'الإنتاج الزراعي وإدارة المزرعة', 'description' => 'معلومات عن الإنتاج الموسمي للمحاصيل المتاحة من مزارع الشركة أو شركاء التوريد.'],
                        ['title' => 'التخطيط للحصاد', 'description' => 'تنسيق التوقيت والكميات التقديرية ومتطلبات التعامل مع المحصول بعد الحصاد وفق موسمه الفعلي.'],
                        ['title' => 'التوريد من المزارع الشريكة', 'description' => 'مراجعة مصادر توريد مناسبة عند الحاجة إلى كميات إضافية أو مواصفات محددة للمنتج.'],
                    ],
                    'workflow_heading' => 'كيف ننسق توريد المحاصيل؟',
                    'workflow' => [
                        ['title' => 'تحديد المحصول والسوق', 'description' => 'تحديد المنتج والدرجة والكمية المستهدفة والوجهة.'],
                        ['title' => 'مراجعة الموسم والكميات', 'description' => 'تأكيد فترة الحصاد والكميات المتاحة قبل الالتزام بالتوريد.'],
                        ['title' => 'الاتفاق على تفاصيل التوريد', 'description' => 'تحديد المواصفات ومتطلبات التعامل مع المنتج والخطوة التالية.'],
                    ],
                ],
            ],
            'packing-stations' => [
                'en' => [
                    'eyebrow' => 'Packing Stations',
                    'title' => 'From incoming crop to shipment-ready preparation',
                    'intro' => 'A clear post-harvest workflow aligns produce intake, sorting, grading, quality checks, packing and destination requirements.',
                    'body' => 'Packing and post-harvest services are matched to the crop, buyer specification and selected facility. ENCAP confirms which steps are available directly or through a packing partner before a quotation is agreed.',
                    'services_heading' => 'Post-harvest services',
                    'services_intro' => 'The service scope is confirmed per product and facility; not every process is suitable for every crop.',
                    'services' => [
                        ['title' => 'Receiving & lot identification', 'description' => 'Record incoming crop details and keep available batch and source information connected to the enquiry.'],
                        ['title' => 'Sorting & grading', 'description' => 'Discuss sorting criteria such as size, grade and visible quality against the buyer’s agreed specification.'],
                        ['title' => 'Quality checks', 'description' => 'Review product condition and agreed requirements before packing; any inspection or testing scope is confirmed in advance.'],
                        ['title' => 'Packing & labelling', 'description' => 'Align carton or pack format, pack weight, labelling and pallet preparation to the product and destination.'],
                        ['title' => 'Pre-cooling & cold-chain coordination', 'description' => 'Coordinate temperature-sensitive handling or cold storage where the chosen facility and product requirements support it.'],
                    ],
                    'workflow_heading' => 'From intake to dispatch preparation',
                    'workflow' => [
                        ['title' => 'Receive the crop brief', 'description' => 'Confirm crop, expected volume, timing and source information.'],
                        ['title' => 'Agree the specification', 'description' => 'Set the grading, quality, packaging and labelling requirements with the buyer.'],
                        ['title' => 'Confirm the available process', 'description' => 'Match the order to a suitable facility and agree the steps included in the quote.'],
                        ['title' => 'Prepare for dispatch', 'description' => 'Align packing completion, storage needs and shipment handover with the agreed plan.'],
                    ],
                ],
                'ar' => [
                    'eyebrow' => 'محطات التعبئة والتجهيز',
                    'title' => 'من استلام المحصول إلى التجهيز للشحن',
                    'intro' => 'تنظم خطوات ما بعد الحصاد استلام المنتج وفرزه وتصنيفه وفحصه وتعبئته وفق متطلبات الوجهة.',
                    'body' => 'يتم تحديد خدمات التعبئة وما بعد الحصاد وفق المحصول ومواصفات المشتري والمحطة المختارة. وتؤكد ENCAP الخطوات المتاحة لديها أو من خلال محطة شريكة قبل الاتفاق على عرض السعر.',
                    'services_heading' => 'خدمات ما بعد الحصاد',
                    'services_intro' => 'يتم تأكيد نطاق الخدمة لكل محصول ومحطة؛ فليست كل العمليات مناسبة لجميع المحاصيل.',
                    'services' => [
                        ['title' => 'استلام المحصول وتحديد الدفعات', 'description' => 'تسجيل بيانات المحصول عند الاستلام وربط معلومات المصدر والدفعة المتاحة بالطلب.'],
                        ['title' => 'الفرز والتصنيف', 'description' => 'مناقشة معايير الفرز مثل الحجم والدرجة والجودة الظاهرة وفق المواصفات المتفق عليها.'],
                        ['title' => 'فحوصات الجودة', 'description' => 'مراجعة حالة المنتج والمتطلبات المتفق عليها قبل التعبئة، مع تأكيد نطاق أي فحص أو اختبار مسبقًا.'],
                        ['title' => 'التعبئة ووضع الملصقات', 'description' => 'تحديد شكل العبوة أو الكرتونة والوزن والملصقات وتجهيز المنصات بما يناسب المنتج والوجهة.'],
                        ['title' => 'التبريد المسبق وتنسيق سلسلة التبريد', 'description' => 'تنسيق التعامل مع المنتجات الحساسة للحرارة أو التخزين المبرد عندما تسمح إمكانيات المحطة المختارة ومتطلبات المنتج.'],
                    ],
                    'workflow_heading' => 'من الاستلام إلى التجهيز للإرسال',
                    'workflow' => [
                        ['title' => 'استلام متطلبات المحصول', 'description' => 'تأكيد نوع المحصول والكمية المتوقعة والتوقيت ومعلومات المصدر.'],
                        ['title' => 'الاتفاق على المواصفات', 'description' => 'تحديد متطلبات الفرز والجودة والتعبئة والملصقات مع المشتري.'],
                        ['title' => 'تأكيد العمليات المتاحة', 'description' => 'اختيار المحطة المناسبة والاتفاق على الخطوات المشمولة في عرض السعر.'],
                        ['title' => 'التجهيز للإرسال', 'description' => 'تنسيق موعد اكتمال التعبئة واحتياجات التخزين وتسليم الشحنة وفق الخطة المتفق عليها.'],
                    ],
                ],
            ],
            'quality' => [
                'en' => [
                    'eyebrow' => 'Quality, Food Safety & Processing',
                    'title' => 'Quality is built into every stage.',
                    'intro' => 'From crop handling and grading to packing, frozen formats and export preparation, quality starts with clear requirements and careful coordination.',
                    'body' => 'Our approach begins by agreeing product specifications, reviewing available supply, and aligning handling, grading, packing and destination requirements. Where applicable, we consider internationally used food-safety, good-agriculture, quality-management, worker-welfare and responsible-sourcing frameworks. Any certification, audit result or site-level status is confirmed for the relevant farm, product or facility before it is stated.',
                    'services_heading' => 'Quality practices across the supply chain',
                    'services_intro' => 'The checks and records required for each order depend on the crop, its origin, the selected facility and the buyer’s destination requirements.',
                    'services' => [
                        ['title' => 'Product specifications & traceability', 'description' => 'Align variety, grade, size, origin, batch details and the buyer’s written specification before supply is confirmed.'],
                        ['title' => 'Condition, sorting & grading', 'description' => 'Review available product condition and agree the applicable sorting, grading and acceptance criteria for the order.'],
                        ['title' => 'Food safety & hygienic handling', 'description' => 'Discuss suitable handling, cleaning, storage and product-protection controls with the responsible farm or facility.'],
                        ['title' => 'Packing, cold chain & dispatch', 'description' => 'Confirm packing formats, labelling, temperature-sensitive handling and destination needs before dispatch planning.'],
                    ],
                    'workflow_heading' => 'A clear quality review before dispatch',
                    'workflow' => [
                        ['title' => 'Define the requirement', 'description' => 'Record the product, grade, quantity, destination and any buyer-specific quality requirements.'],
                        ['title' => 'Confirm source and scope', 'description' => 'Review available crop information and confirm which handling, inspection or packing steps apply.'],
                        ['title' => 'Align records and packing', 'description' => 'Agree the relevant batch information, package details and destination documentation with responsible parties.'],
                        ['title' => 'Confirm before shipment', 'description' => 'Check the agreed requirements and share verified product or facility documentation where applicable.'],
                    ],
                    'frameworks_heading' => 'Standards & frameworks in focus',
                    'frameworks_intro' => 'These internationally used standards and supply-chain frameworks help structure quality discussions and buyer requirements where they are relevant to a product or operation.',
                    'frameworks_note' => 'Listing a framework here does not mean ENCAP or a particular supplier holds that certification or has completed an audit. Current status is confirmed for the specific farm, product or facility and shared only when documented.',
                    'frameworks' => [
                        ['code' => 'BRCGS', 'name' => 'BRCGS Food Safety (BRC)', 'description' => 'Food-safety, product-integrity and operational-control requirements for relevant food operations.'],
                        ['code' => 'G.A.P.', 'name' => 'GLOBALG.A.P.', 'description' => 'Good agricultural practices, farm management and traceability requirements where applicable.'],
                        ['code' => 'ISO', 'name' => 'ISO 9001', 'description' => 'Quality-management principles, process consistency and continual improvement.'],
                        ['code' => 'GRASP', 'name' => 'GRASP', 'description' => 'A GLOBALG.A.P. add-on addressing social practice and worker-related requirements.'],
                        ['code' => 'SEDEX', 'name' => 'SEDEX / SMETA', 'description' => 'Supply-chain visibility and ethical-trade assessment approaches; these are not a company certification.'],
                    ],
                ],
                'ar' => [
                    'eyebrow' => 'الجودة وسلامة الغذاء والتجهيز',
                    'title' => 'الجودة جزء من كل مرحلة.',
                    'intro' => 'من التعامل مع المحصول وفرزه إلى التعبئة والتجميد والتجهيز للتصدير، تبدأ الجودة من وضوح المتطلبات وحسن التنسيق.',
                    'body' => 'نبدأ بتحديد مواصفات المنتج ومراجعة التوريد المتاح وتنسيق التعامل مع المحصول والفرز والتعبئة ومتطلبات الوجهة. وحسب طبيعة المنتج والعملية، نسترشد بالأطر الدولية ذات الصلة بسلامة الغذاء والممارسات الزراعية وإدارة الجودة ورفاه العاملين والتوريد المسؤول. ويتم التحقق من أي شهادة أو نتيجة تدقيق أو حالة خاصة بمزرعة أو منتج أو منشأة قبل ذكرها.',
                    'services_heading' => 'ممارسات الجودة عبر سلسلة التوريد',
                    'services_intro' => 'تتحدد الفحوصات والسجلات المطلوبة لكل طلب وفق المحصول ومصدره والمنشأة المختارة ومتطلبات سوق الوجهة.',
                    'services' => [
                        ['title' => 'مواصفات المنتج وإمكانية التتبع', 'description' => 'تحديد الصنف والدرجة والحجم والمصدر وبيانات الدفعة والمواصفات المكتوبة للمشتري قبل تأكيد التوريد.'],
                        ['title' => 'مراجعة الحالة والفرز والتصنيف', 'description' => 'مراجعة حالة المنتج المتاح والاتفاق على معايير الفرز والتصنيف والقبول المناسبة للطلب.'],
                        ['title' => 'سلامة الغذاء والتعامل الصحي', 'description' => 'مناقشة إجراءات التعامل والتنظيف والتخزين وحماية المنتج المناسبة مع المزرعة أو المنشأة المسؤولة.'],
                        ['title' => 'التعبئة وسلسلة التبريد والإرسال', 'description' => 'تأكيد أشكال التعبئة والملصقات والتعامل مع المنتجات الحساسة للحرارة ومتطلبات الوجهة قبل التخطيط للإرسال.'],
                    ],
                    'workflow_heading' => 'مراجعة واضحة للجودة قبل الإرسال',
                    'workflow' => [
                        ['title' => 'تحديد المتطلبات', 'description' => 'تسجيل المنتج والدرجة والكمية والوجهة وأي متطلبات جودة خاصة بالمشتري.'],
                        ['title' => 'تأكيد المصدر ونطاق العمل', 'description' => 'مراجعة معلومات المحصول المتاحة وتحديد خطوات التعامل أو الفحص أو التعبئة المنطبقة.'],
                        ['title' => 'توحيد السجلات والتعبئة', 'description' => 'الاتفاق مع الجهات المسؤولة على بيانات الدفعات وتفاصيل العبوة ومستندات الوجهة ذات الصلة.'],
                        ['title' => 'المراجعة قبل الشحن', 'description' => 'مراجعة المتطلبات المتفق عليها ومشاركة مستندات المنتج أو المنشأة التي تم التحقق منها عند انطباقها.'],
                    ],
                    'frameworks_heading' => 'معايير وأطر مرجعية نسترشد بها',
                    'frameworks_intro' => 'تساعد هذه المعايير والأطر الدولية على تنظيم مناقشات الجودة ومتطلبات المشترين عندما تكون ذات صلة بالمنتج أو العملية.',
                    'frameworks_note' => 'ذكر هذه الأطر لا يعني أن ENCAP أو أي مورد بعينه حاصل على الشهادة أو أتم التدقيق. يتم التحقق من الحالة الحالية للمزرعة أو المنتج أو المنشأة المعنية ولا تتم الإشارة إليها إلا بوثائق مؤكدة.',
                    'frameworks' => [
                        ['code' => 'BRCGS', 'name' => 'BRCGS Food Safety (BRC)', 'description' => 'متطلبات سلامة الغذاء وسلامة المنتج والضوابط التشغيلية للعمليات الغذائية ذات الصلة.'],
                        ['code' => 'G.A.P.', 'name' => 'GLOBALG.A.P.', 'description' => 'الممارسات الزراعية الجيدة وإدارة المزرعة والتتبع عند انطباقها.'],
                        ['code' => 'ISO', 'name' => 'ISO 9001', 'description' => 'مبادئ إدارة الجودة واتساق العمليات والتحسين المستمر.'],
                        ['code' => 'GRASP', 'name' => 'GRASP', 'description' => 'إضافة ضمن GLOBALG.A.P. تتناول الممارسات الاجتماعية والمتطلبات المتعلقة بالعاملين.'],
                        ['code' => 'SEDEX', 'name' => 'SEDEX / SMETA', 'description' => 'أطر لرؤية سلسلة التوريد وتقييم الممارسات التجارية الأخلاقية، وليست شهادة للشركة.'],
                    ],
                ],
            ],
            'export' => [
                'en' => [
                    'eyebrow' => 'Export & Logistics',
                    'title' => 'Export coordination built around your market',
                    'intro' => 'From an initial buyer brief to shipment planning, each step starts with the product, volume and destination requirements.',
                    'body' => 'ENCAP coordinates product sourcing, agreed specifications, preparation and export steps for each enquiry. Documentation, inspection and freight arrangements depend on the product and destination and may be coordinated with relevant service partners.',
                    'services_heading' => 'Export support, step by step',
                    'services_intro' => 'We bring the buyer requirement and practical supply steps into one clear enquiry.',
                    'services' => [
                        ['title' => 'Export sourcing', 'description' => 'Review the requested crop, target volume, season and available sourcing options before confirming supply.'],
                        ['title' => 'Specifications & packing requirements', 'description' => 'Agree product grade, size, presentation, carton format, labelling and destination needs with the buyer.'],
                        ['title' => 'Documentation & compliance coordination', 'description' => 'Identify applicable destination requirements and coordinate relevant documents, inspections or certificates with responsible parties.'],
                        ['title' => 'Shipping & logistics coordination', 'description' => 'Coordinate collection, storage, container or transport needs with suitable logistics partners where required.'],
                    ],
                    'workflow_heading' => 'From enquiry to shipment planning',
                    'workflow' => [
                        ['title' => 'Share the buyer brief', 'description' => 'Confirm product, target quantity, destination and requested delivery window.'],
                        ['title' => 'Review crop availability', 'description' => 'Check seasonal supply options and whether the required specification can be met.'],
                        ['title' => 'Align preparation and documents', 'description' => 'Agree packing details and identify destination-specific steps before confirmation.'],
                        ['title' => 'Coordinate dispatch', 'description' => 'Plan transport and shipment handover according to the agreed order and partner availability.'],
                    ],
                ],
                'ar' => [
                    'eyebrow' => 'التصدير والخدمات اللوجستية',
                    'title' => 'تنسيق التصدير وفق متطلبات سوقك',
                    'intro' => 'من طلب المشتري الأولي إلى التخطيط للشحن، تبدأ كل خطوة من المنتج والكمية ومتطلبات الوجهة.',
                    'body' => 'تنسق ENCAP توريد المنتج والمواصفات المتفق عليها والتجهيز وخطوات التصدير لكل استفسار. وتعتمد المستندات والفحوصات وترتيبات الشحن على المنتج والوجهة، وقد يتم تنسيقها مع مزودي الخدمات المعنيين.',
                    'services_heading' => 'دعم التصدير خطوة بخطوة',
                    'services_intro' => 'نجمع متطلبات المشتري وخطوات التوريد العملية في مسار استفسار واضح.',
                    'services' => [
                        ['title' => 'توريد المحاصيل للتصدير', 'description' => 'مراجعة المحصول المطلوب والكمية والموسم وخيارات التوريد المتاحة قبل تأكيد التوريد.'],
                        ['title' => 'المواصفات ومتطلبات التعبئة', 'description' => 'الاتفاق مع المشتري على درجة المنتج وحجمه وطريقة عرضه وشكل الكرتونة والملصقات ومتطلبات الوجهة.'],
                        ['title' => 'تنسيق المستندات والامتثال', 'description' => 'تحديد متطلبات الوجهة المنطبقة وتنسيق المستندات أو الفحوصات أو الشهادات ذات الصلة مع الجهات المسؤولة.'],
                        ['title' => 'تنسيق الشحن والخدمات اللوجستية', 'description' => 'تنسيق الاستلام والتخزين واحتياجات الحاويات أو النقل مع شركاء الخدمات المناسبين عند الحاجة.'],
                    ],
                    'workflow_heading' => 'من الاستفسار إلى التخطيط للشحن',
                    'workflow' => [
                        ['title' => 'استلام متطلبات المشتري', 'description' => 'تحديد المنتج والكمية المستهدفة والوجهة والفترة المطلوبة للتسليم.'],
                        ['title' => 'مراجعة توافر المحصول', 'description' => 'مراجعة خيارات التوريد الموسمية وإمكانية تلبية المواصفات المطلوبة.'],
                        ['title' => 'تحديد التجهيز والمستندات', 'description' => 'الاتفاق على تفاصيل التعبئة وتحديد الخطوات الخاصة بالوجهة قبل التأكيد.'],
                        ['title' => 'تنسيق الإرسال', 'description' => 'تخطيط النقل وتسليم الشحنة وفق الطلب المتفق عليه وتوافر الشركاء.'],
                    ],
                ],
            ],
        ];

        abort_unless(isset($content[$page]), 404);

        $pageContent = $content[$page][$locale];

        $serviceMedia = [
            'farms' => [
                'hero' => \App\Support\EncapImage::url('orchard-harvest-2026.jpg', 'https://images.unsplash.com/photo-1643138411076-238fda8c1e1d?fit=crop&fm=jpg&w=1400&q=85'),
                'hero_alt' => [
                    'en' => 'A grower reaching into an orchard to harvest fruit.',
                    'ar' => 'عامل يقطف ثمارًا من شجرة في بستان.',
                ],
                'heading' => [
                    'en' => 'The right supply starts with the crop.',
                    'ar' => 'التوريد الجيد يبدأ من المحصول.',
                ],
                'detail' => [
                    'en' => 'We review season, quantity, grade, source and destination before confirming an available supply option.',
                    'ar' => 'نراجع الموسم والكمية والدرجة والمصدر والوجهة قبل تأكيد خيار التوريد المتاح.',
                ],
                'services' => [
                    [
                        'image' => \App\Support\EncapImage::url('citrus-orchard-2026.jpg', 'https://images.unsplash.com/photo-1705573382809-bbf1a337746f?fit=crop&fm=jpg&w=720&q=85'),
                        'alt' => ['en' => 'Citrus fruit growing on a tree branch.', 'ar' => 'ثمار موالح تنمو على أغصان الشجرة.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('farm-cultivation-2026.jpg', 'https://images.unsplash.com/photo-1719757168062-3ceb06988532?fit=crop&fm=jpg&w=720&q=85'),
                        'alt' => ['en' => 'A cultivated field ready for crop care and harvest planning.', 'ar' => 'أرض زراعية مزروعة ضمن مراحل العناية بالمحصول والتخطيط للحصاد.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('field-origin-2026.jpg', 'https://images.unsplash.com/photo-1775931438023-f042dfda9a14?fit=crop&fm=jpg&w=720&q=85'),
                        'alt' => ['en' => 'Rows of agricultural land extending across a broad landscape.', 'ar' => 'صفوف من الأراضي الزراعية تمتد عبر مساحة واسعة.'],
                    ],
                ],
            ],
            'packing-stations' => [
                'hero' => \App\Support\EncapImage::url('potato-sorting-2026.jpg', 'https://images.pexels.com/photos/2889093/pexels-photo-2889093.jpeg?auto=compress&cs=tinysrgb&w=1400'),
                'hero_alt' => [
                    'en' => 'Freshly harvested potatoes moving through a sorting conveyor.',
                    'ar' => 'محصول بطاطس طازج يمر على سير للفرز.',
                ],
                'heading' => [
                    'en' => 'Every packing plan starts with the product specification.',
                    'ar' => 'كل خطة تعبئة تبدأ من مواصفات المنتج.',
                ],
                'detail' => [
                    'en' => 'The crop, buyer requirements and selected facility determine which post-harvest steps are suitable and included in the quotation.',
                    'ar' => 'يحدد المحصول ومتطلبات المشتري والمحطة المختارة خطوات ما بعد الحصاد المناسبة وما يدخل ضمن عرض السعر.',
                ],
                'services' => [
                    [
                        'image' => \App\Support\EncapImage::url('potato-sorting-2026.jpg', 'https://images.pexels.com/photos/2889093/pexels-photo-2889093.jpeg?auto=compress&cs=tinysrgb&w=720'),
                        'alt' => ['en' => 'Potatoes travelling along a sorting conveyor.', 'ar' => 'بطاطس تمر على سير لفرز المحصول.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('produce-packing-2026.jpg', 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=720'),
                        'alt' => ['en' => 'Fresh berries being sorted by workers at a packing line.', 'ar' => 'عمال يفرزون ثمارًا طازجة على خط تجهيز.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('produce-packing-2026.jpg', 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=720'),
                        'alt' => ['en' => 'People preparing and handling cartons in a warehouse.', 'ar' => 'عاملون يجهزون ويتعاملون مع الكراتين داخل مخزن.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('crate-labels-2026.jpg', 'https://images.unsplash.com/photo-1625207883754-c0314540c03c?fit=crop&fm=jpg&w=720&q=85'),
                        'alt' => ['en' => 'Cardboard cartons prepared with produce labels.', 'ar' => 'كراتين مجهزة بملصقات خاصة بالمنتجات.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('export-crates-2026.jpg', 'https://images.pexels.com/photos/7843989/pexels-photo-7843989.jpeg?auto=compress&cs=tinysrgb&w=720'),
                        'alt' => ['en' => 'Organized cartons arranged for storage and dispatch planning.', 'ar' => 'كراتين مرتبة للتخزين والتجهيز للإرسال.'],
                    ],
                ],
            ],
            'export' => [
                'hero' => \App\Support\EncapImage::url('cargo-port-2026.jpg', 'https://images.unsplash.com/photo-1759272840538-ae4b07214c71?fit=crop&fm=jpg&w=1400&q=85'),
                'hero_alt' => [
                    'en' => 'Cargo containers stacked at an international port terminal.',
                    'ar' => 'حاويات بضائع مكدسة في محطة ميناء دولي.',
                ],
                'heading' => [
                    'en' => 'A clear route from buyer requirements to shipment planning.',
                    'ar' => 'مسار واضح من متطلبات المشتري إلى التخطيط للشحن.',
                ],
                'detail' => [
                    'en' => 'Product availability, packing, documents and transport arrangements are checked against the requested destination before the order is confirmed.',
                    'ar' => 'تتم مراجعة توافر المنتج والتعبئة والمستندات وترتيبات النقل وفق الوجهة المطلوبة قبل تأكيد الطلب.',
                ],
                'services' => [
                    [
                        'image' => \App\Support\EncapImage::url('citrus-orchard-2026.jpg', 'https://images.unsplash.com/photo-1705573382809-bbf1a337746f?fit=crop&fm=jpg&w=720&q=85'),
                        'alt' => ['en' => 'Citrus fruit growing on a tree, representing farm-level sourcing.', 'ar' => 'ثمار موالح على الشجرة لتمثيل التوريد من مصدر المحصول.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('crate-labels-2026.jpg', 'https://images.unsplash.com/photo-1625207883754-c0314540c03c?fit=crop&fm=jpg&w=720&q=85'),
                        'alt' => ['en' => 'Produce cartons with labels prepared for a supply order.', 'ar' => 'كراتين منتجات عليها ملصقات ومجهزة لطلب توريد.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('produce-packing-2026.jpg', 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=720'),
                        'alt' => ['en' => 'Cartons handled and arranged in a warehouse before dispatch.', 'ar' => 'كراتين يجري تجهيزها وترتيبها في مخزن قبل الإرسال.'],
                    ],
                    [
                        'image' => \App\Support\EncapImage::url('export-crates-2026.jpg', 'https://images.pexels.com/photos/7843989/pexels-photo-7843989.jpeg?auto=compress&cs=tinysrgb&w=720'),
                        'alt' => ['en' => 'Organized boxes illustrating shipment and supply-chain preparation.', 'ar' => 'صناديق مرتبة توضح تجهيز الشحنات وسلسلة التوريد.'],
                    ],
                ],
            ],
            'quality' => [
                'hero' => \App\Support\EncapImage::url('produce-packing-2026.jpg', 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=1400'),
                'hero_alt' => ['en' => 'Produce moving through a packing line for handling and quality review.', 'ar' => 'منتجات زراعية على خط تعبئة أثناء التجهيز ومراجعة الجودة.'],
                'heading' => ['en' => 'Good quality is planned before dispatch.', 'ar' => 'الجودة الجيدة يجري التخطيط لها قبل الإرسال.'],
                'detail' => ['en' => 'Align specifications, handling, packing and destination requirements before the shipment plan is confirmed.', 'ar' => 'توحيد المواصفات والتعامل مع المنتج والتعبئة ومتطلبات الوجهة قبل تأكيد خطة الشحن.'],
                'services' => [
                    ['image' => \App\Support\EncapImage::url('crate-labels-2026.jpg', 'https://images.unsplash.com/photo-1625207883754-c0314540c03c?fit=crop&fm=jpg&w=900&q=85'), 'alt' => ['en' => 'Produce cartons and labels prepared for order identification.', 'ar' => 'كراتين منتجات وملصقات لتحديد بيانات الطلب.']],
                    ['image' => \App\Support\EncapImage::url('potato-sorting-2026.jpg', 'https://images.pexels.com/photos/2889093/pexels-photo-2889093.jpeg?auto=compress&cs=tinysrgb&w=900'), 'alt' => ['en' => 'Harvested potatoes on a sorting line.', 'ar' => 'بطاطس بعد الحصاد على خط للفرز.']],
                    ['image' => \App\Support\EncapImage::url('produce-packing-2026.jpg', 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=900'), 'alt' => ['en' => 'Produce cartons handled in a packing warehouse.', 'ar' => 'كراتين منتجات زراعية يجري التعامل معها في مخزن تعبئة.']],
                    ['image' => \App\Support\EncapImage::url('produce-packing-2026.jpg', 'https://images.pexels.com/photos/8877381/pexels-photo-8877381.jpeg?auto=compress&cs=tinysrgb&w=900'), 'alt' => ['en' => 'Fresh berries being handled along a produce packing line.', 'ar' => 'ثمار طازجة يجري التعامل معها على خط تعبئة.']],
                ],
            ],
        ];

        if (isset($serviceMedia[$page])) {
            $visuals = $serviceMedia[$page];
            $pageContent['hero_image'] = $visuals['hero'];
            $pageContent['hero_image_alt'] = $visuals['hero_alt'][$locale];
            $pageContent['visual_heading'] = $visuals['heading'][$locale];
            $pageContent['visual_detail'] = $visuals['detail'][$locale];

            foreach ($pageContent['services'] as $index => &$service) {
                if (isset($visuals['services'][$index])) {
                    $service['image'] = $visuals['services'][$index]['image'];
                    $service['image_alt'] = $visuals['services'][$index]['alt'][$locale];
                }
            }
            unset($service);
        }

        $settings = SeoSetting::current();
        $pageSeo = $settings->metadataFor(
            $page,
            $locale,
            $pageContent['title'] . ' | ENCAP',
            $pageContent['intro']
        );

        $categories = match ($page) {
            'about' => Category::query()->where('is_published', true)->whereNotNull('image')->orderBy('sort_order')->orderBy('name_en')->limit(4)->get(),
            default => collect(),
        };

        return view('website.content-page', compact('locale', 'page', 'pageContent', 'categories', 'pageSeo'));
    }

    public function contact(string $locale): View
    {
        $this->useLocale($locale);
        $settings = SeoSetting::current();
        $pageSeo = $settings->metadataFor(
            'contact',
            $locale,
            __('Contact ENCAP | Agricultural Export Enquiries'),
            __('Contact ENCAP about agricultural products, wholesale supply and export requirements.')
        );

        return view('website.contact', compact('locale', 'settings', 'pageSeo'));
    }

    public function storeContact(Request $request, string $locale): RedirectResponse
    {
        $this->useLocale($locale);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['required', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'size:0'],
        ]);

        unset($validated['website']);
        ContactMessage::create($validated);

        return back()->with('success', __('Your message has been received. Our team will get back to you soon.'));
    }

    public function quote(Request $request, string $locale): View
    {
        $this->useLocale($locale);
        $categories = Category::query()->where('is_published', true)
            ->orderBy('name_en')->get(['id', 'name_en', 'name_ar']);

        $settings = SeoSetting::current();
        $pageSeo = $settings->metadataFor(
            'quote',
            $locale,
            __('Request a Quote | ENCAP'),
            __('Send ENCAP your agricultural sourcing and export requirements.')
        );

        $pageSeo['robots'] = 'noindex,follow';

        $requestedForm = $request->query('form');
        $selectedForm = is_string($requestedForm)
            && in_array($requestedForm, ['fresh', 'frozen', 'processed'], true)
                ? $requestedForm
                : null;

        return view('website.quote', [
            'categories' => $categories,
            'selectedCategory' => $request->integer('category') ?: null,
            'selectedForm' => $selectedForm,
            'locale' => $locale,
            'pageSeo' => $pageSeo,
        ]);
    }

    public function storeQuote(Request $request, string $locale): RedirectResponse
    {
        $this->useLocale($locale);

        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('is_published', true)],
            'contact_name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'country' => ['required', 'string', 'max:100'],
            'quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999999'],
            'quantity_unit' => ['nullable', 'string', 'max:40'],
            'product_form' => ['nullable', 'in:fresh,frozen,processed'],
            'packaging_requirements' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'size:0'],
        ]);

        unset($validated['website']);
        QuoteRequest::create($validated);

        return back()->with('success', __('Your request has been received. Our team will contact you soon.'));
    }
}
