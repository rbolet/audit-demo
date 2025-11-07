<?php

namespace Tests\Unit;

use App\Models\Attribute;
use App\Models\Existing;
use App\Models\Type;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_type_uses_uuid_primary_key(): void
    {
        $type = new Type();
        
        $this->assertTrue($type->getIncrementing() === false);
        $this->assertEquals('string', $type->getKeyType());
    }

    public function test_type_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Type::class));
    }

    public function test_type_has_correct_fillable_fields(): void
    {
        $type = new Type();
        
        $expectedFillable = [
            'type_name',
            'type_description',
            'type_category',
            'is_active',
        ];

        $this->assertEquals($expectedFillable, $type->getFillable());
    }

    public function test_type_has_correct_casts(): void
    {
        $type = new Type();
        
        $expectedCasts = [
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $type->getCasts()[$field]);
        }
    }

    public function test_type_can_be_created_with_valid_data(): void
    {
        $typeData = [
            'type_name' => 'Door',
            'type_description' => 'Various types of doors',
            'type_category' => 'Openings',
            'is_active' => true,
        ];

        $type = new Type($typeData);

        $this->assertEquals($typeData['type_name'], $type->type_name);
        $this->assertEquals($typeData['type_category'], $type->type_category);
        $this->assertTrue($type->is_active);
    }

    public function test_type_has_attributes_relationship(): void
    {
        $type = new Type();
        
        $relation = $type->attributes();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
        $this->assertEquals(Attribute::class, $relation->getRelated()::class);
        $this->assertEquals('type_attributes', $relation->getTable());
    }

    public function test_type_attributes_relationship_has_pivot_fields(): void
    {
        $type = new Type();
        
        $relation = $type->attributes();
        
        $expectedPivotColumns = ['display_order', 'is_required'];
        
        foreach ($expectedPivotColumns as $column) {
            $this->assertContains($column, $relation->getPivotColumns());
        }
    }

    public function test_type_has_existing_relationship(): void
    {
        $type = new Type();
        
        $relation = $type->existing();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(Existing::class, $relation->getRelated()::class);
    }

    public function test_type_can_be_soft_deleted(): void
    {
        $type = new Type([
            'type_name' => 'Test Type for Deletion',
            'type_category' => 'Test',
            'is_active' => true,
        ]);
        
        // Test that soft delete methods are available
        $this->assertTrue(method_exists($type, 'delete'));
        $this->assertTrue(method_exists($type, 'restore'));
        $this->assertTrue(method_exists($type, 'trashed'));
    }

    public function test_type_boolean_casting(): void
    {
        $type = new Type([
            'type_name' => 'Test Type',
            'is_active' => '1',  // String that should be cast to boolean
        ]);

        $this->assertIsBool($type->is_active);
        $this->assertTrue($type->is_active);

        $type->is_active = '0';
        $this->assertIsBool($type->is_active);
        $this->assertFalse($type->is_active);
    }

    public function test_type_timestamps_casting(): void
    {
        $type = new Type();
        
        // Set some timestamp values
        $type->setRawAttributes([
            'created_at' => '2023-01-15 10:30:00',
            'updated_at' => '2023-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $type->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $type->updated_at);
    }
}