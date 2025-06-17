<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'المخزن الرئيسي - بغداد',
                'code' => 'WH001',
                'description' => 'المخزن الرئيسي للشركة في العاصمة بغداد',
                'city' => 'بغداد',
                'area' => 'الكرادة',
                'address' => 'شارع الكرادة الداخل، مجمع الأطباء التجاري، الطابق الثاني',
                'phone' => '07901234567',
                'manager' => 'أحمد محمد علي',
                'type' => 'main',
                'status' => 'active',
                'total_value' => 8500000,
                'total_items' => 1247,
                'notes' => 'المخزن الرئيسي يحتوي على جميع أنواع الأدوية والمستلزمات الطبية'
            ],
            [
                'name' => 'صيدلية النور - البصرة',
                'code' => 'PH001',
                'description' => 'صيدلية متخصصة في الأدوية العامة والمستلزمات الطبية',
                'city' => 'البصرة',
                'area' => 'العشار',
                'address' => 'شارع الاستقلال، مقابل مستشفى البصرة العام',
                'phone' => '07801234567',
                'manager' => 'فاطمة علي حسن',
                'type' => 'pharmacy',
                'status' => 'active',
                'total_value' => 2100000,
                'total_items' => 456,
                'notes' => 'صيدلية تخدم منطقة العشار والمناطق المجاورة'
            ],
            [
                'name' => 'مخزن الموصل الفرعي',
                'code' => 'WH002',
                'description' => 'مخزن فرعي لخدمة محافظة نينوى والمحافظات الشمالية',
                'city' => 'الموصل',
                'area' => 'الجامعة',
                'address' => 'حي الجامعة، شارع فلسطين، مجمع الأطباء',
                'phone' => '07701234567',
                'manager' => 'عمر حسن محمود',
                'type' => 'branch',
                'status' => 'active',
                'total_value' => 3200000,
                'total_items' => 789,
                'notes' => 'يخدم المحافظات الشمالية ويوزع على الصيدليات المحلية'
            ],
            [
                'name' => 'مركز توزيع أربيل',
                'code' => 'DC001',
                'description' => 'مركز توزيع متخصص في إقليم كردستان',
                'city' => 'أربيل',
                'area' => 'عنكاوا',
                'address' => 'منطقة عنكاوا، الشارع الرئيسي، مجمع التوزيع الطبي',
                'phone' => '07501234567',
                'manager' => 'كريم أحمد رشيد',
                'type' => 'distribution',
                'status' => 'active',
                'total_value' => 1800000,
                'total_items' => 345,
                'notes' => 'مركز توزيع يخدم إقليم كردستان والمناطق المجاورة'
            ],
            [
                'name' => 'صيدلية الشفاء - النجف',
                'code' => 'PH002',
                'description' => 'صيدلية في المدينة المقدسة تخدم الزوار والمواطنين',
                'city' => 'النجف',
                'area' => 'المركز',
                'address' => 'شارع الرسول، قرب الحرم الشريف',
                'phone' => '07601234567',
                'manager' => 'حيدر عبد الحسين',
                'type' => 'pharmacy',
                'status' => 'active',
                'total_value' => 950000,
                'total_items' => 234,
                'notes' => 'تخدم زوار الإمام علي (ع) والمواطنين المحليين'
            ]
        ];

        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
