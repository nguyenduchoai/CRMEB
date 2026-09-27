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

use crmeb\services\crud\enum\ActionEnum;
use crmeb\services\crud\enum\FormTypeEnum;
use think\helper\Str;

/**
 * Tạo Controller
 * Class Controller
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/3/13
 * @package crmeb\servives\crud
 */
class Controller extends Make
{

    /**
     * @var string
     */
    protected $name = 'controller';

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/4
     */
    protected function setBaseDir(): string
    {
        return 'app' . DS . 'adminapi' . DS . 'controller' . DS . 'crud';
    }

    /**
     * @return Controller
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/3/13
     */
    public function handle(string $name, array $options = [])
    {
        $this->options = $options;

        $path = $options['path'] ?? '';
        $field = $options['field'] ?? [];
        $hasOneFields = $options['hasOneField'] ?? [];

        $this->value['MODEL_NAME'] = $options['modelName'] ?? $name;
        $this->value['NAME_CAMEL'] = Str::studly($name);
        $this->value['PATH'] = $this->getfolderPath($path);

        return $this->setUseContent()
            ->setControllerContent($field,
                $options['searchField'] ?? [],
                $name,
                $options['columnField'] ?? [],
                $hasOneFields)
            ->setController($name, $path);
    }

    /**
     * Đặt nội dung controller
     * @param string $name
     * @param string $path
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function setController(string $name, string $path)
    {
        [$className, $contentController] = $this->getStubContent($name, 'controller');

        $this->value['NAME'] = $className;

        $contentStr = str_replace($this->var, $this->value, $contentController);
        $filePath = $this->getFilePathName($path, $this->value['NAME_CAMEL']);
        $this->usePath = $this->value['PATH'];

        $this->setPathname($filePath);
        $this->setContent($contentStr);

        return $this;
    }

    /**
     * Đặt nội dung use
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function setUseContent()
    {
        $this->value['USE_PHP'] = "use " . str_replace('/', '\\', $this->options['usePath']) . "Services;\n";

        return $this;
    }

    /**
     * Đặt nội dung controller
     * @param array $field
     * @param array $searchField
     * @param string $name
     * @param array $columnField
     * @param array $hasOneFields
     * @return $this
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function setControllerContent(array $field, array $searchField, string $name, array $columnField, array $hasOneFields)
    {
        $var = [
            "{%DATE%}",
            '{%VALIDATE_NAME%}',
            '{%FIELD_PHP%}',
            '{%FIELD%}',
            '{%WITH%}',
            '{%OTHER_PHP%}',
            '{%FIELD_ALL_PHP%}',
            '{%FIELD_SEARCH_PHP%}'
        ];

        $replace = [
            $this->value['DATE'],
            $this->options['validateName'] ?? '',
            $this->getSearchFieldContent($field),
            $this->getSearchListFieldContent($columnField),
            $this->getSearchListWithContent($hasOneFields),
            $this->getSearchListOtherContent($columnField),
            $this->getStatusUpdateContent($columnField),
            $this->getSearchPhpContent($searchField)
        ];

        $this->value['CONTENT_PHP'] = str_replace($var, $replace, $this->getStubControllerContent($name));

        return $this;
    }

    /**
     * Lấy nội dung trường tìm kiếm
     * @param array $field
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function getSearchFieldContent(array $field)
    {
        $fieldStr = '';
        foreach ($field as $k) {
            $fieldStr .= $this->tab(3) . "['$k', ''],\n";
        }

        return $fieldStr;
    }

    /**
     * Trích xuất nội dung mẫu controller
     * @param string $name
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function getStubControllerContent(string $name)
    {
        $contentPhp = '';
        foreach (ActionEnum::ACTION_ALL as $item) {
            [, $stub] = $this->getStubContent($name, $item);
            $contentPhp .= $stub . "\r\n";
        }

        return $contentPhp;
    }

    /**
     * Đặt hiển thị trường tìm kiếm
     * @param array $columnField
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function getSearchListFieldContent(array $columnField)
    {
        $select = [];
        foreach ($columnField as $item) {
            //Xử lý trường truy vấn
            if (in_array($item['type'], [
                FormTypeEnum::DATE_TIME_RANGE,
                FormTypeEnum::FRAME_IMAGES,
                FormTypeEnum::RADIO,
                FormTypeEnum::SELECT,
                FormTypeEnum::CHECKBOX])) {
                $select[] = '`' . $item['field'] . '` as ' . $item['field'] . $this->attrPrefix;
            }
        }
        unset($item);

        return $select ? '\'*\',\'' . implode('\',\'', $select) . '\'' : '\'*\'';
    }

    /**
     * Đặt nội dung liên kết tìm kiếm
     * @param array $hasOneFields
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function getSearchListWithContent(array $hasOneFields)
    {
        $with = [];
        foreach ($hasOneFields as $item) {
            if (isset($item['hasOne'])) {
                [$modelName,] = is_array($item['hasOne']) ? $item['hasOne'] : [$item['hasOne'], 'id'];
                $modelName = Model::getHasOneNamePases($modelName);
                if (!$modelName) {
                    continue;
                }
                $with[] = "'" . Str::camel($item['field']) . 'HasOne' . "'";
            }
        }
        unset($item);

        return $with ? implode(',', $with) : '[]';
    }

    /**
     * Lấy nội dung trường có thể sửa
     * @param array $columnField
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/9/6
     */
    public function getStatusUpdateContent(array $columnField)
    {
        $fieldAll = [];
        foreach ($columnField as $item) {
            if ($item['type'] == FormTypeEnum::SWITCH) {
                $fieldAll[] = $item['field'];
            }
        }
        return $fieldAll ? "['" . implode("','", $fieldAll) . "']" : '[]';
    }

    /**
     * Đặt nội dung tìm kiếm khác
     * @param array $columnField
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/12
     */
    protected function getSearchListOtherContent(array $columnField)
    {
        $otherContent = '';

        foreach ($columnField as $item) {
            //Xử lý trường truy vấn
            if (in_array($item['type'], [FormTypeEnum::FRAME_IMAGES, FormTypeEnum::CHECKBOX, FormTypeEnum::DATE_TIME_RANGE])) {
                if (!$otherContent) {
                    $otherContent .= "\n";
                }
                $otherContent .= $this->tab(2) . '$data[\'' . $item['field'] . '\'] = json_encode($data[\'' . $item['field'] . "']);\n";
            }
        }

        return $otherContent;
    }

    /**
     * Lấy nội dung tìm kiếm trong controller
     * @param array $fields
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/3
     */
    protected function getSearchPhpContent(array $fields)
    {
        $fieldStr = '';
        foreach ($fields as $i => $item) {
            if (!empty($item['search'])) {
                $fieldStr .= $this->tab(3) . "['$item[field]', '']," . ((count($fields) - 1) == $i ? "" : "\n");
            }
        }

        return $fieldStr;
    }

    /**
     * Trả về đường dẫn mẫu
     * @param string $type
     * @return string|string[]
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/3/13
     */
    protected function getStub(string $type = 'controller')
    {
        $controllerPath = __DIR__ . DS . 'stubs' . DS . 'controller' . DS;

        $stubs = [
            'index' => $controllerPath . 'index.stub',
            'create' => $controllerPath . 'create.stub',
            'save' => $controllerPath . 'save.stub',
            'status' => $controllerPath . 'status.stub',
            'edit' => $controllerPath . 'edit.stub',
            'update' => $controllerPath . 'update.stub',
            'delete' => $controllerPath . 'delete.stub',
            'read' => $controllerPath . 'read.stub',
            'controller' => $controllerPath . 'crudController.stub',
        ];

        return $type ? $stubs[$type] : $stubs;
    }

    /**
     * @param string $path
     * @param string $name
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/3/13
     */
    protected function getFilePathName(string $path, string $name): string
    {
        $path = str_replace(['app\\', 'app/'], '', $path);

        $path = ltrim(str_replace('\\', '/', $path), '/');

        return $this->getBasePath($path) . $name . '.' . $this->fileMime;
    }
}
