<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://kit.fontawesome.com/1980864942.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('asset/css/style.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="main-wrapper">
        @include('partials.topbar')
        <main class="content-area">
            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const alert = document.querySelector('.custom-alert-success');

            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transform = 'translateY(-10px)';

                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 2500);
            }
        });
    </script>
</body>

</html>