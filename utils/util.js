const formatTime = (date, type = "normal") => {
  const year = date.getFullYear();
  const month = date.getMonth() + 1;
  const day = date.getDate();
  const hour = date.getHours();
  const minute = date.getMinutes();
  const second = date.getSeconds();
  if (type == "normal") {
    return `${[year, month, day].map(formatNumber).join("-")} ${[
      hour,
      minute,
      second,
    ]
      .map(formatNumber)
      .join(":")}`;
  }
  if (type == "yearMonth") {
    return `${[year, month].map(formatNumber).join("-")} `;
  }
  if (type == "day") {
    return formatNumber(day);
  }
};

const formatNumber = (n) => {
  n = n.toString();
  return n[1] ? n : `0${n}`;
};

function numberToTraditionalChinese(number) {
  const chineseNumberMap = {
    0: "0",
    1: "1",
    2: "2",
    3: "3",
    4: "4",
    5: "5",
    6: "6",
    7: "7",
    8: "8",
    9: "9",
  };

  const chineseUnitMap = {
    1: "",
    10: "10",
    100: "100",
    1000: "1000",
    10000: "10000",
    100000000: "100000000",
  };

  const numberString = number.toString();
  let result = "";

  for (let i = 0; i < numberString.length; i++) {
    const digit = parseInt(numberString[i]);
    const digitPlace = numberString.length - i - 1;

    if (digit === 0) {
      continue;
    }

    const chineseDigit = chineseNumberMap[digit.toString()];
    const chineseUnit = chineseUnitMap[Math.pow(10, digitPlace).toString()];
    result += chineseDigit + chineseUnit;
  }

  return result;
}

// 获取当前年月日（YYYY-mm-dd）格式
export function getNowFormatDate(type) {
  let date = new Date(),
    year = date.getFullYear(), //获取完整的年份(4位)
    month = date.getMonth() + 1, //获取当前月份(0-11,0代表1月)
    day = date.getDate(), // 获取当前日(1-31)
    hours = date.getHours(),
    minutes = date.getMinutes(),
    seconds = date.getSeconds();
  var s1 = "-"; //定义年月日分隔符-
  var s2 = ":"; //定义时分秒分隔符:
  if (month < 10) month = `0${month}`; // 如果月份是个位数，在前面补0
  if (day < 10) day = `0${day}`; // 如果日是个位数，在前面补0
  if (day < 10) day = `0${day}`; // 如果日是个位数，在前面补0
  if (type === "YYYY-mm-dd") {
    return `${year}-${month}-${day}`;
  } else {
    //拼接年月日,时分秒
    var currDate = year + s1 + month + s1 + day + " " + hours + s2 + minutes;
    return currDate;
  }
}

// 直接输出倒计时封装
export function dayTime(bb) {
  var bb = bb
  var day = parseInt(bb / 86400);
  var time = parseInt((bb - (day * 86400)) / 3600);
  var min = parseInt((bb - (time * 3600 + day * 86400)) / 60)
  var sinTime = time * 3600 + min * 60 + day * 86400
  var sinTimeb;
  var sin1 = parseInt((bb - sinTime))
  var thisTime = addEge(day) + "天" + addEge(time) + ":" + addEge(min) + ":" + addEge(sin1);
  bb <= 0 ? thisTime = "0天00:00:00" : thisTime
  return thisTime
}

// 输出数组倒计时封装
export function dayTimeArr(bb) {
  var bb = bb
  var day = parseInt(bb / 86400);
  var time = parseInt((bb - (day * 86400)) / 3600);
  var min = parseInt((bb - (time * 3600 + day * 86400)) / 60)
  var sinTime = time * 3600 + min * 60 + day * 86400
  var sinTimeb;
  var sin1 = parseInt((bb - sinTime))
  var timeArr = [addEge(day), addEge(time), addEge(min), addEge(sin1)];
  if (bb <= 0) {
    timeArr = ["0", "00", "00", "00"];
  }
  return timeArr
}

function addEge(a) {
  return a < 10 ? a = "0" + a : a = a
}

function interval(startTime, _this, index) { //到期时间戳
  interval = setInterval(function () {
    var insertTime = _this.data.insertTime;
    // 获取现在的时间
    var nowTime = new Date();
    var nowTime = Date.parse(nowTime); //当前时间戳
    var differ_time = 30 * 60 * 1000 - (nowTime - startTime); //时间差：
    if (differ_time >= 0) {
      var differ_day = Math.floor(differ_time / (3600 * 24 * 1e3)); //相差天数
      var differ_hour = Math.floor(differ_time % (3600 * 1e3 * 24) / (1e3 * 60 * 60)); //相差小时
      var differ_minute = Math.floor(differ_time % (3600 * 1e3) / (1000 * 60)); //相差分钟
      var s = Math.floor(differ_time % (3600 * 1e3) % (1000 * 60) / 1000);
      if (differ_day.toString().length < 2) {
        differ_day = "0" + differ_day;
      }
      var str = (differ_day > 0 ? differ_day + '天' : '') + (differ_hour > 0 ? addEge(differ_hour) + ':' : '') + addEge(differ_minute) + ':' + addEge(s);

      insertTime[index] = str;
      _this.setData({
        insertTime: insertTime
      });
    } else { // 当车险到期时，不再进行倒计时
      console.log("不进行倒计时");
      insertTime[index] = "00:00:00";
      _this.setData({
        insertTime: insertTime
      });
      clearInterval(interval);
    }
  }, 1000);

}

module.exports = {
  getNowFormatDate,
  formatTime,
  interval,
  numberToTraditionalChinese,
};