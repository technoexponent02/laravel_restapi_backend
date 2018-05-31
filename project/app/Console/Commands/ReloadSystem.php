<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;
use File;

class ReloadSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reloadsystem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs a couple of commands and does the work to configure the system.';

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
     * @return mixed
     */
    public function handle()
    {
        echo 'Starting the database migration' . PHP_EOL;
        $exitCode = Artisan::call('migrate:refresh', [
            '--force' => true,
        ]);
        echo 'Finishing the database migration' . PHP_EOL;
        echo 'Starting the database seeding' . PHP_EOL;
        $exitCode = Artisan::call('db:seed', [
            '--force' => true,
        ]);
        echo 'Finishing the database seeding' . PHP_EOL;
        echo 'Cleaning up the images' . PHP_EOL;
        $directory = '../uploads/linked_images';
        $success = File::cleanDirectory($directory);
        $directory = '../uploads/cover_pictures';
        $success = File::cleanDirectory($directory);
        $directory = '../uploads/profile_pictures';
        $success = File::cleanDirectory($directory);
        //print_r($success);
        echo 'The system is all set to go.' . PHP_EOL;
    }
}
