<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ctrl_no');
            $table->smallInteger('status')->default(0)->comment = "0-For Edit, 1-For Log Manager assignment, 2-For purchasing quotation, 3-For Logistics head approval, 4-Served, 5-log cancelled";
            $table->unsignedBigInteger('category_id');
            $table->string('date_needed');
            $table->longText('attachment')->nullable();
            $table->longText('cc')->nullable();
            $table->string('justification')->nullable();
            $table->string('assigned_by')->nullable();
            $table->string('assigned_at')->nullable();
            $table->string('assigned_to')->nullable();
            $table->longText('approver_remarks')->nullable();
            $table->string('approved_at')->nullable();
            $table->longText('log_cancel_remarks')->nullable();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_requests');
    }
}
