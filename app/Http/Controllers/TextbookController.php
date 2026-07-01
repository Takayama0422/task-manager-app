<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNameRequest;
use App\Http\Requests\UpdateStatusRequest;
use App\Models\ProgressLog;
use App\Models\Textbook;
use App\Services\DashboardService;
use App\Services\TextbookCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TextbookController extends Controller
{
    public function index(DashboardService $dashboardService): View
    {
        return view(
            'textbooks.index',
            $dashboardService->getDashboardData(auth()->id())
        );
    }

    /**
     * 指定した大項目（major_id）の詳細進捗画面を表示する
     */
    public function show(int $major_id, TextbookCategoryService $categoryService): View
    {
        $userId = auth()->id();

        $textbooks = Textbook::where('user_id', $userId)
            ->where('major_id', $major_id)
            ->with(['progressLog' => fn ($query) => $query->where('user_id', $userId)])
            ->orderBy('mid_sort')
            ->orderBy('chapter_no')
            ->get();

        return view('textbooks.show', [
            'textbooks' => $textbooks,
            'currentTitle' => $categoryService->getCategoryName($major_id),
            'majorId' => $major_id,
        ]);
    }

    /**
     * チャプターの進捗（ステータス・フラグ・メモ）を非同期で更新する
     *
     * ルートパラメータ {id} は textbooks テーブルの主キー（教材レコード ID）
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        ProgressLog::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'textbook_id' => $id,
            ],
            [
                'status' => $request->status,
                'is_flagged' => $request->boolean('is_flagged'),
                'memo' => $request->memo,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => '正常に更新されました。',
        ]);
    }

    public function edit(int $major_id, TextbookCategoryService $categoryService): View
    {
        $this->authorize('admin', Textbook::class);

        if (! array_key_exists($major_id, config('textbooks.categories', []))) {
            abort(404);
        }

        return view('textbooks.edit', [
            'majorId' => $major_id,
            'categoryName' => $categoryService->getCategoryName($major_id),
        ]);
    }

    public function updateName(UpdateNameRequest $request, int $major_id, TextbookCategoryService $categoryService): RedirectResponse
    {
        $this->authorize('admin', Textbook::class);

        if (! array_key_exists($major_id, config('textbooks.categories', []))) {
            abort(404);
        }

        $categoryService->updateCategoryName($major_id, $request->category_name);

        return redirect()
            ->route('textbooks.index')
            ->with('success', '教材名を更新しました。');
    }
}
