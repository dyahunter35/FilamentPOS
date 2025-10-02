<div>
    <x-filament-panels::page>
        <x-filament::section>
            <div>
                <div class="flex flex-col justify-between gap-4 xl:gap-60 lg:gap-48 md:gap-16 sm:gap-8 sm:flex-row">
                    <div class="w-full ">

                        {{-- <img src="{{ asset('asset/images/logo/gas 200.png') }}" class="w-16"> --}}
                        <div class="flex flex-row items-start gap-4">
                            <img alt="" src="{{ asset('asset/images/logo/gas 200.png') }}" class="w-16" />

                            <div>
                                <div class="text-2xl font-bold">
                                    {{ __('app.name') }}
                                </div>
                                <div class="text-lg font-bold">
                                    {{ $this->getRecord()->branch->name }}
                                </div>
                            </div>
                        </div>


                        <div class="mt-6">
                            <div class="mt-4">
                                <div class="text-sm text-gray-400">
                                    {{ trans('filament-invoices::messages.invoices.view.bill_to') }}:
                                </div>
                                <div class="text-lg font-bold">
                                    {{ $this->getRecord()->customer?->name }}
                                </div>
                                {{-- <div class="text-sm">
                                    {{ $this->getRecord()->customer?->email }}
                                </div> --}}
                                <div class="text-sm">
                                    {{ $this->getRecord()->customer?->phone }}
                                </div>
                                {{--  @php
                                    $address = $this->getRecord()->billedFor?->locations()->first();
                                @endphp
                                @if ($address)
                                    <div class="text-sm">
                                        {{$address->street}}
                                    </div>
                                    <div class="text-sm">
                                        {{$address->zip}}, {{$address->city->name}}
                                    </div>
                                    <div class="text-sm">
                                        {{$this->getRecord()->billedFor?->locations()->first()?->country->name}}
                                    </div>
                                @endif --}}

                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col w-full">
                        <div class="flex justify-end font-bold">
                            <div>
                                <div>
                                    <h1 class="text-3xl uppercase">
                                        {{ trans('filament-invoices::messages.invoices.view.invoice') }}</h1>
                                </div>
                                <div>
                                    #{{ $this->getRecord()->number }}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end h-full">
                            <div class="flex flex-col justify-end">
                                <div>
                                    <div class="flex justify-between gap-4">
                                        <div class="text-gray-400">
                                            {{ trans('order.invoice.labels.today') }}
                                            : </div>
                                        <div>{{ now()->toDateString() }}</div>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <div class="text-gray-400">
                                            {{ trans('filament-invoices::messages.invoices.view.issue_date') }} :
                                        </div>
                                        <div>{{ $this->getRecord()->created_at->toDateString() }}</div>
                                    </div>
                                    {{-- <div class="flex justify-between gap-4">
                                        <div class="text-gray-400">
                                            {{ trans('filament-invoices::messages.invoices.view.due_date') }} : </div>
                                        <div>{{ $this->getRecord()->due_date?->toDateString() }}</div>
                                    </div> --}}
                                    <div class="flex justify-between gap-4">
                                        <div class="text-gray-400">
                                            {{ trans('filament-invoices::messages.invoices.view.status') }} : </div>
                                        <div>{{ $this->getRecord()->status->getLabel() }}</div>
                                    </div>
                                    {{-- <div class="flex justify-between gap-4">
                                        <div class="text-gray-400">
                                            {{ trans('filament-invoices::messages.invoices.view.type') }} : </div>
                                        <div>{{-- type_of($this->getRecord()->type, 'invoices', 'type')->name -}}</div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="px-2 my-4 border border-gray-200 rounded-lg dark:border-gray-700">
                        <div class="flex flex-col">
                            <div
                                class="flex justify-between px-4 py-2 font-bold text-center border-b border-gray-200 dark:border-gray-700">
                                <div class="w-full p-2">
                                    {{ trans('filament-invoices::messages.invoices.view.item') }}
                                </div>
                                <div class="w-full p-2">
                                    {{ trans('filament-invoices::messages.invoices.view.qty') }}
                                </div>
                                <div class="w-full p-2">
                                    {{ trans('filament-invoices::messages.invoices.view.price') }}
                                </div>
                                <div class="w-full p-2">
                                    {{ trans('filament-invoices::messages.invoices.view.discount') }}
                                </div>
                                <div class="w-full p-2">
                                    {{ trans('filament-invoices::messages.invoices.view.total') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4 divide-y divide-gray-100 dark:divide-white/5">
                            @foreach ($this->getRecord()->items as $key => $item)
                                <div class="flex justify-between px-4 py-2">
                                    <div class="flex flex-col w-full">
                                        <div class="flex items-center justify-center">
                                            <div>
                                                <div class="text-lg font-bold">
                                                    {{ $item->product?->name }}
                                                </div>
                                                @if ($item->description)
                                                    <div class="text-gray-400">
                                                        {{ $item->description }}
                                                    </div>
                                                @endif
                                                @if ($item->options)
                                                    <div class="text-gray-400">
                                                        @foreach ($item->options ?? [] as $label => $options)
                                                            <span>{{ str($label)->ucfirst() }}</span> :
                                                            {{ $options }} <br>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-center">
                                            <div>
                                                <div class="font-medium">
                                                    (1)
                                                </div>
                                                <div class="font-bold">
                                                    {{ number_format($item->qty, 1) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-center">
                                            <div>
                                                <div class="font-medium">
                                                    {{ number_format($item->price, 1) }}
                                                </div>
                                                <div class="font-bold">
                                                    {{ number_format($item->price * $item->qty, 1) }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-center">
                                            <div>
                                                <div class="font-medium">
                                                    {{ number_format($item->sub_discount, 1) }}
                                                </div>
                                                <div class="font-bold">
                                                    {{ number_format($item->sub_discount * $item->qty, 1) }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-center">
                                            <div>
                                                <div class="font-medium">
                                                    {{ number_format($item->price - $item->sub_discount, 1) }}
                                                </div>
                                                <div class="font-bold">
                                                    {{ number_format(($item->price - $item->sub_discount) * $item->qty, 1) }}
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>


                    <div class="flex justify-between mt-6">
                        <div class="flex flex-col justify-end w-full gap-4">
                            @if ($this->getRecord()->is_bank_transfer)
                                <div>
                                    <div class="mb-2 text-xl">
                                        {{ trans('filament-invoices::messages.invoices.view.bank_account') }}
                                    </div>
                                    <div class="flex flex-col text-sm">
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.name') }}</span>
                                            : <span class="font-bold"></span>
                                        </div>
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.address') }}</span>
                                            : <span class="font-bold">,,</span>
                                        </div>
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.branch') }}</span>
                                            : <span class="font-bold"></span>
                                        </div>
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.swift') }}</span>
                                            : <span class="font-bold"></span>
                                        </div>
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.account') }}</span>
                                            : <span class="font-bold"></span>
                                        </div>
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.owner') }}</span>
                                            : <span class="font-bold"></span>
                                        </div>
                                        <div>
                                            <span
                                                clas="text-gray-400">{{ trans('filament-invoices::messages.invoices.view.iban') }}</span>
                                            : <span class="font-bold"></span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div>
                                <div class="mb-2 text-xl">
                                    {{ trans('filament-invoices::messages.invoices.view.signature') }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{-- <div>
                                        {{ $this->getRecord()->branch?->name }}
                                    </div>
                                    <div>
                                        {{ $this->getRecord()->billedFrom?->email }}
                                    </div>
                                    <div>
                                        {{ $this->getRecord()->billedFrom?->phone }}
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col w-full gap-2 mt-4">
                            <div class="flex justify-between">
                                <div class="font-bold">
                                    {{ trans('order.invoice.labels.subtotal') }}
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->total + $this->getRecord()->discount - $this->getRecord()->shipping - $this->getRecord()->install, 1) }}
                                    <small class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                </div>
                            </div>
                            @if ($this->getRecord()->install > 0)
                                <div class="flex justify-between">
                                    <div class="font-bold">
                                        {{ trans('order.fields.shipping.label') }}
                                    </div>
                                    <div>
                                        {{ number_format($this->getRecord()->shipping, 1) }} <small
                                            class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($this->getRecord()->install > 0)
                                <div class="flex justify-between">
                                    <div class="font-bold">
                                        {{ trans('order.fields.installation.label') }}
                                    </div>
                                    <div>
                                        {{ number_format($this->getRecord()->install, 1) }} <small
                                            class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                    </div>
                                </div>
                            @endif

                            @if ($this->getRecord()->discount > 0)
                                <div class="flex justify-between">
                                    <div class="font-bold">
                                        {{ trans('filament-invoices::messages.invoices.view.discount') }}
                                    </div>
                                    <div>
                                        {{ number_format($this->getRecord()->discount, 1) }} <small
                                            class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                    </div>
                                </div>
                            @endif
                            <div class="flex justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="font-bold">
                                    {{ trans('filament-invoices::messages.invoices.view.total') }}
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->total, 1) }} <small
                                        class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                </div>
                            </div>
                            @if ($this->getRecord()->paid > 0)
                                <div class="flex justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                                    <div class="font-bold">
                                        {{ trans('filament-invoices::messages.invoices.view.paid') }}
                                    </div>
                                    <div>
                                        {{ number_format($this->getRecord()->paid, 1) }} <small
                                            class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                    </div>
                                </div>
                            @endif


                            @if ($this->getRecord()->total - $this->getRecord()->paid > 0)
                                <div class="flex justify-between text-xl font-bold">
                                    <div>
                                        {{ trans('filament-invoices::messages.invoices.view.balance_due') }}
                                    </div>
                                    <div>
                                        {{ number_format($this->getRecord()->total - $this->getRecord()->paid, 1) }}
                                        <small class="font-normal text-md">{{ $this->getRecord()->currency }}</small>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    @if ($this->getRecord()->notes)
                        <div class="my-4 border-b border-gray-200 dark:border-gray-700"></div>
                        <div>
                            <div class="mb-2 text-xl">
                                {{ trans('filament-invoices::messages.invoices.view.notes') }}
                            </div>
                            <div class="text-sm text-gray-400">
                                {!! $this->getRecord()->notes !!}
                            </div>
                            </div`>
                    @endif
                </div>
            </div>
        </x-filament::section>
        @if ($this->getRecord()->orderMetas()->count() > 0)
            <x-filament::section>


                <!-- Header Section -->
                <header class="border-b border-gray-200 ">
                    <div class="flex flex-col items-start justify-between sm:flex-row sm:items-center">
                        <div>
                            <h1 class="p-3 font-bold text-gray-900 text-l">المدفوعات</h1>
                        </div>

                    </div>
                </header>

                <!-- Table Container for Responsiveness -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-right text-gray-600">
                        <!-- Table Head -->
                        <thead class="text-gray-700 uppercase bg-white border-b border-gray-200 text-l">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">التاريخ</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">
                                    المبلغ
                                </th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">
                                    طريقة الدفع
                                </th>
                            </tr>
                        </thead>

                        <!-- Table Body -->
                        <tbody>
                            @forelse ($this->getRecord()->orderMetas()->latest()->get() as $meta)

                                <tr
                                    class="transition-colors duration-200 bg-white border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-center text-gray-900 whitespace-nowrap">
                                        {{ $meta->created_at->toDateString() }}
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        {{ number_format($meta->value, 1) }} <small
                                            class="font-normal text-md">SDG</small>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        {{ \App\Enums\Payment::tryFrom($meta->group)->getLabel() }}
                                    </td>

                                    {{-- <td class="px-6 py-4 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            أداء مرتفع
                                        </span>
                                    </td> --
                                    </tr> --}}
                            @endforeach
                        </tbody>


                    </table>
                </div>
            </x-filament::section>
        @endif

        {{-- <div class="no-print">
            @php
                $relationManagers = $this->getRelationManagers();
                $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
            @endphp
            @if (count($relationManagers))
                <x-filament-panels::resources.relation-managers :active-locale="isset($activeLocale) ? $activeLocale : null" :active-manager="$this->activeRelationManager ??
                    ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))" :content-tab-label="$this->getContentTabLabel()"
                    :content-tab-icon="$this->getContentTabIcon()" :content-tab-position="$this->getContentTabPosition()" :managers="$relationManagers" :owner-record="$record" :page-class="static::class">
                    @if ($hasCombinedRelationManagerTabsWithContent)
                        <x-slot name="content">
                            @if ($this->hasInfolist())
                                {{ $this->infolist }}
                            @else
                                {{ $this->form }}
                            @endif
                        </x-slot>
                    @endif
                </x-filament-panels::resources.relation-managers>
            @endif
        </div> --}}
    </x-filament-panels::page>


    <style type="text/css" media="print">
        @page {
            margin: 0;
            size: auto;
        }

        body {
            margin: 1cm;
        }

        .fi-section-content-ctn {
            padding: 0 !important;
            border: none !important;
        }

        .fi-section {
            border: none !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .fi-section-content {
            border: none !important;
            box-shadow: none !important;
        }

        .fi-main {
            margin: 0 !important;
            padding: 0 !important;
            background-color: white !important;
            color: black !important;
        }

        img {
            display: block !important;
            page-break-inside: avoid;
        }

        .no-print {
            display: none !important;
        }

        .fi-header {
            display: none !important;
        }

        .fi-topbar {
            display: none !important;
        }

        .fi-sidebar {
            display: none !important;
        }

        .fi-sidebar-close-overlay {
            display: none !important;
        }
    </style>

</div>
