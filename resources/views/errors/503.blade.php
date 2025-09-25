<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خطأ في الخادم - 500</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f7fafc;
            color: #4a5568;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            direction: rtl;
            /* للغة العربية */
            text-align: center;
        }

        .error-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 700;
            color: #047fc2;
            /* أحمر */
            margin-bottom: 20px;
            line-height: 1;
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            /* رمادي غامق */
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .home-button {
            display: inline-block;
            background-color: #4299e1;
            /* أزرق */
            color: #ffffff;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            transition: background-color 0.3s ease;
        }

        .home-button:hover {
            background-color: #2b6cb0;
            /* أزرق أغمق */
        }

        .footer-text {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-code">503</div>
        <h1 class="error-title">الموقع في حاله الصيانة حاليا</h1>
        <p class="error-message">

        </p>
        <p class="footer-text">للتفاصيل  يرجى التواصل مع الدعم الفني.</p>
    </div>
</body>

</html>
