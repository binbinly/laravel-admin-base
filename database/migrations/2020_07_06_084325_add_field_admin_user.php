<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldAdminUser extends Migration
{
    private $table = "admin_users";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->string("google2fa_secret", 255)->after('avatar')->nullable(false)->default("");
            $table->string("recovery_code", 255)->after('avatar')->nullable(false)->default("");
            $table->tinyInteger("enable")->after('avatar')->nullable(false)->comment('1=正常，0=禁用')->default(1);
            $table->tinyInteger("is_validate")->after('avatar')->nullable(false)->comment('1=开启，0=关闭')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn("google2fa_secret");
            $table->dropColumn("recovery_code");
            $table->dropColumn("enable");
            $table->dropColumn("is_validate");
        });
    }
}
