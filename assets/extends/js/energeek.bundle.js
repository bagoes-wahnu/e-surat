var baseUrl = function (value = '') {
	let base_url = $('meta[name=base_url]').attr('content');

	if (value != '') {
		if (value.includes('?') == false) {
			value += '/';
		}
	}

	return base_url + value;
}

var preventLeaving = function () {
	window.onbeforeunload = function () {
		return "Are you sure you want to navigate away?";
	}
}

var roundTo = function (value, decimals) {
	if (decimals === undefined) {
		decimals = 0;
	}
	// return value = value.toFixed(decimals);
	return value = Number(Math.round(value + "e+" + decimals) + "e-" + decimals);
}

var helpCurrency = function (value = '', symbol = '', thousandSeparator = '.', centSeparator = ',', defaultCent = 'default', decimals = 2) {
	if (value == '' || value == null || value == 'null') {
		value = 0;
	}

	value = roundTo(value, decimals);
	value = String(value);
	value = value.replace(' ', '');


	var split_value = value.split(".");

	var centValue = '';

	if (defaultCent != '' && defaultCent != 'default') {
		centValue = centSeparator + defaultCent;
	}

	if (defaultCent == 'default') {
		if (split_value.length > 1) {
			if (split_value[1].length == 1) {
				centValue = centSeparator + split_value[1] + '0';
			} else {
				centValue = centSeparator + split_value[1];
			}
		}
	}

	return symbol + split_value[0].split("").reverse().reduce(function (acc, value, i, orig) {
		return value == "-" ? acc : value + (i && !(i % 3) ? thousandSeparator : "") + acc;
	}, "") + centValue;
}

var reformat_number = function (value = "") {
	if (value == "") {
		return 0;
	} else {
		return value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
	}
}

var protectForm = function (target = "", method = "", maxLength = "", selection = "") {
	$(target).off();
	$(target).keyup(function (event) {
		var replaced_value = '';
		/* skip for arrow keys */
		if (event.which >= 37 && event.which <= 40) return;

		/* format number */
		$(this).val(function (index, value) {
			replaced_value = value.replace(/\D/g, "");

			if (maxLength != '' && !isNaN(maxLength)) {
				replaced_value = replaced_value.substr(0, maxLength);
			}

			return replaced_value;
		});
	});
}

var protectNumber = function (target = "", maxLength = "", format = false) {
	$(target).off();
	$(target).keyup(function (event) {
		var replaced_value = '';
		/* skip for arrow keys */
		if (event.which >= 37 && event.which <= 40) return;

		/* format number */
		$(this).val(function (index, value) {
			replaced_value = value.replace(/\D/g, "");

			if (maxLength != '' && !isNaN(maxLength)) {
				replaced_value = replaced_value.substr(0, maxLength);
			}

			if (format == true) {
				return replaced_value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
			} else {
				return replaced_value;
			}
		});
	});
}

var protectString = function (target = "", maxStringLength = '') {
	$(target).off();
	$(target).keyup(function (event) {
		var pattern = /[^a-zA-Z0-9-/()., ]/g;
		var replaced_value = '';

		/* format number */
		$(this).val(function (index, value) {
			replaced_value = value.replace(pattern, '');
			if (maxStringLength != '' && !isNaN(maxStringLength)) {
				replaced_value = replaced_value.substr(0, maxStringLength);
			}
			return replaced_value;
			;
		});
	});
}

var helpDay = function (value, language = 'id', format = 'full') {
	var dayArray = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
	if (format != 'full') {
		dayArray = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];
	}

	if (language == 'eng') {
		dayArray = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
		if (format != 'full') {
			dayArray = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
		}
	}

	if (dayArray[value]) {
		return dayArray[value];
	} else {
		return 'Undefined';
	}
}

/**
* Function helpMonth
* Fungsi ini digunakan untuk mencari nama bulan dalam bahasa Indonesia
* @access public
* @param (int) var Nomor urut bulan yang dimulai dari angka 0 untuk bulan januari
* @return (string) {'Undefined'}
*/
var helpMonth = function (num, language = 'id', format = 'full') {
	var monthArray = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

	if (format != 'full') {
		monthArray = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
	}

	if (language == 'eng') {
		monthArray = ["January", "February", "March", "April", "May", "June", "Jule", "August", "September", "October", "November", "December"];
		if (format != 'full') {
			monthArray = ["Jan", "Feb", "Mar", "Apr", "May", "June", "Jule", "August", "September", "October", "November", "December"];
		}
	}

	if (monthArray[num]) {
		return monthArray[num];
	} else {
		return 'Undefined';
	}
}

var helpTime = function (value, format = 24, separator = ':', style = '2') {
	var mainTime = new Date(value);
	var hours = mainTime.getHours(),
		minutes = mainTime.getMinutes(),
		seconds = mainTime.getSeconds(),
		end = '';

	if (minutes < 10) {
		minutes = '0' + minutes;
	}

	if (seconds < 10) {
		seconds = '0' + seconds;
	}

	if (format == 12) {
		if (hours > 11) {
			end = ' PM';
		} else {
			end = ' AM';
		}

		if (hours > 12) {
			hours = (hours - 12);
		}
	}

	switch (style) {
		case Number('3'):
			return hours + separator + minutes + separator + seconds + end;
			break;
		default:
			return hours + separator + minutes + end;
			break;
	}
}

/**
* Function helpDateFormat
* Fungsi ini digunakan untuk melakukan konversi format tanggal
* @access public
* @param (value) var Tanggal yang akan dikonversi
* @param (string) mode Kode untuk model format yang baru
- se (short English)		: (Y-m-d) 2015-31-01
- si (short Indonesia)	: (d-m-Y) 31-01-2015
- me (medium English)	: (F d, Y) January 31, 2015
- mi (medium Indonesia)	: (d F Y) 31 Januari 2015
- le (long English)		: (l F d, Y) Sunday January 31, 2015
- li (long Indonesia)	: (l, d F Y) Senin, 31 Januari 2015
* @return (string) {'Undefined'}
*/
var helpDateFormat = function (value, format = 'se') {
	var help_date = new Date(value);
	var date = help_date.getDate(),
		month = help_date.getMonth(),
		year = help_date.getFullYear(),
		day = help_date.getDay(),
		text_month = (month + 1);

	if (date < 10) {
		date = '0' + date;
	}

	if (text_month < 10) {
		text_month = '0' + text_month;
	}

	switch (format) {
		case 'se':
			return year + '-' + text_month + '-' + date;
			break;
		case 'si':
			return date + '-' + text_month + '-' + year;
			break;
		case 'me':
			return helpMonth(month, 'eng') + ' ' + date + ', ' + year;
			break;
		case 'mi':
			return date + ' ' + helpMonth(month) + ' ' + year;
			break;
		case 'le':
			return helpDay(day, 'eng') + ' ' + helpMonth(month, 'eng') + ' ' + date + ', ' + year;
			break;
		case 'li':
			return helpDay(day) + ', ' + date + ' ' + helpMonth(month) + ' ' + year;
			break;
		case 'bi':
			return helpMonth(month) + ' ' + year;
			break;
		default:
			return 'Undefined';
			break;
	}
}

/**
* Function advanceDateFormat
* Fungsi ini digunakan untuk melakukan konversi format tanggal
* @access public
* @param (value) var Tanggal yang akan dikonversi
* @param (format) format tanggal baru
* @param (language) bahasa yang digunakan eng / id
* Available format
- d : The day of the month (from 01 to 31)
- D : A textual representation of a day (three letters)
- j : The day of the month without leading zeros (1 to 31)
- l (lowercase 'L') : A full textual representation of a day
- F : A full textual representation of a month (January through December)
- m : A numeric representation of a month (from 01 to 12)
- M : A short textual representation of a month (three letters)
- n : A numeric representation of a month, without leading zeros (1 to 12)
- y : A two digit representation of a year
- Y : A four digit representation of a year
- g : 12-hour format of an hour (1 to 12)
- G : 24-hour format of an hour (0 to 23)
- h : 12-hour format of an hour (01 to 12)
- H : 24-hour format of an hour (00 to 23)
- i : Minutes with leading zeros (00 to 59)
- s : Seconds, with leading zeros (00 to 59)
- a : Lowercase am or pm
- A : Uppercase AM or PM
* @return (string) {'Undefined'}
*/
var advanceDateFormat = function (value, format = 'Y-m-d', language = 'id') {
	result = '';
	var mainDate = new Date(value);

	var tempFormat = format.replace(/[^a-zA-Z]+/g, "");

	var d = mainDate.getDate(),
		D = helpDay(mainDate.getDay(), language, 'short'),
		j = d,
		l = helpDay(mainDate.getDay(), language, 'full'),
		F = helpMonth(mainDate.getMonth(), language, 'full'),
		n = (mainDate.getMonth() + 1),
		m = (mainDate.getMonth() + 1),
		M = helpMonth(mainDate.getMonth(), language, 'short'),
		y = mainDate.getFullYear(),
		y = String(y).substring(2),
		Y = mainDate.getFullYear(),
		g = mainDate.getHours(),
		G = mainDate.getHours(),
		h = mainDate.getHours(),
		H = mainDate.getHours(),
		a = mainDate.getHours(),
		A = mainDate.getHours(),
		i = mainDate.getMinutes(),
		s = mainDate.getSeconds();

	if (d < 10) {
		d = '0' + d;
	}

	if (m < 10) {
		m = '0' + m;
	}

	if (g > 12) {
		g = (g - 12);
	}

	if (h > 12) {
		h = (h - 12);

		if (h < 10) {
			h = '0' + h;
		}
	}

	a = (a > 12) ? 'pm' : 'am';
	A = (A > 12) ? 'PM' : 'AM';

	if (H < 10) {
		H = '0' + H;
	}

	if (i < 10) {
		i = '0' + i;
	}

	if (s < 10) {
		s = '0' + s;
	}

	result = format;

	/* reform ke alias */

	result = result.replace(/[a]+/g, 'PSK'); /* Pagi Siang Kecil */
	result = result.replace(/[A]+/g, 'PSB'); /* Pagi Siang Besar */
	result = result.replace(/[n]+/g, 'BAS'); /* BulanAngkaSingkat */
	result = result.replace(/[g]+/g, 'JSS'); /* Jam 12 Singkat */
	result = result.replace(/[G]+/g, 'JPS'); /* Jam 24 Singkat */
	result = result.replace(/[h]+/g, 'JSP'); /* Jam 12 Penuh */
	result = result.replace(/[H]+/g, 'JPP'); /* Jam 24 Penuh */
	result = result.replace(/[y]+/g, 'Tahun2');
	result = result.replace(/[Y]+/g, 'Tahun4');
	result = result.replace(/[l]+/g, 'HP'); /* HariPenuh */
	result = result.replace(/[F]+/g, 'BulanHurufPenuh');
	result = result.replace(/[M]+/g, 'BHS'); /* BulanHurufSingkat */
	result = result.replace(/[i]+/g, 'MenitPenuh');
	result = result.replace(/[m]+/g, 'BulanAngkaPenuh');
	result = result.replace(/[j]+/g, 'TanggalSingkat');
	result = result.replace(/[d]+/g, 'TanggalPenuh');
	result = result.replace(/[D]+/g, 'HariSingkat');
	result = result.replace(/[s]+/g, 'DetikPenuh');

	/* reform ke variabel */

	result = result.replace('PSK', a);
	result = result.replace('PSB', A);
	result = result.replace('DetikPenuh', s);
	result = result.replace('MenitPenuh', i);
	result = result.replace('JSS', g);
	result = result.replace('JPS', G);
	result = result.replace('JSP', h);
	result = result.replace('JPP', H);
	result = result.replace('HP', l);
	result = result.replace('Tahun2', y);
	result = result.replace('Tahun4', Y);
	result = result.replace('BulanAngkaPenuh', m);
	result = result.replace('BAS', n);
	result = result.replace('BHS', M);
	result = result.replace('BulanHurufPenuh', F);
	result = result.replace('TanggalSingkat', j);
	result = result.replace('TanggalPenuh', d);
	result = result.replace('HariSingkat', D);

	return result;
}

var getDisplayDate = function (value, withHour = false, format = 'd-m-Y', textToday = true, language = 'id') {
	let year = advanceDateFormat(value, 'Y');
	let month = advanceDateFormat(value, 'n');
	let day = advanceDateFormat(value, 'd');
	let result = '';

	/*  start : Today */
	let arrToday = { 'id': 'Hari ini', 'eng': 'Today' };
	let teksHariIni = 'Undefined';

	if (arrToday[language]) {
		teksHariIni = arrToday[language];
	}
	/* ens : Today */

	/* start : Yesterday */
	let arrYesterday = { 'id': 'Kemarin', 'eng': 'Yesterday' };
	let teksKemarin = 'Undefined';

	if (arrYesterday[language]) {
		teksKemarin = arrYesterday[language];
	}
	/* end : Yesterday */

	today = new Date();
	today.setHours(0);
	today.setMinutes(0);
	today.setSeconds(0);
	today.setMilliseconds(0);

	compDate = new Date(value); /* month - 1 because January == 0 */
	diff = today.getTime() - compDate.getTime(); /* get the difference between today(at 00:00:00) and the date */

	if (advanceDateFormat(today, 'Y-m-d') == advanceDateFormat(compDate, 'Y-m-d')) {
		if (textToday == false) {
			result = advanceDateFormat(value, 'H:i');
		} else {
			result = teksHariIni;
		}
	} else if (diff <= (24 * 60 * 60 * 1000) && diff > 0) {
		result = teksKemarin;
	} else {
		if (format != '') {
			result = advanceDateFormat(compDate, format); /* or format it what ever way you want */
		} else {
			result = 'Undefined date format';
		}
	}

	if (withHour == true) {
		result += ", " + advanceDateFormat(value, 'H:i');
	}

	return result;
}

var formatBytes = function (bytes, decimals) {
	if (bytes == 0) return '0 Bytes';
	var k = 1024,
		dm = decimals <= 0 ? 0 : decimals || 2,
		sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
		i = Math.floor(Math.log(bytes) / Math.log(k));
	return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
}

var helpEmpty = function (value, replaceWith = '') {
	if (value == '' || value === null || typeof value === 'undefined') {
		value = replaceWith;
	}

	return value;
}

var getJsonFromUrl = function (url) {
	if (!url) url = location.search;
	var query = url.substr(1);
	var result = {};
	query.split("&").forEach(function (part) {
		var item = part.split("=");
		result[item[0]] = decodeURIComponent(item[1]);
	});
	return result;
}

var helpDelay = function (callback, ms) {
	var timer = 0;
	return function () {
		var context = this, args = arguments;
		clearTimeout(timer);
		timer = setTimeout(function () {
			callback.apply(context, args);
		}, ms || 0);
	};
}

var protectQuantity = function (target = "", maxValue = null, format = false) {
	$(target).off();
	$(target).keyup(function (event) {
		// event.preventDefault();
		var replaced_value = '';
		/* skip for arrow keys */
		if (event.which >= 37 && event.which <= 40) return;

		/* format number */
		$(this).val(function (index, value) {
			replaced_value = value.replace(/\D/g, "");

			console.log('maxValue : ' + maxValue);

			if (maxValue != null && !isNaN(maxValue)) {
				maxValue = parseInt(maxValue);

				var maxLoop = parseInt(replaced_value.toString().length);

				var replace = true;
				for (var i = 0; i < maxLoop; i++) {
					if (parseInt(replaced_value) <= maxValue) {
						replace = false;
					}

					if (replace == true) {
						replaced_value = replaced_value.substr(0, (replaced_value.toString().length - 1));
					}
				}
			}

			if (format == true) {
				return replaced_value.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1.");
			} else {
				return replaced_value;
			}
		});
	});
}

var roundQuantity = function (value = '', symbol = '', thousandSeparator = '.') {
	if (value == '' || value == null || value == 'null') {
		value = 0;
	}

	value = String(value);
	value = value.replace(' ', '');

	var split_value = value.split(".");

	return symbol + split_value[0].split("").reverse().reduce(function (acc, value, i, orig) {
		return value == "-" ? acc : value + (i && !(i % 3) ? thousandSeparator : "") + acc;
	}, "");
}