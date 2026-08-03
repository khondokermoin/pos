<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Hostinger এ exec() disabled থাকায় php artisan storage:link কাজ করে না।
 * এই command PHP native symlink() function ব্যবহার করে — exec() ছাড়াই।
 */
class StorageLinkFix extends Command
{
    protected $signature = 'storage:link-fix';

    protected $description = 'Create storage symlink using PHP native symlink() — works on Hostinger where exec() is disabled';

    public function handle(): int
    {
        $target = storage_path('app/public');
        $link   = public_path('storage');

        // পুরনো symlink বা directory থাকলে সরিয়ে দাও
        if (is_link($link)) {
            unlink($link);
            $this->line("  <comment>Removed old symlink:</comment> {$link}");
        } elseif (is_dir($link)) {
            $this->error("  A real directory exists at {$link}. Please remove it manually first.");
            return self::FAILURE;
        }

        // storage/app/public directory না থাকলে তৈরি করো
        if (! is_dir($target)) {
            mkdir($target, 0755, true);
            $this->line("  <comment>Created target directory:</comment> {$target}");
        }

        // PHP native symlink() দিয়ে absolute path link তৈরি করো
        if (symlink($target, $link)) {
            $this->info('  ✅ Symlink created successfully! (absolute path)');
            $this->line("     Link  : {$link}");
            $this->line("     Target: {$target}");
            $this->newLine();
            $this->info('  Storage URL: ' . config('app.url') . '/storage');
            return self::SUCCESS;
        }

        // absolute path কাজ না করলে relative symlink চেষ্টা করো
        $this->warn('  symlink() failed with absolute path. Trying relative path...');

        $relativeTarget = '../storage/app/public';
        $cwd = getcwd();
        chdir(public_path());

        if (symlink($relativeTarget, 'storage')) {
            chdir($cwd);
            $this->info('  ✅ Relative symlink created successfully!');
            $this->line("     Link  : {$link}");
            $this->line("     Target: {$relativeTarget} (relative)");
            return self::SUCCESS;
        }

        chdir($cwd);

        $this->error('  ❌ Could not create symlink. Both absolute and relative methods failed.');
        $this->newLine();
        $this->warn('  ℹ️  The /storage/{path} PHP fallback route in web.php will serve files automatically.');
        $this->warn('     Files are still accessible via: ' . config('app.url') . '/storage/...');

        return self::FAILURE;
    }
}
