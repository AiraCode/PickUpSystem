<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    /**
     * Allowed private folders for KTP/identity images.
     * Only files within these folders can be streamed.
     */
    private const ALLOWED_PREFIXES = ['ktp/', 'accu_ktp/'];

    /**
     * Stream a private KTP/identity file to the authenticated admin.
     *
     * Route: GET /api/admin/secure-file/{path}
     * Middleware: auth:sanctum  (defined in routes/api.php)
     *
     * @param  Request  $request
     * @param  string   $path  Relative path within the private disk, e.g. "ktp/6a94e9d82aeb6.jpeg"
     */
    public function stream(Request $request, string $path): StreamedResponse|\Illuminate\Http\JsonResponse
    {
        // Normalise & prevent path traversal
        $path = ltrim($path, '/');
        $realPath = realpath(storage_path('app/private/' . $path));
        $basePath  = realpath(storage_path('app/private'));

        if (
            $realPath === false ||
            $basePath === false ||
            !str_starts_with($realPath, $basePath . DIRECTORY_SEPARATOR)
        ) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // Only allow files inside approved sub-folders
        $allowed = false;
        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return response()->json(['message' => 'Folder tidak diizinkan'], 403);
        }

        // Check file exists on the local (private) disk
        if (!Storage::disk('local')->exists($path)) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }

        $mimeType = Storage::disk('local')->mimeType($path) ?: 'application/octet-stream';

        return response()->stream(function () use ($path) {
            $stream = Storage::disk('local')->readStream($path);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'inline',
            'Cache-Control'       => 'private, no-store, no-cache, must-revalidate',
            'Pragma'              => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
