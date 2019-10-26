<?php
/**
 * lemocms
 * ============================================================================
 * 版权所有 2018-2027 lemocms，并保留所有权利。
 * 网站地址: https://www.lemocms.com
 * ----------------------------------------------------------------------------
 * 采用最新Thinkphp6实现
 * ============================================================================
 * Author: yuege
 * Date: 2019/8/2
 */
namespace app\admin\controller;
use think\facade\Config;
use think\facade\Request;
use app\admin\model\AdminLog as LogModel;
use think\facade\View;
class Adminlog extends Base{

    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * @return array|string
     * @throws \think\db\exception\DbException
     */
    public function index(){

        if(Request::isPost()){
            $keys=Request::post('keys','','trim');
            $page = Request::post('page')?Request::post('page'):1;
            $list = LogModel::where('status',1)
               ->where('username|log_title','like','%'.$keys.'%')
               ->order('id desc')
               ->paginate(array('list_rows'=> $this->pageSize,'page'=>$page))
               ->toArray();
            if(!empty($list['data'])){
                foreach ($list['data'] as $k => $v) {
                    $useragent = explode('(', $v['log_agent']);
                    $list['data'][$k]['log_agent'] = $useragent[0];
                }
            }
            $result = ['code'=>0,'msg'=>lang('get info success'),'data'=>$list['data'],'count'=>$list['total']];
            return $result;
        }

        return View::fetch();
    }

    /**
     * @throws \Exception
     * 删除日志 单个+批量
     */
    public function delete(){
        $id = Request::post('id');
        if(!$id){
            $this->error(lang('id is not exist'));

        }
        if(!is_array($id)){
            $id = [$id];
        }

        if(LogModel::destroy($id)){
            $this->success(lang('delete success'));
        }else{
            $this->error(lang('delete fail'));
        }



    }

}
