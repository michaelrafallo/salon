<?php

namespace App\Services\Salon;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ServiceService
{
    /**
     * @param  array{name: string, description?: string|null, price: float|int, active?: bool, image?: string|null, categories?: array<string>}  $data
     */
    public function create(array $data): Service
    {
        return DB::transaction(function () use ($data) {
            $service = Service::query()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'active' => filter_var($data['active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'image' => $data['image'] ?? null,
                'color' => $data['color'] ?? null,
            ]);
            $this->syncCategoriesBySlug($service, $data['categories'] ?? []);

            return $service->fresh('categories');
        });
    }

    /**
     * @param  array{name?: string, description?: string|null, price?: float|int, active?: bool, image?: string|null, categories?: array<string>}  $data
     */
    public function update(Service $service, array $data): Service
    {
        return DB::transaction(function () use ($service, $data) {
            $attrs = [];
            if (array_key_exists('name', $data)) {
                $attrs['name'] = $data['name'];
            }
            if (array_key_exists('description', $data)) {
                $attrs['description'] = $data['description'];
            }
            if (array_key_exists('price', $data)) {
                $attrs['price'] = $data['price'];
            }
            if (array_key_exists('active', $data)) {
                $attrs['active'] = filter_var($data['active'], FILTER_VALIDATE_BOOLEAN);
            }
            if (array_key_exists('image', $data)) {
                if ($service->image) {
                    Storage::disk('public')->delete($service->image);
                }
                $attrs['image'] = $data['image'];
            }
            if (array_key_exists('color', $data)) {
                $attrs['color'] = $data['color'] ?: null;
            }
            if ($attrs !== []) {
                $service->update($attrs);
            }
            if (array_key_exists('categories', $data)) {
                $this->syncCategoriesBySlug($service, $data['categories']);
            }

            return $service->fresh('categories');
        });
    }

    public function delete(Service $service): bool
    {
        return DB::transaction(function () use ($service) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $service->categories()->detach();

            return (bool) $service->delete();
        });
    }

    /**
     * @param  array<string>  $slugs
     */
    protected function syncCategoriesBySlug(Service $service, array $slugs): void
    {
        $ids = ServiceCategory::query()
            ->whereIn('slug', $slugs)
            ->pluck('id');
        $service->categories()->sync($ids);
    }
}
