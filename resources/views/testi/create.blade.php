@extends('layouts.testi')

@section('content')
<div class="bg-gray-100 min-h-screen py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Tambah Testimonial</h1>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow" role="alert">
                <p class="font-bold">Error!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow" role="alert">
                <p class="font-bold">Oops! Ada beberapa masalah:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('testimonials.store') }}" method="POST" class="bg-white shadow-lg rounded-lg px-8 pt-6 pb-8 mb-4">
            @csrf
            <div class="mb-6">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama</label>
                <input type="text" class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-orange-500 transition duration-300" id="name" name="name" required value="{{ old('name') }}" placeholder="Masukkan nama Anda">
            </div>
            <div class="mb-6">
                <label for="review" class="block text-gray-700 text-sm font-bold mb-2">Review</label>
                <textarea class="shadow appearance-none border rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-orange-500 transition duration-300" id="review" name="review" rows="4" required placeholder="Bagikan pengalaman Anda">{{ old('review') }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Rating</label>
                <div class="flex items-center">
                    <input type="hidden" name="rating" id="rating" value="{{ old('rating', 0) }}">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-8 h-8 cursor-pointer star-rating {{ $i <= old('rating', 0) ? 'text-orange-400' : 'text-gray-300' }}" data-rating="{{ $i }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    @endfor
                </div>
            </div>
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-110">
                    Kirim Testimonial
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.star-rating');
    const ratingInput = document.getElementById('rating');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const rating = star.dataset.rating;
            ratingInput.value = rating;
            updateStars(rating);
        });

        star.addEventListener('mouseover', () => {
            const rating = star.dataset.rating;
            updateStars(rating);
        });

        star.addEventListener('mouseout', () => {
            const currentRating = ratingInput.value;
            updateStars(currentRating);
        });
    });

    function updateStars(rating) {
        stars.forEach(star => {
            if (star.dataset.rating <= rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-orange-400');
            } else {
                star.classList.remove('text-orange-400');
                star.classList.add('text-gray-300');
            }
        });
    }
});
</script>
@endsection