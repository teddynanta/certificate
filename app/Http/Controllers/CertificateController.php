<?php

namespace App\Http\Controllers;

use App\Http\Requests\CertificateStoreRequest;
use App\Http\Requests\CertificateUpdateRequest;
use App\Http\Resources\CertificateResource;
use App\Repositories\Certificate\CertificateRepositoryInterface;
use Exception;
// use Illuminate\Http\Client\Request;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{

    protected $certificateRepository;

    public function __construct(CertificateRepositoryInterface $certificateRepository)
    {
        $this->certificateRepository = $certificateRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certificate = $this->certificateRepository->all();

        if (!$certificate) {
            return response()->json([
                'error' => 'No certificates available.'
            ], 404);
        }

        return CertificateResource::collection($certificate);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CertificateStoreRequest $request): CertificateResource
    {
        $certificate = $request->validated();

        return new CertificateResource($this->certificateRepository->create($certificate));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): CertificateResource
    {
        return new CertificateResource($this->certificateRepository->show($id));
    }

    public function verify(Request $request)
    {
        $certificate = $this->certificateRepository->findByCode($request->code);

        if (!$certificate) {
            return response()->json([
                'error' => 'No certificates found.'
            ], 404);
        }
        return new CertificateResource($certificate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CertificateUpdateRequest $request, string $id)
    {
        $certificate = $this->certificateRepository->update($request->validated(), $id);

        if (!$certificate) {
            return response()->json([
                'message' => 'No Changes Detected.'
            ], 422);
        }

        return new CertificateResource($certificate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $certificate = $this->certificateRepository->delete($id);

        if (!$certificate) {
            return response()->json([
                'error' => 'Certificate not found.'
            ], 404);
        }

        return response()->json([
            'message' => 'Certificate successfully deleted.'
        ], 200);
    }
}
