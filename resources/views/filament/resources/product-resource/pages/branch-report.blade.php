  @php
      use App\Enums\StockCase;
  @endphp

  <x-filament-panels::page>

      <div class="text-gray-800">

          <!-- Main Container -->
          <main class="w-full  mx-auto p-4 sm:p-6 md:p-8 m-4" id="report-content">

              <!-- Report Card -->
              <div class="bg-white shadow-lg rounded-xl overflow-hidden">

                  <!-- Header Section -->
                  <header class="border-b border-gray-200 p-6">
                      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                          <div>
                              <h1 class="text-2xl font-bold text-gray-900">تقرير المنتجات للفروع </h1>
                              <p class="text-md text-gray-500 mt-1">{{ $branch->name }}</p>
                          </div>
                          <div class="text-sm text-gray-600 mt-4 sm:mt-0 text-left sm:text-right">
                              <p class="font-semibold">تاريخ التقرير:</p>
                              <p>{{ now()->format('Y-m-d') }}</p>
                          </div>
                      </div>
                  </header>

                  <!-- Table Container for Responsiveness -->
                  <div class="overflow-x-auto">
                      <table class="w-full text-sm text-right text-gray-600">
                          <!-- Table Head -->
                          <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                              <tr>
                                  <th scope="col" class="px-6 py-4 font-semibold">المنتج</th>
                                  <th scope="col" class="px-6 py-4 font-semibold text-center">
                                      اول كمية
                                  </th>
                                  <th scope="col" class="px-6 py-4 font-semibold text-center">
                                      التوريدات
                                  </th>
                                  <th scope="col" class="px-6 py-4 font-semibold text-center">
                                      المبيعات
                                  </th>
                                  <th scope="col" class="px-6 py-4 font-semibold text-center">
                                      المجموع الكلي
                                  </th>
                              </tr>
                          </thead>

                          <!-- Table Body -->
                          <tbody>
                              @forelse ($products as $product)
                                  @php
                                      $p = $product->history->where('branch_id', $branch->id);
                                  @endphp
                                  <tr
                                      class="bg-white border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                                      <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                          {{ $product->name }}
                                      </td>

                                      <td class="px-6 py-4 text-center">
                                          {{ $p->where('type', StockCase::Initial)->sum('quantity_change') }}
                                      </td>
                                      <td class="px-6 py-4 text-center">
                                          {{ $p->where('type', StockCase::Increase)->sum('quantity_change') }}
                                      </td>
                                      <td class="px-6 py-4 text-center">
                                          {{ $p->where('type', StockCase::Decrease)->sum('quantity_change') }}
                                      </td>
                                      <td class="px-6 py-4 text-center font-semibold text-gray-900">
                                          {{ number_format($product->stock_for_current_branch ?? 0) }}</td>
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
                                <td class="px-6 py-4 text-center text-base">$2,117,780</td>
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

  </x-filament-panels::page>
