<?php
namespace Home\Model;
use Think\Model;

class TiceModel extends Model{
    //表示插入的时候只允许插入下边三个字段
    //public $insertFields = 'goods_name,goods_sn,goods_desc';
    // protected $_auto = array(
    //     array('add_time','time',1,'function'),
    // ); 
    //自动验证是create()的时候自动验证的，如果没有create就不会验证！！create()还会自动过滤数据库中没有的字段！！
    public $_validate = array(
        // array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
        array('xueid','','此学号已经存在于数据库中','1','unique','3'),
    );
}
