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


use crmeb\services\crud\enum\SearchEnum;

/**
 * Class Business
 * @package crmeb\services
 */
class Dao extends Make
{
    /**
     * Tên lệnh hiện tại
     * @var string
     */
    protected $name = "dao";

    /**
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/4/4
     */
    protected function setBaseDir(): string
    {
        return 'app' . DS . 'dao' . DS . 'crud';
    }

    /**
     * Thực hiện thay thế
     * @param string $name
     * @param array $options
     * @return Dao
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/3
     */
    public function handle(string $name, array $options = [])
    {
        $this->setSearchDaoPhpContent($options['searchField'] ?? []);
        return parent::handle($name, $options);
    }

    /**
     * Lấy code php của dao tìm kiếm
     * @param array $fields
     * @return Dao
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/8/3
     */
    protected function setSearchDaoPhpContent(array $fields)
    {
        $templateContent = file_get_contents($this->getStub('search'));
        $contentSearchPhp = '';
        foreach ($fields as $item) {
            $tab2 = $this->tab(2);
            $contentStr = <<<CONTENT
->when(!empty(\$where['$item[field]']), function(\$query) use (\$where) {
$tab2    \$query->{%WHERE%}('$item[field]', '{%SEARCH%}', \$where['$item[field]']);
$tab2})
CONTENT;
            if (isset($item['search']) && $item['search']) {

                switch ($item['search']) {
                    case SearchEnum::SEARCH_TYPE_EQ:
                    case SearchEnum::SEARCH_TYPE_GTEQ:
                    case SearchEnum::SEARCH_TYPE_LTEQ:
                    case SearchEnum::SEARCH_TYPE_NEQ:
                        $contentSearchPhp .= str_replace([
                            '{%WHERE%}',
                            '{%SEARCH%}'
                        ], [
                            'where',
                            $item['search']
                        ], $contentStr);
                        break;
                    case SearchEnum::SEARCH_TYPE_LIKE:
                        $contentSearchPhp .= <<<CONTENT
->when(!empty(\$where['$item[field]']), function(\$query) use (\$where) {
$tab2    \$query->whereLike('$item[field]', '%'.\$where['$item[field]'].'%');
$tab2})
CONTENT;
                        break;
                    case SearchEnum::SEARCH_TYPE_BETWEEN:
                        $contentSearchPhp .= <<<CONTENT
->when(!empty(\$where['$item[field]']), function(\$query) use (\$where) {
$tab2    \$query->whereBetween('$item[field]', \$where['$item[field]']);
$tab2})
CONTENT;
                        break;
                }
            }
        }

        $this->value['CONTENT_PHP'] = str_replace(['{%CONTENT_SEARCH_PHP%}'], [$contentSearchPhp . ';'], $templateContent);

        return $this;
    }

    /**
     * Tệp mẫu
     * @param string $type
     * @return string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/3/13
     */
    protected function getStub(string $type = '')
    {
        $daoPath = __DIR__ . DS . 'stubs' . DS . 'dao' . DS;

        $stubs = [
            'dao' => $daoPath . 'crudDao.stub',
            'search' => $daoPath . 'search.stub',
        ];

        return $type ? $stubs[$type] : $stubs['dao'];
    }
}
