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


        $this->app->server->push(function ($message) {
            $userService = $this->app->user;
            $user = $userService->get($message->FromUserName);
            switch ($message->MsgType) {
                case 'event':
                    switch ($message->Event) {
                        case 'subscribe':
                            $this->app->staff->message(new Text(
                                $user."您好，终于找到我们啦～河马赴美为你准备了各种攻略，回复下列数字查看详细内容^ ^\n1、了解河马赴美（回复1）\n2、赴美流程（回复2）\n3、套餐详情（回复3）\n4、月子中心各房型介绍（回复4）\n5、美国签证流程（回复5）\n6、如何选择医院（回复6）\n7、如何选择医生（回复7）\n8、赴美生子百人真实diy经验分享：传送门（回复8）\n9、赴美生子如何领取生育津贴（回复9）\n10、赴美生子孕妈收藏的尔湾儿科诊所收费信息 ＋ 疫苗品牌（回复10）\n11、尔湾幼儿园入园指南（回复11）\n12、美国美食推荐（回复12）\n13、美国景点推荐（回复13）"
                            ))->to($message->FromUserName)->send();
                            break;
                        case 'unsubscribe':
                            break;
                    }
                    break;
                case 'text':
                    break;
            }
        });

        return $this->app->server->serve();
    }
}