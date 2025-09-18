<x-filament-panels::page>
    {{-- زر الطباعة الذي يختفي عند الطباعة --}}
    <div class="text-right print:hidden">
        <x-filament::button icon="heroicon-o-printer" tag="button" onclick="window.print()">
            طباعة التقرير
        </x-filament::button>
    </div>

    {{-- حاوية التقرير الرئيسية --}}
    <div id="report-content" class="p-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        {{-- 1. رأس التقرير --}}
        <header class="flex items-center justify-between pb-6 border-b-2 border-gray-200 dark:border-gray-700">
            {{-- الشعار والعنوان --}}
            <div>
                <img src="{{ asset('images/your-logo.png') }}" alt="Company Logo" class="h-12 mb-2">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    تقرير مخزون المنتجات
                </h1>
            </div>
            {{-- التاريخ --}}
            <div class="text-right text-gray-600 dark:text-gray-300">
                <p class="font-semibold">تاريخ التقرير:</p>
                <p>{{ now()->format('Y-m-d') }}</p>
            </div>
        </header>

        {{-- 2. جدول البيانات --}}
        <tr class="border-b border-slate-700 hover:bg-slate-700/50">
            <td class="p-4 font-medium text-slate-300 whitespace-nowrap">المنتج</td>
            @foreach ($branches as $branch)
                <td class="p-4 text-center text-slate-400">{{ $branch->name }}</td>
            @endforeach

            <td class="p-4 text-center font-bold text-sky-400">total</td>
        </tr>
        <main class="mt-8">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">المنتج</th>
                        {{-- إنشاء عمود لكل فرع بشكل ديناميكي --}}
                        @foreach ($branches as $branch)
                            <th scope="col" class="px-6 py-3 text-center">{{ $branch->name }}</th>
                        @endforeach
                        <th scope="col" class="px-6 py-3 text-center bg-gray-100 dark:bg-gray-600">الكمية الإجمالية
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{-- اسم المنتج --}}
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $product->name }}
                            </td>

                            {{-- عرض كمية المنتج في كل فرع --}}
                            @foreach ($branches as $branch)
                                @php
                                    // البحث عن بيانات الفرع المحدد داخل علاقة المنتج بالفروع
                                    $branchPivot = $product->branches->firstWhere('id', $branch->id);
                                    // جلب الكمية من حقل الـ pivot أو عرض 0 إذا لم يكن المنتج مرتبطاً بالفرع
                                    $quantityInBranch = $branchPivot?->pivot->total_quantity ?? 0;
                                @endphp
                                <td class="px-6 py-4 text-center">
                                    {{ $quantityInBranch }}
                                </td>
                            @endforeach

                            {{-- عرض الكمية الإجمالية (المحسوبة مسبقاً بـ withSum) --}}
                            <td class="px-6 py-4 text-center font-bold bg-gray-100 dark:bg-gray-600">
                                {{ $product->totalStock ?? 0 }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 2 + $branches->count() }}" class="p-4 text-center">
                                لا توجد منتجات لعرضها.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </main>
    </div>

    {{-- تنسيقات خاصة بالطباعة --}}
    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            #report-content,
            #report-content * {
                visibility: visible;
            }

            #report-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }
        }
    </style>

</x-filament-panels::page>
