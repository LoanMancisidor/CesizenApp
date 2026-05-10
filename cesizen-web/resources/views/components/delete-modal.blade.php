<div id="custom-modal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">⚠️</div>
        <h3>Confirmer la suppression</h3>
        <p id="modal-main-message">Cette action est irréversible.</p>

        {{-- Zone pour la liste des émotions liées --}}
        <div id="modal-warning-zone"
            style="display: none; margin-top: 15px; padding: 10px; background: #fff5f5; border-radius: var(--radius-md); border: 1px solid var(--red);">
            <p style="color: var(--red); font-weight: bold; font-size: 0.85rem; margin: 0;">Attention !</p>
            <p style="font-size: 0.8rem; margin: 5px 0;">Les émotions suivantes seront aussi supprimées :</p>
            <p id="modal-children-list" style="font-weight: 600; font-size: 0.8rem; color: var(--dark-text);"></p>
        </div>

        {{-- Zone pour le reclassement des utilisateurs --}}
        <div id="modal-user-warning"
            style="display: none; margin-top: 15px; padding: 12px; background: #fff5f5; border-radius: var(--radius-md); border: 1px solid var(--red);">
            <p style="color: var(--red); font-weight: bold; font-size: 0.85rem; margin: 0;">⚠️ Attention !</p>
            <p style="font-size: 0.8rem; margin: 5px 0;">Les membres suivants seront reclassés en
                <strong>Utilisateur</strong> :</p>
            <p id="modal-user-list" style="font-weight: 600; font-size: 0.8rem; color: var(--dark-text);"></p>
        </div>

        <div class="modal-footer">
            <button onclick="deleteManager.closeModal()" class="btn-secondary">Annuler</button>
            <button id="confirm-delete-btn" class="btn-danger-modal">Supprimer</button>
        </div>
    </div>
</div>
