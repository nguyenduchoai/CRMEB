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
namespace app\services\diy;

use app\dao\diy\ThemeDao;
use app\services\BaseServices;
use crmeb\exceptions\AdminException;
use crmeb\exceptions\ApiException;

/**
 * Lớp dịch vụ chủ đề
 *
 * Tổng quan chức năng:
 * Phụ trách quản lý theme của hệ thống, bao gồm các chức năng thêm/xóa/sửa/tra cứu theme, nhập/xuất, áp dụng và chuyển đổi theme, v.v.
 * Cung cấp khả năng quản lý độc lập và sử dụng kết hợp dữ liệu của các trang như trang chủ, trang danh mục, trang chi tiết, trang cá nhân.
 *
 * Chức năng chính:
 * 1. Quản lý theme - Tra cứu danh sách theme, lấy chi tiết, tạo và chỉnh sửa
 * 2. Áp dụng theme - Chuyển đổi theme đang sử dụng, hoặc áp dụng riêng dữ liệu của một trang cụ thể thuộc một theme nào đó
 * 3. Nhập dữ liệu - Hỗ trợ nhập dữ liệu cấu hình theme từ bên ngoài
 * 4. Quản lý tài nguyên - Quản lý hình ảnh, tiêu đề và các tài nguyên khác liên quan đến theme
 * 5. Quản lý phiên bản - Ghi lại thời gian cập nhật và thông tin phiên bản của dữ liệu theme
 *
 * @package app\services\diy
 * @author wuhaotian
 * @email 442384644@qq.com
 * @date 2025/12/18
 */
class ThemeServices extends BaseServices
{
    /**
     * Hàm khởi tạo - Khởi tạo các phụ thuộc
     *
     * Inject phụ thuộc ThemeDao, dùng cho các thao tác cơ sở dữ liệu.
     *
     * @param ThemeDao $dao Đối tượng truy cập dữ liệu theme
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function __construct(ThemeDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Lấy danh sách chủ đề
     *
     * Tổng quan chức năng:
     * Dựa theo điều kiện truy vấn truyền vào, lấy dữ liệu danh sách theme có phân trang và định dạng dữ liệu trả về.
     * Nội dung xử lý bao gồm: chuyển timestamp thành chuỗi ngày, chuyển đường dẫn ảnh thành URL đầy đủ, phân tích dữ liệu JSON, v.v.
     *
     * @param array $where Mảng điều kiện truy vấn
     * @return array Mảng chứa dữ liệu danh sách list và tổng số count
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function getThemeList($where)
    {
        [$page, $limit] = $this->getPageValue();
        $field = 'id,title,info,type,home_image,category_image,detail_image,user_image,theme_data,add_time,up_time,is_use,page_type';
        $order = 'id desc';
        if (($where['page_type'] ?? '') === 'all') {
            unset($where['page_type']);
        } else {
            $where['page_type'] = 'theme';
        }
        $list = $this->dao->themeList($where, $field, $page, $limit, $order);
        foreach ($list as &$item) {
            if (isset($item['add_time'])) $item['add_time'] = date('Y-m-d H:i', $item['add_time']);
            if (isset($item['up_time'])) $item['up_time'] = date('Y-m-d H:i', $item['up_time']);
            if (isset($item['home_data_update_time'])) $item['home_data_update_time'] = date('Y-m-d H:i', $item['home_data_update_time']);
            if (isset($item['category_data_update_time'])) $item['category_data_update_time'] = date('Y-m-d H:i', $item['category_data_update_time']);
            if (isset($item['detail_data_update_time'])) $item['detail_data_update_time'] = date('Y-m-d H:i', $item['detail_data_update_time']);
            if (isset($item['user_data_update_time'])) $item['user_data_update_time'] = date('Y-m-d H:i', $item['user_data_update_time']);
            if (isset($item['theme_data_update_time'])) $item['theme_data_update_time'] = date('Y-m-d H:i', $item['theme_data_update_time']);
            if (isset($item['type'])) $item['type'] = $item['type'] == 0 ? 'Chủ đề tự tạo' : 'Chủ đề từ kho';
            if (isset($item['theme_data'])) $item['theme_data'] = json_decode($item['theme_data'], true) ?? [];
            $item['home_image'] = set_file_url($item['home_image']);
            $item['category_image'] = set_file_url($item['category_image']);
            $item['detail_image'] = set_file_url($item['detail_image']);
            $item['user_image'] = set_file_url($item['user_image']);
        }
        $count = $this->dao->themeCount($where);
        return compact('list', 'count');
    }

    /**
     * Lấy số phiên bản theme
     *
     * Tổng quan chức năng:
     * Lấy số phiên bản hiện tại của theme theo ID theme.
     * Nếu ID là 0 thì lấy số phiên bản của theme đang được sử dụng.
     *
     * @param int $id ID theme, 0 nghĩa là theme đang sử dụng
     * @return mixed Chuỗi số phiên bản
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function getThemeVersion($id)
    {
        $where = $id == 0 ? ['is_use' => 1] : ['id' => $id];
        return $this->dao->value($where, 'version');
    }

    /**
     * Lấy thông tin chủ đề
     *
     * Tổng quan chức năng:
     * Lấy thông tin chi tiết của theme theo ID theme và loại.
     * Hỗ trợ lấy toàn bộ thông tin hoặc dữ liệu của loại chỉ định (như trang chủ, trang danh mục, trang chi tiết, v.v.).
     * Thực hiện định dạng cần thiết và điền giá trị mặc định cho dữ liệu trả về.
     *
     * Cấu trúc dữ liệu trả về:
     * Tùy theo $type mà trả về cấu trúc khác nhau:
     * - 'all'/'base': Trả về mảng bản ghi đầy đủ của theme
     * - 'home'/'detail'/'user'/'theme': Trả về mảng cấu hình đã phân tích
     * - 'category': Trả về mảng có chứa status
     *
     * @param int $id ID theme, 0 nghĩa là theme đang sử dụng
     * @param string $type Loại dữ liệu: all, home, category, detail, user, theme, base
     * @return array|int[]|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws AdminException Ném ra khi dữ liệu không tồn tại
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function getThemeInfo($id, $type = 'all')
    {
        $where = $id == 0 ? ['is_use' => 1] : ['id' => $id];
        $info = $this->dao->get($where);
        if (!$info) throw new AdminException('Dữ liệu không tồn tại');
        $info = $info->toArray();
        if ($type == 'home') {
            return json_decode($info['home_data'], true) ?? [];
        } elseif ($type == 'category') {
            return ['status' => $info['category_data'] ?? 1];
        } elseif ($type == 'detail') {
            return json_decode($info['detail_data'], true) ?? [];
        } elseif ($type == 'user') {
            return json_decode($info['user_data'], true) ?? [];
        } elseif ($type == 'theme') {
            if ($info['theme_data'] == '' || $info['theme_data'] == null || $info['theme_data'] == 'null') {
                $info['theme_data'] = '{"theme_color":"#E93323","gradient_color":"#FF7931","sub_color":"#FE960F","light_color":"rgba(233, 51, 35, 0.1)"}';
            }
            return json_decode($info['theme_data'], true) ?? [];
        } elseif ($type == 'base') {
            return ['id' => $info['id'], 'type' => $info['type'], 'title' => $info['title'], 'info' => $info['info']];
        } else {
            $info['home_data_update_time'] = date('Y-m-d H:i:s', $info['home_data_update_time']);
            $info['category_data_update_time'] = date('Y-m-d H:i:s', $info['category_data_update_time']);
            $info['detail_data_update_time'] = date('Y-m-d H:i:s', $info['detail_data_update_time']);
            $info['user_data_update_time'] = date('Y-m-d H:i:s', $info['user_data_update_time']);
            $info['theme_data_update_time'] = date('Y-m-d H:i:s', $info['theme_data_update_time']);
            $info['add_time'] = date('Y-m-d H:i:s', $info['add_time']);
            $info['up_time'] = date('Y-m-d H:i:s', $info['up_time']);
            $ids = [$info['home_data_id'], $info['category_data_id'], $info['detail_data_id'], $info['user_data_id'], $info['theme_data_id']];
            $titles = $this->dao->getColumn([['id', 'in', $ids]], 'title', 'id');
            $info['home_data_id_title'] = $titles[$info['home_data_id']] ?? '';
            $info['category_data_id_title'] = $titles[$info['category_data_id']] ?? '';
            $info['detail_data_id_title'] = $titles[$info['detail_data_id']] ?? '';
            $info['user_data_id_title'] = $titles[$info['user_data_id']] ?? '';
            $info['theme_data_id_title'] = $titles[$info['theme_data_id']] ?? '';
            return $info;
        }
    }

    /**
     * Lưu dữ liệu theme
     *
     * Tổng quan chức năng:
     * Tạo theme mới hoặc cập nhật dữ liệu của theme hiện có.
     * Hỗ trợ sao chép dữ liệu từ theme mẫu để tạo theme mới.
     * Xử lý logic lưu dữ liệu tương ứng theo từng loại trang (home, category, detail, user, theme).
     * Tự động cập nhật số phiên bản và thời gian sửa đổi cuối cùng.
     *
     * @param int $id Khóa chính của theme, 0 nghĩa là thêm mới
     * @param array $data Dữ liệu cần lưu, bắt buộc có type, value, tùy chọn tid, title
     * @return int ID theme sau khi thêm mới hoặc cập nhật
     * @throws AdminException Ném ra khi có chỉ định tid nhưng theme không tồn tại
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function saveTheme($id, $data)
    {
        // Khởi tạo mảng dữ liệu cần ghi
        $saveData = [];

        // Nếu có chỉ định ID theme mẫu (tid) thì trước tiên sao chép dữ liệu của nó làm cơ sở
        if ($data['tid'] !== 0) {
            // Truy vấn theme mẫu
            $tInfo = $this->dao->get($data['tid']);
            if (!$tInfo) {
                throw new AdminException('Chủ đề không tồn tại');
            }
            // Chuyển dữ liệu theme mẫu thành mảng và loại bỏ khóa chính id để tránh xung đột
            $saveData = $tInfo->toArray();
            // Theme mới mặc định chưa kích hoạt
            $saveData['is_use'] = 0;
            unset($saveData['id']);
        }

        // Nếu có truyền tiêu đề thì ghi đè
        if ($data['title'] != '') {
            $saveData['title'] = $data['title'];
        }

        if ($id == 0) {
            $type = 0;
            $saveData['category_data'] = 1;
            $saveData['category_data_update_time'] = time();
            $saveData['category_image'] = '/statics/images/cate1.png';
        } else {
            $type = $this->dao->value(['id' => $id], 'type');
        }

        // Chuyển value truyền vào thống nhất thành chuỗi JSON
        $value = json_encode($data['value']);

        // Xử lý riêng dữ liệu, ảnh xem trước và thời gian cập nhật theo loại module
        switch ($data['type']) {
            case 'home':
                // Trang chủ
                $saveData['home_data'] = $value;
                $saveData['home_data_update_time'] = time();
                // Theme tự tạo cần ghi đồng thời dữ liệu mặc định
                if ($type == 0) {
                    $saveData['home_default_data'] = $value;
                }
                break;

            case 'category':
                // Trang danh mục
                $saveData['category_data'] = $value;
                $saveData['category_data_update_time'] = time();
                // Tạo đường dẫn ảnh xem trước tương ứng dựa theo value
                $saveData['category_image'] = '/statics/images/cate' . $value . '.png';
                if ($type == 0) {
                    $saveData['category_default_data'] = $value;
                    $saveData['category_default_image'] = '/statics/images/cate' . $value . '.png';
                }
                break;

            case 'detail':
                // Trang chi tiết
                $saveData['detail_data'] = $value;
                $saveData['detail_data_update_time'] = time();
                if ($type == 0) {
                    $saveData['detail_default_data'] = $value;
                }
                break;

            case 'user':
                // Trung tâm người dùng
                $saveData['user_data'] = $value;
                $saveData['user_data_update_time'] = time();
                if ($type == 0) {
                    $saveData['user_default_data'] = $value;
                }
                break;

            case 'theme':
                // Dữ liệu riêng của theme
                $saveData['theme_data'] = $value;
                $saveData['theme_data_update_time'] = time();
                if ($type == 0) {
                    $saveData['theme_default_data'] = $value;
                }
                break;
        }

        // Mỗi lần lưu đều tạo số phiên bản mới
        $saveData['version'] = uniqid();

        // Thêm mới hoặc cập nhật
        if ($id) {
            // Cập nhật
            $saveData['up_time'] = time();
            $this->dao->update($id, $saveData);
        } else {
            // Thêm mới
            $saveData['page_type'] = $data['page_type'];
            $saveData['add_time'] = time();
            $saveData['up_time'] = time();
            $id = $this->dao->insertGetId($saveData);
        }

        // Trả về ID theme cuối cùng
        return $id;
    }

    /**
     * Lưu thông tin tiêu đề của chủ đề
     *
     * Tổng quan chức năng:
     * Cập nhật tiêu đề và thông tin giới thiệu của theme, hoặc tạo bản ghi theme mới (chỉ gồm thông tin tiêu đề).
     * Thao tác cập nhật sẽ đồng thời cập nhật số phiên bản và thời gian sửa đổi cuối cùng.
     *
     * @param int $id ID theme, 0 nghĩa là thêm mới
     * @param array $data Mảng dữ liệu chứa title và info
     * @return int|mixed|string ID chủ đề
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function saveThemeTitle($id, $data)
    {
        // Nếu có chỉ định ID theme mẫu (tid) thì trước tiên sao chép dữ liệu của nó làm cơ sở
        if ($data['tid'] !== 0) {
            // Truy vấn theme mẫu
            $tInfo = $this->dao->get($data['tid']);
            if (!$tInfo) {
                throw new AdminException('Chủ đề không tồn tại');
            }
            // Chuyển dữ liệu theme mẫu thành mảng và loại bỏ khóa chính id để tránh xung đột
            $saveData = $tInfo->toArray();
            // Theme mới mặc định chưa kích hoạt
            $saveData['is_use'] = 0;
            unset($saveData['id']);
        }
        $saveData['title'] = $data['title'];
        $saveData['info'] = $data['info'];
        $saveData['version'] = uniqid();
        $saveData['page_type'] = $data['page_type'];
        if ($id) {
            $saveData['up_time'] = time();
            $this->dao->update($id, $saveData);
        } else {
            $saveData['add_time'] = time();
            $saveData['up_time'] = time();
            $id = $this->dao->insertGetId($saveData);
        }
        return $id;
    }

    /**
     * Lưu thông tin hình ảnh của chủ đề
     *
     * Tổng quan chức năng:
     * Cập nhật ảnh xem trước của các module theme (trang chủ, trang chi tiết, trang cá nhân).
     * Nếu là theme mặc định (type=0), sẽ đồng thời cập nhật cấu hình ảnh mặc định.
     * Tự động cập nhật số phiên bản và thời gian sửa đổi cuối cùng.
     *
     * @param int $id ID chủ đề
     * @param array $data Mảng dữ liệu chứa type (home/detail/user) và image
     * @return int|mixed|string ID chủ đề
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2025/12/18
     */
    public function saveThemeImage($id, $data)
    {
        $type = $id ? $this->dao->value(['id' => $id], 'type') : 0;
        switch ($data['type']) {
            case 'home':
                $saveData['home_image'] = $data['image'];
                if ($type == 0) $saveData['home_default_image'] = $data['image'];
                break;
            case 'detail':
                $saveData['detail_image'] = $data['image'];
                if ($type == 0) $saveData['detail_default_image'] = $data['image'];
                break;
            case 'user':
                $saveData['user_image'] = $data['image'];
                if ($type == 0) $saveData['user_default_image'] = $data['image'];
                break;
        }
        $saveData['version'] = uniqid();
        if ($id) {
            $saveData['up_time'] = time();
            $this->dao->update($id, $saveData);
        } else {
            $saveData['page_type'] = 'theme';
            $saveData['add_time'] = time();
            $saveData['up_time'] = time();
            $id = $this->dao->insertGetId($saveData);
        }
        return $id;
    }

    /**
     * Nhập dữ liệu theme
     *
     * Tổng quan chức năng:
     * Lưu dữ liệu cấu hình theme được nhập từ bên ngoài vào cơ sở dữ liệu.
     * Bao gồm toàn bộ cấu hình trang của theme (trang chủ, danh mục, chi tiết, trang cá nhân) và cấu hình mặc định tương ứng.
     *
     * @param array $config Mảng dữ liệu cấu hình theme
     * @return mixed ID theme mới thêm
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function importThemeData($config)
    {
        $data = [];
        $data['version'] = uniqid(); // Số phiên bản
        $data['title'] = $config['title']; // Tiêu đề
        $data['info'] = $config['info']; // Mô tả ngắn
        $data['type'] = 1; // Loại
        $data['home_data'] = $data['home_default_data'] = $config['home_data']; // Dữ liệu trang chủ
        $data['home_image'] = $data['home_default_image'] = $config['home_image']; // Ảnh bìa trang chủ
        $data['home_data_id'] = $config['home_data_id']; // ID dữ liệu trang chủ
        $data['home_data_update_time'] = time(); // Thời gian cập nhật dữ liệu trang chủ
        $data['category_data'] = $data['category_default_data'] = $config['category_data']; // Dữ liệu trang danh mục
        $data['category_image'] = $data['category_default_image'] = $config['category_image']; // Ảnh bìa trang danh mục
        $data['category_data_id'] = $config['category_data_id']; // ID dữ liệu trang danh mục
        $data['category_data_update_time'] = time(); // Thời gian cập nhật dữ liệu trang danh mục
        $data['detail_data'] = $data['detail_default_data'] = $config['detail_data']; // Dữ liệu trang chi tiết
        $data['detail_image'] = $data['detail_default_image'] = $config['detail_image']; // Ảnh bìa trang chi tiết
        $data['detail_data_id'] = $config['detail_data_id']; // ID dữ liệu trang chi tiết
        $data['detail_data_update_time'] = time(); // Thời gian cập nhật dữ liệu trang chi tiết
        $data['user_data'] = $data['user_default_data'] = $config['user_data']; // Dữ liệu trang cá nhân
        $data['user_image'] = $data['user_default_image'] = $config['user_image']; // Ảnh bìa trang cá nhân
        $data['user_data_id'] = $config['user_data_id']; // ID dữ liệu trang cá nhân
        $data['user_data_update_time'] = time(); // Thời gian cập nhật dữ liệu trang cá nhân
        $data['theme_data'] = $data['theme_default_data'] = json_encode($config['theme_data'], JSON_UNESCAPED_UNICODE); // Dữ liệu chủ đề
        $data['theme_data_id'] = $config['theme_data_id']; // ID dữ liệu theme
        $data['theme_data_update_time'] = time(); // Thời gian cập nhật dữ liệu theme
        $data['page_type'] = 'theme'; // Loại trang
        $data['is_use'] = 0; // Đã sử dụng
        $data['is_del'] = 0; // Đã xóa
        $data['add_time'] = time(); // Thời gian thêm
        $data['up_time'] = time(); // Thời gian cập nhật
        $id = $this->dao->insertGetId($data);
        return $id;
    }

    /**
     * Áp dụng chủ đề
     *
     * Tổng quan chức năng:
     * Đặt theme được chỉ định thành theme đang kích hoạt.
     * Thao tác này sẽ đặt tất cả theme về trạng thái chưa kích hoạt trước, sau đó kích hoạt theme có ID chỉ định.
     *
     * @param int $id ID theme cần kích hoạt
     * @return bool Thao tác thành công thì trả về true
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function useTheme(int $id)
    {
        $this->dao->update(['is_use' => 1], ['is_use' => 0]);
        $this->dao->update($id, ['is_use' => 1]);
        return true;
    }

    /**
     * Áp dụng dữ liệu chủ đề
     *
     * Tổng quan chức năng:
     * Áp dụng dữ liệu module cụ thể của một theme (như trang chủ, trang chi tiết, v.v.) vào bản ghi dữ liệu theme đích.
     * Thực hiện tái sử dụng một phần hoặc kết hợp dữ liệu theme.
     *
     * @param int $id ID dữ liệu theme đích
     * @param int $theme_id ID theme nguồn
     * @param string $type Loại dữ liệu (home/category/detail/user/theme)
     * @return bool Thao tác thành công thì trả về true
     * @throws AdminException Ném ra khi dữ liệu theme nguồn không tồn tại
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function useThemeData(int $id, int $theme_id, string $type)
    {
        $data = $this->dao->get(['id' => $theme_id]);
        if (!$data) throw new AdminException('Dữ liệu chủ đề không tồn tại');
        $this->dao->update(['id' => $id], [$type . '_data_update_time' => time(), $type . '_image' => $data[$type . '_image'], $type . '_data' => $data[$type . '_data'], $type . '_data_id' => $theme_id]);
        return true;
    }


    /**
     * Lấy thông tin theme đang được sử dụng
     *
     * Tổng quan chức năng:
     * Truy vấn theme đang được kích hoạt (is_use=1) và tổng hợp dữ liệu các module liên kết của nó (trang chủ, danh mục, chi tiết, v.v.).
     * Nếu dùng chế độ kết hợp (tham chiếu module của theme khác), sẽ phân tích ra tiêu đề và thông tin ảnh của theme nguồn thực tế.
     *
     * Cấu trúc dữ liệu trả về:
     * - id, title, info, version: Thông tin cơ bản của theme
     * - confuse: Có phải chế độ kết hợp không (0/1)
     * - data_info: Danh sách chi tiết các module (gồm key, title, image, update_time)
     * - theme_data: Cấu hình style toàn cục của theme
     *
     * @return array Trả về dữ liệu gồm thông tin cơ bản của theme và cấu hình chi tiết của từng module
     * @throws AdminException Ném ngoại lệ khi không có theme nào đang được sử dụng
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function getUsingTheme()
    {
        // Truy vấn bản ghi theme đang được sử dụng (is_use = 1)
        $data = $this->dao->get(['is_use' => 1]);
        if (!$data) throw new AdminException('Không có chủ đề nào đang được sử dụng');

        // Thu thập ID theme liên kết của các module và lọc bỏ giá trị rỗng
        $themeIds = array_filter([
            $data['home_data_id'],      // ID theme liên kết của module trang chủ
            $data['category_data_id'],  // ID theme liên kết của module trang danh mục
            $data['detail_data_id'],    // ID theme liên kết của module trang chi tiết
            $data['user_data_id'],      // ID theme liên kết của module trang cá nhân
            $data['theme_data_id'],     // ID theme liên kết của dữ liệu riêng theme
        ]);


        // Tạo mảng thông tin theme trả về cuối cùng
        $theme = [];
        $theme['id'] = $data['id'];             // ID chủ đề
        $theme['title'] = $data['title'];         // Tên chủ đề
        $theme['info'] = $data['info'];           // Mô tả ngắn của chủ đề
        $theme['version'] = $data['version'];   // Số phiên bản theme
        $theme['confuse'] = 0;                  // Có dùng theme kết hợp không: 0 không, 1 có
        // Nếu có ID theme liên kết thì truy vấn hàng loạt tiêu đề của chúng để dùng cho việc ghép nối phía sau
        if ($themeIds) {
            $themeData = $this->dao->getColumn([['id', 'in', $themeIds]], 'title', 'id');
            $theme['confuse'] = 1;
        }
        $theme['data_info'] = [
            [
                'key' => 'home',
                'title' => $themeData[$data['home_data_id']] ?? $data['title'], // Tiêu đề module trang chủ (ưu tiên lấy tiêu đề theme liên kết)
                'image' => set_file_url($data['home_image']), // Ảnh xem trước trang chủ
                'update_time' => date('Y-m-d H:i:s', $data['home_data_update_time']), // Thời gian cập nhật dữ liệu trang chủ
            ],
            [
                'key' => 'category',
                'title' => $themeData[$data['category_data_id']] ?? $data['title'], // Tiêu đề module trang danh mục (ưu tiên lấy tiêu đề theme liên kết)
                'image' => set_file_url($data['category_image']), // Ảnh xem trước trang danh mục
                'update_time' => date('Y-m-d H:i:s', $data['category_data_update_time']), // Thời gian cập nhật dữ liệu trang danh mục
            ],
            [
                'key' => 'detail',
                'title' => $themeData[$data['detail_data_id']] ?? $data['title'], // Tiêu đề module trang chi tiết (ưu tiên lấy tiêu đề theme liên kết)
                'image' => set_file_url($data['detail_image']), // Ảnh xem trước trang chi tiết
                'update_time' => date('Y-m-d H:i:s', $data['detail_data_update_time']), // Thời gian cập nhật dữ liệu trang chi tiết
            ],
            [
                'key' => 'user',
                'title' => $themeData[$data['user_data_id']] ?? $data['title'], // Tiêu đề module trang cá nhân (ưu tiên lấy tiêu đề theme liên kết)
                'image' => set_file_url($data['user_image']), // Ảnh xem trước trang cá nhân
                'update_time' => date('Y-m-d H:i:s', $data['user_data_update_time']), // Thời gian cập nhật dữ liệu trang cá nhân
            ],
        ];
        $theme['theme_data'] = json_decode($data['theme_data'], true); // Dữ liệu riêng của theme (định dạng JSON)

        return $theme;
    }

    /**
     * @description: Khôi phục chủ đề
     * @param int $id ID chủ đề
     * @return void
     */
    public function restoreTheme(int $id)
    {
        $data = $this->dao->get($id);
        if (!$data) throw new AdminException('Chủ đề không tồn tại');
        $this->dao->update($id, [
            'home_data' => $data['home_default_data'],
            'home_data_id' => 0,
            'home_image' => $data['home_default_image'],
            'home_data_update_time' => time(),
            'category_data' => $data['category_default_data'],
            'category_data_id' => 0,
            'category_image' => $data['category_default_image'],
            'category_data_update_time' => time(),
            'detail_data' => $data['detail_default_data'],
            'detail_data_id' => 0,
            'detail_image' => $data['detail_default_image'],
            'detail_data_update_time' => time(),
            'user_data' => $data['user_default_data'],
            'user_data_id' => 0,
            'user_image' => $data['user_default_image'],
            'user_data_update_time' => time(),
            'theme_data' => $data['theme_default_data'],
            'theme_data_update_time' => time(),
            'version' => uniqid(), // Cập nhật số phiên bản
            'up_time' => time(), // Thời gian cập nhật
        ]);
        return true;
    }

    /**
     * Xóa chủ đề
     *
     * Tổng quan chức năng:
     * Xóa mềm theme chỉ định (cập nhật trường is_del).
     *
     * @param int $id ID chủ đề
     * @return bool Thao tác thành công thì trả về true
     * @throws AdminException Ném ra khi theme không tồn tại
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function deleteTheme(int $id)
    {
        $data = $this->dao->get($id);
        if (!$data) throw new AdminException('Chủ đề không tồn tại');
        if ($data['is_use']) throw new AdminException('Chủ đề này đang được sử dụng, không thể xóa');
        $this->dao->update($id, ['is_del' => 1]);
        return true;
    }

    /**
     * Lấy cấu hình thanh điều hướng dưới cùng của theme đang kích hoạt
     *
     * Tổng quan chức năng:
     * Phân tích dữ liệu trang chủ của theme đang kích hoạt, trích xuất cấu hình thành phần điều hướng dưới cùng (pagefoot) trong đó.
     *
     * @return array Trả về mảng cấu hình của thành phần có tên pagefoot, không tìm thấy thì trả về mảng rỗng
     * @throws ApiException Ném ra khi theme đang kích hoạt không có dữ liệu trang chủ
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function themeNavigation()
    {
        // Truy vấn dữ liệu trang chủ của theme đang được sử dụng (chuỗi JSON)
        $value = $this->dao->value(['is_use' => 1], 'home_data');
        if (!$value) {
            throw new ApiException('Dữ liệu không tồn tại');
        }

        // Khởi tạo dữ liệu điều hướng là mảng rỗng
        $navigation = [];

        // Nếu dữ liệu trang chủ tồn tại thì tiến hành phân tích và duyệt qua
        if ($value) {
            // Giải mã chuỗi JSON thành mảng
            $value = json_decode($value, true);
            // Duyệt qua các thành phần trang chủ, tìm thành phần điều hướng dưới cùng có tên pagefoot
            foreach ($value['value'] as $item) {
                if (isset($item['name']) && strtolower($item['name']) === 'pagefoot') {
                    // Tìm thấy thì gán giá trị và dừng vòng lặp
                    $navigation = $item;
                    break;
                }
            }
        }

        // Trả về cấu hình điều hướng (có thể là mảng rỗng)
        return $navigation;
    }

    /**
     * Lấy danh sách trang micro
     *
     * Tổng quan chức năng:
     * Truy vấn phân trang dữ liệu danh sách trang micro (page_type='micro').
     *
     * Chức năng chính:
     * 1. Truy vấn phân trang - Lấy dữ liệu theo tham số phân trang của hệ thống
     * 2. Lọc dữ liệu - Chỉ truy vấn các bản ghi chưa bị xóa và có loại là trang micro
     * 3. Định dạng - Chuyển timestamp sang định dạng ngày dễ đọc
     *
     * @return array Mảng chứa dữ liệu danh sách list và tổng số count
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/02/03
     */
    public function getMicroPageList()
    {
        [$page, $limit] = $this->getPageValue(); // Lấy tham số phân trang
        $field = 'id,title,info,type,add_time,up_time,page_type'; // Các trường cần truy vấn
        $order = 'id desc'; // Thứ tự sắp xếp
        $where = [
            'is_del' => 0, // Chưa xóa
            'page_type' => 'micro', // Loại trang micro
        ];
        $list = $this->dao->themeList($where, $field, $page, $limit, $order); // Tra cứu danh sách
        foreach ($list as &$item) {
            // Định dạng thời gian
            if (isset($item['add_time'])) $item['add_time'] = date('Y-m-d H:i', $item['add_time']);
            if (isset($item['up_time'])) $item['up_time'] = date('Y-m-d H:i', $item['up_time']);
        }
        $count = $this->dao->themeCount($where); // Lấy tổng số
        return compact('list', 'count');
    }

    /**
     * Xuất dữ liệu theme (logic cốt lõi)
     * Đóng gói cấu hình theme và các ảnh liên quan thành file Zip, trả về địa chỉ tải xuống
     *
     * @param $themeInfo
     * @return string Địa chỉ tải xuống
     * @throws    hinkdbexceptionDataNotFoundException
     * @throws    hinkdbexceptionDbException
     * @throws    hinkdbexceptionModelNotFoundException
     * @author wuhaotian
     * @email 442384644@qq.com
     * @date 2026/3/10
     */
    public function exportThemePackage($info): string
    {
        // 1. Thiết lập thư mục tạm để xuất
        $dir = public_path() . 'theme/download/' . $info['id'] . '/';

        // 2. Xử lý các ảnh chính (ảnh trang chủ, ảnh danh mục, ảnh chi tiết, ảnh trang cá nhân)
        $images = ['home_image', 'category_image', 'detail_image', 'user_image'];
        $defaultImages = ['home_default_image', 'category_default_image', 'detail_default_image', 'user_default_image'];
        $i = 1;
        foreach ($images as $key => $image) {
            if (isset($info[$image]) && $info[$image]) {
                $originalUrl = $info[$image];
                $isRemote = (bool)preg_match('/^https?:\/\//i', $originalUrl);
                if ($isRemote) {
                    $urlPath = parse_url($originalUrl, PHP_URL_PATH) ?: $originalUrl;
                    $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION)) ?: 'jpg';
                    $newPath = $dir . $i . '_' . $image . '.' . $extension;
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $originalUrl,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_REFERER => rtrim(sys_config('site_url'), '/'),
                        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CRMEB/1.0)',
                        CURLOPT_HTTPHEADER => ['Accept: image/webp,image/*,*/*'],
                    ]);
                    $content = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($content && $httpCode === 200 && file_put_contents($newPath, $content) !== false) {
                        $info[$image] = 'theme/download/' . $i . '_' . $image . '.' . $extension;
                        $info[$defaultImages[$key]] = 'theme/download/' . $i . '_' . $image . '.' . $extension;
                    }
                } else {
                    $localPath = public_path() . ltrim(preg_replace('/^https?:\/\/[^\/]+/', '', $originalUrl), '/');
                    if (!file_exists($localPath)) {
                        $i++;
                        continue;
                    }
                    $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION)) ?: 'jpg';
                    $newPath = $dir . $i . '_' . $image . '.' . $extension;
                    if (copy($localPath, $newPath)) {
                        $info[$image] = 'theme/download/' . $i . '_' . $image . '.' . $extension;
                        $info[$defaultImages[$key]] = 'theme/download/' . $i . '_' . $image . '.' . $extension;
                    }
                }
            }
            $i++;
        }

        // 3. Xử lý ảnh trong home_data / detail_data / user_data
        $imagesDir = $dir . 'images/';
        $index = 1;
        $map = [];
        $isAttachImage = function ($str) {
            if (!is_string($str) || $str === '') return false;
            if (strpos($str, 'uploads/attach') !== false) return true;
            if (preg_match('/^https?:\/\//i', $str)) return true;
            return false;
        };

        $process = function (&$value, $key) use (&$index, &$map, $imagesDir, $isAttachImage) {
            if (!is_string($value) || !$isAttachImage($value)) return;
            if (!isset($map[$value])) {
                $path = parse_url($value, PHP_URL_PATH);
                $path = $path ?: $value;
                $basename = basename($path);
                $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'])) {
                    $ext = 'jpg';
                    $basename = md5($value) . '.' . $ext;
                }
                $dest = $imagesDir . $basename;
                $rel = 'images/' . $basename;
                $ok = false;
                $isRemote = preg_match('/^https?:\/\//i', $value);
                if ($isRemote) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $value,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_REFERER => rtrim(sys_config('site_url'), '/'),
                        CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CRMEB/1.0)',
                        CURLOPT_HTTPHEADER => ['Accept: image/webp,image/*,*/*'],
                    ]);
                    $content = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    if ($content && $httpCode === 200) {
                        $ok = (file_put_contents($dest, $content) !== false);
                    }
                } else {
                    $src = public_path() . ltrim($path, '/');
                    if (file_exists($src)) $ok = @copy($src, $dest);
                }
                if ($ok) {
                    $map[$value] = $rel;
                    $index++;
                } else {
                    return;
                }
            }
            $value = $map[$value] ?? $value;
        };

        $homeData = json_decode($info['home_data'] ?? '[]', true);
        $detailData = json_decode($info['detail_data'] ?? '[]', true);
        $userData = json_decode($info['user_data'] ?? '[]', true);

        if (is_array($homeData)) array_walk_recursive($homeData, $process);
        if (is_array($detailData)) array_walk_recursive($detailData, $process);
        if (is_array($userData)) array_walk_recursive($userData, $process);

        $info['home_data'] = json_encode($homeData, JSON_UNESCAPED_UNICODE);
        $info['detail_data'] = json_encode($detailData, JSON_UNESCAPED_UNICODE);
        $info['user_data'] = json_encode($userData, JSON_UNESCAPED_UNICODE);
        $info['home_default_data'] = json_encode($homeData, JSON_UNESCAPED_UNICODE);
        $info['detail_default_data'] = json_encode($detailData, JSON_UNESCAPED_UNICODE);
        $info['user_default_data'] = json_encode($userData, JSON_UNESCAPED_UNICODE);
        $info['theme_data'] = $info['theme_default_data'] = json_decode($info['theme_data'], true);

        // 4. Ghi config.json
        file_put_contents($dir . 'config.json', json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 7. Đóng gói thành zip
        $zip = new \ZipArchive();
        $zip->open($dir . $info['title'] . '.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $rootPath = realpath($dir);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            if ($file->isDir()) continue;
            $filePath = $file->getRealPath();
            if (basename($filePath) === $info['title'] . '.zip') continue;
            $relativePath = ltrim(str_replace($rootPath, '', $filePath), DIRECTORY_SEPARATOR);
            $zip->addFile($filePath, $relativePath);
        }
        $zip->close();

        return sys_config('site_url') . '/theme/download/' . $info['id'] . '/' . $info['title'] . '.zip';
    }
}
