import is from './is.js';

export function $(selector) {
  return document.querySelector(selector);
}

export function _$(document, selector) {
  return document.querySelector(selector);
}

export function $$(selector) {
  return document.querySelectorAll(selector);
}

export function replaceElement(newChild, oldChild) {
  if (!is.element(oldChild) || !is.element(oldChild.parentNode) || !is.element(newChild)) {
    return null;
  }

  oldChild.parentNode.replaceChild(newChild, oldChild);

  return newChild;
}

export function emptyEl(el) {
  while (el.firstChild) {
    el.removeChild(el.firstChild);
  }

  return el;
}
