<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentReplyNotification;
use App\Notifications\UserMentionedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mews\Purifier\Facades\Purifier;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        //return response()->json(['all' => $request->all()]);

        $request->validate([
            'article_id' => 'required|exists:articles,id',
            //'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:comments,id',
            'comment_body' => 'required|string|max:1000',
            'locale' => Rule::in(LaravelLocalization::getSupportedLanguagesKeys())
        ]);

        $comment_body = $request->comment_body;
        // 1. استخراج جميع الكلمات التي تبدأ بـ @ عبر Regular Expression
        preg_match_all('/@([a-zA-Z0-9_]+)/', $comment_body, $matches);
        $usernames = $matches[1]; // ['Ahmad']

        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        // Create new comment
        $comment_item = Comment::create([
            'user_id' => Auth::id(),
            'article_id' => $request->article_id,
            'parent_id' => $request->parent_id,
            'body' => trim(strip_tags($request->comment_body)),
            //'mentioned_user_ids' => json_encode($mentionedUserIds),
            'locale' =>  LaravelLocalization::getCurrentLocale()
        ]);

        $comment_item->mentionedUsers()->sync($mentionedUserIds);


        // جلب المقال مع جميع ترجماته لمسح الكاش لكل لغات المقال
        // ليظهر التعليق في صفحة المقالة المترجمة مباشرة بدون انتظار انتهاء الكاش
        $article = Article::with('translations')->find($request->article_id);
        if ($article) {
            foreach ($article->translations as $translation) {
                Cache::forget("article_show_{$translation->id}_lang_{$translation->language_code}");
            }
        }

        $article_trans = ArticleTranslation::where('article_id', (int) $request->article_id)->where('language_code', $comment_item->locale)->first();
        if (!$article_trans) {
            return;
        }

        //Create the notification for the new comment
        if ($comment_item->parent_id !== null) {
            $original_comment = Comment::find($comment_item->parent_id);
            $receiver = $original_comment->user;
            $original_comment_locale = $original_comment->locale;

            // لا تقم بإرسال إشعار الرد في حالتين:
            // 1. إذا كان المستلم هو نفس الشخص الكاتب (Auth::id())
            // 2. إذا كان المستلم نفسه سيصله إشعار Mention في نفس الرد (لتجنب التكرار وإرسال إشعار الإشارة فقط)
            if ($receiver->id !== Auth::id() && !in_array($receiver->id, $mentionedUserIds)) {
                $receiver->notify(new NewCommentReplyNotification($comment_item, $original_comment_locale));
            }
        }

        // 4. إرسال إشعارات لكل شخص تم عمل Mention له
        foreach ($mentionedUserIds as $userId) {
            $user = User::find($userId);
            if ($user->id !== Auth::id()) { // لا تُرسل إشعار لنفسك
                $user->notify(new \App\Notifications\UserMentionedNotification($comment_item));
            }
        }

        if ($request->ajax()) {
            if ($request->parent_id == null)
                $html = view('partials.comment_item', compact('comment_item'))->render();
            else
                $html = view('partials.comment_reply', ['reply' => $comment_item])->render();
            return response()->json(['html' => $html]);
        }
        return back();
    }

    public function update(Request $request)
    {
        //return response()->json($request->all());
        $request->validate([
            'id' => "required|exists:comments,id",
            'body' => 'required|string|max:1000',
        ]);

        $comment_item = Comment::find($request->id);
        if (Auth::id() !== $comment_item->user_id) {
            //return response()->json(['error' => 'This comment is not yours']);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $newBody = $request->input('body');

        // 1. استخراج أسماء المستخدمين الجدد من النص المعدل عبر Regular Expression
        preg_match_all('/@([a-zA-Z0-9_]+)/', $newBody, $matches);
        $usernames = $matches[1];

        // 2. البحث عن الـ IDs الخاصة بالمستخدمين في قاعدة البيانات
        $mentionedUserIds = User::whereIn('name', $usernames)->pluck('id')->toArray();

        // 3. تحديث محتوى التعليق
        $comment_item->update([
            'body' => trim(strip_tags($newBody)),
        ]);

        // 4. مزامنة جدول المنشنات الوسيط (يحذف القديم غير الموجود ويضيف الجديد تلقائياً)
        // إذا كنت قد عرفت علاقة mentionedUsers في نموذج Comment
        $comment_item->mentionedUsers()->sync($mentionedUserIds);

        // 5. (اختياري) إرسال إشعارات لمن تم الإشارة إليهم حديثاً ولم يكونوا موجودين مسبقاً
        foreach ($mentionedUserIds as $userId) {
            $user = User::find($userId);
            // نتحقق ألا يُرسل إشعار لنفسه، وألا يكون قد استلم إشعاراً مسبقاً لنفس التعليق
            if ($user->id !== Auth::id()) {
                // يمكنك هنا إرسال الإشعار بالطريقة التي تفضلها
                $user->notify(new UserMentionedNotification($comment_item));
            }
        }

        // جلب المقال وترجماته المرتبطة بالتعليق ومسح الكاش ليظهر
        // التعليق في صفحة المقالة مباشرة بدون تأخير
        $article = Article::with('translations')->find($comment_item->article_id);
        if ($article) {
            foreach ($article->translations as $translation) {
                Cache::forget("article_show_{$translation->id}_lang_{$translation->language_code}");
            }
        }

        return response()->json([
            'message' => 'Comment Updated Successfully',
            'new_body' => $comment_item->formatted_body
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => "required|exists:comments,id",
        ]);

        $comment_item = Comment::find($request->id);

        if (Auth::id() !== $comment_item->user_id) {
            return response()->json(['error' => 'This comment is not yours']);
        }

        $article_id = $comment_item->article_id;

        $comment_item->delete();
        // جلب المقال وترجماته ومسح الكاش
        // ليختفي التعليق مباشرة قبل انتظار انتهاء الكاش
        $article = Article::with('translations')->find($article_id);
        if ($article) {
            foreach ($article->translations as $translation) {
                Cache::forget("article_show_{$translation->id}_lang_{$translation->language_code}");
            }
        }

        return response()->json([
            'message' => 'Comment Deleted Successfully',
        ]);
    }
}
