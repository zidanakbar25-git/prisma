<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;

class BackupController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );

        return view('backup.index');
    }


    public function download()
    {
        abort_unless(
            auth()->user()->role === 'kabag',
            403
        );


        $database = config(
            'database.connections.mysql.database'
        );

        $username = config(
            'database.connections.mysql.username'
        );

        $password = config(
            'database.connections.mysql.password'
        );

        $host = config(
            'database.connections.mysql.host'
        );

        $port = config(
            'database.connections.mysql.port'
        );


        /*
        |--------------------------------------------------------------------------
        | Lokasi mysqldump Laragon
        |--------------------------------------------------------------------------
        */

        $mysqldump = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';


        /*
        |--------------------------------------------------------------------------
        | Cek mysqldump
        |--------------------------------------------------------------------------
        */

        if (!file_exists($mysqldump)) {

            return redirect()
                ->route('backup.index')
                ->with(
                    'error',
                    'File mysqldump.exe tidak ditemukan pada lokasi konfigurasi.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Backup Directory
        |--------------------------------------------------------------------------
        */

        $backupDirectory = storage_path(
            'app/backups'
        );


        if (!is_dir($backupDirectory)) {

            mkdir(
                $backupDirectory,
                0755,
                true
            );
        }


        /*
        |--------------------------------------------------------------------------
        | File Name
        |--------------------------------------------------------------------------
        */

        $fileName =
            'backup-' .
            $database .
            '-' .
            now()->format('Y-m-d-H-i-s') .
            '.sql';


        $filePath =
            $backupDirectory .
            DIRECTORY_SEPARATOR .
            $fileName;


        /*
        |--------------------------------------------------------------------------
        | Build mysqldump command
        |--------------------------------------------------------------------------
        */

        $command =
            '"' .
            $mysqldump .
            '"' .
            ' --host=' .
            escapeshellarg($host) .
            ' --port=' .
            escapeshellarg($port) .
            ' --user=' .
            escapeshellarg($username);


        if (
            $password !== null &&
            $password !== ''
        ) {

            $command .=
                ' --password=' .
                escapeshellarg($password);
        }


        $command .=
            ' ' .
            escapeshellarg($database) .
            ' > ' .
            escapeshellarg($filePath);


        /*
        |--------------------------------------------------------------------------
        | Execute Backup
        |--------------------------------------------------------------------------
        */

        $output = [];

        $exitCode = null;


        exec(
            $command,
            $output,
            $exitCode
        );


        /*
        |--------------------------------------------------------------------------
        | Check Result
        |--------------------------------------------------------------------------
        */

        if (
            $exitCode !== 0 ||
            !file_exists($filePath) ||
            filesize($filePath) === 0
        ) {

            if (file_exists($filePath)) {

                unlink($filePath);
            }


            return redirect()
                ->route('backup.index')
                ->with(
                    'error',
                    'Backup database gagal dibuat. Pastikan konfigurasi database MySQL sudah benar.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Audit Log
        |--------------------------------------------------------------------------
        */

        ActivityLog::create([
            'user_id' =>
                auth()->id(),

            'action' =>
                'Backup Database',

            'description' =>
                'Membuat dan mengunduh backup database "' .
                $database .
                '".',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Download
        |--------------------------------------------------------------------------
        */

        return response()
            ->download(
                $filePath,
                $fileName
            )
            ->deleteFileAfterSend(true);
    }
}