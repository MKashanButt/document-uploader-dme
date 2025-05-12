<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('user')) {
            $documents = Document::with('user')
                ->where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $documents = Document::with('user')
                ->orderBy('id', 'desc')
                ->get();
        }


        return view('dashboard', compact('documents'));
    }

    public function store(StoreDocumentRequest $request)
    {
        $uploadedFiles = [];

        foreach ($request->file('document') as $file) {
            $path = $file->store('documents', 'public');

            $uploadedFiles[] = Document::create([
                'path' => $path,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('dashboard.index')
            ->with('success', count($uploadedFiles) . ' document(s) uploaded successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $documents = Document::with('user') // Eager load the user relationship
            ->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard', compact('documents'));
    }

    public function destroy(Document $document)
    {
        if (Auth::user()->hasRole('user')) {
            return redirect()->route('dashboard.index')
                ->with('error', 'You do not have permission to delete this document.');
        }

        Log::info("Deleting document path: " . $document->path);
        if ($document->path && Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        } else {
            Log::warning("File missing at path: " . $document->path);
        }

        $document->delete();

        return redirect()->route('dashboard.index')
            ->with('success', 'Document deleted');
    }
}
