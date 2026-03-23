from app import create_app, db
from app.models import Group

app = create_app()

with app.app_context():
    groups = Group.query.all()
    print(f"Nombre de groupes: {len(groups)}")
    for group in groups:
        print(f"\nGroupe: {group.name}")
        print(f"  - Schedule: {group.schedule}")
        print(f"  - Price per season: {group.price_per_season}")
        print(f"  - Description: {group.description}")
