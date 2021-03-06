<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class PhotosController extends Controller
{
    public function delete(Request $request)
    {
        $id = $request->input('id');
        $photo = Photo::find($id);

        if ($photo) {
            try {
                $filename = '/photos/' . basename($photo->path);

                if (Storage::disk('public')->exists($filename)) {
                    Storage::disk('public')->delete($filename);
                }

                $photo->delete();
                return response('Photo deleted.', 200);
            } catch (Exception $e) {
                return response('Internal server Error', 500);
            }
        } else {
            return response('Photo not found.', 404);
        }
    }

    public function create(Request $request)
    {
        $photo = new Photo();

        $request->validate([
            'photo' => 'required|image|max:10000',
            'description' => 'string|nullable:max:128',
        ]);

        $photo->description = $request->input('description');
        $photo->imageable_id = $request->input('project_id');
        $photo->imageable_type = 'App\Project';

        $file = $request->file('photo');
        $name =
            \Str::random(90) . now()->format('U') . '.' . $file->extension();

        try {
            $photo->path = $file->storeAs('photos', $name, 'public');

            if (!TinifyController::compress($name)) {
                throw new Exception('Error on image compression');
            }

            $photo->save();

            return redirect()
                ->back()
                ->with(['success' => 'Foto publicada correctamente']);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['error' => 'Lo sentimos, intente de nuevo mas tarde']);
        }
    }

    public function edit(Request $request, $id)
    {
        $photo = Photo::findOrFail($id);

        $request->validate([
            'photo' => 'image|max:10000',
            'description' => 'string|nullable:max:128',
        ]);

        $photo->description = $request->input('description');
        $photo->imageable_id = $request->input('project_id');
        $photo->imageable_type = 'App\Project';

        try {
            if ($request->file('photo')) {
                $file = $request->file('photo');
                $name =
                    \Str::random(90) .
                    now()->format('U') .
                    '.' .
                    $file->extension();

                $fileToDelete = '/photos/' . basename($photo->path);

                if (Storage::disk('public')->exists($fileToDelete)) {
                    Storage::disk('public')->delete($fileToDelete);
                }

                $photo->path = $file->storeAs('photos', $name, 'public');

                if (!TinifyController::compress($name)) {
                    throw new Exception('Error on image compression');
                }
            }

            $photo->save();

            return redirect()
                ->back()
                ->with(['success' => 'Foto publicada correctamente']);
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['error' => 'Lo sentimos, intente de nuevo mas tarde']);
        }
    }

    public function uploadBlob(Request $request)
    {
        $request->validate([
            'photo' => 'required|max:10000',
        ]);

        $file = $request->file('photo');

        try {
            $file = $request->file('photo');
            $name =
                \Str::random(90) .
                now()->format('U') .
                '.' .
                $file->extension();

            $path = $file->storeAs('photos', $name, 'public');

            return response()->json($path, 201);
        } catch (Exception $e) {
            if (App::environment('local')) {
                return response()->json($e->getMessage(), 500);
            } else {
                return response()->json('Algo salió mal.', 500);
            }
        }
    }
}
