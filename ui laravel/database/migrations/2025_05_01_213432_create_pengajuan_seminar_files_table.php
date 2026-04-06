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
                Schema::create('pengajuan_seminar_files', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('pengajuan_seminar_id')->constrained('pengajuan_seminar')->onDelete('cascade');
                    $table->string('file_path');    
                    $table->string('file_name');
                    $table->string('file_type');
                    $table->bigInteger('file_size');
                    $table->timestamps();
                });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('pengajuan_seminar_files');
        }
    };
