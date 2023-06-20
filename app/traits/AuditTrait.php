<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait AuditTrait
{
    public function auditMethod($record_action, $record_snapshot, $table_name)
    {
        // Method implementation
        // return $record_action;
        $save_audit = DB::table('FE_RECORD_LOG')
            ->insert([
                'user_id' => Cache::get('userId'),
                'date_created' => date('Ymd'),
                'time_created' => date('gisA'),
                'table_name' => $table_name,
                'record_action' => $record_action,
                'application' => 'ProPBM',
                'record_snapshot' => $record_snapshot,
            ]);
        return $save_audit;
    }

    // Additional methods...
}
