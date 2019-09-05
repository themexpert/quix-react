import React, {Component} from 'react';
import {getElement} from '../Services/QXElementService';

const jQuery = require('jquery');
const $ = jQuery;

export default class QXElement extends Component {
  state = {style: '', script: '', html: ''};

  _renderHTML = (nodes, whatever, default_) => {
    if (nodes.length === 0) {
      if (default_ === 'frontend') {
        console.log('front-end');
        return null;
      }
      return default_;
    }
    return nodes
        .map(node => {
          const tpl = getElement(node.slug);
          if (tpl === null || !tpl.html) {
            return default_;
          }

          const data = {$, jQuery, node, renderer: {render: this._renderHTML}};
          return tpl.html.render(data);
        })
        .join('')
        .replace(/&gt;/g, '>')
        .replace(/&lt;/g, '<');
  };

  _renderJS = (nodes, whatever, default_) => {
    if (nodes.length === 0) {
      if (default_ === 'frontend') {
        console.log('front-end');
        return null;
      }
      return default_;
    }
    return nodes.map(node => {
      const tpl = getElement(node.slug);
      if (tpl === null || !tpl.script) {
        console.log(default_);
        return null;
      }

      const data = {$, jQuery, node, renderer: {render: this._renderJS}};
      return tpl.script.render(data);
    }).join('');
  };

  _renderCSS = (nodes, whatever, default_) => {
    if (nodes.length === 0) {
      if (default_ === 'frontend') {
        console.log('front-end');
        return null;
      }
      return default_;
    }
    return nodes.map(node => {
      const tpl = getElement(node.slug);
      if (tpl === null || !tpl.style) {
        console.log(default_);
        return null;
      }

      const data = {$, jQuery, node, renderer: {render: this._renderCSS}};
      return tpl.style.render(data);
    }).join('');
  };

  render() {
    const __html = this._renderHTML(this.props.content, null, '');
    const css = this._renderCSS(this.props.content, null, '');
    const js = this._renderJS(this.props.content, null, '');
    eval(css);
    eval(js);
    return (<div dangerouslySetInnerHTML={{
      __html,
    }}/>);
  }
}
