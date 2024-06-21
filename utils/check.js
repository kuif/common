const checkName = name => {
  var reg = /^[\u4e00-\u9fa5]{2,10}$/;
  return reg.test(name);
}

const checkMobile = mobile => {
  var reg = /^1[3456789]\d{9}$/;
  return reg.test(mobile);
}

const checkNumber = num => {
  var reg = /^\d{4}$/;
  return reg.test(num);
}

const parse = (ymdhms, format) => {
  var regymdzz = "YYYY|MM|DD|hh|mm|ss|zz";
  return format.replace(new RegExp(regymdzz, "g"), function (str, index) {
    return str == "zz" ? "00" : digit(ymdhms[str]);
  });
}

const digit = num => {
  return num < 10 ? "0" + (num | 0) : num;
}

// 验证身份证
export function checkIDCard(idCard) {
  // ^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$ // 15位
  var regIdCard =
    /^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;
  if (regIdCard.test(idCard)) {
    if (idCard.length == 18) {
      var idCardWi = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10,
        5, 8, 4, 2);
      var idCardY = new Array(1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2);
      var idCardWiSum = 0;
      for (var i = 0; i < 17; i++) {
        idCardWiSum += idCard.substring(i, i + 1) * idCardWi[i];
      }
      var idCardMod = idCardWiSum % 11;
      var idCardLast = idCard.substring(17);
      if (idCardMod == 2) {
        if (idCardLast == "X" || idCardLast == "x") {
          return true;
        } else {
          // app.showNone('身份证号码错误！')
          return false;
        }
      } else {
        if (idCardLast == idCardY[idCardMod]) {
          return true;
        } else {
          // app.showNone('身份证号码错误！')
          return false;
        }
      }
    } else {
      return true;
    }
  } else {
    return false;
    // app.showNone('请输入有效的身份证号码！')
  }
}

// 验证手机号
export function checkPhone(phone) {
  let reg_phone = /^(\+)?(0|86|17951)?1(3\d|4[579]|5\d|6\d|7\d|8\d|9\d)\d{8}$/;
  if (reg_phone.test(phone)) {
    return true;
  } else {
    // app.showNone('请输入正确的手机号！')
    return false;
  }
}

const timeToDate = (date,format) => {
  format = format || 'YYYY-MM-DD hh:mm:ss';
  var dateTest = (/^(-)?\d{1,10}$/.test(date) || /^(-)?\d{1,13}$/.test(date));
  if (/^[1-9]*[1-9][0-9]*$/.test(date) && dateTest) {
    var vdate = parseInt(date);
    if (/^(-)?\d{1,10}$/.test(vdate)) {
      vdate = vdate * 1000;
    } else if (/^(-)?\d{1,13}$/.test(vdate)) {
      vdate = vdate * 1000;
    } else if (/^(-)?\d{1,14}$/.test(vdate)) {
      vdate = vdate * 100;
    } else {
      alert("时间戳格式不正确");
      return;
    }
    var setdate = new Date(vdate);
    return parse({ YYYY: setdate.getFullYear(), MM: digit(setdate.getMonth() + 1), DD: digit(setdate.getDate()), hh: digit(setdate.getHours()), mm: digit(setdate.getMinutes()), ss: digit(setdate.getSeconds()) }, format);
  } else {
    //将日期转换成时间戳
    re = getRegExp('(\d{4})(?:\D?(\d{1,2})(?:\D?(\d{1,2}))?[^\d\s]?)?(?:\s+(\d{1,2})\D?(\d{1,2})\D?(\d{1,2}))?').exec(date);
    return getDate(re[1], (re[2] || 1) - 1, re[3] || 1, re[4] || 0, re[5] || 0, re[6] || 0).getTime() / 1000;
  }
}

module.exports = {
  checkIDCard,
  checkPhone,
  checkName: checkName,
  checkMobile: checkMobile,
  checkNumber: checkNumber,
  timeToDate: timeToDate,
  digit: digit,
  parse: parse
}