/**
 * MODULE: Form Handling
 * WORKFLOW: All
 * PURPOSE: Form validation, submission, and dynamic fields per 0530_COMPONENT_LIBRARY.md
 */

class FormHandler {
  constructor(formId) {
    this.form = document.getElementById(formId);
    if (this.form) this.init();
  }

  init() {
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    this.form.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('blur', () => this.validateField(input));
    });
  }

  handleSubmit(e) {
    e.preventDefault();
    if (!this.validate()) return;
    const data = this.serialize();
    this.submit(data);
  }

  validate() {
    let valid = true;
    this.form.querySelectorAll('.form-control[required]').forEach(input => {
      if (!input.value.trim()) {
        this.showError(input, 'This field is required');
        valid = false;
      } else {
        this.clearError(input);
      }
    });
    return valid;
  }

  validateField(input) {
    if (input.hasAttribute('required') && !input.value.trim()) {
      this.showError(input, 'This field is required');
      return false;
    }
    if (input.type === 'email' && input.value && !this.isValidEmail(input.value)) {
      this.showError(input, 'Invalid email address');
      return false;
    }
    if (input.type === 'url' && input.value && !this.isValidUrl(input.value)) {
      this.showError(input, 'Invalid URL');
      return false;
    }
    this.clearError(input);
    return true;
  }

  showError(input, message) {
    input.classList.add('error');
    let errorEl = input.parentElement.querySelector('.form-error');
    if (!errorEl) {
      errorEl = document.createElement('div');
      errorEl.className = 'form-error';
      input.parentElement.appendChild(errorEl);
    }
    errorEl.textContent = message;
  }

  clearError(input) {
    input.classList.remove('error');
    const errorEl = input.parentElement.querySelector('.form-error');
    if (errorEl) errorEl.remove();
  }

  serialize() {
    const data = {};
    this.form.querySelectorAll('.form-control').forEach(input => {
      data[input.name] = input.value;
    });
    return data;
  }

  async submit(data) {
    const endpoint = this.form.dataset.endpoint;
    const method = this.form.dataset.method || 'POST';
    const result = await API.request(method, endpoint, data);
    if (result.success) {
      this.showSuccess('Saved successfully');
      this.form.reset();
    } else {
      this.showError(this.form, result.error?.message || 'Save failed');
    }
  }

  showSuccess(message) {
    Toast.show(message, 'success');
  }

  isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  isValidUrl(url) {
    try { new URL(url); return true; } catch { return false; }
  }
}

// Toast system
class Toast {
  static show(message, type = 'info') {
    let container = document.querySelector('.toast-container');
    if (!container) {
      container = document.createElement('div');
      container.className = 'toast-container';
      document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
  }
}
window.Toast = Toast;
