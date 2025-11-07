<?php

namespace Tests\Unit;

use App\Models\Valueset;
use App\Models\ValuesetValue;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ValuesetValueTest extends TestCase
{
    use RefreshDatabase;

    public function test_valueset_value_uses_uuid_primary_key(): void
    {
        $valuesetValue = new ValuesetValue();
        
        $this->assertTrue($valuesetValue->getIncrementing() === false);
        $this->assertEquals('string', $valuesetValue->getKeyType());
    }

    public function test_valueset_value_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(ValuesetValue::class));
    }

    public function test_valueset_value_has_correct_fillable_fields(): void
    {
        $valuesetValue = new ValuesetValue();
        
        $expectedFillable = [
            'valueset_id',
            'value_text',
            'value_description',
            'display_order',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $valuesetValue->getFillable());
    }

    public function test_valueset_value_has_correct_casts(): void
    {
        $valuesetValue = new ValuesetValue();
        
        $expectedCasts = [
            'display_order' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $valuesetValue->getCasts()[$field]);
        }
    }

    public function test_valueset_value_can_be_created_with_valid_data(): void
    {
        $data = [
            'valueset_id' => Str::uuid()->toString(),
            'value_text' => 'Wood',
            'value_description' => 'Natural wood material',
            'display_order' => 1,
            'is_active' => true,
        ];

        $valuesetValue = new ValuesetValue($data);

        $this->assertEquals($data['valueset_id'], $valuesetValue->valueset_id);
        $this->assertEquals($data['value_text'], $valuesetValue->value_text);
        $this->assertEquals($data['value_description'], $valuesetValue->value_description);
        $this->assertEquals($data['display_order'], $valuesetValue->display_order);
        $this->assertTrue($valuesetValue->is_active);
    }

    public function test_valueset_value_has_valueset_relationship(): void
    {
        $valuesetValue = new ValuesetValue();
        
        $relation = $valuesetValue->valueset();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Valueset::class, $relation->getRelated()::class);
    }

    public function test_valueset_value_display_order_casting(): void
    {
        $valuesetValue = new ValuesetValue([
            'value_text' => 'Steel',
            'display_order' => '5',  // String that should be cast to integer
        ]);

        $this->assertIsInt($valuesetValue->display_order);
        $this->assertEquals(5, $valuesetValue->display_order);
    }

    public function test_valueset_value_boolean_casting(): void
    {
        $valuesetValue = new ValuesetValue([
            'value_text' => 'Concrete',
            'is_active' => '1',  // String that should be cast to boolean
        ]);

        $this->assertIsBool($valuesetValue->is_active);
        $this->assertTrue($valuesetValue->is_active);

        $valuesetValue->is_active = '0';
        $this->assertIsBool($valuesetValue->is_active);
        $this->assertFalse($valuesetValue->is_active);
    }

    public function test_valueset_value_can_be_soft_deleted(): void
    {
        $valuesetValue = new ValuesetValue([
            'valueset_id' => Str::uuid()->toString(),
            'value_text' => 'Test Value for Deletion',
            'display_order' => 1,
            'is_active' => true,
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($valuesetValue, 'delete'));
        $this->assertTrue(method_exists($valuesetValue, 'restore'));
        $this->assertTrue(method_exists($valuesetValue, 'trashed'));
    }

    public function test_valueset_value_foreign_key_relationship(): void
    {
        $valuesetValue = new ValuesetValue();
        
        $valuesetRelation = $valuesetValue->valueset();
        
        $this->assertEquals('valueset_id', $valuesetRelation->getForeignKeyName());
    }

    public function test_valueset_value_can_have_optional_description(): void
    {
        $valueWithDescription = new ValuesetValue([
            'valueset_id' => Str::uuid()->toString(),
            'value_text' => 'Aluminum',
            'value_description' => 'Lightweight metal material',
            'display_order' => 3,
        ]);

        $valueWithoutDescription = new ValuesetValue([
            'valueset_id' => Str::uuid()->toString(),
            'value_text' => 'Plastic',
            'display_order' => 4,
        ]);

        $this->assertEquals('Lightweight metal material', $valueWithDescription->value_description);
        $this->assertNull($valueWithoutDescription->value_description);
    }

    public function test_valueset_value_ordering(): void
    {
        $firstValue = new ValuesetValue([
            'value_text' => 'First Option',
            'display_order' => 1,
        ]);

        $secondValue = new ValuesetValue([
            'value_text' => 'Second Option',
            'display_order' => 2,
        ]);

        $this->assertLessThan($secondValue->display_order, $firstValue->display_order);
    }

    public function test_valueset_value_timestamps_casting(): void
    {
        $valuesetValue = new ValuesetValue();
        
        // Set some timestamp values
        $valuesetValue->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $valuesetValue->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $valuesetValue->updated_at);
    }

    public function test_valueset_value_inactive_state(): void
    {
        $inactiveValue = new ValuesetValue([
            'valueset_id' => Str::uuid()->toString(),
            'value_text' => 'Deprecated Material',
            'display_order' => 99,
            'is_active' => false,
        ]);

        $this->assertFalse($inactiveValue->is_active);
    }
}