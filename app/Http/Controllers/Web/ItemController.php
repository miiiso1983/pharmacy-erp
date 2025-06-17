<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Supplier;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with('supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('items.index', compact('items'));
    }

    public function show($id)
    {
        $item = Item::with('supplier')->findOrFail($id);
        return view('items.show', compact('item'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', 'active')->get();
        return view('items.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:items',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'barcode' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Item::create($request->all());

        return redirect()->route('items.index')
            ->with('success', 'تم إنشاء العنصر بنجاح');
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $suppliers = Supplier::where('status', 'active')->get();
        return view('items.edit', compact('item', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:items,code,' . $id,
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'barcode' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today',
            'batch_number' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $item->update($request->all());

        return redirect()->route('items.show', $item->id)
            ->with('success', 'تم تحديث العنصر بنجاح');
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);

        // التحقق من عدم وجود طلبات مرتبطة
        if ($item->orderItems()->count() > 0) {
            return back()->withErrors(['error' => 'لا يمكن حذف العنصر لوجود طلبات مرتبطة به']);
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'تم حذف العنصر بنجاح');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $items = Item::where('name', 'like', "%{$query}%")
            ->orWhere('code', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->with('supplier')
            ->limit(10)
            ->get();

        return response()->json($items);
    }

    public function lowStock()
    {
        $items = Item::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->with('supplier')
            ->orderBy('stock_quantity', 'asc')
            ->paginate(15);

        return view('items.low-stock', compact('items'));
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('items.import');
    }

    /**
     * Import items from Excel
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
            $import = new ItemsImport;
            Excel::import($import, $request->file('file'));

            $message = "تم استيراد العناصر بنجاح";

            if (count($import->errors()) > 0 || count($import->failures()) > 0) {
                $message .= ". تم تجاهل بعض الصفوف بسبب أخطاء";
            }

            return redirect()->route('items.index')
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
            'Content-Disposition' => 'attachment; filename="items_sample.csv"',
        ];

        // إنشاء ملف نموذجي
        $sampleData = [
            ['الكود', 'اسم_العنصر', 'الوصف', 'الفئة', 'الباركود', 'الوحدة', 'السعر', 'التكلفة', 'الكمية', 'الحد_الادنى', 'الحد_الاقصى', 'اسم_المورد', 'تاريخ_الانتهاء', 'رقم_الدفعة', 'الموقع', 'الحالة', 'ملاحظات'],
            ['MED001', 'باراسيتامول 500 مجم', 'مسكن للألم وخافض للحرارة', 'مسكنات', '1234567890123', 'قرص', '500', '300', '100', '10', '500', 'شركة الأدوية المتحدة', '2025-12-31', 'BATCH001', 'رف A1', 'نشط', 'دواء آمن'],
            ['MED002', 'أموكسيسيلين 250 مجم', 'مضاد حيوي واسع المجال', 'مضادات حيوية', '1234567890124', 'كبسولة', '1200', '800', '50', '5', '200', 'مختبرات الشفاء', '2025-06-30', 'BATCH002', 'رف B2', 'نشط', 'يحفظ في مكان بارد'],
        ];

        return response()->streamDownload(function() use ($sampleData) {
            $file = fopen('php://output', 'w');
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'items_sample.csv', $headers);
    }
}
