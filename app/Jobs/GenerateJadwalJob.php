<?php
namespace App\Jobs;

use App\Helpers\GeneticScheduler;
use App\Models\Waktu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class GenerateJadwalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function handle(SchedulerService $service)
    {
        [$bestSchedule, $conflicts, $skipped, $fitness] = $service->generate($this->params);
        $saved = $service->save($bestSchedule);

        Log::info("Jadwal disimpan: {$saved} entri. Fitness: {$fitness}");

        // Simpan progress ke Redis, event broadcasting, dsb.
        // Broadcast progress bisa ditambahkan di sini (Opsional)
    }
}

