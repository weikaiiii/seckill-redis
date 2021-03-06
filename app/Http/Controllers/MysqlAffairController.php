<?php

namespace App\Http\Controllers;

use App\Goods;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * Class MysqlAffairController
 * @package App\Http\Controllers
 * @name: mysql事务解决并发秒杀场景
 * @author: weikai
 * @date: 2018/6/12 14:39
 */
class MysqlAffairController extends Controller
{
    /**
     * @param $goods
     * @param $userId
     * @return bool|string
     * @name: 生成订单号
     * @author: weikai
     * @date: 2018/6/12 14:54
     */
    public function buildOrderNo()
    {

        return date('ymd').rand(1000,9999);
    }

    /**
     * @name: 生成订单事务处理
     * @author: weikai
     * @date: 2018/6/12 15:38
     */
    public function buildOrder()
    {
        //开启事务
        DB::beginTransaction();
        try{
                $goodsNum = Goods::where('id',1)->value('num');//库存
                $goodsPrice = Goods::where('id',1)->value('price');//单价
                if($goodsNum>=1) {
                    $orderNo = $this->buildOrderNo();//单号
                    //赋值
                    $data['order_num'] = $orderNo;
                    $data['user_id'] = 1;
                    $data['goods_id'] = 1;
                    $data['order_price'] = $goodsPrice;
                    $bool = Order::insert($data);//插入订单数据
                    $numBool = Goods::where('id',1)->decrement('num');//库存自减
                    if($bool && $numBool) DB::commit();//提交事务
                }

        }catch (\Exception $e){
            echo $e->getMessage();
            DB::rollBack();//回滚事务
        }



    }
}
