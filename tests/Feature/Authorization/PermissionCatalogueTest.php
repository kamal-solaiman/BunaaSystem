<?php

declare(strict_types=1);

namespace Tests\Feature\Authorization;

use App\Features\Authorization\Support\PermissionMatrix;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * The seeded catalogue must match the frozen registry exactly.
 *
 * `PERMISSION_REGISTRY.md` froze 215 permission names and scopes. These tests
 * fail if a name, a scope, or the count ever drifts.
 */
final class PermissionCatalogueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed', ['--force' => true]);
    }

    #[Test]
    public function the_five_confirmed_roles_are_seeded(): void
    {
        // 07 §2: "Version 1 has exactly five roles."
        $this->assertSame(5, Role::count());

        $this->assertSame(
            ['parent', 'student', 'super_admin', 'teacher', 'teacher_staff'],
            Role::query()->orderBy('role_name')->pluck('role_name')->all(),
        );
    }

    #[Test]
    public function every_role_carries_its_confirmed_scope(): void
    {
        $expected = [
            'super_admin' => 'platform',
            'teacher' => 'teacher_workspace',
            'teacher_staff' => 'teacher_workspace',
            'student' => 'student_account',
            'parent' => 'parent_linked_students',
        ];

        foreach ($expected as $name => $scope) {
            $this->assertSame($scope, Role::where('role_name', $name)->value('role_scope'));
        }
    }

    #[Test]
    public function all_215_permissions_are_seeded(): void
    {
        $this->assertSame(215, Permission::count());
    }

    #[Test]
    public function the_scope_distribution_matches_the_registry(): void
    {
        $expected = [
            'platform' => 60,
            'teacher_workspace' => 92,
            'student_account' => 34,
            'parent_linked_students' => 29,
        ];

        foreach ($expected as $scope => $count) {
            $this->assertSame(
                $count,
                Permission::where('permission_scope', $scope)->count(),
                "Scope {$scope} must hold exactly {$count} permissions."
            );
        }
    }

    #[Test]
    public function seeding_is_idempotent(): void
    {
        Artisan::call('db:seed', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);

        $this->assertSame(215, Permission::count());
        $this->assertSame(5, Role::count());
    }

    #[Test]
    public function the_matrix_and_the_catalogue_agree(): void
    {
        $seeded = Permission::pluck('permission_name')->sort()->values()->all();
        $matrix = collect(PermissionMatrix::allPermissions())->sort()->values()->all();

        $this->assertSame($matrix, $seeded, 'The seeded catalogue must equal the matrix exactly.');
    }

    #[Test]
    public function permissions_denied_to_every_role_are_still_registered(): void
    {
        // Their presence is what makes a refusal explicit and auditable.
        foreach ([
            'teacher_workspace.teacher_staff.self_assign_permission',
            'platform.user.create_platform_staff',
        ] as $permission) {
            $this->assertTrue(
                Permission::where('permission_name', $permission)->exists(),
                "{$permission} must exist so the refusal is explicit."
            );

            foreach (['super_admin', 'teacher', 'student', 'parent'] as $role) {
                $this->assertNull(
                    PermissionMatrix::grant($role, $permission),
                    "{$permission} must be denied to {$role}."
                );
            }
        }
    }

    #[Test]
    public function permission_names_use_canonical_terminology(): void
    {
        foreach (Permission::pluck('permission_name') as $name) {
            foreach (['class', 'course', 'tenant', 'delete'] as $banned) {
                $this->assertDoesNotMatchRegularExpression(
                    '/\b'.$banned.'\b/',
                    $name,
                    "Non-canonical term in {$name}."
                );
            }
        }
    }

    #[Test]
    public function teacher_staff_holds_no_matrix_row_of_its_own(): void
    {
        // 09 §5: Teacher Staff is not a matrix column; access is derived.
        $this->assertSame([], PermissionMatrix::forRole('teacher_staff'));
    }
}
