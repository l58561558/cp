<?php
namespace app\adminz\model;
use think\Model;
// 生成明细
class Detail extends Model
{   
                            // 用户id  , 类型       , 钱         , 收入或支出(1 || 2)
    public function add_detail($user_id, $deal_cate, $deal_money, $status)
    {
        $yh = db('user')->where('id='.$user_id)->find();
        $arr['user_id'] = $yh['id'];
        $arr['deal_cate'] = $deal_cate;
        $arr['deal_money'] = $deal_money;
        $arr['new_money'] = $deal_money+$yh['amount_money'];
        $arr['add_time'] = date('Y-m-d H:i:s');
        $arr['status'] = $status;
        $res = db('account_details')->insert($arr);

        return $res;
    }
}
