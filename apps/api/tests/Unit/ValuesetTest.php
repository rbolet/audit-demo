<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\Valueset;
use App\Models\ValuesetValue;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValuesetTest extends TestCase
{
    use RefreshDatabase;

    public function test_valueset_uses_uuid_primary_key(): void
    {
        $valueset = new Valueset();
        
        $this->assertTrue($valueset->getIncrementing() === false);
        $this->assertEquals('string', $valueset->getKeyType());
    }

    public function test_valueset_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Valueset::class));
    }

    public function test_valueset_has_correct_fillable_fields(): void
    {
        $valueset = new Valueset();
        
        $expectedFillable = [
            'valueset_name',
            'valueset_description',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $valueset->getFillable());
    }

    public function test_valueset_has_correct_casts(): void
    {
        $valueset = new Valueset();
        
        $expectedCasts = [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $valueset->getCasts()[$field]);
        }
    }

    public function test_valueset_can_be_created_with_valid_data(): void
    {
        $valuesetData = [
            'valueset_name' => 'Material Types',
            'valueset_description' => 'Common building material types',
            'is_active' => true,
        ];

        $valueset = new Valueset($valuesetData);

        $this->assertEquals($valuesetData['valueset_name'], $valueset->valueset_name);
        $this->assertEquals($valuesetData['valueset_description'], $valueset->valueset_description);
        $this->assertTrue($valueset->is_active);
    }

    public function test_valueset_has_values_relationship(): void
    {
        $valueset = new Valueset();
        
        $relation = $valueset->values();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(ValuesetValue::class, $relation->getRelated()::class);
    }

    public function test_valueset_has_attributes_relationship(): void
    {
        $valueset = new Valueset();
        
        $relation = $valueset->attributes();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
        $this->assertEquals(Attribute::class, $relation->getRelated()::class);
        $this->assertEquals('attribute_valuesets', $relation->getTable());
    }

    public function test_valueset_boolean_casting(): void
    {
        $valueset = new Valueset([
            'valueset_name' => 'Test Valueset',
            'is_active' => '1',  // String that should be cast to boolean
        ]);

        $this->assertIsBool($valueset->is_active);
        $this->assertTrue($valueset->is_active);

        $valueset->is_active = '0';
        $this->assertIsBool($valueset->is_active);
        $this->assertFalse($valueset->is_active);
    }

    public function test_valueset_can_be_soft_deleted(): void
    {
        $valueset = new Valueset([
            'valueset_name' => 'Test Valueset for Deletion',
            'is_active' => true,
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($valueset, 'delete'));
        $this->assertTrue(method_exists($valueset, 'restore'));
        $this->assertTrue(method_exists($valueset, 'trashed'));
    }

    public function test_valueset_relationships_foreign_keys(): void
    {
        $valueset = new Valueset();
        
        $valuesRelation = $valueset->values();
        
        $this->assertEquals('valueset_id', $valuesRelation->getForeignKeyName());
    }

    public function test_valueset_many_to_many_with_attributes(): void
    {
        $valueset = new Valueset();
        
        $attributesRelation = $valueset->attributes();
        
        // Test that the many-to-many relationship is properly configured
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $attributesRelation);
        $this->assertEquals('attribute_valuesets', $attributesRelation->getTable());
        $this->assertEquals('valueset_id', $attributesRelation->getForeignPivotKeyName());
        $this->assertEquals('attribute_id', $attributesRelation->getRelatedPivotKeyName());
    }

    public function test_valueset_timestamps_casting(): void
    {
        $valueset = new Valueset();
        
        // Set some timestamp values
        $valueset->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $valueset->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $valueset->updated_at);
    }

    public function test_valueset_inactive_state(): void
    {
        $inactiveValueset = new Valueset([
            'valueset_name' => 'Deprecated Materials',
            'valueset_description' => 'Old material types no longer used',
            'is_active' => false,
        ]);

        $this->assertFalse($inactiveValueset->is_active);
    }
}