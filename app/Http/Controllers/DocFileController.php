<?php

namespace App\Http\Controllers;

use App\Models\DocFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validators = Validator::make(request()->all(), DocFile::rules());
        if ($validators->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'error' => $validators->errors()
            ]);
        }
        $idEntrado = $request->libro_id;
        $docFile = DocFile::where('libro_id', $idEntrado)->first();
        if ($docFile) {
            if ($docFile->doc != '') {
                return response()->json([
                    'message' => 'El libro ya tiene un documento asociado'
                ]);
            }
        } else {
            $docFile = DocFile::create(request()->all());
            if ($request->hasFile('doc')) {
                $extension = $request->file('doc')->getClientOriginalExtension();
                $fileName = $docFile->id . '_' . $request->file('doc')->getClientOriginalName();
                $path = $request->file('doc')->storeAs('public/documents', $fileName);
                $docFile->doc = $path;
                $docFile->save();
            }
        }
        return response()->json([
            'message' => 'Documento creado exitosamente',
            'doc' => $docFile
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $docFile = DocFile::find($id);

        if(!$docFile) {
            return response()-> json([
                'message'=> 'Imagen no encontrado'
            ], 404);
        }

        return response()-> json($docFile);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $docFile = DocFile::where('id', $id)->first();
         
        $url = $docFile->doc;
        
        Storage::delete($url);
        $docFile->delete();
    }


    /**
     * Download the specified resource from storage.
     */
    public function downloadDoc(DocFile $docFile)
    {
        return response()-> download(public_path(Storage::url($docFile->doc)), $docFile->title);
    }


    /**
     * Read the specified resource from storage.
     */
    public function readDoc($id)
{
    $docFile = DocFile::find($id);

    if (!$docFile) {
        return response()->json([
            'message' => 'Documento no encontrado'
        ], 404);
    }

    $path = $docFile->doc;
    $content = Storage::get($path);

    return response()->json([
        'message' => 'Documento leído exitosamente',
        'content' => $content
    ]);
}

public function openDocument($id)
{
    $docFile = DocFile::find($id);
    if (!$docFile) {
        return response()->json([
            'message' => 'Documento no encontrado'
        ], 404);
    }
    $path = $docFile->doc;
    
    // Check if the file exists
    if (!Storage::exists($path)) {
        return response()->json([
            'message' => 'Archivo no encontrado'
        ], 404);
    }

    // Get the file content
    $fileContent = Storage::get($path);
    
    // Determine the MIME type of the file
    $mimeType = Storage::mimeType($path);
    
    // Return the file as response
    return response($fileContent, 200)->header('Content-Type', $mimeType);
}


}
