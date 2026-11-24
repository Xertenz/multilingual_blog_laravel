<?php

namespace App\Services;

use App\Enums\ContentStatus;
use App\Jobs\SendAuthorNewContentJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContentNotificationDispatcher
{
    public function dispatchIfReady(
        Model $translation,
        string $contentType,
        string $translationTable,
        ?string $parentRelation = null,
        ?string $parentTable = null,
        ?string $parentForeignKey = null,
        bool $translationHasStatus = true,
        bool $parentHasStatus = true
    ): void {
        $published = ContentStatus::PUBLISHED->value;
        $claimed = false;

        DB::transaction(function () use (
            $translation,
            $translationTable,
            $parentRelation,
            $parentTable,
            $parentForeignKey,
            $translationHasStatus,
            $parentHasStatus,
            $published,
            &$claimed
        ) {
            $freshTrans = DB::table($translationTable)
                ->where('id', $translation->id)
                ->lockForUpdate()
                ->first();

            if (!$freshTrans || !is_null($freshTrans->notification_sent_at)) {
                return;
            }

            if ($translationHasStatus && $freshTrans->status !== $published) {
                return;
            }

            if ($parentTable && $parentForeignKey) {
                $parentId = $freshTrans->{$parentForeignKey} ?? null;
                if (!$parentId) {
                    return;
                }

                $parent = DB::table($parentTable)->where('id', $parentId)->first();
                if (!$parent) {
                    return;
                }

                if ($parentHasStatus && $parent->status !== $published) {
                    return;
                }
            }

            $now = now();
            $query = DB::table($translationTable)
                ->where('id', $freshTrans->id)
                ->whereNull('notification_sent_at');

            if ($translationHasStatus) {
                $query->where('status', $published);
            }

            $updated = $query->update(['notification_sent_at' => $now]);

            if ($updated > 0) {
                $translation->notification_sent_at = $now;

                $relations = ['author'];
                if ($parentRelation) {
                    $relations[] = $parentRelation . '.author';
                }

                $translation->loadMissing($relations);
                $claimed = true;
            }
        });

        if ($claimed) {
            SendAuthorNewContentJob::dispatch($translation, $contentType)->onQueue('default');
        }
    }
}
