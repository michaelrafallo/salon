<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalonServiceCategoryController extends Controller
{
    public function store(StoreServiceCategoryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated['slug'] = Str::slug($validated['name']);

            $category = DB::transaction(function () use ($validated) {
                return ServiceCategory::query()->create($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => $this->categoryToApiShape($category),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory): JsonResponse
    {
        try {
            $validated = $request->validated();
            if (isset($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            DB::transaction(function () use ($serviceCategory, $validated) {
                $serviceCategory->update($validated);
            });

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => $this->categoryToApiShape($serviceCategory->fresh()),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, ServiceCategory $serviceCategory): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        DB::transaction(function () use ($serviceCategory) {
            $serviceCategory->services()->detach();
            $serviceCategory->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryToApiShape(ServiceCategory $category): array
    {
        $category->loadCount('services');

        return [
            'id' => $category->id,
            'slug' => $category->slug,
            'name' => $category->name,
            'services_count' => (int) $category->services_count,
            'created_at' => $category->created_at?->toISOString(),
        ];
    }
}
