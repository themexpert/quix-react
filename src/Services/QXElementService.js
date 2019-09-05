import twig from '../Helpers/TwigHelper';

const elements = {};
const files = require.context('./blocks/', true, /\.twig$/);

files.keys().forEach(_file => {
  const __file = _file.split('/');
  const _name = __file[__file.length - 1];
  if (['animation.twig', 'global.twig'].indexOf(_name) >= 0) {
    twig({
      id: _name,
      allowInlineIncludes: true,
      data: files(_file),
    });
    return;
  }
  const module = twig({
    id: name,
    allowInlineIncludes: true,
    data: files(_file),
  });
  const name = __file[1];
  if (elements[name] === undefined) {
    elements[name] = {};
  }
  if (__file[3] === 'html.twig') {
    elements[name].html = module;
  }
  else if (__file[3] === 'style.twig') {
    elements[name].style = module;
  }
  else if (__file[3] === 'script.twig') {
    elements[name].script = module;
  }
});

export function getElement(slug) {
  return elements[slug] || null;
}