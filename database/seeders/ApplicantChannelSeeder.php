<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ApplicantChannel;
use App\Models\Recruitment\Applicants\Channel;

class ChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = [
            'LinkedIn',
            'CFA Society',
            'AMCham',
            'Recruitment Agency',
            'Consultants',
            'Others',
        ];

        foreach ($channels as $channel) {
            Channel::create([
                'name' => $channel
            ]);
        }
    }
}
