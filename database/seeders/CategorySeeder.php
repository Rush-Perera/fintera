<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all unique categories from transactions grouped by type
        $incomeCategories = Transaction::where('type', 'income')
            ->whereNotNull('category')
            ->distinct('category')
            ->pluck('category')
            ->toArray();

        $expenseCategories = Transaction::where('type', 'expense')
            ->whereNotNull('category')
            ->distinct('category')
            ->pluck('category')
            ->toArray();

        $users = User::all();

        $incomeCategoryNames = array_unique(array_filter($incomeCategories));
        $expenseCategoryNames = array_unique(array_filter($expenseCategories));

        // Default icons and colors for common categories
        $categoryDefaults = [
            'Salary' => ['icon' => '💼', 'color' => '#2ecc71'],
            'Bonus' => ['icon' => '🎁', 'color' => '#27ae60'],
            'Freelance' => ['icon' => '💻', 'color' => '#3498db'],
            'Investment' => ['icon' => '📈', 'color' => '#f39c12'],
            'Other Income' => ['icon' => '💰', 'color' => '#9b59b6'],
            'Food' => ['icon' => '🍔', 'color' => '#e74c3c'],
            'Transport' => ['icon' => '🚕', 'color' => '#3498db'],
            'Entertainment' => ['icon' => '🎮', 'color' => '#e91e63'],
            'Shopping' => ['icon' => '🛍️', 'color' => '#f39c12'],
            'Utilities' => ['icon' => '💡', 'color' => '#95a5a6'],
            'Healthcare' => ['icon' => '🏥', 'color' => '#e74c3c'],
            'Education' => ['icon' => '📚', 'color' => '#3498db'],
            'Rent' => ['icon' => '🏠', 'color' => '#c0392b'],
            'Insurance' => ['icon' => '🛡️', 'color' => '#2980b9'],
            'Other Expense' => ['icon' => '💳', 'color' => '#7f8c8d'],
        ];

        // Create categories for each user
        foreach ($users as $user) {
            // Income categories
            foreach ($incomeCategoryNames as $categoryName) {
                $categoryName = trim($categoryName);
                if (empty($categoryName)) {
                    continue;
                }

                $defaults = $categoryDefaults[$categoryName] ?? ['icon' => '📊', 'color' => '#2ecc71'];

                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $categoryName,
                        'type' => 'income',
                    ],
                    [
                        'icon' => $defaults['icon'],
                        'color' => $defaults['color'],
                    ]
                );
            }

            // Expense categories
            foreach ($expenseCategoryNames as $categoryName) {
                $categoryName = trim($categoryName);
                if (empty($categoryName)) {
                    continue;
                }

                $defaults = $categoryDefaults[$categoryName] ?? ['icon' => '💳', 'color' => '#e74c3c'];

                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $categoryName,
                        'type' => 'expense',
                    ],
                    [
                        'icon' => $defaults['icon'],
                        'color' => $defaults['color'],
                    ]
                );
            }
        }
    }
}
