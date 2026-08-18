<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\CouponScope;
use App\Enums\DiscountType;
use App\Models\Coupon;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CouponModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $seller;
    private Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach(\App\Models\Role::query()->where('slug', 'admin')->first());

        $this->seller = User::factory()->create();
        $sellerRole = \App\Models\Role::query()->where('slug', 'seller')->first();
        $this->seller->roles()->attach($sellerRole);
        $this->seller->sellerProfile()->create([
            'referral_code' => 'SELLER1',
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ]);

        $this->package = Package::create([
            'name' => 'Test Package',
            'slug' => 'test-package',
            'short_description' => 'Test description',
            'price' => 4400,
            'features' => json_encode(['Feature 1']),
            'active' => true,
        ]);
    }

    public function test_admin_can_create_global_percentage_coupon(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'WEB10',
            'name' => 'Cupón web 10%',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'scope' => 'global',
            'is_active' => true,
        ]);

        $response->assertRedirect();

        $coupon = Coupon::query()->where('code', 'WEB10')->first();
        $this->assertNotNull($coupon);
        $this->assertEquals('WEB10', $coupon->code);
        $this->assertEquals(DiscountType::Percentage, $coupon->discount_type);
        $this->assertEquals(10, $coupon->discount_value);
        $this->assertEquals(CouponScope::Global, $coupon->scope);
    }

    public function test_coupon_code_is_case_insensitive_unique(): void
    {
        Coupon::create([
            'code' => 'PROMO10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'promo10',
            'name' => 'Duplicado',
            'discount_type' => 'percentage',
            'discount_value' => 5,
            'scope' => 'global',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_coupon_code_is_normalized_to_uppercase(): void
    {
        $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'carlos10',
            'name' => 'Cupón Carlos',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'scope' => 'global',
            'is_active' => true,
        ]);

        $coupon = Coupon::query()->first();
        $this->assertNotNull($coupon);
        $this->assertEquals('CARLOS10', $coupon->code);
    }

    public function test_coupon_service_calculates_percentage_discount(): void
    {
        $coupon = Coupon::create([
            'code' => 'TEST10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => true,
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 4400);

        $this->assertTrue($result->valid, 'Errors: ' . implode(', ', $result->errors));
        $this->assertEquals(4400, $result->subtotal);
        $this->assertEquals(440, $result->discount);
        $this->assertEquals(3960, $result->total);
    }

    public function test_coupon_service_calculates_fixed_discount(): void
    {
        $coupon = Coupon::create([
            'code' => 'FIX500',
            'name' => 'Test',
            'discount_type' => DiscountType::Fixed,
            'discount_value' => 500,
            'scope' => CouponScope::Global,
            'is_active' => true,
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 2700);

        $this->assertTrue($result->valid, 'Errors: ' . implode(', ', $result->errors));
        $this->assertEquals(500, $result->discount);
        $this->assertEquals(2200, $result->total);
    }

    public function test_fixed_discount_never_goes_negative(): void
    {
        $coupon = Coupon::create([
            'code' => 'FIX999',
            'name' => 'Test',
            'discount_type' => DiscountType::Fixed,
            'discount_value' => 500,
            'scope' => CouponScope::Global,
            'is_active' => true,
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 300);

        $this->assertTrue($result->valid, 'Errors: ' . implode(', ', $result->errors));
        $this->assertEquals(300, $result->discount);
        $this->assertEquals(0, $result->total);
    }

    public function test_percentage_discount_respects_maximum_discount(): void
    {
        $coupon = Coupon::create([
            'code' => 'MAX10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'maximum_discount' => 1000,
            'scope' => CouponScope::Global,
            'is_active' => true,
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 20000);

        $this->assertTrue($result->valid, 'Errors: ' . implode(', ', $result->errors));
        $this->assertEquals(1000, $result->discount);
        $this->assertEquals(19000, $result->total);
    }

    public function test_package_scoped_coupon_rejects_other_package(): void
    {
        $otherPackage = Package::create([
            'name' => 'Other Package',
            'slug' => 'other-package',
            'short_description' => 'Other description',
            'price' => 2700,
            'features' => json_encode(['Feature 1']),
            'active' => true,
        ]);

        $coupon = Coupon::create([
            'code' => 'PKG10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Packages,
        ]);
        $coupon->packages()->attach($this->package->id);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $otherPackage, 4400);

        $this->assertFalse($result->valid);
        $this->assertContains('El cupón no aplica a este paquete.', $result->errors);
    }

    public function test_expired_coupon_is_invalid(): void
    {
        $coupon = Coupon::create([
            'code' => 'EXP10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'expires_at' => now()->subDay(),
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 4400);

        $this->assertFalse($result->valid);
        $this->assertContains('El cupón ha expirado.', $result->errors);
    }

    public function test_inactive_coupon_is_invalid(): void
    {
        $coupon = Coupon::create([
            'code' => 'INACT',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => false,
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 4400);

        $this->assertFalse($result->valid);
        $this->assertContains('El cupón no está activo.', $result->errors);
    }

    public function test_coupon_can_be_assigned_to_seller(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'CARLOS10',
            'name' => 'Cupón Carlos',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'scope' => 'global',
            'seller_id' => $this->seller->id,
        ]);

        $response->assertRedirect();

        $coupon = Coupon::query()->where('code', 'CARLOS10')->first();
        $this->assertNotNull($coupon);
        $this->assertEquals($this->seller->id, $coupon->seller_id);
    }

    public function test_admin_cannot_assign_coupon_to_non_seller(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'BAD10',
            'name' => 'Test',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'scope' => 'global',
            'seller_id' => $this->admin->id,
        ]);

        $response->assertSessionHasErrors('seller_id');
    }

    public function test_seller_can_view_own_coupons(): void
    {
        Coupon::create([
            'code' => 'MYCUPON',
            'name' => 'Mi cupón',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'seller_id' => $this->seller->id,
        ]);

        $response = $this->actingAs($this->seller)->get(route('seller.coupons.index'));

        $response->assertOk();
        $response->assertSee('MYCUPON');
    }

    public function test_seller_cannot_view_other_seller_coupons(): void
    {
        $otherSeller = User::factory()->create();
        $otherSeller->roles()->attach(\App\Models\Role::query()->where('slug', 'seller')->first());
        $otherSeller->sellerProfile()->create([
            'referral_code' => 'OTHER1',
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ]);

        Coupon::create([
            'code' => 'OTHER20',
            'name' => 'Otro cupón',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 20,
            'scope' => CouponScope::Global,
            'seller_id' => $otherSeller->id,
        ]);

        $response = $this->actingAs($this->seller)->get(route('seller.coupons.index'));

        $response->assertOk();
        $response->assertDontSee('OTHER20');
    }

    public function test_admin_can_toggle_coupon_status(): void
    {
        $coupon = Coupon::create([
            'code' => 'TOGGLE',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.coupons.toggle', $coupon));

        $response->assertRedirect();
        $coupon->refresh();
        $this->assertFalse($coupon->is_active);
    }

    public function test_admin_can_create_coupon_with_specific_packages(): void
    {
        $package2 = Package::create([
            'name' => 'Package 2',
            'slug' => 'package-2',
            'short_description' => 'Package 2 description',
            'price' => 3000,
            'features' => json_encode(['Feature 1']),
            'active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.coupons.store'), [
            'code' => 'PRO500',
            'name' => 'Cupón profesional',
            'discount_type' => 'fixed',
            'discount_value' => 500,
            'scope' => 'packages',
            'package_ids' => [$this->package->id, $package2->id],
        ]);

        $response->assertRedirect();

        $coupon = Coupon::query()->where('code', 'PRO500')->first();
        $this->assertNotNull($coupon);
        $this->assertEquals(2, $coupon->packages()->count());
    }

    public function test_coupon_service_returns_seller_id(): void
    {
        $coupon = Coupon::create([
            'code' => 'SELL10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'seller_id' => $this->seller->id,
            'is_active' => true,
        ]);

        $service = new \App\Services\CouponService();
        $result = $service->evaluate($coupon, $this->package, 4400);

        $this->assertTrue($result->valid, 'Errors: ' . implode(', ', $result->errors));
        $this->assertEquals($this->seller->id, $result->seller_id);
    }

    public function test_admin_can_access_coupons_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.coupons.index'));

        $response->assertOk();
        $response->assertSee('Cupones');
    }

    public function test_admin_can_access_coupon_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.coupons.create'));

        $response->assertOk();
        $response->assertSee('Nuevo cupón');
    }

    public function test_admin_can_view_coupon_detail(): void
    {
        $coupon = Coupon::create([
            'code' => 'VIEW10',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.coupons.show', $coupon));

        $response->assertOk();
        $response->assertSee('VIEW10');
    }

    public function test_coupon_status_computation(): void
    {
        $active = Coupon::create([
            'code' => 'ACTIVE',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => true,
        ]);
        $this->assertEquals('active', $active->computeStatus());

        $inactive = Coupon::create([
            'code' => 'INACTIVE',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => false,
        ]);
        $this->assertEquals('inactive', $inactive->computeStatus());

        $scheduled = Coupon::create([
            'code' => 'SCHEDULED',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => true,
            'starts_at' => now()->addDay(),
        ]);
        $this->assertEquals('scheduled', $scheduled->computeStatus());

        $expired = Coupon::create([
            'code' => 'EXPIRED',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);
        $this->assertEquals('expired', $expired->computeStatus());

        $exhausted = Coupon::create([
            'code' => 'EXHAUSTED',
            'name' => 'Test',
            'discount_type' => DiscountType::Percentage,
            'discount_value' => 10,
            'scope' => CouponScope::Global,
            'is_active' => true,
            'usage_limit' => 5,
            'times_used' => 5,
        ]);
        $this->assertEquals('exhausted', $exhausted->computeStatus());
    }
}
