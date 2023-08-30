export function emptyObject(obj) {
  for (let key in obj) {
    if (obj[key] instanceof Object === true) {
      if (emptyObject(obj[key]) === false) return false;
    } else {
      if (obj[key].length !== 0) return false;
    }
  }

  return true;
}
