<?php

namespace App\Http\Controllers;

use App\Models\Donat;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonatController extends Controller
{
    public function index(Request $request)
    {
        $donats = Donat::with('category')->get();
        if ($request->wantsJson()) {
            return response()->json($donats);
        }
        return view('donat.index', compact('donats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('donat.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store('donats', 'public');
        }

        $donat = Donat::create($validated);

        if ($request->wantsJson()) {
            return response()->json($donat->fresh('category'), 201);
        }

        return redirect()->route('donat.index')->with('success', 'Donat berhasil ditambahkan');
    }

    public function edit($id)
    {
        $donat = Donat::findOrFail($id);
        $categories = Category::all();
        return view('donat.edit', compact('donat', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $donat = Donat::findOrFail($id);

        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'gambar'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('gambar')) {
            if ($donat->gambar && Storage::disk('public')->exists($donat->gambar)) {
                Storage::disk('public')->delete($donat->gambar);
            }
            $validated['gambar'] = $request->file('gambar')->store('donats', 'public');
        }

        $donat->update($validated);

        if ($request->wantsJson()) {
            return response()->json($donat->fresh('category'));
        }

        return redirect()->route('donat.index')->with('success', 'Donat berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $donat = Donat::findOrFail($id);

        if ($donat->gambar && Storage::disk('public')->exists($donat->gambar)) {
            Storage::disk('public')->delete($donat->gambar);
        }

        $donat->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'deleted']);
        }

        return redirect()->back()->with('success', 'Donat berhasil dihapus');
    }
}
