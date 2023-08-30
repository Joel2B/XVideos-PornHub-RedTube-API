import { $, $$ } from './utils/dom.js';

class SearchControl {
  constructor(app) {
    this.app = app;

    this.currentType = 0;
    this.currentSite = 0;

    this.siteId = 0;
    this.videoId = 0;
    this.link = '';

    this.init();
    this.render();
    this.listeners();
    this.loadStorage();
  }

  init() {
    this.type1 = $('#content-type-1');
    this.type2 = $('#content-type-2');

    const buttons = $$('[name="type-search"]');
    this.btnType1 = buttons[0];
    this.btnType2 = buttons[1];

    this.dropdownSites = $('#dropdown-sites');
    this.currentSiteLabel = $('#current-site');

    this.search = $('#search-btn');
    this._videoId = $('#video-id');
    this._link = $('#link');
  }

  render() {
    const { sites } = this.app.config;
    const _sites = Object.entries(sites);

    for (const [index, site] of _sites.entries()) {
      const li = `<li><a data-site="${index}" class="dropdown-item" href="#">${site[0]}</a></li>`;

      this.dropdownSites.innerHTML += li;
    }
  }

  setType1() {
    const { storage, config } = this.app;

    this.type1.classList.remove('collapse');
    this.type2.classList.add('collapse');

    this.currentType = config.typeSearch.one;
    storage.set('currentType', 1);
  }

  setType2() {
    const { storage, config } = this.app;

    this.type1.classList.add('collapse');
    this.type2.classList.remove('collapse');

    this.currentType = config.typeSearch.two;
    storage.set('currentType', 2);
  }

  setDropdownSite(event) {
    const { storage, config } = this.app;
    const { sites } = config;
    const target = event.target;

    if (target.tagName !== 'A') return;

    this.currentSiteLabel.textContent = target.textContent;
    this.currentSite = target.dataset.site;

    storage.set('currentSite', this.currentSite);

    if (storage.get('videoId') === null) {
      const siteId = Object.keys(sites)[storage.get('currentSite') || this.currentSite];

      this.videoId = config.sites[siteId];
      this._videoId.value = this.videoId;
    }
  }

  setSearch() {
    const { storage, config } = this.app;

    if (this.currentType == config.typeSearch.one) {
      this.siteId = Object.keys(config.sites)[this.currentSite];
      this.videoId = this._videoId.value;

      storage.set('siteId', this.siteId);
      storage.set('videoId', this.videoId);
    } else {
      this.link = this._link.value;

      storage.set('link', this.link);
    }
  }

  listeners() {
    this.btnType1.addEventListener('click', this.setType1.bind(this));
    this.btnType2.addEventListener('click', this.setType2.bind(this));
    this.dropdownSites.addEventListener('click', this.setDropdownSite.bind(this));
    this.search.addEventListener('click', this.setSearch.bind(this));
  }

  loadStorage() {
    const { storage, config } = this.app;
    const { sites } = config;

    storage.get('currentType') === 1 ? this.btnType1.click() : this.btnType2.click();

    this.currentSite = storage.get('currentSite') || this.currentSite;

    const siteId = Object.keys(sites)[this.currentSite];

    this.currentSiteLabel.textContent = siteId;
    this.siteId = storage.get('siteId') || siteId;
    this.videoId = storage.get('videoId') || config.sites[siteId];
    this.link = storage.get('link') || config.defaultLink;

    this._videoId.value = this.videoId;
    this._link.value = this.link;
  }
}

export default SearchControl;
