<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestItemQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_item_quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('request_item_id')->comment = "from table request item";
            $table->string('supplier_name');
            $table->string('currency');
            $table->bigInteger('price');
            $table->string('moq');
            $table->string('warranty');
            $table->string('lead_time');
            $table->string('date_served');
            $table->string('quotation_validity');
            $table->string('terms_of_payment');
            $table->string('attachment')->nullable();
            $table->string('remarks');
            $table->smallInteger('selected_quotation')->default(0)->comment = '0 - not selected, 1 - selected';
            $table->softDeletes();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('request_item_quotations');
    }
}
