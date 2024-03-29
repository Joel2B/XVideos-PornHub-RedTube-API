export default function fetch(url, responseType = 'text') {
  return new Promise((resolve, reject) => {
    try {
      const request = new XMLHttpRequest();

      // Check for CORS support
      if (!('withCredentials' in request)) {
        return;
      }

      request.addEventListener('load', () => {
        if (responseType === 'text') {
          try {
            resolve(JSON.parse(request.responseText));
          } catch (_) {
            resolve(request.responseText);
          }
        } else {
          resolve(request);
        }
      });

      request.addEventListener('error', () => {
        throw new Error(request.status);
      });

      request.open('GET', url, true);

      // Set the required response type
      request.responseType = responseType;

      request.send();
    } catch (error) {
      reject(error);
    }
  });
}
