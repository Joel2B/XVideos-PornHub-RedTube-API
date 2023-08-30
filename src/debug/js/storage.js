import is from './utils/is.js';

class Storage {
  constructor(app) {
    this.enabled = app.config.storage.enabled;
    this.key = app.config.storage.key;
    this.expiration = app.config.storage.expiration;

    this.key += ':';
  }

  supported = () => {
    try {
      const test = '__storage_test__';
      window.localStorage.setItem(test, test);
      window.localStorage.removeItem(test);
      return true;
    } catch (_) {
      return false;
    }
  };

  set = (key, value) => {
    if (!this.supported() || !this.enabled) {
      return;
    }

    const data = {
      value: value,
      expire: new Date().getTime() / 1000 + 60 * 60 * 24 * (this.expiration || 30),
    };

    window.localStorage.setItem(this.key + key, JSON.stringify(data));
  };

  get = (key) => {
    if (!this.supported() || !this.enabled) {
      return null;
    }

    const data = JSON.parse(window.localStorage.getItem(this.key + key));

    if (is.empty(data) || data.expire <= new Date().getTime() / 1000) {
      window.localStorage.removeItem(this.key + key);
      return null;
    }

    return data.value;
  };

  remove = (key) => {
    if (!this.supported() || !this.enabled) {
      return;
    }

    window.localStorage.removeItem(this.key + key);
  };
}

export default Storage;
