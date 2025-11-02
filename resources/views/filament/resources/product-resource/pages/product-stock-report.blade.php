<x-filament-panels::page>

    <x-filament::section>

        <div class="text-gray-800 dark:text-white">

            <!-- Main Container -->
            <main class="w-full p-4 m-4 mx-auto sm:p-6 md:p-8" id="report-content">

                <!-- Report Card -->
                <div class="overflow-hidden shadow-lg rounded-xl">

                    <!-- Header Section -->
                    <header class="pb-6 m-4 border-b border-gray-200">
                        <div class="flex flex-row items-start justify-between gap-4">

                            <div class="flex ">
                                <img alt="" src="{{ __('app.image') }}" class="w-16 mx-2" />

                                <div class="flex flex-col items-start justify-between sm:flex-row sm:items-center">
                                    <div>
                                        <h1 class="text-2xl font-bold text-gray-900">{{ __('branch_reports.all_branch.label') }}
                                        </h1>
                                        <p class="mt-1 text-gray-500 text-md"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-lg text-left text-gray-600 sm:mt-0 sm:text-right">
                                <p class="font-semibold">{{  __('order.invoice.labels.today') }}</p>
                                <p>{{ now()->format('Y-m-d') }}</p>
                            </div>
                        </div>
                    </header>

                    <!-- Table Container for Responsiveness -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-center text-gray-600 dark:text-white">
                            <!-- Table Head -->
                            <thead
                                class="text-xs text-gray-700 uppercase border-b border-gray-200 bg-gray-50 dark:bg-gray-700 dark:text-white">
                                <tr>
                                    <th scope="col" class="px-6 py-4 font-semibold">
                                        {{ __('order.fields.items.product.label') }}</th>
                                    @foreach ($branches as $branch)
                                        <th scope="col" class="px-6 py-4 font-semibold text-center">
                                            {{ $branch->name }}
                                        </th>
                                    @endforeach
                                    <th scope="col" class="px-6 py-4 font-semibold text-center">
                                        {{ __('order.fields.items.total.label') }}</th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody class="dark:text-white">
                                @forelse ($products as $product)

                                    <tr class="transition-colors duration-200 border-b border-gray-200 ">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $product->name }}
                                        </td>
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
                                        <td class="px-6 py-4 font-semibold text-center text-gray-900">
                                            {{ number_format($product->totalStock ?? 0) }}</td>
                                        {{-- <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            أداء مرتفع
                                        </span>
                                    </td> --}}
                                    </tr>
                                @endforeach
                            </tbody>

                            <!-- Table Footer -->
                            {{-- <tfoot class="bg-gray-50">
                            <tr class="font-semibold text-gray-900">
                                <td class="px-6 py-4 text-base">الإجمالي</td>
                                <td class="px-6 py-4 text-center">5,920</td>
                                <td class="px-6 py-4 text-center">--</td>
                                <td class="px-6 py-4 text-base text-center">$2,117,780</td>
                                <td class="px-6 py-4 text-center">--</td>
                            </tr>
                        </tfoot> --}}
                        </table>
                    </div>

                </div>

            </main>

        </div>

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
    </x-filament::section>
</x-filament-panels::page>
