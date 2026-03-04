<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('country', 3)->default('IN')->after('status');
            $table->string('state', 100)->default('KA')->after('country');
            $table->string('city', 100)->default('Bengaluru')->after('state');
            $table->string('address', 500)->default('Not provided')->after('city');

            $table->text('hobbies')->nullable()->after('address');
            $table->text('likes')->nullable()->after('hobbies');
            $table->string('food_preference', 20)->nullable()->after('likes');
            $table->text('health_issues')->nullable()->after('food_preference');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'country',
                'state',
                'city',
                'address',
                'hobbies',
                'likes',
                'food_preference',
                'health_issues',
            ]);
        });
    }
};
