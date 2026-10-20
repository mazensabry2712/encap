<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\QuoteRequest;
use App\Models\SeoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

        return view('website.products.show', compact('category', 'locale'));
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
                'en' => ['eyebrow' => 'Farms & Production', 'title' => 'Closer to the origin', 'intro' => 'Our supply approach starts with agricultural production and extends through trusted partner farms.', 'body' => 'Crop availability changes with season and harvest. Contact our team to confirm current products, quantities and sourcing requirements before placing an order.'],
                'ar' => ['eyebrow' => 'المزارع والإنتاج', 'title' => 'أقرب إلى مصدر المنتج', 'intro' => 'يبدأ نهج التوريد لدينا من الإنتاج الزراعي ويمتد إلى المزارع الشريكة الموثوقة.', 'body' => 'يتغير توافر المحاصيل حسب الموسم والحصاد. تواصل مع فريقنا لتأكيد المنتجات والكميات ومتطلبات التوريد الحالية قبل الطلب.'],
            ],
            'quality' => [
                'en' => ['eyebrow' => 'Quality & Processing', 'title' => 'Specifications agreed before supply', 'intro' => 'Clear product requirements help buyers and suppliers align on the details that matter.', 'body' => 'Grading, preparation, processing and packaging requirements are discussed and confirmed per enquiry. Specific certifications or capabilities are shared only when verified for the requested product.'],
                'ar' => ['eyebrow' => 'الجودة والتجهيز', 'title' => 'تأكيد المواصفات قبل التوريد', 'intro' => 'تساعد متطلبات المنتج الواضحة المشترين والموردين على الاتفاق على التفاصيل المهمة.', 'body' => 'تتم مناقشة وتأكيد متطلبات الفرز والتجهيز والتصنيع والتعبئة لكل طلب. ولا نعرض شهادات أو قدرات محددة إلا بعد التحقق من انطباقها على المنتج المطلوب.'],
            ],
            'export' => [
                'en' => ['eyebrow' => 'Export', 'title' => 'A straightforward enquiry-to-supply process', 'intro' => 'Tell us what you need, where it is going and how it should be prepared.', 'body' => 'Our team reviews the product, volume, form, packaging and destination requirements, then follows up to confirm availability and practical next steps.'],
                'ar' => ['eyebrow' => 'التصدير', 'title' => 'خطوات واضحة من الطلب إلى التوريد', 'intro' => 'أخبرنا بما تحتاجه والوجهة المطلوبة وطريقة تجهيز المنتج.', 'body' => 'يراجع فريقنا المنتج والكمية والشكل والتعبئة ومتطلبات الوجهة، ثم يتابع معك لتأكيد التوافر والخطوات العملية التالية.'],
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
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'contact_name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'country' => ['required', 'string', 'max:100'],
            'quantity' => ['nullable', 'numeric', 'min:0.001', 'max:999999999'],
            'quantity_unit' => ['nullable', 'string', 'max:40'],
            'product_form' => ['nullable', 'in:fresh,frozen,processed'],
            'packaging_requirements' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if (!empty($validated['category_id'])) {
            abort_unless(
                Category::query()->whereKey($validated['category_id'])->where('is_published', true)->exists(),
                422
            );
        }

        QuoteRequest::create($validated);

        return back()->with('success', __('Your request has been received. Our team will contact you soon.'));
    }
}
