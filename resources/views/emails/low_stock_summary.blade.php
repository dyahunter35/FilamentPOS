<x-mail::message>
# تنبيه انخفاض المخزون

مرحباً،

لقد وصلت المنتجات التالية إلى حد المخزون الآمن أو أقل. يرجى اتخاذ الإجراء اللازم.

<x-mail::table>
| المنتج | الكمية الحالية | حد المخزون الآمن |
| :------------- |:-------------:|:-------------:|
@foreach ($lowStockProducts as $product)
| {{ $product->name }} | {{ $product->total_stock }} | {{ $product->security_stock }} |
@endforeach
</x-mail::table>

<x-mail::button :url="url('/')">
    عرض لوحة التحكم
</x-mail::button>

شكراً لك،<br>
{{ config('app.name') }}
</x-mail::message>

