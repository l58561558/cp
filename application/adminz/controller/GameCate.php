<?php
namespace app\adminz\controller;

class GameCate extends Base
{
    /**
     * 列表页面
     * @return [type] [description]
     */
    public function index($type_id=0){
        
    	$data = db("GameCate")->select();
        $this->assign('data',$data);
        $this->assign('type_id',$type_id);

        return view();
    }

    /**
     * 插入数据
     */
    public function add(){
        if(IS_POST){
            $file = request()->file('pic');
            $data = $_REQUEST;
            //删除非数据库字段
            unset($data['pic']);

            if($file){
                $info = $file->move(config('uploads_path.path').DS.'game_cate');
                if($info){
                    $data['pic'] = $info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }
            $game_id = db("game_cate")->insert($data,false,true);
            if($game_id){
                $this->success("添加成功");
            }
            $this->error("添加失败");
        }
        $game_type = db("game_type")->select();
        $this->assign('game_type',$game_type);
        return view();
    }

    /**
     * 编辑记录
     * @param  integer $game_id [description]
     * @return [type]          [description]
     */
    public function edit($game_id = 0){
        if(IS_POST){
            $file = request()->file('pic');
            $data = $_REQUEST;
            //删除非数据库字段
            unset($data['pic']);

            if($file){
                $info = $file->move(config('uploads_path.path').DS.'game_cate');
                if($info){
                    $data['pic'] = $info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }
            $flag = db("game_cate")->update($data);
            if($flag || $flag === 0){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $game_type = db("game_type")->select();
        $this->assign('game_type',$game_type);
        $game_cate = db('game_cate')->where("game_id=".$game_id)->find();
        $this->assign("game_cate",$game_cate);
        return view();
    }

    /**
     * 获取数据
     * @param  integer $type_id [description]
     * @return [type]               [description]
     */
    public function get_game_cate_list($type_id = 0){
        $map = '';
        if($type_id){
            $map = "type_id=".$type_id;
        }
        $count = db("game_cate")->where($map)->count();
        $list = db("game_cate")->where($map)->paginate(20,$count);
        //获取分页
        $page = $list->render();
        //遍历数据
        $list->each(function($item,$key){
            $item['type_name'] = db("game_type")->where("type_id=".$item['type_id'])->value('type_name');
            return $item;
        });
        $this->assign("page",$page);
        $this->assign("_list",$list);
        $html = $this->fetch("tpl/game_cate_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    /**
     * 编辑指定字段
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function game_edit_field($id = 0){
        //模块化更新
        // $flag = model('game')->allowField(true)->save($_REQUEST,['game_id'=>$id]);
        $data = $_REQUEST;
        //删除非数据库字段
        $data['game_id'] = $data['id'];
        unset($data['id']);
        
        $flag = db("game_cate")->where('game_id='.$id)->update($data);
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }

    /**
     * 删除数据
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function game_delete($id = 0){
        $map = array();
        $map['game_id'] = $id;
        $flag = db('game_cate')->where($map)->delete();
        if($flag){
            $this->success("删除成功");
        }
        $this->error('删除失败');
    }
}
