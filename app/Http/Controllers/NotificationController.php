<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\App;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NotificationController extends Controller
{
    public function fetch()
    {
        /*
        $user = Auth::user();
        $userNotifications = $user->notifications()
            ->latest()
            ->paginate(10);

        */
        $user_id = Auth::id();
        $userNotifications = DatabaseNotification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user_id)
            ->latest()
            ->paginate(10);

        $html = '';
        foreach ($userNotifications as $userNotification) {
            $html .= view('partials.notification_item', ['notification' => $userNotification])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMorePages' => $userNotifications->hasMorePages(),
        ]);
    }

    public function read(string $notification_id)
    {
        $user_id = Auth::id();
        $notification = DatabaseNotification::where('id', $notification_id)
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user_id)
            ->first();

        // 1. حماية من الإشعارات غير الموجودة أو المفتوحة من قبل مستخدم آخر
        if (!$notification) {
            return redirect()->back()->with('error', __('Notification not found.'));
        }

        // 2. تعليم الإشعار كمقروء
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        $data = $notification->data;
        $targetUrl = route('home.index'); // رابط افتراضي للـ Fallback

        // 3. التوجيه الديناميكي حسب نوع الإشعار
        switch ($notification->type) {

            /*
            // 💬 حالة إشعار الرد على تعليق
            case 'App\Notifications\NewCommentReplyNotification':
                if (isset($data['article_trans_id'], $data['article_trans_slug'])) {
                    $targetUrl = route('articles.show', [
                        'id'   => $data['article_trans_id'],
                        'slug' => $data['article_trans_slug']
                    ]) . (isset($data['reply_id']) ? '#reply-item-' . $data['reply_id'] : '');
                }
                break;
            */

            // 💬 حالة إشعار الرد على تعليق
            //case 'App\Notifications\NewCommentReplyNotification':
            case \App\Notifications\NewCommentReplyNotification::class:
                $replyId = $data['reply_id'] ?? null;
                $replyComment = $replyId ? \App\Models\Comment::find($replyId) : null;

                if ($replyComment && $replyComment->commentable) {


                    $commentable = $replyComment->commentable;

                    // 1. لغة المستلم الحالية
                    $recipientLocale = $notification->notifiable->locale ?? LaravelLocalization::getCurrentLocale();

                    // 2. لغة المُرسِل المخزنة في الإشعار (أو نلجأ للغة الرد نفسه كبديل)
                    $senderLocale = $data['sender_locale'] ?? $replyComment->locale;

                    if ($commentable instanceof \App\Models\Article) {
                        // أ. البحث عن ترجمة المقال بلغة المستلم أولاً
                        $articleTrans = $commentable->translations()->where('locale', $recipientLocale)->first();

                        // ب. إذا لم تتوفر، نبحث بلغة المُرسِل
                        if (!$articleTrans && $senderLocale) {
                            $articleTrans = $commentable->translations()->where('locale', $senderLocale)->first();
                        }

                        // ج. كملجأ أخير، نأخذ أول ترجمة متاحة للمقالة
                        if (!$articleTrans) {
                            $articleTrans = $commentable->translations()->first();
                        }

                        if ($articleTrans) {
                            $targetLocale = $articleTrans->locale;

                            $baseUrl = LaravelLocalization::getLocalizedURL($targetLocale, route('articles.show', [
                                'id'   => $articleTrans->id,
                                'slug' => $articleTrans->slug
                            ]));

                            $targetUrl = $baseUrl . '#comment-' . $replyComment->id;
                        }
                    }
                } else {
                    // تراجع آمن (Fallback) في حال كان الإشعار قديماً جداً وتم حذفه أو تلفه
                    $targetUrl = route('home.index');
                }
                break;



            // 📰 حالة إشعار مقالة جديدة من الفئة المفضلة
            //case 'App\Notifications\NewArticleInFavoriteCategory':
            case \App\Notifications\NewArticleInFavoriteCategory::class:
                if (isset($data['article_id'])) {
                    $article = Article::find($data['article_id']);
                    if ($article) {
                        // جلب الترجمة للغة الحالية للزائر أو أول ترجمة متاحة
                        $currentLocale = LaravelLocalization::getCurrentLocale();
                        $articleTrans = $article->translations()->where('locale', $currentLocale)->first()
                            ?? $article->translations()->first();

                        if ($articleTrans) {
                            $targetUrl = route('articles.show', [
                                'id'   => $articleTrans->id,
                                'slug' => $articleTrans->slug
                            ]);
                        }
                    }
                }
                break;


            // ❤️ حالة إشعار التفاعل على التعليق أو الرد
            //case 'App\Notifications\NewCommentReactionNotification':
            case \App\Notifications\NewCommentReactionNotification::class:
                $targetId = $data['reply_id'] ?? $data['comment_id'] ?? null;
                $comment = $targetId ? \App\Models\Comment::find($targetId) : null;

                if ($comment && $comment->commentable) {
                    $commentable = $comment->commentable;

                    // 1. التوجيه الذكي إذا كان التفاعل على تعليق داخل مقال (Article)
                    if ($commentable instanceof \App\Models\Article) {
                        $recipientLocale = $notification->notifiable->locale ?? LaravelLocalization::getCurrentLocale();
                        $senderLocale = $data['sender_locale'] ?? $comment->locale;

                        // البحث عن الترجمة المناسبة (لغة المستلم -> لغة المرسل -> أول ترجمة)
                        $articleTrans = $commentable->translations()->where('locale', $recipientLocale)->first()
                            ?? $commentable->translations()->where('locale', $senderLocale)->first()
                            ?? $commentable->translations()->first();

                        if ($articleTrans) {
                            $baseUrl = LaravelLocalization::getLocalizedURL($articleTrans->locale, route('articles.show', [
                                'id'   => $articleTrans->id,
                                'slug' => $articleTrans->slug
                            ]));

                            $targetUrl = $baseUrl . '#comment-' . $comment->id;
                        }
                    }
                    // 2. التوجيه الذكي إذا كان التفاعل على تعليق داخل قضية في لوحة المحققين (CommunityCase)
                    elseif ($commentable instanceof \App\Models\CommunityCase) {
                        $targetUrl = route('community.cases.show', $commentable->slug) . '#comment-' . $comment->id;
                    }
                } else {
                    // Fallback احتياطي في حال الاعتماد على البيانات القديمة المخزنة في الـ JSON
                    $transId = data_get($data, 'article_trans_id') ?? data_get($data, 'article_id');
                    if ($transId) {
                        $routeParams = ['id' => $transId];
                        if ($transSlug = data_get($data, 'article_trans_slug')) {
                            $routeParams['slug'] = $transSlug;
                        }
                        $targetUrl = route('articles.show', $routeParams) . ($targetId ? "#comment-{$targetId}" : '');
                    }
                }
                break;



            // 🔔 حالة إشعار الـ Mention (الإشارة في تعليق أو رد)
            //case 'App\Notifications\UserMentionedNotification':
            case \App\Notifications\UserMentionedNotification::class:
                if (isset($data['comment_id'])) {
                    $comment = \App\Models\Comment::find($data['comment_id']);

                    if ($comment && $comment->commentable) {
                        $commentable = $comment->commentable;
                        $anchor = '#comment-' . $comment->id;

                        // 1. إذا كان التعليق داخل مقال (Article)
                        if ($commentable instanceof \App\Models\Article) {
                            $recipientLocale = $notification->notifiable->locale ?? LaravelLocalization::getCurrentLocale();
                            $senderLocale = $data['sender_locale'] ?? $comment->locale;

                            $articleTrans = $commentable->translations()->where('locale', $recipientLocale)->first()
                                ?? $commentable->translations()->where('locale', $senderLocale)->first()
                                ?? $commentable->translations()->first();

                            if ($articleTrans) {
                                $baseUrl = LaravelLocalization::getLocalizedURL($articleTrans->locale, route('articles.show', [
                                    'id'   => $articleTrans->id,
                                    'slug' => $articleTrans->slug
                                ]));

                                $targetUrl = $baseUrl . $anchor;
                            }
                        }
                        // 2. إذا كان التعليق داخل قضية في لوحة المحققين (CommunityCase)
                        elseif ($commentable instanceof \App\Models\CommunityCase) {
                            $targetUrl = route('community.cases.show', $commentable->slug) . $anchor;
                        }
                    }
                }
                break;






            // 🔮 لأي أنواع إشعارات مستقبلية
            default:
                $targetUrl = route('home.index');
                break;
        }

        return redirect($targetUrl);
    }

    public function updateLastRead()
    {
        //$user = Auth::user();
        $user_id = Auth::id();
        $user = User::where('id', $user_id);
        if ($user) {
            $user->update([
                'notifications_last_read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
