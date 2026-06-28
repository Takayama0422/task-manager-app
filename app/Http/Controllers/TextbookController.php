<?php

namespace App\Http\Controllers;

use App\Models\Textbook;
use App\Models\ProgressLog;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateStatusRequest;

class TextbookController extends Controller
{
    /**
     * ダッシュボード（教材一覧）を表示する
     * 集計処理は DashboardService に委譲し、Controller は薄く保つ
     */
    public function index(DashboardService $dashboardService)
    {
        return view(
            'textbooks.index',
            $dashboardService->getDashboardData(auth()->id())
        );
    }

    /**
     * 指定した大項目（major_id）の詳細進捗画面を表示する
     * user_id で絞り込むことで、他ユーザーのデータへのアクセスを防ぐ（① ⑤ 対応）
     */
    public function show(int $major_id)
    {
        $userId = auth()->id();

        $textbooks = Textbook::where('user_id', $userId)
            ->where('major_id', $major_id)
            ->with('progressLog')
            ->orderBy('mid_sort')
            ->orderBy('chapter_no')
            ->get();

        return view('textbooks.show', [
            'textbooks'    => $textbooks,
            'currentTitle' => config('textbook.categories')[$major_id] ?? 'カスタム教材',
            'majorId'      => $major_id,
        ]);
    }

    /**
     * チャプターの進捗（ステータス・フラグ・メモ）を非同期で更新する
     * user_id は auth() から取得し、リクエストパラメーターには依存しない（① 対応）
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $userId = auth()->id();

        ProgressLog::updateOrCreate(
            [
                'user_id'     => $userId,
                'textbook_id' => $id,
            ],
            [
                'status'     => $request->status,
                'is_flagged' => filter_var($request->is_flagged, FILTER_VALIDATE_BOOLEAN)
                    || $request->is_flagged == 1,
                'memo'       => $request->memo,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => '正常に更新されました。',
        ]);
    }

    /**
     * 教材名の編集画面を表示する（管理者のみ）
     */
    public function edit(int $major_id)
    {
        $this->authorize('admin', Textbook::class);

        $categoryName = config('textbooks.categories')[$major_id] ?? abort(404);

        return view('textbooks.edit', [
            'majorId'      => $major_id,
            'categoryName' => $categoryName,
        ]);
    }

    /**
     * 教材名を更新する（管理者のみ）
     * 現在は認可の疎通確認のみ。本実装時はDB更新処理を追加する。
     */
    public function updateName(Request $request, int $major_id)
    {
        $this->authorize('admin', Textbook::class);

        return redirect()
            ->route('textbooks.index')
            ->with('success', '教材名を更新しました。');
    }
}