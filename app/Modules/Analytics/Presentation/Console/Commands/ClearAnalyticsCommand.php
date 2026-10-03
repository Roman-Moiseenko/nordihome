<?php

namespace App\Modules\Analytics\Presentation\Console\Commands;

use App\Console\CreatesApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Полная очистка данных аналитики.
 *
 * Удаляет все записи из append-only таблиц analytics_* и агрегатов.
 * Таблицы перечислены в порядке удаления: дочерние — первыми, родительские — последними.
 */
class ClearAnalyticsCommand extends Command
{
    use CreatesApplication;

    protected $signature = 'analytics:clear
                            {--force : Не запрашивать подтверждение}';

    protected $description = 'Полная очистка данных аналитики (все таблицы analytics_*)';

    /**
     * Таблицы модуля Analytics в порядке очистки (дочерние — первыми).
     */
    private const array TABLES = [
        'analytics_exits',
        'analytics_paths',
        'analytics_actions',
        'analytics_searches',
        'analytics_page_views',
        'analytics_sessions',
        'analytics_visitors',
        'analytics_popular_searches',
        'analytics_page_daily',
        'analytics_sources_daily',
    ];

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm('Удалить ВСЮ историю аналитики? Действие необратимо.')) {
            $this->info('Операция отменена.');
            return self::SUCCESS;
        }

        $this->info('Начинаем очистку аналитики...');

        Schema::disableForeignKeyConstraints();

        try {
            foreach (self::TABLES as $table) {
                $count = DB::table($table)->count();
                DB::table($table)->truncate();
                $this->info("Очищено записей из {$table}: {$count}");
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->info('Очистка аналитики завершена.');

        return self::SUCCESS;
    }
}
