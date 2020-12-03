<?php

namespace app\test\controller;

use app\test\model\Picture;
use think\Controller;
use think\Request;

class Search extends Controller
{
    /**
     * 搜索
     *
     * @return \think\Response
     */
   static public function search($data)
    {
        //根据分类搜索
        $type=Picture::with("picUser")
            ->where('uid',$data['id'])
            ->whereOr('type','like',"%{$data['search']}%")
            ->whereOr('title','like',"%{$data['search']}%")
            ->paginate(3);
        //根据关键词搜索
//        $title=Picture::with("picUser")
//            ->where('uid',$data['id'])
//            ->where('title','like',"%{$data['search']}%")
//            ->paginate(3);
        //返回对应数据
        if (!empty($type->toArray())){
            return ['code'=>200,'msg'=>'分类查询','result'=>$type];
        }else{
            return ['code'=>500,'msg'=>'没有信息','result'=>null];
        }
    }


}
