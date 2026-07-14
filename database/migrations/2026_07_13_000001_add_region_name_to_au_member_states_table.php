<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('myb_au_member_states', 'region_name')) {
            Schema::table('myb_au_member_states', function (Blueprint $table) {
                $table->string('region_name', 100)->nullable()->after('code_alpha2');
                $table->index('region_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('myb_au_member_states', 'region_name')) {
            Schema::table('myb_au_member_states', function (Blueprint $table) {
                $table->dropIndex(['region_name']);
                $table->dropColumn('region_name');
            });
        }
    }
};
