import type SlideEditor from "./slide-editor";
import { Editor, EditorEvent } from "tinymce/tinymce";

export default class FontSizer {

  private classes = ['txtxs', 'txtsm', 'txtb', 'txtmd', 'txtlg', 'txtxl', 'txt2xl'];
  private fontSizes: Array<{title: string, class: string}> = []

  constructor(
    private readonly slideEditor: SlideEditor
  ) {
    for (const className of this.classes) {
      let title = 'Extra Small';
      if (className === 'txtsm') title = 'Small';
      if (className === 'txtb') title = 'Normal';
      if (className === 'txtmd') title = 'Medium';
      if (className === 'txtlg') title = 'Large';
      if (className === 'txtxl') title = 'Extra Large';
      if (className === 'txt2xl') title = '2X Large';

      this.fontSizes.push({title, class: className})
    }
  }

  setup(editor: Editor) {
    const fontSizes = this.fontSizes
    let activeClass: string|null = null;
    // Create custom dropdown button
    editor.ui.registry.addMenuButton('fontsizer', {
      text: 'Font Size',
      fetch: function(callback) {
        const items = fontSizes.map(function(size) {
          return {
            type: 'togglemenuitem',
            text: size.title,
            onAction: function() {
              editor.undoManager.transact(function() {
                // Remove all other font size classes
                fontSizes.forEach(function(s) {
                  editor.formatter.remove(s.class);
                });
                // Apply the selected class
                editor.formatter.apply(size.class);
              });
            },
            // @ts-ignore
            onSetup: function(api) {
              api.setActive(size.class === activeClass)
            }
          };
        });
        // @ts-ignore
        callback(items);
      },
      onSetup: api => {
        const test = (eventApi: EditorEvent<{ element: Element}>) => {
          let text = 'Font Size'
          let ac = null;
          for (let i = 0; i < this.classes.length; i++) {
            if (eventApi.element.classList.contains(this.classes[i])) {
              text = this.fontSizes[i].title
              ac = this.classes[i];
              break;
            }
          }
          api.setText(text);
          activeClass = ac;
        }
        editor.on('NodeChange', test)
        return () => editor.off('NodeChange', test)
      }
    });
  }

  initInstance(editor: Editor) {
    this.fontSizes.forEach(function(size) {
      editor.formatter.register(size.class, {
        inline: 'span',
        classes: size.class,
        styles: { 'font-size': '' } // Clear any inline styles
      });
    });
  }
}
