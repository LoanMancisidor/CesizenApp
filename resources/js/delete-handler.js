window.deleteManager = {
    currentUrl: '',
    currentElement: null,

    openModal: function(button) {
        this.currentUrl = button.getAttribute('data-url');
        
        // On cherche l'élément à supprimer : soit une Card, soit une ligne de Tableau (tr)
        this.currentElement = button.closest('.article-card') || 
                              button.closest('.card') || 
                              button.closest('tr');
        
        // --- 1. Gestion des sous-émotions (Zone existante) ---
        const children = button.getAttribute('data-children');
        const warningZone = document.getElementById('modal-warning-zone');
        const childrenList = document.getElementById('modal-children-list');

        if (children && children.trim().length > 0) {
            childrenList.innerText = children;
            warningZone.style.display = 'block';
        } else {
            if (warningZone) warningZone.style.display = 'none';
        }

        // --- 2. Gestion du reclassement des Utilisateurs (Nouvelle zone) ---
        const users = button.getAttribute('data-users');
        const userWarning = document.getElementById('modal-user-warning');
        const userList = document.getElementById('modal-user-list');

        if (users && users.trim().length > 0) {
            userList.innerText = users;
            userWarning.style.display = 'block';
        } else {
            if (userWarning) userWarning.style.display = 'none';
        }

        const modal = document.getElementById('custom-modal');
        if (modal) modal.style.display = 'flex';
    },

    closeModal: function() {
        const modal = document.getElementById('custom-modal');
        if (modal) modal.style.display = 'none';
    },

    confirmDelete: function() {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(this.currentUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                this.closeModal();
                if (this.currentElement) {
                    // Animation de sortie
                    this.currentElement.style.transition = "all 0.4s ease";
                    this.currentElement.style.opacity = "0";
                    this.currentElement.style.transform = "translateX(20px)"; // Petit effet de glissement
                    
                    setTimeout(() => {
                        this.currentElement.remove();
                        
                        // Si c'est un tableau et qu'il est vide, on peut recharger ou afficher un message
                        if (document.querySelectorAll('tbody tr').length === 0 && document.querySelector('table')) {
                            location.reload(); 
                        }
                    }, 400);
                }
            } else {
                response.json().then(data => {
                    alert(data.message || "Erreur lors de la suppression.");
                });
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirm-delete-btn');
    if (confirmBtn) {
        confirmBtn.onclick = () => window.deleteManager.confirmDelete();
    }
});