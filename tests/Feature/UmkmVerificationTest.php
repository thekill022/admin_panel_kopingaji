<?php

namespace Tests\Feature;

use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_toggle_umkm_verification()
    {
        $admin = User::factory()->create(['role' => 'SUPERADMIN']);
        $umkm = Umkm::factory()->create(['is_verified' => false]);

        $this->actingAs($admin)
            ->patch(route('umkms.toggleVerify', $umkm));

        $this->assertTrue($umkm->fresh()->is_verified);

        $this->actingAs($admin)
            ->patch(route('umkms.toggleVerify', $umkm));

        $this->assertFalse($umkm->fresh()->is_verified);
    }
}
