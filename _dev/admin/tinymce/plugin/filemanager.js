tinymce.PluginManager.add('filemanager', (editor, url) => {
  // Register a toolbar button
  editor.ui.registry.addButton('example', {
    text: 'My Button',
    onAction: () => {
      editor.insertContent('Hello from the plugin!');
    }
  });


  // Optional: Return metadata for the Help plugin
  return {
    getMetadata: () => ({
      name: 'Example Plugin',
      url: 'https://example.com/docs'
    })
  };
});
