<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticket::factory()->count(5)->open()->create();
        Ticket::factory()->count(3)->inProgress()->create();
        Ticket::factory()->count(2)->closed()->create();
    }
}
