<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BackupRun;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BackupAdminController extends Controller
{
    public function index()
    {
        $backups = BackupRun::query()
            ->with('requester')
            ->latest()
            ->paginate(30);

        return view('admin.backups.index', compact('backups'));
    }

    public function requestBackup(Request $request)
    {
        $validated = $request->validate([
            'backup_type' => ['required', 'in:database,files,full'],
        ]);

        do {
            $number = 'BKP-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(5));
        } while (BackupRun::where('backup_number', $number)->exists());

        BackupRun::create([
            'backup_number' => $number,
            'backup_type' => $validated['backup_type'],
            'status' => 'queued',
            'requested_by' => auth()->id(),
        ]);

        return back()->with(
            'success',
            'تم تسجيل طلب النسخة الاحتياطية. التنفيذ الفعلي سيربط ببيئة الاستضافة في مرحلة النشر.'
        );
    }
}
