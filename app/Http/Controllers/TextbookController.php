<?php

namespace App\Http\Controllers;

use App\Models\Textbook;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateStatusRequest;
use App\Http\Requests\UpdateNameRequest;

class TextbookController extends Controller
{
    private $majorCategoryMap = [
        1  => '学習準備',
        2  => '開発環境セットアップ',
        3  => 'コマンドライン入門',
        4  => 'Git入門',
        5  => 'HTML & CSS入門',
        6  => 'Docker入門',
        7  => 'PHP入門',
        8  => 'データベース & SQL入門',
        9  => 'Laravel基礎',
        10 => 'Laravel実践',
        11 => 'Git × GitHub実践',
        12 => 'Laravel × API',
        13 => '総合アプリケーション開発',
    ];

    public function index()
    {
        $userId = auth()->id(); // ✅ ハードコードから認証ユーザーに変更

        $allTextbooks = Textbook::where('user_id', $userId)->with('progressLog')->get();

        $totalCount = $allTextbooks->count();
        $completedCount = $allTextbooks->filter(function ($textbook) {
            return $textbook->progressLog && $textbook->progressLog->status == 2;
        })->count();
        $progressRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        $textbooksGrouped = $allTextbooks->groupBy('major_id')->map(function ($group) {
            $groupTotal = $group->count();
            $groupCompleted = $group->filter(function ($textbook) {
                return $textbook->progressLog && $textbook->progressLog->status == 2;
            })->count();
            $group->progress_rate = $groupTotal > 0 ? round(($groupCompleted / $groupTotal) * 100) : 0;
            return $group;
        });

        $flaggedChapters = $allTextbooks->filter(function ($textbook) {
            return $textbook->progressLog && $textbook->progressLog->is_flagged;
        });

        return view('textbooks.index', [
            'textbooks'       => $textbooksGrouped,
            'categories'      => $this->majorCategoryMap,
            'progressRate'    => $progressRate,
            'flaggedChapters' => $flaggedChapters,
        ]);
    }

    public function show($major_id)
    {
        $textbooks = Textbook::where('major_id', $major_id)
            ->with('progressLog')
            ->orderBy('mid_sort')
            ->orderBy('chapter_no')
            ->get();

        $textbooks->map(function ($textbook) {
            $textbook->display_title = $textbook->major_id
                ? ($this->majorCategoryMap[$textbook->major_id] ?? '未定義のカテゴリ')
                : $textbook->custom_title;

            if (!$textbook->progressLog) {
                $textbook->setRelation('progressLog', new ProgressLog([
                    'status'     => 0,
                    'is_flagged' => false,
                    'memo'       => ''
                ]));
            }
            return $textbook;
        });

        return view('textbooks.show', [
            'textbooks'    => $textbooks,
            'currentTitle' => $this->majorCategoryMap[$major_id] ?? 'カスタム教材',
            'majorId'      => $major_id,
        ]);
    }

    public function updateStatus(UpdateStatusRequest $request, $id)
    {
        $userId = auth()->id(); // ✅ ハードコードから認証ユーザーに変更

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
            'message' => '正常に更新されました。'
        ]);
    }

    public function edit($major_id)
    {
        $this->authorize('admin', Textbook::class);

        $categoryName = $this->majorCategoryMap[$major_id] ?? abort(404);

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