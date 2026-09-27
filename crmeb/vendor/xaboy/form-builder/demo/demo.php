<?php

namespace App;

use  FormBuilder\Factory\Elm;
use FormBuilder\Form\IviewForm;

require '../vendor/autoload.php';


$action = '/save.php';
$method = 'POST';

$input = Elm::input('goods_name', 'Tên sản phẩm')->required();
$textarea = Elm::textarea('goods_info', 'Mô tả ngắn sản phẩm');
$switch = Elm::switches('is_open', 'Bật')->activeText('Bật')->inactiveText('Tắt');

//创建表单
$form = (new IviewForm($action))->setMethod($method);

//添加组件
$form->setRule([$input, $textarea]);
$form->append($switch);

$form->formData([
    'goods_name' => 'goods_name123',
    'asdf' => 'asdfafd',
    'is_open' => '0'
])->setValue('goods_info', "asdf\r\nadfa");

//生成表单页面
$formHtml = $form->view();

echo $formHtml;