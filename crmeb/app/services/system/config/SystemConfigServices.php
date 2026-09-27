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

namespace app\services\system\config;


use app\dao\system\config\SystemConfigDao;
use app\services\agent\AgentManageServices;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\services\CacheService;
use crmeb\services\FileService;
use crmeb\services\FormBuilder;
use think\facade\Log;

/**
 * Cấu hình hệ thống
 * Class SystemConfigServices
 * @package app\services\system\config
 * @method count(array $where = []) Lấy count theo điều kiện chỉ định
 * @method save(array $data) Lưu dữ liệu
 * @method get(int $id, ?array $field = []) Lấy một dòng dữ liệu
 * @method update($id, array $data, ?string $key = null) Dữ liệu cần chỉnh sửa
 * @method delete(int $id, ?string $key = null) Xóa dữ liệu
 * @method getUploadTypeList(string $configName) Lấy loại tải lên trong cấu hình tải lên
 */
class SystemConfigServices extends BaseServices
{
    /**
     * Handle của biểu mẫu (form)
     * @var FormBuilder
     */
    protected $builder;

    /**
     * Ký tự phân tách dữ liệu biểu mẫu
     * @var string
     */
    protected $cuttingStr = '=>';

    /**
     * URL gửi biểu mẫu
     * @var string[]
     */
    protected $postUrl = [
        'setting' => [
            'url' => '/setting/config/save_basics',
            'auth' => [],
        ],
        'serve' => [
            'url' => '/serve/sms_config/save_basics',
            'auth' => ['short_letter_switch'],
        ],
        'freight' => [
            'url' => '/freight/config/save_basics',
            'auth' => ['express'],
        ],
        'agent' => [
            'url' => '/agent/config/save_basics',
            'auth' => ['fenxiao'],
        ],
        'marketing' => [
            'url' => '/marketing/integral_config/save_basics',
            'auth' => ['point'],
        ]
    ];

    /**
     * Quy tắc kiểm soát tập con
     * @var array[]
     */
    protected $relatedRule = [
        'sign_status' => [
            'son_type' => [
                'sign_mode' => '',
                'sign_remind' => [
                    'son_type' => [
                        'sign_remind_time' => '',
                        'sign_remind_type' => '',
                    ],
                    'show_value' => 1
                ],
                'sign_give_point' => '',
                'sign_give_exp' => '',
            ],
            'show_value' => 1
        ],
        'brokerage_func_status' => [
            'son_type' => [
                'store_brokerage_statu' => [
                    'son_type' => ['store_brokerage_price' => ''],
                    'show_value' => 3
                ],
                'brokerage_bindind' => '',
                'store_brokerage_binding_status' => [
                    'son_type' => ['store_brokerage_binding_time' => ''],
                    'show_value' => 2
                ],
                'spread_banner' => '',
                'brokerage_level' => '',
                'division_status' => '',
                'agent_apply_open' => '',
                'brokerage_window_switch' => '',
            ],
            'show_value' => 1
        ],
        'brokerage_user_status' => [
            'son_type' => [
                'uni_brokerage_price' => '',
                'day_brokerage_price_upper' => '',
            ],
            'show_value' => 1
        ],
        'invoice_func_status' => [
            'son_type' => [
                'special_invoice_status' => '',
            ],
            'show_value' => 1
        ],
        'member_func_status' => [
            'son_type' => [
                'order_give_exp' => '',
                'invite_user_exp' => ''
            ],
            'show_value' => 1
        ],
        'balance_func_status' => [
            'son_type' => [
                'recharge_attention' => '',
                'recharge_switch' => '',
                'store_user_min_recharge' => '',
            ],
            'show_value' => 1
        ],
        'pay_wechat_type' => [
            'son_type' => [
                'pay_weixin_key' => '',
            ],
            'show_value' => 0
        ],
        'pay_wechat_type@' => [
            'son_type' => [
                'pay_weixin_serial_no' => '',
                'v3_transfer_scene_id' => '',
                'pay_weixin_key_v3' => '',
                'v3_pay_public_key' => '',
                'v3_pay_public_pem' => '',
            ],
            'show_value' => 1
        ],
        'image_watermark_status' => [
            'son_type' => [
                'watermark_type' => [
                    'son_type' => [
                        'watermark_image' => '',
                        'watermark_opacity' => '',
                        'watermark_rotate' => '',
                    ],
                    'show_value' => 1
                ],
                'watermark_position' => '',
                'watermark_x' => '',
                'watermark_y' => '',
                'watermark_type@' => [
                    'son_type' => [
                        'watermark_text' => '',
                        'watermark_text_size' => '',
                        'watermark_text_color' => '',
                        'watermark_text_angle' => ''
                    ],
                    'show_value' => 2
                ],
            ],
            'show_value' => 1
        ],
        'customer_type' => [
            'son_type' => [
                'service_feedback' => '',
            ],
            'show_value' => 0
        ],
        'customer_type#' => [
            'son_type' => [
                'customer_phone' => '',
            ],
            'show_value' => 1
        ],
        'customer_type@' => [
            'son_type' => [
                'customer_url' => '',
                'customer_corpId' => '',
            ],
            'show_value' => 2
        ],
        'pay_new_weixin_open' => [
            'son_type' => [
                'pay_new_weixin_mchid' => ''
            ],
            'show_value' => 1
        ],
        'mer_type' => [
            'son_type' => [
                'pay_sub_merchant_id' => '',
                'sp_appid' => ''
            ],
            'show_value' => 1
        ],
        'member_card_status' => [
            'son_type' => [
                'member_price_status' => '',
            ],
            'show_value' => 1
        ],
    ];

    /**
     * SystemConfigServices constructor.
     * @param SystemConfigDao $dao
     * @param FormBuilder $builder
     */
    public function __construct(SystemConfigDao $dao, FormBuilder $builder)
    {
        $this->dao = $dao;
        $this->builder = $builder;
    }

    /**
     * @return array|int[]|string[]
     * @author Wu Xi
     * @email 442384644@qq.com
     * @date 2023/04/12
     */
    public function getSonConfig()
    {
        $sonConfig = [];
        $rolateRule = $this->relatedRule;
        if ($rolateRule) {
            foreach ($rolateRule as $key => $value) {
                $sonConfig = array_merge($sonConfig, array_keys($value['son_type']));
                foreach ($value['son_type'] as $k => $v) {
                    if (isset($v['son_type'])) {
                        $sonConfig = array_merge($sonConfig, array_keys($v['son_type']));
                    }
                }
            }
        }
        return $sonConfig;
    }

    /**
     * Lấy một cấu hình hệ thống
     * @param string $configName
     * @param null $default
     * @return mixed|null
     * @throws \ReflectionException
     */
    public function getConfigValue(string $configName, $default = null)
    {
        $value = $this->dao->getConfigValue($configName);
        return is_null($value) ? $default : json_decode($value, true);
    }

    /**
     * Lấy toàn bộ cấu hình
     * @param array $configName
     * @return array
     * @throws \ReflectionException
     */
    public function getConfigAll(array $configName = [])
    {
        return array_map(function ($item) {
            return json_decode($item, true);
        }, $this->dao->getConfigAll($configName));
    }

    /**
     * Lấy cấu hình và phân trang
     * @param array $where
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfigList(array $where)
    {
        [$page, $limit] = $this->getPageValue();
        $list = $this->dao->getConfigList($where, $page, $limit);
        $count = $this->dao->count($where);
        $tidy_srr = [];
        $configTabList = app()->make(SystemConfigTabServices::class)->getColumn([], 'title', 'id');
        foreach ($list as &$item) {
            $item['value'] = $item['value'] ? (json_decode($item['value'], true) ?: '') : '';
            if ($item['type'] == 'radio' || $item['type'] == 'checkbox') {
                $item['value'] = $this->getRadioOrCheckboxValueInfo($item['menu_name'], $item['value']);
            }
            if ($item['type'] == 'upload' && !empty($item['value'])) {
                if ($item['upload_type'] == 1 || $item['upload_type'] == 3) {
                    $item['value'] = [set_file_url($item['value'])];
                } elseif ($item['upload_type'] == 2) {
                    $item['value'] = set_file_url($item['value']);
                }
                foreach ($item['value'] as $key => $value) {
                    $tidy_srr[$key]['filepath'] = $value;
                    $tidy_srr[$key]['filename'] = basename($value);
                }
                $item['value'] = $tidy_srr;
            }
            if ($item['level'] == 1) {
                $item['link_data'] = $this->getLinkData($item['link_id'], $item['link_value']);
            }
            $item['config_tab_name'] = $configTabList[$item['config_tab_id']] ?? '';
        }
        return compact('count', 'list');
    }

    /**
     * Lấy giá trị liên kết
     * @param $id
     * @param $value
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/31
     */
    public function getLinkData($id, $value)
    {
        $info = $this->dao->get($id);
        if (!$info) return '';
        $parameter = explode("\n", $info['parameter']);
        $result = [];
        foreach ($parameter as $item) {
            $parts = explode('=>', $item);
            $result[$parts[0]] = $parts[1];
        }
        return $info['info'] . '/' . $result[$value];
    }

    /**
     * Lấy giá trị hiển thị của radio button hoặc checkbox
     * @param $menu_name
     * @param $value
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getRadioOrCheckboxValueInfo(string $menu_name, $value): string
    {
        $option = [];
        $config_one = $this->dao->getOne(['menu_name' => $menu_name]);
        if (!$config_one) {
            return '';
        }
        $parameter = explode("\n", $config_one['parameter']);
        foreach ($parameter as $k => $v) {
            if (isset($v) && strlen($v) > 0) {
                $data = explode('=>', $v);
                $option[$data[0]] = $data[1];
            }
        }
        $str = '';
        if (is_array($value)) {
            foreach ($value as $v) {
                $str .= $option[$v] . ',';
            }
        } else {
            $str .= !empty($value) ? $option[$value] ?? '' : $option[0] ?? '';
        }
        return $str;
    }

    /**
     * Lấy thông tin cấu hình hệ thống
     * @param int $tabId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getReadList(int $tabId)
    {
        $info = $this->dao->getConfigTabAllList($tabId);
        foreach ($info as $k => $v) {
            if (!is_null(json_decode($v['value'])))
                $info[$k]['value'] = json_decode($v['value'], true);
            if ($v['type'] == 'upload' && !empty($v['value'])) {
                if ($v['upload_type'] == 1 || $v['upload_type'] == 3) $info[$k]['value'] = explode(',', $v['value']);
            }
        }
        return $info;
    }

    /**
     * Tạo biểu mẫu một dòng
     * @param string $type
     * @param array $data
     * @return array
     */
    public function createTextForm(string $type, array $data)
    {
        $formbuider = [];
        switch ($type) {
            case 'number':
                $data['value'] = isset($data['value']) ? json_decode($data['value'], true) : 0;
                $formbuider[] = $this->builder->number($data['menu_name'], $data['info'], (float)$data['value'])->controls(false)->appendRule('suffix', [
                    'type' => 'div',
                    'class' => 'tips-info',
                    'domProps' => ['innerHTML' => $data['desc']]
                ]);
                break;
            case 'dateTime':
                $formbuider[] = $this->builder->dateTime($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
                    'type' => 'div',
                    'class' => 'tips-info',
                    'domProps' => ['innerHTML' => $data['desc']]
                ]);
                break;
            case 'date':
                $data['value'] = json_decode($data['value'], true) ?: '';
                $formbuider[] = $this->builder->date($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
                    'type' => 'div',
                    'class' => 'tips-info',
                    'domProps' => ['innerHTML' => $data['desc']]
                ]);
                break;
            case 'time':
                $data['value'] = json_decode($data['value'], true) ?: '';
                $formbuider[] = $this->builder->time($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
                    'type' => 'div',
                    'class' => 'tips-info',
                    'domProps' => ['innerHTML' => $data['desc']]
                ]);
                break;
            case 'color':
                $data['value'] = isset($data['value']) ? json_decode($data['value'], true) : '';
                $formbuider[] = $this->builder->color($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
                    'type' => 'div',
                    'class' => 'tips-info',
                    'domProps' => ['innerHTML' => $data['desc']]
                ]);
                break;
            default:
                $data['value'] = isset($data['value']) ? json_decode($data['value'], true) : '';
                if ($data['menu_name'] == 'api' || $data['menu_name'] == 'routine_api') {
                    $formbuider[] = $this->builder->input($data['menu_name'], $data['info'], strpos($data['value'], 'http') === false ? sys_config('site_url') . $data['value'] : $data['value'])->appendRule('suffix', [
                        'type' => 'div',
                        'class' => 'tips-info',
                        'domProps' => ['innerHTML' => $data['desc']]
                    ])->col(13)->readonly(true);
                } else {
                    $formbuider[] = $this->builder->input($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
                        'type' => 'div',
                        'class' => 'tips-info',
                        'domProps' => ['innerHTML' => $data['desc']]
                    ])->col(13);
                }
                break;
        }
        return $formbuider;
    }

    /**
     * Tạo textarea nhiều dòng
     * @param array $data
     * @return mixed
     */
    public function createTextareaForm(array $data)
    {
        $data['value'] = json_decode($data['value'], true) ?: '';
        if ($data['menu_name'] == 'param_filter_data') $data['value'] = base64_decode($data['value']);
        $formbuider[] = $this->builder->textarea($data['menu_name'], $data['info'], $data['value'])->placeholder($data['desc'])->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => $data['desc']]
        ])->rows(6)->col(13);
        return $formbuider;
    }

    /**
     * Tạo biểu mẫu lựa chọn
     * @param array $data
     * @param array $control
     * @param array $control_two
     * @return array
     */
    public function createRadioForm(array $data, $control = [], $control_two = [], $control_three = [])
    {
        $formbuider = [];
        $data['value'] = json_decode($data['value'], true) ?: '0';
        $parameter = explode("\n", $data['parameter']);
        $options = [];
        if ($parameter) {
            foreach ($parameter as $v) {
                if (strstr($v, $this->cuttingStr) !== false) {
                    $pdata = explode($this->cuttingStr, $v);
                    $res = preg_match('/^[0-9]$/', $pdata[0]);
                    $options[] = ['label' => $pdata[1], 'value' => $res ? (int)$pdata[0] : $pdata[0]];
                }
            }
            $res = preg_match('/^[0-9]$/', $data['value']);
            $formbuider[] = $radio = $this->builder->radio($data['menu_name'], $data['info'], $res ? (int)$data['value'] : $data['value'])->options($options)->appendRule('suffix', [
                'type' => 'div',
                'class' => 'tips-info',
                'domProps' => ['innerHTML' => $data['desc']]
            ])->col(13);
            if ($control) {
                $radio->appendControl($data['show_value'] ?? 1, is_array($control) ? $control : [$control]);
            }
            if ($control_two && isset($data['show_value2'])) {
                $radio->appendControl($data['show_value2'] ?? 2, is_array($control_two) ? $control_two : [$control_two]);
            }
            if ($control_three && isset($data['show_value3'])) {
                $radio->appendControl($data['show_value3'] ?? 3, is_array($control_three) ? $control_three : [$control_three]);
            }
            return $formbuider;
        }
    }

    /**
     * Tạo biểu mẫu thành phần tải lên (upload)
     * @param int $type
     * @param array $data
     * @return array
     */
    public function createUploadForm(int $type, array $data)
    {
        $formbuider = [];
        switch ($type) {
            case 1:
                $data['value'] = json_decode($data['value'], true) ?: '';
                if ($data['value'] != '') $data['value'] = set_file_url($data['value']);
                $formbuider[] = $this->builder->frameImage($data['menu_name'], $data['info'], $this->url(config('app.admin_prefix', 'admin') . '/widget.images/index', ['fodder' => $data['menu_name']], true), $data['value'])
                    ->icon('el-icon-picture-outline')->width('950px')->height('560px')->Props(['footer' => false, 'modalTitle' => 'Xem trước'])->appendRule('suffix', [
                        'type' => 'div',
                        'class' => 'tips-info',
                        'domProps' => ['innerHTML' => $data['desc']]
                    ])->col(13);
                break;
            case 2:
                $data['value'] = json_decode($data['value'], true) ?: [];
                if ($data['value'])
                    $data['value'] = set_file_url($data['value']);
                $formbuider[] = $this->builder->frameImages($data['menu_name'], $data['info'], $this->url(config('app.admin_prefix', 'admin') . '/widget.images/index', ['fodder' => $data['menu_name'], 'type' => 'many', 'maxLength' => 5], true), $data['value'])
                    ->maxLength(5)->icon('el-icon-picture-outline')->width('950px')->height('560px')->Props(['footer' => false, 'modalTitle' => 'Xem trước'])
                    ->appendRule('suffix', [
                        'type' => 'div',
                        'class' => 'tips-info',
                        'domProps' => ['innerHTML' => $data['desc']]
                    ])->col(13);
                break;
            case 3:
                $data['value'] = json_decode($data['value'], true) ?: '';
                if ($data['value'] != '') $data['value'] = set_file_url($data['value']);
                $formbuider[] = $this->builder->uploadFile($data['menu_name'], $data['info'], $this->url('/adminapi/file/upload/1', ['type' => 1], false, true), $data['value'])
                    ->name('file')->appendRule('suffix', [
                        'type' => 'div',
                        'class' => 'tips-info',
                        'domProps' => ['innerHTML' => $data['desc']]
                    ])->col(13)->data(['menu_name' => $data['menu_name']])->headers([
                        'Authori-zation' => app()->request->header('Authori-zation'),
                    ]);
                break;
        }
        return $formbuider;
    }

    /**
     * Tạo radio button
     * @param array $data
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createCheckboxForm(array $data)
    {
        $formbuider = [];
        $data['value'] = json_decode($data['value'], true) ?: [];
        $parameter = explode("\n", $data['parameter']);
        $options = [];
        if ($parameter) {
            foreach ($parameter as $v) {
                if (strstr($v, $this->cuttingStr) !== false) {
                    $pdata = explode($this->cuttingStr, $v);
                    $options[] = ['label' => $pdata[1], 'value' => $pdata[0]];
                }
            }
            $formbuider[] = $this->builder->checkbox($data['menu_name'], $data['info'], $data['value'])->options($options)->appendRule('suffix', [
                'type' => 'div',
                'class' => 'tips-info',
                'domProps' => ['innerHTML' => $data['desc']]
            ])->col(13);
        }
        return $formbuider;
    }

    /**
     * Tạo biểu mẫu select box
     * @param array $data
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function createSelectForm(array $data)
    {
        $formbuider = [];
        $data['value'] = json_decode($data['value'], true) ?: [];
        $parameter = explode("\n", $data['parameter']);
        $options = [];
        if ($parameter) {
            foreach ($parameter as $v) {
                if (strstr($v, $this->cuttingStr) !== false) {
                    $pdata = explode($this->cuttingStr, $v);
                    $options[] = ['label' => $pdata[1], 'value' => $pdata[0]];
                }
            }
            $formbuider[] = $this->builder->select($data['menu_name'], $data['info'], $data['value'])->options($options)->appendRule('suffix', [
                'type' => 'div',
                'class' => 'tips-info',
                'domProps' => ['innerHTML' => $data['desc']]
            ])->col(13);
        }
        return $formbuider;
    }

    /**
     * Chọn công tắc (switch)
     * @param $data
     * @return array
     * @author: Wu Xi
     * @email: 442384644@qq.com
     * @date: 2023/9/6
     */
    public function createSwitchForm($data)
    {
        $data['value'] = json_decode($data['value'], true) ?: '';
        $formbuider[] = $this->builder->switches($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => $data['desc']]
        ])->col(13);
        return $formbuider;
    }

    /**
     * Tạo bộ chọn màu (color picker)
     * @param array $data
     * @return mixed
     */
    public function createColorForm(array $data)
    {
        $data['value'] = json_decode($data['value'], true) ?: '';
        $formbuider[] = $this->builder->color($data['menu_name'], $data['info'], $data['value'])->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => $data['desc']]
        ])->col(13);
        return $formbuider;
    }

    public function bindBuilderData($data, $relatedRule)
    {
        if (!$data) return false;
        $p_list = array();
        foreach ($relatedRule as $rk => $rv) {
            $p_list[$rk] = $data[$rk];
            if (isset($rv['son_type']) && is_array($rv['son_type'])) {
                foreach ($rv['son_type'] as $sk => $sv) {
                    if (is_array($sv) && isset($sv['son_type'])) {
                        foreach ($sv['son_type'] as $ssk => $ssv) {
                            $tmp = $data[$sk];
                            $tmp['console'] = $data[$ssk];
                            $p_list[$rk]['console'][] = $tmp;
                        }
                    } else {
                        $p_list[$rk]['console'][] = $data[$sk];
                    }
                }
            }

        }
        return array_values($p_list);
    }

    /**
     * Lấy biểu mẫu cấu hình hệ thống
     * @param $data
     * @param bool $control
     * @param array $controle_two
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     */
    public function formTypeShine($data, $control = false, $controle_two = [], $controle_three = [])
    {

        switch ($data['type']) {
            case 'text'://Ô văn bản
                return $this->createTextForm($data['input_type'], $data);
            case 'radio'://Ô chọn một
                return $this->createRadioForm($data, $control, $controle_two, $controle_three);
            case 'textarea'://Ô văn bản nhiều dòng
                return $this->createTextareaForm($data);
            case 'upload'://Tải tệp lên
                return $this->createUploadForm((int)$data['upload_type'], $data);
            case 'checkbox'://Ô chọn nhiều
                return $this->createCheckboxForm($data);
            case 'select'://Ô chọn nhiều
                return $this->createSelectForm($data);
            case 'color':
                return $this->createColorForm($data);
            case 'switch'://Công tắc
                return $this->createSwitchForm($data);
        }
    }

    /**
     * @param int $tabId
     * @param array $formData
     * @param array $relatedRule
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createConfigForm(int $tabId, array $relatedRule)
    {
        $list = $this->dao->getConfigTabAllList($tabId);
        if (!$relatedRule) {
            $formbuider = $this->createNoCrontrolForm($list);
        } else {
            $formbuider = $this->createBindCrontrolForm($list, $relatedRule);
        }
        return $formbuider;
    }

    /**
     * Tạo
     * @param array $list
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createForm(array $list)
    {
        if (!$list) return [];
        $list = array_combine(array_column($list, 'menu_name'), $list);
        $formbuider = [];
        $sonConfig = $this->getSonConfig();
        $sonConfig = array_merge($sonConfig, $this->dao->getColumn(['level' => 1], 'menu_name'));
        foreach ($list as $key => $data) {
            if (in_array($key, $sonConfig)) {
                continue;
            }
            switch ($data['type']) {
                case 'text'://Ô văn bản
                    $formbuider = array_merge($formbuider, $this->createTextForm($data['input_type'], $data));
                    break;
                case 'radio'://Ô chọn một
                    $relateRule = $this->relatedRule;
                    $builder = [];
                    if (!isset($relateRule[$key])) {
                        $relateRule = [];
                        $sonData = $this->dao->getColumn(['level' => 1, 'link_id' => $data['id']], 'menu_name,link_value');
                        $sonValue = [];
                        foreach ($sonData as $sv) {
                            $sonValue[$sv['link_value']][] = $sv['menu_name'];
                        }
                        $i = 0;
                        foreach ($sonValue as $pk => $pv) {
                            $label = $data['menu_name'];
                            if ($i == 1) $label = $data['menu_name'] . '@';
                            if ($i == 2) $label = $data['menu_name'] . '#';
                            $relateRule[$label]['show_value'] = (int)$pk;
                            foreach ($pv as $pvv) {
                                $relateRule[$label]['son_type'][$pvv] = '';
                            }
                            $i++;
                        }
                    } else {
                        $sonData = $this->dao->getColumn(['level' => 1, 'link_id' => $data['id']], 'menu_name,link_value');
                        if ($sonData) {
                            $sonValue = [];
                            foreach ($sonData as $sv) {
                                $sonValue[$sv['link_value']][] = $sv['menu_name'];
                            }
                            $i = 0;
                            foreach ($sonValue as $pk => $pv) {
                                $label = $data['menu_name'];
                                if ($i == 1) $label = $data['menu_name'] . '@';
                                if ($i == 2) $label = $data['menu_name'] . '#';
                                if (!isset($relateRule[$label])) {
                                    $relateRule[$label]['show_value'] = (int)$pk;
                                }
                                foreach ($pv as $pvv) {
                                    $relateRule[$label]['son_type'][$pvv] = '';
                                }
                                $i++;
                            }
                        }
                    }
                    if (isset($relateRule[$key])) {
                        $role = $relateRule[$key];
                        $data['show_value'] = $role['show_value'];
                        foreach ($role['son_type'] as $sk => $sv) {
                            if (isset($list[$sk])) {
                                $son_data = $list[$sk];
                                $son_data['show_value'] = $role['show_value'];
                                $son_build = [];
                                if (isset($sv['son_type'])) {
                                    foreach ($sv['son_type'] as $ssk => $ssv) {
                                        $son_data['show_value'] = $sv['show_value'];
                                        $son_build[] = $this->formTypeShine($list[$ssk])[0];
                                        unset($list[$ssk]);
                                    }
                                }
                                $son_build_two = [];
                                if (isset($role['son_type'][$sk . '@'])) {
                                    $son_type_two = $role['son_type'][$sk . '@'];
                                    $son_data['show_value2'] = $son_type_two['show_value'];
                                    if (isset($son_type_two['son_type'])) {
                                        foreach ($son_type_two['son_type'] as $ssk => $ssv) {
                                            if (isset($list[$ssk]['menu_name']) && $list[$ssk]['menu_name'] == 'watermark_text_color') $list[$ssk]['type'] = 'color';
                                            $son_build_two[] = $this->formTypeShine($list[$ssk])[0];
                                            unset($list[$ssk]);
                                        }
                                    }
                                }
                                $son_build_three = [];
                                if (isset($role['son_type'][$sk . '#'])) {
                                    $son_type_three = $role['son_type'][$sk . '#'];
                                    $son_data['show_value3'] = $son_type_three['show_value'];
                                    if (isset($son_type_three['son_type'])) {
                                        foreach ($son_type_three['son_type'] as $ssk => $ssv) {
                                            if (isset($list[$ssk]['menu_name']) && $list[$ssk]['menu_name'] == 'watermark_text_color') $list[$ssk]['type'] = 'color';
                                            $son_build_three[] = $this->formTypeShine($list[$ssk])[0];
                                            unset($list[$ssk]);
                                        }
                                    }
                                }
                                $builder[] = $this->formTypeShine($son_data, $son_build, $son_build_two, $son_build_three)[0];
                                unset($list[$sk]);
                            }
                        }
                        $data['show_value'] = $role['show_value'];
                    }
                    $builder_two = [];
                    if (isset($relateRule[$key . '@'])) {
                        $role = $relateRule[$key . '@'];
                        $data['show_value2'] = $role['show_value'];
                        foreach ($role['son_type'] as $sk => $sv) {
                            $son_data = $list[$sk];
                            $son_data['show_value'] = $role['show_value'];
                            $builder_two[] = $this->formTypeShine($son_data)[0];
                        }
                    }
                    $builder_three = [];
                    if (isset($relateRule[$key . '#'])) {
                        $role = $relateRule[$key . '#'];
                        $data['show_value3'] = $role['show_value'];
                        foreach ($role['son_type'] as $sk => $sv) {
                            $son_data = $list[$sk];
                            $son_data['show_value'] = $role['show_value'];
                            $builder_three[] = $this->formTypeShine($son_data)[0];
                        }
                    }
                    $formbuider = array_merge($formbuider, $this->createRadioForm($data, $builder, $builder_two, $builder_three));
                    break;
                case 'textarea'://Ô văn bản nhiều dòng
                    $formbuider = array_merge($formbuider, $this->createTextareaForm($data));
                    break;
                case 'upload'://Tải tệp lên
                    $formbuider = array_merge($formbuider, $this->createUploadForm((int)$data['upload_type'], $data));
                    break;
                case 'checkbox'://Ô chọn nhiều
                    $formbuider = array_merge($formbuider, $this->createCheckboxForm($data));
                    break;
                case 'select'://Ô chọn nhiều
                    $formbuider = array_merge($formbuider, $this->createSelectForm($data));
                    break;
                case 'switch'://Công tắc
                    $formbuider = array_merge($formbuider, $this->createSwitchForm($data));
                    break;
            }
        }
        return $formbuider;
    }

    /**Quy tắc ràng buộc không có thành phần
     * @param array $list
     * @return array|bool
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createNoCrontrolForm(array $list)
    {
        if (!$list) return false;
        $formbuider = [];
        foreach ($list as $key => $data) {

            switch ($data['type']) {
                case 'text'://Ô văn bản
                    $formbuider = array_merge($formbuider, $this->createTextForm($data['input_type'], $data));
                    break;
                case 'radio'://Ô chọn một
                    $formbuider = array_merge($formbuider, $this->createRadioForm($data));
                    break;
                case 'textarea'://Ô văn bản nhiều dòng
                    $formbuider = array_merge($formbuider, $this->createTextareaForm($data));
                    break;
                case 'upload'://Tải tệp lên
                    $formbuider = array_merge($formbuider, $this->createUploadForm((int)$data['upload_type'], $data));
                    break;
                case 'checkbox'://Ô chọn nhiều
                    $formbuider = array_merge($formbuider, $this->createCheckboxForm($data));
                    break;
                case 'select'://Ô chọn nhiều
                    $formbuider = array_merge($formbuider, $this->createSelectForm($data));
                    break;
                case 'switch'://Công tắc
                    $formbuider = array_merge($formbuider, $this->createSwitchForm($data));
                    break;
            }
        }
        return $formbuider;
    }

    /**
     * Quy tắc ràng buộc có thành phần
     * @param array $list
     * @param array $relatedRule
     * @return array|bool
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createBindCrontrolForm(array $list, array $relatedRule)
    {
        if (!$list || !$relatedRule) return false;
        $formbuider = [];
        $new_data = array();
        foreach ($list as $dk => $dv) {
            $new_data[$dv['menu_name']] = $dv;
        }
        foreach ($relatedRule as $rk => $rv) {
            if (isset($rv['son_type'])) {
                $data = $new_data[$rk];
                switch ($data['type']) {
                    case 'text'://Ô văn bản
                        $formbuider = array_merge($formbuider, $this->createTextForm($data['input_type'], $data));
                        break;
                    case 'radio'://Ô chọn một
                        $son_builder = array();
                        foreach ($rv['son_type'] as $sk => $sv) {
                            if (isset($sv['son_type'])) {
                                foreach ($sv['son_type'] as $ssk => $ssv) {
                                    $son_data = $new_data[$sk];
                                    $son_data['show_value'] = $sv['show_value'];
                                    $son_builder[] = $this->formTypeShine($son_data, $this->formTypeShine($new_data[$ssk])[0])[0];
                                }
                            } else {
                                $son_data = $new_data[$sk];
                                $son_data['show_value'] = $rv['show_value'];
                                $son_builder[] = $this->formTypeShine($son_data)[0];
                            }

                        }
                        $formbuider = array_merge($formbuider, $this->createRadioForm($data, $son_builder));
                        break;
                    case 'textarea'://Ô văn bản nhiều dòng
                        $formbuider = array_merge($formbuider, $this->createTextareaForm($data));
                        break;
                    case 'upload'://Tải tệp lên
                        $formbuider = array_merge($formbuider, $this->createUploadForm((int)$data['upload_type'], $data));
                        break;
                    case 'checkbox'://Ô chọn nhiều
                        $formbuider = array_merge($formbuider, $this->createCheckboxForm($data));
                        break;
                    case 'select'://Ô chọn nhiều
                        $formbuider = array_merge($formbuider, $this->createSelectForm($data));
                        break;
                    case 'switch'://Công tắc
                        $formbuider = array_merge($formbuider, $this->createSwitchForm($data));
                        break;
                }
            }
        }
        return $formbuider;
    }

    /**
     * Tạo biểu mẫu (form) cấu hình hệ thống
     * @param $url
     * @param int $tabId
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getConfigForm($url, int $tabId)
    {
        /** @var SystemConfigTabServices $service */
        $service = app()->make(SystemConfigTabServices::class);
        $title = $service->value(['id' => $tabId], 'title');
        $list = $this->dao->getConfigTabAllList($tabId);
        $formbuider = $this->createForm($list);
        $name = 'setting';
        if ($url) {
            $name = explode('/', $url)[2] ?? $name;
        }
        $postUrl = $this->postUrl[$name]['url'] ?? '/setting/config/save_basics';
        return create_form($title, $formbuider, $this->url($postUrl), 'POST');
    }

    /**
     * Thêm route mới kèm xác thực mục cấu hình
     * @param $url
     * @param $post
     * @return bool
     */
    public function checkParam($url, $post)
    {
        $name = '';
        if ($url) {
            $name = explode('/', $url)[2] ?? $name;
        }
        $auth = $this->postUrl[$name]['auth'] ?? false;
        if ($auth === false) {
            throw new AdminException('Yêu cầu không được phép');
        }
        if ($auth) {
            /** @var SystemConfigTabServices $systemConfigTabServices */
            $systemConfigTabServices = app()->make(SystemConfigTabServices::class);
            foreach ($post as $key => $value) {
                $tab_ids = $systemConfigTabServices->getColumn([['eng_title', 'IN', $auth]], 'id');
                if (!$tab_ids || !in_array($key, $this->dao->getColumn([['config_tab_id', 'IN', $tab_ids]], 'menu_name'))) {
                    throw new AdminException('Không được phép cài đặt danh mục này');
                }
            }
        }
        return true;
    }

    /**
     * Lấy biểu mẫu (form) sửa cấu hình
     * @param int $id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editConfigForm(int $id)
    {
        $menu = $this->dao->get($id)->getData();
        if (!$menu) {
            throw new AdminException('Dữ liệu không tồn tại');
        }
        /** @var SystemConfigTabServices $service */
        $service = app()->make(SystemConfigTabServices::class);
        $formbuider = [];
        $linkData = $this->linkData($menu['config_tab_id']);
        $formbuider[] = $this->builder->radio('level', 'Hiển thị theo liên kết', $menu['level'])->options([['value' => 0, 'label' => 'Không'], ['value' => 1, 'label' => 'Có']])->appendRule('suffix', [
            'type' => 'div',
            'class' => 'tips-info',
            'domProps' => ['innerHTML' => 'Không: mặc định hiển thị cấu hình này bình thường; Có: cấu hình này mặc định bị ẩn, chỉ hiển thị khi chọn giá trị tương ứng của cấu hình bên dưới']
        ])->appendControl(1, [
            $this->builder->cascader('link_data', 'Cấu hình/Giá trị liên kết', [$menu['link_id'], $menu['link_value']])->options($linkData)->props(['props' => ['multiple' => false, 'checkStrictly' => false, 'emitPath' => true]])->style(['width' => '100%']),
        ]);
        $formbuider[] = $this->builder->input('menu_name', 'Biến trường', $menu['menu_name'])->disabled(1);
        $formbuider[] = $this->builder->hidden('type', $menu['type']);
        [$configTabList, $data] = $service->getConfigTabListForm((int)($menu['config_tab_id'] ?? 0));
        $formbuider[] = $this->builder->cascader('config_tab_id', 'Danh mục', $data)->options($configTabList)->filterable(true)->props(['props' => ['multiple' => false, 'checkStrictly' => true, 'emitPath' => true]])->style(['width' => '100%']);
        $formbuider[] = $this->builder->input('info', 'Tên cấu hình', $menu['info'])->autofocus(1);
        $formbuider[] = $this->builder->input('desc', 'Mô tả ngắn cấu hình', $menu['desc']);
        switch ($menu['type']) {
            case 'text':
                $menu['value'] = json_decode($menu['value'], true);
                $formbuider[] = $this->builder->select('input_type', 'Loại', $menu['input_type'])->setOptions([
                    ['value' => 'input', 'label' => 'Ô văn bản']
                    , ['value' => 'dateTime', 'label' => 'Ngày giờ']
                    , ['value' => 'date', 'label' => 'Ngày']
                    , ['value' => 'time', 'label' => 'Thời gian']
                    , ['value' => 'color', 'label' => 'Màu sắc']
                    , ['value' => 'number', 'label' => 'Số']
                ]);
                //Quy tắc xác thực ô nhập liệu
                $formbuider[] = $this->builder->input('value', 'Giá trị mặc định', $menu['value']);
                if (!empty($menu['required'])) {
                    $formbuider[] = $this->builder->number('width', 'Chiều rộng ô văn bản', (int)$menu['width']);
                    $formbuider[] = $this->builder->input('required', 'Quy tắc xác thực', $menu['required'])->placeholder('Nhiều quy tắc thì phân tách bằng dấu “,”, ví dụ: required:true,url:true');
                }
                break;
            case 'textarea':
                $menu['value'] = json_decode($menu['value'], true);
                //Văn bản nhiều dòng
                if (!empty($menu['high'])) {
                    $formbuider[] = $this->builder->textarea('value', 'Giá trị mặc định', $menu['value'])->rows(5);
                    $formbuider[] = $this->builder->number('width', 'Chiều rộng ô văn bản', (int)$menu['width']);
                    $formbuider[] = $this->builder->number('high', 'Chiều cao ô văn bản nhiều dòng', (int)$menu['high']);
                } else {
                    $formbuider[] = $this->builder->input('value', 'Giá trị mặc định', $menu['value']);
                }
                break;
            case 'radio':
                $formbuider = array_merge($formbuider, $this->createRadioForm($menu));
                //Cấu hình tham số chọn một và chọn nhiều
                if (!empty($menu['parameter'])) {
                    $formbuider[] = $this->builder->textarea('parameter', 'Tham số cấu hình', $menu['parameter'])->placeholder("Định dạng tham số, ví dụ:\n1=>Trắng\n2=>Đỏ\n3=>Đen");
                }
                break;
            case 'checkbox':
                $formbuider = array_merge($formbuider, $this->createCheckboxForm($menu));
                //Cấu hình tham số chọn một và chọn nhiều
                if (!empty($menu['parameter'])) {
                    $formbuider[] = $this->builder->textarea('parameter', 'Tham số cấu hình', $menu['parameter'])->placeholder("Định dạng tham số, ví dụ:\n1=>Trắng\n2=>Đỏ\n3=>Đen");
                }
                break;
            case 'upload':
                $formbuider = array_merge($formbuider, $this->createUploadForm(($menu['upload_type']), $menu));
                //Chọn loại tải lên (upload)
                if (!empty($menu['upload_type'])) {
                    $formbuider[] = $this->builder->radio('upload_type', 'Loại tải lên', $menu['upload_type'])->options([['value' => 1, 'label' => 'Một ảnh'], ['value' => 2, 'label' => 'Nhiều ảnh'], ['value' => 3, 'label' => 'Tệp']]);
                }
                break;
            case 'switch':
                $formbuider = array_merge($formbuider, $this->createSwitchForm($menu));
                break;
        }
        $formbuider[] = $this->builder->number('sort', 'Thứ tự sắp xếp', (int)$menu['sort']);
        $formbuider[] = $this->builder->radio('status', 'Trạng thái', $menu['status'])->options([['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']]);
        return create_form('Sửa trường', $formbuider, $this->url('/setting/config/' . $id), 'PUT');
    }

    /**
     * Trạng thái trường (field)
     * @return array
     */
    public function formStatus(): array
    {
        return [['value' => 1, 'label' => 'Hiện'], ['value' => 0, 'label' => 'Ẩn']];
    }

    /**
     * Chọn loại tệp
     * @return array
     */
    public function uploadType(): array
    {
        return [
            ['value' => 1, 'label' => 'Một ảnh']
            , ['value' => 2, 'label' => 'Nhiều ảnh']
            , ['value' => 3, 'label' => 'Tệp']
        ];
    }

    /**
     * Chọn loại ô văn bản (textbox)
     * @return array
     */
    public function textType(): array
    {
        return [
            ['value' => 'input', 'label' => 'Ô văn bản']
            , ['value' => 'dateTime', 'label' => 'Ngày giờ']
            , ['value' => 'date', 'label' => 'Ngày']
            , ['value' => 'time', 'label' => 'Thời gian']
            , ['value' => 'color', 'label' => 'Màu sắc']
            , ['value' => 'number', 'label' => 'Số']
        ];
    }

    /**
     * Lấy biểu mẫu tạo quy cách cấu hình
     * @param int $type
     * @param int $tab_id
     * @return array
     * @throws \FormBuilder\Exception\FormBuilderException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createFormRule(int $type, int $tab_id): array
    {
        /** @var SystemConfigTabServices $service */
        $service = app()->make(SystemConfigTabServices::class);
        $formbuider = [];
        $form_type = '';
        $info_type = [];
        $parameter = [];
        switch ($type) {
            case 0://Ô văn bản
                $form_type = 'text';
                $info_type = $this->builder->select('input_type', 'Loại')->setOptions($this->textType());
                $parameter[] = $this->builder->input('value', 'Giá trị mặc định');
                $parameter[] = $this->builder->number('width', 'Chiều rộng ô văn bản', 100);
                $parameter[] = $this->builder->input('required', 'Quy tắc xác thực')->placeholder('Nhiều quy tắc thì phân tách bằng dấu “,”, ví dụ: required:true,url:true');
                break;
            case 1://Ô văn bản nhiều dòng
                $form_type = 'textarea';
                $parameter[] = $this->builder->textarea('value', 'Giá trị mặc định');
                $parameter[] = $this->builder->number('width', 'Chiều rộng ô văn bản', 100);
                $parameter[] = $this->builder->number('high', 'Chiều cao ô văn bản nhiều dòng', 5);
                break;
            case 2://Ô chọn một
                $form_type = 'radio';
                $parameter[] = $this->builder->textarea('parameter', 'Tham số cấu hình')->placeholder("Định dạng tham số, ví dụ:\n1=>Nam\n2=>Nữ\n3=>Không tiết lộ");
                $parameter[] = $this->builder->input('value', 'Giá trị mặc định');
                break;
            case 3://Tải tệp lên
                $form_type = 'upload';
                $parameter[] = $this->builder->radio('upload_type', 'Loại tải lên', 1)->options($this->uploadType());
                break;
            case 4://Ô chọn nhiều
                $form_type = 'checkbox';
                $parameter[] = $this->builder->textarea('parameter', 'Tham số cấu hình')->placeholder("Định dạng tham số, ví dụ:\n1=>Trắng\n2=>Đỏ\n3=>Đen");
                break;
            case 5://Danh sách thả xuống
                $form_type = 'select';
                $parameter[] = $this->builder->textarea('parameter', 'Tham số cấu hình')->placeholder("Định dạng tham số, ví dụ:\n1=>Trắng\n2=>Đỏ\n3=>Đen");
                break;
            case 6://Công tắc
                $form_type = 'switch';
                $parameter[] = $this->builder->switches('value', 'Mặc định');
                break;
        }
        if ($form_type) {
            $formbuider[] = $this->builder->hidden('type', $form_type);
            [$configTabList, $data] = $service->getConfigTabListForm((int)($tab_id ?? 0));
            $linkData = $this->linkData($tab_id);
            $formbuider[] = $this->builder->radio('level', 'Hiển thị theo liên kết', 0)->options([['value' => 0, 'label' => 'Không'], ['value' => 1, 'label' => 'Có']])->appendRule('suffix', [
                'type' => 'div',
                'class' => 'tips-info',
                'domProps' => ['innerHTML' => 'Không: mặc định hiển thị cấu hình này bình thường; Có: cấu hình này mặc định bị ẩn, chỉ hiển thị khi chọn giá trị tương ứng của cấu hình bên dưới']
            ])->appendControl(1, [
                $this->builder->cascader('link_data', 'Cấu hình/Giá trị liên kết')->options($linkData)->props(['props' => ['multiple' => false, 'checkStrictly' => false, 'emitPath' => true]])->style(['width' => '100%']),
            ]);
            $formbuider[] = $this->builder->cascader('config_tab_id', 'Danh mục', $data)->options($configTabList)->filterable(true)->props(['props' => ['multiple' => false, 'checkStrictly' => true, 'emitPath' => false]])->style(['width' => '100%']);
            if ($info_type) {
                $formbuider[] = $info_type;
            }
            $formbuider[] = $this->builder->input('info', 'Tên cấu hình')->autofocus(1);
            $formbuider[] = $this->builder->input('menu_name', 'Biến trường')->placeholder('Ví dụ: site_url');
            $formbuider[] = $this->builder->input('desc', 'Mô tả biểu mẫu');
            $formbuider = array_merge($formbuider, $parameter);
            $formbuider[] = $this->builder->number('sort', 'Thứ tự sắp xếp', 0);

            $formbuider[] = $this->builder->radio('status', 'Trạng thái', 1)->options($this->formStatus());
        }
        return create_form('Thêm trường', $formbuider, $this->url('/setting/config'), 'POST');
    }

    /**
     * Theo ID nhãn chỉ định, liên kết dữ liệu và trả về theo định dạng cụ thể.
     * @param $tab_id
     * @return array
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2024/5/30
     */
    public function linkData($tab_id)
    {
        $linkData = $this->selectList(['config_tab_id' => $tab_id, 'type' => 'radio', 'level' => 0], 'info as label,id as value,parameter')->toArray();
        foreach ($linkData as &$item) {
            $parameter = [];
            $parameter = explode("\n", $item['parameter']);
            foreach ($parameter as $pv) {
                $pvArr = explode('=>', $pv);
                $item['children'][] = [
                    'label' => $pvArr[1],
                    'value' => (int)$pvArr[0]
                ];
            }
        }
        return $linkData;
    }

    /**
     * Kiểm tra quy tắc của radio và checkbox
     * @param $data
     * @return bool
     */
    public function valiDateRadioAndCheckbox($data)
    {
        $option = [];
        $option_new = [];
        $data['parameter'] = str_replace("\r\n", "\n", $data['parameter']);//Ngăn không tương thích
        $parameter = explode("\n", $data['parameter']);
        if (count($parameter) < 2) {
            throw new AdminException('Vui lòng nhập tham số cấu hình đúng định dạng');
        }
        foreach ($parameter as $k => $v) {
            if (isset($v) && !empty($v)) {
                $option[$k] = explode('=>', $v);
            }
        }
        if (count($option) < 2) {
            throw new AdminException('Vui lòng nhập tham số cấu hình đúng định dạng');
        }
        $bool = 1;
        foreach ($option as $k => $v) {
            $option_new[$k] = $option[$k][0];
            foreach ($v as $kk => $vv) {
                $vv_num = strlen($vv);
                if (!$vv_num) {
                    $bool = 0;
                }
            }
        }
        if (!$bool) {
            throw new AdminException('Vui lòng nhập tham số cấu hình đúng định dạng');
        }
        $num1 = count($option_new);//Lấy số lượng của mảng này
        $arr2 = array_unique($option_new);//Gộp các phần tử giống nhau
        $num2 = count($arr2);//Lấy số lượng mảng sau khi gộp
        if ($num1 > $num2) {
            throw new AdminException('Vui lòng nhập tham số cấu hình đúng định dạng');
        }
        return true;
    }

    /**
     * Xác thực tham số
     * @param $data
     * @return bool
     */
    public function valiDateValue($data)
    {
        if (!$data || !isset($data['required']) || !$data['required']) {
            return true;
        }
        $valids = explode(',', $data['required']);
        foreach ($valids as $valid) {
            $valid = explode(':', $valid);
            if (isset($valid[0]) && isset($valid[1])) {
                $k = strtolower(trim($valid[0]));
                $v = strtolower(trim($valid[1]));
                switch ($k) {
                    case 'required':
                        if ($v == 'true' && $data['value'] === '') {
                            throw new AdminException('Vui lòng nhập giá trị mặc định cho {:name}', ['name' => $data['info'] ?? '']);
                        }
                        break;
                    case 'url':
                        if ($v == 'true' && !check_link($data['value'])) {
                            throw new AdminException('Vui lòng nhập đúng url cho {:name}', ['name' => $data['info'] ?? '']);
                        }
                        break;
                }
            }
        }
    }

    /**
     * Lưu thông tin in vận đơn điện tử của nền tảng
     * @param array $data
     * @return bool
     */
    public function saveExpressInfo(array $data)
    {
        if (!is_array($data) || !$data) return false;
        // config_export_id ID đơn vị vận chuyển
        // config_export_temp_id ID mẫu đơn vị vận chuyển
        // config_export_com Mã đơn vị vận chuyển
        // config_export_to_name Họ tên người gửi hàng
        // config_export_to_tel Số điện thoại người gửi hàng
        // config_export_to_address Địa chỉ chi tiết người gửi hàng
        // config_export_siid Mã máy in vận đơn điện tử
        foreach ($data as $key => $value) {
            $this->dao->update(['menu_name' => 'config_export_' . $key], ['value' => json_encode($value)]);
        }
        CacheService::clear();
        return true;
    }

    /**
     * Lấy poster chia sẻ (phương thức tương thích)
     */
    public function getSpreadBanner()
    {
        //Cấu hình
        $banner = sys_config('spread_banner', []);
        if (!$banner) {
            //Dữ liệu tổ hợp
            $banner = sys_data('routine_spread_banner');
            if ($banner) {
                $banner = array_column($banner, 'pic');
                $this->dao->update(['menu_name' => 'spread_banner'], ['value' => json_encode($banner)]);
                CacheService::clear();
            }
        }
        return $banner;
    }

    /**
     * Lưu cấu hình wss
     * @param int $wssOpen
     * @param string $wssLocalpk
     * @param string $wssLocalCert
     */
    public function saveSslFilePath(int $wssOpen, string $wssLocalpk, string $wssLocalCert)
    {
        $wssFile = root_path() . '.wss';
        $content = <<<WSS
wssOpen = $wssOpen
wssLocalpk = $wssLocalpk
wssLocalCert = $wssLocalCert
WSS;
        try {
            file_put_contents($wssFile, $content);
        } catch (\Throwable $e) {
            throw new AdminException('Lưu chứng chỉ wss thất bại');
        }
    }

    /**
     * Lấy cấu hình wss
     * @param string $key
     * @return array|false|mixed
     */
    public function getSslFilePath(string $key = '')
    {
        $wssFile = root_path() . '.wss';
        try {
            $content = parse_ini_file($wssFile);
        } catch (\Throwable $e) {
            $content = [];
        }
        return $content[$key] ?? $content;
    }

    /**
     * Kiểm tra cấu hình watermark ảnh thu nhỏ có thay đổi không
     * @param array $post
     * @return bool
     */
    public function checkThumbParam(array $post)
    {
        unset($post['upload_type'], $post['image_watermark_status']);
        /** @var SystemConfigTabServices $systemConfigTabServices */
        $systemConfigTabServices = app()->make(SystemConfigTabServices::class);
        //Cấu hình tải lên -> Cấu hình cơ bản
        $tab_id = $systemConfigTabServices->getColumn(['eng_title' => 'base_config'], 'id');
        if ($tab_id) {
            $all = $this->dao->getColumn(['config_tab_id' => $tab_id], 'value', 'menu_name');
            if (array_intersect(array_keys($all), array_keys($post))) {
                foreach ($post as $key => $item) {
                    //Khi cấu hình thay đổi, xóa ảnh thu nhỏ đã tạo trước đó
                    if (isset($all[$key]) && $item != json_decode($all[$key], true)) {
                        try {
                            FileService::delDir(public_path('uploads/thumb_water'));
                            break;
                        } catch (\Throwable $e) {

                        }
                    }
                }
            }
        }
        return true;
    }

    /**
     * Thay đổi chế độ ràng buộc quan hệ tiếp thị liên kết
     * @param array $post
     * @return bool
     */
    public function checkBrokerageBinding(array $post)
    {
        try {
            $config_data = $post['store_brokerage_binding_status'];
            $config_one = $this->dao->getOne(['menu_name' => 'store_brokerage_binding_status']);
            $config_old = json_decode($config_one['value'], true);
            if ($config_old != 2 && $config_data == 2) {
                //Tự động hủy liên kết với người giới thiệu

                /** @var AgentManageServices $agentManage */
                $agentManage = app()->make(AgentManageServices::class);
                $agentManage->resetSpreadTime();
            }
        } catch (\Throwable $e) {
            Log::error('Đặt lại thời gian ràng buộc khi thay đổi chế độ ràng buộc CTV thất bại, lý do:' . $e->getMessage());
            return false;
        }
        return true;
    }
}
