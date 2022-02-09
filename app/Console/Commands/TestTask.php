<?php

namespace App\Console\Commands;

use App\Http\Controllers\AperturaController;
use App\Models\Apertura;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class TestTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creacion de apertura cada 1 de cada mes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $fecha = Carbon::now();
        $fecha = $fecha->toDateString();
        $apertura = new AperturaController();
        $apertura->store($fecha);
        $texto =  'Esto es automatico: ' . date("Y-m-d H:i:s");
        Storage::append("lista.txt", $fecha);
    }
}
