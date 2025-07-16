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

    protected $requirements, $rooms, $waktuList;

    public function __construct($requirements, $rooms, $waktuList)
    {
        $this->requirements = $requirements;
        $this->rooms = $rooms;
        $this->waktuList = $waktuList;
    }

    public function handle()
    {
        try {
            // Update status ke cache
            Cache::put("jadwal_status_{$this->userId}", 'running', 1800);

            $waktuList = Waktu::all(); // Atau sesuai relasi
            $scheduler = new GeneticScheduler(
                $this->requirements,
                $this->rooms,
                $waktuList,
                100, 0.8, 0.2, 300,
                $this->mapelJamPerMinggu,
                $this->maxGuruJam
            );

            $result = $scheduler->run();

            // Simpan ke DB
            foreach ($result['jadwal'] as $item) {
                \App\Models\Jadwal::create($item);
            }

            // Sukses
            Cache::put("jadwal_status_{$this->userId}", 'completed', 1800);
            Cache::put("jadwal_fitness_{$this->userId}", $result['fitness'], 1800);
        } catch (\Throwable $e) {
            Log::error("Generate Jadwal Job error: " . $e->getMessage());
            Cache::put("jadwal_status_{$this->userId}", 'failed', 1800);
        }
    }

}
