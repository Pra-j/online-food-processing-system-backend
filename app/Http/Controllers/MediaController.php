<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    /**
     * Upload a single file and return media record
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif,svg|max:10240', // 10MB
        ]);

        $file = $request->file('file');

        // Organize by model type / date (optional)
        $folder = 'products/' . date('Y') . '/' . date('m');

        // Generate filename
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();

        // Save file to public disk
        $path = $file->storeAs($folder, $filename, 'public');

        $media = Media::create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'disk' => 'public',
        ]);

        return response()->json([
            'message' => 'Uploaded',
            'media' => $media,
            'url' => $media->url,
        ], 201);
    }

    /**
     * Delete a media record and file
     */
    public function destroy(Media $media): JsonResponse
    {
        Storage::disk($media->disk)->delete($media->file_path);
        $media->delete();

        return response()->json(['message' => 'Deleted'], 200);
    }
}
