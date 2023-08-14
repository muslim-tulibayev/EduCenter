<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api,student,parent', ["only" => ['show']]);
        $this->middleware('auth:api', ["only" => ['index', 'store', 'update', 'destroy']]);
    }

    public function index()
    {
        $certificates = Certificate::orderByDesc('id')->paginate();

        return response()->json($certificates);
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'student_id' => 'required|numeric|exists:students,id',
            'file' => 'required|mimes:jpg, jpeg, png, pdf|max:2048'
        ]);

        if ($validator->fails())
            return response()->json($validator->messages());

        $path = $req->file('file')->store('certificates');

        $newCertificate = Certificate::create([
            'student_id' => $req->student_id,
            'url' => $path
        ]);

        return response()->json([
            "message" => "Certificate stored successfully",
            "id" => $newCertificate->id
        ]);
    }

    public function show(string $id)
    {
        // $certificate = Certificate::with('student')->find($id);

        // if (!$certificate)
        //     return response()->json(["error" => "Not found"]);

        // return response()->json([
        //     "certificate" => $certificate,
        // ]);
    }

    public function update(Request $req, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        $certificate = Certificate::with('student')->find($id);

        if (!$certificate)
            return response()->json(["error" => "Not found"]);

        if (!Storage::exists($certificate->url))
            return response()->json(["error" => "File not found"]);

        Storage::delete($certificate->url);
        $certificate->delete();

        return response()->json([
            "message" => "Certificate has been deleted successfully.",
            "certificate_id" => $id
        ]);
    }
}
