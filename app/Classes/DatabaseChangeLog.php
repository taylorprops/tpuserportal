<?php

namespace App\Classes;

use App\Models\Database\ChangeLog;
use App\Models\Database\ChangeLogChanges;

class DatabaseChangeLog
{

    public $changed_by;
    public $model;
    public $model_id;
    public $data_before;
    public $data_after;
    public $data_manual;
    public $ignore_fields = [
        'created_at',
        'updated_at',
        'lender_id',
        'lender_uuid',
    ];


    public function log_changes($changed_by, $model, $model_id, $data_before, $data_after, $data_manual = null)
    {


        $data_before = collect($data_before);
        $data_after = collect($data_after);

        if(!$data_before) {

            $add_changes = new ChangeLog();
            $add_changes -> model = $model;
            $add_changes -> model_id = $model_id;
            $add_changes -> changed_by = $changed_by;
            $add_changes -> change_type = 'add';
            $add_changes -> save();
            $add_changes_id = $add_changes -> id;

        }

        foreach($this -> ignore_fields as $ignore_field) {
            unset($data_before[$ignore_field]);
            unset($data_after[$ignore_field]);
        }

        $changes = [];

        foreach($data_before as $key => $value_before) {

            if(!is_array($value_before)) {

                if(!preg_match('/(_id|_uuid)/', $key)) {

                    $value_after = $data_after[$key];
                    if(is_numeric($data_after[$key]) && preg_match('/\./', $data_after[$key])) {
                        $value_after = number_format($data_after[$key], 2, '.', '');
                    }
                    if(is_numeric($value_before) && preg_match('/\./', $value_before)) {
                        $value_before = number_format($value_before, 2, '.', '');
                    }

                    if($value_before != $value_after) {

                        $changes[] = [
                            'field_name' => $key,
                            'value_before' => $value_before,
                            'value_after' => $value_after
                        ];

                    }

                }

            }

        }

        if(count($changes) > 0) {

            $add_changes = new ChangeLog();
            $add_changes -> model = $model;
            $add_changes -> model_id = $model_id;
            $add_changes -> changed_by = $changed_by;
            $add_changes -> change_type = 'edit';
            $add_changes -> save();
            $add_changes_id = $add_changes -> id;

            foreach($changes as $change) {
                $add_change = new ChangeLogChanges();
                $add_change -> change_id = $add_changes_id;
                $add_change -> field_name = $change['field_name'];
                $add_change -> field_name_display = ucwords(preg_replace('/_/', ' ', $change['field_name']));
                $add_change -> value_before = $change['value_before'];
                $add_change -> value_after = $change['value_after'];
                $add_change -> save();
            }

        }

        return response() -> json([
            'status' => 'success',
            'changes' => $changes
        ]);


    }


}
