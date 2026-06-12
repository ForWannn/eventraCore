<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordCode;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_code_can_be_requested()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'ichwan.r7@gmail.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'ichwan.r7@gmail.com',
        ]);

        $response->assertRedirect(route('password.reset'));
        
        $this->assertNotNull(Cache::get('password_reset_code_ichwan.r7@gmail.com'));
        
        Mail::assertSent(ResetPasswordCode::class, function ($mail) {
            return $mail->hasTo('ichwan.r7@gmail.com');
        });
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        $response = $this->get('/reset-password?email=ichwan.r7@gmail.com');

        $response->assertStatus(200);
    }

    public function test_password_can_be_reset_with_valid_code()
    {
        $user = User::factory()->create([
            'email' => 'ichwan.r7@gmail.com',
            'password' => Hash::make('old-password'),
        ]);

        $code = '123456';
        Cache::put('password_reset_code_ichwan.r7@gmail.com', $code, now()->addMinutes(5));

        $response = $this->post('/reset-password', [
            'email' => 'ichwan.r7@gmail.com',
            'code' => $code,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
        
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
        $this->assertNull(Cache::get('password_reset_code_ichwan.r7@gmail.com'));
    }

    public function test_password_cannot_be_reset_with_invalid_code()
    {
        $user = User::factory()->create([
            'email' => 'ichwan.r7@gmail.com',
            'password' => Hash::make('old-password'),
        ]);

        Cache::put('password_reset_code_ichwan.r7@gmail.com', '123456', now()->addMinutes(5));

        $response = $this->post('/reset-password', [
            'email' => 'ichwan.r7@gmail.com',
            'code' => '654321', 
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertFalse(Hash::check('new-password', $user->refresh()->password));
    }

    public function test_password_reset_code_can_be_resent()
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'ichwan.r7@gmail.com',
        ]);

        $response = $this->post('/resend-password-code', [
            'email' => 'ichwan.r7@gmail.com',
        ]);

        $response->assertRedirect(route('password.reset'));
        $response->assertSessionHas('status');
        $response->assertSessionHas('email', 'ichwan.r7@gmail.com');
        
        $this->assertNotNull(Cache::get('password_reset_code_ichwan.r7@gmail.com'));
        $this->assertNotNull(session('code_sent_at'));
        
        Mail::assertSent(ResetPasswordCode::class, function ($mail) {
            return $mail->hasTo('ichwan.r7@gmail.com');
        });
    }

    public function test_password_reset_code_cannot_be_resent_for_non_existent_email()
    {
        $response = $this->post('/resend-password-code', [
            'email' => 'nonexistent@gmail.com',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
