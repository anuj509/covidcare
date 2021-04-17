<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->date('dob');
            $table->enum('gender', ['male','female','other']);
            $table->enum('blood_group', ['A+','A-','B+','B-','AB+','AB-','O+','O-']);
            $table->text('oxygen_level');
            $table->text('poc_name');
            $table->text('poc_phone');
            $table->text('patient_currently_admitted_at');
            $table->text('ward');
            $table->enum('requirement', ['oxygen','plasma','medicines','bed','other']);
            $table->text('oxygen');
            $table->text('plasma');
            $table->text('medicines');
            $table->text('bed');
            $table->text('other');
            $table->timestamps();
            $table->string('user_id',191);
            $table->foreign('user_id')->references('id')->on('covid_care_users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
