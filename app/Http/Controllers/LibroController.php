<?php

namespace App\Http\Controllers;

use App\Models\DocFile;
use App\Models\ImagenFile;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LibroController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $libros = Libro::with(['imagen', 'documento'])
                             ->orderBy('autor', 'asc')
                             ->get();

        return response()-> json($libros);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Libro::rules());
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'error' => $validator->errors()
            ], 422);
        }

        // Validando que el campo código no se repita en la BD
        $libroExistente = Libro::where('codigo', $request->codigo)->first();
        if ($libroExistente) {
            return response()->json([
                'message' => 'El código introducido ya se encuentra en la Base de Datos',
                'codigo' => 'El código ya existe'
            ], 422);
        }

        $libro = new Libro;
        $libro->fill(request()->all());
        $libro->save();

        if ($request->has('tipo') && $request->tipo === 'digital') {
            $libro->ubicacion = null; 
            $libro->url = null;
            $libro->save();
        }

        else if ($request->has('tipo') && $request->tipo === 'físico') {
            $libro->url = null;
            $libro->descargable = false;
            $libro->save();
        }

        else if ($request->has('tipo') && $request->tipo === 'externo') {
            $libro->ubicacion = null;
            $libro->descargable = false;
            $libro->save();
        }

        else {
            $libro->save();
        }

        // Algoritmo para cambiar el libro principal
        // Verificar si el libro es marcado como principal
        if ($libro->principal) {
            // Obtener todos los libros marcados como principal excepto el libro recién creado
            $librosPrincipales = Libro::where('principal', true)
                ->where('id', '!=', $libro->id)
                ->get();
            // Actualizar la propiedad "principal" de los libros a falso
            foreach ($librosPrincipales as $libroPrincipal) {
                $libroPrincipal->principal = false;
                $libroPrincipal->save();
            }
            // Marcar el libro recién creado como principal
            $libro->principal = true;
            $libro->save();
        }
    
    return response()->json([
        'message' => 'Libro creado exitosamente',
        'libro' => $libro
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $libro = Libro::with(['imagen', 'documento'])->find($id);

        if(!$libro) {
            return response()-> json([
                'message'=> 'Libro no encontrado'
            ], 404);
        }

        return response()-> json($libro);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $libro = Libro::find($id);

        $validator = Validator::make($request->all(), Libro::rules());
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'error' => $validator->errors()
            ], 422);
        }

        // Validando que el campo código no se repita en la BD
        if ($request->has('codigo')) {
            $libroExistente = Libro::where('codigo', $request->codigo)->where('id', '!=', $id)->first();
            if ($libroExistente) {
                return response()->json([
                    'message' => 'El código introducido ya se encuentra en la Base de Datos'
                ], 422);
            }
        }

        // Actualizar los campos del libro
        $libro->update($request->all());

        // Verificar si el tipo de libro ha cambiado a "digital, físico o externo"
        if ($request->has('tipo') && $request->tipo === 'digital') {
            $libro->ubicacion = null; 
            $libro->url = null;
            $libro->save();
        }

        if ($request->has('tipo') && $request->tipo === 'físico') {
            $libro->url = null;
            $libro->descargable = false;
            $libro->save();
        }

        if ($request->has('tipo') && $request->tipo === 'externo') {
            $libro->ubicacion = null;
            $libro->descargable = false;
            $libro->save();
        }

        if ($request->has('tipo') && ($request->tipo === 'externo' || $request->tipo === 'físico')) {
            if ($libro->documento) {
                $url = $libro->documento->doc;
                Storage::delete($url);
                $libro->documento->destroy($libro->documento->id); // Eliminar el objeto completo de la relación "documento"
            }
        }

        // Algoritmo para cambiar el libro principal
        // Verificar si el libro es marcado como principal
        if ($libro->principal) {
            // Obtener todos los libros marcados como principal excepto el libro recién creado
            $librosPrincipales = Libro::where('principal', true)
                ->where('id', '!=', $libro->id)
                ->get();
            // Actualizar la propiedad "principal" de los libros a falso
            foreach ($librosPrincipales as $libroPrincipal) {
                $libroPrincipal->principal = false;
                $libroPrincipal->save();
            }
            // Marcar el libro recién creado como principal
            $libro->principal = true;
            $libro->save();
        }
    
    return response()->json([
        'message' => 'Libro actualizado exitosamente',
        'libro' => $libro
    ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $libro = Libro::find($id);
        $imagenFile = ImagenFile::find($id);
        $docFile = DocFile::find($id);

        if (!$libro) {
            return response()->json([
                'message' => 'Libro no encontrado'
            ], 404);
        }
        $imagenFile = ImagenFile::where('libro_id', $id)->first();
        if ($imagenFile) {
            $url = $imagenFile->imagen;
            Storage::delete($url);
            $imagenFile->delete();
        }

        $docFile = DocFile::where('libro_id', $id)->first();
        if ($docFile) {
            $path = $docFile->doc;
            Storage::delete($path);
            $docFile->delete();
        }

        $libro->delete();


        return response()->json([
            'message' => 'Libro eliminado correctamente',
            'trabajador' => $libro
        ]);
    }

    public function showPrincipalBook(){
        
        $libros = Libro::where('principal', true)
                              ->with(['imagen', 'documento'])
                              -> first();
        return $libros;
        
    }

}
