<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE users MODIFY matricule VARCHAR(255) NULL');

        DB::transaction(function () {
            $users = DB::table('users')
                ->select('id', 'matricule', 'telephone')
                ->orderBy('id')
                ->get();

            foreach ($users as $user) {
                $matricule = preg_replace('/\D+/', '', (string) ($user->matricule ?? '')) ?? '';
                $telephone = trim((string) ($user->telephone ?? ''));

                if ($matricule !== '' && preg_match('/^[0-9]{9}$/', $matricule)) {
                    $update = [
                        'matricule' => null,
                    ];

                    if ($telephone === '') {
                        $update['telephone'] = $matricule;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update($update);
                }
            }
        });
    }

    public function down(): void
    {
        $users = DB::table('users')
            ->select('id')
            ->whereNull('matricule')
            ->get();

        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'matricule' => 'REV' . str_pad((string) $user->id, 6, '0', STR_PAD_LEFT),
                ]);
        }

        DB::statement('ALTER TABLE users MODIFY matricule VARCHAR(255) NOT NULL');
    }
};