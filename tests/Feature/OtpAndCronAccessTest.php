<?php
/**
 * One-time code and cron URL tests.
 *
 * The OTP endpoints had no rate limit, so the short code space could be walked,
 * codes never expired and could be replayed, and the fee-reminder URL let anyone
 * trigger a message run to every student. These tests hold all three shut.
 */

namespace Tests\Feature;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class OtpAndCronAccessTest extends TestCase
{
    use RefreshDatabase;

    private function user()
    {
        return User::create([
            'firstname' => 'Test', 'lastname' => 'Teacher', 'login' => 'teacher',
            'email' => 'teacher@example.test', 'desc' => '', 'group' => 'Doctor',
            'password' => bcrypt('secret'),
        ]);
    }

    private function code($user, $minutesAgo = 0)
    {
        $c = VerificationCode::create(['user_id' => $user->id, 'status' => 1, 'ip_address' => '127.0.0.1']);
        if ($minutesAgo) {
            $c->created_at = now()->subMinutes($minutesAgo);
            $c->save();
        }
        return $c;
    }

    public function test_code_check_is_rate_limited(): void
    {
        $user = $this->user();
        $c = $this->code($user);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/users/code_check', ['id' => $c->id, 'code' => '0'])->assertRedirect();
        }
        $this->post('/users/code_check', ['id' => $c->id, 'code' => '0'])->assertStatus(429);
    }

    public function test_verified_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/verified', ['code' => '0'])->assertRedirect();
        }
        $this->post('/verified', ['code' => '0'])->assertStatus(429);
    }

    public function test_correct_code_logs_in_once_only(): void
    {
        $user = $this->user();
        $c = $this->code($user);

        $this->post('/users/code_check', ['id' => $c->id, 'code' => $c->code])->assertRedirect('/dashboard');
        $this->assertSame($user->id, Auth::id());

        Auth::logout();
        $this->post('/users/code_check', ['id' => $c->id, 'code' => $c->code])->assertRedirect();
        $this->assertGuest();
    }

    public function test_expired_code_is_rejected(): void
    {
        $user = $this->user();
        $c = $this->code($user, 30);

        $this->post('/users/code_check', ['id' => $c->id, 'code' => $c->code])->assertRedirect();
        $this->assertGuest();
    }

    public function test_unknown_code_id_does_not_error(): void
    {
        $this->post('/users/code_check', ['id' => 999999, 'code' => '12345'])->assertRedirect();
        $this->assertGuest();
    }

    public function test_cron_url_needs_the_token(): void
    {
        $this->get('/cronjob/payment-reminder')->assertNotFound();
        $this->get('/cronjob/payment-reminder?token=guess')->assertNotFound();
    }
}
