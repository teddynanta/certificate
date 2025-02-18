<?php

namespace App\Repositories\Certificate;


interface CertificateRepositoryInterface
{
  public function all();
  public function findByCode($code);
  public function create(array $data);
  public function update(array $data, $id);
  public function delete($id);
  public function show($id);
}
