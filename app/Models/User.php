<?php



namespace App\Models;



use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use Illuminate\Support\Facades\Log;

use Spatie\Permission\Traits\HasRoles;

use Exception;

use Mail;

use App\Mail\SendMail;



class User extends Authenticatable

{



    protected $guard_name = 'web';



    use HasApiTokens, HasFactory, Notifiable, HasRoles;

 

    /**

     * The attributes that are mass assignable.

     *

     * @var array<int, string>

     */

    protected $fillable = [

        'fname',

        'email',

        'password',

        'is_admin',

        'user_type_id',

        'google2fa_secret'

    ];



    public function UserType()

    {

        return $this->belongsTo(UserType::class, 'user_type_id', 'id');

    }



    public function UserRoleType()

    {

        return $this->belongsTo(UserRoleType::class, 'user_role_type_id', 'id');

    }



    public function Role()

    {

        return $this->belongsTo(Role::class, 'role_id', 'id');

    }



    /**

     * The attributes that should be hidden for serialization.

     *

     * @var array<int, string>

     */

    protected $hidden = [

        'password',

        'remember_token',

    ];



    /** 

     * The attributes that should be cast.

     *

     * @var array<string, string>

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

    ];



    /**

     * Write code on Method

     *

     * @return response()

     */

   public function generateCode()
{
    $code = random_int(1000, 9999);

    UserCode::updateOrCreate(
        ['user_id' => $this->id],
        ['code' => $code]
    );

    $details = [
        'title' => 'Your Two-Factor Authentication Code',
        'name'  => $this->fname,
        'body'  => 'Your verification code is below',
        'code'  => $code,
    ];

    Log::info('2FA mail: before send', [
        'to' => $this->email,
        'user_id' => $this->id
    ]);

    try {
        Mail::to($this->email)->send(new SendMail($details));

        Log::info('2FA mail: after send', [
            'to' => $this->email,
            'user_id' => $this->id
        ]);
    } catch (\Throwable $e) {
        Log::error('2FA mail failed', [
            'user_id' => $this->id,
            'email' => $this->email,
            'error' => $e->getMessage(),
        ]);
    }

    return $code;
}
}

