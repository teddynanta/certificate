<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepository
{
  public function all();
  public function findById($id);
  public function create(array $data);
  public function update(array $data, $id);
  public function delete($id);
  public function show($id);
}
