<?php

namespace App\Http\Controllers;

use App\Models\Textbook;
use App\Models\ProgressLog;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateStatusRequest;

class TextbookController extends Controller
{
    // ① DashboardService をDI（依存性注入）で受け取る
    public function index(DashboardService $dashboardService)
    {
        return view(
            'textbooks.index',
            $dashboardService->getDashboardData(auth()->id())
        );
    }

    public function show($major_id)
    {
        $userId = auth()->id();

        // ⑤ user_id 条件を追加（セキュリティ修正）
        $textbooks = Textbook::where('user_id', $userId)
            ->where('major_id', $major_id)
            ->with('progressLog')
            ->orderBy('mid_sort')
            ->orderBy('chapter_no')
            ->get();

        // ② display_title はモデルのアクセサで自動生成されるため不要
        // ③ withDefault により progressLog が null になることはないため if 不要

        return view('textbooks.show', [
            'textbooks'    => $textbooks,
            'currentTitle' => config('textbooks.categories')[$major_id] ?? 'カスタム教材',
            'majorId'      => $major_id,
        ]);
    }

    public function updateStatus(UpdateStatusRequest $request, $id)
    {
        $userId = auth()->id();

        ProgressLog::updateOrCreate(
            [
                'user_id'     => $userId,
                'textbook_id' => $id,
            ],
            [
                'status'     => $request->status,
                'is_flagged' => filter_var($request->is_flagged, FILTER_VALIDATE_BOOLEAN) || $request->is_flagged == 1,
                'memo'       => $request->memo,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => '正常に更新されました。',
        ]);
    }

    public function edit($major_id)
    {
        $this->authorize('admin', Textbook::class);

        // ④ config から取得
        $categoryName = config('textbooks.categories')[$major_id] ?? abort(404);

        return view('textbooks.edit', [
            'majorId'      => $major_id,
            'categoryName' => $categoryName,
        ]);
    }

    public function updateName(Request $request, $major_id)
    {
        $this->authorize('admin', Textbook::class);

        return redirect()->route('textbooks.index')->with('success', '教材名を更新しました（認可成功）');
    }
}