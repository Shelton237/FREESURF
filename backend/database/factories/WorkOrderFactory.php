<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Carbon\Carbon;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    public function definition(): array
    {
        $status = Arr::random(['assigned', 'accepted', 'on_site', 'completed']);
        $type = $this->faker->randomElement(['survey', 'install', 'maintenance']);
        $scheduled = $this->faker->dateTimeBetween('-15 days', '+15 days');
        $scheduledEnd = (clone Carbon::instance($scheduled))->addDays(5);
        $started = in_array($status, ['accepted', 'on_site', 'completed'], true)
            ? $this->faker->dateTimeBetween($scheduled, $scheduledEnd)
            : null;
        $completed = $status === 'completed'
            ? $this->faker->dateTimeBetween($started ?? $scheduled, (clone Carbon::instance($scheduled))->addDays(8))
            : null;

        $surveyFields = [
            'survey_result' => null,
            'survey_reason' => null,
            'survey_follow_up' => false,
        ];

        if ($type === 'survey' && $status === 'completed') {
            $surveyFields['survey_result'] = $this->faker->randomElement(['available', 'not_available']);
            $surveyFields['survey_reason'] = $surveyFields['survey_result'] === 'not_available'
                ? $this->faker->randomElement(['Obstacle', 'Puissance faible', 'Visibilité limitée'])
                : null;
            $surveyFields['survey_follow_up'] = $this->faker->boolean(30);
        }

        return [
            'type' => $type,
            'client_id' => null,
            'bts_id' => null,
            'assigned_to' => null,
            'status' => $status,
            'scheduled_at' => $scheduled,
            'started_at' => $started,
            'completed_at' => $completed,
            'lat' => $this->faker->latitude(-4, 5),
            'lng' => $this->faker->longitude(10, 15),
            'notes' => $this->faker->sentence(),
            'checklist' => [
                ['label' => 'Inspection site', 'done' => $this->faker->boolean()],
                ['label' => 'Test signal', 'done' => $this->faker->boolean()],
                ['label' => 'Validation client', 'done' => $this->faker->boolean()],
            ],
            ...$surveyFields,
        ];
    }
}
