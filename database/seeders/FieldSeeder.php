<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $james = User::where('email', 'james@matchpoint.test')->firstOrFail();
        $mutias = User::where('email', 'mutias@matchpoint.test')->firstOrFail();

        $fields = [
            [
                'owner'      => $james,
                'name'       => 'GOR Pertamina Futsal',
                'location'   => 'Surabaya, East Java',
                'type'       => 'Indoor',
                'sport_type' => 'Futsal',
                'price'      => 150000,
                'slots'      => $this->standardSlots(),
            ],
            [
                'owner'      => $james,
                'name'       => 'Arena Badminton Mawar',
                'location'   => 'Malang, East Java',
                'type'       => 'Outdoor',
                'sport_type' => 'Badminton',
                'price'      => 80000,
                'slots'      => $this->weekendSlots(),
            ],
            [
                'owner'      => $james,
                'name'       => 'Desi Stadium',
                'location'   => 'Sukodono, Malang',
                'type'       => 'Outdoor',
                'sport_type' => 'Football',
                'price'      => 250000,
                'slots'      => $this->standardSlots(),
            ],
            [
                'owner'      => $mutias,
                'name'       => 'Lapangan Basket Kota',
                'location'   => 'Surabaya, East Java',
                'type'       => 'Outdoor',
                'sport_type' => 'Basketball',
                'price'      => 200000,
                'slots'      => $this->standardSlots(),
            ],
            [
                'owner'      => $mutias,
                'name'       => 'Tennis Club Merdeka',
                'location'   => 'Malang, East Java',
                'type'       => 'Indoor',
                'sport_type' => 'Tennis',
                'price'      => 120000,
                'slots'      => $this->eveningSlots(),
            ],
        ];

        foreach ($fields as $data) {
            $field = Field::updateOrCreate(
                [
                    'owner_id' => $data['owner']->id,
                    'name'     => $data['name'],
                ],
                [
                    'location'       => $data['location'],
                    'type'           => $data['type'],
                    'sport_type'     => $data['sport_type'],
                    'price_per_slot' => $data['price'],
                    'is_approved'    => true,
                ]
            );

            foreach ($data['slots'] as $slot) {
                TimeSlot::updateOrCreate(
                    [
                        'field_id'    => $field->id,
                        'day_of_week' => $slot['day'],
                        'start_time'  => $slot['start'],
                    ],
                    [
                        'end_time'          => $slot['end'],
                        'is_available_base' => true,
                    ]
                );
            }

            $this->command->info("Field seeded: {$field->name} with " . count($data['slots']) . ' slots');
        }
    }

    private function standardSlots(): array
    {
        $slots = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $times = [
            ['08:00', '10:00'],
            ['10:00', '12:00'],
            ['14:00', '16:00'],
            ['16:00', '18:00'],
        ];

        foreach ($days as $day) {
            foreach ($times as [$start, $end]) {
                $slots[] = ['day' => $day, 'start' => $start, 'end' => $end];
            }
        }

        return $slots;
    }

    private function weekendSlots(): array
    {
        $slots = [];
        $days = ['Saturday', 'Sunday'];
        $times = [
            ['07:00', '09:00'],
            ['09:00', '11:00'],
            ['13:00', '15:00'],
            ['15:00', '17:00'],
            ['17:00', '19:00'],
        ];

        foreach ($days as $day) {
            foreach ($times as [$start, $end]) {
                $slots[] = ['day' => $day, 'start' => $start, 'end' => $end];
            }
        }

        return $slots;
    }

    private function eveningSlots(): array
    {
        $slots = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $times = [
            ['18:00', '20:00'],
            ['20:00', '22:00'],
        ];

        foreach ($days as $day) {
            foreach ($times as [$start, $end]) {
                $slots[] = ['day' => $day, 'start' => $start, 'end' => $end];
            }
        }

        return $slots;
    }
}


