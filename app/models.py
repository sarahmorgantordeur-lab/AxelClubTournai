from app import db
from datetime import datetime
from werkzeug.security import generate_password_hash, check_password_hash
from flask_login import UserMixin
from sqlalchemy.orm import attributes

# Table d'association pour la relation parent-enfant (many-to-many)
parent_child = db.Table('parent_child',
    db.Column('parent_id', db.Integer, db.ForeignKey('users.id'), primary_key=True),
    db.Column('child_id', db.Integer, db.ForeignKey('users.id'), primary_key=True)
)

class User(UserMixin, db.Model):
    """Modèle unifié pour tous les utilisateurs (patineurs, parents, admins)"""
    __tablename__ = 'users'

    id = db.Column(db.Integer, primary_key=True)

    # Authentification
    username = db.Column(db.String(100), unique=True, nullable=False)
    email = db.Column(db.String(120), unique=True, nullable=False)
    password_hash = db.Column(db.String(255), nullable=False)
    roles = db.Column(db.JSON, default=lambda: ['patineur'])  # Tableau de rôles: ['admin', 'patineur', 'parent']
    is_active = db.Column(db.Boolean, default=True)

    # Informations personnelles
    first_name = db.Column(db.String(100), nullable=False)
    last_name = db.Column(db.String(100), nullable=False)
    date_of_birth = db.Column(db.Date)
    phone = db.Column(db.String(20))
    emergency_contacts = db.Column(db.JSON, default=lambda: [])  # Tableau de numéros d'urgence

    # Informations club (pour les patineurs)
    license_number = db.Column(db.String(50), unique=True)
    group_id = db.Column(db.Integer, db.ForeignKey('groups.id'))
    status = db.Column(db.String(20), default='active')  # active, inactive, suspended

    # Dates
    joined_date = db.Column(db.DateTime, default=datetime.utcnow)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    # Réinscription annuelle (année de la dernière inscription/réinscription)
    registration_year = db.Column(db.Integer, default=lambda: datetime.utcnow().year)

    # Relations
    group = db.relationship('Group', backref='members', foreign_keys=[group_id])
    attendances = db.relationship('Attendance', backref='user', cascade='all, delete-orphan', foreign_keys='Attendance.user_id')
    payment_records = db.relationship('PaymentRecord', backref='user', cascade='all, delete-orphan', foreign_keys='PaymentRecord.user_id')
    season_payments = db.relationship('SeasonPayment', backref='user', cascade='all, delete-orphan')

    # Relation parent-enfant (many-to-many)
    children = db.relationship(
        'User',
        secondary=parent_child,
        primaryjoin=(parent_child.c.parent_id == id),
        secondaryjoin=(parent_child.c.child_id == id),
        backref=db.backref('parents', lazy='dynamic'),
        lazy='dynamic'
    )

    def set_password(self, password):
        self.password_hash = generate_password_hash(password, method='pbkdf2:sha256')

    def check_password(self, password):
        return check_password_hash(self.password_hash, password)

    def get_roles_list(self):
        """Retourne la liste des rôles"""
        if isinstance(self.roles, list):
            return self.roles
        return ['patineur']

    def has_role(self, role):
        """Vérifie si l'utilisateur a un rôle spécifique"""
        roles_list = self.get_roles_list()
        return role in roles_list

    def add_role(self, role):
        """Ajoute un rôle à l'utilisateur"""
        if not self.has_role(role):
            roles_list = self.get_roles_list()
            roles_list.append(role)
            self.roles = roles_list
            attributes.flag_modified(self, "roles")

    def remove_role(self, role):
        """Supprime un rôle de l'utilisateur"""
        if self.has_role(role):
            roles_list = [r for r in self.get_roles_list() if r != role]
            self.roles = roles_list if roles_list else ['patineur']
            attributes.flag_modified(self, "roles")

    def has_any_role(self, *roles):
        """Vérifie si l'utilisateur a l'un des rôles spécifiés"""
        user_roles = self.get_roles_list()
        return any(role in user_roles for role in roles)

    def is_staff(self):
        """Vérifie si l'utilisateur est admin"""
        return self.has_role('admin')

    def is_patineur(self):
        """Vérifie si l'utilisateur est un patineur"""
        return self.has_role('patineur')

    def is_parent(self):
        """Vérifie si l'utilisateur est un parent"""
        return self.has_role('parent')

    def can_have_group(self):
        """Vérifie si l'utilisateur peut avoir un groupe (doit être patineur)"""
        return self.has_role('patineur')

    def add_child(self, child):
        """Ajoute un enfant à ce parent"""
        if not self.is_child_of(child):
            self.children.append(child)

    def remove_child(self, child):
        """Retire un enfant de ce parent"""
        if self.is_child_of(child):
            self.children.remove(child)

    def is_child_of(self, user):
        """Vérifie si user est un enfant de ce parent"""
        return self.children.filter(parent_child.c.child_id == user.id).count() > 0

    def get_children_list(self):
        """Retourne la liste des enfants"""
        return self.children.all()

    def get_parents_list(self):
        """Retourne la liste des parents"""
        return self.parents.all()

    def get_emergency_contacts_list(self):
        """Retourne la liste des numéros d'urgence"""
        if isinstance(self.emergency_contacts, list):
            return self.emergency_contacts
        return []

    def add_emergency_contact(self, phone):
        """Ajoute un numéro d'urgence"""
        if phone and phone not in self.get_emergency_contacts_list():
            contacts = self.get_emergency_contacts_list()
            contacts.append(phone)
            self.emergency_contacts = contacts
            attributes.flag_modified(self, "emergency_contacts")

    def remove_emergency_contact(self, phone):
        """Supprime un numéro d'urgence"""
        contacts = [c for c in self.get_emergency_contacts_list() if c != phone]
        self.emergency_contacts = contacts
        attributes.flag_modified(self, "emergency_contacts")

    def get_role_display(self):
        """Retourne le nom des rôles en français"""
        roles_display = {
            'admin': 'Administrateur',
            'patineur': 'Patineur',
            'parent': 'Parent'
        }
        user_roles = self.get_roles_list()
        return ', '.join([roles_display.get(role, role.capitalize()) for role in user_roles])

    def attendance_rate(self):
        """Calcule le taux de présence"""
        if not self.attendances:
            return 0
        total = len(self.attendances)
        present = len([a for a in self.attendances if a.status == 'present'])
        return round((present / total) * 100, 2) if total > 0 else 0

    def is_registered_this_year(self):
        """Vérifie si l'utilisateur est inscrit/réinscrit pour l'année en cours"""
        current_year = datetime.utcnow().year
        return self.registration_year == current_year

    def register_for_year(self, year=None):
        """Marque l'utilisateur comme inscrit pour une année donnée"""
        if year is None:
            year = datetime.utcnow().year
        self.registration_year = year

    def get_season_payment(self, year=None):
        """Retourne le SeasonPayment pour l'année donnée (ou l'année courante)"""
        if year is None:
            year = datetime.utcnow().year
        return SeasonPayment.query.filter_by(user_id=self.id, season_year=year).first()

    @classmethod
    def query_by_role(cls, role):
        """Filtre compatible SQLite et PostgreSQL pour les colonnes JSON roles"""
        from sqlalchemy import cast, String
        return cls.query.filter(cast(cls.roles, String).contains(f'"{role}"'))

    def __repr__(self):
        return f'<User {self.username}>'


class Group(db.Model):
    __tablename__ = 'groups'

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(100), nullable=False, unique=True)
    schedule = db.Column(db.String(255))  # ex: Lundi 18h-19h, Mercredi 19h-20h
    price_per_season = db.Column(db.Float)
    description = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    sessions = db.relationship('TrainingSession', backref='group', cascade='all, delete-orphan')

    def __repr__(self):
        return f'<Group {self.name}>'

    def member_count(self):
        return len(self.members)


class TrainingSession(db.Model):
    __tablename__ = 'training_sessions'

    id = db.Column(db.Integer, primary_key=True)
    group_id = db.Column(db.Integer, db.ForeignKey('groups.id'), nullable=False)
    date = db.Column(db.DateTime, nullable=False)
    duration_minutes = db.Column(db.Integer, default=60)
    coach_notes = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def __repr__(self):
        return f'<TrainingSession {self.group.name} on {self.date}>'


class Attendance(db.Model):
    __tablename__ = 'attendances'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)
    session_id = db.Column(db.Integer, db.ForeignKey('training_sessions.id'))
    session_date = db.Column(db.DateTime, nullable=False)
    status = db.Column(db.String(20), default='present')  # present, absent, late, excused
    notes = db.Column(db.Text)
    recorded_at = db.Column(db.DateTime, default=datetime.utcnow)
    recorded_by_id = db.Column(db.Integer, db.ForeignKey('users.id'))

    recorded_by = db.relationship('User', foreign_keys=[recorded_by_id], backref='recorded_attendances')

    def __repr__(self):
        return f'<Attendance {self.user_id} on {self.session_date}>'


class PaymentRecord(db.Model):
    __tablename__ = 'payment_records'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)
    amount = db.Column(db.Float, nullable=False)
    payment_type = db.Column(db.String(50))  # monthly, annual, lessons, etc.
    payment_method = db.Column(db.String(50))  # cash, card, transfer, check
    payment_date = db.Column(db.DateTime, nullable=False)
    due_date = db.Column(db.DateTime)
    status = db.Column(db.String(20), default='paid')  # paid, pending, overdue, cancelled
    notes = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)

    def __repr__(self):
        return f'<PaymentRecord {self.user_id} - {self.amount}€>'


class SeasonPayment(db.Model):
    __tablename__ = 'season_payments'

    id = db.Column(db.Integer, primary_key=True)
    user_id = db.Column(db.Integer, db.ForeignKey('users.id'), nullable=False)
    season_year = db.Column(db.Integer, nullable=False)

    # Montants à payer
    licence_due = db.Column(db.Float, default=0)
    saison_tournai_due = db.Column(db.Float, default=0)
    wasquehal_p1_due = db.Column(db.Float, default=0)
    wasquehal_p2_due = db.Column(db.Float, default=0)
    competitions_due = db.Column(db.Float, default=0)

    # Montants payés
    licence_paid = db.Column(db.Float, default=0)
    saison_tournai_paid = db.Column(db.Float, default=0)
    wasquehal_p1_paid = db.Column(db.Float, default=0)
    wasquehal_p2_paid = db.Column(db.Float, default=0)
    competitions_paid = db.Column(db.Float, default=0)

    notes = db.Column(db.Text)
    created_at = db.Column(db.DateTime, default=datetime.utcnow)
    updated_at = db.Column(db.DateTime, default=datetime.utcnow, onupdate=datetime.utcnow)

    def total_due(self):
        return (self.licence_due or 0) + (self.saison_tournai_due or 0) + \
               (self.wasquehal_p1_due or 0) + (self.wasquehal_p2_due or 0) + \
               (self.competitions_due or 0)

    def total_paid(self):
        return (self.licence_paid or 0) + (self.saison_tournai_paid or 0) + \
               (self.wasquehal_p1_paid or 0) + (self.wasquehal_p2_paid or 0) + \
               (self.competitions_paid or 0)

    def balance(self):
        return self.total_due() - self.total_paid()

    def is_fully_paid(self):
        return self.balance() <= 0

    def __repr__(self):
        return f'<SeasonPayment {self.user_id} - {self.season_year}>'


class Report(db.Model):
    __tablename__ = 'reports'

    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(255), nullable=False)
    report_type = db.Column(db.String(50))  # attendance, financial, member, group
    data = db.Column(db.JSON)
    generated_by_id = db.Column(db.Integer, db.ForeignKey('users.id'))
    generated_at = db.Column(db.DateTime, default=datetime.utcnow)
    start_date = db.Column(db.DateTime)
    end_date = db.Column(db.DateTime)

    generated_by = db.relationship('User', backref='generated_reports')

    def __repr__(self):
        return f'<Report {self.title}>'
