import React, {Component} from 'react';
import QXElement from './Components/QXElement';

window.$ = window.jQuery = require('jquery');
window.axios = require('axios');

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
