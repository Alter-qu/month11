<?php

namespace app\test\model;

use think\Model;

class Picture extends Model
{
    protected $resultSetType='collection';
    function picUser(){
        return $this->belongsTo("User","uid");
    }
}
