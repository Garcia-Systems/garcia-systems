<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'company',
        'source',
        'status',
        'assessment_score',
        'assessment_tier',
        'notes',
        'latest_activity_at',
    ];

    protected $casts = [
        'latest_activity_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return ['new', 'reviewing', 'contacted', 'qualified', 'closed', 'archived'];
    }

    public static function sources(): array
    {
        return ['contact_form', 'ai_readiness_assessment'];
    }

    public static function createOrUpdateFromContactSubmission(ContactSubmission $submission): self
    {
        $lead = self::forEmail($submission->email);

        $lead->fill([
            'name' => $submission->name,
            'email' => mb_strtolower($submission->email),
            'company' => $submission->company,
            'source' => 'contact_form',
            'status' => 'new',
            'latest_activity_at' => $submission->created_at ?? now(),
        ])->save();

        $submission->update(['lead_id' => $lead->id]);

        return $lead;
    }

    public static function createOrUpdateFromAssessment(Assessment $assessment): ?self
    {
        if (blank($assessment->email)) {
            return null;
        }

        $lead = self::forEmail($assessment->email);
        $status = $lead->exists && $lead->status !== 'new' ? $lead->status : 'new';

        $lead->fill([
            'name' => $assessment->name ?: $lead->name,
            'email' => mb_strtolower($assessment->email),
            'company' => $assessment->company ?: $lead->company,
            'source' => 'ai_readiness_assessment',
            'status' => $status,
            'assessment_score' => $assessment->score,
            'assessment_tier' => $assessment->result_tier,
            'latest_activity_at' => $assessment->created_at ?? now(),
        ])->save();

        $assessment->update(['lead_id' => $lead->id]);

        return $lead;
    }

    public function contactSubmissions()
    {
        return $this->hasMany(ContactSubmission::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    private static function forEmail(string $email): self
    {
        return self::firstOrNew(['email' => mb_strtolower($email)]);
    }
}
