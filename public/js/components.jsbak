/**
 * MODULE: UI Components
 * WORKFLOW: All
 * PURPOSE: Reusable UI components per 0530_COMPONENT_LIBRARY.md
 */

// Modal Component
class Modal {
  constructor(options = {}) {
    this.title = options.title || '';
    this.content = options.content || '';
    this.onConfirm = options.onConfirm || (() => {});
    this.onCancel = options.onCancel || (() => {});
  }

  open() {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.innerHTML = `
      <div class="modal">
        <h3>${this.title}</h3>
        <div class="modal-body">${this.content}</div>
        <div class="form-actions">
          <button class="btn-secondary modal-cancel">Cancel</button>
          <button class="btn-primary modal-confirm">Confirm</button>
        </div>
      </div>
    `;
    document.body.appendChild(overlay);

    overlay.querySelector('.modal-cancel').addEventListener('click', () => {
      this.onCancel();
      overlay.remove();
    });
    overlay.querySelector('.modal-confirm').addEventListener('click', () => {
      this.onConfirm();
      overlay.remove();
    });
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) overlay.remove();
    });
  }
}

// Pagination Component
class Pagination {
  constructor(container, options = {}) {
    this.container = container;
    this.currentPage = options.currentPage || 1;
    this.totalPages = options.totalPages || 1;
    this.onPageChange = options.onPageChange || (() => {});
  }

  render() {
    if (this.totalPages <= 1) {
      this.container.innerHTML = '';
      return;
    }
    let html = '';
    for (let i = 1; i <= this.totalPages; i++) {
      const active = i === this.currentPage ? 'active' : '';
      html += `<button class="${active}" data-page="${i}">${i}</button>`;
    }
    this.container.innerHTML = html;
    this.container.querySelectorAll('button').forEach(btn => {
      btn.addEventListener('click', () => {
        this.currentPage = parseInt(btn.dataset.page);
        this.onPageChange(this.currentPage);
        this.render();
      });
    });
  }
}

// Search Component
class SearchBox {
  constructor(input, options = {}) {
    this.input = input;
    this.debounceMs = options.debounce || 300;
    this.onSearch = options.onSearch || (() => {});
    this.timeout = null;
    this.init();
  }

  init() {
    this.input.addEventListener('input', () => {
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        this.onSearch(this.input.value);
      }, this.debounceMs);
    });
  }
}

// Tab Component
class Tabs {
  constructor(container, options = {}) {
    this.container = container;
    this.onChange = options.onChange || (() => {});
    this.init();
  }

  init() {
    this.container.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        this.container.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        this.onChange(tab.dataset.tab);
      });
    });
  }
}

// Export for global use
window.Modal = Modal;
window.Pagination = Pagination;
window.SearchBox = SearchBox;
window.Tabs = Tabs;
