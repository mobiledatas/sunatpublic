<?php

namespace App\Console\Commands;

use App\Models\CorrelativeService;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CorrelativeSerie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'correlative:edit {--action=edit} {--index=1}';

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
        $action = $this->option('action');
        if($action == 'deleteall'){
            CorrelativeService::truncate();
            if(($this->option('index')) != null){
                Schema::table('correlative_services',function(Blueprint $table){
                    $table->id()->startingValue($this->option('index'))->change();
                });
            }
            $this->info('Se ejecuto con exito');

        }
        if($action == 'edit'){
            if(($this->option('index')) != null){
                $statement = "ALTER TABLE correlative_services AUTO_INCREMENT = {$this->option('index')}";
                DB::unprepared($statement);
            }
            $this->info('Se ejecuto con exito');
        }

        return 0;
    }
}
