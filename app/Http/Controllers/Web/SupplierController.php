<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Imports\SuppliersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    public function show($id)
    {
        $supplier = Supplier::with('items')->findOrFail($id);
        return view('suppliers.show', compact('supplier'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:suppliers',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Supplier::create($request->all());

        return redirect()->route('suppliers.index')
            ->with('success', 'تم إنشاء المورد بنجاح');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:suppliers,email,' . $id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $supplier->update($request->all());

        return redirect()->route('suppliers.show', $supplier->id)
            ->with('success', 'تم تحديث المورد بنجاح');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        // التحقق من عدم وجود عناصر مرتبطة
        if ($supplier->items()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف المورد لوجود عناصر مرتبطة به']);
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'تم حذف المورد بنجاح');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $suppliers = Supplier::where('name', 'like', "%{$query}%")
            ->orWhere('contact_person', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($suppliers);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('suppliers.import');
    }

    /**
     * Import suppliers from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file.required' => 'يرجى اختيار ملف',
            'file.mimes' => 'يجب أن يكون الملف من نوع Excel أو CSV',
            'file.max' => 'حجم الملف يجب أن يكون أقل من 2 ميجابايت'
        ]);

        try {
            $import = new SuppliersImport;
            Excel::import($import, $request->file('file'));

            $successCount = Supplier::count();
            $errorCount = count($import->errors());
            $failureCount = count($import->failures());

            $message = "تم استيراد الموردين بنجاح";

            if ($errorCount > 0 || $failureCount > 0) {
                $message .= ". تم تجاهل بعض الصفوف بسبب أخطاء";
            }

            return redirect()->route('suppliers.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء استيراد الملف: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel file
     */
    public function downloadSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="suppliers_sample.csv"',
        ];

        // إنشاء ملف نموذجي
        $sampleData = [
            ['اسم_المورد', 'الشخص_المسؤول', 'البريد_الالكتروني', 'الهاتف', 'العنوان', 'المدينة', 'البلد', 'الرقم_الضريبي', 'الحالة', 'ملاحظات'],
            ['شركة الأدوية المتحدة', 'أحمد محمد', 'ahmed@pharmacy.com', '07901234567', 'شارع الكندي', 'بغداد', 'العراق', '123456789', 'نشط', 'مورد موثوق'],
            ['مختبرات الشفاء', 'فاطمة علي', 'fatima@shifa.com', '07801234567', 'شارع الجامعة', 'البصرة', 'العراق', '987654321', 'نشط', 'متخصص في المضادات الحيوية'],
        ];

        return response()->streamDownload(function() use ($sampleData) {
            $file = fopen('php://output', 'w');
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'suppliers_sample.csv', $headers);
    }
}
