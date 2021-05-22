<?php

class AddressParser
{

    private $str;
    private $area;

    private $result = [
        "consignee" => "",
        "mobile"    => "",
        "prov"      => "",
        "city"      => "",
        "dist"      => "",
        "addr"      => "",
        "post_code" => "",
    ];

    /**
     * AddressParser constructor.
     * @param $addrStr
     */
    public function __construct($addrStr)
    {
        $this->str = $addrStr;
        $areaJson = require "area.php";
        $this->area = json_decode($areaJson, 1);
    }

    public function parse()
    {
        $this->wash();
        $this->mobile();
        $this->postCode();
        $this->address();
        $this->consignee();
        return $this->result;
    }

    private function wash()
    {
        // 替换掉特殊字符和描述性文本
        $specialChars = [',', '，', '"', "'", "\n", "\r\n", "\r", ":", "：", "、"];
        $labelText = [
            '收件人',
            '收货人',
            '详细地址',
        ];
        $this->str = str_replace(array_merge($specialChars, $labelText), " ", $this->str);

        // 替换不规则的描述性文本
        $commentStrs = [
            "收件",
            "收货",
            "地址",
            "详细",
            "电话",
            "邮编",
            "手机",
            "姓名",
            "名字",
            "名称",
            "联系",
            "所在地区",
            "地区",
        ];
        $keywords = join("|", $commentStrs);
        $pattern = "/\s?($keywords).*?(?=\s|\d)/u";
        $this->str = preg_replace($pattern, " ", $this->str);
    }

    private function mobile()
    {
        $phoneNumber = "";
        $patterns = [
            "phone"  => "/(\d{3,}-\d{5,})/u", // 010-7650342
            "mobile" => "/(\d{3}.?\d{4}.?\d+)/u" // 13344556677 133-4455-6677 133 4455 6677
        ];
        foreach ($patterns as $mod => $pattern) {
            preg_match_all($pattern, $this->str, $m);
            if (!empty($m[1][0])) {
                $phoneNumber = $m[1][0];
                if ($mod == 'mobile') {
                    $phoneNumber = str_replace(["-", " "], "", $phoneNumber);
                }
                break;
            }
        }

        $this->str = preg_replace($patterns, [" ", " "], $this->str);
        $this->result['mobile'] = $phoneNumber;
    }

    private function postCode()
    {
        $postCode = "";
        $pattern = "/(\d{6})/u";
        preg_match_all($pattern, $this->str, $m);
        if (!empty($m[1][0])) {
            $postCode = $m[1][0];
        }

        $this->result['post_code'] = $postCode;
        $this->str = preg_replace($pattern, " ", $this->str);
    }

    private function address()
    {
        $prov = $city = $dist = $address = "";
        $hasP = $hasC = $hasD = false;
        foreach ($this->area as $p => $items) {
            $cleanP = $this->pureProv($p);
            if (strpos($this->str, $cleanP) !== false) {
                $prov = $p;
                $hasP = true;
            }

            foreach ($items as $c => $ds) {
                if (strpos($this->str, $this->pureAddr($c)) !== false) {
                    $prov = $prov ?: $p;
                    $city = $c;
                    $hasC = true;
                }

                foreach ($ds as $d) {
                    if (strpos($this->str, $d) !== false) {
                        $city = $city ?: $c;
                        $dist = $dist ?: $d;
                        if (!$prov || !empty($this->area[$p][$city])) {
                            $prov = $p;
                        }
                        $hasD = true;

                        if ($hasP || $hasC) {
                            break 3;
                        }
                    }
                }
            }
        }

        $isMunicipality = $this->pureAddr($prov) == $this->pureAddr($city); // 是否是直辖市

        $replacePatterns = [];
        $distPattern = "";
        if ($hasP) {
            $p = sprintf("(%s.*(?=[省|市|区])?)", $this->pureProv($prov));
            $p = $isMunicipality ? $p . "?" : $p;
            $replacePatterns[] = $p;
            $distPattern = $p;
        }
        if ($hasC) {
            $p = sprintf("(%s[区|县|市|州]?)", $this->pureAddr($city));
            $p = $isMunicipality ? $p . "?" : $p;
            $replacePatterns[] = $p;
            $distPattern = $p;
        }
        if ($hasD) {
            $p = sprintf("(%s[区|县|市|州]?)", $this->pureAddr($dist));;
            $replacePatterns[] = $p;
            $distPattern = $p;
        }

        // 获取地区
        $distPattern = "/$distPattern\s*(\S+)/u";
        if (preg_match_all($distPattern, $this->str, $m)) {
            $address = $m[count($m) - 1][0];
        }

        $ps = join("", $replacePatterns);
        $pattern = "/$ps\s*(\S+)/u";
        $this->str = preg_replace($pattern, "", $this->str);

        $this->result['prov'] = $prov;
        $this->result['city'] = $city;
        $this->result['dist'] = $dist;
        $this->result['addr'] = trim($address);
    }

    private function pureProv($province)
    {
        if (mb_strlen($province) <= 2) {
            return $province;
        }
        if (strpos($province, "黑龙江") !== false) {
            return "黑龙江";
        }
        return mb_substr($province, 0, 2);
    }

    private function pureAddr($addressName)
    {
        if (mb_strlen($addressName) <= 2) {
            return $addressName;
        }
        return preg_replace('/[省|市|区|县|州]$/u', "", $addressName);
    }

    private function consignee()
    {
        $consignee = "";
        $pattern = "/[^0-9\s]+\s?/u";
        if (preg_match_all($pattern, $this->str, $m)) {
            $consignee = trim($m[0][0]);
        }
        $this->result['consignee'] = $consignee;
    }
}