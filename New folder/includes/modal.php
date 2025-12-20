<!-- Modal Overlay -->
<div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2 sm:p-4 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal()">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95" onclick="event.stopPropagation()" id="modalContainer">
        <div id="modalContent">
            <!-- Modal content will be inserted here -->
        </div>
    </div>
</div>

<script>
function openModal(content, size = 'default') {
    const overlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalContainer = document.getElementById('modalContainer');
    
    // Reset classes
    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95';
    
    // Apply size classes
    if (size === 'large') {
        modalContainer.classList.add('max-w-6xl');
    } else if (size === 'xl') {
        modalContainer.classList.add('max-w-7xl');
    } else {
        modalContainer.classList.add('max-w-2xl');
    }
    
    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Animate modal in
    setTimeout(() => {
        modalContainer.classList.remove('scale-95');
        modalContainer.classList.add('scale-100');
    }, 10);
}

function closeModal() {
    const overlay = document.getElementById('modalOverlay');
    const modalContainer = document.getElementById('modalContainer');
    
    if (modalContainer) {
        modalContainer.classList.remove('scale-100');
        modalContainer.classList.add('scale-95');
    }
    
    setTimeout(() => {
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 200);
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

