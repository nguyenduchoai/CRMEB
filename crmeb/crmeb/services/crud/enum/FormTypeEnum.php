<?php
// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2026 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

namespace crmeb\services\crud\enum;

/**
 * Enum loại form
 * Class FormTypeEnum
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/8/14
 * @package crmeb\services\crud\enum
 */
class FormTypeEnum
{
    //Danh sách thả xuống
    const SELECT = 'select';
    //Ô nhập liệu
    const INPUT = 'input';
    //Ô nhập số
    const NUMBER = 'number';
    //Ô văn bản nhiều dòng
    const TEXTAREA = 'textarea';
    //Chọn một ngày giờ
    const DATE_TIME = 'dateTime';
    //Chọn khoảng ngày giờ
    const DATE_TIME_RANGE = 'dateTimeRange';
    //Ô chọn nhiều
    const  CHECKBOX = 'checkbox';
    //Công tắc
    const SWITCH = 'switches';
    //Ô chọn một
    const  RADIO = 'radio';
    //Chọn một ảnh
    const FRAME_IMAGE_ONE = 'frameImageOne';
    //Chọn nhiều ảnh
    const  FRAME_IMAGES = 'frameImages';

    const FORM_TYPE_ALL = [
        self::INPUT,
        self::NUMBER,
        self::RADIO,
        self::SELECT,
        self::TEXTAREA,
        self::FRAME_IMAGE_ONE,
        self::FRAME_IMAGES,
        self::CHECKBOX,
        self::DATE_TIME,
        self::DATE_TIME_RANGE,
    ];


}
