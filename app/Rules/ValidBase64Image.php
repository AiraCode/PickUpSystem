<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBase64Image implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^data:image\/(\w+);base64,/', $value)) {
            $fail('Format ' . $attribute . ' harus berupa string Base64 gambar yang valid.');
            return;
        }

        $data = base64_decode(substr($value, strpos($value, ',') + 1));
        if ($data === false) {
            $fail('Dekode Base64 gagal.');
            return;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data);

        if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
            $fail('File ' . $attribute . ' terdeteksi bukan berupa berkas gambar valid.');
            return;
        }

        if (@imagecreatefromstring($data) === false) {
            $fail('File ' . $attribute . ' corrupt atau mengandung payload berbahaya.');
        }
    }
}
