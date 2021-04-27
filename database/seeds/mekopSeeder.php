<?php
use Illuminate\Database\Seeder;

class mekopSeeder extends Seeder
{
/**
* Run the database seeds.
*
* @return void
*/
public function run()
{
    ini_set('memory_limit', '8192M');
    $path = 'database/seeds/bkkbn_mekop_dev.sql';
    DB::unprepared(file_get_contents($path));
    $this->command->info('Mekop table seeded!');

    $path = 'database/seeds/indonesia.sql';
    DB::unprepared(file_get_contents($path));
    $this->command->info('Indonesia table seeded!');
}
}