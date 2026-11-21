<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Убираем холдинг (OrganizationHolding больше не нужен).
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'holding_id')) {
                $table->dropConstrainedForeignId('holding_id');
            }
            if (Schema::hasColumn('organizations', 'default')) {
                $table->dropColumn('default');
            }
        });
        Schema::dropIfExists('organization_holdings');

        // 2. legal_address: json -> string.
        Schema::table('organizations', function (Blueprint $table) {
            $table->renameColumn('legal_address', 'legal_address_json');
            $table->string('legal_address')->default('')->after('legal_address_json');
        });

        // 3. chief: json -> string.
        Schema::table('organizations', function (Blueprint $table) {
            $table->renameColumn('chief', 'chief_json');
            $table->string('chief')->default('')->after('chief_json');
        });

        // 4. Отдельный VO Geo для координат фактического адреса.
        Schema::table('organizations', function (Blueprint $table) {
            $table->json('geo')->nullable()->after('actual_address');
        });

        // 5. Контакт организации fullname: json -> string.
        Schema::table('organization_contacts', function (Blueprint $table) {
            $table->renameColumn('fullname', 'fullname_json');
            $table->string('fullname')->default('')->after('fullname_json');
        });

        // 6. Перенос данных.
        DB::table('organizations')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $legal = json_decode((string) ($row->legal_address_json ?? '{}'), true) ?? [];
                $actual = json_decode((string) ($row->actual_address ?? '{}'), true) ?? [];
                $chief = json_decode((string) ($row->chief_json ?? '{}'), true) ?? [];

                DB::table('organizations')->where('id', $row->id)->update([
                    'legal_address' => (string) ($legal['address'] ?? ''),
                    'actual_address' => json_encode([
                        'country' => '',
                        'region' => (string) ($actual['region'] ?? ''),
                        'city' => (string) ($actual['city'] ?? ''),
                        'street' => (string) ($actual['address'] ?? ''),
                        'postalCode' => (string) ($actual['post'] ?? ''),
                        'regionCode' => null,
                    ]),
                    'geo' => json_encode([
                        'latitude' => (string) ($actual['latitude'] ?? ''),
                        'longitude' => (string) ($actual['longitude'] ?? ''),
                    ]),
                    'chief' => trim(implode(' ', array_filter([
                        (string) ($chief['surname'] ?? ''),
                        (string) ($chief['firstname'] ?? ''),
                        (string) ($chief['secondname'] ?? ''),
                    ]))),
                ]);
            }
        });

        DB::table('organization_contacts')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                $fullname = json_decode((string) ($row->fullname_json ?? '{}'), true) ?? [];

                DB::table('organization_contacts')->where('id', $row->id)->update([
                    'fullname' => trim(implode(' ', array_filter([
                        (string) ($fullname['surname'] ?? ''),
                        (string) ($fullname['firstname'] ?? ''),
                        (string) ($fullname['secondname'] ?? ''),
                    ]))),
                ]);
            }
        });

        // 7. Удаляем старые json-колонки.
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('legal_address_json');
            $table->dropColumn('chief_json');
        });
        Schema::table('organization_contacts', function (Blueprint $table) {
            $table->dropColumn('fullname_json');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['geo', 'legal_address', 'chief', 'default']);
            $table->json('legal_address')->nullable();
            $table->json('chief')->nullable();
            $table->boolean('default')->default(false);
            $table->foreignId('holding_id')->nullable()->constrained('organization_holdings')->onDelete('set null');
        });

        Schema::table('organization_contacts', function (Blueprint $table) {
            $table->dropColumn('fullname');
            $table->json('fullname')->nullable();
        });
    }
};
