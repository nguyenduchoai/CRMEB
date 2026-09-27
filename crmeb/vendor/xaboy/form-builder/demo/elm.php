<?php

namespace App;


require '../vendor/autoload.php';

use FormBuilder\Annotation\Col;
use FormBuilder\Annotation\Emit;
use FormBuilder\Annotation\Group;
use FormBuilder\Annotation\Validate\Required;
use FormBuilder\Annotation\Validate\Min;
use FormBuilder\Annotation\Validate\Range;
use FormBuilder\Factory\Elm;
use FormBuilder\Handle\ElmFormHandle;
use FormBuilder\UI\Elm\Components\Rate;
use FormBuilder\UI\Iview\Components\DatePicker;


class GoodsForm extends ElmFormHandle
{
    protected $action = 'save.php';
    protected $title = 'Kiểm thử Handle';
    protected $fieldTitles = [
        'start_time' => 'Thời gian bắt đầu',
        'star' => 'Lượt thích'
    ];

    protected $scene = 'get';

    protected function getScene()
    {
//        $this->except = ['goods_name'];
    }

    /**
     * @Col(6)
     * @return \FormBuilder\UI\Elm\Components\Input
     */
    public function goods_name_field()
    {
        return Elm::input('goods_name', 'Tên sản phẩm')->required();
    }

    /**
     * @Required()
     * @Group()
     * @Col(8)
     * @Range({10,1000},message = "最少输入10个字")
     * @return \FormBuilder\UI\Elm\Components\Input
     */
    public function goods_info_field()
    {
        return Elm::textarea('goods_info', 'Mô tả ngắn sản phẩm');
    }

    /**
     * @Group()
     * @Col(8)
     * @Emit({"change","click"})
     * @return \FormBuilder\UI\Elm\Components\Switches
     */
    public function is_open_field()
    {
        return Elm::switches('is_open', 'Bật');
    }

    /**
     * @Group(2)
     * @Col(12)
     * @return \FormBuilder\UI\Elm\Components\Frame
     */
    public function frame_field()
    {
        return Elm::frameFile('as', 'asd', 'afsdfasdf');
    }

    /**
     * @Group(2)
     * @Col(12)
     * @return \FormBuilder\UI\Elm\Components\Upload
     */
    public function test_field()
    {
        return Elm::uploadFiles('aaa', 'aaa', 'bbb', [1])->required();
    }

    /**
     * @return \FormBuilder\UI\Elm\Components\Hidden
     */
    public function id_field()
    {
        return Elm::hidden('1', '1');
    }

    /**
     * @Required("请输入 testRow")
     * @return array
     */
    public function row_field()
    {
//        return [
//            'type' => 'row',
//            'children' =>
//                [
        return [
            'type' => 'input',
            'field' => 'row',
            'title' => 'test Row',
            'value' => '123',
            'col' => [
                'span' => 12
            ]
        ];
//        ,
//                Elm::input('row2', 'row2', 'asdf')->col(12)
//            ],
//            'native' => true
//        ];
    }

    /**
     * 通过依赖注入方式生成组件
     *
     * @param DatePicker $date
     * @return DatePicker
     */
    public function start_time_field(DatePicker $date)
    {
        return $date->required()->info('asdfasdfasdfsf');
    }

    public function starField(Rate $rate)
    {
        return $rate;
    }

    protected function getFormConfig()
    {
        $config = Elm::config();
        $config->createResetBtn()->show(true);

        return $config;
    }

    protected function getFormData()
    {
        return [
            'goods_name' => 'goods_name123',
            'asdf' => 'asdfafd',
            'is_open' => '0',
            'goods_info' => "asdf\r\nadfa",
            'star' => 0,
            'row' => 'adsfasdfasd'
        ];
    }
}

$formHtml = (new GoodsForm())->view();
//$formHtml = (new GoodsForm())->form()->view();

echo $formHtml;