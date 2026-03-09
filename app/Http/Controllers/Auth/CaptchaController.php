<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function generate(Request $request)
    {
        $phraseBuilder = new PhraseBuilder(5, 'abcdefghjkmnpqrstuvwxyz23456789');
        $builder = new CaptchaBuilder(null, $phraseBuilder);

        $builder
            ->setBackgroundColor(10, 22, 40)        // Sesuai warna dark theme login
            ->setTextColor(212, 175, 55)             // Warna gold SiTUMAN
            ->setMaxAngle(25)
            ->setMaxBehindLines(3)
            ->setMaxFrontLines(3)
            ->build(200, 60);                        // Width x Height

        // Simpan phrase ke session
        $request->session()->put('captcha_phrase', $builder->getPhrase());

        // Return sebagai base64 image (tidak perlu simpan file)
        $imageData = $builder->get();

        return response($imageData, 200, [
            'Content-Type'  => 'image/jpeg',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }
}
