<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Collection::with(['customer', 'invoice', 'collectedBy'])
            ->orderBy('created_at', 'desc');

        // إذا كان المستخدم عميل، عرض تحصيلاته فقط
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $collections = $query->paginate(15);

        return view('collections.index', compact('collections'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $query = Collection::with(['customer', 'invoice.order', 'collectedBy']);

        // إذا كان المستخدم عميل، التأكد أن التحصيل له
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $collection = $query->findOrFail($id);

        return view('collections.show', compact('collection'));
    }

    public function create()
    {
        $invoices = Invoice::with(['customer', 'order'])
            ->where('remaining_amount', '>', 0)
            ->get();

        $customers = User::where('user_type', 'customer')
            ->where('status', 'active')
            ->get();

        return view('collections.create', compact('invoices', 'customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'collection_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $invoice = Invoice::findOrFail($request->invoice_id);

        // التحقق من أن المبلغ لا يتجاوز المبلغ المتبقي
        if ($request->amount > $invoice->remaining_amount) {
            return back()->withErrors(['amount' => 'المبلغ يتجاوز المبلغ المتبقي في الفاتورة'])->withInput();
        }

        Collection::create([
            'invoice_id' => $request->invoice_id,
            'customer_id' => $invoice->customer_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'collection_date' => $request->collection_date,
            'reference_number' => $request->reference_number,
            'collected_by' => Auth::id(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('collections.index')
            ->with('success', 'تم إنشاء التحصيل بنجاح');
    }

    public function edit($id)
    {
        $collection = Collection::with(['invoice', 'customer'])->findOrFail($id);

        $invoices = Invoice::with(['customer', 'order'])
            ->where('remaining_amount', '>', 0)
            ->orWhere('id', $collection->invoice_id)
            ->get();

        return view('collections.edit', compact('collection', 'invoices'));
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card',
            'collection_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // التحقق من أن المبلغ الجديد لا يتجاوز المبلغ المتبقي + المبلغ الحالي
        $invoice = $collection->invoice;
        $maxAmount = $invoice->remaining_amount + $collection->amount;

        if ($request->amount > $maxAmount) {
            return back()->withErrors(['amount' => 'المبلغ يتجاوز المبلغ المتاح'])->withInput();
        }

        $collection->update([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'collection_date' => $request->collection_date,
            'reference_number' => $request->reference_number,
            'notes' => $request->notes,
        ]);

        return redirect()->route('collections.show', $collection->id)
            ->with('success', 'تم تحديث التحصيل بنجاح');
    }

    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);
        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'تم حذف التحصيل بنجاح');
    }

    public function getInvoiceDetails($invoiceId)
    {
        $invoice = Invoice::with(['customer', 'order'])->findOrFail($invoiceId);

        return response()->json([
            'customer_name' => $invoice->customer->name,
            'order_number' => $invoice->order->order_number,
            'total_amount' => $invoice->total_amount,
            'paid_amount' => $invoice->paid_amount,
            'remaining_amount' => $invoice->remaining_amount,
        ]);
    }
}
