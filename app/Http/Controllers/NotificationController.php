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
            case 'App\Notifications\NewCommentReplyNotification':
                $replyId = $data['reply_id'] ?? null;
                $replyComment = $replyId ? \App\Models\Comment::find($replyId) : null;

                if ($replyComment && $replyComment->article) {
                    $article = $replyComment->article;

                    // 1. لغة المستلم الحالية
                    $recipientLocale = $notification->notifiable->locale ?? LaravelLocalization::getCurrentLocale();

                    // 2. لغة المُرسِل المخزنة في الإشعار (أو نلجأ للغة الرد نفسه كبديل)
                    $senderLocale = $data['sender_locale'] ?? $replyComment->locale;

                    // أ. البحث عن ترجمة المقال بلغة المستلم أولاً
                    $articleTrans = $article->translations()->where('language_code', $recipientLocale)->first();

                    // ب. إذا لم تتوفر، نبحث بلغة المُرسِل
                    if (!$articleTrans && $senderLocale) {
                        $articleTrans = $article->translations()->where('language_code', $senderLocale)->first();
                    }

                    // ج. كملجأ أخير، نأخذ أول ترجمة متاحة للمقالة
                    if (!$articleTrans) {
                        $articleTrans = $article->translations()->first();
                    }

                    if ($articleTrans) {
                        $targetLocale = $articleTrans->language_code;

                        $baseUrl = LaravelLocalization::getLocalizedURL($targetLocale, route('articles.show', [
                            'id'   => $articleTrans->id,
                            'slug' => $articleTrans->slug
                        ]));

                        $targetUrl = $baseUrl . '#comment-' . $replyComment->id;
                    }
                } else {
                    // تراجع آمن (Fallback) في حال كان الإشعار قديماً جداً وتم حذفه أو تلفه
                    $targetUrl = route('home.index');
                }
                break;






            // 📰 حالة إشعار مقالة جديدة من الفئة المفضلة
            case 'App\Notifications\NewArticleInFavoriteCategory':
                if (isset($data['article_id'])) {
                    $article = Article::find($data['article_id']);
                    if ($article) {
                        // جلب الترجمة للغة الحالية للزائر أو أول ترجمة متاحة
                        $currentLocale = LaravelLocalization::getCurrentLocale();
                        $articleTrans = $article->translations()->where('language_code', $currentLocale)->first()
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
            /*
            // ❤️ حالة إشعار التفاعل على التعليق (الجديدة)
            case 'App\Notifications\NewCommentReactionNotification':
                if (isset($data['article_trans_id'], $data['article_trans_slug'])) {
                    $targetUrl = route('articles.show', [
                        'id'   => $data['article_trans_id'],
                        'slug' => $data['article_trans_slug']
                    ]) . (isset($data['comment_id']) ? '#comment-' . $data['comment_id'] : '');
                }
                break;
            */

            // ❤️ حالة إشعار التفاعل على التعليق أو الرد
            case 'App\Notifications\NewCommentReactionNotification':
                $transId = data_get($data, 'article_trans_id') ?? data_get($data, 'article_id');

                if ($transId) {
                    // تكوين الرابط بالاعتماد الأساسي على الـ ID (مع تمرير الـ slug إن وجد)
                    $routeParams = ['id' => $transId];
                    if ($transSlug = data_get($data, 'article_trans_slug')) {
                        $routeParams['slug'] = $transSlug;
                    }

                    $baseUrl = route('articles.show', $routeParams);
                    $targetId = $data['reply_id'] ?? $data['comment_id'] ?? null;

                    $comment = $targetId ? \App\Models\Comment::find($targetId) : null;

                    if ($comment) {
                        //$targetUrl = $baseUrl . (!empty($comment->parent_id) ? "#reply-item-{$targetId}" : "#comment-{$targetId}");
                        $targetUrl = $baseUrl .  "#comment-{$targetId}";
                    } else {
                        $targetUrl = $baseUrl;
                    }
                }
                break;







            // 🔔 حالة إشعار الـ Mention (الإشارة في تعليق أو رد)
            case 'App\Notifications\UserMentionedNotification':
                if (isset($data['comment_id'], $data['article_id'])) {
                    $comment = \App\Models\Comment::find($data['comment_id']);
                    $article = \App\Models\Article::find($data['article_id']);

                    if ($comment && $article) {
                        // 1. لغة المستلم الحالية
                        $recipientLocale = $notification->notifiable->locale ?? LaravelLocalization::getCurrentLocale();

                        // 2. لغة المُرسِل المخزنة في الإشعار (مع وضع الكقاعدة بيانات احتياطية لغة التعليق نفسها)
                        $senderLocale = $data['sender_locale'] ?? $comment->locale;

                        // الخطوة أ: البحث عن ترجمة المقال بلغة المستلم أولاً
                        $articleTrans = $article->translations()->where('language_code', $recipientLocale)->first();

                        // الخطوة ب: إذا لم تتوفر، نبحث بلغة المُرسِل (فكرتك العبقرية)
                        if (!$articleTrans && $senderLocale) {
                            $articleTrans = $article->translations()->where('language_code', $senderLocale)->first();
                        }

                        // الخطوة ج: كملجأ أخير، نأخذ أول ترجمة متاحة للمقالة
                        if (!$articleTrans) {
                            $articleTrans = $article->translations()->first();
                        }

                        if ($articleTrans) {
                            $targetLocale = $articleTrans->language_code;

                            $baseUrl = LaravelLocalization::getLocalizedURL($targetLocale, route('articles.show', [
                                'id'   => $articleTrans->id,
                                'slug' => $articleTrans->slug
                            ]));

                            $anchor = $comment->parent_id !== null ? "#reply-item-{$comment->id}" : "#comment-{$comment->id}";
                            $targetUrl = $baseUrl . $anchor;
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
