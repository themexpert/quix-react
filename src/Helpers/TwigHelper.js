import Twig from 'twig';
import classNames from 'classnames';
import {log} from 'util';
import Cookies from 'js-cookie';

const quix = {url: ''};
const QUIX_URL = '';
const FILE_MANAGER_ROOT_URL = '';

/**
 * Wrap the value with HTML tag
 */
Twig.extendFilter('wrap', function(value, params) {
  let tag = params[0];

  return `<${tag}> ${value} </${tag}>`;
});

/**
 * Json decode filter
 */
Twig.extendFilter('json_decode', function(value) {
  return !_.isEmpty(value) ? JSON.parse(value) : '';
});

/**
 * Remove lines filter
 */
Twig.extendFilter('removeLines', function(value) {
  return value.replace(/(\r\n|\n|\r)/gm, '');
});

/**
 * Link Filter
 */
Twig.extendFilter('link', function(value, params) {
  let url = params[0].url,
      target = params[0].target,
      nofollow = params[0].nofollow;
  let klass = '';
  let attr = '';

  if (params[1]) {
    klass = 'class="' + params[1] + '"';
  }

  if (params[2]) {
    attr = ' ' + params[2];
  }

  target = (target) ? 'target="_blank"' : '';
  nofollow = (nofollow) ? 'rel="nofollow"' : '';

  if (url) {
    return `<a ${klass} href="${url}" ${target} ${nofollow}${attr}> ${value} </a>`;
  }
  else {
    return `${value}`;
  }
});

/**
 * startTag function : return startTag
 */
Twig.extendFunction('startTag', function(tag, attr) {
  return `<${tag} ${attr}>`;
});

/**
 * loadSvg function : load svg or icon
 */
Twig.extendFunction('loadSvg', function(svg) {
  if (_.startsWith(svg, '<svg') || _.startsWith(svg, '<?xml')) {
    return svg;
  }
  else {
    return `<i class="${svg}"></i>`;
  }
});

/**
 * prepareWidthValue function : return processed width with unit
 */
Twig.extendFunction('prepareWidthValue', function(width) {
  if (width.unit) {
    return width;
  }
  else {
    return {
      'unit': '%',
      'value': width,
    };
  }

  return width;

});

/**
 * prepareResponsiveValue function : return processed responsive value.
 */
Twig.extendFunction('prepareResponsiveValue', function(responsive) {

  if (responsive.desktop !== undefined) {
    responsive.unit = _.isEmpty(responsive.unit) ? 'px' : responsive.unit;
    return responsive;
  }
  else {
    let newResponsive = responsive.value;
    newResponsive.unit = responsive.unit === undefined ? 'px' : responsive.unit;

    return newResponsive;
  }

});

/**
 * Image function : return image html
 */
Twig.extendFunction('prepareSvgSizeValue', function(size = '') {

  if (typeof (size) == 'object') {
    return size;
  }

  return {
    value: size,
    unit: 'px',
  };
});

/**
 * visibilityClass for grid
 */
Twig.extendFunction('visibilityClass', function(visibility) {
  return '';
});

/**
 * visibilityClass for grid
 */
Twig.extendFunction('visibilityClassNode', function(visibility) {
  return '';
});

/**
 * Image function : return image html
 */
Twig.extendFunction('image', function(src, alt, cls = '') {
  if (_.isEmpty(src)) {
    return `<span>No Image found!</span>`;
  }
  if (typeof (src) == 'object' && src.url != undefined) {
    src = src.url;
  }

  let img_src;
  if (_.startsWith(src, 'data:image') || _.startsWith(src, 'http')) {
    img_src = src;
  }
  else if (_.startsWith(src, 'libraries')) {
    img_src = quix.url + '/' + src;
  }
  else if (_.startsWith(src, 'images')) {
    img_src = quix.url + '/' + src;
  }
  else {
    img_src = FILE_MANAGER_ROOT_URL + src.replace(/^\//, '');
  }

  return `<img draggable="false" src="${img_src}" alt="${alt}" class="${cls}" />`;
});

/**
 * imageUrl
 */
Twig.extendFunction('imageUrl', function(src) {
  if (_.isEmpty(src)) {
    return '';
  }

  if (typeof (src) == 'object' && src.url != undefined) {
    src = src.url;
  }

  let img_src;
  if (_.startsWith(src, 'data:image') || _.startsWith(src, 'http')) {
    img_src = src;
  }
  else if (_.startsWith(src, 'libraries')) {
    img_src = quix.url + '/' + src;
  }
  else if (_.startsWith(src, 'images')) {
    img_src = quix.url + '/' + src;
  }
  else {
    img_src = FILE_MANAGER_ROOT_URL + src.replace(/^\//, '');
  }

  return img_src;

});

/**
 * Image function : return video html
 */
Twig.extendFunction('video', function(id, src, poster, attr = ' playsInline controls ') {
  let source = FILE_MANAGER_ROOT_URL + src;
  poster = FILE_MANAGER_ROOT_URL + poster;
  let type = 'video/' + src.split('.').pop();

  return `<video id="${id}" poster="${poster}"${attr}>
    <source type="${type}" src="${source}" />
    Your browser does not support the video tag.
  </video>`;
});

/**
 * Get Joomla Module
 */
Twig.extendFunction('getJoomlaModule', function(id, style) {
  let token = jQuery('#jform_token').attr('name');
  let tokenValue = jQuery('#jform_token').val();
  let url = `index.php?option=com_quix&task=api.getJoomlaModule&id=${id}&style=${style}&${token}=${tokenValue}`;
  return url;
  return new Promise(function(resolve, reject) {
    axios.get(url)
         .then(response => {
           var str = response.data;
           var data = str.replace(/name=|form/gi, function(x) {
             return 'x' + x;
           });
           resolve(data);
         });
  });
});

/**
 * Get Element ajax Call
 */
Twig.extendFunction('ElementApiCall', function(element, data) {
  let token = jQuery('#jform_token').attr('name');
  let dataEndoce = btoa(JSON.stringify(data));
  let url = `index.php?option=com_quix&task=ajax&builder=frontend&element=${element}&${token}=1&data=${dataEndoce}`;
  return url;
  return new Promise(function(resolve, reject) {
    axios.get(url)
         .then(response => {
           var response = response.data;
           if (response.success) {
             resolve(response.data);
           }
           else {
             resolve(resolve.messages);
           }
         });
  });
});

/**
 * Post Element ajax Call test
 */
Twig.extendFunction('ajaxPostQuix', function(element, data) {
  let token = jQuery('#jform_token').attr('name');
  let bodyFormData = new FormData();
  for (var key in data) {
    bodyFormData.set(key, data[key]);
  }

  let url = `index.php?option=com_quix&task=ajax&builder=frontend&element=${element}&${token}=1`;
  return url;
  return new Promise(function(resolve, reject) {
    axios.post(url, bodyFormData)
         .then(response => {
           var response = response.data;
           if (response.success) {
             resolve(response.data);
           }
           else {
             resolve(resolve.messages);
           }
         });
  });
});

/**
 * Get Joomla Module
 */
Twig.extendFunction('prepareContent', function(text, prepare) {

  if (!prepare) {
    return text;
  }

  let token = jQuery('#jform_token').attr('name');
  let url = `index.php?option=com_quix&task=api.prepareContent&${token}=1`;
  return url;
  var bodyFormData = new FormData();
  bodyFormData.set('content', text);

  return new Promise(function(resolve, reject) {
    axios({
      method: 'post',
      url: url,
      data: bodyFormData,
      config: {headers: {'Content-Type': 'multipart/form-data'}},
    })
        .then(response => {
          var str = response.data;
          var data = str.replace(/name=|form/gi, function(x) {
            return 'x' + x;
          });
          resolve(data);
        });
  });
});

/**
 * startsWith function : return boolean
 */
Twig.extendFunction('startsWith', function(str, subStr) {
  return _.startsWith(str, subStr);
});

/**
 * startsWith function : return boolean
 */
Twig.extendFunction('validateJoomlaCaptcha', function(value) {
  return true;
});

/*
 * startsWith function : return boolean
 */
Twig.extendFunction('captchaPublicKey', function() {
  return Cookies.get('captcha-public-key');
});

/**
 * classNames function : return concat class names
 */
Twig.extendFunction('classNames', function() {
  return classNames(...arguments).replace('_keys', '');
});

/**
 * getOpacity function : return background opacity
 */
Twig.extendFunction('getOpacity', function(background, type) {
  return background ? background.state[type].opacity : null;
});

/**
 * getWrapper
 */
Twig.extendFunction('wrapper', function(wrapper, tag, multipart = false, end = false) {
  return (end ? '</' + wrapper + '>' : '<' + wrapper + '>');
});
/**
 * formFooter
 */
Twig.extendFunction('formFooter', function(element, config) {
  return '';
});
/**
 * allfield
 */
Twig.extendFunction('allfield', function() {
  return '[]';
});

/**
 * fieldsGroup function : return fields group data
 */
Twig.extendFunction('fieldsGroup', function(fieldsGroup, index) {

  var data = fieldsGroup[index];

  var results = {};
  for (let i = 0; i < data.length; i++) {
    let d = data[i];
    results[d.name] = d;
  }

  return results;
});

/**
 * getQuixElementPath function : return quix element path
 */
Twig.extendFunction('getQuixElementPath', function() {
  return QUIX_URL + '/app/frontend/elements';
});

/**
 * raw ( mock ) function : return path
 */
Twig.extendFunction('raw', function(path, alt) {
  var altValue = alt ? alt : '';

  return `<img src="${QUIX_URL + path}" alt="${altValue}" />`;
});

/**
 * raw ( mock ) function : return path
 */
Twig.extendFunction('getFileContent', function(element, path, ext) {
  let token = jQuery('#jform_token').attr('name');
  let pathCode = btoa(path);
  let url = `index.php?option=com_quix&task=api.getFileContent&source=${element}&path=${pathCode}&ext=${ext}&${token}=1`;

  // cache to localStorage
  let responseCache = localStorage.getItem(url);
  if (responseCache) {
    return responseCache;
  }
  return url;

  return new Promise(function(resolve, reject) {
    axios.get(url)
         .then(response => {
           var response = response.data;
           if (response.success) {
             localStorage.setItem(url, response.data);
             resolve(response.data);
           }
           else {
             localStorage.setItem(url, response.messages);
             resolve(resolve.messages);
           }
         });
  });
});

/**
 * less than function : return boolean
 */
window.lessThan = function(number1, number2) {
  return number1 < number2;
};
Twig.extendFunction('lessThan', window.lessThan);

/**
 * greater than function : return boolean
 */
window.greaterThan = function(number1, number2) {
  return number1 > number2;
};
Twig.extendFunction('greaterThan', window.greaterThan);

/**
 * greater than function : return boolean
 */
window.greaterThanSign = function() {
  return `>`;
};
Twig.extendFunction('greaterThanSign', window.greaterThanSign);

Twig.extendFunction('field', function(field) {
  if (this.context.node[field] !== undefined) {
    return this.context.node[field];
  }
  if (field === 'html_tag') {
    return 'div';
  }
  return null;
});

global.twig = Twig.twig;

export default Twig.twig;