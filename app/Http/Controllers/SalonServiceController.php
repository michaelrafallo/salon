<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Services\Salon\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class SalonServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService
    ) {}

    public function store(StoreServiceRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated = $this->processImageUpload($validated);

            $service = $this->serviceService->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully.',
                'data' => $this->serviceToApiShape($service),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        try {
            $validated = $request->validated();
            $validated = $this->processImageUpload($validated);

            $service = $this->serviceService->update($service, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully.',
                'data' => $this->serviceToApiShape($service),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function processImageUpload(array $data): array
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('services', 'public');
        }

        return $data;
    }

    public function destroy(Request $request, Service $service): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $this->serviceService->delete($service);

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceToApiShape(Service $service): array
    {
        $service->load('categories');

        return [
            'id' => $service->id,
            'name' => $service->name,
            'description' => $service->description,
            'price' => (float) $service->price,
            'active' => $service->active,
            'image' => $service->image,
            'image_url' => $service->image ? asset('storage/'.$service->image) : null,
            'categories' => $service->categories->pluck('slug')->values()->all(),
        ];
    }
}
