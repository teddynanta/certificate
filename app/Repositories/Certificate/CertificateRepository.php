<?php

namespace App\Repositories\Certificate;

use App\Models\User;
use App\Models\Recipient;
use App\Models\Certificate;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CertificateVerification;

class CertificateRepository implements CertificateRepositoryInterface
{
  public function all()
  {
    $certificate = Certificate::all();

    if ($certificate->isEmpty()) {
      return null;
    }

    return $certificate;
  }

  public function findByCode(Request $request)
  {
    $code = $request->code;
    $ip = $request->ip();
    $userAgent = $request->userAgent();
    $certificate = Certificate::where('code', $code)->first();

    if (!$certificate) {
      return response()->json([
        'error' => 'Not Found.'
      ], 404);
    }

    CertificateVerification::create([
      'certificate_id' => $certificate->id,
      'verified_at' => date(now()),
      'ip_address' => $ip,
      'user_agent' => $userAgent
    ]);

    return $certificate;
  }

  public function show($id)
  {
    return Certificate::findOrFail($id);
  }

  public function create(array $data)
  {
    //create recipient if not exists
    $recipient = Recipient::firstOrCreate([
      'name' => $data['name'],
      'email' => $data['email']
    ]);

    return Certificate::create([
      'recipient_id' => $recipient->id,
      'code' => $data['code'],
      'issued_date' => $data['issued_date'],
      'created_by' => Auth::id()
    ]);
  }

  public function update(array $data, $id)
  {
    $certificate = Certificate::findOrFail($id);

    if ($certificate->only(array_keys($data)) == $data) {
      return null;
    }

    $certificate->update($data);

    return $certificate;
  }

  public function delete($id)
  {
    return Certificate::destroy($id);
  }
}
