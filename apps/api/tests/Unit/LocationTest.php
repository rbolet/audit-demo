<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\Existing;
use App\Models\Location;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_uses_uuid_primary_key(): void
    {
        $location = new Location();
        
        $this->assertTrue($location->getIncrementing() === false);
        $this->assertEquals('string', $location->getKeyType());
    }

    public function test_location_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Location::class));
    }

    public function test_location_has_correct_fillable_fields(): void
    {
        $location = new Location();
        
        $expectedFillable = [
            'assessment_id',
            'location_name',
            'location_description',
            'parent_location_id',
        ];

        $this->assertEquals($expectedFillable, $location->getFillable());
    }

    public function test_location_has_correct_casts(): void
    {
        $location = new Location();
        
        $expectedCasts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $location->getCasts()[$field]);
        }
    }

    public function test_location_can_be_created_with_valid_data(): void
    {
        $locationData = [
            'assessment_id' => Str::uuid()->toString(),
            'location_name' => 'First Floor',
            'location_description' => 'Main floor of the building',
            'parent_location_id' => null,
        ];

        $location = new Location($locationData);

        $this->assertEquals($locationData['location_name'], $location->location_name);
        $this->assertEquals($locationData['location_description'], $location->location_description);
        $this->assertEquals($locationData['assessment_id'], $location->assessment_id);
    }

    public function test_location_has_assessment_relationship(): void
    {
        $location = new Location();
        
        $relation = $location->assessment();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Assessment::class, $relation->getRelated()::class);
    }

    public function test_location_has_parent_location_relationship(): void
    {
        $location = new Location();
        
        $relation = $location->parentLocation();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Location::class, $relation->getRelated()::class);
        $this->assertEquals('parent_location_id', $relation->getForeignKeyName());
    }

    public function test_location_has_child_locations_relationship(): void
    {
        $location = new Location();
        
        $relation = $location->childLocations();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(Location::class, $relation->getRelated()::class);
        $this->assertEquals('parent_location_id', $relation->getForeignKeyName());
    }

    public function test_location_has_existing_relationship(): void
    {
        $location = new Location();
        
        $relation = $location->existing();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(Existing::class, $relation->getRelated()::class);
    }

    public function test_location_self_referencing_relationships(): void
    {
        // Test that parent and child relationships reference the same model
        $location = new Location();
        
        $parentRelation = $location->parentLocation();
        $childRelation = $location->childLocations();
        
        $this->assertEquals(
            $parentRelation->getRelated()::class,
            $childRelation->getRelated()::class
        );
        
        $this->assertEquals(Location::class, $parentRelation->getRelated()::class);
        $this->assertEquals(Location::class, $childRelation->getRelated()::class);
    }

    public function test_location_can_be_soft_deleted(): void
    {
        $location = new Location([
            'assessment_id' => Str::uuid()->toString(),
            'location_name' => 'Test Location for Deletion',
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($location, 'delete'));
        $this->assertTrue(method_exists($location, 'restore'));
        $this->assertTrue(method_exists($location, 'trashed'));
    }

    public function test_location_hierarchical_structure(): void
    {
        $location = new Location([
            'assessment_id' => Str::uuid()->toString(),
            'location_name' => 'Room 101',
            'parent_location_id' => Str::uuid()->toString(),
        ]);

        // Test that parent_location_id can be null for root locations
        $rootLocation = new Location([
            'assessment_id' => Str::uuid()->toString(),
            'location_name' => 'Building',
            'parent_location_id' => null,
        ]);

        $this->assertNull($rootLocation->parent_location_id);
        $this->assertNotNull($location->parent_location_id);
    }

    public function test_location_timestamps_casting(): void
    {
        $location = new Location();
        
        // Set some timestamp values
        $location->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $location->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $location->updated_at);
    }
}