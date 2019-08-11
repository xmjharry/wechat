<?php
/**
 * Created by PhpStorm.
 * User: xuki
 * Date: 2019/7/14
 * Time: 上午10:01
 */

namespace App\Http\Controllers;


use EasyWeChat\Kernel\Messages\Text;
use Illuminate\Support\Facades\Log;

class WeChatController extends Controller
{

    protected $app;

    public function __construct()
    {
        $this->app = app('wechat.official_account');
    }


    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        //$this->createMenu();
        
        $this->app->server->push(function ($message) {
                Log::info($message);
            //Log::info('message is '.$message);
            //$userService = $this->app->user;
            //$user = $userService->get($message->FromUserName);
            switch ($message['MsgType']) {
                case 'event':
                    switch ($message->Event) {
                        case 'subscribe':
                            return '欢迎订阅';
                            break;
                        case 'unsubscribe':
                            break;
                    }
                    break;
                case 'text':
                    $result_name=$message['FromUserName'].'_result.txt';
                    $result_file=resource_path($result_name);
                    if(file_exists($result_file)){
                        $content=file_get_contents($result_file);
                        unlink($result_file);
                        return $content;
                    }
                    return '您可以尝试发送一张纯文字的图，可能会出现“该公众号提供的服务出现故障，请稍后再试”。Be easy。您可以等10秒之后随便发文字即可以获得图片中的文字，Good luck to you!(受限于订阅号的功能，都没动力搞下去了~)';
                    break;
                case 'image':
                    $image_name=$message['FromUserName'];
                    $content=file_get_contents($message['PicUrl']);
                    $myfile = fopen(resource_path($image_name), "w") or die("Unable to open file!");
                    fwrite($myfile,$content);
                    fclose($myfile);
                    $shell=sprintf('tesseract %s %s -l chi_sim+eng','/var/www/wechat/resources/'.$image_name,'/var/www/wechat/resources/'.$image_name.'_result');
                    system($shell);
                    return file_get_contents('/var/www/wechat/resources/result');
                    //return '10秒之后随便发一个文字即可以获得图片中的文字';
                    break;
            }
        });

        return $this->app->server->serve();
    }

    public function createMenu()
    {
        $this->menu_destroy();
        $this->menu_add();
    }

    /**
     * 添加菜单
     */
    public function menu_add()
    {
        $menu = $this->app->menu;
        $buttons = [
            [
                "name"     => "预定民宿",
                "type"     => "view",
                "url"      => "http://mp.weixin.qq.com",
            ],
            [
                "name"       => "美国生活",
                "sub_button" => [
                    [
                        "type"     => "view",
                        "name"     => "医生",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "医院",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "城市选择",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "美国教育",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "儿科疫苗",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                ],
            ],
            [
                "name"       => "我的河马",
                "sub_button" => [
                    [
                        "type"     => "view",
                        "name"     => "赴美百科",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "夏令营",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "赴美故事",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "注册房东",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                    [
                        "type"     => "view",
                        "name"     => "个人中心",
                        "url"      => "http://mp.weixin.qq.com",
                    ],
                ],
            ],
        ];
        $menu->create($buttons);
    }


    /**
     * 删除菜单
     *
     */
    public function menu_destroy()
    {
        $menu = $this->app->menu;
        $menu->delete();
    }
}
