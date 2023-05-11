<?php

namespace App\Console\Commands;

use App\Http\Controllers\BillController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:generate {--bills}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            (new BillController)->generateBillsOfRecurrentes();
            return Command::SUCCESS;

        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return Command::FAILURE;
        }
    }
}
