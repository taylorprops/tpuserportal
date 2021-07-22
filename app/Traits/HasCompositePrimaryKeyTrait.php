<?php
namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;

trait HasCompositePrimaryKeyTrait {

    //Get the value indicating whether the IDs are incrementing.
    public function getIncrementing(){
        return false;
    }


    //Set the keys for a save update query.
    protected function setKeysForSaveQuery($query) {

        foreach ($this -> getKeyName() as $key) {
            if (isset($this -> $key)){
                $query -> where($key, '=', $this -> $key);
            }
        }
        return $query;
    }


    protected function getKeyForSaveQuery($keyName = null)
    {

        if(is_null($keyName)){
            $keyName = $this -> getKeyName();
        }

        if (isset($this -> original[$keyName])) {
            return $this -> original[$keyName];
        }

        return $this -> getAttribute($keyName);
    }


    //Execute a query for a single record by ID.
    public static function find($ids, $columns = ['*']) {
        $me = new self;
        $query = $me -> newQuery();

        foreach ($me -> getKeyName() as $key) {
            $query -> where($key, '=', $ids[$key]);
        }

        return $query -> first($columns);
    }

}
