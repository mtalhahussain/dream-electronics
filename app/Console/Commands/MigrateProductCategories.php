<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Console\Command;

class MigrateProductCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:product-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing product categories to use the new category system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting product category migration...');

        // Category mapping from old static categories to new category IDs
        $categoryMapping = [
            'Mobile' => 'mobile-phones',
            'Laptop' => 'laptops', 
            'TV' => 'television',
            'Refrigerator' => 'refrigerator',
            'Washing Machine' => 'washing-machine',
            'Air Conditioner' => 'air-conditioner',
            'Other' => 'home-appliances'
        ];

        $products = Product::whereNotNull('category')->whereNull('category_id')->get();
        $migrated = 0;
        $skipped = 0;

        foreach ($products as $product) {
            $oldCategory = $product->category;
            
            if (isset($categoryMapping[$oldCategory])) {
                $category = Category::where('slug', $categoryMapping[$oldCategory])->first();
                
                if ($category) {
                    $product->update(['category_id' => $category->id]);
                    $migrated++;
                    $this->info("Migrated product '{$product->name}' from '{$oldCategory}' to '{$category->name}'");
                } else {
                    $this->warn("Category not found for mapping: {$categoryMapping[$oldCategory]}");
                    $skipped++;
                }
            } else {
                // Try to find a category by name similarity
                $category = Category::where('name', 'like', "%{$oldCategory}%")->first();
                
                if ($category) {

                    $product->update(['category_id' => $category->id]);
                    $migrated++;
                    $this->info("Migrated product '{$product->name}' from '{$oldCategory}' to '{$category->name}' (fuzzy match)");
                
                } else {
                    // Default to "Home Appliances" category
                    $defaultCategory = Category::where('slug', 'home-appliances')->first();
                    if ($defaultCategory) {

                        $product->update(['category_id' => $defaultCategory->id]);
                        $migrated++;
                        $this->warn("Migrated product '{$product->name}' from '{$oldCategory}' to default category '{$defaultCategory->name}'");
                    
                    } else {
                        
                        $skipped++;
                        $this->error("Could not migrate product '{$product->name}' with category '{$oldCategory}'");
                    }
                }
            }
        }

        $this->info("Migration completed!");
        $this->info("Migrated: {$migrated} products");
        $this->info("Skipped: {$skipped} products");

        if ($migrated > 0) {
            $this->info("You can now safely drop the old 'category' column from the products table if desired.");
        }
    }
}
