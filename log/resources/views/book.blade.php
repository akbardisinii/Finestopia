<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finestopia - Manage Your Money Smartly</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        purple: '#E8C6F8'
                    }
                }
            }
        }
    </script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        .custom-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
        .navbar-fixed {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
    </style>
</head>
<body class="antialiased bg-white">
    <div class="min-h-screen flex flex-col">
        <nav id="navbar" class="bg-orange-500 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <img src="{{ asset('icons/logobaru.png') }}" alt="Finest Logo" class="h-8 w-auto mr-2">
                        <span class="text-xl sm:text-2xl font-bold text-white">Finestopia</span>
                    </div>
                    <div class="flex items-center">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/api/home') }}" class="text-orange-600 bg-white hover:bg-orange-600 hover:text-white mr-2 sm:mr-4 px-3 sm:px-4 py-1 sm:py-2 text-sm sm:text-base rounded-md border border-orange-600">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-white hover:underline mr-2 sm:mr-4 text-sm sm:text-base">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-orange-600 bg-white hover:bg-orange-600 hover:text-white px-3 sm:px-4 py-1 sm:py-2 text-sm sm:text-base rounded-md border border-orange-600">Daftar</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-10 sm:py-12 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="text-left w-full md:w-1/2 mb-8 md:mb-0">
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900">
                            <span class="block">Take Control of Your</span>
                            <span class="block text-orange-600">Financial Future</span>
                        </h1>
                        <p class="mt-3 text-sm sm:text-base md:text-lg text-gray-500">
                            Finestopia membantu Anda mengelola keuangan dengan mudah. Lacak pengeluaran, tetapkan anggaran, dan raih tujuan keuangan Anda.
                        </p>
                        <div class="mt-5 space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="#about" class="inline-block px-5 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                Tentang Kami
                            </a>
                            <a href="http://finestopia.com/blog" class="inline-block px-5 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                Berita
                            </a>
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 mt-8 md:mt-0">
                        <img src="{{ ('icons/landing.jpg') }}" loading="lazy" alt="Gambar keuangan" class="w-full h-auto rounded-lg shadow-lg">
                    </div>
                </div>
            </div>
        </main>

        <section id="about" class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="w-full md:w-1/2 mb-8 md:mb-0 md:mr-10">
                        <img src="{{ asset('icons/19198999.png') }}" loading="lazy" alt="Finest Logo" class="w-full max-w-[500px] h-auto mx-auto">
                    </div>
                    <div class="w-full md:w-1/2">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-4">Tentang Kami</h1>
                        <p class="text-gray-600 mb-6 text-sm sm:text-base" style="text-align: justify;">
                            Finestopia adalah aplikasi manajemen keuangan yang dirancang untuk membantu pengguna mengelola keuangan pribadi dengan bijak dan aman. Dengan fitur perencanaan anggaran, pelacakan pengeluaran, dan strategi tabungan, Finestopia memudahkan pengguna dalam mengambil keputusan finansial yang tepat. Kami mengutamakan keamanan data dengan teknologi enkripsi terkini untuk melindungi informasi keuangan Anda, menjadikan Finestopia sebagai mitra terpercaya untuk mencapai kesehatan finansial yang lebih baik.
                        </p>
                        <ul class="list-disc pl-5 text-gray-600 mb-6 space-y-4 text-sm sm:text-base" style="text-align: justify;">
                            <li>
                                <span class="font-bold" style="color: #916CC4;">Manajemen Keuangan Simpel</span> 
                                <p>Semua urusan keuangan bisa diatur dalam satu tempat yang mudah digunakan.</p>
                            </li>
                            <li>
                                <span class="font-bold" style="color: #916CC4;">Perencanaan Masa Depan</span> 
                                <p>Bantu Anda menyiapkan masa depan finansial, seperti dana pendidikan atau pensiun, dengan perencanaan yang terarah.</p>
                            </li>
                            <li>
                                <span class="font-bold" style="color: #916CC4;">Aman dan Terpercaya</span> 
                                <p>Keamanan dan privasi data keuangan Anda adalah prioritas utama kami.</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <button id="scrollToTopBtn" class="hidden fixed bottom-5 right-5 z-50 p-2 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>

        <footer class="bg-gray-100">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Finestopia. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <script>
        const navbar = document.getElementById('navbar');
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');

        window.onscroll = function() {
            if (window.pageYOffset > 0) {
                navbar.classList.add('navbar-fixed');
            } else {
                navbar.classList.remove('navbar-fixed');
            }

            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                scrollToTopBtn.classList.remove('hidden');
            } else {
                scrollToTopBtn.classList.add('hidden');
            }
        };

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>