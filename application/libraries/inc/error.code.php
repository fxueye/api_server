<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Err_Code {
    const ERR_OK =   '0000'; //成功
    //1001  --- 1099  系统错误
    const ERR_PARA =   '1001'; //参数错误
    const ERR_PARAM_UNKNOWN =   '1002'; //参数非法
    const ERR_PARAM_SIGN = '1003'; //签名校验不通过
    const ERR_NO_SELECT_DATA = '1004'; //没有可查询的数据
    const ERR_DB = '1005'; //数据库一般性错误
    const ERR_LONG_TIME = '1006'; //接口超时
    const ERR_DB_INSERT = '1007'; //数据库插入错误
    const ERR_DB_UPDATE = '1008'; //数据库更新错误
    const ERR_UNKOWNL = '1099'; //未知错误
    
    //1101  --- 1999  用户信息错误
    const ERR_REPEAT_SUB = '1101';//数据已经提交,请未重复提交
    const ERR_MOBILE_FORMAT = '1102'; //手机号码非法 

    const ERR_SEARCH_TXT_EMPTY = '3001'; //搜索key为空
    const ERR_FEEDBACK_EMPTY = '3002'; //反馈结果不空
}   