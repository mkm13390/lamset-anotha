<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>لمسة أنوثة</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8f4ef;
            color: #241f1c;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 8%;
            background: #ffffff;
            border-bottom: 1px solid #eadfd5;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
        }

        nav a {
            color: #241f1c;
            text-decoration: none;
            margin-right: 24px;
        }

        .hero {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f7eee7, #e7d5c8);
        }

        .hero-content {
            max-width: 760px;
        }

        .badge {
            display: inline-block;
            padding: 9px 18px;
            margin-bottom: 22px;
            border: 1px solid #6d5142;
            border-radius: 30px;
        }

        h1 {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 20px;
            line-height: 1.9;
            margin-bottom: 32px;
        }

        .button {
            display: inline-block;
            background: #241f1c;
            color: #ffffff;
            text-decoration: none;
            padding: 15px 34px;
            border-radius: 8px;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 55px 8%;
        }

        .feature {
            background: #ffffff;
            padding: 30px;
            text-align: center;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .feature h2 {
            margin-bottom: 12px;
            font-size: 21px;
        }

        footer {
            background: #241f1c;
            color: #ffffff;
            text-align: center;
            padding: 24px;
        }

        @media (max-width: 700px) {
            header {
                flex-direction: column;
                gap: 18px;
            }

            nav a {
                margin: 0 8px;
            }

            h1 {
                font-size: 43px;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<header>
    <div class="logo">لمسة أنوثة</div>

    <nav>
        <a href="#">الرئيسية</a>
        <a href="#">المنتجات</a>
        <a href="#">حسابي</a>
        <a href="#">السلة</a>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="hero-content">
            <div class="badge">أناقة تعبّر عنك</div>

            <h1>لمسة أنوثة</h1>

            <p>
                وجهتك للأناقة الراقية من الحقائب والأحذية والإكسسوارات،
                بتشكيلات مختارة لتمنحك حضورًا مختلفًا.
            </p>

            <a class="button" href="#features">تصفحي المتجر</a>
        </div>
    </section>

    <section class="features" id="features">
        <div class="feature">
            <h2>تشكيلات مميزة</h2>
            <p>منتجات مختارة بعناية تناسب مختلف الأذواق.</p>
        </div>

        <div class="feature">
            <h2>دفع آمن</h2>
            <p>خيارات دفع سهلة وآمنة لإتمام طلبك.</p>
        </div>

        <div class="feature">
            <h2>توصيل داخل عمان</h2>
            <p>توصيل منظم وسريع إلى مختلف المحافظات.</p>
        </div>
    </section>
</main>

<footer>
    جميع الحقوق محفوظة © لمسة أنوثة
</footer>

</body>
</html>s