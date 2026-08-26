[12:41 ص، 2026/8/8] Alsaadi: <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>استيراد المنتجات</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">استيراد المنتجات من Excel</h4>
        </div>

        <div class="card-body">

            <div class="mb-4">
                <h5>نموذج Excel</h5>

                <p class="text-muted">
                    نزّل النموذج، ثم أدخل بيانات المنتجات واحفظ الملف.
                </p>

    …
[12:44 ص، 2026/8/8] Alsaadi: <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>استيراد المنتجات</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">استيراد المنتجات من Excel</h4>
        </div>

        <div class="card-body">

            <div class="mb-4">
                <h5>نموذج Excel</h5>

                <p class="text-muted">
                    نزّل النموذج، ثم أدخل بيانات المنتجات واحفظ الملف.
                </p>

                <a
                    href="{{ route('admin.products.import.template') }}"
                    class="btn btn-success"
                >
                    تنزيل نموذج Excel
                </a>
            </div>

            <hr>

            @if(session('success'))
                <div class="alert alert-success">
                    {!! nl2br(e(session('success'))) !!}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                action="{{ route('admin.products.import') }}"
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf

                <div class="mb-3">
                    <label for="excel_file" class="form-label">
                        اختر ملف Excel بعد تعبئته
                    </label>

                    <input
                        id="excel_file"
                        type="file"
                        name="excel_file"
                        class="form-control"
                        accept=".xlsx,.xls"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary">
                    استيراد المنتجات
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>