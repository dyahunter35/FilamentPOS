  @php
      use App\Enums\StockCase;
  @endphp

  <x-filament-panels::page>

      <x-filament::section>

          <div class="text-gray-800 dark:text-white">

              <!-- Main Container -->
              <main class="w-full p-4 m-4 mx-auto sm:p-6 md:p-8" id="report-content">

                  <!-- Report Card -->
                  <div class="overflow-hidden shadow-lg rounded-xl dark:text-white">

                      <!-- Header Section -->
                      <header class="p-6 border-b border-gray-200">
                          <div class="flex flex-row items-start justify-between gap-4">

                              <div class="flex ">
                                  <img alt="" src="{{ __('app.image') }}" class="w-16 mx-2" />

                                  <div class="flex flex-col items-start justify-between sm:flex-row sm:items-center">
                                      <div>
                                          <h1 class="text-2xl font-bold text-gray-900">
                                              {{ __('branch_reports.single_branch.model_label') }}</h1>
                                          <p class="mt-1 text-gray-500 text-md">{{ $branch->name }}</p>
                                      </div>
                                  </div>
                              </div>
                              <div class="mt-4 text-lg text-left text-gray-600 sm:mt-0 sm:text-right">
                                  <p class="font-semibold">{{ __('order.invoice.labels.today') }}</p>
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
                                      <th scope="col" class="px-6 py-4 font-semibold text-center">
                                          {{ StockCase::Initial->getLabel() }}
                                      </th>
                                      <th scope="col" class="px-6 py-4 font-semibold text-center">
                                          {{ StockCase::Increase->getLabel() }}

                                      </th>
                                      <th scope="col" class="px-6 py-4 font-semibold text-center">
                                          {{ StockCase::Decrease->getLabel() }}
                                      </th>
                                      <th scope="col" class="px-6 py-4 font-semibold text-center">
                                          {{ __('order.fields.total.label') }}
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
                                          class="transition-colors duration-200 bg-white border-b border-gray-200 hover:bg-gray-50 dark:bg-gray-900">
                                          <td
                                              class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-gray-200">
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
                                          <td class="px-6 py-4 font-semibold text-center text-gray-900">

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
