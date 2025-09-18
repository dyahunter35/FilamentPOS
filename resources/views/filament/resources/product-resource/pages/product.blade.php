<x-filament-panels::page class="bg-slate-900 text-slate-100 flex items-center justify-center font-sans">
    {{-- زر الطباعة الذي يختفي عند الطباعة --}}
    <div class="text-right print:hidden">
        <x-filament::button icon="heroicon-o-printer" tag="button" onclick="window.print()">
            طباعة التقرير
        </x-filament::button>
    </div>

    <div id="report-content">
        <main id="app-container" class="w-full max-w-4xl mx-auto p-4 md:p-8">
            <div class="bg-slate-800 shadow-2xl rounded-2xl p-6 md:p-8">
                <header class="mb-6">
                    <div
                        class="flex flex-col sm:flex-row justify-between sm:items-center border-b border-slate-700 pb-4">
                        <div class="flex items-center space-x-4">
                            <!-- Logo SVG -->
                            <svg class="h-10 w-10 text-sky-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1.5-1.5m1.5 1.5l1.5-1.5m3.75-3l-1.5-1.5m1.5 1.5l1.5-1.5m-7.5-3v3.75c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125V6.75m-6.75 0h6.75" />
                            </svg>
                            <div>
                                <h1
                                    class="text-2xl md:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-sky-400 to-indigo-500">
                                    Product Inventory Report</h1>
                                <p class="text-slate-400 text-sm">A sample inventory report styled with Tailwind CSS.
                                </p>
                            </div>
                        </div>
                        <div class="text-slate-400 mt-4 sm:mt-0 sm:text-right">
                            <p class="font-semibold text-slate-300">Date</p>
                            <p class="text-sm">October 26, 2023</p>
                        </div>
                    </div>
                </header>

                <div id="report-container" class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right text-slate-400">
                        <thead class="text-xs text-slate-300 uppercase bg-slate-700/50">
                            <tr>
                                <th scope="col" class="p-4">Product Name</th>
                                <th scope="col" class="p-4 text-center">Branch A</th>
                                <th scope="col" class="p-4 text-center">Branch B</th>
                                <th scope="col" class="p-4 text-center">Branch C</th>
                                <th scope="col" class="p-4 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                                <td class="p-4 font-medium text-slate-300 whitespace-nowrap">Quantum Laptop</td>
                                <td class="p-4 text-center text-slate-400">25</td>
                                <td class="p-4 text-center text-slate-400">40</td>
                                <td class="p-4 text-center text-slate-400">15</td>
                                <td class="p-4 text-center font-bold text-sky-400">80</td>
                            </tr>
                            <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                                <td class="p-4 font-medium text-slate-300 whitespace-nowrap">Hyper-V Drone</td>
                                <td class="p-4 text-center text-slate-400">10</td>
                                <td class="p-4 text-center text-slate-400">55</td>
                                <td class="p-4 text-center text-slate-400">30</td>
                                <td class="p-4 text-center font-bold text-sky-400">95</td>
                            </tr>
                            <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                                <td class="p-4 font-medium text-slate-300 whitespace-nowrap">Nova Smartwatch</td>
                                <td class="p-4 text-center text-slate-400">75</td>
                                <td class="p-4 text-center text-slate-400">20</td>
                                <td class="p-4 text-center text-slate-400">50</td>
                                <td class="p-4 text-center font-bold text-sky-400">145</td>
                            </tr>
                            <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                                <td class="p-4 font-medium text-slate-300 whitespace-nowrap">Echo Earbuds</td>
                                <td class="p-4 text-center text-slate-400">90</td>
                                <td class="p-4 text-center text-slate-400">60</td>
                                <td class="p-4 text-center text-slate-400">85</td>
                                <td class="p-4 text-center font-bold text-sky-400">235</td>
                            </tr>
                            <tr class="border-b border-slate-700 hover:bg-slate-700/50">
                                <td class="p-4 font-medium text-slate-300 whitespace-nowrap">Galactic Tablet</td>
                                <td class="p-4 text-center text-slate-400">33</td>
                                <td class="p-4 text-center text-slate-400">48</td>
                                <td class="p-4 text-center text-slate-400">62</td>
                                <td class="p-4 text-center font-bold text-sky-400">143</td>
                            </tr>
                        </tbody>
                        <tfoot class="font-bold text-slate-300 bg-slate-700/50">
                            <tr class="border-t-2 border-slate-600">
                                <td class="p-4">Branch Totals</td>
                                <td class="p-4 text-center">233</td>
                                <td class="p-4 text-center">223</td>
                                <td class="p-4 text-center">242</td>
                                <td class="p-4 text-center text-lg text-sky-400">698</td>
                            </tr>
                        </tfoot>
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
