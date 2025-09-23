<?php

namespace App\Console\Commands;

use App\Mail\LowStockSummaryMail;
use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:check-levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for products with low stock levels and notify users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking stock levels...');
        Log::info('Running Stock Level Check Command...');

        // --- 1. Find products that are low on stock ---
        $lowStockProducts = Product::query()
            ->withSum('branches as total_stock', 'branch_product.total_quantity')
            ->whereRaw('COALESCE((select sum(total_quantity) from branch_product where product_id = products.id), 0) <= security_stock')
            ->whereNull('low_stock_notified_at')
            ->get();

        $allUsers = User::role(['مدير','super_admin'])->get();

        if ($lowStockProducts->isNotEmpty()) {

            $this->info("Found {$lowStockProducts->count()} products with low stock.");

            foreach ($lowStockProducts as $product) {
                // Send notification to all users
                Notification::make()
                    ->title('تنبيه انخفاض المخزون')
                    ->body("المنتج '{$product->name}' وصل إلى حد المخزون الآمن. الكمية الحالية: {$product->total_stock}")
                    ->danger()
                    ->sendToDatabase($allUsers);

                // Mark the product as notified to prevent spam
                $this->line("Notification sent for product: {$product->name}");
                Log::info("Low stock notification sent for Product ID: {$product->id}");
            }
            // Send notifications and emails
            foreach ($allUsers as $user) {
                // Send a single summary email to the user
                Mail::to($user)->send(new LowStockSummaryMail($lowStockProducts));
                $this->line("Email Send : {$user->email} .");
            }

            $this->line("Notifications and emails sent to {$allUsers->count()} users.");

            // Mark all products as notified in a single query for efficiency
            Product::whereIn('id', $lowStockProducts->pluck('id'))
                ->update(['low_stock_notified_at' => now()]);

            Log::info("Low stock notifications sent for Product IDs: " . $lowStockProducts->pluck('id')->implode(', '));
        } else {
            $this->info('No new low-stock products found.');
        }

        // --- 2. Reset notification for products that are restocked ---
        $restockedProductIds = Product::query()
            ->whereRaw('COALESCE((select sum(total_quantity) from branch_product where product_id = products.id), 0) > security_stock')
            ->whereNotNull('low_stock_notified_at')
            ->pluck('id');

        if ($restockedProductIds->isNotEmpty()) {
            $updatedCount = Product::whereIn('id', $restockedProductIds)->update(['low_stock_notified_at' => null]);
            $this->info("Reset notification flag for {$updatedCount} restocked products.");
            Log::info("Reset notification flag for {$updatedCount} restocked products.");
        }

        $this->info('Stock level check complete.');
        return self::SUCCESS;
    }
}
