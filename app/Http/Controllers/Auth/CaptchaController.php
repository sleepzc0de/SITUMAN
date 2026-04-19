<?php
// app/Http/Controllers/Auth/CaptchaController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function generate(Request $request)
    {
        // Buat captcha dengan karakter yang mudah dibaca (tanpa 0/O/1/l/I)
        $phraseBuilder = new PhraseBuilder(5, 'abcdefghjkmnpqrstuvwxyz23456789');

        $builder = (new CaptchaBuilder(null, $phraseBuilder))
            ->setBackgroundColor(10, 22, 40)
            ->setTextColor(212, 175, 55)
            ->setMaxAngle(25)
            ->setMaxBehindLines(3)
            ->setMaxFrontLines(3)
            ->build(200, 60);

        // Simpan phrase ke session (pull saat validasi agar sekali pakai)
        $request->session()->put('captcha_phrase', $builder->getPhrase());

        return response($builder->get(), 200, [
            'Content-Type'  => 'image/jpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }
}
