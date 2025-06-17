<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Invoice::with(['customer', 'order'])
            ->orderBy('created_at', 'desc');

        // إذا كان المستخدم عميل، عرض فواتيره فقط
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $invoices = $query->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $query = Invoice::with(['customer', 'order.orderItems.item', 'collections']);

        // إذا كان المستخدم عميل، التأكد أن الفاتورة له
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $invoice = $query->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['customer', 'order'])->findOrFail($id);
        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'discount_amount' => 'nullable|numeric|min:0|max:' . $invoice->subtotal,
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // إعادة حساب المبلغ الإجمالي
        $subtotal = $invoice->subtotal;
        $taxAmount = $invoice->tax_amount;
        $discountAmount = $request->discount_amount ?? 0;
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        $invoice->update([
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'remaining_amount' => $totalAmount - $invoice->paid_amount,
            'due_date' => $request->due_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'تم تحديث الفاتورة بنجاح');
    }

    public function markAsPaid($id)
    {
        $invoice = Invoice::findOrFail($id);

        // إنشاء تحصيل للمبلغ المتبقي
        Collection::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => $invoice->remaining_amount,
            'payment_method' => 'cash',
            'collection_date' => now(),
            'collected_by' => Auth::id(),
            'notes' => 'تم وضع علامة كمدفوع من النظام',
        ]);

        return back()->with('success', 'تم وضع علامة على الفاتورة كمدفوعة');
    }

    public function pending()
    {
        $user = Auth::user();

        $query = Invoice::with(['customer', 'order'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc');

        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $invoices = $query->paginate(15);

        return view('invoices.pending', compact('invoices'));
    }

    public function overdue()
    {
        $user = Auth::user();

        $query = Invoice::with(['customer', 'order'])
            ->where('status', 'overdue')
            ->orWhere(function($q) {
                $q->where('status', 'pending')
                  ->where('due_date', '<', now());
            })
            ->orderBy('due_date', 'asc');

        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $invoices = $query->paginate(15);

        return view('invoices.overdue', compact('invoices'));
    }

    public function print($id)
    {
        $user = Auth::user();

        $query = Invoice::with(['customer', 'order.orderItems.item']);

        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $invoice = $query->findOrFail($id);

        return view('invoices.print', compact('invoice'));
    }
}
