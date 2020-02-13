var time = {
    TimeToDate: function (date, format) {
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
            re = /(\d{4})(?:\D?(\d{1,2})(?:\D?(\d{1,2}))?[^\d\s]?)?(?:\s+(\d{1,2})\D?(\d{1,2})\D?(\d{1,2}))?/.exec(date);
            timeStr = Math.round(new Date(re[1], (re[2] || 1) - 1, re[3] || 1, re[4] || 0, re[5] || 0, re[6] || 0).getTime() / 1000);
            return timeStr;
        }

        function parse(ymdhms, format) {
            var regymdzz = "YYYY|MM|DD|hh|mm|ss|zz";
            return format.replace(new RegExp(regymdzz, "g"), function (str, index) {
                return str == "zz" ? "00" : digit(ymdhms[str]);
            });
        }

        function digit(num) {
            return num < 10 ? "0" + (num | 0) : num;
        }
    }
}

export default {
    TimeToDate: time.TimeToDate,
}