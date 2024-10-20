<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = Testimonial::latest()->get();
        \Log::info('Testimonials in index: ' . $testimonials->count());
        return view('testi.index', compact('testimonials'));
    }

    public function create()
    {
        return view('testi.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'review' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        try {
            $testimonial = Testimonial::create($validatedData);
            \Log::info('Testimonial created: ' . $testimonial->id);
            return redirect()->route('testimonials.index')->with('success', 'Testimonial berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error('Error creating testimonial: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan testimonial.');
        }
    }
}