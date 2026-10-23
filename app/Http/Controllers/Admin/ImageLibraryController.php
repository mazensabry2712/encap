<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EncapImageLibrary;
use Illuminate\Http\RedirectResponse;

final class ImageLibraryController extends Controller
{
    public function prepare(EncapImageLibrary $library): RedirectResponse
    {
        $result = $library->prepare();

        return redirect()->route('admin.dashboard')->with('imageLibraryResult', $result);
    }
}
