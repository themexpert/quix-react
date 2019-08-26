import React, {Component} from 'react';
import {getElement} from '../Services/QXElementService';

const jQuery = require('jquery');
const $ = jQuery;

export default class QXElement extends Component {
  _render(nodes, whatever, default_) {
    console.log(nodes, default_);
    return nodes.map(node => {
      const tpl = getElement(node.slug);
      if (tpl === null) {
        return default_;
      }

      const data = {_render: this._render, $, jQuery, node, renderer: true};

      return tpl.html.render(data) + tpl.style.render(data);
    }).join('');
  }

  render() {
    return (<div dangerouslySetInnerHTML={{
      __html: this._render(this.props.content, null, 'Hello World')
                  .replace(/&gt;/g, '>')
                  .replace(/&lt;/g, '<'),
    }}/>);
  }
}
