{{-- Modal Overlay (matches original: semi-transparent overlay, centered white box, scale animation) --}}
<div id="modalOverlay" class="fixed inset-0 z-50 p-2 sm:p-4 transition-opacity duration-300" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;" data-modal-overlay>
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full transform transition-all duration-300 relative z-10" style="transform: scale(0.95);" onclick="event.stopPropagation()" id="modalContainer">
        <div id="modalContent" class="p-0">
            {{-- Modal content will be inserted here via JS --}}
        </div>
    </div>
</div>

{{-- Add Customer Modal Overlay (used by waiting-list and others) --}}
<div id="addCustomerModalOverlay" class="fixed inset-0 z-[60] p-4" style="background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;" onclick="typeof closeAddCustomerModal === 'function' && closeAddCustomerModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div id="addCustomerModalContent">
            {{-- Add Customer modal content will be inserted here --}}
        </div>
    </div>
</div>

{{-- Nested Modal Overlay --}}
<div id="nestedModalOverlay" class="fixed inset-0 z-[60] p-2 sm:p-4 transition-opacity duration-300" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;" data-modal-overlay>
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden transform transition-all duration-300 relative z-10" style="transform: scale(0.95);" onclick="event.stopPropagation()" id="nestedModalContainer">
        <div id="nestedModalContent" class="overflow-y-auto max-h-[90vh] p-0">
            {{-- Nested modal content will be inserted here via JS --}}
        </div>
    </div>
</div>

<script>
function openModal(content, size = 'default', closeOnOutsideClick = true) {
    const overlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalContainer = document.getElementById('modalContainer');

    if (!overlay || !modalContent || !modalContainer) return;

    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full transform transition-all duration-300 relative z-10';
    modalContainer.style.transform = 'scale(0.95)';
    modalContent.style.overflowY = '';
    modalContent.style.maxHeight = '';
    modalContent.style.overflow = '';
    modalContent.style.display = '';
    modalContent.style.flexDirection = '';
    if (size === 'large-flex') {
        modalContainer.classList.add('max-w-6xl');
        modalContainer.style.maxHeight = '90vh';
        modalContent.style.maxHeight = '90vh';
        modalContent.style.overflow = 'hidden';
        modalContent.style.display = 'flex';
        modalContent.style.flexDirection = 'column';
    } else if (size === 'large') {
        modalContainer.classList.add('max-w-6xl');
        modalContainer.style.maxHeight = '95vh';
        modalContent.style.maxHeight = '95vh';
        modalContent.style.overflowY = 'auto';
    } else if (size === 'xl') {
        modalContainer.classList.add('max-w-7xl');
        modalContainer.style.maxHeight = '95vh';
        modalContent.style.maxHeight = '95vh';
        modalContent.style.overflowY = 'auto';
    } else if (size === 'medium') {
        modalContainer.classList.add('max-w-4xl');
    } else if (size === 'small') {
        modalContainer.classList.add('max-w-md');
    } else {
        modalContainer.classList.add('max-w-2xl');
    }

    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';
    overlay.onclick = closeOnOutsideClick ? closeModal : null;

    setTimeout(() => {
        modalContainer.style.transform = 'scale(1)';
    }, 10);
}

function closeModal() {
    const overlay = document.getElementById('modalOverlay');
    const modalContainer = document.getElementById('modalContainer');
    if (modalContainer) {
        modalContainer.style.transform = 'scale(0.95)';
    }
    setTimeout(() => {
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.style.display = 'none';
        }
        document.body.style.overflow = 'auto';
    }, 200);
}

function openNestedModal(content, size = 'default', closeOnOutsideClick = true) {
    const overlay = document.getElementById('nestedModalOverlay');
    const modalContent = document.getElementById('nestedModalContent');
    const modalContainer = document.getElementById('nestedModalContainer');

    if (!overlay || !modalContent || !modalContainer) return;

    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full transform transition-all duration-300 relative z-10';
    modalContainer.style.transform = 'scale(0.95)';
    modalContent.style.overflowY = '';
    modalContent.style.maxHeight = '';
    modalContent.style.overflow = '';
    modalContent.style.display = '';
    modalContent.style.flexDirection = '';

    if (size === 'large-flex') {
        modalContainer.classList.add('max-w-6xl');
        modalContainer.style.maxHeight = '90vh';
        modalContent.style.maxHeight = '90vh';
        modalContent.style.overflow = 'hidden';
        modalContent.style.display = 'flex';
        modalContent.style.flexDirection = 'column';
    } else if (size === 'large') {
        modalContainer.classList.add('max-w-6xl');
        modalContainer.style.maxHeight = '90vh';
        modalContent.style.maxHeight = '90vh';
        modalContent.style.overflowY = 'auto';
    } else if (size === 'xl') {
        modalContainer.classList.add('max-w-7xl');
        modalContainer.style.maxHeight = '90vh';
        modalContent.style.maxHeight = '90vh';
        modalContent.style.overflowY = 'auto';
    } else if (size === 'medium') {
        modalContainer.classList.add('max-w-4xl');
        modalContainer.style.maxHeight = '90vh';
    } else if (size === 'small') {
        modalContainer.classList.add('max-w-md');
        modalContainer.style.maxHeight = '90vh';
    } else {
        modalContainer.classList.add('max-w-2xl');
        modalContainer.style.maxHeight = '90vh';
    }

    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.onclick = closeOnOutsideClick ? closeNestedModal : null;

    setTimeout(() => {
        modalContainer.style.transform = 'scale(1)';
    }, 10);
}

function closeNestedModal() {
    const overlay = document.getElementById('nestedModalOverlay');
    const modalContainer = document.getElementById('nestedModalContainer');
    if (modalContainer) {
        modalContainer.style.transform = 'scale(0.95)';
    }
    setTimeout(() => {
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.style.display = 'none';
        }
    }, 200);
}

// Helper functions for addCustomerModalOverlay (used by waiting-list)
window.openAddCustomerModal = function(content) {
    const overlay = document.getElementById('addCustomerModalOverlay');
    const modalContent = document.getElementById('addCustomerModalContent');
    if (!overlay || !modalContent) return;
    if (content) modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';
};

window.closeAddCustomerModal = function() {
    const overlay = document.getElementById('addCustomerModalOverlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.style.display = 'none';
    }
    document.body.style.overflow = 'auto';
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const addCustomerOverlay = document.getElementById('addCustomerModalOverlay');
        const nestedOverlay = document.getElementById('nestedModalOverlay');
        if (addCustomerOverlay && addCustomerOverlay.style.display !== 'none' && !addCustomerOverlay.classList.contains('hidden')) {
            window.closeAddCustomerModal();
        } else if (nestedOverlay && nestedOverlay.style.display !== 'none' && !nestedOverlay.classList.contains('hidden')) {
            closeNestedModal();
        } else {
            closeModal();
        }
    }
});

function showSuccessMessage(message) {
    const el = document.createElement('div');
    el.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[70]';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(function() { el.remove(); }, 3000);
}

function showErrorMessage(message) {
    const el = document.createElement('div');
    el.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-[70]';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(function() { el.remove(); }, 3000);
}

/**
 * Open a modern confirmation modal (e.g. for delete).
 * @param {Object} options - title, message, confirmLabel (optional), onConfirm (function), nested (boolean) - if true, opens on top of current modal (z-60)
 */
function escHtml(str) { return (str + '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
function boldName(name) { return '<strong class="text-gray-900 font-semibold">' + escHtml(name) + '</strong>'; }
function openConfirmModal(options) {
    const title = (options && options.title) || 'Confirm';
    const message = (options && options.message) || 'Are you sure you want to continue?';
    const confirmLabel = (options && options.confirmLabel) || 'Delete';
    const onConfirm = options && typeof options.onConfirm === 'function' ? options.onConfirm : null;
    const nested = options && options.nested === true;
    window._confirmModalOnConfirm = onConfirm;
    const closeFn = nested ? 'closeNestedModal' : 'closeModal';
    const content = `
        <div class="flex flex-col rounded-2xl overflow-hidden shadow-xl">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-200">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-gray-900 tracking-tight">${(title + '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</h3>
                    <button type="button" onclick="${closeFn}(); window._confirmModalOnConfirm = null;" class="p-2 -m-2 text-gray-400 hover:text-gray-600 hover:bg-gray-200 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-6 sm:py-8 bg-white">
                <div class="flex items-start gap-4 sm:gap-5">
                    <div class="flex-shrink-0 w-11 h-11 rounded-full bg-red-50 flex items-center justify-center ring-4 ring-red-50/50">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5 space-y-1">
                        <p class="text-sm text-gray-600 leading-relaxed">${message}</p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50/80 border-t border-gray-200 flex flex-row justify-end gap-3">
                <button type="button" onclick="${closeFn}(); window._confirmModalOnConfirm = null;" class="min-w-[5rem] px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                    Cancel
                </button>
                <button type="button" id="confirmModalConfirmBtn" class="min-w-[5rem] px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    ${(confirmLabel + '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')}
                </button>
            </div>
        </div>
    `;
    if (nested) {
        openNestedModal(content, 'small');
    } else {
        openModal(content, 'small');
    }
    const btn = document.getElementById('confirmModalConfirmBtn');
    if (btn) {
        btn.onclick = function() {
            if (nested) {
                closeNestedModal();
            } else {
                closeModal();
            }
            if (window._confirmModalOnConfirm) {
                window._confirmModalOnConfirm();
                window._confirmModalOnConfirm = null;
            }
        };
    }
}
</script>
