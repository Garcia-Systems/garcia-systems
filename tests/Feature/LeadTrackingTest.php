<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\ContactSubmission;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\AssessmentSubmitted;
use App\Notifications\ContactSubmissionReceived;
use App\Notifications\LeadSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LeadTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_creates_and_updates_lead(): void
    {
        $this->post('/contact', ['name' => 'Avery Garcia', 'email' => 'avery@example.com', 'company' => 'First Co', 'message' => 'Hello']);
        $this->assertDatabaseHas('leads', ['email' => 'avery@example.com', 'name' => 'Avery Garcia', 'company' => 'First Co', 'source' => 'contact_form', 'status' => 'new']);
        $this->assertSame(Lead::first()->id, ContactSubmission::first()->lead_id);

        $this->post('/contact', ['name' => 'Avery Updated', 'email' => 'avery@example.com', 'company' => 'Second Co', 'message' => 'Again']);
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseHas('leads', ['email' => 'avery@example.com', 'name' => 'Avery Updated', 'company' => 'Second Co']);
    }

    public function test_assessment_creates_and_updates_lead_without_resetting_later_status(): void
    {
        $question = AssessmentQuestion::create(['question' => 'Ready?', 'sort_order' => 1]);
        Lead::create(['name' => 'Morgan', 'email' => 'morgan@example.com', 'source' => 'contact_form', 'status' => 'contacted']);

        $this->post('/ai-readiness-assessment', ['name' => 'Morgan Lee', 'email' => 'morgan@example.com', 'company' => 'Readiness Co', 'responses' => [$question->id => 5]]);

        $lead = Lead::sole();
        $assessment = Assessment::sole();
        $this->assertSame('contacted', $lead->status);
        $this->assertSame('ai_readiness_assessment', $lead->source);
        $this->assertSame(5, $lead->assessment_score);
        $this->assertSame('Early', $lead->assessment_tier);
        $this->assertSame($lead->id, $assessment->lead_id);
    }

    public function test_contact_form_sends_internal_and_visitor_notifications(): void
    {
        Mail::fake();
        Notification::fake();
        config([
            'app.url' => 'https://example.test',
            'mail.from.address' => 'hello@example.test',
            'mail.from.name' => 'Garcia Systems',
            'mail.lead_notification_email' => 'private-notifications@example.test',
        ]);

        $this->post('/contact', [
            'name' => 'Notify Lead',
            'email' => 'notify@example.com',
            'company' => 'Notify Co',
            'service_interest' => 'Workflow automation',
            'message' => 'Please follow up.',
        ])->assertSessionHas('status');

        $submission = ContactSubmission::sole();
        $lead = Lead::sole();
        $adminUrl = route('admin.leads.show', $lead);

        $this->assertSame($lead->id, $submission->lead_id);
        $this->assertDatabaseHas('leads', [
            'email' => 'notify@example.com',
            'name' => 'Notify Lead',
            'company' => 'Notify Co',
            'source' => 'contact_form',
            'status' => 'new',
        ]);

        Notification::assertSentOnDemand(LeadSubmitted::class);
        Notification::assertSentOnDemand(ContactSubmissionReceived::class);

        $internalNotification = null;
        $internalChannels = null;
        $internalNotifiable = null;

        Notification::assertSentOnDemand(LeadSubmitted::class, function (LeadSubmitted $notification, array $channels, object $notifiable) use (&$internalNotification, &$internalChannels, &$internalNotifiable) {
            $internalNotification = $notification;
            $internalChannels = $channels;
            $internalNotifiable = $notifiable;

            return true;
        });

        $this->assertContains('mail', $internalChannels);
        $this->assertSame('private-notifications@example.test', $internalNotifiable->routes['mail']);
        $this->assertTrue($internalNotification->lead->is($lead));
        $this->assertTrue($internalNotification->submission->is($submission));

        $internalMail = $internalNotification->toMail($internalNotifiable);
        $internalHtml = view($internalMail->view, $internalMail->viewData)->render();

        $this->assertSame('New Garcia Systems inquiry from Notify Lead', $internalMail->subject);
        $this->assertSame('notify@example.com', data_get($internalMail->replyTo, '0.0'));
        $this->assertSame('Notify Lead', data_get($internalMail->replyTo, '0.1'));
        $this->assertStringContainsString('Lead ID', $internalHtml);
        $this->assertStringContainsString((string) $lead->id, $internalHtml);
        $this->assertStringContainsString($adminUrl, $internalHtml);

        $visitorNotification = null;
        $visitorChannels = null;
        $visitorNotifiable = null;

        Notification::assertSentOnDemand(ContactSubmissionReceived::class, function (ContactSubmissionReceived $notification, array $channels, object $notifiable) use (&$visitorNotification, &$visitorChannels, &$visitorNotifiable) {
            $visitorNotification = $notification;
            $visitorChannels = $channels;
            $visitorNotifiable = $notifiable;

            return true;
        });

        $this->assertContains('mail', $visitorChannels);

        $visitorRoute = $visitorNotifiable->routes['mail'];
        $visitorEmail = is_array($visitorRoute) ? array_key_first($visitorRoute) : $visitorRoute;

        $this->assertSame('notify@example.com', $visitorEmail);

        if (is_array($visitorRoute)) {
            $this->assertSame('Notify Lead', $visitorRoute[$visitorEmail]);
        }

        $visitorMail = $visitorNotification->toMail($visitorNotifiable);
        $visitorHtml = view($visitorMail->view, $visitorMail->viewData)->render();

        $this->assertSame('We received your Garcia Systems inquiry', $visitorMail->subject);
        $this->assertSame('hello@example.test', data_get($visitorMail->replyTo, '0.0'));
        $this->assertSame('Garcia Systems', data_get($visitorMail->replyTo, '0.1'));
        $this->assertNotSame(config('mail.lead_notification_email'), data_get($visitorMail->replyTo, '0.0'));
        $this->assertStringNotContainsString('private-notifications@example.test', $visitorMail->subject);
        $this->assertStringNotContainsString('private-notifications@example.test', data_get($visitorMail->replyTo, '0.0', ''));
        $this->assertStringNotContainsString('private-notifications@example.test', data_get($visitorMail->replyTo, '0.1', ''));
        $this->assertStringNotContainsString('private-notifications@example.test', $visitorHtml);
        $this->assertStringContainsString('Notify Co', $visitorHtml);
        $this->assertStringContainsString('Workflow automation', $visitorHtml);
        $this->assertStringContainsString('Please follow up.', $visitorHtml);
        $this->assertStringNotContainsString('Lead ID', $visitorHtml);
        $this->assertStringNotContainsString($adminUrl, $visitorHtml);
    }

    public function test_assessment_submission_sends_admin_notification_with_score_and_tier(): void
    {
        Mail::fake();
        Notification::fake();
        config(['mail.lead_notification_email' => 'admin@example.com']);

        $question = AssessmentQuestion::create(['question' => 'Ready?', 'sort_order' => 1]);

        $this->post('/ai-readiness-assessment', [
            'name' => 'Assess Lead',
            'email' => 'assess@example.com',
            'company' => 'Assess Co',
            'responses' => [$question->id => 5],
        ])->assertRedirect();

        Notification::assertSentOnDemand(AssessmentSubmitted::class, function (AssessmentSubmitted $notification, array $channels, object $notifiable) {
            return in_array('mail', $channels, true)
                && $notifiable->routes['mail'] === 'admin@example.com'
                && $notification->assessment->score === 5
                && $notification->assessment->result_tier === 'Early'
                && $notification->lead?->email === 'assess@example.com';
        });
    }

    public function test_contact_mail_diagnostic_logging_records_success_events(): void
    {
        Notification::fake();
        Log::spy();
        config([
            'mail.default' => 'smtp',
            'queue.default' => 'sync',
            'mail.lead_notification_email' => 'admin@example.com',
        ]);

        $this->post('/contact', [
            'name' => 'Diagnostic Lead',
            'email' => 'diagnostic@example.com',
            'message' => 'Please follow up.',
        ])->assertSessionHas('status');

        $submission = ContactSubmission::sole();
        $lead = Lead::sole();

        Log::shouldHaveReceived('info')->with('contact.mail.internal.start', \Mockery::on(fn (array $context) =>
            $context['contact_submission_id'] === $submission->id
            && $context['lead_id'] === $lead->id
            && $context['internal_notification_recipient'] === 'admin@example.com'
            && $context['visitor_confirmation_recipient'] === 'diagnostic@example.com'
            && $context['mailer'] === 'smtp'
            && $context['queue_connection'] === 'sync'
        ));
        Log::shouldHaveReceived('info')->with('contact.mail.internal.sent', \Mockery::type('array'));
        Log::shouldHaveReceived('info')->with('contact.mail.confirmation.start', \Mockery::type('array'));
        Log::shouldHaveReceived('info')->with('contact.mail.confirmation.sent', \Mockery::type('array'));
    }

    public function test_contact_mail_diagnostic_logging_records_failure_events_when_transport_throws(): void
    {
        Log::spy();
        config(['mail.lead_notification_email' => 'admin@example.com']);

        Mail::shouldReceive('mailer')
            ->twice()
            ->andReturn(new class {
                public function send(mixed ...$arguments): void
                {
                    throw new \RuntimeException('Transport unavailable for diagnostics');
                }
            });

        $this->post('/contact', [
            'name' => 'Failure Lead',
            'email' => 'failure@example.com',
            'message' => 'Please follow up.',
        ])->assertSessionHas('status');

        $submission = ContactSubmission::sole();

        Log::shouldHaveReceived('error')->with('contact.mail.internal.failed', \Mockery::on(fn (array $context) =>
            $context['contact_submission_id'] === $submission->id
            && $context['exception_class'] === \RuntimeException::class
            && $context['exception_message'] === 'Transport unavailable for diagnostics'
        ));
        Log::shouldHaveReceived('error')->with('contact.mail.confirmation.failed', \Mockery::on(fn (array $context) =>
            $context['contact_submission_id'] === $submission->id
            && $context['exception_class'] === \RuntimeException::class
            && $context['exception_message'] === 'Transport unavailable for diagnostics'
        ));
    }

    public function test_contact_mail_diagnostics_command_masks_secrets_and_reports_latest_submission(): void
    {
        Notification::fake();

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => 'smtp.example.test',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.encryption' => 'tls',
            'mail.mailers.smtp.password' => 'seeded-super-secret-password',
            'mail.from.address' => 'hello@example.test',
            'mail.lead_notification_email' => 'diagnostics@garciasystems.org',
        ]);

        $this->post('/contact', [
            'name' => 'Command Lead',
            'email' => 'command@example.com',
            'message' => 'Please follow up.',
        ])->assertSessionHas('status');

        $exitCode = Artisan::call('contact:mail-diagnostics');
        $output = Artisan::output();

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('mail.default: smtp', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('Mail host: smtp.example.test', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('Mail port: 587', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('Mail encryption: tls', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('Configured from address: hello@example.test', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('Masked internal recipient: d***@garciasystems.org', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('App\\Notifications\\LeadSubmitted', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringContainsString('App\\Notifications\\ContactSubmissionReceived', $output, "Actual diagnostics output:\n{$output}");
        $this->assertStringNotContainsString('seeded-super-secret-password', $output, "Actual diagnostics output:\n{$output}");
    }

    public function test_contact_mail_diagnostics_command_succeeds_without_submissions_or_queue_tables(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');

        $this->artisan('contact:mail-diagnostics')
            ->assertExitCode(0)
            ->expectsOutputToContain('table missing')
            ->expectsOutputToContain('(none)');
    }

    public function test_contact_honeypot_blocks_spam_without_persisting_or_notifying(): void
    {
        Mail::fake();
        Notification::fake();

        $this->from('/contact')->post('/contact', [
            'name' => 'Spam Lead',
            'email' => 'spam@example.com',
            'message' => 'Spam payload',
            'website' => 'https://spam.example',
        ])->assertRedirect('/contact');

        $this->assertDatabaseMissing('leads', ['email' => 'spam@example.com']);
        $this->assertDatabaseMissing('contact_submissions', ['email' => 'spam@example.com']);
        Notification::assertNothingSent();
    }

    public function test_admin_leads_index_requires_auth(): void
    {
        $this->get('/admin/leads')->assertRedirect('/login');
    }

    public function test_admin_can_view_leads_index(): void
    {
        $lead = Lead::create(['name' => 'Jordan Lead', 'email' => 'jordan@example.com', 'company' => 'Jordan Co', 'source' => 'contact_form', 'status' => 'new']);
        $this->actingAs(User::factory()->create())->get('/admin/leads')->assertOk()->assertSee($lead->name)->assertSee($lead->email)->assertSee('Jordan Co');
    }

    public function test_admin_can_search_and_filter_leads(): void
    {
        Lead::create(['name' => 'Needle Lead', 'email' => 'needle@example.com', 'company' => 'Search Co', 'source' => 'contact_form', 'status' => 'new']);
        Lead::create(['name' => 'Other Lead', 'email' => 'other@example.com', 'company' => 'Other Co', 'source' => 'ai_readiness_assessment', 'status' => 'qualified']);

        $this->actingAs(User::factory()->create())->get(route('admin.leads.index', ['search' => 'Needle', 'status' => 'new', 'source' => 'contact_form']))
            ->assertOk()->assertSee('Needle Lead')->assertDontSee('Other Lead');
    }

    public function test_admin_can_view_lead_detail(): void
    {
        $lead = Lead::create(['name' => 'Detail Lead', 'email' => 'detail@example.com', 'company' => 'Detail Co', 'source' => 'contact_form', 'status' => 'new']);
        ContactSubmission::create(['lead_id' => $lead->id, 'name' => 'Detail Lead', 'email' => 'detail@example.com', 'message' => 'Related message']);
        Assessment::create(['lead_id' => $lead->id, 'email' => 'detail@example.com', 'score' => 12, 'result_tier' => 'Emerging', 'summary' => 'Useful foundations.']);

        $this->actingAs(User::factory()->create())->get(route('admin.leads.show', $lead))
            ->assertOk()->assertSee('Detail Lead')->assertSee('Related message')->assertSee('Emerging');
    }

    public function test_admin_can_update_lead_status(): void
    {
        $lead = Lead::create(['name' => 'Status Lead', 'email' => 'status@example.com', 'source' => 'contact_form', 'status' => 'new']);
        $this->actingAs(User::factory()->create())->put(route('admin.leads.update', $lead), ['status' => 'qualified', 'notes' => 'Good fit'])->assertRedirect(route('admin.leads.show', $lead));
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'status' => 'qualified', 'notes' => 'Good fit']);
    }

    public function test_dashboard_displays_lead_metrics(): void
    {
        Lead::create(['name' => 'Recent Lead', 'email' => 'recent@example.com', 'source' => 'contact_form', 'status' => 'new']);
        Lead::create(['name' => 'Qualified Lead', 'email' => 'qualified@example.com', 'source' => 'ai_readiness_assessment', 'status' => 'qualified']);

        $this->actingAs(User::factory()->create())->get('/admin')
            ->assertOk()->assertSee('Total leads')->assertSee('New leads')->assertSee('Recent leads')->assertSee('Recent Lead');
    }
}
