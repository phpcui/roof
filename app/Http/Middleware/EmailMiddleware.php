<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use App\User;

class EmailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $rs = $next($request);
        $users = User::find($request->user()->uid);
        //存入数据库
        $users->test_mail = str_random(20);
        $users->save();
        $str = Crypt::encrypt($request->user()->uid.'&&'.$users->test_mail);
        $mails = $users->email;
        $mail = new Message;
        $mail->setFrom( env('SMTP_NAME') .' <'.env('SMTP_USERNAME').'>')
            ->addTo($mails)
            ->setSubject('来自宁夏E营销的确认邮件!')
            ->setHTMLBody("
            <h3>".$users->name." : 您好,这是一封确认邮件!</h3>
            <a href='http://".$_SERVER['SERVER_NAME']."/test/mail/".$str."' target='_blank'>请点击进行邮箱认证!</a>
                ");
        $mailer = new SmtpMailer([
            'host' => env('SMTP_HOST'),
            'username' => env('SMTP_USERNAME'),
            'password' => env('SMTP_PWD'),
            'secure' => 'ssl',
        ]);
        $mailer->send($mail);

//        $mails = "1015517471@qq.com";
//        $mail = new Message;
//        $mail->setFrom( env('SMTP_NAME') .' <'.env('SMTP_USERNAME').'>')
//            ->addTo($mails)
//            ->setSubject('来自宁夏E营销的确认邮件!')
//            ->setHTMLBody("
//            <h3>黄河 : 您好,这是一封确认邮件!</h3><br>
//            <a href='http://".$_SERVER['SERVER_NAME']."/test/mail/sdfsd' target='_blank'>请点击进行邮箱认证!</a>
//                ");
//        $mailer = new SmtpMailer([
//            'host' => env('SMTP_HOST'),
//            'username' => env('SMTP_USERNAME'),
//            'password' => env('SMTP_PWD'),
//            'secure' => 'ssl',
//        ]);
//        $mailer->send($mail);
//        echo "测试一下 事实上";
        return $rs;
    }
}
