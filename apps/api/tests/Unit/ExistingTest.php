<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\Existing;
use App\Models\ExistingAttributeValue;
use App\Models\Location;
use App\Models\Type;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExistingTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_uses_uuid_primary_key(): void
    {
        $existing = new Existing();
        
        $this->assertTrue($existing->getIncrementing() === false);
        $this->assertEquals('string', $existing->getKeyType());
    }

    public function test_existing_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Existing::class));
    }

    public function test_existing_has_correct_table_name(): void
    {
        $existing = new Existing();
        
        $this->assertEquals('existing', $existing->getTable());
    }

    public function test_existing_has_correct_fillable_fields(): void
    {
        $existing = new Existing();
        
        $expectedFillable = [
            'assessment_id',
            'type_id',
            'location_id',
            'existing_name',
            'existing_description',
            'quantity',
            'unit',
        ];

        $this->assertEquals($expectedFillable, $existing->getFillable());
    }

    public function test_existing_has_correct_casts(): void
    {
        $existing = new Existing();
        
        $expectedCasts = [
            'quantity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $existing->getCasts()[$field]);
        }
    }

    public function test_existing_can_be_created_with_valid_data(): void
    {
        $existingData = [
            'assessment_id' => Str::uuid()->toString(),
            'type_id' => Str::uuid()->toString(),
            'location_id' => Str::uuid()->toString(),
            'existing_name' => 'Main Entrance Door',
            'existing_description' => 'Primary entrance door made of wood',
            'quantity' => 1,
            'unit' => 'each',
        ];

        $existing = new Existing($existingData);

        $this->assertEquals($existingData['existing_name'], $existing->existing_name);
        $this->assertEquals($existingData['existing_description'], $existing->existing_description);
        $this->assertEquals($existingData['quantity'], $existing->quantity);
        $this->assertEquals($existingData['unit'], $existing->unit);
    }

    public function test_existing_has_assessment_relationship(): void
    {
        $existing = new Existing();
        
        $relation = $existing->assessment();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Assessment::class, $relation->getRelated()::class);
    }

    public function test_existing_has_type_relationship(): void
    {
        $existing = new Existing();
        
        $relation = $existing->type();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Type::class, $relation->getRelated()::class);
    }

    public function test_existing_has_location_relationship(): void
    {
        $existing = new Existing();
        
        $relation = $existing->location();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Location::class, $relation->getRelated()::class);
    }

    public function test_existing_has_attribute_values_relationship(): void
    {
        $existing = new Existing();
        
        $relation = $existing->attributeValues();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(ExistingAttributeValue::class, $relation->getRelated()::class);
    }

    public function test_existing_quantity_casting(): void
    {
        $existing = new Existing([
            'existing_name' => 'Test Door',
            'quantity' => '5',  // String that should be cast to integer
        ]);

        $this->assertIsInt($existing->quantity);
        $this->assertEquals(5, $existing->quantity);
    }

    public function test_existing_can_be_soft_deleted(): void
    {
        $existing = new Existing([
            'assessment_id' => Str::uuid()->toString(),
            'type_id' => Str::uuid()->toString(),
            'location_id' => Str::uuid()->toString(),
            'existing_name' => 'Test Item for Deletion',
            'quantity' => 1,
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($existing, 'delete'));
        $this->assertTrue(method_exists($existing, 'restore'));
        $this->assertTrue(method_exists($existing, 'trashed'));
    }

    public function test_existing_all_relationships_defined(): void
    {
        $existing = new Existing();
        
        // Test that all foreign key relationships are properly defined
        $assessmentRelation = $existing->assessment();
        $typeRelation = $existing->type();
        $locationRelation = $existing->location();
        $attributeValuesRelation = $existing->attributeValues();
        
        $this->assertEquals('assessment_id', $assessmentRelation->getForeignKeyName());
        $this->assertEquals('type_id', $typeRelation->getForeignKeyName());
        $this->assertEquals('location_id', $locationRelation->getForeignKeyName());
        $this->assertEquals('existing_id', $attributeValuesRelation->getForeignKeyName());
    }

    public function test_existing_timestamps_casting(): void
    {
        $existing = new Existing();
        
        // Set some timestamp values
        $existing->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $existing->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $existing->updated_at);
    }
}