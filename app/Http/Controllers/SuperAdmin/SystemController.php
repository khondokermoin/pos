<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    // ── Activity Logs ─────────────────────────────────────────────────────────

    public function logs(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs    = $query->paginate(50)->withQueryString();
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('super-admin.system.logs', compact('logs', 'actions'));
    }

    // ── Database Backup ───────────────────────────────────────────────────────

    public function backup()
    {
        $backupPath = storage_path('app/backups');
        $backups    = [];

        if (File::exists($backupPath)) {
            $backups = collect(File::files($backupPath))
                ->map(fn($file) => [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'path' => $file->getPathname(),
                ])
                ->sortByDesc('date')
                ->values();
        }

        return view('super-admin.system.backup', compact('backups'));
    }

    public function generateBackup()
    {
        try {
            $backupPath = storage_path('app/backups');

            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            $filename = 'backup_' . date('Y_m_d_His') . '.sql';
            $filepath = $backupPath . DIRECTORY_SEPARATOR . $filename;

            $dbConfig = config('database.connections.' . config('database.default'));
            $host     = $dbConfig['host'];
            $port     = $dbConfig['port'] ?? 3306;
            $database = $dbConfig['database'];
            $username = $dbConfig['username'];
            $password = $dbConfig['password'];

            // Build mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            exec($command, $output, $returnCode);

            if ($returnCode !== 0 || !File::exists($filepath) || File::size($filepath) === 0) {
                // Fallback: PHP-based backup using DB queries
                $this->phpBackup($filepath, $database);
            }

            // Log the backup action
            ActivityLog::record('backup', "Database backup created: {$filename}");

            return redirect()->route('superadmin.system.backup')
                ->with('success', "✅ Backup created successfully: {$filename}");
        } catch (\Exception $e) {
            return redirect()->route('superadmin.system.backup')
                ->with('error', '❌ Backup failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup(string $filename)
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (!File::exists($filepath)) {
            abort(404, 'Backup file not found.');
        }

        // Security: only allow .sql files
        if (!str_ends_with($filename, '.sql')) {
            abort(403);
        }

        return response()->download($filepath);
    }

    public function deleteBackup(string $filename)
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (File::exists($filepath) && str_ends_with($filename, '.sql')) {
            File::delete($filepath);
            return back()->with('success', "Backup '{$filename}' deleted.");
        }

        return back()->with('error', 'Backup file not found.');
    }

    // ── System Info ───────────────────────────────────────────────────────────

    public function info()
    {
        $info = [
            'php_version'      => phpversion(),
            'laravel_version'  => app()->version(),
            'server_software'  => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'database_driver'  => DB::connection()->getDriverName(),
            'database_name'    => DB::connection()->getDatabaseName(),
            'storage_used'     => $this->formatBytes(disk_total_space(storage_path()) - disk_free_space(storage_path())),
            'storage_free'     => $this->formatBytes(disk_free_space(storage_path())),
            'storage_total'    => $this->formatBytes(disk_total_space(storage_path())),
            'memory_limit'     => ini_get('memory_limit'),
            'max_upload_size'  => ini_get('upload_max_filesize'),
            'max_post_size'    => ini_get('post_max_size'),
            'timezone'         => config('app.timezone'),
            'app_env'          => config('app.env'),
            'app_debug'        => config('app.debug') ? 'Enabled' : 'Disabled',
            'php_extensions'   => implode(', ', get_loaded_extensions()),
        ];

        return view('super-admin.system.info', compact('info'));
    }

    // ── Update Application ────────────────────────────────────────────────────

    public function update()
    {
        $currentVersion = config('app.version', '1.0.0');
        return view('super-admin.system.update', compact('currentVersion'));
    }

    public function runUpdate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            ActivityLog::record('system_update', 'Application updated: migrations run and caches cleared.');

            return back()->with('success', '✅ Application updated successfully! Migrations run and caches cleared.');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Update failed: ' . $e->getMessage());
        }
    }

    // ── Cache Clear ───────────────────────────────────────────────────────────

    public function cacheClear()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            ActivityLog::record('cache_clear', 'All application caches cleared.');

            return back()->with('success', '✅ All caches cleared successfully! (cache, config, view, route)');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Cache clear failed: ' . $e->getMessage());
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * PHP-based SQL dump fallback (no mysqldump required).
     */
    private function phpBackup(string $filepath, string $database): void
    {
        $handle = fopen($filepath, 'w');

        fwrite($handle, "-- Cloud POS Database Backup\n");
        fwrite($handle, "-- Generated: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- Database: {$database}\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = DB::select('SHOW TABLES');
        $key    = 'Tables_in_' . $database;

        foreach ($tables as $table) {
            $tableName = $table->$key;

            // Table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $createSQL   = $createTable[0]->{'Create Table'};

            fwrite($handle, "-- Table: {$tableName}\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
            fwrite($handle, $createSQL . ";\n\n");

            // Table data
            $rows = DB::table($tableName)->get();
            if ($rows->isNotEmpty()) {
                fwrite($handle, "INSERT INTO `{$tableName}` VALUES\n");
                $rowStrings = [];
                foreach ($rows as $row) {
                    $values = array_map(function ($val) {
                        if ($val === null) return 'NULL';
                        return "'" . addslashes((string) $val) . "'";
                    }, (array) $row);
                    $rowStrings[] = '(' . implode(', ', $values) . ')';
                }
                fwrite($handle, implode(",\n", $rowStrings) . ";\n\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    private function tailFile(string $path, int $lines = 500): string
    {
        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        $startLine  = max(0, $totalLines - $lines);
        $result     = [];

        $file->seek($startLine);
        while (!$file->eof()) {
            $result[] = $file->current();
            $file->next();
        }

        return implode('', $result);
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units  = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes  = max($bytes, 0);
        $pow    = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow    = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
