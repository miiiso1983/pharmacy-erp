<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Invoice;
use App\Models\Collection;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء موردين
        $suppliers = [
            ['name' => 'شركة الأدوية المتقدمة', 'contact_person' => 'أحمد محمد', 'phone' => '0501234567', 'email' => 'supplier1@example.com', 'address' => 'الرياض', 'status' => 'active'],
            ['name' => 'مؤسسة الصحة الطبية', 'contact_person' => 'فاطمة علي', 'phone' => '0507654321', 'email' => 'supplier2@example.com', 'address' => 'جدة', 'status' => 'active'],
            ['name' => 'شركة الدواء الشامل', 'contact_person' => 'محمد سالم', 'phone' => '0509876543', 'email' => 'supplier3@example.com', 'address' => 'الدمام', 'status' => 'active'],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // إنشاء عناصر (أدوية)
        $items = [
            ['name' => 'باراسيتامول 500 مجم', 'code' => 'PARA500', 'category' => 'مسكنات', 'unit' => 'قرص', 'price' => 2.50, 'cost' => 1.50, 'stock_quantity' => 1000, 'min_stock_level' => 100, 'supplier_id' => 1, 'barcode' => '1234567890123', 'status' => 'active'],
            ['name' => 'أموكسيسيلين 250 مجم', 'code' => 'AMOX250', 'category' => 'مضادات حيوية', 'unit' => 'كبسولة', 'price' => 5.00, 'cost' => 3.00, 'stock_quantity' => 500, 'min_stock_level' => 50, 'supplier_id' => 1, 'barcode' => '1234567890124', 'status' => 'active'],
            ['name' => 'إيبوبروفين 400 مجم', 'code' => 'IBU400', 'category' => 'مسكنات', 'unit' => 'قرص', 'price' => 3.00, 'cost' => 2.00, 'stock_quantity' => 800, 'min_stock_level' => 80, 'supplier_id' => 2, 'barcode' => '1234567890125', 'status' => 'active'],
            ['name' => 'أسبرين 100 مجم', 'code' => 'ASP100', 'category' => 'مسكنات', 'unit' => 'قرص', 'price' => 1.50, 'cost' => 1.00, 'stock_quantity' => 1200, 'min_stock_level' => 120, 'supplier_id' => 2, 'barcode' => '1234567890126', 'status' => 'active'],
            ['name' => 'فيتامين د 1000 وحدة', 'code' => 'VITD1000', 'category' => 'فيتامينات', 'unit' => 'كبسولة', 'price' => 8.00, 'cost' => 5.00, 'stock_quantity' => 300, 'min_stock_level' => 30, 'supplier_id' => 3, 'barcode' => '1234567890127', 'status' => 'active'],
            ['name' => 'شراب السعال للأطفال', 'code' => 'COUGH100', 'category' => 'أدوية الأطفال', 'unit' => 'زجاجة', 'price' => 15.00, 'cost' => 10.00, 'stock_quantity' => 200, 'min_stock_level' => 20, 'supplier_id' => 3, 'barcode' => '1234567890128', 'status' => 'active'],
            ['name' => 'كريم مضاد للفطريات', 'code' => 'ANTIFUNG', 'category' => 'كريمات', 'unit' => 'أنبوب', 'price' => 12.00, 'cost' => 8.00, 'stock_quantity' => 150, 'min_stock_level' => 15, 'supplier_id' => 1, 'barcode' => '1234567890129', 'status' => 'active'],
            ['name' => 'قطرة عين مضاد حيوي', 'code' => 'EYEDROP', 'category' => 'قطرات', 'unit' => 'زجاجة', 'price' => 20.00, 'cost' => 15.00, 'stock_quantity' => 100, 'min_stock_level' => 10, 'supplier_id' => 2, 'barcode' => '1234567890130', 'status' => 'active'],
            ['name' => 'أنسولين سريع المفعول', 'code' => 'INSULIN', 'category' => 'أدوية السكري', 'unit' => 'قلم', 'price' => 80.00, 'cost' => 60.00, 'stock_quantity' => 50, 'min_stock_level' => 5, 'supplier_id' => 3, 'barcode' => '1234567890131', 'status' => 'active'],
            ['name' => 'دواء ضغط الدم', 'code' => 'BPMED', 'category' => 'أدوية القلب', 'unit' => 'قرص', 'price' => 6.00, 'cost' => 4.00, 'stock_quantity' => 600, 'min_stock_level' => 60, 'supplier_id' => 1, 'barcode' => '1234567890132', 'status' => 'active'],
        ];

        foreach ($items as $itemData) {
            Item::create($itemData);
        }

        // الحصول على العميل التجريبي
        $customer = User::where('email', 'customer@pharmacy-erp.com')->first();
        $admin = User::where('email', 'admin@pharmacy-erp.com')->first();

        if ($customer && $admin) {
            // إنشاء طلبات تجريبية
            for ($i = 1; $i <= 10; $i++) {
                $order = Order::create([
                    'customer_id' => $customer->id,
                    'status' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered'][array_rand(['pending', 'confirmed', 'processing', 'shipped', 'delivered'])],
                    'delivery_address' => 'الرياض، حي النخيل، شارع الملك فهد',
                    'delivery_date' => now()->addDays(rand(1, 7)),
                    'notes' => 'طلب تجريبي رقم ' . $i,
                    'created_by' => $admin->id,
                ]);

                $subtotal = 0;
                $itemsCount = rand(2, 5);
                $selectedItems = Item::inRandomOrder()->limit($itemsCount)->get();

                foreach ($selectedItems as $item) {
                    $quantity = rand(1, 10);
                    $unitPrice = $item->price;
                    $totalPrice = $unitPrice * $quantity;
                    $subtotal += $totalPrice;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_id' => $item->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                    ]);
                }

                $taxAmount = $subtotal * 0.15;
                $totalAmount = $subtotal + $taxAmount;

                $order->update([
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                ]);

                // إنشاء فاتورة
                $invoice = Invoice::create([
                    'order_id' => $order->id,
                    'customer_id' => $customer->id,
                    'subtotal' => $subtotal,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'due_date' => now()->addDays(30),
                ]);

                // إنشاء تحصيلات عشوائية
                if (rand(0, 1)) {
                    $collectionAmount = rand(50, 100) / 100 * $totalAmount;
                    Collection::create([
                        'invoice_id' => $invoice->id,
                        'customer_id' => $customer->id,
                        'amount' => $collectionAmount,
                        'payment_method' => ['cash', 'bank_transfer', 'check', 'credit_card'][array_rand(['cash', 'bank_transfer', 'check', 'credit_card'])],
                        'collection_date' => now()->subDays(rand(0, 10)),
                        'collected_by' => $admin->id,
                        'notes' => 'تحصيل تجريبي',
                    ]);
                }
            }
        }

        $this->command->info('تم إنشاء البيانات التجريبية بنجاح!');
        $this->command->info('- 3 موردين');
        $this->command->info('- 10 عناصر (أدوية)');
        $this->command->info('- 10 طلبات مع عناصرها');
        $this->command->info('- 10 فواتير');
        $this->command->info('- تحصيلات عشوائية');
    }
}
