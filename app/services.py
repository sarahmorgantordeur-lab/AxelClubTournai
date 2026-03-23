from app import db
from app.models import User, Group, Attendance
from datetime import datetime
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

class UserService:
    @staticmethod
    def create_user(username, email, first_name, last_name, password, role='patineur', phone=None, group_id=None):
        user = User(
            username=username,
            email=email,
            first_name=first_name,
            last_name=last_name,
            phone=phone,
            group_id=group_id,
            roles=[role]  # Convertir en array
        )
        user.set_password(password)
        db.session.add(user)
        db.session.commit()
        return user

    @staticmethod
    def get_all_users():
        return User.query.all()

    @staticmethod
    def get_patineurs():
        return User.query.filter(User.roles.contains('patineur')).all()

    @staticmethod
    def get_user(user_id):
        return User.query.get(user_id)

    @staticmethod
    def update_user(user_id, **kwargs):
        user = User.query.get(user_id)
        if user:
            for key, value in kwargs.items():
                if hasattr(user, key):
                    setattr(user, key, value)
            db.session.commit()
        return user

    @staticmethod
    def delete_user(user_id):
        user = User.query.get(user_id)
        if user:
            db.session.delete(user)
            db.session.commit()
            return True
        return False


class GroupService:
    @staticmethod
    def create_group(name, level=None, coach_id=None, max_members=None):
        group = Group(
            name=name,
            level=level,
            coach_id=coach_id,
            max_members=max_members
        )
        db.session.add(group)
        db.session.commit()
        return group

    @staticmethod
    def get_all_groups():
        return Group.query.all()

    @staticmethod
    def get_group(group_id):
        return Group.query.get(group_id)

    @staticmethod
    def update_group(group_id, **kwargs):
        group = Group.query.get(group_id)
        if group:
            for key, value in kwargs.items():
                if hasattr(group, key):
                    setattr(group, key, value)
            db.session.commit()
        return group


class AttendanceService:
    @staticmethod
    def record_attendance(user_id, session_date, status='present', notes=None, recorded_by_id=None):
        attendance = Attendance(
            user_id=user_id,
            session_date=session_date,
            status=status,
            notes=notes,
            recorded_by_id=recorded_by_id
        )
        db.session.add(attendance)
        db.session.commit()
        return attendance

    @staticmethod
    def get_user_attendance(user_id):
        return Attendance.query.filter_by(user_id=user_id).all()

    @staticmethod
    def get_attendance_by_date(session_date):
        return Attendance.query.filter_by(session_date=session_date).all()

    @staticmethod
    def get_attendance_stats(user_id):
        attendances = Attendance.query.filter_by(user_id=user_id).all()
        stats = {
            'present': len([a for a in attendances if a.status == 'present']),
            'absent': len([a for a in attendances if a.status == 'absent']),
            'late': len([a for a in attendances if a.status == 'late']),
            'total': len(attendances)
        }
        return stats
    