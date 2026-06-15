<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name'     => 'Alex Morgan',
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $categories = [
            ['name' => 'Salary',         'type' => 'income',  'color' => '#10B981'],
            ['name' => 'Freelance',      'type' => 'income',  'color' => '#3B82F6'],
            ['name' => 'Investments',    'type' => 'income',  'color' => '#8B5CF6'],
            ['name' => 'Bonus',          'type' => 'income',  'color' => '#06B6D4'],
            ['name' => 'Rent',           'type' => 'expense', 'color' => '#EF4444'],
            ['name' => 'Food & Dining',  'type' => 'expense', 'color' => '#F59E0B'],
            ['name' => 'Transport',      'type' => 'expense', 'color' => '#F97316'],
            ['name' => 'Utilities',      'type' => 'expense', 'color' => '#6366F1'],
            ['name' => 'Entertainment',  'type' => 'expense', 'color' => '#EC4899'],
            ['name' => 'Healthcare',     'type' => 'expense', 'color' => '#14B8A6'],
            ['name' => 'Shopping',       'type' => 'expense', 'color' => '#A855F7'],
            ['name' => 'Subscriptions',  'type' => 'expense', 'color' => '#64748B'],
        ];

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[$cat['name']] = Category::create(array_merge($cat, ['user_id' => $user->id]));
        }

        $incomeTemplates = [
            'Salary'      => ['amount' => 7200, 'day' => 1,  'desc' => 'Monthly salary deposit'],
            'Freelance'   => ['amount' => [1200, 1800, 950, 2100], 'day' => 15, 'desc' => ['Client project payment', 'Consulting invoice', 'Design contract', 'Development retainer']],
            'Investments' => ['amount' => [320, 450, 280, 510], 'day' => 20, 'desc' => ['Dividend payout', 'Interest income', 'ETF distribution', 'Bond coupon']],
            'Bonus'       => ['amount' => [2500, 1800], 'months' => [3, 12], 'day' => 28, 'desc' => ['Q1 performance bonus', 'Year-end bonus']],
        ];

        $expenseTemplates = [
            'Rent'          => ['budget' => 1850, 'amount' => 1850, 'day' => 1,  'desc' => 'Monthly rent payment'],
            'Food & Dining' => ['budget' => 650,  'range' => [420, 780],  'desc' => ['Grocery shopping', 'Restaurant dinner', 'Coffee & lunch', 'Weekly groceries', 'Takeout order', 'Farmers market']],
            'Transport'     => ['budget' => 320,  'range' => [180, 410],  'desc' => ['Fuel refill', 'Uber rides', 'Parking fees', 'Public transit pass', 'Car maintenance']],
            'Utilities'     => ['budget' => 280,  'range' => [195, 340],  'desc' => ['Electricity bill', 'Internet & phone', 'Water bill', 'Gas utility']],
            'Entertainment' => ['budget' => 200,  'range' => [45, 280],   'desc' => ['Cinema tickets', 'Streaming subscription', 'Concert tickets', 'Gaming purchase']],
            'Healthcare'    => ['budget' => 150,  'range' => [35, 220],   'desc' => ['Pharmacy', 'Doctor visit', 'Dental checkup', 'Health insurance co-pay']],
            'Shopping'      => ['budget' => 400,  'range' => [60, 520],   'desc' => ['Clothing purchase', 'Electronics', 'Home supplies', 'Amazon order']],
            'Subscriptions' => ['budget' => 95,   'range' => [85, 110],   'desc' => ['Software subscriptions', 'Gym membership', 'Cloud storage', 'News subscription']],
        ];

        $year = now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($year, $month, 1);

            // Salary every month
            Transaction::create([
                'user_id'     => $user->id,
                'category_id' => $catMap['Salary']->id,
                'amount'      => 7200 + ($month % 4 === 0 ? 200 : 0),
                'type'        => 'income',
                'date'        => $date->copy()->day(1),
                'description' => 'Monthly salary deposit',
            ]);

            // Freelance (most months)
            if ($month % 5 !== 0) {
                $freelanceAmounts = $incomeTemplates['Freelance']['amount'];
                $freelanceDescs   = $incomeTemplates['Freelance']['desc'];
                Transaction::create([
                    'user_id'     => $user->id,
                    'category_id' => $catMap['Freelance']->id,
                    'amount'      => $freelanceAmounts[($month - 1) % count($freelanceAmounts)],
                    'type'        => 'income',
                    'date'        => $date->copy()->day(15),
                    'description' => $freelanceDescs[($month - 1) % count($freelanceDescs)],
                ]);
            }

            // Investments (every other month)
            if ($month % 2 === 0) {
                $invAmounts = $incomeTemplates['Investments']['amount'];
                Transaction::create([
                    'user_id'     => $user->id,
                    'category_id' => $catMap['Investments']->id,
                    'amount'      => $invAmounts[($month / 2 - 1) % count($invAmounts)],
                    'type'        => 'income',
                    'date'        => $date->copy()->day(20),
                    'description' => $incomeTemplates['Investments']['desc'][($month / 2 - 1) % 4],
                ]);
            }

            // Bonus in March and December
            if (in_array($month, [3, 12])) {
                $bonusIdx = $month === 3 ? 0 : 1;
                Transaction::create([
                    'user_id'     => $user->id,
                    'category_id' => $catMap['Bonus']->id,
                    'amount'      => $incomeTemplates['Bonus']['amount'][$bonusIdx],
                    'type'        => 'income',
                    'date'        => $date->copy()->day(28),
                    'description' => $incomeTemplates['Bonus']['desc'][$bonusIdx],
                ]);
            }

            // Expenses
            foreach ($expenseTemplates as $name => $tpl) {
                $cat = $catMap[$name];
                $daysInMonth = $date->daysInMonth;

                if (isset($tpl['amount'])) {
                    Transaction::create([
                        'user_id'     => $user->id,
                        'category_id' => $cat->id,
                        'amount'      => $tpl['amount'],
                        'type'        => 'expense',
                        'date'        => $date->copy()->day(min($tpl['day'], $daysInMonth)),
                        'description' => $tpl['desc'],
                    ]);
                } else {
                    $txCount = $name === 'Food & Dining' ? 8 : ($name === 'Transport' ? 5 : 3);
                    for ($t = 0; $t < $txCount; $t++) {
                        $amount = rand($tpl['range'][0] * 100, $tpl['range'][1] * 100) / 100 / $txCount;
                        $amount = round($amount, 2);
                        Transaction::create([
                            'user_id'     => $user->id,
                            'category_id' => $cat->id,
                            'amount'      => max($amount, 5),
                            'type'        => 'expense',
                            'date'        => $date->copy()->day(rand(1, $daysInMonth)),
                            'description' => $tpl['desc'][$t % count($tpl['desc'])],
                        ]);
                    }
                }

                Budget::create([
                    'user_id'     => $user->id,
                    'category_id' => $cat->id,
                    'amount'      => $tpl['budget'],
                    'month'       => $month,
                    'year'        => $year,
                ]);
            }
        }

        // Add some prior-year data for year-over-year comparison
        for ($month = 1; $month <= 6; $month++) {
            $date = Carbon::create($year - 1, $month, 1);
            Transaction::create([
                'user_id'     => $user->id,
                'category_id' => $catMap['Salary']->id,
                'amount'      => 6800,
                'type'        => 'income',
                'date'        => $date->copy()->day(1),
                'description' => 'Monthly salary deposit',
            ]);
            foreach (['Rent', 'Food & Dining', 'Transport', 'Utilities'] as $name) {
                $tpl = $expenseTemplates[$name];
                Transaction::create([
                    'user_id'     => $user->id,
                    'category_id' => $catMap[$name]->id,
                    'amount'      => isset($tpl['amount']) ? $tpl['amount'] : rand($tpl['range'][0], $tpl['range'][1]),
                    'type'        => 'expense',
                    'date'        => $date->copy()->day(rand(1, 28)),
                    'description' => is_array($tpl['desc']) ? $tpl['desc'][0] : $tpl['desc'],
                ]);
            }
        }
    }
}
