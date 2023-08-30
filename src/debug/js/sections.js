import { $, _$ } from './utils/dom.js';

class Sections {
  constructor(app) {
    this.app = app;

    this.head = '_accordion-head';
    this.body = '_accordion-collapse';
    this.badge = '{badge}';
    this.label = '{label}';
    this.span = '{span}';

    // this.template = {
    //   category: null,
    //   missingData: null,
    //   loadTime: null,
    // };

    this.init();
  }

  init() {
    this.sections = $('#sections');

    var template = $('#accordion-item');
    this.template = template.cloneNode(true);
    template.remove();
  }

  addMissingData(missingData) {
    const missingDataContent = _$(this.template, '.missing-data_content');
    const missingDataBadge = missingDataContent.firstElementChild;
    const newBadge = missingDataBadge.cloneNode(true);

    missingDataBadge.remove();

    newBadge.textContent = newBadge.textContent.replace(
      this.badge,
      missingData
    );

    missingDataContent.appendChild(newBadge);
    this.accordion.appendChild(this.template);
  }

  addLoadTime(type, time) {
    const loadTimeContent = _$(this.template, '.load-time_content');
    const loadTimeTemplate = loadTimeContent.firstElementChild;
    const newLoadTime = loadTimeTemplate.cloneNode(true);
    const label = newLoadTime.children[0];
    const data = newLoadTime.children[1];

    loadTimeTemplate.remove();

    label.textContent = label.textContent.replace(this.span, type);
    data.textContent = data.textContent.replace(this.span, time);

    loadTimeContent.appendChild(newLoadTime);
    this.accordion.appendChild(this.template);
  }

  setup() {
    const { accordion, template, head, body, badge, label } = this;
    const { missing_data, categories, load_time, memory_usage, data } =
      this.app.content.content;

    sections.innerHTML = '';

    for (let _category of categories) {
      const categoryName = _category.category;

      var accordionCategory = template.outerHTML;

      accordionCategory = accordionCategory
        .replaceAll(head, `${categoryName}${head}`)
        .replaceAll(body, `${categoryName}${body}`)
        .replace(badge, categoryName)
        .replace(label, '');

      let content = '';

      for (const [i, data] of _category.server_data.entries()) {
        var accordionContent = template.outerHTML;

        var _content =
          data['content'] == ''
            ? 'Empty'
            : data['content'].replace(/</g, '&lt;').replace(/>/g, '&gt;');

        accordionContent = accordionContent
          .replaceAll(head, `${categoryName}${head}${i}`)
          .replaceAll(body, `${categoryName}${body}${i}`)
          .replace(badge, data['url'])
          .replace(label, '')
          .replace('{content}', _content)
          .replace('{missing-data}', 'hidden')
          .replace('{load-time}', 'hidden')
          .replace('{copy}', `${categoryName}${body}${i}`);

        content += accordionContent;
      }

      accordionCategory = accordionCategory.replace('{content}', content);

      sections.innerHTML += accordionCategory;
    }
  }
}

export default Sections;
