<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Repositories\User\EloquentUserRepository;
use Illuminate\Http\Request;
use App\Repositories\User\UserRepository;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection($this->userRepository->all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRegisterRequest $request): UserResource
    {
        $user = $this->userRepository->create($request->validated());

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            return new UserResource($this->userRepository->findById($id));
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Data not found.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
