<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $query = Order::with(['customer', 'orderItems.item'])
            ->orderBy('created_at', 'desc');

        // إذا كان المستخدم عميل، عرض طلباته فقط
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $orders = $query->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $user = Auth::user();

        $query = Order::with(['customer', 'orderItems.item', 'invoice']);

        // إذا كان المستخدم عميل، التأكد أن الطلب له
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $order = $query->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $items = Item::where('status', 'active')
            ->where('quantity_in_stock', '>', 0)
            ->get();

        $customers = User::where('user_type', 'customer')
            ->where('status', 'active')
            ->get();

        return view('orders.create', compact('items', 'customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'nullable|string',
            'delivery_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $customerId = $user->user_type === 'customer' ? $user->id : $request->customer_id;

            // إنشاء الطلب
            $order = Order::create([
                'customer_id' => $customerId,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_date' => $request->delivery_date,
                'notes' => $request->notes,
                'created_by' => $user->id,
            ]);

            $subtotal = 0;

            // إضافة عناصر الطلب
            foreach ($request->items as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                // التحقق من توفر الكمية
                if ($item->quantity_in_stock < $itemData['quantity']) {
                    throw new \Exception("الكمية المطلوبة غير متوفرة للعنصر: {$item->name}");
                }

                $unitPrice = $item->selling_price;
                $totalPrice = $unitPrice * $itemData['quantity'];
                $subtotal += $totalPrice;

                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                // تقليل المخزون
                $item->decrement('quantity_in_stock', $itemData['quantity']);
            }

            // تحديث إجمالي الطلب
            $taxAmount = $subtotal * 0.15; // ضريبة 15%
            $totalAmount = $subtotal + $taxAmount;

            $order->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);

            // إنشاء فاتورة
            Invoice::create([
                'order_id' => $order->id,
                'customer_id' => $customerId,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'due_date' => now()->addDays(30),
            ]);

            DB::commit();

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'تم إنشاء الطلب بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح');
    }

    public function repeat($id)
    {
        $user = Auth::user();

        $originalOrder = Order::with('orderItems.item')
            ->where('customer_id', $user->id)
            ->findOrFail($id);

        $items = $originalOrder->orderItems->map(function ($orderItem) {
            return [
                'item_id' => $orderItem->item_id,
                'quantity' => $orderItem->quantity,
                'item' => $orderItem->item
            ];
        });

        return view('orders.repeat', compact('originalOrder', 'items'));
    }
}
