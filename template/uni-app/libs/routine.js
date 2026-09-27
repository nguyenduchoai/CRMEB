// +----------------------------------------------------------------------
// | CRMEB [ CRMEB tiếp sức cho nhà phát triển, hỗ trợ doanh nghiệp phát triển ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2024 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB không phải là phần mềm tự do, không được phép gỡ bỏ bản quyền liên quan đến CRMEB khi chưa được cho phép
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------

import store from '../store';
import {
	checkLogin
} from './login';
import {
	login,
	routineLogin,
	silenceAuth
} from '../api/public';
import Cache from '../utils/cache';
import {
	STATE_R_KEY,
	USER_INFO,
	EXPIRES_TIME,
	LOGIN_STATUS
} from './../config/cache';
import {
	mapGetters
} from "vuex";
class Routine {

	constructor() {
		this.scopeUserInfo = 'scope.userInfo';
	}

	async getUserCode() {
		let isAuth = await this.isAuth(),
			code = '';
		if (isAuth)
			code = await this.getCode();
		return code;
	}
	// Ủy quyền ngầm Mini Program
	// silenceAuth(code) {
	// 	const app = getApp();
	// 	let that = this;
	// 	let spread = app.globalData.spid ? app.globalData.spid : '';
	// 	return new Promise((resolve, reject) => {
	// 		silenceAuth({
	// 				code: code,
	// 				spread_spid: spread,
	// 				spread_code: app.globalData.code
	// 			})
	// 			.then(res => {
	// 				if (res.data && res.data.token !== undefined) {
	// 					uni.hideLoading();
	// 					let time = res.data.expires_time - Math.round(new Date() / 1000);
	// 					store.commit('LOGIN', {
	// 						token: res.data.token,
	// 						time: time
	// 					});
	// 					store.commit('SETUID', res.data.userInfo.uid);
	// 					store.commit('UPDATE_USERINFO', res.data.userInfo);
	// 					resolve(res)
	// 				} else {
	// 					reject()
	// 					uni.navigateTo({
	// 						url: '/pages/users/wechat_login/index'
	// 					})
	// 				}
	// 			})
	// 			.catch(err => {
	// 				reject(err)
	// 			});
	// 	})
	// }
	/**
	 * Lấy thông tin người dùng
	 */
	getUserInfo() {
		let that = this,
			code = this.getUserCode();
		return new Promise((resolve, reject) => {
			uni.getUserInfo({
				lang: 'zh_CN',
				success(user) {
					if (code) user.code = code;
					resolve({
						userInfo: user,
						islogin: false
					});
				},
				fail(res) {
					reject(res);
				}
			})
		})
	}

	/**
	 * Lấy thông tin người dùng phiên bản Mini Program mới, chính thức áp dụng từ 13/4/2021
	 */
	getUserProfile(code) {
		return new Promise((resolve, reject) => {
			uni.getUserProfile({
				lang: 'zh_CN',
				desc: 'Dùng để hoàn thiện hồ sơ thành viên', // Khai báo mục đích sử dụng sau khi lấy thông tin cá nhân người dùng, nội dung sẽ hiển thị trong popup, vui lòng điền cẩn thận
				success(user) {
					if (code) user.code = code;
					resolve({
						userInfo: user,
						islogin: false
					});
				},
				fail(res) {
					reject(res);
				}
			})
		})
	}

	/**
	 * Lấy thông tin người dùng
	 */
	authorize() {
		let that = this;
		return new Promise((resolve, reject) => {
			if (checkLogin())
				return resolve({
					userInfo: Cache.get(USER_INFO, true),
					islogin: true,
				});
			uni.authorize({
				scope: that.scopeUserInfo,
				success() {
					resolve({
						islogin: false
					});
				},
				fail(res) {
					reject(res);
				}
			})
		})
	}

	async getCode() {
		let provider = await this.getProvider();
		return new Promise((resolve, reject) => {
			// if(Cache.has(STATE_R_KEY)){
			// 	return resolve(Cache.get(STATE_R_KEY));
			// }
			uni.login({
				provider: provider,
				success(res) {
					if (res.code) Cache.set(STATE_R_KEY, res.code, 10800);
					return resolve(res.code);
				},
				fail() {
					return reject(null);
				}
			})
		})
	}

	/**
	 * Lấy nhà cung cấp dịch vụ
	 */
	getProvider() {
		return new Promise((resolve, reject) => {
			uni.getProvider({
				service: 'oauth',
				success(res) {
					resolve(res.provider);
				},
				fail() {
					resolve(false);
				}
			});
		});
	}

	/**
	 * Đã cấp phép
	 */
	isAuth() {
		let that = this;
		return new Promise((resolve, reject) => {
			uni.getSetting({
				success(res) {
					if (!res.authSetting[that.scopeUserInfo]) {
						resolve(true)
					} else {
						resolve(true);
					}
				},
				fail() {
					resolve(false);
				}
			});
		});
	}
	/**
	 * So sánh thông tin phiên bản Mini Program
	 * @param v1 Phiên bản hiện tại
	 * @param v2 Phiên bản đem so sánh 
	 * @return boolen
	 * 
	 */
	compareVersion(v1, v2) {
		v1 = v1.split('.')
		v2 = v2.split('.')
		const len = Math.max(v1.length, v2.length)

		while (v1.length < len) {
			v1.push('0')
		}
		while (v2.length < len) {
			v2.push('0')
		}

		for (let i = 0; i < len; i++) {
			const num1 = parseInt(v1[i])
			const num2 = parseInt(v2[i])

			if (num1 > num2) {
				return 1
			} else if (num1 < num2) {
				return -1
			}
		}

		return 0
	}
	authUserInfo(data) {
		return new Promise((resolve, reject) => {
			routineLogin(data).then(res => {
				if (res.data.key !== undefined && res.data.key) {} else {
					store.commit('UPDATE_USERINFO', res.data.userInfo);
					store.commit('SETUID', res.data.userInfo.uid);
					Cache.set(USER_INFO, res.data.userInfo);
				}
				return resolve(res);
			}).catch(res => {
				return reject(res);
			})
		})
	}
}

export default new Routine();
