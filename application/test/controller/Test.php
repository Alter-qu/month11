<?php

namespace app\test\controller;

use app\test\model\Picture;
use app\test\model\User;
use think\Controller;
use think\Request;

class Test extends Controller
{
    /**
     * 显示登录页面
     *
     * @return \think\Response
     */
    public function index()
    {
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function login(Request $request)
    {
        //接收数据
        $param=$request->param();
        //验证数据
        $result = $this->validate(
            $param,
            [
                'name'  => 'require|token',
                'pwd'   => 'require',
            ]);
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);
        }
        //判断是否正确
        //加密设置
        $pwd=md5(md5($param['pwd']));
        $res=User::where("name",$param['name'])->where("pwd",$pwd)->find();
        if($res){
            //如果正确，登录成功，存入seesion
            session('user',$res->toArray());
            return redirect("Test/show");
           // return json(['code'=>200,'msg'=>'登录成功','result'=>$res]);
        }else{
            //错误返回三要素，提示错误
            return json(['code'=>500,'msg'=>'账号或密码错误','result'=>null]);
        }
    }

    /**
     * 展示登录成功，显示对应相册
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function show()
    {
        $id=session('user.id');
        $data=Picture::with("picUser")->where('uid',$id)->paginate(4);
        $name=session('user.nickname');
       return view("",['name'=>$name,'list'=>$data]);
    }

    /**
     * 退出登录
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function logout()
    {
        session('user',null);
        return redirect("Test/index");
    }

    /**
     * 存储上传的信息
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //判断是否登录，没登录不让继续
       $user= session('user');
       if (empty($user)){
           $this->error("请先登录",'Test/index');
       }
       //接收并验证数据
        $param=$request->param();
        // 获取表单上传文件
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        $info = $file->validate(['size'=>1024*1024*3,'ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
        if($info){
            // 成功上传后 获取上传信息
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
            $param['img']= $info->getSaveName();
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
        //验证其他信息
        $result = $this->validate(
            $param,
            [
                'type'  => 'require|token',
                'title'   => 'require',
            ]);
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);
        }
        //添加上对应的用户信息ID
        $param['uid']=session('user.id');
        //存储入库
        $res=Picture::create($param,true);
        //dump($res->toArray());
        $name=session('user.name');
        return redirect("Test/show");
    }

    /**
     * 搜索并展示界面
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function search(Request $request)
    {
        //判断是否登录，没登录不让继续
        $user= session('user');
        if (empty($user)){
            $this->error("请先登录",'Test/index');
        }
        //接收并验证
        $param=$request->param();

        //验证
        $result = $this->validate(
            $param,
            [
                'search'  => 'require|token',
            ]);
        if(true !== $result){
            // 验证失败 输出错误信息
            $this->error($result);
        }

        $param['id']=session('user.id');
        $res=Search::search($param);
        if ($res['code']==500){
            $this->error($res['msg']);
        }
        //dump($res);
        return view('',['list'=>$res['result']]);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
