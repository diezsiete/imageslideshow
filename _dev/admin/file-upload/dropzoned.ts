import Dropzone from "dropzone";
Dropzone.autoDiscover = false;

type DropzonedOptions = {
  url?: string,
  fetchUrl?: string,
  complete?: () => void,
  error?: (message: string) => void,
  sending?: () => void,
  success?: (response: SuccessResponse) => void,
}
export type SuccessResponse = { name: string, path: string }

export default class Dropzoned {
  private dropzone: Dropzone;

  private fetchUrl: string|null;
  private completeListeners: Array<() => void> = [];
  private errorListeners: Array<(message: string) => void> = [];
  private sendingListeners: Array<() => void> = [];
  private successListeners: Array<(this: Dropzoned, response: SuccessResponse) => void> = [];

  constructor(container: string|HTMLElement, opts?: DropzonedOptions) {
    const containerElement = container instanceof HTMLElement ? container : document.querySelector<HTMLElement>(container);
    if (containerElement) {
      container = containerElement;
    }
    const url = opts?.url ?? (containerElement && containerElement.dataset.dzdUrl ? containerElement.dataset.dzdUrl : '');
    this.fetchUrl = opts?.fetchUrl ?? (containerElement && containerElement.dataset.dzdFetchUrl ? containerElement.dataset.dzdFetchUrl : '')

    this.dropzone = new Dropzone(container, {
      url,
      paramName: 'file_upload',
      createImageThumbnails: false,
      previewsContainer: false,
      error: (file, response: string|object) => this.onDropzoneError(response),
      sending: () => this.sendingListeners.forEach(listener => listener()),
      complete: () => this.completeListeners.forEach(listener => listener())
    });

    this.dropzone.on('success', (file, response) => this.onDropzoneSuccess(response));

    if (opts?.complete) {
      this.completeListeners.push(opts.complete)
    }
    if (opts?.error) {
      this.errorListeners.push(opts.error);
    }
    if (opts?.sending) {
      this.sendingListeners.push(opts.sending)
    }
    if (opts?.success) {
      this.successListeners.push(opts.success)
    }
  }

  click() {
    this.dropzone.hiddenFileInput?.click();
  }

  async fetchImage(fileName: string|SuccessResponse): Promise<string|null> {
    if (this.fetchUrl) {
      fileName = typeof fileName === 'object' ? fileName.path : fileName;

      const url = this.fetchUrl.replace('location/fileName', fileName);

      const headers = new Headers();
      // options.headers.set('Accept', 'application/json, text/javascript, */*; q=0.01');
      headers.set('X-Requested-With', 'XMLHttpRequest');

      const response = await fetch(url, { headers })
      const blob = await response.blob()
      return URL.createObjectURL(blob);
    }
    return null;
  }

  private onDropzoneError(response: string|any) {
    if (this.errorListeners.length) {
      const message = typeof response === 'string' ? response : (response.message || response.detail || response.title || 'Unknown error');
      this.errorListeners.forEach(listener => listener(message));
    }
  }

  private onDropzoneSuccess(response: any) {
    if (this.successListeners.length) {
      this.successListeners.forEach(listener => listener(response))
    } else {
      console.log({name: response.name, path: response.path})
    }
  }
}
