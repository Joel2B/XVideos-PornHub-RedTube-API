import { $, $$, emptyEl } from './utils/dom.js';
import { emptyObject } from './utils/utils.js';

class Player {
  constructor(app) {
    this.app = app;

    this.player = {
      hls: {
        el: null,
        instance: null,
      },
      mp4: {
        el: null,
        instance: null,
      },
    };
    this.data = {};
  }

  setup() {
    const { hls, mp4 } = this.player;

    hls.el = $('#player-hls');
    mp4.el = $('#player-mp4');
  }

  async load(data) {
    await this.destroy();

    if (emptyObject(data)) {
      this.unload();
      return;
    }

    this.data = data;

    this.setup();
    this.removeSources();
    this.setSources();
    this.setConfig();
    this.setPlayers();
    this.setWrapper();
  }

  setSources() {
    const { hls, mp4 } = this.player;

    this.setSource(hls.el, this.data.hls);
    this.setSource(mp4.el, this.data.mp4);
  }

  setConfig() {
    const { layoutControls } = this.app.config.player;

    layoutControls.posterImage = this.data.thumb;
    layoutControls.timelinePreview = {
      file: this.data.thumbnails,
      type: 'VTT',
    };
  }

  setPlayers() {
    const { player } = this.app.config;
    const { hls, mp4 } = this.player;

    hls.instance = fluidPlayer(hls.el, player);
    mp4.instance = fluidPlayer(mp4.el, player);
  }

  setWrapper() {
    this.wrapper = $$('.wrapper-video > div > div');

    for (const wrapper of this.wrapper) {
      wrapper.classList.add('rounded');
    }
  }

  removeSources() {
    const { hls, mp4 } = this.player;

    emptyEl(hls.el);
    emptyEl(mp4.el);
  }

  setSource(video, sources) {
    for (const key in sources) {
      const source = document.createElement('source');

      source.title = key;
      source.src = sources[key];

      video.appendChild(source);
    }
  }

  unload() {
    const { hls, mp4 } = this.player;

    this.setup();
    this.removeSources();

    hls.el.src = '';
    hls.el.load();

    mp4.el.src = '';
    mp4.el.load();
  }

  async destroy() {
    const { hls, mp4 } = this.player;

    if (hls.instance) {
      await hls.instance.destroy();
      hls.instance = null;
    }

    if (mp4.instance) {
      await mp4.instance.destroy();
      mp4.instance = null;
    }
  }
}

export default Player;
