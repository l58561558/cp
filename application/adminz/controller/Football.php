<?php
namespace app\adminz\controller;
use app\adminz\model\Detail;
use app\home\model\Group;
use think\Db;
class Football extends Base
{
    public $game_cate = 1;
    /**
     * 列表页面
     * @return [type] [description]
     */
    public function index()
    {
        return view();
    }
    /**
     * 获取数据
     * @param  integer $position_id [description]
     * @return [type]               [description]
     */
    public function get_fb_list()
    {
        $count = db("fb_game")->where('status>0')->order(['end_time'=>'asc','id'=>'asc'])->count();
        $list = db("fb_game")->where('status>0')->order(['end_time'=>'asc','id'=>'asc'])->paginate(20,$count);

        //获取分页
        $page = $list->render();
        //遍历数据
        if(!empty($list)){
            $list->each(function($item,$key){
                if($item['is_postpone'] == 1){
                    $item['tz_status'] = '已延期';
                }else{
                    if($item['status'] == 0){
                        $item['tz_status'] = '已结算';
                    }else{
                        if($item['end_time'] <= date('Y-m-d H:i:s')){
                            $item['tz_status'] = '停止投注';
                        }else{
                            $item['tz_status'] = '可以投注';
                        }    
                    }   
                }
                
                return $item;
            });
        }
        $this->assign("page",$page);
        $this->assign("_list",$list);
        $html = $this->fetch("football/fb_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function add()
    {
        if(IS_POST){
            $data = $_REQUEST;
            $end_time = $data['end_time'];
            $game['game_no'] = $data['game_no'];
            $weekday = array('周日','周一','周二','周三','周四','周五','周六'); 
            $today = strtotime($end_time)-43200;
            $game['week'] = $weekday[date('w', $today)];
            $game['game_name'] = $data['game_name'];
            $game['end_time'] = $end_time;
            $game['home_team'] = $data['home_team'];
            $game['road_team'] = $data['road_team'];
            $game['let_score'] = $data['let_score'];
            $game['add_time'] = date('Y-m-d H:i:s');
            unset($data['game_no']);
            unset($data['game_name']);
            unset($data['end_time']);
            unset($data['home_team']);
            unset($data['road_team']);
            unset($data['let_score']);
            $game_id = db('fb_game')->insert($game,false,true);
            if($game_id > 0){
                $cate = array();
                $array = array('home_win','home_eq','home_lose','let_score_home_win','let_score_home_eq','let_score_home_lose');
                foreach ($data as $key => $value) {
                    $fb_code_date = db('fb_code')->where('code="'.$key.'"')->find();
                    $cate['game_id'] = $game_id;
                    $cate['cate_name'] = $fb_code_date['code_name'];
                    $cate['cate_code'] = $key;
                    // $cate['cate_odds'] = $value==0?0:(in_array($key, $array)?floor($value*1.085*100)/100:$value);
                    $cate['cate_odds'] = $value;
                    $game_cate[] = $cate;
                }
                $res = db('fb_game_cate')->insertAll($game_cate);
                if($res > 0){
                    $this->success('创建成功');
                }else{
                    $this->error('插入失败');
                }
            }
        }
        $code = db("fb_code")->where('code_pid=0')->select();
        foreach ($code as $key => $value) {
            if($code[$key]['id'] == 3){
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'].' and code_id<=13')->select();
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'].' and code_id>13 and code_id<=18')->select();
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'].' and code_id>18')->select();
            }else{
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'])->select();
            }
        }
        $this->assign('code',$fb_code);
        return view();
    }

    public function drop($id = 0)
    {
        db('fb_game')->where('id='.$id)->delete();
        db('fb_game_cate')->where('game_id='.$id)->delete();
        $this->success('删除成功');
    }

    public function look($id)
    {
        $data = db('fb_game')->where("id=".$id)->find();
        $game_cate = db('fb_game_cate')->where('game_id='.$id)->select();
        $data['cate'] = $game_cate;

        $this->assign("data",$data);
        return view();
    }

    /**
     * 编辑记录
     * @param  integer $id [description]
     * @return [type]          [description]
     */
    public function edit($id = 0){
        if(IS_POST){
            // 开启事务
            Db::startTrans();
            $data = $_REQUEST;
            $end_time = $data['end_time'];

            $game['game_no'] = $data['game_no'];
            $weekday = array('周日','周一','周二','周三','周四','周五','周六'); 
            $today = strtotime($end_time)-43200;
            $game['week'] = $weekday[date('w', $today)];
            $game['game_name'] = $data['game_name'];
            $game['end_time'] = $end_time;
            $game['home_team'] = $data['home_team'];
            $game['road_team'] = $data['road_team'];
            $game['let_score'] = $data['let_score'];
            $game['top_score'] = $data['top_score'];
            $game['down_score'] = $data['down_score'];
            if(!empty($data['top_score']) && !empty($data['down_score'])){
                $total_score = explode(':', $data['down_score']);
                $game['total_score'] = $total_score[0]+$total_score[1];
            }

            unset($data['game_name']);
            unset($data['end_time']);
            unset($data['home_team']);
            unset($data['road_team']);
            unset($data['let_score']);
            unset($data['top_score']);
            unset($data['down_score']);
            unset($data['id']);
            $flag = db("fb_game")->where('id='.$id)->update($game);
            foreach ($data as $key => $value) {
                db("fb_game_cate")->where('game_id='.$id.' and cate_code="'.$key.'"')->setField('cate_odds',$data[$key]);
            }
            if($flag || $flag === 0){
                // 提交事务
                Db::commit();
                $this->success("保存成功");
            }else{
                // 回滚事务
                Db::rollback();
                $this->error("保存失败");
            }
        }
        
        $code = db("fb_code")->where('code_pid=0')->select();
        foreach ($code as $key => $value) {
            if($code[$key]['id'] == 3){
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'].' and code_id<=13')->select();
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'].' and code_id>13 and code_id<=18')->select();
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'].' and code_id>18')->select();
            }else{
                $fb_code[] = db("fb_code")->where('code_pid='.$code[$key]['id'])->select();
            }
        }

        // $fb_game_cate = db("fb_game_cate")->where('game_id='.$id)->select();
        for ($i=0; $i < count($fb_code); $i++) { 
            for ($j=0; $j < count($fb_code[$i]); $j++) { 
                $fb_cate[$i][$j] = db("fb_game_cate")->where('game_id='.$id.' and cate_code="'.$fb_code[$i][$j]['code'].'"')->find();
            }
        }

        $this->assign('code',$fb_cate);

        $data = db('fb_game')->where("id=".$id)->find();
        $this->assign("data",$data);

        return view();
    }


    // 订单
    public function fb_order()
    {
        return view();
    }
    public function get_fb_order()
    {
        $data = $_REQUEST;

        $count = db("order")->where('game_cate='.$this->game_cate)->order('add_time desc')->count();
        $list = db("order")->where('game_cate='.$this->game_cate)->order('add_time desc')->paginate(20,$count);

        $list->each(function($item,$key){
            $item['user_id'] = db('user')->where('id='.$item['user_id'])->value('yhid');
            $chuan = explode(',', $item['chuan']);
            $chuann = [];
            for ($i=0; $i < count($chuan); $i++) { 
                $chuann[] = db('chuan')->where('chuan_id='.$chuan[$i])->value('chuan_name');
            }
            $item['chuan'] = implode(',', $chuann);
            return $item;
        });
        $page = $list->render();

        $this->assign("page",$page);
        $this->assign("_list",$list);
        
        $html = $this->fetch("tpl/fb_order_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    public function order_info($order_id)
    {
        $order = db('order')->where('game_cate='.$this->game_cate.' and order_id='.$order_id)->order('add_time desc')->find();
        if($order['order_type'] == 2){
            $hm_id = db('order_hm_user')->where('order_id='.$order['order_id'])->value('hm_id');
            $data['hm_user'] = db('order_hm_user')->where('hm_id='.$hm_id)->select();
            $data['hm'] = db('order_hm_desc')->where('hm_id='.$hm_id)->find();
            $data['hm']['user_name'] = db('user')->where('id='.$data['hm']['user_id'])->value('user_name');
            $data['hm']['order_no'] = db('order')->where('order_id='.$data['hm']['order_id'])->value('order_no');
            $data['hm']['hm_status'] = db('code_info')->where('code_pid=13 and code='.$data['hm']['hm_status'])->value('code_name');
            foreach ($data['hm_user'] as $key => $value) {
                $data['hm_user'][$key]['yhid'] = db('user')->where('id='.$data['hm_user'][$key]['user_id'])->value('yhid');
                $data['hm_user'][$key]['user_name'] = db('user')->where('id='.$data['hm_user'][$key]['user_id'])->value('user_name');
                $data['hm_user'][$key]['order_no'] = db('order')->where('order_id='.$data['hm_user'][$key]['order_id'])->value('order_no');
            }
        }
        if($order['order_type'] == 3){
            $gd_id = db('order_gd_user')->where('order_id='.$order['order_id'])->value('gd_id');
            $data['gd_user'] = db('order_gd_user')->where('gd_id='.$gd_id)->select();
            $data['gd'] = db('order_gd_desc')->where('gd_id='.$gd_id)->find();
            $data['gd']['user_name'] = db('user')->where('id='.$data['gd']['user_id'])->value('user_name');
            $data['gd']['order_no'] = db('order')->where('order_id='.$data['gd']['order_id'])->value('order_no');
            $data['gd']['gd_status'] = db('code_info')->where('code_pid=13 and code='.$data['gd']['gd_status'])->value('code_name');
            foreach ($data['gd_user'] as $key => $value) {
                $data['gd_user'][$key]['yhid'] = db('user')->where('id='.$data['gd_user'][$key]['user_id'])->value('yhid');
                $data['gd_user'][$key]['user_name'] = db('user')->where('id='.$data['gd_user'][$key]['user_id'])->value('user_name');
                $data['gd_user'][$key]['order_no'] = db('order')->where('order_id='.$data['gd_user'][$key]['order_id'])->value('order_no');
            }
        }
        $data['order_money'] = $order['order_money'];
        $data['win_money'] = $order['win_money'];
        $data['order_no'] = $order['order_no'];
        $data['add_time'] = $order['add_time'];
        $order_info = db('order_info')->where('game_cate='.$this->game_cate.' and order_id='.$order_id)->select();
        $data['game_num'] = count($order_info);
        // $data['chuan'] = db('chuan')->where('chuan_id='.$order['chuan'])->value('chuan_name');
        $chuan = explode(',', $order['chuan']);
        $chuann = [];
        for ($i=0; $i < count($chuan); $i++) { 
            $chuann[] = db('chuan')->where('chuan_id='.$chuan[$i])->value('chuan_name');
        }
        $data['chuan'] = implode(',', $chuann);
        $data['multiple'] = $order['multiple'];
        $data['order_type'] = $order['order_type'];

        $let_arr = array('let_score_home_win','let_score_home_eq','let_score_home_lose');
        $total_arr = array('total_small','total_big');

        foreach ($order_info as $key => $value) {
            $game[$key] = db('fb_game')->where('id='.$order_info[$key]['game_id'])->find();
            $data['order_info'][$key]['week'] = $game[$key]['week'];
            $data['order_info'][$key]['game_no'] = $game[$key]['game_no'];
            $data['order_info'][$key]['home_team'] = $game[$key]['home_team'];
            $data['order_info'][$key]['road_team'] = $game[$key]['road_team'];
            $data['order_info'][$key]['down_score'] = $game[$key]['down_score'];
            $data['order_info'][$key]['win_result'] = [];
            $data['order_info'][$key]['win_game_result'] = '';
            $data['order_info'][$key]['status'] = $game[$key]['status'];
            if(strpos($order_info[$key]['tz_result'] , ',') === false){
                $data['order_info'][$key]['tz_result'][0] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds,is_win')->where('cate_id='.$order_info[$key]['tz_result'])->find();
            }else{
                if(!empty($order_info[$key]['tz_result'])){
                    $tz_result = explode(',', $order_info[$key]['tz_result']);
                    foreach ($tz_result as $ke => $val) {
                        $tz_result[$ke] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds,is_win')->where('cate_id='.$tz_result[$ke])->find();
                    }
                    $data['order_info'][$key]['tz_result'] = $tz_result;
                }  
            }

            foreach ($data['order_info'][$key]['tz_result'] as $ke => $val) {
                $gc = db('fb_game_cate')->where('cate_id='.$data['order_info'][$key]['tz_result'][$ke]['cate_id'])->find();
                if(in_array($gc['cate_code'], $let_arr)){
                    $let_score = db('fb_game')->where('id='.$gc['game_id'])->value('let_score');
                    $gc['cate_name'] .= '('.$let_score.')';
                }
                if(in_array($gc['cate_code'], $total_arr)){
                    $total_score = db('nba_game')->where('id='.$gc['game_id'])->value('total_score');
                    $gc['cate_name'] .= '('.$total_score.')';
                }
                $data['order_info'][$key]['tz_result'][$ke] = $gc;
            }
            if(!empty($order_info[$key]['win_result'])){
                if(strpos($order_info[$key]['win_result'] , ',') === false){
                    $data['order_info'][$key]['win_result'][0] = db('fb_game_cate')->field('cate_id,cate_name,cate_odds,is_win')->where('cate_id='.$order_info[$key]['win_result'])->find();
                }else{
                    $win_result = explode(',', $order_info[$key]['win_result']);
                    foreach ($win_result as $ke => $val) {
                        $win_result[$ke] = db('fb_game_cate')->field('cate_name,cate_odds,is_win')->where('cate_id='.$win_result[$ke])->find();
                    }
                    $data['order_info'][$key]['win_result'] = $win_result;                      
                }  
            }
            foreach ($data['order_info'][$key]['win_result'] as $ke => $val) {
                $gc = db('fb_game_cate')->where('cate_id='.$data['order_info'][$key]['tz_result'][$ke]['cate_id'])->find();
                if(in_array($gc['cate_code'], $let_arr)){
                    $let_score = db('fb_game')->where('id='.$gc['game_id'])->value('let_score');
                    $gc['cate_name'] .= '('.$let_score.')';
                }
                if(in_array($gc['cate_code'], $total_arr)){
                    $total_score = db('nba_game')->where('id='.$gc['game_id'])->value('total_score');
                    $gc['cate_name'] .= '('.$total_score.')';
                }
                $data['order_info'][$key]['win_result'][$ke] = $gc;
            }
            // if(!empty($order_info[$key]['win_game_result'])){
            //     $win_game_result = explode(',', $order_info[$key]['win_game_result']);
            //     foreach ($win_game_result as $ke => $val) {
            //         $win_game_result[$ke] = db('fb_game_cate')->field('cate_name,cate_odds,is_win')->where('cate_id='.$win_game_result[$ke])->find();
            //     }
            //     $data['order_info'][$key]['win_game_result'] = $win_game_result;  
            // }
        }
        // dump($data);die;
        $this->assign("data",$data);
        return view();
    }

    public function rank($score)
    {
        switch (true) 
        {
            case $score >= 7:  return 'total_seven_gt';
            case $score == 6:  return 'total_six';
            case $score == 5:  return 'total_five';
            case $score == 4:  return 'total_four';
            case $score == 3:  return 'total_three';
            case $score == 2:  return 'total_two';
            case $score == 1:  return 'total_one';
            case $score == 0:  return 'total_zero';
        }
    }
    // 结算
    // 通过比赛ID查其所有的投注选项ID -> 在通过投注选项ID查询所有的订单 -> 结算
    public function fb_over($id)
    {
        set_time_limit(0);
        // 开启事务
        Db::startTrans();

        $Detail = new Detail();

        $game = db('fb_game')->where('id='.$id)->find();
        if($game['is_postpone'] == 1){
            db('fb_game_cate')->where('game_id='.$id)->update(array('is_win'=>1,'cate_odds'=>1));
        }else{
            if(empty($game['top_score']) && empty($game['down_score'])){
                $this->error("请输入分数比!");
            }
            $home_score = explode(':', $game['down_score'])[0];
            $road_score = explode(':', $game['down_score'])[1];
            $top_home_score = explode(':', $game['top_score'])[0];
            $top_road_score = explode(':', $game['top_score'])[1];
            //让分分数
            $home_let_score = $home_score+$game['let_score'];
            //俩队总进球数
            $total_score = $game['total_score'];
            //获取比分所有选项
            $score = db('fb_code')->where('code_pid=3')->column('code_name');
            //根据比赛分数修改本场比赛的所有投注选项的中奖状态
            db('fb_game_cate')->where('game_id='.$id)->setField('is_win',2);
            if($top_home_score > $top_road_score){
                $half_full = 'win_';
            }else if($top_home_score < $top_road_score){
                $half_full = 'lose_';
            }else{
                $half_full = 'eq_';
            }
            if($home_score > $road_score){
                $half_full .= 'win';
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="home_win"')->setField('is_win',1);
            }else if($home_score < $road_score){
                $half_full .= 'lose';
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="home_lose"')->setField('is_win',1);
            }else{
                $half_full .= 'eq';
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="home_eq"')->setField('is_win',1);
            }
            db('fb_game_cate')->where('game_id='.$id.' and cate_code="'.$half_full.'"')->setField('is_win',1);

            if($home_let_score > $road_score){
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="let_score_home_win"')->setField('is_win',1);
            }else if($home_let_score < $road_score){
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="let_score_home_lose"')->setField('is_win',1);
            }else{
                db('fb_game_cate')->where('game_id='.$id.' and cate_code="let_score_home_eq"')->setField('is_win',1);
            }
            if(in_array($game['down_score'], $score)){
                db('fb_game_cate')->where('game_id='.$id.' and cate_name="'.$game['down_score'].'"')->setField('is_win',1);
            }else{
                if($home_score > $road_score){
                    db('fb_game_cate')->where('game_id='.$id.' and cate_code="win_other"')->setField('is_win',1);
                }else if($home_score < $road_score){
                    db('fb_game_cate')->where('game_id='.$id.' and cate_code="lose_other"')->setField('is_win',1);
                }else{
                    db('fb_game_cate')->where('game_id='.$id.' and cate_code="eq_other"')->setField('is_win',1);
                }
            }
            db('fb_game_cate')->where('game_id='.$id.' and cate_code="'.$this->rank($total_score).'"')->setField('is_win',1);
        }
        
        // 获取这场比赛的所有中奖选项ID::cate_ids
        $cate_id_arr = db('fb_game_cate')->where('game_id='.$id.' and is_win=1')->column('cate_id');
        
        $cate_ids = implode(',', $cate_id_arr);
        // 并将所有的中奖ID添加到明细里
        db('order_info oi')
        ->join('order o','oi.order_id=o.order_id')
        ->where('o.order_status=1 and oi.game_cate='.$this->game_cate.' and oi.game_id='.$id.' and oi.game_status=0')
        ->setField('win_game_result',$cate_ids);
        // 查询 已付款 订单明细里所有有关于这场比赛数据 并更改他们的比赛状态和中奖彩果
        $order_info_all = db('order_info oi')
        ->join('order o','oi.order_id=o.order_id')
        ->where('o.order_status=1 and oi.game_cate='.$this->game_cate.' and oi.game_id='.$id.' and oi.game_status=0')
        ->select();
        // dump($order_info_all);
        // Db::rollback();
        // die;
        if(!empty($order_info_all)){
            foreach ($order_info_all as $key => $value) {
                $tz_result = explode(',', $order_info_all[$key]['tz_result']);
                foreach ($tz_result as $ke => $val) {
                    // 判断投注选项是否在中奖集合中
                    if(in_array($tz_result[$ke], $cate_id_arr)){
                        $result[] = $tz_result[$ke];
                    }else{
                        $cate_code = db('fb_game_cate')->where('cate_id='.$tz_result[$ke])->value('cate_code');
                        $pid = db('fb_code')->where('code="'.$cate_code.'"')->value('code_pid');
                        $code_arr = db('fb_code')->where('code_pid='.$pid)->column('code');

                        foreach ($code_arr as $k => $v) {
                            $cid = db('fb_game_cate')->where('game_id='.$id.' and is_win=1 and cate_code ="'.$v.'"')->value('cate_id');
                            if(!empty($cid)){
                                $cate_id[] = $cid;
                            }
                        }
                        $result[] = implode(',', $cate_id);
                        unset($cate_id);
                    }
                }
                $win_result = implode(',', $result);
                unset($result);
                
                db('order_info')->where('order_info_id='.$order_info_all[$key]['order_info_id'])->update(array('win_result'=>$win_result,'game_status'=>1));

                $order_ids[] = $order_info_all[$key]['order_id'];
            }
            $order_ids = array_unique($order_ids);
            
            $Group = new Group();
            // $order_ids = db('order_info')->where('game_cate='.$this->game_cate.' and game_id='.$id)->column('order_id');
            foreach ($order_ids as $key => $value) {
                // 判断该笔订单先所有比赛的比赛状态(0:未结束|1:已结算)
                $game_status = db('order_info')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->column('game_status');
                if(!in_array('0', $game_status)){
                    // 如果所有比赛全部为 已结算 结算该笔订单
                    $order = db('order')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->find();

                    $order_tz_result = db('order_info')->field('dan,tz_result')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->select();
                    $order_win_result = db('order_info')->field('dan,win_result tz_result')->where('game_cate='.$this->game_cate.' and order_id='.$order_ids[$key])->select();

                    $chuan = explode(',', $order['chuan']);
                    $order_tz_data = array();
                    $order_win_data = array();
                    for ($i=0; $i < count($chuan); $i++) { 
                        $get_tz_data = $Group->get_group($chuan[$i],$order_tz_result);
                        for ($j=0; $j < count($get_tz_data); $j++) { 
                            $order_tz_data[] = $get_tz_data[$j];
                        }
                        $get_win_data = $Group->get_group($chuan[$i],$order_win_result);
                        for ($j=0; $j < count($get_win_data); $j++) { 
                            $order_win_data[] = $get_win_data[$j];
                        }
                    }

                    $total_odds = 0;

                    if(count($order_tz_data) == count($order_win_data)){
                        for ($i=0; $i < count($order_tz_data); $i++) { 
                            if($order_tz_data[$i] == $order_win_data[$i]){
                                $cate_odds = 1;
                                $exp_tz_data = explode(',', $order_tz_data[$i]);
                                for ($j=0; $j < count($exp_tz_data); $j++) { 
                                    $order_tz_odds[] = db('fb_game_cate')->where('cate_id='.$exp_tz_data[$j])->value('cate_odds');
                                    $cate_odds *= db('fb_game_cate')->where('cate_id='.$exp_tz_data[$j])->value('cate_odds');
                                }
                                $total_odds += $cate_odds;
                            }
                        }
                        if($order['order_type'] != 3){
                            $total_odds = ($total_odds*0.08)+$total_odds;
                        }
                        $win_money = $total_odds*$order['multiple']*2;
                    }

                    if($total_odds > 0){
                        // 如果订单为合买订单
                        if($order['order_type'] == 2){

                            // 获取合买信息
                            $order_hm_user = db('order_hm_user')->where('order_id='.$order_ids[$key])->find();
                            $order_hm_desc = db('order_hm_desc')->where('hm_id='.$order_hm_user['hm_id'])->find();
                            if($order_hm_desc['hm_status'] == 2){
                                // 将合买订单总中奖金额添加  
                                db('order_hm_desc')->where('hm_id='.$order_hm_desc['hm_id'])->update(array('total_win_money'=>$win_money));
                                // 然后用总中奖金额 除以 合买总分数 等于 每份中奖金额
                                $one_win_money = $win_money / $order_hm_desc['hm_num']; // 每份中奖金额
                                // 合买订单应分金额
                                $user_win_money = $one_win_money * $order_hm_user['pay_num'];
                                // 判断是不是发起人 不是得话将佣金返给发起人
                                if($order_hm_user['user_id'] != $order_hm_desc['user_id']){
                                    /********* 将佣金发放给发起人 *********/
                                    $commission = $user_win_money * $order_hm_desc['brokerage']; 
                                    $res = $Detail->add_detail($order_hm_desc['user_id'], 7, $commission, 1);
                                    /**添加明细**/
                                    if($res > 0){
                                        db('user')->where('id='.$order_hm_desc['user_id'])->setInc('no_balance',$commission*0.04);
                                        db('user')->where('id='.$order_hm_desc['user_id'])->setInc('balance',$commission*0.06);
                                        db('user')->where('id='.$order_hm_desc['user_id'])->setInc('amount_money',$commission);
                                    }else{
                                        // 回滚事务
                                        Db::rollback();
                                    }
                                    /********* 将佣金发放给发起人 END *********/
                                    // 扣除佣金
                                    $user_win_money = $user_win_money - $commission;
                                }
                                // 订单生效 将应分金额添加到列表数据中
                                db('order_hm_user')->where('hm_user_id='.$order_hm_user['hm_user_id'])->setField('win_money',$user_win_money);
                                // 将中奖金额发放
                                db('order')->where('order_id='.$order_ids[$key])->update(array('win_money'=>$user_win_money,'is_win'=>1));
                                $res = $Detail->add_detail($order['user_id'], 4, $user_win_money, 1);
                                /**添加明细**/
                                if($res > 0){
                                    // 将中奖金额添加到用户余额里
                                    db('user')->where('id='.$order['user_id'])->setInc('no_balance',$user_win_money*0.04);
                                    db('user')->where('id='.$order['user_id'])->setInc('balance',$user_win_money*0.06);
                                    db('user')->where('id='.$order['user_id'])->setInc('amount_money',$user_win_money);
                                }else{
                                    // 回滚事务
                                    Db::rollback();
                                }    
                            }
                        }else if($order['order_type'] == 3){
                            $one_win_money = $win_money / $order['multiple'];

                            // 将方案中奖金额添加  
                            db('order_gd_desc')->where('gd_id='.$order_gd_desc['gd_id'])->update(array('total_win_money'=>$one_win_money));

                            // 获取跟单信息
                            $order_gd_user = db('order_gd_user')->where('order_id='.$order_ids[$key])->find();
                            $order_gd_desc = db('order_gd_desc')->where('gd_id='.$order_gd_user['gd_id'])->find();

                            $win_money = $one_win_money * $order_gd_user['pay_num'];

                            if($order_gd_user['user_id'] != $order_gd_desc['user_id']){
                                /********* 将佣金发放给发起人 *********/
                                $commission = $win_money * $order_gd_desc['brokerage']; 
                                $commission = $commission * 0.7;
                                $res = $Detail->add_detail($order_gd_desc['user_id'], 8, $commission, 1);
                                /**添加明细**/
                                if($res > 0){
                                    db('user')->where('id='.$order_gd_desc['user_id'])->setInc('no_balance',$commission*0.04);
                                    db('user')->where('id='.$order_gd_desc['user_id'])->setInc('balance',$commission*0.06);
                                    db('user')->where('id='.$order_gd_desc['user_id'])->setInc('amount_money',$commission);
                                }else{
                                    // 回滚事务
                                    Db::rollback();
                                }
                                /********* 将佣金发放给发起人 END *********/
                                // 扣除佣金
                                $win_money = $win_money - $commission;
                            }
                            // 订单生效 将应分金额添加到列表数据中
                            db('order_gd_user')->where('gd_user_id='.$order_gd_user['gd_user_id'])->setField('win_money',$win_money);
                            // 将中奖金额发放
                            db('order')->where('order_id='.$order_ids[$key])->update(array('win_money'=>$win_money,'is_win'=>1));
                            $res = $Detail->add_detail($order['user_id'], 4, $win_money, 1);
                            /**添加明细**/
                            if($res > 0){
                                // 将中奖金额添加到用户余额里
                                db('user')->where('id='.$order['user_id'])->setInc('no_balance',$win_money*0.04);
                                db('user')->where('id='.$order['user_id'])->setInc('balance',$win_money*0.06);
                                db('user')->where('id='.$order['user_id'])->setInc('amount_money',$win_money);
                            }else{
                                // 回滚事务
                                Db::rollback();
                            }  
                        }else{
                            db('order')->where('order_id='.$order_ids[$key])->update(array('win_money'=>$win_money,'is_win'=>1));
                            $res = $Detail->add_detail($order['user_id'], 4, $win_money, 1);
                            /**添加明细**/
                            if($res > 0){
                                db('user')->where('id='.$order['user_id'])->setInc('no_balance',$win_money*0.04);
                                db('user')->where('id='.$order['user_id'])->setInc('balance',$win_money*0.06);
                                db('user')->where('id='.$order['user_id'])->setInc('amount_money',$win_money);
                            }else{
                                // 回滚事务
                                Db::rollback();
                            } 
                        }
                    }else{
                        db('order')->where('order_id='.$order_ids[$key])->update(array('is_win'=>2));
                    }
                }
            }
        }
        // 操作成功 修改比赛状态
        $res = db('fb_game')->where('id='.$id)->setField('status',0);
        if($res > 0) {
            // 提交事务
            Db::commit();

            $this->success("操作成功");
        }else{
            // 回滚事务
            Db::rollback();
        }
    }
     /** 

      * 根据时间戳返回星期几 

      * @param string $time 时间戳 

      * @return 星期几 

      */

    public function weekday($time) 
    { 
        if(is_numeric($time)) 
        { 
            $weekday = array('周日','周一','周二','周三','周四','周五','周六'); 
            return $weekday[date('w', $time)]; 
        } 
        return false; 
    } 

    /**
     * 编辑指定字段
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function edit_field(){
        $data = $_REQUEST;
        $flag = db("fb_game")->update($data);
        $fb_game = db('fb_game')->where('id='.$data['id'])->find();
        if(!empty($fb_game['top_score']) && !empty($fb_game['down_score'])){
            $total_score = explode(':', $fb_game['down_score']);
            $total = $total_score[0]+$total_score[1];
            db('fb_game')->where('id='.$data['id'])->setField('total_score',$total);
        }
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }
}
