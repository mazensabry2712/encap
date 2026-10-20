<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Mango', 'مانجو', 'mango'],
            ['Citrus', 'موالح', 'citrus'],
            ['Grapes', 'عنب', 'grapes'],
            ['Potatoes', 'بطاطس', 'potatoes'],
            ['Onions', 'بصل', 'onions'],
            ['Garlic', 'ثوم', 'garlic'],
            ['Pomegranates', 'رمان', 'pomegranates'],
            ['Peppers', 'فلفل', 'peppers'],
            ['Strawberries', 'فراولة', 'strawberries'],
            ['Other Fruits & Vegetables', 'فواكه وخضروات أخرى', 'other-fruits-vegetables'],
        ];

        foreach ($categories as $index => [$name, $arabic, $slug]) {
            Category::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name_en' => $name,
                    'name_ar' => $arabic,
                    'short_description_en' => 'Contact ENCAP for current availability and export specifications.',
                    'short_description_ar' => 'تواصل مع ENCAP لمعرفة التوافر الحالي ومواصفات التصدير.',
                    'description_en' => 'Product availability, specifications, grades and packaging are confirmed for each buyer enquiry.',
                    'description_ar' => 'يتم تأكيد التوافر والمواصفات والدرجات والتعبئة وفقًا لكل طلب من المشترين.',
                    'available_forms' => ['Fresh', 'Frozen', 'Processed'],
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
