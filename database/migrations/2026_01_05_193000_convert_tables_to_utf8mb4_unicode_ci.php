<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = DB::select('SHOW FULL TABLES');
        $databaseName = config('database.connections.mysql.database');
        $key = "Tables_in_{$databaseName}";
        $typeKey = "Table_type";

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            $tableArray = (array) $table;
            $tableName = reset($tableArray);
            $tableType = next($tableArray);

            // Skip migrations table and Views
            if ($tableName === 'migrations' || strtoupper($tableType) === 'VIEW') {
                continue;
            }

            DB::statement("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is complex because we don't know exactly what the previous collation was for each table.
        // Assuming the previous state was utf8mb4_0900_ai_ci based on the user's context.

        $tables = DB::select('SHOW TABLES');
        $databaseName = config('database.connections.mysql.database');
        $key = "Tables_in_{$databaseName}";

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            $tableName = $table->$key ?? reset($table);

            if ($tableName === 'migrations') {
                continue;
            }

            DB::statement("ALTER TABLE `{$tableName}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
