<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Metal;
use App\Models\MetalPurity;
use App\Models\ProductImage;

class LegacyDataMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder looks for legacy tables (e.g., '22kgold_product_necklaces')
     * and migrates their data into the new 'products' table.
     */
    public function run(): void
    {
        // Define all legacy tables and their mapping to Metal/Purity
        $legacyMap = [
            '22kgold' => ['metal' => 'Gold', 'purity' => '22K Gold'],
            '18kgold' => ['metal' => 'Gold', 'purity' => '18K Gold'],
            'silver' => ['metal' => 'Silver', 'purity' => '925 Silver'],
            'rosegold' => ['metal' => 'Rose Gold', 'purity' => '18K Rose Gold'],
            '18kdgold' => ['metal' => 'Gold', 'purity' => '18K Gold', 'diamond' => true],
            '18kd' => ['metal' => 'Gold', 'purity' => '18K Gold', 'diamond' => true],
        ];

        // Categories Map (Legacy Suffix -> New Slug)
        $catMap = [
            'necklaces' => 'necklaces',
            'pendants' => 'pendants',
            'bracelets' => 'bracelets',
            'bangles' => 'bangles',
            'anklets' => 'anklets',
            'earrings' => 'earrings',
            'studs' => 'studs',
            'rings' => 'rings',
            'nose_pins' => 'nose-pins',
            'chains' => 'chains',
            'fancy_chain' => 'fancy-chains',
            'second_studs' => 'second-studs',
        ];

        foreach ($legacyMap as $prefix => $meta) {
            foreach ($catMap as $suffix => $slug) {
                $tableName = "{$prefix}_product_{$suffix}";
                
                if (Schema::hasTable($tableName)) {
                    $this->command->info("Migrating from {$tableName}...");
                    $this->migrateTable($tableName, $meta, $slug);
                }
            }
        }
        
        $this->migrateUsers();
    }

    private function migrateUsers()
    {
        $legacyUsersTable = 'legacy_users';
        if (Schema::hasTable($legacyUsersTable)) {
            $this->command->info("Migrating Users from {$legacyUsersTable}...");
            $legacyUsers = DB::table($legacyUsersTable)->get();
            
            foreach ($legacyUsers as $lUser) {
                if (User::where('phone', $lUser->phone)->exists()) continue;
                
                User::create([
                    'fullname' => $lUser->fullname ?? $lUser->name ?? 'User',
                    'email' => $lUser->email,
                    'phone' => $lUser->phone,
                    'password' => $lUser->password, // Plain text
                    'address1' => $lUser->address1 ?? '',
                    'address2' => $lUser->address2 ?? '',
                    'city' => $lUser->city ?? '',
                    'state' => $lUser->state ?? '',
                    'pincode' => $lUser->pincode ?? '',
                    'dob' => $lUser->dob ?? null,
                    'anniversary' => $lUser->anniversary ?? null,
                    'security_question' => $lUser->security_question ?? '',
                    'security_answer' => $lUser->security_answer ?? '',
                    'landmark' => $lUser->landmark ?? '',
                ]);
            }
        } else {
             $this->command->warn("Legacy users table ($legacyUsersTable) not found.");
        }
    }

    private function migrateTable($tableName, $meta, $categorySlug)
    {
        // Use default connection instead of legacy since we are in same DB now
        $rows = DB::table($tableName)->get();
        
        $metal = Metal::where('name', $meta['metal'])->first();
        $purity = MetalPurity::where('name', $meta['purity'])->first();
        $category = Category::where('slug', $categorySlug)->first();

        foreach ($rows as $row) {
            // Check if exists
            if (Product::where('product_code', $row->product_code)->exists()) {
                continue;
            }

            // Create Product
            $product = Product::create([
                'product_code' => $row->product_code,
                'name' => $row->name ?? 'Unknown Product',
                'slug' => Str::slug($row->name . '-' . $row->product_code),
                'description' => $row->description,
                
                'category_id' => $category ? $category->id : null,
                'metal_id' => $metal ? $metal->id : null,
                'metal_purity_id' => $purity ? $purity->id : null,
                
                'stock_quantity' => $row->stock_quantity ?? 0,
                'is_featured' => ($row->is_featured ?? 0) == 1,
                'delist' => ($row->delist ?? 0) == 1,
                
                'gender' => $row->gender ?? 'Unisex',
                'size' => $row->size,
                'gross_weight' => $row->gross_weight ?? 0,
                'net_weight' => $row->net_weight, 
                
                // Specs
                'stone_available' => $row->stone_available ?? 0,
                'stone_cost' => $row->stone_cost,
                'diamond_available' => $row->diamond_available ?? 0,
                'dia_count' => $row->dia_count,
                'dia_cent' => $row->dia_cent,
                
                // SEO
                'search_keywords' => $row->search_keywords ?? null,
            ]);

            // Migrate Images
            $images = [];
            
            if (!empty($row->img2)) {
                $images[] = ['path' => $row->img2, 'primary' => true, 'sort' => 0];
            }
            if (!empty($row->img1_webp)) {
                $images[] = ['path' => $row->img1_webp, 'primary' => false, 'sort' => 1];
            }
            if (!empty($row->img3)) {
                $images[] = ['path' => $row->img3, 'primary' => false, 'sort' => 2];
            }
            if (!empty($row->img4)) {
                $images[] = ['path' => $row->img4, 'primary' => false, 'sort' => 3];
            }
            if (!empty($row->img5)) {
                $images[] = ['path' => $row->img5, 'primary' => false, 'sort' => 4];
            }

            foreach ($images as $img) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $img['path'],
                    'is_primary' => $img['primary'],
                    'sort_order' => $img['sort']
                ]);
            }
        }
    }
}
