<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\Existing;
use App\Models\ExistingAttributeValue;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExistingAttributeValueTest extends TestCase
{
    use RefreshDatabase;

    public function test_existing_attribute_value_uses_uuid_primary_key(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        $this->assertTrue($existingAttributeValue->getIncrementing() === false);
        $this->assertEquals('string', $existingAttributeValue->getKeyType());
    }

    public function test_existing_attribute_value_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(ExistingAttributeValue::class));
    }

    public function test_existing_attribute_value_has_correct_fillable_fields(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        $expectedFillable = [
            'existing_id',
            'attribute_id',
            'attribute_value',
        ];

        $this->assertEquals($expectedFillable, $existingAttributeValue->getFillable());
    }

    public function test_existing_attribute_value_has_correct_casts(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        $expectedCasts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $existingAttributeValue->getCasts()[$field]);
        }
    }

    public function test_existing_attribute_value_can_be_created_with_valid_data(): void
    {
        $data = [
            'existing_id' => Str::uuid()->toString(),
            'attribute_id' => Str::uuid()->toString(),
            'attribute_value' => 'Wood',
        ];

        $existingAttributeValue = new ExistingAttributeValue($data);

        $this->assertEquals($data['existing_id'], $existingAttributeValue->existing_id);
        $this->assertEquals($data['attribute_id'], $existingAttributeValue->attribute_id);
        $this->assertEquals($data['attribute_value'], $existingAttributeValue->attribute_value);
    }

    public function test_existing_attribute_value_has_existing_relationship(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        $relation = $existingAttributeValue->existing();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Existing::class, $relation->getRelated()::class);
    }

    public function test_existing_attribute_value_has_attribute_relationship(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        $relation = $existingAttributeValue->attribute();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Attribute::class, $relation->getRelated()::class);
    }

    public function test_existing_attribute_value_can_be_soft_deleted(): void
    {
        $existingAttributeValue = new ExistingAttributeValue([
            'existing_id' => Str::uuid()->toString(),
            'attribute_id' => Str::uuid()->toString(),
            'attribute_value' => 'Test Value for Deletion',
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($existingAttributeValue, 'delete'));
        $this->assertTrue(method_exists($existingAttributeValue, 'restore'));
        $this->assertTrue(method_exists($existingAttributeValue, 'trashed'));
    }

    public function test_existing_attribute_value_foreign_key_relationships(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        $existingRelation = $existingAttributeValue->existing();
        $attributeRelation = $existingAttributeValue->attribute();
        
        $this->assertEquals('existing_id', $existingRelation->getForeignKeyName());
        $this->assertEquals('attribute_id', $attributeRelation->getForeignKeyName());
    }

    public function test_existing_attribute_value_can_store_various_attribute_values(): void
    {
        // Test different types of attribute values that might be stored
        $textValue = new ExistingAttributeValue([
            'existing_id' => Str::uuid()->toString(),
            'attribute_id' => Str::uuid()->toString(),
            'attribute_value' => 'Wooden Oak',
        ]);

        $numericValue = new ExistingAttributeValue([
            'existing_id' => Str::uuid()->toString(),
            'attribute_id' => Str::uuid()->toString(),
            'attribute_value' => '150.5',
        ]);

        $booleanValue = new ExistingAttributeValue([
            'existing_id' => Str::uuid()->toString(),
            'attribute_id' => Str::uuid()->toString(),
            'attribute_value' => 'true',
        ]);

        $this->assertEquals('Wooden Oak', $textValue->attribute_value);
        $this->assertEquals('150.5', $numericValue->attribute_value);
        $this->assertEquals('true', $booleanValue->attribute_value);
    }

    public function test_existing_attribute_value_timestamps_casting(): void
    {
        $existingAttributeValue = new ExistingAttributeValue();
        
        // Set some timestamp values
        $existingAttributeValue->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $existingAttributeValue->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $existingAttributeValue->updated_at);
    }
}