<?php

namespace App\Repositories\Certificate;

use Illuminate\Http\Request;


interface CertificateRepositoryInterface
{
  public function all();
  public function findByCode(Request $request);
  public function create(array $data);
  public function update(array $data, $id);
  public function delete($id);
  public function show($id);
}
