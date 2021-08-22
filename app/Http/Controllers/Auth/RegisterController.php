<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Email;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Mail\RegistrationMail;
use Mail,PDF;
class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $createUser = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        Product::factory(10)->create();
        $getproduct = Product::where('user_id',$createUser->id)->first();
        if(!empty($getproduct)){
            $data['product'] = $getproduct->product_name;
        }else{ $data['product'] = 'Jeans'; }
        
        // $details = [
            //     'name' => $data['name'],
            //     'product' => $data['product'],
            //     'title' => 'Mail Demo form Registration',
            //     'url' => '/login'
            // ];
            
            // $pdf = PDF::loadView('pdf.registrationPdf', $details);
            // return $pdf->download('itsolutionstuff.pdf');
            // Storage::put('public/csv/name.pdf',$content) ;
            $details = [
                'product' => $data['product'],
                'name' => $data['name']
        ];
        $filename = $data['name']?str_replace(' ','',$data['name']):time();
        $filename = $filename.'_'.date('dYmHsm').'_Registration.pdf';
        $pdf = PDF::loadView('pdf.registrationPdf', $details);
        $content = $pdf->download($filename);
        Storage::put('public/registration_pdf/'.$filename,$content);
        // $data['pdf_file'] = Storage::url('registration_pdf/'.$filename);
        $data['user_id'] = $createUser->id;
        Email::create([
            'user_id' => $createUser->id,
            'pdf_file' => $filename,
        ]);
        $this->sendmail($data);
        
        return $createUser;
    }
    public  function sendmail($data){
        $myEmail = $data['email'];
        $details = [
            'name' => $data['name'],
            'product' => $data['product'],
            'title' => 'Mail Demo form Registration',
            'user_id' =>  $data['user_id']
        ];

        // $filename = $data['name']?$data['name']:time();
        // $filename = $filename.'_Registration.pdf';
        // $pdf = PDF::loadView('pdf.registrationPdf', $details);
        // $content = $pdf->download('registration.pdf');
        // Storage::put('registrationPDF/'.$filename,$content) ;

        Mail::to($myEmail)->send(new RegistrationMail($details));
    }
}
