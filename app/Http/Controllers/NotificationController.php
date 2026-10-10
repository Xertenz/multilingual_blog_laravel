<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NotificationController extends Controller
{
    public function fetch()
    {
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

            // 💬 حالة إشعار الرد على تعليق
            case 'App\Notifications\NewCommentReply':
                if (isset($data['article_trans_id'], $data['article_trans_slug'])) {
                    $targetUrl = route('articles.show', [
                        'id'   => $data['article_trans_id'],
                        'slug' => $data['article_trans_slug']
                    ]) . (isset($data['reply_id']) ? '#reply-item-' . $data['reply_id'] : '');
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

            // 🔮 لأي أنواع إشعارات مستقبلية
            default:
                $targetUrl = route('home.index');
                break;
        }

        return redirect($targetUrl);
    }
}
