<?php

require "../src/AddrPaser.class.php";

$cases = [
    "山西省运城市新绛县龙兴镇店头村店头大街1号 李彦宏电话:0359-7654321 邮编：043100",
    "山西省运城市新绛县龙兴镇店头村店头大街1号 李彦宏133-4455-6677 邮编：043100",
    "山西省运城市新绛县龙兴镇店头村店头大街1号 李彦宏133 4455 6677 邮编：043100",
    "北京市朝阳区富康路姚家园3楼马云150-0000-0000",
    "北京市朝阳区富康路姚家园3号楼5单元3305马云13344556677",
    "北京市朝阳区富康路姚家园3号楼5单元3305马云13344556677邮编038300",
    "马云,1351111111,北京市朝阳区富康路姚家园3楼邮编038300",
    "马云1351111111北京市朝阳区富康路姚家园3楼0",
    "北京市朝阳区富康路姚家园3楼1351111111马云",
    "北京市朝阳区富康路姚家园3楼150-0000-0000马云",
    /* pro cases */
    "深圳市南山区爱荣路景园大厦 郑小姐13344556677",
    "刘小姐13344556677
广州市南沙区进港大道中碧桂园蜜柚2B1106",
    "深圳龙岗坂田杨美荔枝苑 b3_1506 贾女士 13344556677",
    "广州市南沙区金隆路35号保利大都汇9-5-1105 吴先生13344556677",
    "湖北省随州市东关学校南楼 
13344556677雷先生",
    "瑶瑶 13344556677 浙江温州娄桥街道公馆10号71栋703",
    "李总13344556677四川省宜宾市叙州区南岸街道鼎业兴城 3栋",
    "北京市金顶北路69号院金隅科技 陈女士 13344556677 1组",
    "林先生 13344556677
温州市瓯海瞿溪街道红桥路109号",
    "福建省福州市潘渡乡潘渡大桥2号福州理工学院江美女13344556677",
    "茵悦之生三期5栋B单元7E(放丰巢柜），13344556677，张姐",
    "深圳市光明区光明街道碧眼社区碧眼旧村南面坑996-7号、收件人翁先生、电话：13344556677",
    "广西省南宁市青秀区民族大道139号凤岭新新家园A区5栋，黄女士，13344556677",
    "袁sir13344556677 四川省成都市其它区高新区紫荆西路6号神仙树大院3期19栋1单元1002",
    "收件人：佛山市南海区桂城街道富丰君御1栋701 林姐",
    "深圳市龙岗区坂田万科城紫檀居A-502李阿姨13344556677",
    "招远市天府路88号原森红木，收件人Luna，电话13344556677",
    "收件人：陶容
手机号：13344556677
所在地区：四川省凉山彝族自治州西昌市
详细地址：春城西路88号月城公寓二期38幢一单元二楼一号",
    "青岛市黄岛区汉江路10号融创维多利亚湾内网点商铺 展女士 13344556677",
    "收货人：朱姐
手机号：13344556677
所在地区：安徽省 六安市叶集区
详细地址：龙庭御景小区",
    "龙岗区坂田街道佳兆业悦峰1栋B座303 汪女士 13344556677",
    "深圳市龙岗区龙城街道中央悦城9A101，舒女士，13344556677",
    "收件人：佛山市南海区桂城街道富丰君御8栋201 林姐 13344556677",
];

foreach ($cases as $case) {
    $parseResult = (new AddressParser($case))->parse();
    print_r($parseResult);
}