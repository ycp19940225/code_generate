/**
 * 判断是否为空
 * @param parm
 * @returns {boolean|arg is Array<any>}
 */
function empty(parm) {
    // console.log( parm == "undefined" , parm === null , parm === '', parm === 0 , parm === {} , parm === [], (Array.isArray(parm) && parm.length === 0), parm === false , parm === -1);
    return typeof parm == "undefined" || parm === null || parm === ''
        || parm === 0 || parm === {} || parm === []
        || (Array.isArray(parm) && parm.length === 0)
        || parm === false || parm === -1;
}

/**
 * 判断是否有重复项
 * @param arr
 * @returns {boolean}
 */
function isRepeat(arr) {
    var hash = {};
    for (var i in arr) {
        if (hash[arr[i]]) {
            return true;
        }
        hash[arr[i]] = true;
    }
    return false;
}

/**
 * 去除数组重复项
 * @param arr
 * @returns {[]}
 */
function array_unique(arr) {
    let hash = [];
    for (let i = 0; i < arr.length; i++) {
        if (hash.indexOf(arr[i]) === -1) {
            hash.push(arr[i]);
        }
    }
    return hash;
}

/**
 * 是否在数组中
 * @param search
 * @param array
 * @returns {boolean}
 */
function in_array(search, array) {
    for (var i in array) {
        if (array[i] == search) {
            return true;
        }
    }
    return false;
}

/**
 * 是否在数组中
 * @returns {boolean}
 */
function is_array(value) {
    if (typeof Array.isArray === "function") {
        return Array.isArray(value);
    }else{
        return Object.prototype.toString.call(value) === "[object Array]";
    }
}

/**
 * 删除数组的某项
 * @param array
 * @returns {*[] | *}
 */
Array.prototype.remove = function (val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) this.splice(i, 1);
    }
}

/**
 * 删除前后空格
 * @param str
 * @returns {string}
 */
function trim(str) {
    var re = '';
    if (str != '' && str != undefined) {
        re = str.replace(/(^\s*)|(\s*$)/g, "");
    }

    return re;
}
/**
 * 删除空格
 * @param str
 * @param is_global
 * @returns {string}
 */
function Trim(str, is_global)
{
    var result;
    result = str.replace(/(^\s+)|(\s+$)/g,"");
    if(is_global.toLowerCase()=="g")
    {
        result = result.replace(/\s/g,"");
    }
    return result;
}

/**
 * 获取字符串字节长度（中文占2个字节）
 * @param val
 * @returns {number}
 * @constructor
 */
function GetStringByteLength(val) {

    var Zhlength = 0;// 全角
    var Enlength = 0;// 半角
    for (var i = 0; i < val.length; i++) {
        if (val.substring(i, i + 1).match(/[^\x00-\xff]/ig) != null)
            Zhlength += 1;
        else
            Enlength += 1;
    }
    return (Zhlength * 2) + Enlength;
}

export {
    empty
}