<?php

namespace App\Http\Controllers\Api;

use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if ($request->filled('id')) {
            return $this->show((int) $request->query('id'));
        }

        $userId = $request->integer('user_id') ?: $request->user()->id;
        $skills = Skill::where('user_id', $userId)
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->query('search').'%';
                $query->where(fn ($builder) => $builder
                    ->where('name', 'like', $search)
                    ->orWhere('category', 'like', $search));
            })
            ->latest('id')
            ->get();

        return $this->success('Data skill berhasil diambil', $skills);
    }

    public function show(int $id): JsonResponse
    {
        $skill = Skill::find($id);

        return $skill
            ? $this->success('Detail skill berhasil diambil', $skill)
            : $this->fail('Skill tidak ditemukan', 404);
    }

    public function store(Request $request): JsonResponse
    {
        $limit = $this->enforcePlanLimit($request->user()->load('plan'), Skill::class, 'max_skills', 'skill');

        if ($limit) {
            return $limit;
        }

        $data = $this->validated($request, [
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:100'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $skill = Skill::create($data + ['user_id' => $request->user()->id]);

        return $this->success('Skill berhasil ditambahkan', $skill, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $skill = Skill::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $skill) {
            return $this->fail('Skill tidak ditemukan atau bukan milik Anda', 404);
        }

        $data = $this->validated($request, [
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:100'],
            'level' => ['required', 'in:beginner,intermediate,advanced'],
        ]);

        if ($data instanceof JsonResponse) {
            return $data;
        }

        $skill->update($data);

        return $this->success('Skill berhasil diperbarui', $skill);
    }

    public function updateFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->update($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $skill = Skill::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (! $skill) {
            return $this->fail('Skill tidak ditemukan atau bukan milik Anda', 404);
        }

        $skill->delete();

        return $this->success('Skill berhasil dihapus', ['id' => $id]);
    }

    public function destroyFromQuery(Request $request): JsonResponse
    {
        $id = $request->integer('id');

        return $id
            ? $this->destroy($request, $id)
            : $this->fail('Parameter id wajib dikirim', 400);
    }
}
