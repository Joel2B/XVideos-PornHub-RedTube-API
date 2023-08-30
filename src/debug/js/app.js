// TODO: use a framework

import Api from './api.js';
import config from '../config.js';
import SearchControl from './search-control.js';
import Content from './content.js';
import Storage from './storage.js';
import Player from './player.js';
import Sections from './sections.js';

class App {
  constructor() {
    this.config = config;

    this.storage = new Storage(this);
    this.searchControl = new SearchControl(this);
    this.sections = new Sections(this);
    this.api = new Api(this);
    this.player = new Player(this);
    this.content = new Content(this);
  }
}

const app = new App();
