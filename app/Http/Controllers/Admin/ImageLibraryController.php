<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EncapImageLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ImageLibraryController extends Controller
{
    public function prepare(Request $request, EncapImageLibrary $library): RedirectResponse
    {
        $validated = $request->validate([
            'offset' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);
        $offset = (int) ($validated['offset'] ?? 0);
        $batch = $library->prepare(false, $offset);

        $emptySummary = [
            'downloaded' => 0,
            'kept' => 0,
            'copied' => 0,
            'errors' => [],
        ];
        $summary = $offset === 0
            ? $emptySummary
            : (session('imageLibraryProgress') ?? $emptySummary);

        foreach (['downloaded', 'kept', 'copied'] as $counter) {
            $summary[$counter] = (int) ($summary[$counter] ?? 0) + (int) $batch[$counter];
        }
        $summary['errors'] = array_values(array_unique(array_merge(
            $summary['errors'] ?? [],
            $batch['errors']
        )));
        $summary['offset'] = $batch['offset'];
        $summary['total'] = $batch['total'];
        $summary['next_offset'] = $batch['next_offset'];
        $summary['finished'] = $batch['finished'];

        if ($batch['finished']) {
            session()->forget('imageLibraryProgress');
            $summary['complete'] = $summary['errors'] === [];
            $summary['next_offset'] = 0;
        } else {
            session()->put('imageLibraryProgress', $summary);
            $summary['complete'] = false;
        }

        return redirect()->route('admin.dashboard')->with('imageLibraryResult', $summary);
    }
}
