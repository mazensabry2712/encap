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
                'en' => ['eyebrow' => 'About ENCAP', 'title' => 'Agriculture with a clear route to market', 'intro' => 'ENCAP connects Egyptian agricultural production with the needs of wholesale buyers and international markets.', 'body' => 'Our approach combines production from our own farms with sourcing from trusted partner farms. Product availability, specifications and fulfilment details are confirmed for each enquiry.'],
                'ar' => ['eyebrow' => 'عن ENCAP', 'title' => 'إنتاج زراعي بطريق واضح إلى الأسواق', 'intro' => 'تربط ENCAP الإنتاج الزراعي المصري باحتياجات تجار الجملة والأسواق الدولية.', 'body' => 'يجمع نهجنا بين الإنتاج في مزارعنا والتوريد من المزارع الشريكة الموثوقة. ويتم تأكيد توافر المنتجات ومواصفاتها وتفاصيل التوريد لكل طلب.'],
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
                'en' => ['eyebrow' => 'Quality & Processing', 'title' => 'Specifications agreed before supply', 'intro' => 'Clear product requirements help buyers and suppliers align on the details that matter.', 'body' => 'Grading, preparation, processing and packaging requirements are discussed and confirmed per enquiry. Specific certifications or capabilities are shared only when verified for the requested product.'],
                'ar' => ['eyebrow' => 'الجودة والتجهيز', 'title' => 'تأكيد المواصفات قبل التوريد', 'intro' => 'تساعد متطلبات المنتج الواضحة المشترين والموردين على الاتفاق على التفاصيل المهمة.', 'body' => 'تتم مناقشة وتأكيد متطلبات الفرز والتجهيز والتصنيع والتعبئة لكل طلب. ولا نعرض شهادات أو قدرات محددة إلا بعد التحقق من انطباقها على المنتج المطلوب.'],
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
            'gallery' => [
                'en' => ['eyebrow' => 'Gallery', 'title' => 'A look at our product range', 'intro' => 'Browse product images currently available in our catalogue.', 'body' => 'We will add verified farm, harvest and processing photography as company-approved images become available.'],
                'ar' => ['eyebrow' => 'معرض الصور', 'title' => 'نظرة على مجموعة منتجاتنا', 'intro' => 'تصفح صور المنتجات المتاحة حاليًا في كتالوجنا.', 'body' => 'سنضيف صور المزارع والحصاد والتجهيز المعتمدة من الشركة عند توفرها.'],
            ],
        ];

        abort_unless(isset($content[$page]), 404);

        $pageContent = $content[$page][$locale];
        $settings = SeoSetting::current();
        $pageSeo = $settings->metadataFor(
            $page,
            $locale,
            $pageContent['title'] . ' | ENCAP',
            $pageContent['intro']
        );

        $categories = $page === 'gallery'
            ? Category::query()->where('is_published', true)->whereNotNull('image')->orderBy('sort_order')->get()
            : collect();

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

        return view('website.quote', [
            'categories' => $categories,
            'selectedCategory' => $request->integer('category') ?: null,
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
