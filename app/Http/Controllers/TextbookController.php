<?php

namespace App\Http\Controllers;

use App\Models\Textbook;
use App\Models\ProgressLog;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateStatusRequest; // 🌟 追加
use App\Http\Requests\UpdateNameRequest;

class TextbookController extends Controller
{
    // ★マスタデータマップをコントローラー内に定義
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

    /**
     * 【修正】教材一覧画面（ダッシュボード - 13項目のカード表示）
     */
    /**
     * 教材一覧画面（ダッシュボード - 13項目のカード表示）
     */
    /**
     * 教材一覧画面（ダッシュボード - 13項目のカード表示）
     */
    public function index()
    {
        $userId = 1; 

        // 全データ（進捗率の計算用）
        $allTextbooks = Textbook::where('user_id', $userId)->with('progressLog')->get();

        // 1. 全体の進捗率（%）の計算
        $totalCount = $allTextbooks->count();
        $completedCount = $allTextbooks->filter(function ($textbook) {
            return $textbook->progressLog && $textbook->progressLog->status == 2;
        })->count();
        $progressRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        // 2. 各大項目（カードごと）の進捗率をあらかじめ計算してコレクションに仕込む
        $textbooksGrouped = $allTextbooks->groupBy('major_id')->map(function ($group) {
            $groupTotal = $group->count();
            $groupCompleted = $group->filter(function ($textbook) {
                return $textbook->progressLog && $textbook->progressLog->status == 2;
            })->count();
            
            $group->progress_rate = $groupTotal > 0 ? round(($groupCompleted / $groupTotal) * 100) : 0;
            return $group;
        });

        // 3. 🌟【新規追加】「⚠️ 質問あり」にチェックが入っているチャプターだけを抽出
        $flaggedChapters = $allTextbooks->filter(function ($textbook) {
            return $textbook->progressLog && $textbook->progressLog->is_flagged;
        });

        // 一覧画面に必要なデータをすべて渡す
        return view('textbooks.index', [
            'textbooks'       => $textbooksGrouped,
            'categories'      => $this->majorCategoryMap,
            'progressRate'    => $progressRate,
            'flaggedChapters' => $flaggedChapters // 🌟追加
        ]);
    }

    /**
     * 教材詳細画面（特定のmajor_idに紐づく中・小項目を表示）
     */
    public function show($major_id)
    {
        //$userId = 1;

        // 選択された大項目（major_id）に該当するチャプターのみをEager Loadingで取得
        $textbooks = Textbook::where('major_id', $major_id)
        ->with('progressLog')
        ->orderBy('mid_sort')
        ->orderBy('chapter_no')
        ->get();

        // タイトルセット ＆ 未着手ログの初期化
        $textbooks->map(function ($textbook) {
            // ★ $this->majorCategoryMap に変更
            $textbook->display_title = $textbook->major_id 
                ? ($this->majorCategoryMap[$textbook->major_id] ?? '未定義のカテゴリ')
                : $textbook->custom_title;
            
            if (!$textbook->progressLog) {
                $textbook->setRelation('progressLog', new ProgressLog([
                    'status' => 0, 
                    'is_flagged' => false,
                    'memo' => ''
                ]));
            }
            return $textbook;
        });

        return view('textbooks.show', [
            'textbooks'    => $textbooks,
            'currentTitle' => $this->majorCategoryMap[$major_id] ?? 'カスタム教材',
            'majorId'      => $major_id
        ]);
    }

    /**
     * 非同期更新API
     */
    /**
     * 非同期更新API
     */
    public function updateStatus(UpdateStatusRequest $request, $id)

    {

        // 🌟【重要】ここで確実に $userId を定義します（エラーが起きていた箇所）
        $userId = 1; 

        // データベース更新（または作成）
        $progressLog = ProgressLog::updateOrCreate(
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

    /**
     * 🌟【新規追加】教材名編集画面（管理者のみ）
     */
    public function edit($major_id)
    {   

        $this->authorize('admin', Textbook::class);

        $categoryName = $this->majorCategoryMap[$major_id] ?? abort(404);

        return view('textbooks.edit', [
            'majorId'      => $major_id,
            'categoryName' => $categoryName
        ]);
    }

    /**
     * 🌟【新規追加】教材名更新処理（管理者のみ）
     */
    public function updateName(Request $request, $major_id)
    {   

        $this->authorize('admin', Textbook::class);

        // 💡 補足: 現在教材名はコントローラーの配列（固定値）で持っているため、
        // 本来はDB化して上書き保存しますが、今回は認可の疎通確認のため「成功メッセージを出して一覧に戻る」処理とします。
        
        return redirect()->route('textbooks.index')->with('success', '教材名を更新しました（認可成功）');
    }
}