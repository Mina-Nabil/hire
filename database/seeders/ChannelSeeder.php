<?php

namespace Database\Seeders;

use App\Models\Recruitment\Applicants\Channel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        ];

        foreach ($channels as $channel) {
            Channel::newChannel($channel);
        }
    }
}
