<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentUserRepository implements UserRepository
{
  public function all()
  {
    return User::all();
  }

  public function findById($id)
  {
    return User::findOrFail($id);
  }

  public function create(array $data)
  {
    $data['password'] = bcrypt($data['password']);
    return User::create($data);
  }

  public function update(array $data, $id)
  {
    $user = User::findorFail($id);

    if (isset($data['password'])) {
      $data['password'] = Hash::make($data['password']);
    }

    // $tempuser = $user->only(array_keys())
    // dd($user->only(array_keys($data)));
    // // dd($data);

    if ($user->only(array_keys($data)) == $data) {
      return null;
    }

    $user->update($data);
    return $user;
  }

  public function delete($id)
  {
    return User::destroy($id);
  }

  public function show($id)
  {
    return User::findOrfail($id);
  }
}
