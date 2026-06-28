<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

class ExchangeTypeController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success('Jenis exchange berhasil diambil', [
            ['key' => 'skill', 'name' => 'Skill', 'description' => 'Pertukaran kemampuan teknis atau non-teknis'],
            ['key' => 'time', 'name' => 'Waktu', 'description' => 'Pertukaran bantuan berdasarkan durasi'],
            ['key' => 'experience', 'name' => 'Pengalaman', 'description' => 'Berbagi pengalaman nyata'],
            ['key' => 'mentoring', 'name' => 'Mentoring', 'description' => 'Bimbingan terstruktur'],
            ['key' => 'project', 'name' => 'Bantuan Project', 'description' => 'Bantuan tugas atau project tertentu'],
            ['key' => 'collaboration', 'name' => 'Kolaborasi Kerja', 'description' => 'Kerja sama membuat project'],
        ]);
    }
}
