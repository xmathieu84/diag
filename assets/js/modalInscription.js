

if (document.title == 'Phase 2 de votre inscription' || document.title == 'Phase 3 choix abonnement' || document.title == 'Phase 4 choix des tarifs' || document.title == 'Phase 5 choix des tarifs' || document.title == 'Phase 6 de votre inscription') {


    document.addEventListener('DOMContentLoaded', () => {
        let phase2 = document.querySelector('.modal')
        let fermer = document.querySelector('.btn-secondary')
        phase2.style.display = 'block';
        fermer.addEventListener('click', () => {
            phase2.style.display = 'none';
        })

    })
}


