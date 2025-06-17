<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            // صلاحيات الطلبات
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'manage_order_status',

            // صلاحيات العناصر
            'view_items',
            'create_items',
            'edit_items',
            'delete_items',
            'manage_inventory',

            // صلاحيات الفواتير
            'view_invoices',
            'create_invoices',
            'edit_invoices',
            'delete_invoices',

            // صلاحيات التحصيلات
            'view_collections',
            'create_collections',
            'edit_collections',
            'delete_collections',

            // صلاحيات المرتجعات
            'view_returns',
            'create_returns',
            'edit_returns',
            'process_returns',

            // صلاحيات الموردين
            'view_suppliers',
            'create_suppliers',
            'edit_suppliers',
            'delete_suppliers',

            // صلاحيات المستخدمين
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_roles',

            // صلاحيات التقارير
            'view_reports',
            'export_reports',
            'view_financial_reports',

            // صلاحيات النظام
            'access_admin_panel',
            'manage_system_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إنشاء الأدوار

        // دور المدير العام
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // دور الموظف
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->givePermissionTo([
            'view_orders', 'create_orders', 'edit_orders', 'manage_order_status',
            'view_items', 'create_items', 'edit_items', 'manage_inventory',
            'view_invoices', 'create_invoices', 'edit_invoices',
            'view_collections', 'create_collections', 'edit_collections',
            'view_returns', 'create_returns', 'edit_returns', 'process_returns',
            'view_suppliers', 'create_suppliers', 'edit_suppliers',
            'view_reports', 'export_reports',
        ]);

        // دور العميل
        $customerRole = Role::firstOrCreate(['name' => 'customer']);
        $customerRole->givePermissionTo([
            'view_orders', 'create_orders',
            'view_items',
            'view_invoices',
            'view_returns', 'create_returns',
        ]);

        // إنشاء مستخدم مدير افتراضي
        $admin = User::firstOrCreate(
            ['email' => 'admin@pharmacy-erp.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password123'),
                'user_type' => 'admin',
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');

        // إنشاء مستخدم موظف افتراضي
        $employee = User::firstOrCreate(
            ['email' => 'employee@pharmacy-erp.com'],
            [
                'name' => 'موظف النظام',
                'password' => Hash::make('password123'),
                'user_type' => 'employee',
                'status' => 'active',
            ]
        );
        $employee->assignRole('employee');

        // إنشاء مستخدم عميل افتراضي
        $customer = User::firstOrCreate(
            ['email' => 'customer@pharmacy-erp.com'],
            [
                'name' => 'عميل تجريبي',
                'password' => Hash::make('password123'),
                'user_type' => 'customer',
                'status' => 'active',
                'company_name' => 'شركة تجريبية',
                'phone' => '0501234567',
                'address' => 'الرياض، المملكة العربية السعودية',
            ]
        );
        $customer->assignRole('customer');

        $this->command->info('تم إنشاء الأدوار والصلاحيات والمستخدمين الافتراضيين بنجاح!');
        $this->command->info('بيانات الدخول:');
        $this->command->info('المدير: admin@pharmacy-erp.com / password123');
        $this->command->info('الموظف: employee@pharmacy-erp.com / password123');
        $this->command->info('العميل: customer@pharmacy-erp.com / password123');
    }
}
