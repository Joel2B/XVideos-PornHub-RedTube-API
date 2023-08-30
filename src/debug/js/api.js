import fetch from './utils/fetch.js';

class Api {
  constructor(app) {
    this.apiUrl = `${app.config.apiUrl}/?`;
  }

  async getByParams(siteId, videoId) {
    const params = `site_id=${siteId}&video_id=${videoId}`;

    return this.get(params);
  }

  async getByLink(link) {
    const _link = `data=${link}`;

    return this.get(_link);
  }

  async get(data) {
    const url = `${this.apiUrl}${data}&debug=true`;
    const request = await fetch(url, 'json');

    if (request.code === '204') {
      return null;
    }

    return request.response;
  }
}

export default Api;
