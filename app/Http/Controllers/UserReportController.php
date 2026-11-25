<?php

namespace App\Http\Controllers;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Models\Article;
use App\Models\Book;
use App\Models\Comment;
use App\Models\CommunityCase;
use App\Models\Game;
use App\Models\MediaSummary;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;

class UserReportController extends Controller
{
    private const REPORTABLE_MAP = [
        'comment'        => Comment::class,
        'community_case' => CommunityCase::class,
        'article'        => Article::class,
        'book'           => Book::class,
        'game'           => Game::class,
        'media_summary'  => MediaSummary::class,
        'user'           => User::class,
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reportable_type' => ['required', 'string', 'max:100'],
            'reportable_id'   => ['required', 'integer', 'min:1'],
            'reason_type'     => ['required', new Enum(ReportReason::class)],
            'details'         => ['nullable', 'string', 'max:500'],
        ]);

        [$morphAlias, $modelClass] = $this->resolveReportable($validated['reportable_type']);

        if (!$morphAlias || !$modelClass) {
            return back()->with('error', 'نوع العنصر المُبلَغ عنه غير صالح.');
        }

        $target = $modelClass::find($validated['reportable_id']);

        if (!$target) {
            return back()->with('error', 'العنصر المطلوب الإبلاغ عنه غير موجود.');
        }

        if ($morphAlias === 'user' && (int) $target->id === (int) Auth::id()) {
            return back()->with('error', 'لا يمكنك الإبلاغ عن حسابك الشخصي.');
        }

        $exists = Report::where('reporter_id', Auth::id())
            ->where('reportable_type', $morphAlias)
            ->where('reportable_id', $target->id)
            ->where('status', ReportStatus::PENDING->value)
            ->exists();

        if ($exists) {
            return back()->with('error', 'لقد قمت بالإبلاغ عن هذا العنصر مسبقاً وهو قيد المراجعة.');
        }

        $cleanDetails = trim((string) ($validated['details'] ?? ''));

        Report::create([
            'reporter_id'     => Auth::id(),
            'reportable_type' => $morphAlias,
            'reportable_id'   => $target->id,
            'reason_type'     => $validated['reason_type'],
            'details'         => $cleanDetails !== '' ? $cleanDetails : null,
            'status'          => ReportStatus::PENDING->value,
        ]);

        return back()->with('success', 'تم إرسال البلاغ بنجاح، شكراً لمساعدتنا في الحفاظ على أمان المنصة.');
    }

    private function resolveReportable(string $input): array
    {
        $normalized = strtolower(trim($input));

        if (isset(self::REPORTABLE_MAP[$normalized])) {
            return [$normalized, self::REPORTABLE_MAP[$normalized]];
        }

        // دعم تمرير اسم الكلاس الكامل أو الصيغة القديمة بدون شرطة مائلة إذا طابقت موديل مسموح
        $cleanedClass = match ($input) {
            'AppModelsComment'       => Comment::class,
            'AppModelsCommunityCase' => CommunityCase::class,
            default                  => ltrim($input, '\\'),
        };

        foreach (self::REPORTABLE_MAP as $alias => $class) {
            if ($cleanedClass === $class) {
                return [$alias, $class];
            }
        }

        return [null, null];
    }
}
