<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyPrimaryKeyInSettingsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        if (Schema::getConnection()->getDriverName() === 'sqlsrv') {
            $this->dropPrimaryForSqlServer();
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->string('name')->primary()->change();
        });
    }

    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropPrimary('settings_name_primary');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->increments('id');
        });
    }

    /**
     * Finds the random name of primary key constraint autogenerated by SQL Server and drops it.
     */
    protected function dropPrimaryForSqlServer()
    {
        $schema = Schema::getConnection()->getDoctrineSchemaManager();
        $indexes = $schema->listTableIndexes('settings');
        $primaryIndex = Arr::get($indexes, 'primary');

        if ($primaryIndex) {
            Schema::table('settings', function (Blueprint $table) use ($primaryIndex) {
                $table->dropPrimary($primaryIndex->getName());
            });
        }
    }
}
