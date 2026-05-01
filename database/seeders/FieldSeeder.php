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
        $budi = User::where('email', 'budi@matchpoint.test')->firstOrFail();
        $mutias = User::where('email', 'mutias@matchpoint.test')->firstOrFail();

        $fields = [
            [
                'owner'      => $james,
                'name'       => 'GOR Pertamina Futsal',
                'location'   => 'Surabaya, East Java',
                'type'       => 'Indoor',
                'sport_type' => 'Futsal',
                'price'      => 150000,
                'slots'      => $this->operatingHourSlots('00:00'),
            ],
            [
                'owner'      => $james,
                'name'       => 'Arena Badminton Mawar',
                'location'   => 'Malang, East Java',
                'type'       => 'Outdoor',
                'sport_type' => 'Badminton',
                'price'      => 80000,
                'slots'      => $this->operatingHourSlots('00:00'),
            ],
            [
                'owner'      => $james,
                'name'       => 'Desi Stadium',
                'location'   => 'Sukodono, Malang',
                'type'       => 'Outdoor',
                'sport_type' => 'Football',
                'price'      => 250000,
                'slots'      => $this->operatingHourSlots('20:00'),
            ],
            [
                'owner'      => $mutias,
                'name'       => 'Lapangan Basket Kota',
                'location'   => 'Surabaya, East Java',
                'type'       => 'Outdoor',
                'sport_type' => 'Basketball',
                'price'      => 200000,
                'slots'      => $this->operatingHourSlots('00:00'),
            ],
            [
                'owner'      => $mutias,
                'name'       => 'Tennis Club Merdeka',
                'location'   => 'Malang, East Java',
                'type'       => 'Indoor',
                'sport_type' => 'Tennis',
                'price'      => 120000,
                'slots'      => $this->operatingHourSlots('00:00'),
            ],
            [
                'owner'      => $budi,
                'name'       => 'Volly Arena Malang',
                'location'   => 'Malang, East Java',
                'type'       => 'Indoor',
                'sport_type' => 'Volleyball',
                'price'      => 180000,
                'slots'      => $this->operatingHourSlots('00:00'),
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

            $field->timeSlots()->delete();

            foreach ($data['slots'] as $slot) {
                TimeSlot::create([
                    'field_id'           => $field->id,
                    'day_of_week'        => $slot['day'],
                    'start_time'         => $slot['start'],
                    'end_time'           => $slot['end'],
                    'is_available_base'  => true,
                ]);
            }

            $this->command->info("Field seeded: {$field->name} with " . count($data['slots']) . ' slots');
        }
    }

    private function operatingHourSlots(string $closingTime): array
    {
        $slots = [];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $closingHour = $closingTime === '00:00' ? 24 : (int) substr($closingTime, 0, 2);

        foreach ($days as $day) {
            for ($hour = 8; $hour < $closingHour; $hour++) {
                $start = sprintf('%02d:00', $hour);
                $end = $hour + 1 === 24 ? '00:00' : sprintf('%02d:00', $hour + 1);

                $slots[] = ['day' => $day, 'start' => $start, 'end' => $end];
            }
        }

        return $slots;
    }
}
