<?php

namespace App\Services;

use App\Models\SystemHealthCheck;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SystemHealthService
{
    public function runAll(): array
    {
        return [
            $this->database(),
            $this->cache(),
            $this->storage(),
        ];
    }

    public function database(): SystemHealthCheck
    {
        $started = microtime(true);

        try {
            DB::select('select 1');

            return $this->save(
                'database',
                'قاعدة البيانات',
                'healthy',
                $started,
                'الاتصال بقاعدة البيانات يعمل.'
            );
        } catch (Throwable $e) {
            return $this->save(
                'database',
                'قاعدة البيانات',
                'critical',
                $started,
                $e->getMessage()
            );
        }
    }

    public function cache(): SystemHealthCheck
    {
        $started = microtime(true);

        try {
            $key = 'health_check_' . uniqid();
            Cache::put($key, 'ok', 30);
            $ok = Cache::get($key) === 'ok';
            Cache::forget($key);

            return $this->save(
                'cache',
                'التخزين المؤقت',
                $ok ? 'healthy' : 'warning',
                $started,
                $ok ? 'Cache يعمل.' : 'تعذر التحقق من Cache.'
            );
        } catch (Throwable $e) {
            return $this->save(
                'cache',
                'التخزين المؤقت',
                'warning',
                $started,
                $e->getMessage()
            );
        }
    }

    public function storage(): SystemHealthCheck
    {
        $started = microtime(true);

        try {
            $path = 'health-checks/' . uniqid() . '.txt';
            Storage::disk(config('filesystems.default'))->put($path, 'ok');
            $ok = Storage::disk(config('filesystems.default'))->exists($path);
            Storage::disk(config('filesystems.default'))->delete($path);

            return $this->save(
                'storage',
                'التخزين',
                $ok ? 'healthy' : 'warning',
                $started,
                $ok ? 'الكتابة والقراءة تعمل.' : 'تعذر التحقق من التخزين.'
            );
        } catch (Throwable $e) {
            return $this->save(
                'storage',
                'التخزين',
                'critical',
                $started,
                $e->getMessage()
            );
        }
    }

    private function save(
        string $key,
        string $name,
        string $status,
        float $started,
        ?string $message = null
    ): SystemHealthCheck {
        return SystemHealthCheck::create([
            'check_key' => $key,
            'check_name' => $name,
            'status' => $status,
            'response_time_ms' => (int) round(
                (microtime(true) - $started) * 1000
            ),
            'message' => $message,
            'checked_at' => now(),
        ]);
    }
}
