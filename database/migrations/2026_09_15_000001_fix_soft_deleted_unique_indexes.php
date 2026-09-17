<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            // Drop old non-partial unique indexes / constraints if they exist
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_email_unique');
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_nik_unique');
            DB::statement('DROP INDEX IF EXISTS users_nik_unique');

            DB::statement('ALTER TABLE positions DROP CONSTRAINT IF EXISTS positions_code_unique');
            DB::statement('DROP INDEX IF EXISTS positions_code_unique');

            DB::statement('ALTER TABLE currencies DROP CONSTRAINT IF EXISTS currencies_code_unique');
            DB::statement('DROP INDEX IF EXISTS currencies_code_unique');

            DB::statement('ALTER TABLE corsec_directorates DROP CONSTRAINT IF EXISTS corsec_directorates_code_unique');
            DB::statement('DROP INDEX IF EXISTS corsec_directorates_code_unique');

            DB::statement('ALTER TABLE corsec_senders DROP CONSTRAINT IF EXISTS corsec_senders_code_unique');
            DB::statement('DROP INDEX IF EXISTS corsec_senders_code_unique');

            // Cleanup any existing active duplicates before creating unique indexes
            // Keeps the newest record (highest ID) active, and soft-deletes older duplicates
            DB::statement("
                UPDATE users 
                SET deleted_at = NOW() 
                WHERE id IN (
                    SELECT id FROM (
                        SELECT id, ROW_NUMBER() OVER (PARTITION BY nik ORDER BY id DESC) as rnum
                        FROM users
                        WHERE deleted_at IS NULL AND nik IS NOT NULL AND nik != ''
                    ) t
                    WHERE t.rnum > 1
                )
            ");

            DB::statement("
                UPDATE users 
                SET deleted_at = NOW() 
                WHERE id IN (
                    SELECT id FROM (
                        SELECT id, ROW_NUMBER() OVER (PARTITION BY email ORDER BY id DESC) as rnum
                        FROM users
                        WHERE deleted_at IS NULL AND email IS NOT NULL AND email != ''
                    ) t
                    WHERE t.rnum > 1
                )
            ");

            DB::statement("
                UPDATE positions 
                SET deleted_at = NOW() 
                WHERE id IN (
                    SELECT id FROM (
                        SELECT id, ROW_NUMBER() OVER (PARTITION BY code ORDER BY id DESC) as rnum
                        FROM positions
                        WHERE deleted_at IS NULL AND code IS NOT NULL AND code != ''
                    ) t
                    WHERE t.rnum > 1
                )
            ");

            DB::statement("
                UPDATE currencies 
                SET deleted_at = NOW() 
                WHERE id IN (
                    SELECT id FROM (
                        SELECT id, ROW_NUMBER() OVER (PARTITION BY code ORDER BY id DESC) as rnum
                        FROM currencies
                        WHERE deleted_at IS NULL AND code IS NOT NULL AND code != ''
                    ) t
                    WHERE t.rnum > 1
                )
            ");

            DB::statement("
                UPDATE corsec_directorates 
                SET deleted_at = NOW() 
                WHERE id IN (
                    SELECT id FROM (
                        SELECT id, ROW_NUMBER() OVER (PARTITION BY code ORDER BY id DESC) as rnum
                        FROM corsec_directorates
                        WHERE deleted_at IS NULL AND code IS NOT NULL AND code != ''
                    ) t
                    WHERE t.rnum > 1
                )
            ");

            DB::statement("
                UPDATE corsec_senders 
                SET deleted_at = NOW() 
                WHERE id IN (
                    SELECT id FROM (
                        SELECT id, ROW_NUMBER() OVER (PARTITION BY code ORDER BY id DESC) as rnum
                        FROM corsec_senders
                        WHERE deleted_at IS NULL AND code IS NOT NULL AND code != ''
                    ) t
                    WHERE t.rnum > 1
                )
            ");

            // Re-create as Partial Unique Indexes (filtered on deleted_at IS NULL)
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_email_unique ON users (email) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_nik_unique ON users (nik) WHERE deleted_at IS NULL AND nik IS NOT NULL AND nik != \'\'');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS positions_code_unique ON positions (code) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS currencies_code_unique ON currencies (code) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS corsec_directorates_code_unique ON corsec_directorates (code) WHERE deleted_at IS NULL');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS corsec_senders_code_unique ON corsec_senders (code) WHERE deleted_at IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_email_unique');
            DB::statement('DROP INDEX IF EXISTS users_nik_unique');
            DB::statement('DROP INDEX IF EXISTS positions_code_unique');
            DB::statement('DROP INDEX IF EXISTS currencies_code_unique');
            DB::statement('DROP INDEX IF EXISTS corsec_directorates_code_unique');
            DB::statement('DROP INDEX IF EXISTS corsec_senders_code_unique');

            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_email_unique ON users (email)');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_nik_unique ON users (nik)');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS positions_code_unique ON positions (code)');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS currencies_code_unique ON currencies (code)');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS corsec_directorates_code_unique ON corsec_directorates (code)');
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS corsec_senders_code_unique ON corsec_senders (code)');
        }
    }
};
