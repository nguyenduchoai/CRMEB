<?php
/**
 * FormBuilder表单生成器
 * Author: xaboy
 * Github: https://github.com/xaboy/form-builder
 */

namespace FormBuilder;


class Json
{
    protected static function result($code, $msg = 'ok', $data = [])
    {
        return json_encode(compact('code', 'msg', 'data'));
    }

    public static function succ($msg, $data = [])
    {
        return static::result(200, $msg, $data);
    }

    public static function fail($msg, $data = [])
    {
        return static::result(400, $msg, $data);
    }

    public static function uploadSucc($filePath, $msg = 'Tải lên thành công', $data = [])
    {
        $data['filePath'] = $filePath;
        return static::succ($msg, $data);
    }

    public static function uploadFail($msg = 'Tải lên thất bại', $data = [])
    {
        return static::fail($msg, $data);
    }
}