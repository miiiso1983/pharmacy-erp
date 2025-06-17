<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Order::with(['orderItems.item', 'customer'])
            ->orderBy('created_at', 'desc');

        // إذا كان المستخدم عميل، عرض طلباته فقط
        if ($user->user_type === 'customer') {
            $query->where('customer_id', $user->id);
        }

        $orders = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // التحقق من أن المستخدم عميل
        if ($user->user_type !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Only customers can create orders'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'nullable|string',
            'delivery_date' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // إنشاء رقم الطلب
            $orderNumber = $this->generateOrderNumber();

            // إنشاء الطلب
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $user->id,
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
                    throw new \Exception("Insufficient stock for item: {$item->name}");
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
                'customer_id' => $user->id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'remaining_amount' => $totalAmount,
                'status' => 'pending',
                'due_date' => now()->addDays(30),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order->load(['orderItems.item', 'invoice'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber()
    {
        $lastOrder = Order::latest('id')->first();
        $number = $lastOrder ? $lastOrder->id + 1 : 1;
        return 'ORD-' . date('Y') . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
