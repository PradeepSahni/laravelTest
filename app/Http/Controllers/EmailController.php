<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Email;
class EmailController extends Controller
{
    public function index(Request $request){
        if($request->user_id){
            $getUser = User::where('id',$request->user_id)->first();
            if(!empty($getUser)){
                if($getUser->download < $getUser->download_limit){
                    $getPdfFile = Email::where('user_id',$request->user_id)->first();
                    if(!empty($getPdfFile)){
                        $limit = ((int)$getUser->download)+1;
                        $update = User::where('id',$request->user_id)->update(array('download'=>$limit));
                        return redirect('storage/registration_pdf/'.$getPdfFile->pdf_file);
                    }
                }
                else{
                    return redirect('login');
                }
            }
        }
    }
}
