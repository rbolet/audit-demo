<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\Site;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_uses_uuid_primary_key(): void
    {
        $site = new Site();
        
        $this->assertTrue($site->getIncrementing() === false);
        $this->assertEquals('string', $site->getKeyType());
    }

    public function test_site_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Site::class));
    }

    public function test_site_has_correct_fillable_fields(): void
    {
        $site = new Site();
        
        $expectedFillable = [
            'site_name',
            'site_address',
            'site_address_2',
            'site_city',
            'site_state',
            'site_postal_code',
            'site_contact_name',
            'site_contact_phone',
            'site_contact_email',
        ];

        $this->assertEquals($expectedFillable, $site->getFillable());
    }

    public function test_site_has_correct_casts(): void
    {
        $site = new Site();
        
        $expectedCasts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $site->getCasts()[$field]);
        }
    }

    public function test_site_can_be_created_with_valid_data(): void
    {
        $siteData = [
            'site_name' => 'Test Site',
            'site_address' => '123 Main St',
            'site_city' => 'Test City',
            'site_state' => 'TS',
            'site_postal_code' => '12345',
            'site_contact_name' => 'John Doe',
            'site_contact_phone' => '555-1234',
            'site_contact_email' => 'john@example.com',
        ];

        $site = Site::create($siteData);

        $this->assertInstanceOf(Site::class, $site);
        $this->assertTrue(Str::isUuid($site->id));
        $this->assertEquals($siteData['site_name'], $site->site_name);
        $this->assertEquals($siteData['site_contact_email'], $site->site_contact_email);
    }

    public function test_site_can_be_soft_deleted(): void
    {
        $site = new Site([
            'site_name' => 'Test Site for Deletion',
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($site, 'delete'));
        $this->assertTrue(method_exists($site, 'restore'));
        $this->assertTrue(method_exists($site, 'trashed'));
    }

    public function test_site_has_assessments_relationship(): void
    {
        $site = new Site();
        
        $relation = $site->assessments();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(Assessment::class, $relation->getRelated()::class);
    }

    public function test_site_assessments_relationship_works(): void
    {
        $site = new Site();
        
        // Test the relationship collection type
        $assessments = $site->assessments;
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $assessments);
    }
}