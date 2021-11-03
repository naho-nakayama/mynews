<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
     protected $guarded = array('id');

    // 以下を追記
    public static $rules = array(
        'name' => 'required',
        'gender' => 'required',
        'hobby' => 'required',
        'introduction' => 'required',
    );
    
    // 17 編集履歴を実装しようの課題で以下を追記
    // Profile Modelに関連付けを行う
    public function profile_histories()
    {
        return $this->hasMany('App\ProfileHistory');
    }
}
