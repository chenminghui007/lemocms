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
namespace app\common\lib;

use app\admin\controller\Base;
use app\admin\model\AuthRule;
use app\common\controller\Backend;
use think\facade\Db;
use think\facade\Request;
class Menu extends Backend
{
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
    }

    //获取左侧主菜单
    public static function authMenu($arr,$pid=0,$rules=[]){
        $authrules = explode(',',session('admin.rules'));
        $authopen = AuthRule::where('auth_open',1)->column('id');
        if($authopen){
            $authrules = array_unique(array_merge($authrules,$authopen));
        }
        $list =array();
        foreach ($arr as $k=>$v){
            $v['href'] = url($v['href']);
            if (session('admin.id') != 1) {
                if ($v['pid'] == $pid){
                    if(in_array($v['id'],$authrules)){
                        $v['child'] = self::authMenu($arr,$v['id']);
                        $list[] = $v;
                    }
                }
            }else{
                if ($v['pid'] == $pid) {
                    $v['child'] = self::authMenu($arr, $v['id']);
                    $list[] = $v;
                }
            }

        }

        return $list;

    }

    /*
    * 自定义菜单排列
    */
    public static function menu($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $v['ltitle'] = $v['lefthtml'] . $v['title'];
                $arr[] = $v;
                $arr = array_merge($arr, self::menu($cate, $lefthtml, $v['id'], $lvl + 1, $leftpin + 20));
            }
        }

        return $arr;
    }

   public  static  function cate($cate, $lefthtml = '|— ', $pid = 0, $lvl = 0, $leftpin = 0)
    {
        $arr = array();
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                $v['lvl'] = $lvl + 1;
                $v['leftpin'] = $leftpin + 0;
                $v['lefthtml'] = str_repeat($lefthtml, $lvl);
                $arr[] = $v;
                $arr = array_merge($arr, self::menu($cate, $lefthtml, $v['id'], $lvl + 1, $leftpin + 20));
            }
        }

        return $arr;
    }

   public  static  function auth($cate, $rules, $pid = 0)
    {
        $arr = array();
        $rulesArr = explode(',', $rules);
        foreach ($cate as $v) {
            if ($v['pid'] == $pid) {
                if (in_array($v['id'], $rulesArr)) {
                    $v['checked'] = true;
                }
                $v['open'] = true;
                $arr[] = $v;
                $arr = array_merge($arr, self::auth($cate, $v['id'], $rules));
            }
        }
        return $arr;
    }




}
