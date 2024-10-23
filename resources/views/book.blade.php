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
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .custom-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            border-radius: 10px;
        }
        #flipbook-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 120px);
            padding: 20px;
        }
        #flipbook {
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 800px;
            max-height: calc(100vh - 180px);
            display: flex;
            flex-direction: column;
        }
        #pdf-canvas-container {
            flex-grow: 1;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #pdf-canvas {
            max-width: 100%;
            max-height: 100%;
        }
        #pdf-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.8);
        }
        #pdf-controls button {
            margin: 0 10px;
        }
        @media (min-width: 1024px) {
            #flipbook {
                width: auto;
                height: 70vh;
                aspect-ratio: 3/4;
                max-width: none;
                max-height: none;
            }
        }
    </style>
</head>
<body class="antialiased bg-white flex flex-col min-h-screen">
    <nav class="bg-orange-500 shadow-sm w-full">
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
        <div id="flipbook-container">
            <div id="flipbook" class="custom-shadow">
                <div id="pdf-canvas-container">
                    <canvas id="pdf-canvas"></canvas>
                </div>
                <div id="pdf-controls">
                    <button id="prev-page" class="px-4 py-2 bg-orange-600 text-white rounded-md lg:hidden">Previous Page</button>
                    <span class="text-center px-2 lg:hidden">Page: <span id="page-num"></span> / <span id="page-count"></span></span>
                    <button id="next-page" class="px-4 py-2 bg-orange-600 text-white rounded-md lg:hidden">Next Page</button>
                    <button id="view-pdf" class="px-4 py-2 bg-orange-600 text-white rounded-md hidden lg:block">View PDF</button>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    <script>
        var url = '/Finestopia%20Manual%20Book.pdf';
        var pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';

        var pdfDoc = null,
            pageNum = 1,
            pageIsRendering = false,
            pageNumIsPending = null;

        var canvas = document.getElementById('pdf-canvas'),
            ctx = canvas.getContext('2d');

            function getScale() {
    var container = document.getElementById('pdf-canvas-container');
    var containerWidth = container.clientWidth;
    var containerHeight = container.clientHeight;
    
    var pdfWidth = 595;
    var pdfHeight = 842;

    var widthScale = containerWidth / pdfWidth;
    var heightScale = containerHeight / pdfHeight;

    var scale = Math.min(widthScale, heightScale);
    var pixelRatio = window.devicePixelRatio || 1;
    scale *= pixelRatio;

    return scale;
}

function renderPage(num) {
    pageIsRendering = true;
    pdfDoc.getPage(num).then(function(page) {
        var scale = getScale();
        var viewport = page.getViewport({ scale: scale });

        var pixelRatio = window.devicePixelRatio || 1;
        canvas.width = viewport.width * pixelRatio;
        canvas.height = viewport.height * pixelRatio;
        canvas.style.width = viewport.width + "px";
        canvas.style.height = viewport.height + "px";

        var renderContext = {
            canvasContext: ctx,
            viewport: viewport,
            transform: pixelRatio !== 1 ? [pixelRatio, 0, 0, pixelRatio, 0, 0] : null
        };

        var renderTask = page.render(renderContext);

        renderTask.promise.then(function() {
            pageIsRendering = false;
            if (pageNumIsPending !== null) {
                renderPage(pageNumIsPending);
                pageNumIsPending = null;
            }
        });

        document.getElementById('page-num').textContent = num;
            });
        }

        function queueRenderPage(num) {
            if (pageIsRendering) {
                pageNumIsPending = num;
            } else {
                renderPage(num);
            }
        }

        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page-count').textContent = pdfDoc.numPages;
            renderPage(pageNum);
        });

        document.getElementById('prev-page').addEventListener('click', onPrevPage);
        document.getElementById('next-page').addEventListener('click', onNextPage);
        document.getElementById('view-pdf').addEventListener('click', function() {
            window.open(url, '_blank');
        });

        function resizeCanvas() {
            if (pdfDoc) {
                renderPage(pageNum);
            }
        }

        window.addEventListener('resize', resizeCanvas);
    </script>
</body>
</html>