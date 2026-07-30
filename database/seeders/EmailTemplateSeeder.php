<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── Welcome email sent when a new tenant is provisioned ──────────
            [
                'name'      => 'Welcome — New Tenant',
                'slug'      => 'welcome-tenant',
                'subject'   => 'Welcome to {{app_name}}, {{company_name}}!',
                'body'      => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: linear-gradient(135deg, #1a1d27, #2e3250); color: #fff; padding: 32px 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 24px; }
  .header p  { margin: 6px 0 0; color: #8b92b8; font-size: 14px; }
  .body { padding: 30px; color: #374151; line-height: 1.7; }
  .body h2 { color: #1a1a2e; margin-top: 0; }
  .btn { display: inline-block; margin: 20px 0; padding: 12px 28px; background: #4f6ef7; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; }
  .footer { background: #f9fafb; padding: 18px 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>{{app_name}}</h1>
    <p>Cloud POS &amp; Inventory Management</p>
  </div>
  <div class="body">
    <h2>Welcome, {{owner_name}}! 🎉</h2>
    <p>Your business <strong>{{company_name}}</strong> has been successfully registered on <strong>{{app_name}}</strong>.</p>
    <p>Your account is now active and ready to use. You can log in to your dashboard and start managing your inventory, sales, and more.</p>
    <p style="text-align:center;">
      <a href="{{login_url}}" class="btn">Go to Dashboard →</a>
    </p>
    <p>If you have any questions or need help getting started, our support team is here for you.</p>
    <p>Best regards,<br><strong>The {{app_name}} Team</strong></p>
  </div>
  <div class="footer">
    Need help? Contact us at <a href="mailto:{{support_email}}" style="color:#4f6ef7;">{{support_email}}</a><br>
    &copy; {{ date('Y') }} {{app_name}}. All rights reserved.
  </div>
</div>
</body>
</html>
HTML,
                'variables' => ['app_name', 'company_name', 'owner_name', 'login_url', 'support_email'],
                'is_active' => true,
            ],

            // ── Password reset notification ──────────────────────────────────
            [
                'name'      => 'Password Reset',
                'slug'      => 'password-reset',
                'subject'   => 'Reset Your {{app_name}} Password',
                'body'      => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: #1a1d27; color: #fff; padding: 28px 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 22px; }
  .body { padding: 30px; color: #374151; line-height: 1.7; }
  .btn { display: inline-block; margin: 20px 0; padding: 12px 28px; background: #ef4444; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 15px; }
  .footer { background: #f9fafb; padding: 18px 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header"><h1>Password Reset Request</h1></div>
  <div class="body">
    <p>Hi <strong>{{user_name}}</strong>,</p>
    <p>We received a request to reset the password for your <strong>{{app_name}}</strong> account.</p>
    <p style="text-align:center;">
      <a href="{{reset_url}}" class="btn">Reset My Password</a>
    </p>
    <p>This link will expire in <strong>{{expires_in}}</strong>. If you did not request a password reset, please ignore this email.</p>
    <p>Best regards,<br><strong>The {{app_name}} Team</strong></p>
  </div>
  <div class="footer">&copy; {{ date('Y') }} {{app_name}}. All rights reserved.</div>
</div>
</body>
</html>
HTML,
                'variables' => ['app_name', 'user_name', 'reset_url', 'expires_in'],
                'is_active' => true,
            ],

            // ── Subscription confirmation ────────────────────────────────────
            [
                'name'      => 'Subscription Confirmed',
                'slug'      => 'subscription-confirmed',
                'subject'   => '✅ Subscription Confirmed — {{plan_name}}',
                'body'      => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: linear-gradient(135deg, #059669, #10b981); color: #fff; padding: 28px 30px; text-align: center; }
  .header h1 { margin: 0; font-size: 22px; }
  .body { padding: 30px; color: #374151; line-height: 1.7; }
  .info-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 16px 20px; margin: 16px 0; }
  .info-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 14px; }
  .footer { background: #f9fafb; padding: 18px 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header"><h1>✅ Subscription Confirmed</h1></div>
  <div class="body">
    <p>Hi <strong>{{company_name}}</strong>,</p>
    <p>Your subscription has been confirmed. Here are the details:</p>
    <div class="info-box">
      <div class="info-row"><span>Plan</span><strong>{{plan_name}}</strong></div>
      <div class="info-row"><span>Amount Paid</span><strong>{{amount}}</strong></div>
      <div class="info-row"><span>Valid Until</span><strong>{{expires_at}}</strong></div>
      <div class="info-row"><span>Invoice No</span><strong>{{invoice_number}}</strong></div>
    </div>
    <p>Thank you for your continued trust in <strong>{{app_name}}</strong>.</p>
    <p>Best regards,<br><strong>The {{app_name}} Team</strong></p>
  </div>
  <div class="footer">&copy; {{ date('Y') }} {{app_name}}. All rights reserved.</div>
</div>
</body>
</html>
HTML,
                'variables' => ['app_name', 'company_name', 'plan_name', 'amount', 'expires_at', 'invoice_number'],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }

        $this->command->info('✅ EmailTemplateSeeder: ' . count($templates) . ' templates seeded.');
    }
}
