import React, {Component} from 'react';
import QXElement from './Components/QXElement';

window._ = require('lodash');
window.$ = window.jQuery = require('jquery');
window.axios = require('axios');
for (const style of ['qxbs', 'qxkit', 'quix', 'qxi']) {
  const css = `http://try.getquix.net/libraries/quix/assets/css/${style}.css?ver=2.5.6.1`;
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = css;
  document.head.appendChild(link);
}
for (const js_file of ['qxkit']) {
  const js = `http://try.getquix.net/libraries/quix/assets/js/${js_file}.js?ver=2.5.6.1`;
  const script = document.createElement('script');
  script.src = js;
  document.head.appendChild(script);
}

require('./Helpers/Assets');

export default class QXViewer extends Component {

  state = {page: []};

  componentDidMount() {
    this.load()
        .then(page => {
          this.setState({page});
        });
  }

  load = () => {
    return new Promise((resolve, reject) => {
      fetch('http://try.getquix.net/index.php?option=com_quix&view=page&id=811&format=json&api=true')
          .then(res => res.json())
          .then(res => resolve(res))
          .catch(err => reject(err));
    });
  };

  render() {
    return (
        <QXElement content={this.state.page}/>
    );
  }
}
