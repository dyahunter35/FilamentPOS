<x-filament-panels::page>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inventory Report Sample</title>
        <link rel="stylesheet" href="index.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                /* Dark Theme (Default) */
                --bg-primary: #111827;
                --bg-secondary: #1f2937;
                --border-color: #374151;
                --text-primary: #f3f4f6;
                --text-secondary: #9ca3af;
                --accent-color: #38bdf8;
                --accent-gradient-to: #6366f1;
                --hover-bg-color: rgba(55, 65, 81, 0.5);
                --shadow-color: rgba(0, 0, 0, 0.2);
                --font-family: 'Inter', sans-serif;
            }

            body.light-mode {
                /* Light Theme */
                --bg-primary: #f3f4f6;
                --bg-secondary: #ffffff;
                --border-color: #e5e7eb;
                --text-primary: #1f2937;
                --text-secondary: #6b7280;
                --accent-color: #0ea5e9;
                --accent-gradient-to: #4f46e5;
                --hover-bg-color: rgba(229, 231, 235, 0.6);
                --shadow-color: rgba(0, 0, 0, 0.1);
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                background-color: var(--bg-primary);
                color: var(--text-primary);
                font-family: var(--font-family);
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                padding: 1rem;
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .report-container {
                width: 100%;
                max-width: 56rem;
                /* 896px */
                margin: 0 auto;
            }

            .report-card {
                background-color: var(--bg-secondary);
                border-radius: 1rem;
                /* 16px */
                box-shadow: 0 20px 25px -5px var(--shadow-color), 0 8px 10px -6px var(--shadow-color);
                padding: 2rem;
                transition: background-color 0.3s ease;
            }

            .report-header {
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 1px solid var(--border-color);
                transition: border-color 0.3s ease;
            }

            .header-content {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .header-title-group {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .logo {
                height: 2.5rem;
                width: 2.5rem;
                color: var(--accent-color);
            }

            .report-title {
                font-size: 1.875rem;
                /* 30px */
                font-weight: 700;
                background: linear-gradient(to right, var(--accent-color), var(--accent-gradient-to));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .report-subtitle {
                color: var(--text-secondary);
                font-size: 0.875rem;
                /* 14px */
            }

            .header-actions {
                display: flex;
                align-items: center;
                gap: 1.5rem;
                margin-left: auto;
            }

            .header-date {
                text-align: right;
            }

            .date-label {
                font-weight: 600;
                color: var(--text-primary);
            }

            .date-value {
                color: var(--text-secondary);
                font-size: 0.875rem;
                /* 14px */
            }

            /* Theme Switcher */
            .theme-switcher {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .theme-icon {
                width: 1.25rem;
                height: 1.25rem;
                color: var(--text-secondary);
            }

            .toggle-switch {
                position: relative;
                display: inline-block;
                width: 44px;
                height: 24px;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #4b5563;
                /* gray-600 */
                transition: .4s;
                border-radius: 24px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 3px;
                bottom: 3px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked+.slider {
                background-color: var(--accent-color);
            }

            input:focus+.slider {
                box-shadow: 0 0 1px var(--accent-color);
            }

            input:checked+.slider:before {
                transform: translateX(20px);
            }

            .table-wrapper {
                overflow-x: auto;
            }

            .report-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.875rem;
                color: var(--text-secondary);
            }

            .report-table th,
            .report-table td {
                padding: 1rem;
                text-align: left;
            }

            .report-table thead {
                font-size: 0.75rem;
                text-transform: uppercase;
                color: var(--text-primary);
                background-color: var(--hover-bg-color);
            }

            .report-table tbody tr {
                border-bottom: 1px solid var(--border-color);
                transition: background-color 0.2s ease-in-out, border-color 0.3s ease;
            }

            .report-table tbody tr:hover {
                background-color: var(--hover-bg-color);
            }

            .report-table tbody tr:last-child {
                border-bottom: none;
            }

            .product-name {
                font-weight: 600;
                color: var(--text-primary);
                white-space: nowrap;
            }

            .total-cell {
                font-weight: 700;
                color: var(--accent-color);
            }

            .report-table tfoot {
                font-weight: 700;
                color: var(--text-primary);
                background-color: var(--hover-bg-color);
                border-top: 2px solid var(--border-color);
                transition: border-color 0.3s ease;
            }

            .grand-total-cell {
                font-size: 1.125rem;
                color: var(--accent-color);
            }

            .text-center {
                text-align: center;
            }

            @media (min-width: 640px) {
                .header-content {
                    flex-wrap: nowrap;
                    align-items: center;
                }

                .header-actions {
                    margin-top: 0;
                }
            }
        </style>
    </head>

    <body>

        <main id="app-container" class="report-container">
            <div class="report-card">
                <header class="report-header">
                    <div class="header-content">
                        <div class="header-title-group">
                            <!-- Logo SVG -->
                            <svg class="logo" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1.5-1.5m1.5 1.5l1.5-1.5m3.75-3l-1.5-1.5m1.5 1.5l1.5-1.5m-7.5-3v3.75c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125V6.75m-6.75 0h6.75" />
                            </svg>
                            <div>
                                <h1 class="report-title">Product Inventory Report</h1>
                                <p class="report-subtitle">A sample inventory report with a custom CSS design.</p>
                            </div>
                        </div>
                        <div class="header-actions">
                            <div class="header-date">
                                <p class="date-label">Date</p>
                                <p class="date-value">October 26, 2023</p>
                            </div>
                            <div class="theme-switcher">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="theme-icon sun">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                                <label for="theme-toggle" class="toggle-switch" aria-label="Theme toggle">
                                    <input type="checkbox" id="theme-toggle">
                                    <span class="slider"></span>
                                </label>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="theme-icon moon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="table-wrapper">
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th class="text-center">Branch A</th>
                                <th class="text-center">Branch B</th>
                                <th class="text-center">Branch C</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="product-name">Quantum Laptop</td>
                                <td class="text-center">25</td>
                                <td class="text-center">40</td>
                                <td class="text-center">15</td>
                                <td class="text-center total-cell">80</td>
                            </tr>
                            <tr>
                                <td class="product-name">Hyper-V Drone</td>
                                <td class="text-center">10</td>
                                <td class="text-center">55</td>
                                <td class="text-center">30</td>
                                <td class="text-center total-cell">95</td>
                            </tr>
                            <tr>
                                <td class="product-name">Nova Smartwatch</td>
                                <td class="text-center">75</td>
                                <td class="text-center">20</td>
                                <td class="text-center">50</td>
                                <td class="text-center total-cell">145</td>
                            </tr>
                            <tr>
                                <td class="product-name">Echo Earbuds</td>
                                <td class="text-center">90</td>
                                <td class="text-center">60</td>
                                <td class="text-center">85</td>
                                <td class="text-center total-cell">235</td>
                            </tr>
                            <tr>
                                <td class="product-name">Galactic Tablet</td>
                                <td class="text-center">33</td>
                                <td class="text-center">48</td>
                                <td class="text-center">62</td>
                                <td class="text-center total-cell">143</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>Branch Totals</td>
                                <td class="text-center">233</td>
                                <td class="text-center">223</td>
                                <td class="text-center">242</td>
                                <td class="text-center grand-total-cell">698</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </main>

        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                #app-container,
                #app-container * {
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

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const themeToggle = document.getElementById('theme-toggle');
                const body = document.body;

                const setTheme = (isLight) => {
                    if (isLight) {
                        body.classList.add('light-mode');
                        themeToggle.checked = true;
                        localStorage.setItem('theme', 'light');
                    } else {
                        body.classList.remove('light-mode');
                        themeToggle.checked = false;
                        localStorage.setItem('theme', 'dark');
                    }
                };

                themeToggle.addEventListener('change', () => {
                    setTheme(themeToggle.checked);
                });

                const savedTheme = localStorage.getItem('theme');
                const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;

                if (savedTheme) {
                    setTheme(savedTheme === 'light');
                } else {
                    setTheme(prefersLight);
                }
            });
        </script>
</x-filament-panels::page>
