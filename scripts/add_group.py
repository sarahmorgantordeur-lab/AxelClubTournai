from app import create_app, db
from app.models import Group

app = create_app()

with app.app_context():
    # Créer un groupe de test avec prix
    group = Group(
        name='Groupe Test',
        schedule='Lundi 18h-19h',
        price_per_season=250.0,
        description='Groupe de test avec prix'
    )
    db.session.add(group)
    db.session.commit()

    # Vérifier
    groups = Group.query.all()
    print(f"\nNombre de groupes: {len(groups)}")
    for g in groups:
        print(f"\nGroupe: {g.name}")
        print(f"  - Price per season: {g.price_per_season}")
