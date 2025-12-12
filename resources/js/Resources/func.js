export const func = {
    /**
     * Формат телефона 8 (000)-000-0000
     * @param val
     * @returns {string}
     * @constructor
     */
    MaskPhone: (val) => {
       /* if (val === undefined || val === null) return '';
        if (val.length === 1) {
            if (val === '+') val = '8';
            if (val !== '8') val = '';
        }
        if (val.length > 1 && val.length < 12) {
            if (val.slice(-1).match(/\d+/g) === null)
                val = val.substring(0, val.length - 1);
        }
        if (val.length >= 12) val = val.substring(0, val.length - 1);
        return val;
        */
        if (val.length === 1 && (val === '+' || val === '8')) return '8 ('
        val = val.replace(/^8|\D/g, ''). //val.replace(/^\+7|\D/g, '').
            replace(/^(\d{1,3})(\d{1,3})?(\d{1,2})?(\d{1,2})?.*/, (m, g1, g2, g3, g4) => `8 (${g1}` + (g2 ? `)-${g2}` : '') + (g3 ? `-${g3}` : '') + (g4 ? `-${g4}` : ''))
        return val
    },
    MaskEmail: (val) => {
        if (val === undefined || val === null) return '';

        let last = val.slice(-1);
        if (last.match(/\d+/g) === null && last.match(/[a-z-_.@]/i) === null) {
            val = val.substring(0, val.length - 1);
        }
        return val;
    },
    /**
     * Формат логина - латиница и цифры
     * @param val
     * @returns {string}
     * @constructor
     */
    MaskLogin: (val) => {
        if (val === undefined || val === null) return '';

        let last = val.slice(-1);
        if (last.match(/\d+/g) === null && last.match(/[a-z]/i) === null) {
            val = val.substring(0, val.length - 1);
        }
        return val;
    },
    MaskSlug: (val) => {
        if (val === undefined || val === null) return '';

        let last = val.slice(-1);
        if (last.match(/\d+/g) === null && last.match(/[a-z\-_]/g) === null) {
            val = val.substring(0, val.length - 1);
        }
        return val;
    },
    MaskInteger: (val, max = 999) => {
        if (val === undefined || val === null) return 0;

        let last = val.slice(-1);
        if (last.match(/\d+/g) === null || val.length > max) {
            val = val.substring(0, val.length - 1);
        }
        return val;
    },
    MaskCount: (val, min = 1, max = null) => {
        if (val === undefined || val === null || val === '') return '';

        let last = val.slice(-1);
        if (last.match(/\d+/g) === null) {
            val = val.substring(0, val.length - 1);
        }
        if (val < min) val = min;
        if (max !== null && val > max) val = max;
        return val;
    },
    MaskFloat: (val, rounded = 4) => {
        if (val === undefined || val === null || val === '') return '';
        let last = val.slice(-1);
        let pre_val = val.substring(0, val.length - 1);
        if (last === ',') last = '.'
        //Если тек.символ разделитель и он уже есть, то отмена
        if (last === '.' && pre_val.includes('.')) return pre_val;

        //Если символ не число и не точка, то отмена
        if (last.match(/\d+/g) === null && last.match(/\./g) === null) {
            return pre_val;
            //val = val.substring(0, val.length - 1);
        }
        return pre_val + last
    },
    fullName: (val) => {
        if (val === undefined || val === null) return '';
        let result = val.surname + ' ' + val.firstname + ' ' + val.secondname;
        return result === '' ? 'Не определено' : result;
    },
    price: (val, currency = '₽') => {
        if (val === null || val === '' || val === 0 || val === undefined) return '0 ' + currency;
        val = Math.round(Math.round(val * 100)) / 100;
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") + ' ' + currency;
    },
    phone: (val) => {
        if (val === null || val === '' || val === 0 || val === undefined) return '';
        //return val
        return val.substr(0, 1) + ' ' + val.substr(1, 3) + '-' + val.substr(4, 3) + '-' + val.substr(7, 4);

        //return mb_substr($value, 0, 1) . ' ' . mb_substr($value, 1, 3) . '-' . mb_substr($value, 6, 3) . '-' . mb_substr($value, 7, 4);
    },
    experience: (val) => {
        if (val === null || val === 0 || val === undefined) return '';
        let year = new Date().getFullYear() - val;
        let div = year % 10;
        if (year === 0) return 'менее 1 года';
        if (year > 10 && year < 20) return year + ' лет';
        if (div === 1) return year + ' год';

        if (year > 1 && year < 5) return year + ' года';

        return year + ' лет';
    },
    date: (val, year = true) => {
        if (val === undefined || val === null) return '';
        const _date_ = new Date(val);
        let month = _date_.getMonth() + 1;
        if (month < 10) month = '0' + month;
        let day = _date_.getDate();
        if (day < 10) day = '0' + day;
        if (year) return  _date_.getFullYear() + '-' + month + '-' + day;
        return  month + '-' + day;
    },
    datetime: (val) => {
        if (val === undefined || val === null) return null;
        const _date_ = new Date(val);
        let month = _date_.getMonth() + 1;
        if (month < 10) month = '0' + month;
        let day = _date_.getDate();
        if (day < 10) day = '0' + day;
        let days = _date_.getFullYear() + '-' + month + '-' + day;
        return days + ' ' + _date_.getHours() + ':' + _date_.getMinutes() + ':' + _date_.getSeconds();
    },
    shortdate: (val) => {
        if (val === undefined || val === null) return null;
        const _date_ = new Date(val);
        let month = _date_.getMonth() + 1;
        if (month < 10) month = '0' + month;
        let day = _date_.getDate();
        if (day < 10) day = '0' + day;
        const monthName = _date_.toLocaleString('default', { month: 'short' })
        let days =  month + '-' + day;
        return _date_.getHours() + ':' + _date_.getMinutes() + ' ' + day + ' ' + monthName;
    },

    displayedInfo: (model = null, image = null, icon = null) => {
        if (model === null) return {
            name: null,
            slug: null,
            meta: {
                h1: null,
                title: null,
                description: null,
            },
            breadcrumb: {
                photo_id: null,
                caption: null,
                description: null,
            },
            awesome: null,
            template: null,
            text: null,
            image: null,
            icon: null,
            clear_image: false,
            clear_icon: false,
        }
        return {
            name: model.name,
            slug: model.slug,
            meta: model.meta,
            breadcrumb: model.breadcrumb,
            awesome: model.awesome,
            template: model.template,
            text: model.text,
            image: image,
            icon: icon,
            clear_image: false,
            clear_icon: false,
        };
    },
    displayedImage: (model) => {

    }
}
