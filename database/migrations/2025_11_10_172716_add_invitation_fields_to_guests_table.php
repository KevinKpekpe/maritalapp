<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('invitation_token')->nullable()->unique();
            $table->string('rsvp_status')->default('pending');
            $table->timestamp('rsvp_confirmed_at')->nullable();
        });

        $guests = DB::table('guests')->select('id')->get();

        foreach ($guests as $guest) {
            DB::table('guests')
                ->where('id', $guest->id)
                ->update([
                    'invitation_token' => Str::uuid()->toString(),
                    'rsvp_status' => 'pending',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['invitation_token', 'rsvp_status', 'rsvp_confirmed_at']);
        });
    }
};
