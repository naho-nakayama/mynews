<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// 16 投稿したニュースを更新/削除しようの課題で以下を追記
use App\Profile;
//17編集履歴を実装しようの課題の、ProfileHistory Modelの使用を宣言するのに以下追加
use App\ProfileHistory;
use Carbon\Carbon;

class ProfileController extends Controller
{
    //
    public function add()
    {
        return view('admin.profile.create');
    }

    public function create(Request $request)
    {
         // 16 投稿したニュースを更新/削除しようの課題で以下を追記
      // Varidationを行う
      $this->validate($request, Profile::$rules);
      
      $profile = new Profile;
      
      $form = $request->all();
      // フォームから画像が送信されてきたら、保存して、$profile->image_path に画像のパスを保存する
      if (isset($form['image'])) {
        $path = $request->file('image')->store('public/image');
        $profile->image_path = basename($path);
      } else {
          $profile->image_path = null;
      }
      
      // フォームから送信されてきた_tokenを削除する
      unset($form['_token']);
      // フォームから送信されてきたimageを削除する
      unset($form['image']);
      
      // データベースに保存する
      $profile->fill($form);
      $profile->save();

        return redirect('admin/profile/create');
    }

    public function edit(Request $request)//16投稿したニュースを更新/削除しようの課題で中身を追記
    {
         $profile = Profile::find($request->id);//ニュースを検索　$request->idは「１」のこと
      if (empty($profile)) {
        abort(404);    
      }
      return view('admin.profile.edit', ['profile_form' => $profile]);
        

    }

    public function update(Request $request)
    {
      //以下17 編集履歴を実装しよう課題で記述
       // Validationをかける
      $this->validate($request, Profile::$rules);
      // Profile Modelからデータを取得する
      $profile = Profile::find($request->id);
      // 送信されてきたフォームデータを格納する
      $profile_form = $request->all();
      if ($request->remove == 'true') {
          $profile_form['image_path'] = null;
      } elseif ($request->file('image')) {
          $path = $request->file('image')->store('public/image');
          $profile_form['image_path'] = basename($path);
      } else {
          $profile_form['image_path'] = $profile->image_path;
      }

      unset($profile_form['image']);
      unset($profile_form['remove']);
      unset($profile_form['_token']);
      // 該当するデータを上書きして保存する
      $profile->fill($profile_form)->save();
      
          //17 編集履歴を実装しよう課題で 以下を追記（Profile Modelを保存するタイミングで ProfileHistory Modelにも編集履歴を追加
        $profile_history = new ProfileHistory();
        $profile_history->profile_id = $profile->id;
        $profile_history->edited_at = Carbon::now();
        $profile_history->save();
        
        return redirect('admin/profile/edit?id=' . $profile->id);
    }
}
