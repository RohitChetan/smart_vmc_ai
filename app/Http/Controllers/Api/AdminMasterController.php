<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\IncidentAssignment;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class AdminMasterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    public function departments(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Department::query()
                ->withCount('categories')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeDepartment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                'unique:departments,code',
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $department = Department::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department created successfully.',
            'data' => $department,
        ], 201);
    }

    public function updateDepartment(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('departments', 'code')->ignore($department->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $department->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully.',
            'data' => $department->fresh(),
        ]);
    }

    public function destroyDepartment(Department $department): JsonResponse
    {
        if ($department->categories()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This department has categories. Disable it instead of deleting it.',
            ], 422);
        }

        if ($department->incidents()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This department is already used by incidents. Disable it instead of deleting it.',
            ], 422);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    public function categories(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Category::query()
                ->with('department:id,name,code')
                ->withCount('incidents')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                'unique:categories,slug',
            ],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'department_id' => $validated['department_id'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category->load('department:id,name,code'),
        ], 201);
    }

    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'department_id' => $validated['department_id'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $category->fresh()->load('department:id,name,code'),
        ]);
    }

    public function destroyCategory(Category $category): JsonResponse
    {
        if ($category->incidents()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This category is already used by incidents. Disable it instead of deleting it.',
            ], 422);
        }

        if ($category->complaints()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This category is already used by complaints. Disable it instead of deleting it.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Wards
    |--------------------------------------------------------------------------
    */

    public function wards(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Ward::query()
                ->withCount('users')
                ->withCount('incidents')
                ->orderBy('ward_no')
                ->get(),
        ]);
    }

    public function storeWard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ward_no' => [
                'required',
                'integer',
                'min:1',
                'unique:wards,ward_no',
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $ward = Ward::create([
            'ward_no' => $validated['ward_no'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ward created successfully.',
            'data' => $ward,
        ], 201);
    }

    public function updateWard(Request $request, Ward $ward): JsonResponse
    {
        $validated = $request->validate([
            'ward_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('wards', 'ward_no')->ignore($ward->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_active' => ['required', 'boolean'],
        ]);

        $ward->update([
            'ward_no' => $validated['ward_no'],
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ward updated successfully.',
            'data' => $ward->fresh(),
        ]);
    }

    public function destroyWard(Ward $ward): JsonResponse
    {
        if ($ward->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This ward has users assigned. Disable it instead of deleting it.',
            ], 422);
        }

        if ($ward->incidents()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This ward has incidents. Disable it instead of deleting it.',
            ], 422);
        }

        $ward->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ward deleted successfully.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    public function users(Request $request): JsonResponse
    {
        $query = User::query()
            ->with('ward:id,ward_no,name')
            ->withCount('incidentAssignments');

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('ward_id')) {
            $query->where('ward_id', $request->integer('ward_id'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function storeUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in([
                    'citizen',
                    'ward_officer',
                    'admin',
                ]),
            ],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (
            $validated['role'] === 'ward_officer'
            && empty($validated['ward_id'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Ward is required for a Ward Officer.',
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'ward_id' => $validated['ward_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user->fresh()->load('ward:id,ward_no,name'),
        ], 201);
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => [
                'required',
                Rule::in([
                    'citizen',
                    'ward_officer',
                    'admin',
                ]),
            ],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        if (
            $validated['role'] === 'ward_officer'
            && empty($validated['ward_id'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Ward is required for a Ward Officer.',
            ], 422);
        }

        /*
         * Do not allow an admin to deactivate their own account.
         */
        if (
            $user->id === $request->user()->id
            && !$validated['is_active']
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account.',
            ], 422);
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'ward_id' => $validated['ward_id'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user->fresh()->load('ward:id,ward_no,name'),
        ]);
    }

    public function destroyUser(Request $request, User $user): JsonResponse
    {
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        if ($user->incidentAssignments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This user has incident assignments. Disable the account instead of deleting it.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Master Overview
    |--------------------------------------------------------------------------
    */

    public function overview(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'departments' => Department::count(),
                'active_departments' => Department::where('is_active', true)->count(),

                'categories' => Category::count(),
                'active_categories' => Category::where('is_active', true)->count(),

                'wards' => Ward::count(),
                'active_wards' => Ward::where('is_active', true)->count(),

                'users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),

                'admins' => User::where('role', 'admin')->count(),
                'ward_officers' => User::where('role', 'ward_officer')->count(),
                'citizens' => User::where('role', 'citizen')->count(),
            ],
        ]);
    }
}