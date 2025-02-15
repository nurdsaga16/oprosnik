<?php

declare(strict_types=1);

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'firstname' => "User{$i}",
                'lastname' => "Lastname{$i}",
                'email' => "user{$i}@example.com",
                'password' => Hash::make('123456'),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('departments')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'title' => "Department {$i}",
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('specializations')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'title' => "Specialization {$i}",
                'department_id' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('groups')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'title' => "Group {$i}",
                'course' => rand(1, 3),
                'specialization_id' => $i,
                'user_id' => $i,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('subjects')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'title' => "Subject {$i}",
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('subject_user')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'subject_id' => $i,
                'user_id' => $i,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('practices')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'title' => "Practice {$i}",
                'start_date' => Carbon::now()->subDays(10),
                'end_date' => Carbon::now()->addDays(10),
                'active' => true,
                'subject_id' => $i,
                'user_id' => $i,
                'group_id' => $i,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );

        DB::table('surveys')->insert(
            collect(range(1, 5))->map(fn ($i) => [
                'title' => "Survey {$i}",
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(5),
                'practice_id' => $i,
                'user_id' => $i,
                'group_id' => $i,
                'status' => 'Активный',
                'template' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ])->toArray()
        );
    }
}
