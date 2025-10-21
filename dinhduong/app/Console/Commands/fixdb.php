<?php

namespace App\Console\Commands;

use App\Models\GirlsWeightForAge;
use App\Models\WeightForAge;
use Illuminate\Console\Command;

class fixdb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $rows = GirlsWeightForAge::get();
//        foreach($rows as $row){
//            WeightForAge::create([
//                'gender' => 1,
//                'Year_Month' => $row['Year_Month'],
//                'Months'     => $row['Months'],
//                '-3SD'       => $row['-3SD'],
//                '-2SD'       => $row['-2SD'],
//                '-1SD'       => $row['-1SD'],
//                'Median'     => $row['Median'],
//                '1SD'        => $row['1SD'],
//                '2SD'        => $row['2SD'],
//                '3SD'        => $row['3SD'],
//            ]);
//        }
    }
}
