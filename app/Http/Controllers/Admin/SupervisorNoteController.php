<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Media;
use App\Models\Labour;
use App\Models\ShiftLog;
use Barryvdh\DomPDF\PDF;
use App\Models\Supervisor;
use App\Models\LabourShift;
use Illuminate\Http\Request;
use App\Models\SupervisorNote;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\SupervisorNoteRequest;

class SupervisorNoteController extends Controller
{
    public function create(Request $request)
    {
        $noteType = $request->note_type;
        $logDate = $request->log_date;
        $role = $this->getUserRole();

        if (!$logDate && !$noteType) {
            return redirect()
                ->route('supervisors-shift-log.index', [
                    'role' => $role,
                    'date' => date('d-m-Y')
                ])
                ->withErrors(['error' => 'Please select date and note type']);
        }

        $isLocked = $this->isLocked($logDate);

        $isEditable = $role === 'supervisor' && !$isLocked;

        $supervisor_notes = SupervisorNote::with('media')
            ->where('log_date', $logDate)
            ->where('note_type', $noteType)
            ->first();


        return view('admin.supervisor-notes.create', compact('supervisor_notes', 'noteType', 'isEditable'));
    }

    public function store(SupervisorNoteRequest $request)
    {
        $note = SupervisorNote::updateOrCreate([
            'log_date' => $request->log_date,
            'note_type' => $request->note_type,
        ], [
            'note' => $request->note,
            'log_date' => $request->log_date,
            'note_type' => $request->note_type,
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('supervisor_notes', $imageName, 'public');
                $note->media()->create([
                    'url' => Storage::url($path),
                    'level' => 'note-image'
                ]);
            }
        }
        return redirect()
            ->route('supervisors-shift-log.index', [
                'role' => $this->getUserRole(),
                'date' => $request->log_date
            ])
            ->with('success', 'Supervisor note added successfully');
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'image_id' => 'required|exists:media,id',
        ]);

        $image = Media::findOrFail($request->image_id);
        $path = str_replace('/storage/', '', $image->url);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        $image->delete();
        return response()->json(['status' => 'success']);
    }

    public function exportPdf($log_date, $note_type)
    {
        $supervisor_notes = SupervisorNote::with('media')->where('log_date', $log_date)->where('note_type', $note_type)->first();
        $supervisor = Supervisor::where('date', $log_date)->where('shift', $note_type === 'day_shift' ? 'day' : 'night')->first();

        $labours = LabourShift::with('labour')
            ->where('date', $log_date)
            ->where('shift', $note_type === 'day_shift' ? 'day' : 'night')
            ->first();

        if (!$supervisor_notes) {
            return redirect()->back()->with('error', 'Handover note not found');
        }

        $data = [
            'supervisor_notes' => $supervisor_notes,
            'supervisor' => $supervisor,
            'log_date' => $log_date,
            'note_type' => $note_type === 'day_shift' ? 'Day Shift' : 'Night Shift',
            'labours' => $labours,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.handover-note', $data);
        return $pdf->stream('handover-note-' . $log_date . '-' . time() . '.pdf');
    }

    private function getUserRole()
    {
        return Auth::user()->roles->pluck('name')->first();
    }

    private function isLocked($log_date)
    {
        return $isLocked = now()->greaterThan(Carbon::parse($log_date)->addDay()->setTime(6, 0));
    }
}
