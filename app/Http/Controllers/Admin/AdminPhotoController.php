<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class AdminPhotoController extends Controller
{
    public function index()
    {
        $totalPhotos = User::whereNotNull('photo')->count();
        $totalUsers = User::count();
        
        return view('admin.photos.index', compact('totalPhotos', 'totalUsers'));
    }

    /**
     * Importation de masse via un fichier ZIP.
     * Le nom du fichier doit être le MATRICULE ou le CIN de l'agent.
     */
    public function importZip(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|mimes:zip|max:51200', // 50Mo max
        ]);

        $zip = new ZipArchive;
        $file = $request->file('zip_file');
        
        if ($zip->open($file->getRealPath()) === TRUE) {
            $extractedPath = storage_path('app/temp-photos-' . Str::random(10));
            $zip->extractTo($extractedPath);
            $zip->close();

            $files = scandir($extractedPath);
            $importedCount = 0;
            $skippedCount = 0;

            foreach ($files as $fileName) {
                if ($fileName === '.' || $fileName === '..') continue;

                $pathInfo = pathinfo($fileName);
                $identifier = $pathInfo['filename']; // Nom du fichier sans extension
                $extension = strtolower($pathInfo['extension'] ?? '');

                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $skippedCount++;
                    continue;
                }

                // Recherche de l'agent par Matricule (ou CIN stocké dans matricule)
                // On utilise strtoupper pour garantir le matching avec les matricules en base
                $identifierUpper = strtoupper(trim($identifier));
                
                $user = User::where('matricule', $identifierUpper)
                            ->orWhere('telephone', $identifier) // Parfois le CIN est dans le téléphone
                            ->first();

                if ($user) {
                    // Supprimer l'ancienne photo si elle existe
                    if ($user->photo) {
                        Storage::disk('public')->delete($user->photo);
                    }

                    // Nouveau chemin final
                    $newFileName = $identifier . '_' . time() . '.' . $extension;
                    $finalSubPath = 'photos_profils/' . $newFileName;
                    
                    Storage::disk('public')->put(
                        $finalSubPath, 
                        file_get_contents($extractedPath . '/' . $fileName)
                    );

                    $user->update(['photo' => $finalSubPath]);
                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            }

            // Nettoyage temporaire
            $this->recursiveRemoveDir($extractedPath);

            return back()->with('success', "Importation terminée : $importedCount photos liées avec succès ($skippedCount ignorées).");
        }

        return back()->with('error', "Impossible d'ouvrir le fichier ZIP.");
    }

    /**
     * Mise à jour manuelle d'une photo pour un utilisateur spécifique.
     */
    public function updateManual(Request $request, User $user)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            $extension = $request->file('photo')->getClientOriginalExtension();
            $fileName = ($user->matricule ?: $user->id) . '_' . time() . '.' . $extension;
            $path = $request->file('photo')->storeAs('photos_profils', $fileName, 'public');

            $user->update(['photo' => $path]);

            return back()->with('success', 'Photo de profil mise à jour.');
        }

        return back()->with('error', 'Aucun fichier reçu.');
    }

    private function recursiveRemoveDir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($objects != "." && $object != "..") {
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                        $this->recursiveRemoveDir($dir. DIRECTORY_SEPARATOR .$object);
                    else
                        unlink($dir. DIRECTORY_SEPARATOR .$object);
                }
            }
            rmdir($dir);
        }
    }
}
