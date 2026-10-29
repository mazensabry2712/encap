<?php

use App\Models\SeoSetting;
use App\Models\User;

test('admins can edit visible copy for homepage, static pages and frozen catalogue in all website locales', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.seo.edit'))
        ->assertOk()
        ->assertSee('page_content[home][ar][hero_title]', false)
        ->assertSee('page_content[farms][en][title]', false)
        ->assertSee('page_content[frozen-products][fr][section_heading]', false)
        ->assertSee('محتوى صفحات الموقع');

    $this->from(route('admin.seo.edit'))->put(route('admin.seo.update'), [
        'site_title_en' => 'ENCAP Content Test',
        'site_title_ar' => 'اختبار محتوى ENCAP',
        'default_robots' => 'index,follow',
        'organization_name' => 'ENCAP',
        'page_content' => [
            'home' => [
                'ar' => ['hero_title' => 'عنوان الرئيسية من لوحة الإدارة'],
                'en' => ['hero_title' => 'Homepage heading from admin'],
            ],
            'farms' => ['en' => ['title' => 'Farm content from admin']],
            'frozen-products' => [
                'ar' => ['title' => 'قسم المجمدات من لوحة الإدارة'],
                'fr' => ['section_heading' => 'Produits surgelés modifiables'],
            ],
        ],
    ])->assertRedirect(route('admin.seo.edit'))->assertSessionHas('success');

    expect(SeoSetting::current()->page_content['home']['ar']['hero_title'])
        ->toBe('عنوان الرئيسية من لوحة الإدارة')
        ->and(SeoSetting::current()->page_content['farms']['en']['title'])
        ->toBe('Farm content from admin');

    $this->get('/ar')->assertOk()->assertSee('عنوان الرئيسية من لوحة الإدارة');
    $this->get('/en/farms')->assertOk()->assertSee('Farm content from admin');
    $this->get('/ar/frozen-products')->assertOk()->assertSee('قسم المجمدات من لوحة الإدارة');
    $this->get('/fr/frozen-products')->assertOk()->assertSee('Produits surgelés modifiables');
});
