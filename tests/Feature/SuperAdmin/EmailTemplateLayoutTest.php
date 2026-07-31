<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EmailTemplateLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_template_index_uses_isolated_iframe_for_preview_modal(): void
    {
        Role::create(['name' => 'Super Admin']);

        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('Super Admin');

        EmailTemplate::create([
            'name' => 'Welcome Email',
            'slug' => 'welcome-email',
            'subject' => 'Welcome {{company_name}}',
            'body' => '<style>body{background:red}</style><div class="wrapper">Hello</div>',
            'variables' => ['company_name'],
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)
            ->get(route('superadmin.email-templates.index'));

        $response->assertOk();
        $response->assertSee('sandbox="allow-same-origin"', false);
        $response->assertSee('Email preview for Welcome Email', false);
    }
}
