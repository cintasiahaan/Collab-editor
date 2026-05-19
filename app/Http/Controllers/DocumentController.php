<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRevision;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    // Menampilkan daftar dokumen
    public function index()
    {
        return Inertia::render('Dashboard',[
            'documents' => \App\Models\Document::with('user')
    ->orderBy('created_at', 'desc')
    ->get()
        ]);
    }

    // Proses membuat dokumen baru
    public function store(Request $request)
{
    $document = Auth::user()->documents()->create([

        'title' => 'Untitled Document',

        'slug' => 'untitled-' . Str::random(5),

        'content' => '',
    ]);

    return redirect()->route('documents.show', $document->slug);
}

    // Halaman editor (Nanti akan dipasang Tiptap di sini)
    public function show(Document $document)
    {
        return Inertia::render('Editor', [
            'document' => $document->load(['revisions.user' => function($query) {
                $query->orderBy('created_at', 'desc');
            }
            ])
        ]);
    }

public function update(Request $request, Document $document)
{
    // UPDATE DOCUMENT
    $document->update([

        'title' => $request->title ?? $document->title,

        'content' => $request->content ?? $document->content,
    ]);

    /*
    |--------------------------------------------------------------------------
    | SAVE REVISION
    |--------------------------------------------------------------------------
    */

    // Simpan revision hanya jika content ada
    if ($request->filled('content')) {

        $document->revisions()->create([

            'document_id' => $document->id,

            'user_id' => (string) Auth::id(),

            'content' => $request->content,
        ]);
    }

    return response()->json([

        'message' => 'Tersimpan',
    ]);
}



    public function restore(Document $document, DocumentRevision $revision) 
    {
        $document->update([
            'content'   => $revision->content
        ]);

        return redirect()->back()->with('message', 'Versi berhasil dipulihkan');
    }

    public function deleteAllHistory(Document $document)
    {
    // Hapus semua data di tabel document_revisions yang terhubung dengan id dokumen ini
    $document->revisions()->delete();

    return redirect()->back()->with('message', 'Semua riwayat versi berhasil dihapus');
    }

    public function destroy(Document $document)
    {
        // validasi keamana
        if ($document->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus dokumen ini.');
        }

        $document->delete(Auth::id());

        return redirect()->back()->with('message', 'Dokumen berhasil dihapus.');
    }
}
