'use strict';

document.addEventListener('DOMContentLoaded', function () {

	// 全角変換
	document.querySelectorAll('[data-zenkaku]').forEach(function (el) {
		el.addEventListener('input', function () {
			this.value = this.value.replace(/[!-~]/g, function (s) {
				return String.fromCharCode(s.charCodeAt(0) + 0xFEE0);
			});
		});
	});

	// フリガナ補助(ひらがな→カタカナ変換、カタカナ・長音以外は削除)
	document.querySelectorAll('[data-kana]').forEach(function (el) {
		el.addEventListener('blur', function () {
			this.value = this.value
				.replace(/[ぁ-ん]/g, function (s) {
					return String.fromCharCode(s.charCodeAt(0) + 0x60);
				})
				.replace(/[^ァ-ヶー]/g, '');
		});
	});

	// 半角数字のみ(郵便番号など)
	document.querySelectorAll('[data-number]').forEach(function (el) {
		el.addEventListener('input', function () {
			this.value = this.value
				.replace(/[０-９]/g, function (s) {
					return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
				})
				.replace(/[^0-9]/g, '');
		});
	});

	// 半角数字とハイフンのみ(電話番号)
	document.querySelectorAll('[data-tel]').forEach(function (el) {
		el.addEventListener('input', function () {
			this.value = this.value
				.replace(/[０-９]/g, function (s) {
					return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
				})
				.replace(/[‐－―ー]/g, '-') // 全角・類似ハイフンを半角に統一
				.replace(/[^0-9\-]/g, '');
		});
	});

});