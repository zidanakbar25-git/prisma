<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Membuat backup database dan mengirimkannya sebagai file SQL.
     * Hanya Kabag yang diperbolehkan.
     */
    public function download(Request $request)
    {
        if ($request->user()->role !== 'kabag') {
            return response()->json([
                'message' => 'Hanya Kabag yang dapat melakukan backup database.',
            ], 403);
        }

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');

        $mysqldump = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

        if (!file_exists($mysqldump)) {
            return response()->json([
                'message' => 'File mysqldump.exe tidak ditemukan.',
            ], 500);
        }

        if (!$database || !$username || !$host || !$port) {
            return response()->json([
                'message' => 'Konfigurasi koneksi database belum lengkap.',
            ], 500);
        }

        $backupDirectory = storage_path('app/backups');

        if (
            !is_dir($backupDirectory) &&
            !mkdir($backupDirectory, 0755, true) &&
            !is_dir($backupDirectory)
        ) {
            return response()->json([
                'message' => 'Direktori backup tidak dapat dibuat.',
            ], 500);
        }

        $fileName = 'backup-' . $database . '-' . now()->format('Y-m-d-H-i-s') . '.sql';
        $filePath = $backupDirectory . DIRECTORY_SEPARATOR . $fileName;

        $command = '"' . $mysqldump . '"'
            . ' --host=' . escapeshellarg($host)
            . ' --port=' . escapeshellarg((string) $port)
            . ' --user=' . escapeshellarg($username);

        if ($password !== null && $password !== '') {
            $command .= ' --password=' . escapeshellarg($password);
        }

        $command .= ' --result-file=' . escapeshellarg($filePath)
            . ' ' . escapeshellarg($database);

        $output = [];
        $exitCode = 1;

        try {
            exec($command, $output, $exitCode);
        } catch (\Throwable $exception) {
            Log::error('API backup database gagal dijalankan.', [
                'error' => $exception->getMessage(),
            ]);

            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            return response()->json([
                'message' => 'Backup database gagal dijalankan.',
            ], 500);
        }

        if (
            $exitCode !== 0 ||
            !is_file($filePath) ||
            filesize($filePath) === 0
        ) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            Log::error('API backup database gagal dibuat.', [
                'database' => $database,
                'exit_code' => $exitCode,
                'output' => $output,
            ]);

            return response()->json([
                'message' => 'Backup gagal dibuat. Periksa konfigurasi MySQL dan log aplikasi.',
            ], 500);
        }

        try {
            ActivityLog::create([
                'user_id' => $request->user()->id,
                'action' => 'Backup Database API',
                'description' => 'Membuat dan mengunduh backup database "'
                    . $database . '" melalui API.',
            ]);
        } catch (\Throwable $exception) {
            @unlink($filePath);

            Log::error('Gagal mencatat audit log backup API.', [
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'message' => 'Backup dibuat, tetapi pencatatan audit log gagal. Unduhan dibatalkan.',
            ], 500);
        }

        return response()
            ->download($filePath, $fileName, [
                'Content-Type' => 'application/sql',
            ])
            ->deleteFileAfterSend(true);
    }
}
