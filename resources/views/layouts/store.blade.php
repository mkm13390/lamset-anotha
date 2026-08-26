<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'لمسة أنوثة')</title>

    <meta name="description" content="@yield('meta_description', 'متجر لمسة أنوثة')">

    @stack('styles')
</head>
<body>

    @yield('content')

    @stack('scripts')
</body>
</html>