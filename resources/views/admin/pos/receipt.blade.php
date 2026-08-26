<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إيصال {{ $receipt_number }}</title>
    <style>
        *{box-sizing:border-box}
        body{
            margin:0;
            background:#f3f3f3;
            font-family:Tahoma,Arial,sans-serif;
            color:#111;
        }
        .receipt{
            width:80mm;
            max-width:100%;
            margin:18px auto;
            background:#fff;
            padding:8mm 5mm;
        }
        .center{text-align:center}
        h1{font-size:18px;margin:0 0 4px}
        .muted{font-size:11px;color:#555}
        .line{border-top:1px dashed #222;margin:9px 0}
        .meta{font-size:12px;line-height:1.8}
        table{width:100%;border-collapse:collapse;font-size:11px}
        th,td{padding:4px 1px;text-align:right;vertical-align:top}
        th{border-bottom:1px solid #222}
        .num{text-align:left;white-space:nowrap}
        .totals{font-size:12px;line-height:1.9}
        .totals div{display:flex;justify-content:space-between;gap:8px}
        .grand{font-size:16px;font-weight:800}
        .payments{font-size:11px;line-height:1.7}
        .actions{
            width:80mm;
            max-width:100%;
            margin:0 auto 20px;
            display:flex;
            gap:8px;
        }
        .actions button{
            flex:1;
            padding:10px;
            border:0;
            border-radius:8px;
            cursor:pointer;
        }
        @media print{
            body{background:#fff}
            .receipt{margin:0;padding:4mm;width:80mm}
            .actions{display:none}
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="center">
            <h1>لمسة أنوثة</h1>
            <div class="muted">إيصال نقطة البيع</div>
            <strong>{{ $receipt_number }}</strong>
        </div>

        <div class="line"></div>

        <div class="meta">
            <div>رقم العملية: {{ $sale_number }}</div>
            <div>التاريخ: {{ optional($completed_at)->format('Y-m-d H:i') }}</div>
            <div>الكاشير: {{ $cashier ?: '-' }}</div>
            <div>الخزينة: {{ $register ?: '-' }}</div>
            <div>العميل: {{ $customer_name }}</div>
            @if($customer_phone)
                <div>الهاتف: {{ $customer_phone }}</div>
            @endif
        </div>

        <div class="line"></div>

        <table>
            <thead>
                <tr>
                    <th>الصنف</th>
                    <th>ك</th>
                    <th class="num">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name_ar ?: 'منتج' }}
                            @if($item->sku)
                                <div class="muted">{{ $item->sku }}</div>
                            @endif
                            @if((float)$item->line_discount > 0)
                                <div class="muted">
                                    خصم صنف:
                                    {{ number_format((float)$item->line_discount,3) }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td class="num">
                            {{ number_format((float)$item->line_total,3) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="line"></div>

        <div class="totals">
            <div>
                <span>المجموع الفرعي</span>
                <strong>{{ number_format($subtotal,3) }}</strong>
            </div>
            <div>
                <span>خصم الفاتورة</span>
                <strong>{{ number_format($discount_amount,3) }}</strong>
            </div>
            <div class="grand">
                <span>الإجمالي</span>
                <span>{{ number_format($total,3) }} ر.ع</span>
            </div>
            @if($cash_received > 0)
                <div>
                    <span>المستلم نقدًا</span>
                    <strong>{{ number_format($cash_received,3) }}</strong>
                </div>
                <div>
                    <span>الباقي</span>
                    <strong>{{ number_format($change_due,3) }}</strong>
                </div>
            @endif
        </div>

        <div class="line"></div>

        <div class="payments">
            <strong>الدفعات</strong>
            @foreach($payments as $payment)
                <div>
                    {{ $payment->payment_method }}
                    :
                    {{ number_format((float)$payment->amount,3) }} ر.ع
                    @if($payment->reference)
                        | {{ $payment->reference }}
                    @endif
                </div>
            @endforeach
        </div>

        <div class="line"></div>

        <div class="center muted">
            شكرًا لتسوقكم معنا
        </div>
    </div>

    <div class="actions">
        <button onclick="window.print()">طباعة</button>
        <button onclick="window.close()">إغلاق</button>
    </div>
</body>
</html>
