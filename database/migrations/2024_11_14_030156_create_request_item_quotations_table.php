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
            $table->smallInteger('status')->default(0)->comment = "0-with quote, 1-decline to quote, 2-no quote";
            $table->unsignedInteger('request_item_id')->comment = "from table request item";
            $table->string('supplier_name');
            $table->string('currency')->nullable();
            $table->decimal('price', total: 10, places: 2)->nullable();
            $table->float('rate')->nullable()->comment = "Rate is equal to the rate on currency";
            $table->string('moq')->nullable();
            $table->string('warranty')->nullable();
            $table->string('lead_time')->nullable();
            $table->string('date_served')->nullable();
            $table->string('quotation_validity')->nullable();
            $table->string('terms_of_payment')->nullable();
            $table->string('attachment')->nullable();
            $table->string('remarks')->nullable();
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
