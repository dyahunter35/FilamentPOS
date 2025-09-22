<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Filament\Notifications\Notification as NotificationsNotification;

class Error500 extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // هذا الجزء سيتم تنفيذه عند حدوث خطأ
        });

        $this->renderable(function (Throwable $e, $request) {
            if ($e->getStatusCode() === 500) {
                // هنا يتم إرسال الإشعار
                NotificationsNotification::make()
                    ->title('خطأ في الخادم')
                    ->body('حدث خطأ غير متوقع في التطبيق. يرجى التحقق من السجلات.')
                    ->danger()
                    ->send();

                // يمكنك إرجاع استجابة JSON أو إعادة توجيه المستخدم
                return response()->json(['message' => 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.'], 500);
            }
        });
    }
}
