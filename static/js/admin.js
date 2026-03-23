// Admin functions

// Récupérer et afficher les membres
async function loadMembers() {
    try {
        const response = await fetch('/api/members');
        const members = await response.json();
        const membersList = document.getElementById('membersList');
        
        if (members.length === 0) {
            membersList.innerHTML = '<p>Aucun membre enregistré.</p>';
            return;
        }
        
        membersList.innerHTML = members.map(member => `
            <div class="item-card">
                <div class="item-info">
                    <strong>${member.first_name} ${member.last_name}</strong>
                    <p>${member.email}</p>
                    <small>Groupe: ${member.group || 'N/A'}</small>
                </div>
                <div class="item-actions">
                    <button class="btn btn-danger" onclick="deleteMember(${member.id})">Supprimer</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Erreur lors du chargement des membres:', error);
        alert('Erreur lors du chargement des membres');
    }
}

// Récupérer et afficher les groupes
async function loadGroups() {
    try {
        const response = await fetch('/api/groups');
        const groups = await response.json();
        const groupsList = document.getElementById('groupsList');
        
        if (groups.length === 0) {
            groupsList.innerHTML = '<p>Aucun groupe créé.</p>';
            return;
        }
        
        groupsList.innerHTML = groups.map(group => `
            <div class="item-card">
                <div class="item-info">
                    <strong>${group.name}</strong>
                    <p>Niveau: ${group.level}</p>
                    <p>Coach: ${group.coach}</p>
                    <small>Membres: ${group.members_count}${group.members_count ? ' / ' + group.members_count : ''}</small>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Erreur lors du chargement des groupes:', error);
        alert('Erreur lors du chargement des groupes');
    }
}

// Ajouter un nouveau membre
document.getElementById('memberForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const groupId = document.getElementById('groupId').value;
    
    const data = {
        first_name: document.getElementById('firstName').value,
        last_name: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        group_id: groupId ? parseInt(groupId) : null
    };
    
    try {
        const response = await fetch('/api/members', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            showAlert('Membre ajouté avec succès!', 'success');
            document.getElementById('memberForm').reset();
            loadMembers();
        } else {
            const error = await response.json();
            showAlert('Erreur: ' + error.message, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Erreur lors de l\'ajout du membre', 'error');
    }
});

// Créer un nouveau groupe
document.getElementById('groupForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const data = {
        name: document.getElementById('groupName').value,
        level: document.getElementById('groupLevel').value,
        coach: document.getElementById('groupCoach').value,
        max_members: document.getElementById('groupMaxMembers').value ? 
                     parseInt(document.getElementById('groupMaxMembers').value) : null
    };
    
    try {
        const response = await fetch('/api/groups', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            showAlert('Groupe créé avec succès!', 'success');
            document.getElementById('groupForm').reset();
            loadGroups();
        } else {
            const error = await response.json();
            showAlert('Erreur: ' + error.message, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Erreur lors de la création du groupe', 'error');
    }
});

// Enregistrer une présence
document.getElementById('attendanceForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const data = {
        member_id: parseInt(document.getElementById('attendanceMemberId').value),
        session_date: document.getElementById('sessionDate').value,
        status: document.getElementById('attendanceStatus').value,
        notes: document.getElementById('attendanceNotes').value
    };
    
    try {
        const response = await fetch('/api/attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            showAlert('Présence enregistrée!', 'success');
            document.getElementById('attendanceForm').reset();
        } else {
            const error = await response.json();
            showAlert('Erreur: ' + error.message, 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Erreur lors de l\'enregistrement de la présence', 'error');
    }
});

// Supprimer un membre
async function deleteMember(memberId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce membre?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/members/${memberId}`, {
            method: 'DELETE'
        });
        
        if (response.ok) {
            showAlert('Membre supprimé!', 'success');
            loadMembers();
        } else {
            showAlert('Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('Erreur lors de la suppression du membre', 'error');
    }
}

// Afficher une alerte
function showAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    
    // Trouver le premier .admin-section et insérer l'alerte
    const adminSection = document.querySelector('.admin-section');
    if (adminSection) {
        adminSection.parentNode.insertBefore(alert, adminSection);
    }
    
    // Supprimer l'alerte après 4 secondes
    setTimeout(() => {
        alert.remove();
    }, 4000);
}

// Charger les données au démarrage
document.addEventListener('DOMContentLoaded', () => {
    loadMembers();
    loadGroups();
});
