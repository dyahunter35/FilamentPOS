<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Filament\Notifications\Notification;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
        // هذه الدالة مخصصة لإرسال إشعارات أو تسجيل الأخطاء
        $this->reportable(function (Throwable $e) {
            // يمكنك هنا إرسال الإشعارات إليك كمطور
            // مثال: إذا كنت تريد إشعارًا على Slack أو البريد الإلكتروني
        });

        // هذه الدالة مخصصة لمعالجة الاستجابة التي تظهر للمستخدم
        $this->renderable(function (Throwable $e, $request) {

            // التحقق من أن الخطأ هو من نوع HttpException (مثل 404, 500)
            if ($e instanceof HttpException) {
                // إذا كان الخطأ 500
                if ($e->getStatusCode() === 500) {
                    Notification::make()
                        ->title('خطأ في الخادم')
                        ->body('حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.')
                        ->danger()
                        ->send();

                    return response()->json(['message' => 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.'], 500);
                }
            }

            // مثال آخر: معالجة أخطاء قاعدة البيانات
            if ($e instanceof QueryException) {
                Notification::make()
                    ->title('خطأ في قاعدة البيانات')
                    ->body('حدث خطأ أثناء معالجة البيانات. يرجى الاتصال بالدعم الفني.')
                    ->danger()
                    ->send();

                return response()->json(['message' => 'حدث خطأ في قاعدة البيانات، يرجى المحاولة لاحقاً.'], 500);
            }
        });
    }
}
