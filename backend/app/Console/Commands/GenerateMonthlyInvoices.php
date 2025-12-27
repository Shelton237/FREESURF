<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly {--period=}';

    protected $description = 'Generate monthly invoices for active customers';

    public function handle(): int
    {
        $period = $this->option('period') ?: now()->format('Y-m');
        $this->info("[CuWiP] Generating invoices for period {$period}...");
        // TODO: implement real generation logic
        $this->info('[CuWiP] Done (skeleton).');
        return self::SUCCESS;
    }
}

