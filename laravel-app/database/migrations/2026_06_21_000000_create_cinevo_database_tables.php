<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role')->default('user');
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->string('phone', 30)->nullable();
                $table->string('status', 20)->default('Active');
                $table->rememberToken();
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        } else {
            Schema::table('users', function (Blueprint $table): void {
                if (! Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 30)->nullable();
                }
                if (! Schema::hasColumn('users', 'status')) {
                    $table->string('status', 20)->default('Active');
                }
                if (! Schema::hasColumn('users', 'remember_token')) {
                    $table->rememberToken();
                }
                if (! Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable();
                }
                if (! Schema::hasColumn('users', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        if (! Schema::hasTable('category')) {
            Schema::create('category', function (Blueprint $table): void {
                $table->id();
                $table->string('category_name');
                $table->string('status', 100)->default('Active')->nullable();
            });
        }

        if (! Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table): void {
                $table->id();
                $table->string('class_name')->nullable();
            });
        }

        if (! Schema::hasTable('movies')) {
            Schema::create('movies', function (Blueprint $table): void {
                $table->id();
                $table->string('poster');
                $table->string('title');
                $table->string('trailer_link');
                $table->string('movie_desc');
                $table->string('duration');
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->date('release_date')->nullable();
                $table->string('director')->nullable();
                $table->string('rating', 50)->nullable();
                $table->string('language', 100)->nullable();
                $table->string('movie_status', 20)->default('now_showing');
                $table->boolean('is_featured')->default(false);
                $table->string('genre')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (! Schema::hasTable('theaters')) {
            Schema::create('theaters', function (Blueprint $table): void {
                $table->id();
                $table->string('theater_name');
                $table->string('location')->nullable();
                $table->timestamp('Created_at')->useCurrent()->useCurrentOnUpdate();
                $table->integer('screens')->default(1);
            });
        }

        if (! Schema::hasTable('shows')) {
            Schema::create('shows', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('movie_id')->constrained('movies');
                $table->foreignId('theater_id')->constrained('theaters');
                $table->date('show_date')->nullable();
                $table->time('show_time')->nullable();
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (! Schema::hasTable('seats')) {
            Schema::create('seats', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('theater_id')->nullable()->constrained('theaters');
                $table->foreignId('class_id')->nullable()->constrained('classes');
                $table->string('seat_number', 10)->nullable();
            });
        }

        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users');
                $table->foreignId('show_id')->nullable()->constrained('shows');
                $table->foreignId('class_id')->nullable()->constrained('classes');
                $table->integer('total_seats');
                $table->decimal('total_price', 10, 2);
                $table->date('booking_date');
                $table->boolean('has_kids')->default(false);
                $table->integer('kids_count')->default(0)->nullable();
                $table->integer('adults_count')->default(0)->nullable();
                $table->string('payment_status', 50)->default('pending')->nullable();
                $table->string('booking_status', 50)->default('confirmed')->nullable();
            });
        }

        if (! Schema::hasTable('booking_seats')) {
            Schema::create('booking_seats', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('booking_id')->nullable()->constrained('bookings');
                $table->foreignId('seat_id')->nullable()->constrained('seats');
            });
        }

        if (! Schema::hasTable('movie_category')) {
            Schema::create('movie_category', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('movi_id')->constrained('movies')->cascadeOnDelete();
                $table->foreignId('cat_id')->constrained('category')->cascadeOnDelete();
                $table->unique(['movi_id', 'cat_id'], 'unique_movie_category');
            });
        }

        if (! Schema::hasTable('review')) {
            Schema::create('review', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('users_id')->nullable()->constrained('users');
                $table->foreignId('movies_id')->nullable()->constrained('movies');
                $table->text('review');
                $table->tinyInteger('rating')->nullable();
                $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
                $table->timestamp('updated_at')->nullable();
            });
        }

        if (! Schema::hasTable('show_class_pricing')) {
            Schema::create('show_class_pricing', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('show_id')->nullable()->constrained('shows');
                $table->foreignId('class_id')->nullable()->constrained('classes');
                $table->decimal('price', 10, 2)->nullable();
                $table->unique(['show_id', 'class_id']);
            });
        }

        if (! Schema::hasTable('carousel')) {
            Schema::create('carousel', function (Blueprint $table): void {
                $table->id();
                $table->integer('movie_id')->nullable()->index();
                $table->string('title');
                $table->string('image');
                $table->boolean('status')->default(true);
                $table->integer('display_order')->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
        } else {
            Schema::table('carousel', function (Blueprint $table): void {
                if (! Schema::hasColumn('carousel', 'movie_id')) {
                    $table->integer('movie_id')->nullable()->after('id')->index();
                }
            });
        }

    }

    public function down(): void
    {
        // Non-destructive by design: this migration can run against an imported legacy database.
    }
};
