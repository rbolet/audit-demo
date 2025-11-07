<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\ExistingAttributeValue;
use App\Models\Type;
use App\Models\Valueset;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use RefreshDatabase;

    public function test_attribute_uses_uuid_primary_key(): void
    {
        $attribute = new Attribute();
        
        $this->assertTrue($attribute->getIncrementing() === false);
        $this->assertEquals('string', $attribute->getKeyType());
    }

    public function test_attribute_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Attribute::class));
    }

    public function test_attribute_has_correct_fillable_fields(): void
    {
        $attribute = new Attribute();
        
        $expectedFillable = [
            'attribute_name',
            'attribute_description',
            'attribute_type',
            'is_required',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $attribute->getFillable());
    }

    public function test_attribute_has_correct_casts(): void
    {
        $attribute = new Attribute();
        
        $expectedCasts = [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $attribute->getCasts()[$field]);
        }
    }

    public function test_attribute_can_be_created_with_valid_data(): void
    {
        $attributeData = [
            'attribute_name' => 'Material',
            'attribute_description' => 'The material composition',
            'attribute_type' => 'select',
            'is_required' => true,
            'is_active' => true,
        ];

        $attribute = new Attribute($attributeData);

        $this->assertEquals($attributeData['attribute_name'], $attribute->attribute_name);
        $this->assertEquals($attributeData['attribute_type'], $attribute->attribute_type);
        $this->assertTrue($attribute->is_required);
        $this->assertTrue($attribute->is_active);
    }

    public function test_attribute_has_types_relationship(): void
    {
        $attribute = new Attribute();
        
        $relation = $attribute->types();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
        $this->assertEquals(Type::class, $relation->getRelated()::class);
        $this->assertEquals('type_attributes', $relation->getTable());
    }

    public function test_attribute_types_relationship_has_pivot_fields(): void
    {
        $attribute = new Attribute();
        
        $relation = $attribute->types();
        
        $expectedPivotColumns = ['display_order', 'is_required'];
        
        foreach ($expectedPivotColumns as $column) {
            $this->assertContains($column, $relation->getPivotColumns());
        }
    }

    public function test_attribute_has_valuesets_relationship(): void
    {
        $attribute = new Attribute();
        
        $relation = $attribute->valuesets();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
        $this->assertEquals(Valueset::class, $relation->getRelated()::class);
        $this->assertEquals('attribute_valuesets', $relation->getTable());
    }

    public function test_attribute_has_existing_attribute_values_relationship(): void
    {
        $attribute = new Attribute();
        
        $relation = $attribute->existingAttributeValues();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(ExistingAttributeValue::class, $relation->getRelated()::class);
    }

    public function test_attribute_boolean_casting(): void
    {
        $attribute = new Attribute([
            'attribute_name' => 'Test Attribute',
            'is_required' => '1',  // String that should be cast to boolean
            'is_active' => '0',    // String that should be cast to boolean
        ]);

        $this->assertIsBool($attribute->is_required);
        $this->assertTrue($attribute->is_required);
        
        $this->assertIsBool($attribute->is_active);
        $this->assertFalse($attribute->is_active);
    }

    public function test_attribute_can_be_soft_deleted(): void
    {
        $attribute = new Attribute([
            'attribute_name' => 'Test Attribute for Deletion',
            'attribute_type' => 'text',
            'is_active' => true,
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($attribute, 'delete'));
        $this->assertTrue(method_exists($attribute, 'restore'));
        $this->assertTrue(method_exists($attribute, 'trashed'));
    }

    public function test_attribute_timestamps_casting(): void
    {
        $attribute = new Attribute();
        
        // Set some timestamp values
        $attribute->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $attribute->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $attribute->updated_at);
    }
}