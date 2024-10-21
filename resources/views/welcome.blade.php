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
        .typing-indicator {
        display: flex;
        align-items: center;
        padding: 10px;
    }
    .typing-indicator span {
        height: 10px;
        width: 10px;
        float: left;
        margin: 0 1px;
        background-color: #9E9EA1;
        display: block;
        border-radius: 50%;
        opacity: 0.4;
    }
    .typing-indicator span:nth-of-type(1) {
        animation: 1s blink infinite 0.3333s;
    }
    .typing-indicator span:nth-of-type(2) {
        animation: 1s blink infinite 0.6666s;
    }
    .typing-indicator span:nth-of-type(3) {
        animation: 1s blink infinite 0.9999s;
    }
    @keyframes blink {
        50% {
            opacity: 1;
        }
    }
    .ai-message {
        white-space: pre-wrap;
        word-wrap: break-word;
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

            <div id="aiChat" class="fixed bottom-5 right-5 z-40">
                <button id="chatToggle" class="bg-orange-500 text-white p-3 rounded-full shadow-lg hover:bg-orange-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </button>
                <div id="chatWindow" class="hidden bg-white rounded-lg shadow-xl w-80 h-96 flex flex-col">
                    <div class="bg-orange-500 text-white p-4 rounded-t-lg relative">
                        <h3 class="text-lg font-semibold">AI Assistant</h3>
                    </div>
                    <div id="chatMessages" class="flex-grow p-4 overflow-y-auto"></div>
                    <div class="p-4 border-t">
                        <form id="chatForm" class="flex">
                            <input type="text" id="userInput" class="flex-grow border rounded-l-lg p-2 focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Tanya sesuatu...">
                            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-r-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </form>
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

        <button id="scrollToTopBtn" class="hidden fixed bottom-5 right-5 z-40 p-3 bg-orange-500 text-white rounded-full shadow-lg hover:bg-orange-600 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </button>

        <section id="testimonials" class="bg-gray-100 py-12 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-black-800 mb-8 text-center">Testimonials</h2>
        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <a href="{{ route('testimonials.create') }}" class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-2 flex items-center justify-center">
                <div class="p-6 flex flex-col items-center justify-center h-full">
                    <div class="w-14 h-14 bg-orange-600 rounded-full flex items-center justify-center text-white mb-4 transition duration-300 ease-in-out transform hover:scale-110">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <p class="text-gray-600 font-semibold">Tambah Testimonial</p>
                </div>
            </a>
            @foreach($testimonials as $testimonial)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-2">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4">
                                {{ strtoupper(substr($testimonial->name, 0, 1)) }}
                            </div>
                            <h5 class="font-bold text-xl text-gray-800">{{ $testimonial->name }}</h5>
                        </div>
                        <p class="text-gray-600 mb-4">"{{ $testimonial->review }}"</p>
                        <div class="flex items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $testimonial->rating ? 'text-orange-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm text-gray-600">{{ $testimonial->rating }}.0</span>
                        </div>
                        <p class="text-sm text-gray-500">{{ $testimonial->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

        <footer class="bg-gray-100">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500 text-sm">
                    &copy; {{ date('Y') }} Finestopia. All rights reserved.
                </p>
            </div>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const navbar = document.getElementById('navbar');
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');
        const chatToggle = document.getElementById('chatToggle');
        const chatWindow = document.getElementById('chatWindow');
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const userInput = document.getElementById('userInput');
        const aiChat = document.getElementById('aiChat');

        chatToggle.addEventListener('click', () => {
            chatWindow.classList.toggle('hidden');
            chatToggle.classList.toggle('hidden');
        });

        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.classList.add('absolute', 'top-2', 'right-2', 'text-white', 'text-3xl', 'font-bold', 'focus:outline-none', 'w-8', 'h-8', 'flex', 'items-center', 'justify-center');
        closeButton.style.lineHeight = '1';
        closeButton.addEventListener('click', () => {
            chatWindow.classList.add('hidden');
            chatToggle.classList.remove('hidden');
        });

        const chatHeader = chatWindow.querySelector('.bg-orange-500');
        chatHeader.style.position = 'relative';
        chatHeader.appendChild(closeButton);    

        async function getAIResponse(message) {
            console.log('Fetching AI response for:', message);
            try {
                const response = await fetch(`/proxy-ai?prompt=${encodeURIComponent(message)}`);
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Raw response data:', data);
                return data;
            } catch (error) {
                console.error('Error in getAIResponse:', error);
                throw error;
            }
        }

        function addMessage(sender, message) {
            console.log('Adding message:', { sender, message });
            const messageElement = document.createElement('div');
            messageElement.classList.add('mb-2', 'p-2', 'rounded-lg');
            if (sender === 'user') {
                messageElement.classList.add('bg-orange-100', 'text-right');
                messageElement.textContent = message;
            } else {
                messageElement.classList.add('bg-gray-100', 'ai-message');
                messageElement.innerHTML = formatAIResponse(message);
            }
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            console.log('Message added to chat window');
        }

        function formatAIResponse(message) {
            const paragraphs = message.split('\n').filter(p => p.trim() !== '');
            const formattedParagraphs = paragraphs.map(p => {
                if (p.startsWith('•')) {
                    return `<li>${p.substring(1).trim()}</li>`;
                } else {
                    return `<p>${p}</p>`;
                }
            });
            return formattedParagraphs.join('');
        }

        function addLoadingIndicator() {
            const loadingElement = document.createElement('div');
            loadingElement.classList.add('typing-indicator', 'mb-2', 'p-2', 'rounded-lg', 'bg-gray-100');
            loadingElement.innerHTML = '<span></span><span></span><span></span>';
            chatMessages.appendChild(loadingElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function removeLoadingIndicator() {
            const loadingElement = chatMessages.querySelector('.typing-indicator');
            if (loadingElement) {
                loadingElement.remove();
            }
        }

        async function handleSubmit(e) {
            e.preventDefault();
            const message = userInput.value.trim();
            console.log('Handling submit with message:', message);
            if (message) {
                addMessage('user', message);
                userInput.value = '';
                addLoadingIndicator();
                console.log('Fetching AI response...');
                try {
                    const response = await getAIResponse(message);
                    console.log('AI response received:', response);
                    removeLoadingIndicator();
                    if (response && typeof response === 'string' && response.trim() !== '') {
                        addMessage('ai', response);
                    } else {
                        console.error('Invalid response received from AI');
                        addMessage('ai', 'Maaf, terjadi kesalahan. Respons tidak valid diterima.');
                    }
                } catch (error) {
                    console.error('Error getting AI response:', error);
                    removeLoadingIndicator();
                    addMessage('ai', 'Maaf, terjadi kesalahan. Silakan coba lagi nanti.');
                }
            } else {
                console.log('Empty message, not submitting');
            }
        }

        chatForm.addEventListener('submit', handleSubmit);

        let lastScrollTop = 0;
        let scrollThreshold = 200;
        let isChatOpen = false;

        chatToggle.addEventListener('click', () => {
            isChatOpen = !isChatOpen;
        });

        closeButton.addEventListener('click', () => {
            isChatOpen = false;
        });

        window.onscroll = function() {
            let currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (currentScrollTop > 0) {
                navbar.classList.add('navbar-fixed');
            } else {
                navbar.classList.remove('navbar-fixed');
            }

            if (currentScrollTop > scrollThreshold && !isChatOpen) {
                aiChat.classList.add('hidden');
                scrollToTopBtn.classList.remove('hidden');
            } else {
                aiChat.classList.remove('hidden');
                scrollToTopBtn.classList.add('hidden');
            }

            lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop;
        };

        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        userInput.addEventListener('focus', function() {
            isChatOpen = true;
        });

        userInput.addEventListener('blur', function() {
            setTimeout(() => {
                isChatOpen = false;
            }, 100);
        });
    });
    </script>
</body>
</html>