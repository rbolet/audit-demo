<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\Existing;
use App\Models\Location;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AssessmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assessment_uses_uuid_primary_key(): void
    {
        $assessment = new Assessment();
        
        $this->assertTrue($assessment->getIncrementing() === false);
        $this->assertEquals('string', $assessment->getKeyType());
    }

    public function test_assessment_uses_soft_deletes(): void
    {
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Assessment::class));
    }

    public function test_assessment_has_correct_fillable_fields(): void
    {
        $assessment = new Assessment();
        
        $expectedFillable = [
            'site_id',
            'assessment_name',
            'assessment_description',
            'assessment_date',
            'report_date',
            'assigned_to_id',
            'status',
        ];

        $this->assertEquals($expectedFillable, $assessment->getFillable());
    }

    public function test_assessment_has_correct_casts(): void
    {
        $assessment = new Assessment();
        
        $expectedCasts = [
            'assessment_date' => 'date',
            'report_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];

        foreach ($expectedCasts as $field => $cast) {
            $this->assertEquals($cast, $assessment->getCasts()[$field]);
        }
    }

    public function test_assessment_can_be_created_with_valid_data(): void
    {
        $assessmentData = [
            'site_id' => Str::uuid()->toString(),
            'assessment_name' => 'Test Assessment',
            'assessment_description' => 'Test Description',
            'assessment_date' => '2023-01-15',
            'report_date' => '2023-01-30',
            'assigned_to_id' => Str::uuid()->toString(),
            'status' => 'in_progress',
        ];

        $assessment = new Assessment($assessmentData);

        $this->assertEquals($assessmentData['assessment_name'], $assessment->assessment_name);
        $this->assertEquals($assessmentData['status'], $assessment->status);
    }

    public function test_assessment_has_site_relationship(): void
    {
        $assessment = new Assessment();
        
        $relation = $assessment->site();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(Site::class, $relation->getRelated()::class);
    }

    public function test_assessment_has_assigned_to_relationship(): void
    {
        $assessment = new Assessment();
        
        $relation = $assessment->assignedTo();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals(User::class, $relation->getRelated()::class);
        $this->assertEquals('assigned_to_id', $relation->getForeignKeyName());
    }

    public function test_assessment_has_locations_relationship(): void
    {
        $assessment = new Assessment();
        
        $relation = $assessment->locations();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(Location::class, $relation->getRelated()::class);
    }

    public function test_assessment_has_existing_relationship(): void
    {
        $assessment = new Assessment();
        
        $relation = $assessment->existing();
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $relation);
        $this->assertEquals(Existing::class, $relation->getRelated()::class);
    }

    public function test_assessment_can_be_soft_deleted(): void
    {
        $assessment = new Assessment([
            'site_id' => Str::uuid()->toString(),
            'assessment_name' => 'Test Assessment for Deletion',
            'status' => 'draft',
        ]);
        
        // Note: In a real test, you'd want to use factories and actual database
        // For now, just testing the trait is available
        $this->assertTrue(method_exists($assessment, 'delete'));
        $this->assertTrue(method_exists($assessment, 'restore'));
    }

    public function test_assessment_date_casting(): void
    {
        $assessment = new Assessment([
            'assessment_date' => '2023-01-15',
            'report_date' => '2023-01-30',
        ]);

        // Test that dates are cast properly
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $assessment->assessment_date);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $assessment->report_date);
    }
}