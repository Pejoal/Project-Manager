<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ActivityResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    // Helper function to format properties, making the code DRY
    $formatProperties = function ($props) {
      if (!$props) {
        return null;
      }

      if (isset($props['start_datetime'])) {
        $props['start_datetime_formatted'] = Carbon::parse($props['start_datetime'])->isoFormat('L LT'); // Locale-aware format
      }
      if (isset($props['end_datetime'])) {
        $props['end_datetime_formatted'] = Carbon::parse($props['end_datetime'])->isoFormat('L LT');
      }
      if (isset($props['hire_date'])) {
        $props['hire_date'] = Carbon::parse($props['hire_date'])->isoFormat('L LT');
      }
      if (isset($props['termination_date'])) {
        $props['termination_date'] = Carbon::parse($props['termination_date'])->isoFormat('L LT');
      }
      if (isset($props['pay_period_start'])) {
        $props['pay_period_start'] = Carbon::parse($props['pay_period_start'])->isoFormat('L LT');
      }
      if (isset($props['pay_period_end'])) {
        $props['pay_period_end'] = Carbon::parse($props['pay_period_end'])->isoFormat('L LT');
      }
      if (isset($props['gross_amount'])) {
        $props['gross_amount_formatted'] = '€' . number_format($props['gross_amount'], 2, ',', '.');
      }
      return $props;
    };

    return [
      'id' => $this->id,
      'description' => $this->description,
      'event' => $this->event,
      'subject_type' => $this->subject_type ? class_basename($this->subject_type) : 'System',
      'subject_id' => $this->subject_id,
      'causer' => $this->causer
        ? [
          'id' => $this->causer->id,
          'name' => $this->causer->name,
          'profile_photo_url' => $this->causer->profile_photo_url,
        ]
        : ['name' => 'System'],

      // Formatted timestamps for display
      'created_at_formatted' => $this->created_at->isoFormat('LLLL'), // e.g., "Saturday, October 4, 2025 9:16 PM"
      'created_at_human' => $this->created_at->diffForHumans(),

      // Apply formatting to the nested 'old' and 'attributes' data
      'properties' => [
        'old' => $formatProperties($this->properties->get('old')),
        'attributes' => $formatProperties($this->properties->get('attributes')),
      ],
    ];
  }
}
