<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ClearNewsPaginationTokens extends Command
{
    protected $signature = 'news:clear-pagination-tokens';
    protected $description = 'Cancella tutti i token di paginazione salvati per le chiamate NewsData';

    public function handle()
    {
        $this->info('Pulizia token di paginazione...');

        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $keys = Redis::keys('*_page_*');

            if (empty($keys)) {
                $this->info('Nessun token da rimuovere.');
                return;
            }

            foreach ($keys as $key) {
                Redis::del($key);
                $this->line("Eliminato: $key");
            }

            $this->info('Tutti i token di paginazione sono stati rimossi!');
        } else {
            $this->warn('⚠ Il comando funziona solo con Redis come sistema di cache.');
        }
    }
}
