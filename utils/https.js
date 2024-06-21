/**
 * base.js封装域名
 * https.js封装GET和POST请求
 * ports.js封装用到的接口api
 */
const baseUrl = 'https://demo.test';

// GET请求封装api
export function _get({ url, data }) {
  // 为了增加用户体验，添加一个loading效果，不需要的可以注释
  // wx.showLoading({title: '加载中',mask:true});
  // promise封装wx.request请求
  return new Promise((resolved, rejected) => {
    const _obj = {
      url: url.includes('http') ? url : baseUrl + url,
      data,
      method: "GET",
      success: res => { res.statusCode === 200 ? resolved(res.data) : rejected(res.data) },
      fail: err => rejected(err),
      complete: () => {
        // wx.hideLoading()
      }
    }
    wx.request(_obj)
  });
};


// POST请求封装api
export function _post({ url, data }) {
  // 为了增加用户体验，添加一个loading效果，不需要的可以注释
  // wx.showLoading({title: '加载中',mask:true});
  // promise封装wx.request请求
  return new Promise((resolved, rejected) => {
    const _obj = {
      url: url.includes('http') ? url : baseUrl + url,
      data,
      method: "POST",
      success: res => { res.statusCode === 200 ? resolved(res.data) : rejected(res.data) },
      fail: err => rejected(err),
      complete: () => {
        // wx.hideLoading()
      }
    }
    wx.request(_obj)
  });
};

// POST请求封装api
export function _upload({ url, filePath, name='file', formData={} }) {
  // 为了增加用户体验，添加一个loading效果，不需要的可以注释
  wx.showLoading({title: '加载中',mask:true});
  // promise封装wx.uploadFile请求
  return new Promise((resolved, rejected) => {
    const _obj = {
      url: url.includes('http') ? url : baseUrl + url,
      filePath,
      name,
      formData,
      success: res => { res.statusCode === 200 ? resolved(res.data) : rejected(res.data) },
      fail: err => rejected(err),
      complete: () => {
        wx.hideLoading()
      }
    }
    wx.uploadFile(_obj)
  });
};

module.exports = {
  baseUrl: baseUrl,
  _get,
  _post,
  _upload,
}