from sqlalchemy import Column, Integer, String
from core.database import Base


class Ruangan(Base):
    __tablename__ = "ruangan"

    id = Column(Integer, primary_key=True, index=True)
    ruangan = Column(String)
