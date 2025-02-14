<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\UserRepository;

class EloquentUserRepository implements UserRepository
{
  public function all()
  {
    return User::all();
  }

  public function findbyId($id)
  {
    return User::find($id);
  }

  public function create(array $data)
  {
    $data['password'] = bcrypt($data['password']);
    return User::create($data);
  }

  public function update(array $data, $id)
  {
    $user = User::find($id);
    return $user->update($data);
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
