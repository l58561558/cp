<?php
namespace app\adminz\controller;

class Article extends Base
{
    /**
     * 列表页面
     * @return [type] [description]
     */
    public function index(){
        return view();
    }

    /**
     * 插入数据
     */
    public function add(){
        if(IS_POST){
            $file = request()->file('pic');

            $data = $_REQUEST;
            // dump($data);die;
            //删除非数据库字段
            unset($data['pic']);
            
            if($file){
                $info = $file->move(config('uploads_path.path').DS.'article');
                if($info){
                    $data['pic'] = $info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }

            $data['add_time'] = date('Y-m-d H:i:s');
            $data['is_show'] = input("is_show")?1:0;
            $article_id = db("article")->insert($data,false,true);
            if($article_id){
                $this->success("添加成功");
            }
            $this->error("添加失败");
        }
        return view();
    }

    /**
     * 编辑记录
     * @param  integer $article_id [description]
     * @return [type]          [description]
     */
    public function edit($article_id = 0){
        if(IS_POST){
            $file = request()->file('pic');

            $data = $_REQUEST;
            //删除非数据库字段
            unset($data['pic']);
            if($file){
                $info = $file->move(config('uploads_path.path').DS.'article');
                if($info){
                    $data['pic'] = $info->getSaveName();
                }else{
                    $this->error($file->getError());
                }
            }

            $data['is_show'] = input("is_show")?1:0;
            $flag = db("article")->update($data);
            if($flag || $flag === 0){
                $this->success("保存成功");
            }
            $this->error("保存失败");
        }
        $article = db('article')->where("article_id=".$article_id)->find();
        $this->assign("article",$article);
        return view();
    }

    /*保存相册*/
    public function upload(){
        $file = request()->file('file');
        // dump($file);die;
        if($file){
            $info = $file->move(config('uploads_path.path').DS.'article');
            if($info){
                $data['img_url'] = $info->getSaveName();
            }else{
                $this->error($file->getError());
            }
        }
        $data['add_time'] = date('Y-m-d H:i:s');
        $gallery_id = db("article_gallery")->insert($data,false,true);
        if($gallery_id > 0){
            $result['error'] = 0;
            $result['img_url'] = $data['img_url'];
            $result['gallery_id'] = $gallery_id;
        }else{
            $result['error'] = 1;
            $result['content'] = '图片路径写入数据库出错';
        }
        $this->ajaxReturn($result);
        // dump($file);die;
    }

    //删除相册中的图片
    public function del_article_gallery($gallery_id){
        // $table = M('article_gallery');
        $gallery = db("article_gallery")->where('gallery_id='.$gallery_id)->field('gallery_id,img_url')->find();

        if(!$gallery['gallery_id']){
            
            $result['error'] = 1;
            $result['content'] = '未能获取到图片路径';
            $this->ajaxReturn($result);
            
        }

        $res = db("article_gallery")->where('gallery_id='.$gallery_id)->delete(); //删除数据库记录
        if($res){
            //删除图片文件
            if(!empty('/public/uploads/article/'.$gallery['img_url']) && file_exists('/public/uploads/article/'.$gallery['img_url'])){
                @unlink('/public/uploads/article/'.$gallery['img_url']);
            }

            $result['error'] = 0;
            $result['content'] = '删除成功';
        }else{
            $result['error'] = 1;
            $result['content'] = '删除失败';
        }

        $this->ajaxReturn($result);
    }
    /*
     * 递归遍历
     * @param $data array
     * @param $id int
     * return array
     * */
    //四级分类查询
    public function get_data($data, $id=0){
        $list = array();
        foreach($data as $v) {
            if($v['parent_id'] == $id) {
                $v['child'] = $this->get_data($data, $v['cate_id']);
                if(empty($v['child'])) {
                    unset($v['child']);
                }
                array_push($list, $v);
            }
        }
        return $list;     
    }

    /**
     * 获取数据
     * @param  integer $position_id [description]
     * @return [type]               [description]
     */
    public function get_article_list(){
        $map = '';
        $count = db("article")->where($map)->count();
        $list = db("article")->where($map)->paginate(20,$count);
        //当cate_id是1级分类的时候查询其下面所有子分类文章
        //获取分页
        $page = $list->render();
        $this->assign("page",$page);
        $this->assign("_list",$list);
        $html = $this->fetch("tpl/article_list");
        $this->ajaxReturn(['data'=>$html,'code'=>1]);
    }

    /**
     * 编辑指定字段
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function article_edit_field($id = 0){
        //模块化更新
        // $flag = model('article')->allowField(true)->save($_REQUEST,['article_id'=>$id]);
        $data = $_REQUEST;
        //删除非数据库字段
        unset($data['id']);
        $data['article_id'] = $id;
        $flag = db("article")->update($data);
        ($flag || $flag===0)  && $this->success("保存成功");
        $this->error("保存失败");
    }

    /**
     * 删除数据
     * @param  integer $id [description]
     * @return [type]      [description]
     */
    public function article_delete($id = 0){
        $map = array();
        $map['article_id'] = $id;
        $flag = db('article')->where($map)->delete();
        if($flag){
            $this->success("删除成功");
        }
        $this->error('删除失败');
    }
}
