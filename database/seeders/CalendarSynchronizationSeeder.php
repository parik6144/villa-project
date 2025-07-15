<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PropertyCalendarSynchronization;

class CalendarSynchronizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = PropertyCalendarSynchronization::getCalendarSources();

        PropertyCalendarSynchronization::create([
            'property_id' => 1,
            'calendar_source' => $sources['Google Calendar'],
            'calendar_url' => 'https://calendar.google.com/calendar.ics',
            'calendar_name' => 'Google Work Calendar',
        ]);

        PropertyCalendarSynchronization::create([
            'property_id' => 2,
            'calendar_source' => $sources['Outlook Calendar'],
            'calendar_url' => 'https://outlook.live.com/calendar.ics',
            'calendar_name' => 'Outlook Personal Calendar',
        ]);
    }
}
