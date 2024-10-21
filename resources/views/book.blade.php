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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js"></script>
    <style>
        /* Add custom styles for the flipbook */
        #flipbook {
            width: 600px; /* Adjust width as needed */
            height: 400px; /* Allow height to adjust */
            margin: 20px auto;
            position: relative;
        }
        canvas {
            width: 100%; /* Make canvas responsive */
            height: auto; /* Maintain aspect ratio */
        }
        .page {
            background: white;
            border: 1px solid #ccc;
        }
    </style>
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
        #pdf-controls {
            margin-top: 20px;
        }
    </style>
</head>
<body class="antialiased bg-white">
    <div class="min-h-screen flex flex-col">
        <nav id="navbar" class="bg-orange-500 shadow-sm w-full">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
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
                <!-- Tempatkan canvas di sini untuk menampilkan PDF -->
                <div id="flipbook" class="custom-shadow">
                    <canvas id="pdf-canvas" class="w-full"></canvas>
        
                    <!-- Kontrol untuk navigasi PDF -->
                    <div id="pdf-controls" class="flex justify-between mt-5">
                        <button id="prev-page" class="px-4 py-2 bg-orange-600 text-white rounded-md">Previous Page</button>
                        <span>Page: <span id="page-num"></span> / <span id="page-count"></span></span>
                        <button id="next-page" class="px-4 py-2 bg-orange-600 text-white rounded-md">Next Page</button>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-gray-100">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Finestopia. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- Script untuk memuat dan navigasi PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        var url = '/Finestopia%20Manual%20Book.pdf';  // Path PDF di folder public

        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        var pdfDoc = null,
            pageNum = 1,
            pageIsRendering = false,
            pageNumIsPending = null;

        var scale = 1, // Adjust scale as needed for better fit
            canvas = document.getElementById('pdf-canvas'),
            ctx = canvas.getContext('2d');

        // Fungsi render halaman PDF
        function renderPage(num) {
            pageIsRendering = true;
            pdfDoc.getPage(num).then(function(page) {
                var viewport = page.getViewport({ scale: scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                var renderCtx = {
                    canvasContext: ctx,
                    viewport: viewport
                };

                page.render(renderCtx).promise.then(function() {
                    pageIsRendering = false;
                    if (pageNumIsPending !== null) {
                        renderPage(pageNumIsPending);
                        pageNumIsPending = null;
                    }
                });

                document.getElementById('page-num').textContent = num;
            });
        }

        // Fungsi navigasi halaman
        function queueRenderPage(num) {
            if (pageIsRendering) {
                pageNumIsPending = num;
            } else {
                renderPage(num);
            }
        }

        function onPrevPage() {
            if (pageNum <= 1) {
                return;
            }
            pageNum--;
            queueRenderPage(pageNum);
        }

        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) {
                return;
            }
            pageNum++;
            queueRenderPage(pageNum);
        }

        // Muat PDF
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        });

        // Tambahkan event listener untuk tombol navigasi
        document.getElementById('prev-page').addEventListener('click', onPrevPage);
        document.getElementById('next-page').addEventListener('click', onNextPage);
    </script>
</body>
</html>
