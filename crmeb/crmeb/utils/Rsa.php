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

namespace crmeb\utils;

use think\exception\ValidateException;

/**
 * Class Rsa
 * @author Deng Fenglai
 * @email 136327134@qq.com
 * @date 2023/5/16
 * @package crmeb\utils
 */
class Rsa
{
    /**
     * @var string
     */
    protected $publicKey;

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * Lấy file chứng chỉ (certificate)
     * @param $publicKey
     * @param $privateKey
     */
    public function __construct(string $publicKey = 'cert_public_password.key', string $privateKey = 'cert_private_password.key')
    {
        $this->basePath = app()->getRootPath();
        if ($publicKey) {
            $this->publicKey = $this->basePath . $publicKey;
        }
        if ($privateKey) {
            $this->privateKey = $this->basePath . $publicKey;
        }
        if (!is_file($this->publicKey) || !is_file($this->privateKey)) {
            $this->exportOpenSSLFile();
        }
    }

    /**
     * @return false|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/16
     */
    public function getPublicKey()
    {
        if (!is_file($this->publicKey)) {
            $this->exportOpenSSLFile();
        }

        return file_get_contents($this->publicKey);
    }

    /**
     * Tạo chứng chỉ
     * @return bool
     */
    public function exportOpenSSLFile($passwork = null)
    {

        $publicKey = $privateKey = '';
        $dir = app()->getRootPath() . 'runtime/conf';
        $conf = 'openssl.cnf';
        if (!is_dir($dir)) {
            mkdir($dir, 0700);
        }
        if (!file_exists($conf)) {
            touch($dir . '/' . $conf);
        }

        //Thiết lập tham số
        $config = [
            "digest_alg" => "sha256",
            //Số byte 512 1024 2048 4096 v.v.
            "private_key_bits" => 1024,
            "config" => $dir . '/' . $conf,
            //Loại mã hóa
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];

        //Tạo private key và public key
        $res = openssl_pkey_new($config);
        if ($res == false) {
            //Tạo thất bại, vui lòng kiểm tra file openssl.cnf có tồn tại không
            return false;
        }

        //Xuất khóa thành chuỗi mã hóa PEM, rồi trả ra (truyền qua tham chiếu).
        openssl_pkey_export($res, $privateKey, $passwork, $config);
        $publicKey = openssl_pkey_get_details($res);
        $publicKey = $publicKey["key"];

        //Tạo chứng chỉ
        $createPublicFileRet = file_put_contents($this->publicKey, $publicKey);
        $createPrivateFileRet = file_put_contents($this->privateKey, $privateKey);
        if (!($createPublicFileRet || $createPrivateFileRet)) {
            return false;
        }

        openssl_free_key($res);
        return true;
    }

    /**
     * Mã hóa dữ liệu
     * @param string $data
     * @param string|null $passwork
     * @return false|string
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/16
     */
    function privateEncrypt(string $data, string $passwork = null)
    {
        $encrypted = '';
        $pi_key = openssl_pkey_get_private(file_get_contents($this->privateKey), $passwork);//Hàm này dùng để kiểm tra private key có dùng được không, nếu dùng được thì trả về resource id
        //Độ dài mã hóa tối đa cho phép là 117, phải mã hóa theo từng đoạn
        $plainData = str_split($data, 100);//Số bit của khóa được tạo, khóa 1024 bit
        foreach ($plainData as $chunk) {
            $partialEncrypted = '';
            $encryptionOk = openssl_private_encrypt($chunk, $partialEncrypted, $pi_key);//Mã hóa bằng private key
            if ($encryptionOk === false) {
                return false;
            }
            $encrypted .= $partialEncrypted;
        }

        $encrypted = base64_encode($encrypted);//Nội dung sau khi mã hóa thường có ký tự đặc biệt, cần chuyển đổi bảng mã, khi truyền qua url giữa các mạng cần chú ý base64 encode có an toàn cho url không
        return $encrypted;
    }

    /**
     * Giải mã bằng public key RSA (nội dung mã hóa bằng private key có thể giải mã bằng public key)
     * @param string $public_key Public key
     * @param string $data Chuỗi đã mã hóa bằng private key
     * @return string $decrypted Trả về chuỗi sau khi giải mã
     * @author mosishu
     */
    function publicDecrypt(string $data)
    {
        $decrypted = '';
        $pu_key = openssl_pkey_get_public(file_get_contents($this->publicKey));//Hàm này dùng để kiểm tra public key có dùng được không
        $plainData = str_split(base64_decode($data), 128);//Số bit của khóa được tạo, khóa 1024 bit
        foreach ($plainData as $chunk) {
            $str = '';
            $decryptionOk = openssl_public_decrypt($chunk, $str, $pu_key);//Giải mã bằng public key
            if ($decryptionOk === false) {
                return false;
            }
            $decrypted .= $str;
        }
        return $decrypted;
    }

    /**
     * Giải mã bằng private key
     * @param string $data
     * @return mixed
     * @author Deng Fenglai
     * @email 136327134@qq.com
     * @date 2023/5/16
     */
    public function privateDecrypt(string $data)
    {
        if (!is_file($this->privateKey)) {
            $this->exportOpenSSLFile();
        }

        $res = openssl_private_decrypt(base64_decode($data), $decryptedData, file_get_contents($this->privateKey));

        if (false === $res) {
            throw new ValidateException('RSA: giải mã thất bại');
        }

        return $decryptedData;
    }

}
