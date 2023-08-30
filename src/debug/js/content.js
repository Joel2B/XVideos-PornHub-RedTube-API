import { $, $$ } from './utils/dom.js';

class Content {
  constructor(app) {
    this.app = app;

    this.lock = false;
    this.content = '';

    this.init();
    this.listeners();
  }

  init() {
    this.search = $('#search-btn');
  }

  async load() {
    if (this.lock) {
      return;
    }

    const { searchControl, sections, config, api, player } = this.app;
    const { videoId, siteId, link } = searchControl;

    this.setLock();
    this.setLoading();

    if (searchControl.currentType == config.typeSearch.one) {
      this.content = await api.getByParams(siteId, videoId);
    } else {
      this.content = await api.getByLink(link);
    }

    await player.load(this.content.data[0]);

    this.setLock();
    this.setLoading();

    sections.setup();
  }

  setLoading() {
    const wrapperVideo = $('.wrapper-video');
    const wrappers = $$('.wrapper-video > div') || [];

    if (this.lock) {
      wrapperVideo.classList.add('placeholder-glow');

      for (const wrapper of wrappers) {
        wrapper.classList.add('placeholder', 'rounded');
      }

      return;
    }

    wrapperVideo.classList.remove('placeholder-glow');

    for (const wrapper of wrappers) {
      wrapper.classList.remove('placeholder', 'rounded');

      wrapper.querySelector('video').classList.remove('placeholder');
    }
  }

  setLock() {
    this.lock = !this.lock;
  }

  listeners() {
    this.search.addEventListener('click', this.load.bind(this));
  }
}

export default Content;
