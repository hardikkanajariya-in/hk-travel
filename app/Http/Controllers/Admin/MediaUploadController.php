<?php

namespace App\Http\Controllers\Admin;

use App\Core\Storage\StorageManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Tiny upload endpoint behind /admin so the in-house <x-ui.image-picker>
 * Blade component can save user-uploaded images without forcing every
 * parent Livewire component to enable WithFileUploads.
 *
 * Files are written through StorageManager so the same call site works
 * unchanged when the project later swaps in S3/Spaces/GCS — see the
 * `=== Storage` section of implementation.md (StorageManager wraps the
 * Laravel filesystem and currently exposes only the local `public` disk;
 * cloud adapters are scaffolded but disabled in this release).
 */
class MediaUploadController extends Controller
{
    public function __construct(protected StorageManager $storage) {}

    public function image(Request $request): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['message' => 'Please sign in again to upload files.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:5120'],
            'folder' => ['nullable', 'string', 'max:64', 'regex:/^[a-z0-9_\-\/]+$/i'],
        ], [
            'file.required' => 'Please pick an image to upload.',
            'file.image' => 'That file isn’t an image — please pick a JPG, PNG, GIF, WebP or SVG.',
            'file.mimes' => 'Allowed image types are JPG, PNG, GIF, WebP and SVG.',
            'file.max' => 'Images must be 5 MB or smaller.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $folder = trim((string) $request->input('folder', 'media'), '/');
        $folder = $folder === '' ? 'media' : $folder;

        $file = $request->file('file');
        $name = Str::uuid()->toString().'.'.strtolower($file->getClientOriginalExtension() ?: 'bin');
        $path = $file->storeAs($folder, $name, ['disk' => 'public']);

        $disk = $this->storage->publicDisk();
        $url = '/storage/'.$path;
        try {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
            $publicDisk = Storage::disk(config('hk.storage.public_disk', 'public'));
            $url = $publicDisk->url($path);
        } catch (\Throwable) {
            // Local public disk fall-back already set above.
        }
        unset($disk);

        return response()->json([
            'url' => $url,
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
        ]);
    }
}
