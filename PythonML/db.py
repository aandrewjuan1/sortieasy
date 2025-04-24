# db.py
import os
import logging
from sqlalchemy import create_engine, text

# ------------------ Configuration ------------------
DB_RELATIVE_PATH = "database/database.sqlite"

# Set up logging
logger = logging.getLogger(__name__)

# Create engine
db_path = os.path.abspath(DB_RELATIVE_PATH)
engine = create_engine(f"sqlite:///{db_path}")

def get_engine():
    return engine

def test_db_connection(req_tables) -> None:
    """Test database connection and validate required tables exist."""
    logger.info(f"Connecting to database at {db_path}")

    if not os.path.exists(db_path):
        raise FileNotFoundError(f"Database not found at {db_path}")

    with engine.connect() as conn:
        tables = {row[0] for row in conn.execute(text("SELECT name FROM sqlite_master WHERE type='table';"))}
        missing = req_tables - tables
        if missing:
            raise ValueError(f"Missing required tables: {missing}")

    logger.info("✅ Database connection and table check passed.")
