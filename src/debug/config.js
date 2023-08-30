const config = {
  sites: {
    xvideos: '46318431',
    pornhub: 'ph6116a13a48187',
    redtube: '39697741',
  },
  defaultLink: 'https://www.xvideos.com/video46318431/',
  typeSearch: {
    one: 1,
    two: 2,
  },
  storage: {
    enabled: true,
    key: 'debug',
    expiration: 30,
  },
  // apiUrl: 'https://appsdev.cyou/xv-ph-rt/api',
  apiUrl: 'http://localhost/XVideos-PornHub-RedTube-API/src',
  player: {
    layoutControls: {
      fillToContainer: true,
      loop: true,
      // autoPlay: {
      //   active: true,
      //   waitInteraction: true,
      // },
      playPauseAnimation: true,
      controlBar: {
        autoHide: true,
      },
      menu: {
        loop: true,
      },
      fullscreen: {
        iosNative: true,
      },
    },
    storage: {
      shared: false,
    },
    hls: {
      overrideNative: true,
      debug: false,
    },
    debug: false,
  },
};

export default config;
