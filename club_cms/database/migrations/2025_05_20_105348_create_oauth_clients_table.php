<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('oauth_clients')) {
            Schema::create('oauth_clients', function (Blueprint $table) {
                $table->char('id', 36)->primary();
                $table->string('owner_type')->nullable();
                $table->unsignedBigInteger('owner_id')->nullable();
                $table->string('name');
                $table->string('secret')->nullable();
                $table->string('provider')->nullable();
                $table->text('redirect_uris');
                $table->text('grant_types');
                $table->boolean('revoked');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }

    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return $this->connection ?? config('passport.connection');
    }
};
