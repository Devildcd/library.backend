<?php

namespace App\Http\Controllers;

use App\Models\ImagenFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ImagenFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $libros = ImagenFile::all();
        return response()-> json($libros);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validators = Validator::make(request()->all(), ImagenFile::rules());
        if ($validators->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'error' => $validators->errors()
            ]);
        }
        $idEntrado = $request->libro_id;
        $imagenFile = ImagenFile::where('libro_id', $idEntrado)->first();
        if ($imagenFile) {
            if ($imagenFile->imagen != '') {
                return response()->json([
                    'message' => 'El libro ya tiene una imagen asociada'
                ]);
            }
        } else {
            $imagenFile = ImagenFile::create(request()->all());
            if ($request->hasFile('imagen')) {
                $path = $request->file('imagen')->store('public/img');
                $imagenFile->imagen = $path;
                $imagenFile->save();
            }
        }
        return response()->json([
            'message' => 'Imagen creada exitosamente',
            'imagen' => $imagenFile
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $imagenFile = ImagenFile::find($id);

        if(!$imagenFile) {
            return response()-> json([
                'message'=> 'Imagen no encontrado'
            ], 404);
        }

        return response()-> json($imagenFile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $imagenFile = ImagenFile::where('id', $id)->first();
         
        $url = $imagenFile->imagen;
        
        Storage::delete($url);
        $imagenFile->delete();
    }

    
}
