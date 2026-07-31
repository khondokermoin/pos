<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * EmailTemplateService
 *
 * Central service for sending emails using stored EmailTemplate records.
 * Falls back to a plain-text email if no template is found for the given slug.
 *
 * Usage:
 *   app(EmailTemplateService::class)->send('welcome-tenant', $recipient, $data);
 */
class EmailTemplateService
{
    /**
     * Send an email using a stored template identified by slug.
     *
     * @param  string       $slug         The template slug (e.g. 'welcome-tenant')
     * @param  string       $toEmail      Recipient email address
     * @param  array        $data         Key-value pairs for {{variable}} substitution
     * @param  string|null  $toName       Optional recipient display name
     * @param  array        $attachments  Optional array of file paths to attach
     * @return bool  true on success, false on failure
     */
    public function send(
        string $slug,
        string $toEmail,
        array $data = [],
        ?string $toName = null,
        array $attachments = []
    ): bool {
        try {
            $template = EmailTemplate::findBySlug($slug);

            if (! $template) {
                Log::warning("EmailTemplateService: No active template found for slug '{$slug}'. Email not sent to {$toEmail}.");
                return false;
            }

            $subject = $template->renderSubject($data);
            $body    = $template->render($data);

            Mail::send([], [], function ($message) use ($toEmail, $toName, $subject, $body, $attachments) {
                $message->to($toEmail, $toName ?? $toEmail)
                    ->subject($subject)
                    ->html($body);

                foreach ($attachments as $path) {
                    if (file_exists($path)) {
                        $message->attach($path);
                    }
                }
            });

            return true;
        } catch (\Throwable $e) {
            Log::error("EmailTemplateService: Failed to send '{$slug}' to {$toEmail}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Queue an email using a stored template (non-blocking).
     * Uses Laravel's queue system — requires a queue worker.
     */
    public function queue(
        string $slug,
        string $toEmail,
        array $data = [],
        ?string $toName = null
    ): bool {
        try {
            $template = EmailTemplate::findBySlug($slug);

            if (! $template) {
                Log::warning("EmailTemplateService: No active template found for slug '{$slug}'. Queued email skipped.");
                return false;
            }

            $subject = $template->renderSubject($data);
            $body    = $template->render($data);

            Mail::queue([], [], function ($message) use ($toEmail, $toName, $subject, $body) {
                $message->to($toEmail, $toName ?? $toEmail)
                    ->subject($subject)
                    ->html($body);
            });

            return true;
        } catch (\Throwable $e) {
            Log::error("EmailTemplateService: Failed to queue '{$slug}' to {$toEmail}. Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Preview a template with sample data (returns rendered HTML string).
     * Used by the Super Admin preview endpoint.
     *
     * NOTE: Unlike send()/queue(), this intentionally bypasses the is_active
     * check so that inactive templates can still be previewed in the admin UI.
     */
    public function preview(string $slug, array $sampleData = []): ?string
    {
        // Use a direct query instead of findBySlug() so inactive templates
        // can also be previewed (findBySlug requires is_active = true).
        $template = EmailTemplate::where('slug', $slug)->first();

        if (! $template) {
            return null;
        }

        return $template->render($sampleData);
    }
}
