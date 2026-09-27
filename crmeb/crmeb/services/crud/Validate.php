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

namespace crmeb\services\crud;

/**
 * Class Validate
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/3/29
 * @package crmeb\services\crud
 */
class Validate extends Make
{
    /**
     * @var string
     */
    protected $name = 'validate';

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/4
     */
    protected function setBaseDir(): string
    {
        return 'app' . DS . 'adminapi' . DS . 'validate' . DS . 'crud';
    }

    /**
     * @param string $name
     * @param array $options
     * @return Validate
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/23
     */
    public function handle(string $name, array $options = [])
    {
        $this->value['MODEl_NAME'] = $options['modelName'] ?? $name;

        $this->setRuleContent($options['field']);

        return parent::handle($name, $options);
    }

    /**
     * Đặt nội dung quy tắc
     * @param array $field
     * @return Validate
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/23
     */
    protected function setRuleContent(array $field)
    {
        $content = [];
        $message = [];
        foreach ($field as $item) {
            $item['name'] = addslashes($item['name']);
            if ($item['required']) {
                $content[] = $this->tab(2) . '\'' . $item['field'] . '\'=> \'require\',';
                $message[] = $this->tab(2) . '\'' . $item['field'] . '.require\'=> \'' . $item['name'] . 'là bắt buộc\',';
            }
        }

        $this->value['RULE_PHP'] = implode("\n", $content);
        $this->value['MESSAGE_PHP'] = implode("\n", $message);
        return $this;
    }

    /**
     * Cấu hình file mẫu
     * @param string $type
     * @return mixed
     */
    protected function getStub(string $type = '')
    {
        return __DIR__ . DS . 'stubs' . DS . 'validate' . DS . 'crudValidate.stub';
    }
}
